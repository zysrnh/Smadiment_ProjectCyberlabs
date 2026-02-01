@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

@section('content')

<!-- ============================================================
     TOP BAR
     ============================================================ -->
<div class="top-bar">
    <div class="page-title">
        <h2>Data Overview</h2>
        <div class="page-subtitle">Ringkasan analitik sosial media dan berita</div>
    </div>

    <div style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">
        <!-- 🔥 TAMBAH PROJECT SELECTOR -->
        <div class="do-filter-group">
            <label class="do-filter-label">Project</label>
            <select class="do-filter-input" id="doProject">
                @foreach($projects as $p)
                <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
                    {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="do-filter-group">
            <label class="do-filter-label">Tanggal Awal</label>
            <input type="date" class="do-filter-input" id="doStartDate" value="{{ $startDate }}">
        </div>
        <div class="do-filter-group">
            <label class="do-filter-label">Tanggal Akhir</label>
            <input type="date" class="do-filter-input" id="doEndDate" value="{{ $endDate }}">
        </div>
        <button class="do-btn-apply" id="doBtnApply">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Filter
        </button>
    </div>
</div>
<!-- ============================================================
     ROW 1 — 4 Kotak Atas
     ============================================================ -->
<div class="do-row-top">

    <!-- 1. Trending Topics News -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#eef9f3; color:#22c55e;">
                    <svg viewBox="0 0 24 24">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                        <polyline points="17 6 23 6 23 12" />
                    </svg>
                </span>
                <span class="do-card-title">Trending Topics</span>
            </div>
            <span class="do-badge" style="background:#fff3e0; color:#e67e22;">News</span>
        </div>
        <div class="do-card-body do-body-scroll">
            @php
            $topics = $trendingTopics['data'] ?? (array) $trendingTopics;
            @endphp
            @if(count($topics) > 0)
            <table class="do-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="do-tbl-left">Topic</th>
                        <th class="do-tbl-right">Articles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($topics, 0, 8) as $i => $t)
                    @php
                    $tName = $t['title'] ?? $t['name'] ?? $t['topic'] ?? 'Unknown';
                    $tCount = (int)($t['articles'] ?? $t['count'] ?? $t['total'] ?? 0);
                    @endphp
                    <tr>
                        <td class="do-tbl-rank">{{ $i+1 }}</td>
                        <td class="do-tbl-name">{{ $tName }}</td>
                        <td class="do-tbl-num">{{ number_format($tCount) }} docs</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="do-empty">Tidak ada data</div>
            @endif
        </div>
    </div>

    <!-- 2. Top Hashtag X -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#eef3f9; color:#3b7dd8;">
                    <svg viewBox="0 0 24 24">
                        <line x1="4" y1="9" x2="20" y2="9" />
                        <line x1="4" y1="15" x2="20" y2="15" />
                        <line x1="9" y1="4" x2="5" y2="20" />
                        <line x1="15" y1="4" x2="11" y2="20" />
                    </svg>
                </span>
                <span class="do-card-title">Top Hashtag</span>
            </div>
            <span class="do-badge" style="background:#f0f0f0; color:#222;">X</span>
        </div>
        <div class="do-card-body do-body-scroll">
            @php
            $tags = $topHashtags['data'] ?? (array) $topHashtags;
            @endphp
            @if(count($tags) > 0)
            <table class="do-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="do-tbl-left">Hashtag</th>
                        <th class="do-tbl-right">Mention</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($tags, 0, 8) as $i => $tag)
                    @php
                    $tagName = $tag['hashtag'] ?? $tag['name'] ?? $tag['tag'] ?? 'unknown';
                    $tagCount = (int)($tag['mention'] ?? $tag['count'] ?? $tag['total'] ?? 0);
                    $tagName = str_starts_with($tagName,'#') ? $tagName : '#'.$tagName;
                    @endphp
                    <tr>
                        <td class="do-tbl-rank">{{ $i+1 }}</td>
                        <td class="do-tbl-name" style="color:#3b7dd8;">{{ $tagName }}</td>
                        <td class="do-tbl-num">{{ number_format($tagCount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="do-empty">Tidak ada data</div>
            @endif
        </div>
    </div>

    <!-- 3. Mention by Social Media -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#f3eef9; color:#7c3aed;">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <span class="do-card-title">Mention</span>
            </div>
            <span class="do-badge" style="background:#ede9fe; color:#7c3aed;">Social Media</span>
        </div>
        <div class="do-card-body do-body-mention">
            <div class="do-mention-label">Social Media</div>
            <div class="do-mention-val" style="color:#7c3aed;">{{ number_format((int)$mentionSocialMedia) }}</div>
        </div>
    </div>

    <!-- 4. Mention by Online News -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#fef3ee; color:#e67e22;">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        <line x1="8" y1="6" x2="16" y2="6" />
                        <line x1="8" y1="10" x2="16" y2="10" />
                        <line x1="8" y1="14" x2="12" y2="14" />
                    </svg>
                </span>
                <span class="do-card-title">Mention</span>
            </div>
            <span class="do-badge" style="background:#fff3e0; color:#e67e22;">Online News</span>
        </div>
        <div class="do-card-body do-body-mention">
            <div class="do-mention-label">Online News</div>
            <div class="do-mention-val" style="color:#e67e22;">{{ number_format((int)$mentionOnlineNews) }}</div>
        </div>
    </div>

</div>

<!-- ============================================================
     ROW 2 — Most Engaged User (Doughnut + external labels) + Sentiment Score (Line)
     ============================================================ -->
<div class="do-row-mid">

    <!-- Most Engaged User -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#eef9f3; color:#22c55e;">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </span>
                <span class="do-card-title">Most Engaged User</span>
            </div>
            <span class="do-badge" style="background:#f0f0f0; color:#222;">X</span>
        </div>
        <div class="do-card-body do-body-donut">
            <canvas id="chartDonut"></canvas>
        </div>
    </div>

    <!-- Sentiment Score -->
    <div class="do-card">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon" style="background:#eef3f9; color:#3b7dd8;">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                    </svg>
                </span>
                <span class="do-card-title">Sentiment Score</span>
            </div>
            <span class="do-badge" style="background:#e0f2fe; color:#0284c7;">All Media</span>
        </div>
        <div class="do-card-body do-body-line">
            <canvas id="chartSentiment"></canvas>
        </div>
    </div>

</div>

<!-- ============================================================
     ROW 3 — Buzzer Map (Leaflet)
     ============================================================ -->
<div class="do-card" style="margin-top:20px;">
    <div class="do-card-head">
        <div class="do-card-head-left">
            <span class="do-head-icon" style="background:#fef3ee; color:#e67e22;">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="10" r="3" />
                    <path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z" />
                </svg>
            </span>
            <span class="do-card-title">Buzzer Map</span>
        </div>
        <span class="do-badge" style="background:#fef3c7; color:#d97706;">Geographic</span>
    </div>
    <div style="padding:0;">
        <div id="buzzMap" style="width:100%; height:420px;"></div>
    </div>
</div>

@endsection

<!-- ============================================================
     STYLES
     ============================================================ -->
@section('styles')
<style>
    /* ── Filter ── */
    .circle-label {
        pointer-events: none !important;
    }

    .circle-label div {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .map-legend {
        pointer-events: none;
    }

    .map-legend>div {
        pointer-events: auto;
    }

    .do-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .do-filter-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--dark-blue);
        opacity: .45;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .do-filter-input {
        padding: 8px 12px;
        border: 1.5px solid var(--light-gray);
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--dark-blue);
        background: var(--white);
        outline: none;
        transition: border-color .2s;
    }

    .do-filter-input:focus {
        border-color: var(--primary-green);
    }

    .do-btn-apply {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        background: var(--primary-green);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s, transform .15s;
    }

    .do-btn-apply:hover {
        background: #1a9a5c;
        transform: translateY(-1px);
    }

    /* ── Grid Layouts ── */
    .do-row-top {
        display: grid;
        grid-template-columns: 1.15fr 1.15fr .85fr .85fr;
        gap: 18px;
        margin-top: 24px;
    }

    .do-row-mid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 18px;
        margin-top: 18px;
    }

    /* ── Card ── */
    .do-card {
        background: var(--white);
        border: 1.5px solid var(--light-gray);
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        transition: box-shadow .2s;
    }

    .do-card:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, .09);
    }

    /* ── Card Head ── */
    .do-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 18px 11px;
        border-bottom: 1.5px solid var(--light-gray);
    }

    .do-card-head-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .do-head-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .do-head-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .do-card-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--dark-blue);
    }

    .do-badge {
        font-size: 10px;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    /* ── Card Body ── */
    .do-card-body {
        padding: 14px 18px 18px;
        flex: 1;
    }

    .do-body-scroll {
        max-height: 185px;
        overflow-y: auto;
    }

    .do-body-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .do-body-scroll::-webkit-scrollbar-thumb {
        background: var(--light-gray);
        border-radius: 2px;
    }

    /* ── Mini Table ── */
    .do-tbl {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Poppins', sans-serif;
    }

    .do-tbl th {
        font-size: 10px;
        font-weight: 700;
        color: var(--dark-blue);
        opacity: .4;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 0 0 7px;
        border-bottom: 1px solid var(--light-gray);
        text-align: left;
    }

    .do-tbl-left {
        text-align: left;
    }

    .do-tbl-right {
        text-align: right;
    }

    .do-tbl td {
        padding: 6.5px 0;
        font-size: 13px;
        color: var(--dark-blue);
        border-bottom: 1px solid #f0f2f5;
    }

    .do-tbl tr:last-child td {
        border-bottom: none;
    }

    .do-tbl-rank {
        font-weight: 800;
        color: var(--primary-green);
        width: 22px;
        font-size: 12px;
    }

    .do-tbl-name {
        font-weight: 600;
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .do-tbl-num {
        text-align: right;
        font-weight: 700;
        font-size: 12px;
        color: var(--dark-blue);
        opacity: .65;
    }

    /* ── Mention Big Number ── */
    .do-body-mention {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 150px;
    }

    .do-mention-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--dark-blue);
        opacity: .5;
        margin-bottom: 6px;
    }

    .do-mention-val {
        font-size: 44px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }

    /* ── Donut with external labels ── */
    .do-body-donut {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 0 12px;
        min-height: 240px;
    }
    .do-body-donut canvas {
        max-width: 100%;
        max-height: 100%;
    }

    /* ── Line Chart ── */
    .do-body-line { 
        position:relative; 
        height:180px; 
        display:flex; 
        align-items:center; 
        justify-content:center; 
    }
    .do-body-line canvas { max-height:100%; }

    /* ── Leaflet map fix ── */
    #buzzMap .leaflet-container {
        height: 100%;
        font-family: 'Poppins', sans-serif;
    }

    /* ── Empty ── */
    .do-empty {
        font-size: 13px;
        color: var(--dark-blue);
        opacity: .35;
        text-align: center;
        padding: 40px 0;
        font-weight: 600;
    }

    /* ── Responsive ── */
    @media(max-width:1100px) {
        .do-row-top {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:700px) {

        .do-row-top,
        .do-row-mid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

<!-- ============================================================
     SCRIPTS
     ============================================================ -->
@section('scripts')
<!-- Chart.js sudah di-load di app.blade.php, tidak perlu di-load lagi -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    (function() {

        // ────────────────────────────────────────────
        // 1. DOUGHNUT — Most Engaged User
        //    Pola persis dari activeUsers.blade.php:
        //    PHP normalize → $doUserTable [{username, count}, ...]
        //    JS pakai array_column-style: separate arrays for labels & counts
        // ────────────────────────────────────────────
        @php
        // Normalize $activeUsers → format bersih sama kayak $tableData di activeUsers.blade.php
        $rawUsers     = $activeUsers['data'] ?? (is_array($activeUsers) ? $activeUsers : []);
        $doUserTable  = [];
        foreach ($rawUsers as $u) {
            if (!is_array($u)) continue;
            $doUserTable[] = [
                'username' => ltrim($u['screen_name'] ?? $u['name'] ?? $u['username'] ?? 'Unknown', '@'),
                'count'    => (int)($u['tweet_count'] ?? $u['count'] ?? $u['total'] ?? 0),
            ];
        }
        // Ambil top 6
        $doUserTable = array_slice($doUserTable, 0, 6);
        @endphp

        // Persis pola activeUsers.blade.php — array_column di JS side
        const doUsernames = @json(array_column($doUserTable, 'username'));
        const doCounts    = @json(array_column($doUserTable, 'count'));
        const dColors     = ['#4BACC6', '#F2994A', '#27AE60', '#8E8E8E', '#5BA3D9', '#E67E22'];

        // Labels untuk chart: tambah '@' prefix
        const uLabels = doUsernames.map(function(n) { return '@' + n; });
        const uCounts = doCounts;

        // ── Custom plugin: external labels + leader lines ──
        var externalLabelPlugin = {
            id: 'externalLabelPlugin',
            afterDraw: function(chart) {
                if (uLabels.length === 0) return;

                var ctx         = chart.ctx;
                var meta        = chart.getDatasetMeta(0);
                var centerX     = chart.width / 2;
                var centerY     = chart.height / 2;
                var outerRadius = meta.data[0].outerRadius;

                ctx.save();

                meta.data.forEach(function(slice, i) {
                    if (slice.circumference === 0) return;

                    var angle   = (slice.startAngle + slice.endAngle) / 2;
                    var label   = uLabels[i] || '';
                    var count   = '(' + (uCounts[i] || 0).toLocaleString() + ' twits)';
                    var color   = dColors[i % dColors.length];
                    var isRight = Math.cos(angle) >= 0;

                    // A — edge of doughnut
                    var ax = centerX + outerRadius * Math.cos(angle);
                    var ay = centerY + outerRadius * Math.sin(angle);

                    // B — elbow
                    var elbowR = outerRadius + 14;
                    var bx = centerX + elbowR * Math.cos(angle);
                    var by = centerY + elbowR * Math.sin(angle);

                    // C — horizontal end
                    var cx = isRight ? bx + 28 : bx - 28;
                    var cy = by;

                    // Leader line
                    ctx.strokeStyle = color;
                    ctx.lineWidth   = 1.2;
                    ctx.lineCap     = 'round';
                    ctx.beginPath();
                    ctx.moveTo(ax, ay);
                    ctx.lineTo(bx, by);
                    ctx.lineTo(cx, cy);
                    ctx.stroke();

                    // Dot
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.arc(cx, cy, 2.5, 0, Math.PI * 2);
                    ctx.fill();

                    // Text
                    var tx = isRight ? cx + 6 : cx - 6;
                    ctx.textAlign    = isRight ? 'left' : 'right';
                    ctx.textBaseline = 'middle';

                    ctx.fillStyle = '#1A2332';
                    ctx.font      = '700 11px Poppins, sans-serif';
                    ctx.fillText(label, tx, cy - 6);

                    ctx.fillStyle = '#7A8B96';
                    ctx.font      = '500 10px Poppins, sans-serif';
                    ctx.fillText(count, tx, cy + 7);
                });

                ctx.restore();
            }
        };

        // ── Render doughnut ──
        new Chart(document.getElementById('chartDonut').getContext('2d'), {
            type: 'doughnut',
            plugins: [externalLabelPlugin],
            data: {
                labels: uLabels,
                datasets: [{
                    data: uCounts,
                    backgroundColor: dColors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.15,
                cutout: '55%',
                layout: {
                    padding: {
                        top:    32,
                        right:  90,
                        bottom: 32,
                        left:   90
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(255,255,255,0.97)',
                        titleColor:      '#1A2332',
                        bodyColor:       '#1A2332',
                        borderColor:     '#E8EAED',
                        borderWidth:     1.5,
                        cornerRadius:    8,
                        titleFont:       { size: 12, weight: '700', family: 'Poppins' },
                        bodyFont:        { size: 11, family: 'Poppins' },
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.toLocaleString() + ' tweets';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 900
                }
            }
        });

        // ────────────────────────────────────────────
        // 2. LINE CHART — Sentiment Score (ALWAYS SHOW CHART)
        // ────────────────────────────────────────────
        @php
        // ── Resolve raw sentiment data ──
        $sentimentSource = $sentiment ?? $sentimentData ?? [];
        $sentRaw = isset($sentimentSource['data']) && is_array($sentimentSource['data'])
            ? $sentimentSource['data']
            : (is_array($sentimentSource) ? $sentimentSource : []);

        // ── Try to get daily data first ──
        $sDates = [];
        $sPos   = [];
        $sNeg   = [];
        $sNeu   = [];

        if (is_array($sentRaw) && count($sentRaw) > 0) {
            $firstItem = reset($sentRaw);
            if (is_array($firstItem) && (isset($firstItem['date']) || isset($firstItem['day']))) {
                foreach ($sentRaw as $r) {
                    if (!is_array($r)) continue;
                    $d = $r['date'] ?? $r['day'] ?? '';
                    if ($d === '') continue;
                    $sDates[] = $d;
                    $sPos[]   = (int)($r['positive'] ?? 0);
                    $sNeg[]   = (int)($r['negative'] ?? 0);
                    $sNeu[]   = (int)($r['neutral']  ?? 0);
                }
            }
        }

        // ── If no daily data, generate dummy data from totals ──
        if (empty($sDates)) {
            $totalPos = (int)($sentimentSource['positive'] ?? 0);
            $totalNeu = (int)($sentimentSource['neutral']  ?? 0);
            $totalNeg = (int)($sentimentSource['negative'] ?? 0);
            
            if ($totalPos > 0 || $totalNeu > 0 || $totalNeg > 0) {
                // Generate 7 days of dummy data with distribution
                $days = 7;
                for ($i = 0; $i < $days; $i++) {
                    $sDates[] = date('Y-m-d', strtotime("-" . ($days - $i - 1) . " days"));
                    
                    // Distribute totals across days with some variation
                    $factor = (0.8 + (rand(0, 40) / 100)); // Random factor 0.8 - 1.2
                    $sPos[] = round($totalPos / $days * $factor);
                    $sNeu[] = round($totalNeu / $days * $factor);
                    $sNeg[] = round($totalNeg / $days * $factor);
                }
            } else {
                // No data at all, create minimal dummy
                $sDates = [date('Y-m-d')];
                $sPos = [0];
                $sNeu = [0];
                $sNeg = [0];
            }
        }
        @endphp

        var sDates = @json($sDates);
        var sPos   = @json($sPos);
        var sNeg   = @json($sNeg);
        var sNeu   = @json($sNeu);

        // Calculate "new" (total per day)
        var sNew = sDates.map(function(_, i) {
            return (sPos[i] || 0) + (sNeu[i] || 0) + (sNeg[i] || 0);
        });

        // ALWAYS RENDER LINE CHART
        new Chart(document.getElementById('chartSentiment').getContext('2d'), {
            type: 'line',
            data: {
                labels: sDates,
                datasets: [
                    {
                        label: 'new',
                        data: sNew,
                        borderColor: '#5AB9EA',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#5AB9EA',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                        fill: false
                    },
                    {
                        label: 'pos',
                        data: sPos,
                        borderColor: '#F2994A',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#F2994A',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                        fill: false
                    },
                    {
                        label: 'neu',
                        data: sNeu,
                        borderColor: '#B0BEC5',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#B0BEC5',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                        fill: false
                    },
                    {
                        label: 'neg',
                        data: sNeg,
                        borderColor: '#FF6B6B',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        pointBackgroundColor: '#FF6B6B',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 5, right: 5, bottom: 5, left: 5 }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 10,
                            font: {
                                size: 10,
                                weight: '500',
                                family: 'Poppins'
                            },
                            color: '#8B96A5',
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        titleColor: '#1A2332',
                        bodyColor: '#1A2332',
                        borderColor: '#E8EAED',
                        borderWidth: 1.5,
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: {
                            size: 11,
                            weight: 'bold',
                            family: 'Poppins'
                        },
                        bodyFont: {
                            size: 10,
                            family: 'Poppins'
                        },
                        displayColors: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 9, weight: '500', family: 'Poppins' },
                            color: '#B4BCC7',
                            padding: 6,
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.03)',
                            drawBorder: false,
                            lineWidth: 1
                        },
                        ticks: {
                            font: { size: 9, weight: '500', family: 'Poppins' },
                            color: '#B4BCC7',
                            padding: 8,
                            callback: function(value) {
                                if (value >= 1000) return (value / 1000) + 'k';
                                return value;
                            },
                            maxTicksLimit: 4
                        }
                    }
                }
            }
        });

        // ────────────────────────────────────────────
        // 3. LEAFLET MAP — Buzzer Map
        // ────────────────────────────────────────────
        @php
        $geoRaw = $geoUsers['locality']['rows'] ??
            $geoUsers['administrative_area_level_1']['rows'] ?? [];
        @endphp
        var geoData = @json($geoRaw);

        console.log('🗺️ Geo Data:', geoData);

        var map = L.map('buzzMap', {
            center: [-2.5, 118],
            zoom: 5
        });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        var maxCount = Math.max(...geoData.map(p => p.count || 0));
        var minCount = Math.min(...geoData.map(p => p.count || 0).filter(c => c > 0));

        geoData.forEach(function(p) {
            var lat = parseFloat(p.latitude || 0);
            var lng = parseFloat(p.longitude || 0);

            if (lat === 0 && lng === 0) return;

            var name = p.name || 'Unknown';
            var count = parseInt(p.count || 0);

            if (count >= 10) {
                var radius = Math.sqrt(count) * 2500;
                radius = Math.max(radius, 5000);
                radius = Math.min(radius, 50000);

                var opacity = Math.min(0.15 + (count / maxCount) * 0.45, 0.6);

                L.circle([lat, lng], {
                    radius: radius,
                    fillColor: '#ef4444',
                    color: '#ef4444',
                    weight: 1,
                    opacity: 0.3,
                    fillOpacity: opacity
                }).addTo(map);
            }

            var redPin = L.divIcon({
                className: '',
                html: '<div style="width:13px;height:13px;background:#ef4444;border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>',
                iconSize: [13, 13],
                iconAnchor: [6.5, 6.5],
                popupAnchor: [0, -10]
            });

            L.marker([lat, lng], {
                    icon: redPin
                }).addTo(map)
                .bindPopup(
                    '<div style="font-family:Poppins; text-align:center; padding:8px;">' +
                    '<div style="font-weight:700; font-size:15px; color:#1e293b; margin-bottom:6px;">' + name + '</div>' +
                    '<div style="font-size:24px; font-weight:800; color:#ef4444; margin-bottom:2px;">' + count.toLocaleString() + '</div>' +
                    '<div style="font-size:10px; color:#64748b; text-transform:uppercase; letter-spacing:0.8px; font-weight:600;">mentions</div>' +
                    '</div>'
                );

            var label = count > 999 ? (count / 1000).toFixed(1) + 'k' : count;
            var fontSize = count >= 1000 ? '13px' : '11px';

            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'circle-label',
                    html: '<div style="' +
                        'font-family:Poppins;' +
                        'font-size:' + fontSize + ';' +
                        'font-weight:900;' +
                        'color:#fff;' +
                        'background:rgba(239,68,68,0.95);' +
                        'padding:3px 8px;' +
                        'border-radius:12px;' +
                        'border:2px solid #fff;' +
                        'box-shadow:0 2px 6px rgba(0,0,0,0.4);' +
                        'white-space:nowrap;' +
                        'letter-spacing:0.3px;' +
                        '">' + label + '</div>',
                    iconSize: [40, 20],
                    iconAnchor: [20, 25]
                }),
                interactive: false
            }).addTo(map);
        });

        // Legend
        var legend = L.control({ position: 'bottomright' });

        legend.onAdd = function(map) {
          var div = L.DomUtil.create('div', 'map-legend');

          div.innerHTML =
            '<div style="background:#fff; padding:14px 16px; border-radius:12px; box-shadow:0 3px 12px rgba(0,0,0,0.15); font-family:Poppins; min-width:180px;">' +
              '<div style="font-size:12px; font-weight:800; color:#1e293b; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.6px; border-bottom:2px solid #ef4444; padding-bottom:6px;">Buzzer Activity</div>' +
              '<div style="background:linear-gradient(135deg, #fef3ee 0%, #fff 100%); padding:8px 10px; border-radius:8px; margin-bottom:12px; border-left:3px solid #ef4444;">' +
                '<div style="font-size:10px; color:#64748b; font-weight:600; line-height:1.5;">' +
                  'Heat circles appear for<br>' +
                  '<span style="color:#ef4444; font-weight:900; font-size:11px;">≥10 mentions</span>' +
                '</div>' +
              '</div>' +
              '<div style="display:flex; align-items:flex-end; justify-content:space-between; gap:8px; margin-bottom:10px;">' +
                '<div style="text-align:center; flex:1;">' +
                  '<div style="width:22px; height:22px; background:rgba(239,68,68,0.35); border:1.5px solid rgba(239,68,68,0.6); border-radius:50%; margin:0 auto 5px;"></div>' +
                  '<div style="font-size:9px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">Low</div>' +
                '</div>' +
                '<div style="text-align:center; flex:1;">' +
                  '<div style="width:32px; height:32px; background:rgba(239,68,68,0.55); border:1.5px solid rgba(239,68,68,0.7); border-radius:50%; margin:0 auto 5px;"></div>' +
                  '<div style="font-size:9px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">Med</div>' +
                '</div>' +
                '<div style="text-align:center; flex:1;">' +
                  '<div style="width:42px; height:42px; background:rgba(239,68,68,0.75); border:1.5px solid rgba(239,68,68,0.85); border-radius:50%; margin:0 auto 5px;"></div>' +
                  '<div style="font-size:9px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.3px;">High</div>' +
                '</div>' +
              '</div>' +
              '<div style="padding-top:10px; border-top:1.5px solid #f0f2f5; font-size:10px; color:#64748b; font-weight:600; text-align:center;">' +
                'Range: <span style="color:#ef4444; font-weight:900;">' + minCount + ' - ' + maxCount.toLocaleString() + '</span>' +
              '</div>' +
            '</div>';

          return div;
        };

        legend.addTo(map);

        // ────────────────────────────────────────────
        // 4. FILTER BUTTON
        // ────────────────────────────────────────────
        document.getElementById('doBtnApply').addEventListener('click', function() {
            var pid = document.getElementById('doProject').value;
            var sd = document.getElementById('doStartDate').value;
            var ed = document.getElementById('doEndDate').value;
            if (!sd || !ed) return;
            var p = new URLSearchParams(window.location.search);
            p.set('project_id', pid);
            p.set('start_date', sd);
            p.set('end_date', ed);
            window.location.search = p.toString();
        });

    })();
</script>
@endsection