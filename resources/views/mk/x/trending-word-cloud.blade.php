    @extends('mk.layouts.app')

    @section('title', 'X Trending Topics Word Cloud - SMADIMENT')

    @section('styles')
    <style>
        :root {
            --primary-green: #038047;
            --primary-green-dark: #026738;
            --accent-blue: #2FC6F6;
            --text-primary: #1a202c;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
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
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }
        .page-header p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        .filter-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-gray);
        }

        .filter-content {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
        }

        .date-range-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .date-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
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

        .date-input-group svg {
            width: 18px;
            height: 18px;
            color: var(--text-secondary);
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .date-separator {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 14px;
        }

        .date-input {
            border: none;
            background: transparent;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            outline: none;
            min-width: 140px;
        }

        .location-select {
            padding: 10px 16px;
            background: var(--bg-gray-50);
            border: 1px solid var(--border-gray);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 180px;
        }

        .location-select:focus {
            border-color: var(--primary-green);
            background: var(--bg-white);
            box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
        }

        .apply-btn {
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
        }

        .apply-btn svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
        }

        .sentiment-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .sentiment-btn {
            padding: 10px 20px;
            background: var(--bg-white);
            border: 2px solid var(--border-gray);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sentiment-btn:hover {
            border-color: var(--primary-green);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .sentiment-btn.active {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
            border-color: var(--primary-green);
            color: white;
            box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
        }

        .sentiment-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .wordcloud-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            border: 1px solid var(--border-gray);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            padding: 48px;
            min-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .wordcloud-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(3, 128, 71, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(47, 198, 246, 0.03) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Hint shown after cloud is rendered */
        .wordcloud-hint {
            position: absolute;
            bottom: 16px;
            right: 20px;
            font-size: 11px;
            color: var(--text-muted);
            font-style: italic;
            display: none;
            align-items: center;
            gap: 5px;
            z-index: 2;
            pointer-events: none;
        }

        .wordcloud-hint svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
            fill: none;
            flex-shrink: 0;
        }

        #wordCloudChart {
            width: 100% !important;
            height: 700px !important;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        .loading-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 80px 20px;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--bg-gray-100);
            border-top-color: var(--primary-green);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .loading-text {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            color: var(--text-secondary);
            margin-bottom: 16px;
            stroke: currentColor;
            fill: none;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        @media (max-width: 768px) {
            .dashboard-container { padding: 16px; }
            .filter-content { flex-direction: column; align-items: stretch; }
            .date-range-wrapper { flex-direction: column; }
            .location-select { width: 100%; }
            .apply-btn { width: 100%; justify-content: center; }
            .sentiment-filters { width: 100%; }
            .sentiment-btn { flex: 1; justify-content: center; }
            #wordCloudChart { height: 500px !important; }
            .wordcloud-container { padding: 24px; min-height: 550px; }
        }
    </style>
    @endsection

    @section('content')
    <div class="dashboard-container">

        <div class="page-header">
            <h1>X Trending Topics Word Cloud</h1>
            <p>Visual representation of trending topics on X (Twitter) in {{ $location }}</p>
        </div>

        <!-- Filter Card -->
        <div class="filter-card">
            <form id="filterForm" method="GET" action="{{ route('mk.x.trending-word-cloud') }}">
                <div class="filter-content">
                    <div class="filter-label">
                        <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Date Range
                    </div>
                    <div class="date-range-wrapper">
                        <div class="date-input-group">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <input type="date" name="start_date" class="date-input"
                                value="{{ $startDate }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                        <span class="date-separator">to</span>
                        <div class="date-input-group">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <input type="date" name="end_date" class="date-input"
                                value="{{ $endDate }}" max="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <select name="location" class="location-select">
                        <option value="Indonesia"     {{ $location === 'Indonesia'     ? 'selected' : '' }}>Indonesia</option>
                        <option value="Worldwide"     {{ $location === 'Worldwide'     ? 'selected' : '' }}>Worldwide</option>
                        <option value="United States" {{ $location === 'United States' ? 'selected' : '' }}>United States</option>
                        <option value="United Kingdom"{{ $location === 'United Kingdom'? 'selected' : '' }}>United Kingdom</option>
                    </select>
                    <button type="submit" class="apply-btn">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Sentiment Filter Card -->
        <div class="filter-card">
            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                    Sentiment Filter
                </div>
                <div class="sentiment-filters">
                    <button type="button" class="sentiment-btn active" data-sentiment="all">
                        <span class="sentiment-dot" style="background: linear-gradient(135deg, #038047, #2FC6F6);"></span>
                        All Topics
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="positive">
                        <span class="sentiment-dot" style="background: #10b981;"></span>
                        Positive
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="neutral">
                        <span class="sentiment-dot" style="background: #f59e0b;"></span>
                        Neutral
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="negative">
                        <span class="sentiment-dot" style="background: #ef4444;"></span>
                        Negative
                    </button>
                </div>
            </div>
        </div>

        <!-- Word Cloud Container -->
        <div class="wordcloud-container">

            <div id="loadingState" class="loading-state">
                <div class="loading-spinner"></div>
                <div class="loading-text" id="loadingText">Loading trending topics data...</div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;" id="loadingProgress"></div>
            </div>

            <div id="wordCloudChart" style="display: none;"></div>

            <div class="wordcloud-hint" id="wordCloudHint">
                <svg viewBox="0 0 24 24">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                Click a word to search on X
            </div>

            <div id="emptyState" class="empty-state" style="display: none;">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h3>No Trending Topics Found</h3>
                <p>No trending topics data available for the selected date range.</p>
            </div>

        </div>

    </div>
    @endsection

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

    <script>
    const WordCloudGenerator = {
        startDate: '{{ $startDate ?? "" }}',
        endDate:   '{{ $endDate ?? "" }}',
        location:  '{{ $location ?? "Indonesia" }}',
        trendingData:     null,
        currentSentiment: 'all',
        chart: null,

        // ---------------------------------------------------------------
        // Keyword lists — checked as EXACT TOKENS (whole-word matching)
        // to avoid false positives like "protest" matching inside "pro".
        // Multi-word phrases (e.g. "tidak adil") use substring matching
        // on the full topic string instead.
        // ---------------------------------------------------------------
        NEGATIVE_KEYWORDS: [
            // English — single words
            'bad','worst','hate','hated','sad','fail','failed','failure',
            'lose','lost','loss','angry','anger','terrible','awful',
            'poor','dead','death','die','died','dies','kill','killed','killing',
            'corrupt','corruption','crime','criminal','fraud','scam',
            'lie','lies','liar','cheat','cheating','cheater',
            'abuse','abuser','abusive','terror','terrorist','terrorism',
            'attack','attacked','war','riot','rioting','scandal',
            'boycott','crisis','disaster','catastrophe',
            'wrong','broken','hurt','pain','suffer','suffering',
            'injustice','unfair','illegal','violence','violent',
            'racist','racism','bully','bullying','harassment','harass',
            'threat','threaten','danger','dangerous','emergency',
            'victim','bankrupt','poverty','hunger','famine',
            'shooting','murdered','murder','arrested','arrest',
            'fired','layoff','layoffs','resign','resignation',
            // Indonesian — single words
            'buruk','terburuk','benci','sedih','gagal','kalah',
            'marah','parah','miskin','mati','maut','bunuh','tewas',
            'korupsi','korup','kejahatan','kriminal','penipuan','tipu',
            'bohong','curang','kekerasan','serang','perang','rusuh',
            'skandal','boikot','krisis','bencana','musibah',
            'salah','rusak','sakit','derita','menderita',
            'ilegal','rasis','rasisme','ancaman','bahaya','berbahaya',
            'darurat','korban','bangkrut','kemiskinan','kelaparan',
            'tersangka','terdakwa','penjara','ditangkap','tangkap',
            'pecat','dipecat','mengundurkan','narkoba','narkotika',
            'meninggal','wafat','terbunuh','dibunuh','penembakan',
            'kebakaran','banjir','gempa','longsor','kecelakaan',
        ],

        // Multi-word phrases (Indonesian) — substring matched
        NEGATIVE_PHRASES: [
            'tidak adil','tidak beres','tidak bisa','tidak mampu',
            'tidak aman','unjuk rasa','demo besar','huru hara',
            'kasus korupsi','dugaan korupsi','terseret kasus',
            'ditangkap polisi','diciduk polisi','dicokok polisi',
        ],

        POSITIVE_KEYWORDS: [
            // English
            'win','won','winner','best','good','great','love',
            'happy','success','successful','amazing','excellent',
            'awesome','celebrate','celebration','proud','pride',
            'champion','victory','achieve','achievement',
            'congratulations','congrats','hope','inspire','inspiration',
            'wonderful','beautiful','brilliant','fantastic','superb',
            'legend','hero','heroic','progress','growth','improve',
            // Indonesian
            'menang','juara','terbaik','baik','bagus','cinta',
            'senang','sukses','berhasil','hebat','keren',
            'rayakan','bangga','kemenangan','prestasi','selamat',
            'harapan','inspirasi','positif','indah','cemerlang',
            'fantastis','legenda','pahlawan','kemajuan','merdeka',
            'damai','harmonis','aman','sejahtera',
        ],

        // ---------------------------------------------------------------
        // Classify sentiment with whole-word token matching
        // ---------------------------------------------------------------
        getSentimentFromTopic(topicName) {
            const lower  = topicName.toLowerCase().replace(/^#/, '').trim();
            const tokens = lower.split(/[^a-z0-9]+/).filter(t => t.length > 0);

            // Check negative single-word keywords
            for (const kw of this.NEGATIVE_KEYWORDS) {
                if (tokens.includes(kw)) return 'negative';
            }
            // Check negative phrases (substring)
            for (const phrase of this.NEGATIVE_PHRASES) {
                if (lower.includes(phrase)) return 'negative';
            }
            // Check positive single-word keywords
            for (const kw of this.POSITIVE_KEYWORDS) {
                if (tokens.includes(kw)) return 'positive';
            }

            return 'neutral';
        },

        getSentimentColor() {
            const colorSchemes = {
                positive: ['#10b981','#059669','#34d399','#6ee7b7','#047857'],
                negative: ['#ef4444','#dc2626','#b91c1c','#f87171','#c53030'],
                neutral:  ['#f59e0b','#d97706','#fbbf24','#b45309','#fcd34d'],
                all:      ['#038047','#04995a','#2FC6F6','#06b6d4','#8b5cf6',
                        '#a78bfa','#f59e0b','#fbbf24','#10b981','#34d399',
                        '#ef4444','#f87171'],
            };
            return colorSchemes[this.currentSentiment] || colorSchemes.all;
        },

        // ---------------------------------------------------------------
        // Open X search in a new tab
        // ---------------------------------------------------------------
        openXSearch(topicName) {
            const query = encodeURIComponent(topicName);
            window.open(
                `https://x.com/search?q=${query}&src=trend_click`,
                '_blank',
                'noopener,noreferrer'
            );
        },

        // ---------------------------------------------------------------
        // Init
        // ---------------------------------------------------------------
        async init() {
            if (typeof echarts === 'undefined') {
                this.showError('ECharts library failed to load');
                return;
            }
            this.initSentimentFilters();
            try {
                await this.loadData();
            } catch (error) {
                console.error('Failed to load trending data:', error);
                this.showError('Failed to load data');
            }
        },

        initSentimentFilters() {
            const buttons = document.querySelectorAll('.sentiment-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    buttons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.currentSentiment = btn.dataset.sentiment;
                    this.generateWordCloud();
                });
            });
        },

        async loadData() {
            const url = `/mk/api/x/trending-topics?start_date=${this.startDate}&end_date=${this.endDate}&location=${this.location}`;
            const loadingText     = document.getElementById('loadingText');
            const loadingProgress = document.getElementById('loadingProgress');

            loadingText.textContent = 'Fetching data from server...';

            const startTime = Date.now();
            const response  = await fetch(url);

            loadingText.textContent = 'Processing trending topics...';

            const result = await response.json();
            if (!result.success) {
                throw new Error(result.error || 'Failed to load data');
            }

            this.trendingData = result.data;
            const loadTime    = ((Date.now() - startTime) / 1000).toFixed(1);

            loadingText.textContent     = 'Generating word cloud...';
            loadingProgress.textContent = `Loaded ${this.trendingData.top_topics?.length || 0} topics in ${loadTime}s`;

            await new Promise(resolve => setTimeout(resolve, 200));
            this.generateWordCloud();
        },

        // ---------------------------------------------------------------
        // Render
        // ---------------------------------------------------------------
        generateWordCloud() {
            const loadingState = document.getElementById('loadingState');
            const chartDiv     = document.getElementById('wordCloudChart');
            const emptyState   = document.getElementById('emptyState');
            const hintEl       = document.getElementById('wordCloudHint');

            let topics = this.trendingData.top_topics || [];

            // Apply sentiment filter
            if (this.currentSentiment !== 'all') {
                topics = topics.filter(t => this.getSentimentFromTopic(t.name) === this.currentSentiment);
            }

            if (!topics.length) {
                loadingState.style.display = 'none';
                chartDiv.style.display     = 'none';
                hintEl.style.display       = 'none';
                emptyState.style.display   = 'block';

                const label = this.currentSentiment === 'all'
                    ? ''
                    : this.currentSentiment.charAt(0).toUpperCase() + this.currentSentiment.slice(1) + ' ';

                emptyState.innerHTML = `
                    <svg viewBox="0 0 24 24" style="width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <h3>No ${label}Topics Found</h3>
                    <p>Try selecting a different sentiment filter or date range.</p>
                `;
                return;
            }

            // Limit to top 100
            if (topics.length > 100) topics = topics.slice(0, 100);

            const wordData = topics.map(topic => ({
                name:  topic.name.replace(/^#/, ''),
                value: topic.total_volume || (topic.appearances * 100) || 100,
                originalTopic: topic,
            }));

            loadingState.style.display = 'none';
            chartDiv.style.display     = 'block';
            emptyState.style.display   = 'none';
            hintEl.style.display       = 'flex';

            if (this.chart) this.chart.dispose();

            this.chart = echarts.init(chartDiv, null, {
                renderer: 'canvas',
                devicePixelRatio: window.devicePixelRatio || 1,
            });

            const colors = this.getSentimentColor();

            const option = {
                tooltip: {
                    show: true,
                    trigger: 'item',
                    backgroundColor: '#ffffff',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    textStyle: { color: '#1a202c', fontSize: 13, fontFamily: 'Poppins, sans-serif' },
                    padding: 16,
                    shadowBlur: 20,
                    shadowColor: 'rgba(0,0,0,0.15)',
                    shadowOffsetY: 4,
                    formatter: (params) => {
                        const topic     = params.data.originalTopic;
                        const sentiment = this.getSentimentFromTopic(topic.name);
                        const sentimentColors = {
                            positive: '#22c55e',
                            negative: '#ef4444',
                            neutral:  '#94a3b8',
                        };
                        const color = sentimentColors[sentiment];
                        const label = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
                        return `
                            <div style="font-family:Poppins,sans-serif;min-width:200px;">
                                <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:8px;text-align:center;">
                                    ${params.name}
                                </div>
                                <div style="padding:4px 12px;background:${color}20;border-radius:12px;margin-bottom:10px;text-align:center;">
                                    <span style="font-size:10px;font-weight:700;color:${color};text-transform:uppercase;">
                                        ${label}
                                    </span>
                                </div>
                                <div style="font-size:11px;color:#94a3b8;text-align:center;">
                                    Click to search on X
                                </div>
                            </div>
                        `;
                    },
                },
                series: [{
                    type: 'wordCloud',
                    shape: 'circle',
                    keepAspect: false,
                    left: 'center',
                    top: 'center',
                    width: '90%',
                    height: '90%',
                    right: null,
                    bottom: null,
                    sizeRange: [16, 70],
                    rotationRange: [-45, 45],
                    rotationStep: 45,
                    gridSize: 12,
                    drawOutOfBound: false,
                    layoutAnimation: true,
                    textStyle: {
                        fontFamily: 'Poppins, Inter, sans-serif',
                        fontWeight: 'bold',
                        color: () => colors[Math.floor(Math.random() * colors.length)],
                    },
                    emphasis: {
                        focus: 'self',
                        textStyle: {
                            textShadowBlur: 10,
                            textShadowColor: 'rgba(0,0,0,0.35)',
                        },
                    },
                    data: wordData,
                }],
            };

            setTimeout(() => {
                this.chart.setOption(option, true);

                // Click -> open X search
                this.chart.on('click', (params) => {
                    if (params && params.data && params.data.originalTopic) {
                        this.openXSearch(params.data.originalTopic.name);
                    }
                });
            }, 10);

            // Resize with debounce
            let resizeTimer;
            const handleResize = () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => { if (this.chart) this.chart.resize(); }, 250);
            };
            window.removeEventListener('resize', handleResize);
            window.addEventListener('resize', handleResize);
        },

        showError(message = 'Failed to load data') {
            document.getElementById('loadingState').style.display = 'none';
            const emptyState = document.getElementById('emptyState');
            emptyState.innerHTML = `
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" style="color:#ef4444;width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <h3>Failed to Load Data</h3>
                    <p>${message}. Please try again later.</p>
                </div>`;
            emptyState.style.display = 'block';
        },
    };

    document.addEventListener('DOMContentLoaded', () => { WordCloudGenerator.init(); });
    </script>
    @endsection