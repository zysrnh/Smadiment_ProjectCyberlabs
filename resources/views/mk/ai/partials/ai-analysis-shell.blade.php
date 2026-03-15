{{--
    Shared AI Analysis HTML Shell — included by all platform ai-analysis.blade.php
    Required: $aiPlatform['name'], ['slug'], ['color'], ['iconSvg'], ['iconSvgLg']
--}}
@php
    $pName   = $aiPlatform['name']      ?? 'Platform';
    $pSlug   = $aiPlatform['slug']      ?? 'platform';
    $pColor  = $aiPlatform['color']     ?? '#038047';
    $pIcon   = $aiPlatform['iconSvg']   ?? '<i class="ph ph-chart-bar" style="font-size:20px;color:#fff;"></i>';
    $pIconLg = $aiPlatform['iconSvgLg'] ?? $pIcon;
@endphp

<div class="ai-shell">
    {{-- ═══ PROMPT SIDEBAR ═══ --}}
    <aside class="prompt-sidebar" id="promptSidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Prompt Templates</span>
            <button class="sidebar-toggle" id="sidebarToggle" title="Collapse sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
        </div>
        <div class="sidebar-search">
            <div class="sidebar-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="sidebar-search-input" id="sidebarSearch" type="text" placeholder="Search prompts…" oninput="filterPrompts(this.value)">
            </div>
        </div>
        <div class="sidebar-body" id="sidebarBody"></div>
    </aside>

    {{-- ═══ CHAT MAIN ═══ --}}
    <div class="ai-main">
        <div class="ai-header">
            <div class="ai-header-left">
                <div class="ai-avatar" style="background:{{ $pColor }};">{!! $pIcon !!}</div>
                <div class="ai-header-info">
                    <h4>{{ $pName }} AI Research</h4>
                    <p id="headerSubtitle">{{ $projectId ?? '-' }} &middot; {{ $startDate ?? '-' }} to {{ $endDate ?? '-' }}</p>
                </div>
            </div>
            <div class="ai-header-right">
                <div class="status-pill" id="statusPill">
                    <div class="status-dot"></div>
                    <span id="statusText">Loading…</span>
                </div>
                <button class="btn-pdf" onclick="exportChatPDF()" title="Download PDF"><i class="ph ph-file-pdf"></i> PDF</button>
                <button class="btn-clear" onclick="clearChat()">Clear</button>
            </div>
        </div>

        <div class="active-prompt-bar" id="activePromptBar">
            <div class="active-prompt-tag">
                <span id="activePromptLabel">–</span>
                <button onclick="clearActivePrompt()" title="Remove">×</button>
            </div>
            <span class="active-prompt-hint">Template loaded — edit atau kirim langsung</span>
        </div>

        <div class="ai-messages" id="aiMessages">
            <div class="welcome-state" id="welcomeState">
                <div class="welcome-icon-wrap" style="background:{{ $pColor }};">{!! $pIconLg !!}</div>
                <h3>Ready to Analyze {{ $pName }}</h3>
                <p>Pilih template dari panel kiri, atau ketik pertanyaan sendiri untuk menganalisis data percakapan {{ $pName }} pada project ini.</p>
                <div class="data-loading-badge" id="dataLoadingBadge">
                    <div class="spin"></div>
                    Memuat data {{ $pName }}…
                </div>
            </div>
        </div>

        <div class="ai-input-area">
            <div class="img-preview-bar" id="imgPreviewBar"></div>
            <div class="input-row">
                <input type="file" id="imgFileInput" accept="image/*" multiple hidden onchange="handleImgAttach(this)">
                <button class="btn-attach" id="btnAttach" onclick="document.getElementById('imgFileInput').click()" title="Lampirkan gambar" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </button>
                <textarea class="chat-textarea" id="chatInput" placeholder="Memuat data…" rows="1" disabled
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                    oninput="autoResize(this)"></textarea>
                <button class="btn-send" id="sendBtn" onclick="sendMessage()" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <div class="input-hint">Enter untuk kirim &middot; Shift+Enter untuk baris baru &middot; Klik <i class="ph ph-image"></i> untuk lampirkan gambar</div>
        </div>
    </div>
</div>
