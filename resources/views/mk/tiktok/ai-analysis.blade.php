@extends('mk.layouts.app')

@section('title', 'AI Analysis - TikTok')

@section('styles')
<style>
    :root {
        --primary: #010101;
        --primary-dark: #000000;
        --primary-light: rgba(1,1,1,0.08);
        --tt-black: #010101;
        --tt-pink: #EE1D52;
        --tt-cyan: #69C9D0;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --tt-gradient: linear-gradient(135deg, #EE1D52 0%, #010101 50%, #69C9D0 100%);
        --tt-btn-gradient: linear-gradient(135deg, #EE1D52 0%, #69C9D0 100%);
    }

    .ai-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 80px);
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .ai-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        flex-shrink: 0;
    }

    .ai-header-left { display: flex; align-items: center; gap: 12px; }

    .ai-avatar {
        width: 38px; height: 38px;
        border-radius: 10px;
        background: var(--tt-black);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    .ai-avatar::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--tt-gradient);
        opacity: 0.15;
    }
    .ai-avatar svg { width: 22px; height: 22px; position: relative; z-index: 1; }

    .ai-header-info h4 { font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .ai-header-info p  { font-size: 11px; color: var(--text-secondary); margin: 0; }

    .ai-header-right { display: flex; align-items: center; gap: 10px; }

    .date-picker-trigger {
        display: flex; align-items: center; gap: 7px;
        padding: 7px 13px; border-radius: 9px;
        border: 1px solid var(--border); background: #f8fafc;
        cursor: pointer; font-size: 12px; font-weight: 600;
        color: var(--text-primary); font-family: inherit; transition: all 0.15s;
    }
    .date-picker-trigger:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .date-picker-trigger svg { width: 14px; height: 14px; color: var(--text-secondary); }

    .status-pill {
        display: flex; align-items: center; gap: 6px;
        padding: 5px 11px; border-radius: 20px;
        background: #f0fdf4; border: 1px solid #bbf7d0;
        font-size: 11px; font-weight: 600; color: #16a34a;
    }
    .status-pill.loading { background: #fefce8; border-color: #fde68a; color: #ca8a04; }
    .status-pill.error   { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; background: #16a34a; }
    .status-pill.loading .status-dot { background: #ca8a04; animation: pulse 1s infinite; }
    .status-pill.error   .status-dot { background: #dc2626; }

    .btn-clear {
        padding: 6px 13px; border-radius: 8px;
        border: 1px solid var(--border); background: #f8fafc;
        font-size: 12px; font-weight: 600; color: #64748b;
        cursor: pointer; font-family: inherit; transition: all 0.15s;
    }
    .btn-clear:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

    #ctxBar {
        padding: 8px 18px;
        background: #fff0f3;
        border-bottom: 1px solid #ffd6e0;
        font-size: 11.5px; font-weight: 500;
        color: #be1d4b; flex-shrink: 0;
    }

    .ai-messages {
        flex: 1; overflow-y: auto; padding: 20px 18px;
        display: flex; flex-direction: column; gap: 14px;
        background: #fafbfc;
    }

    .welcome-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; text-align: center;
        padding: 40px 20px; flex: 1; gap: 12px;
    }

    .welcome-icon-wrap {
        width: 60px; height: 60px; border-radius: 16px;
        background: var(--tt-black);
        display: flex; align-items: center; justify-content: center;
        box-shadow: -4px 4px 0 var(--tt-cyan), 4px -4px 0 var(--tt-pink);
    }
    .welcome-icon-wrap svg { width: 32px; height: 32px; }

    .welcome-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .welcome-state p  { font-size: 13px; color: var(--text-secondary); margin: 0; max-width: 360px; }

    .data-loading-badge {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 16px; background: #fff; border: 1px solid var(--border);
        border-radius: 20px; font-size: 12px; color: var(--text-secondary);
    }

    .spin {
        width: 14px; height: 14px; border: 2px solid #e2e8f0;
        border-top-color: var(--tt-pink); border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    .ai-input-area {
        border-top: 1px solid var(--border); background: #ffffff;
        padding: 10px 14px 12px; flex-shrink: 0;
    }

    .prompt-chips {
        display: flex; gap: 6px; flex-wrap: nowrap;
        overflow-x: auto; padding-bottom: 8px; scrollbar-width: none;
    }
    .prompt-chips::-webkit-scrollbar { display: none; }

    .chip {
        padding: 5px 12px; border-radius: 20px; white-space: nowrap;
        border: 1px solid var(--border); background: #f8fafc;
        font-size: 11.5px; font-weight: 600; color: #475569;
        cursor: pointer; font-family: inherit; transition: all 0.15s; flex-shrink: 0;
    }
    .chip:hover { background: var(--tt-pink); border-color: var(--tt-pink); color: #fff; }
    .chip.active { background: var(--tt-pink); border-color: var(--tt-pink); color: #fff; }

    .chip-featured {
        background: var(--tt-btn-gradient) !important;
        border-color: var(--tt-pink) !important;
        color: #fff !important;
    }
    .chip-featured:hover { opacity: 0.9; }

    .input-row { display: flex; gap: 8px; align-items: flex-end; }

    .chat-textarea {
        flex: 1; resize: none; border: 1px solid var(--border);
        border-radius: 12px; padding: 10px 14px;
        font-size: 13px; font-family: inherit; line-height: 1.5;
        background: #f8fafc; color: var(--text-primary);
        outline: none; transition: border-color 0.15s, background 0.15s;
        min-height: 42px; max-height: 120px; overflow-y: auto;
    }
    .chat-textarea:focus { border-color: var(--tt-pink); background: #fff; }
    .chat-textarea:disabled { opacity: 0.5; cursor: not-allowed; }

    .btn-send {
        width: 40px; height: 40px; border-radius: 11px; border: none;
        background: var(--tt-btn-gradient);
        color: #fff; cursor: pointer; display: flex;
        align-items: center; justify-content: center;
        box-shadow: 0 3px 10px rgba(238,29,82,0.35); transition: all 0.15s; flex-shrink: 0;
    }
    .btn-send:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(238,29,82,0.45); }
    .btn-send:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-send svg { width: 17px; height: 17px; }

    .input-hint { font-size: 10.5px; color: #cbd5e1; text-align: center; margin-top: 5px; }

    .typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; animation: typing 1.2s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes spin    { to { transform: rotate(360deg); } }
    @keyframes pulse   { 0%,100% { opacity:1; } 50% { opacity:.4; } }
    @keyframes typing  { 0%,80%,100% { transform:scale(0.8); opacity:.5; } 40% { transform:scale(1.1); opacity:1; } }
    @keyframes msgIn   { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    /* Date Picker Modal */
    .date-picker-modal {
        position: fixed; inset: 0; z-index: 9999;
        display: none; align-items: center; justify-content: center;
    }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.35); backdrop-filter: blur(2px); }

    .date-picker-container {
        position: relative; z-index: 1;
        background: #fff; border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        display: flex; overflow: hidden; max-width: 680px; width: 95%;
    }

    .date-picker-sidebar {
        width: 140px; background: #f8fafc;
        border-right: 1px solid #e2e8f0; padding: 16px 10px;
        display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
    }

    .date-preset {
        padding: 8px 12px; border-radius: 8px; border: none;
        background: transparent; font-family: inherit;
        font-size: 12px; font-weight: 600; color: #475569;
        cursor: pointer; text-align: left; transition: all 0.15s;
    }
    .date-preset:hover { background: #e2e8f0; }
    .date-preset.active { background: var(--tt-btn-gradient); color: #fff; }

    .date-picker-content { flex: 1; padding: 18px; display: flex; flex-direction: column; gap: 14px; }
    .date-picker-nav { display: flex; align-items: flex-start; gap: 10px; }

    .dp-nav-btn {
        width: 34px; height: 34px; border-radius: 9px;
        background: #f8fafc; border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }
    .dp-nav-btn:hover { background: var(--tt-pink); border-color: var(--tt-pink); color: #fff; }
    .dp-nav-btn svg { width: 18px; height: 18px; }

    .calendars-wrapper { display: flex; gap: 20px; flex: 1; }
    .calendar { flex: 1; display: flex; flex-direction: column; }
    .calendar-month { font-size: 14px; font-weight: 700; color: #1a202c; text-align: center; margin-bottom: 12px; }
    .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; margin-bottom: 6px; }
    .weekday { text-align: center; font-size: 10px; font-weight: 700; color: #94a3b8; padding: 6px 0; text-transform: uppercase; }
    .calendar-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }

    .calendar-day {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 500; border-radius: 8px; cursor: pointer;
        transition: all 0.15s; color: #1a202c;
        background: transparent; border: none; padding: 0; font-family: inherit;
    }
    .calendar-day:hover:not(:disabled):not(.other-month) { background: #f1f5f9; }
    .calendar-day.other-month { color: #e2e8f0; cursor: default; }
    .calendar-day:disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid var(--tt-pink); font-weight: 700; }
    .calendar-day.selected,
    .calendar-day.range-start,
    .calendar-day.range-end { background: var(--tt-pink); color: #fff !important; }
    .calendar-day.in-range { background: rgba(238,29,82,0.1); color: var(--tt-pink); }

    .dp-display {
        padding: 10px 16px; background: #f8fafc; border-radius: 10px;
        text-align: center; margin: 14px 0 10px; border: 1px solid #e2e8f0;
        font-size: 13px; font-weight: 600; color: #1a202c;
    }

    .dp-footer { display: flex; gap: 10px; justify-content: flex-end; }

    .dp-cancel {
        padding: 9px 20px; border-radius: 9px; border: 1px solid #e2e8f0;
        background: #f8fafc; color: #374151;
        font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;
    }
    .dp-cancel:hover { background: #f1f5f9; }

    .dp-apply {
        padding: 9px 20px; border-radius: 9px; border: none;
        background: var(--tt-btn-gradient);
        color: #fff; font-family: inherit; font-size: 13px; font-weight: 600;
        cursor: pointer; box-shadow: 0 3px 12px rgba(238,29,82,0.3); transition: all 0.15s;
    }
    .dp-apply:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(238,29,82,0.4); }

    @media (max-width: 640px) {
        .date-picker-sidebar { display: none; }
        .calendars-wrapper { flex-direction: column; }
        .ai-header-right { gap: 6px; }
        .date-picker-trigger span { display: none; }
    }
</style>
@endsection

@section('content')
{{-- Date Picker Modal --}}
<div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay" onclick="closeDatePicker()"></div>
    <div class="date-picker-container">
        <div class="date-picker-sidebar">
            <button class="date-preset" data-preset="today">Today</button>
            <button class="date-preset" data-preset="yesterday">Yesterday</button>
            <button class="date-preset" data-preset="last7days">Last 7 Days</button>
            <button class="date-preset" data-preset="last30days">Last 30 Days</button>
            <button class="date-preset" data-preset="thismonth">This Month</button>
            <button class="date-preset" data-preset="lastmonth">Last Month</button>
            <button class="date-preset active" data-preset="custom">Custom Range</button>
        </div>
        <div class="date-picker-content">
            <div class="date-picker-nav">
                <button class="dp-nav-btn" id="dpPrev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="calendars-wrapper">
                    <div class="calendar" id="dpCal1"></div>
                    <div class="calendar" id="dpCal2"></div>
                </div>
                <button class="dp-nav-btn" id="dpNext">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
            <div class="dp-display" id="dpDisplay">Select date range</div>
            <div class="dp-footer">
                <button class="dp-cancel" onclick="closeDatePicker()">Cancel</button>
                <button class="dp-apply" id="dpApply">Apply</button>
            </div>
        </div>
    </div>
</div>

<div class="ai-wrapper">

    {{-- Header --}}
    <div class="ai-header">
        <div class="ai-header-left">
            <div class="ai-avatar">
                {{-- TikTok icon: path only, filled white --}}
                <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
                </svg>
            </div>
            <div class="ai-header-info">
                <h4>TikTok AI Research</h4>
                <p id="headerSubtitle">{{ $projectId ?? '-' }} &middot; {{ $startDate ?? '-' }} to {{ $endDate ?? '-' }}</p>
            </div>
        </div>
        <div class="ai-header-right">
            <button class="date-picker-trigger" id="datePickerTrigger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span id="dpTriggerLabel">{{ $startDate ?? '-' }} to {{ $endDate ?? '-' }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="status-pill" id="statusPill">
                <div class="status-dot"></div>
                <span id="statusText">Loading…</span>
            </div>
            <button class="btn-clear" onclick="clearChat()">Clear Chat</button>
        </div>
    </div>

    

    {{-- Messages --}}
    <div class="ai-messages" id="aiMessages">
        <div class="welcome-state" id="welcomeState">
            <div class="welcome-icon-wrap">
                <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
                </svg>
            </div>
            <h3>Ready to Analyze TikTok</h3>
            <p>Select a template below or type your own question to analyze TikTok conversation data for this project.</p>
            <div class="data-loading-badge" id="dataLoadingBadge">
                <div class="spin"></div>
                Loading TikTok data…
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="ai-input-area">
        <div class="prompt-chips" id="promptChips">
            <button class="chip" onclick="useChip(this,'issue_summary')">Issue Summary</button>
            <button class="chip" onclick="useChip(this,'sentiment_breakdown')">Sentiment Breakdown</button>
            <button class="chip" onclick="useChip(this,'top_creators')">Top Creators Analysis</button>
            <button class="chip" onclick="useChip(this,'hashtag_analysis')">Hashtag Analysis</button>
            <button class="chip" onclick="useChip(this,'engagement_analysis')">Engagement Analysis</button>
            <button class="chip" onclick="useChip(this,'swot')">SWOT Analysis</button>
            <button class="chip" onclick="useChip(this,'crisis')">Crisis Situation (SCCT)</button>
            <button class="chip" onclick="useChip(this,'narrative')">Dominant Narrative</button>
            <button class="chip" onclick="useChip(this,'audience_analysis')">Audience Analysis</button>
            <button class="chip" onclick="useChip(this,'content_strategy')">Content Strategy</button>
            <button class="chip" onclick="useChip(this,'early_warning')">Early Warning Signals</button>
            <button class="chip" onclick="useChip(this,'key_insights')">Key Insights & Actions</button>
            <button class="chip chip-featured" onclick="useChip(this,'comprehensive')">Analisis TikTok Komprehensif</button>
        </div>

        <div class="input-row">
            <textarea
                class="chat-textarea"
                id="chatInput"
                placeholder="Loading data…"
                rows="1"
                disabled
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                oninput="autoResize(this)"
            ></textarea>
            <button class="btn-send" id="sendBtn" onclick="sendMessage()" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2" fill="currentColor" stroke="none"/></svg>
            </button>
        </div>
        <div class="input-hint">Enter to send &middot; Shift+Enter for new line</div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════
// CONFIG — TikTok specific routes
// ═══════════════════════════════════════════════════════════════════
const PROJECT_ID = '{{ $projectId ?? "" }}';
let START_DATE = '{{ $startDate ?? "" }}';
let END_DATE   = '{{ $endDate ?? "" }}';

const ROUTES = {
    mostViewedPosts : '{{ route("mk.api.news.tiktok-top-status") }}',
    topHashtags     : '{{ route("mk.api.news.tiktok-top-status") }}',   // derive hashtags from posts
    mostActive      : '{{ route("mk.api.news.tiktok-top-status") }}',   // derive active from posts
    sentimentTotal  : '{{ route("mk.api.instagram.sentiment-total") }}', // reuse generic sentiment
    volumeTotal     : '{{ route("mk.api.instagram.volume-total") }}',    // reuse generic volume
    aiProxy         : '{{ route("mk.api.news.ai-proxy") }}',
};

// ═══════════════════════════════════════════════════════════════════
// PROMPT TEMPLATES — TikTok specific
// ═══════════════════════════════════════════════════════════════════
const CTX = `Project ID: ${PROJECT_ID}, Platform: TikTok, Period: ${START_DATE} to ${END_DATE}`;

const PROMPTS = {
    issue_summary: {
        label: 'Issue Summary',
        text: `Analyze the TikTok conversation data from this context and perform a comprehensive issue identification.

Context: ${CTX}

Tasks:
1. **MAIN ISSUES** — Identify at least 5 dominant issues in the TikTok conversation. For each: core problem, key creators involved, estimated video/comment volume.
2. **DOMINANT NARRATIVE** — What overarching narrative emerges from TikTok videos, duets, and comment sections?
3. **ISSUE CLASSIFICATION** — Classify each issue as: political, economic, social, legal, or environmental.
4. **VIRAL CONTEXT** — Which issues are driving the most views, likes, and shares on TikTok?
5. **FYP PATTERNS** — Are there notable patterns in how viral content (sounds, trends, effects) frames these issues?
6. **CONCLUSION** — Overall picture of the TikTok conversation landscape in this period.

Use professional, structured, data-driven language.`
    },

    sentiment_breakdown: {
        label: 'Sentiment Breakdown',
        text: `Analyze the sentiment distribution of TikTok data for this project.

Context: ${CTX}

Tasks:
1. **Overall Sentiment Distribution** — Breakdown of positive, negative, and neutral videos/comments with percentages.
2. **Sentiment Drivers** — What topics/trends are driving positive vs negative sentiment on TikTok?
3. **Sentiment Trends** — How has sentiment shifted over the period?
4. **Most Positive Content** — Key videos/creators driving positive sentiment with their engagement metrics.
5. **Most Negative Content** — Key videos driving negative sentiment and their comment tone.
6. **Duet & Stitch Sentiment** — Is sentiment in response duets/stitches aligned or opposite to original videos?
7. **Sound & Trend Association** — Which trending sounds are associated with positive vs negative content?
8. **Recommendations** — How to amplify positive sentiment and mitigate negative sentiment on TikTok.`
    },

    top_creators: {
        label: 'Top Creators Analysis',
        text: `Analyze the top and most active TikTok creators engaging with this project's topics.

Context: ${CTX}

Tasks:
1. **Most Active Creators** — Which accounts posted the most content and what are their key messages?
2. **Most Viewed Content** — Which creators received the most views? What is their content style?
3. **Creator Profiles** — Categorize as: mega-influencer, micro-creator, news/media, brand, everyday user.
4. **Creator Sentiment Alignment** — Are top creators mostly positive, negative, or mixed?
5. **Engagement Quality** — Which creators drive genuine comments and shares vs passive views?
6. **Key Opinion Leaders (KOLs)** — Identify the 5 most influential TikTok voices and their stance.
7. **Follower Tier Analysis** — Compare reach vs engagement rates across creator tiers (nano to mega).
8. **Collaboration Opportunity** — Which creators should be prioritized for monitoring or brand collaboration?`
    },

    hashtag_analysis: {
        label: 'Hashtag Analysis',
        text: `Conduct a deep analysis of hashtags appearing in TikTok video captions and comments.

Context: ${CTX}

Tasks:
1. **Top Hashtags** — List top 15 hashtags with mention counts and context.
2. **Hashtag Sentiment** — Which hashtags are associated with positive vs negative video content?
3. **Campaign Hashtags** — Are there coordinated challenge or campaign hashtags (#challenge) being used?
4. **FYP Hashtags** — Identify which hashtags are optimized for the For You Page algorithm (#fyp, #foryou variants).
5. **Trending vs Niche** — Identify which hashtags are broadly trending vs niche community-specific.
6. **Hashtag Clusters** — Do different hashtag groups represent different TikTok communities or subcultures?
7. **Sound-Hashtag Correlation** — Are certain trending sounds paired with specific hashtags?
8. **Recommendations** — Which hashtags to use, monitor, or avoid in TikTok strategy.`
    },

    engagement_analysis: {
        label: 'Engagement Analysis',
        text: `Analyze TikTok engagement patterns (views, likes, comments, shares, duets) in this project's data.

Context: ${CTX}

Tasks:
1. **Engagement Overview** — Total views, likes, comments, shares and their distribution across videos.
2. **Highest Engagement Videos** — Top 5 videos by engagement. What made them go viral?
3. **Engagement by Sentiment** — Do positive or negative videos receive more engagement?
4. **Comment Section Dynamics** — Are comment sections predominantly supportive, critical, or meme-based?
5. **Duet & Stitch Activity** — Are there significant duet/stitch chains amplifying the topic?
6. **Like-to-View Ratio** — Engagement rate analysis and TikTok benchmarks comparison.
7. **Time-based Patterns** — When were the highest-engagement videos published? Peak hours?
8. **Recommendations** — What content formula drives the highest TikTok engagement for this project?`
    },

    swot: {
        label: 'SWOT Analysis',
        text: `Conduct a SWOT Analysis based on TikTok conversation data for this project.

Context: ${CTX}

**STRENGTHS** (from positive TikTok sentiment and high engagement):
- What positive narratives exist in video content and comment sections?
- Which creators are strong advocates with high follower counts?
- Which content formats (tutorials, POV, trending sounds) generate positive engagement?

**WEAKNESSES** (vulnerabilities revealed in TikTok conversation):
- What recurring criticisms appear in video comments and duet responses?
- Which topics consistently generate negative or mocking comment sections?

**OPPORTUNITIES** (positive factors visible in TikTok data):
- Which trending challenges or sounds align with a positive narrative?
- Which creator communities or subcultures could be mobilized positively?
- Are there underserved topics with high search/FYP demand?

**THREATS** (negative factors visible in TikTok data):
- Which negative narratives have viral potential through duets, stitches, or trending sounds?
- Who are the most influential critics with large follower bases?
- Are there organized negative trend/challenge campaigns visible?

**Strategic Recommendations**: Based on the SWOT, provide 3 priority TikTok communication actions.`
    },

    crisis: {
        label: 'Crisis Situation (SCCT)',
        text: `Apply the Situational Crisis Communication Theory (SCCT) framework to analyze crisis potential in this TikTok data.

Context: ${CTX}

1. **Crisis Type Classification** — Is this: Victim Cluster / Accident Cluster / Preventable Cluster? Explain based on TikTok video and comment evidence.
2. **Crisis Velocity** — How fast is the negative narrative spreading on TikTok (view count growth, duet chains, hashtag velocity)?
3. **Key Crisis Creators** — Who are the most vocal critics and what is their follower reach?
4. **Hashtag Crisis Signals** — Are there crisis-related hashtags or challenges trending on TikTok?
5. **Comment Section Sentiment** — What is the tone of comment sections on the most-viewed crisis-related videos?
6. **Duet/Stitch Amplification** — Are duets and stitches being used to spread or mock the crisis narrative?
7. **Sound Virality Risk** — Is there a trending sound being used to amplify the negative narrative?
8. **SCCT Response Strategy** — Based on crisis type and attribution, recommend: Deny / Diminish / Rebuild / Bolster.
9. **Key Messages** — Draft 3 TikTok-appropriate key messages aligned with the SCCT-recommended strategy.
10. **TikTok Response Timeline** — Immediate (1h), short-term (24h), medium-term (1 week).`
    },

    narrative: {
        label: 'Dominant Narrative',
        text: `Analyze the framing and dominant narrative patterns in TikTok data from this project.

Context: ${CTX}

1. **Dominant Narratives** — Most prominent positive, negative, and neutral narratives in TikTok video content.
2. **Framing Patterns** — How do creators frame issues in video captions, sounds, effects, and on-screen text?
3. **Narrative Evolution** — Has the dominant narrative shifted over the coverage period?
4. **Counter-Narratives** — Are there organized counter-narratives in duet responses or competing hashtag challenges?
5. **Key Voices** — Who is driving each narrative and what is their TikTok follower reach?
6. **Audio vs Visual Framing** — How do trending sounds reinforce or contradict the visual/caption narrative?
7. **Subculture Framing** — Are different TikTok subcultures (#BookTok, #PolitikTok, etc.) framing the issue differently?
8. **FYP Algorithm Amplification** — Which narratives appear to be gaining FYP algorithmic traction?
9. **Recommendations** — How to respond to and reframe existing negative narratives on TikTok?`
    },

    audience_analysis: {
        label: 'Audience Analysis',
        text: `Analyze the TikTok audience engaging with this project's topics.

Context: ${CTX}

1. **Audience Composition** — Who is consuming and commenting on this topic on TikTok? (mega-creators, micro-creators, media, everyday users)
2. **Most Active Segments** — Which audience segments are most engaged (by comment frequency and video views)?
3. **Sentiment by Segment** — How does sentiment differ across audience types?
4. **Follower Tiers** — Breakdown of nano, micro, mid-tier, macro, and mega creators engaging with the topic.
5. **Demographic Signals** — Any visible demographic patterns (Gen Z vs Millennial content styles, regional language use)?
6. **Content Consumption Patterns** — Do different audiences prefer short clips, tutorials, POV, or commentary-style content?
7. **Comment Behavior Analysis** — Are audiences engaging constructively, using memes/humor, or brigading?
8. **Audience Motivations** — What drives different audience segments to engage with this topic on TikTok?
9. **Targeting Recommendations** — Which audience segments should be prioritized in TikTok communication strategy?`
    },

    content_strategy: {
        label: 'Content Strategy',
        text: `Develop a TikTok content strategy based on conversation data from this project.

Context: ${CTX}

1. **Platform Situation Assessment** — Summary of the current TikTok conversation environment.
2. **Target Audiences** — Which creator/viewer segments are most important to reach?
3. **Key Messages** — 3–5 core messages tailored for TikTok's short-form video-first format.
4. **Content Types** — Which formats work best based on data (POV, tutorial, duet, stitch, trend participation, talking-head)?
5. **Sound & Music Strategy** — Which trending sounds or original audio should be leveraged?
6. **Hook & Caption Strategy** — Best practices for first-3-second hooks, on-screen text, and captions.
7. **Hashtag Strategy** — Which hashtags to use for FYP reach, community, and campaign.
8. **Optimal Posting Schedule** — When to post based on engagement patterns in this dataset.
9. **Creator Collaboration** — Which creator tiers and accounts to partner with for maximum FYP reach.
10. **Community Management** — How to respond to comments, duets, and stitches from critics.
11. **Success Metrics** — How to measure TikTok strategy effectiveness (views, watch time, shares, followers gained).`
    },

    early_warning: {
        label: 'Early Warning Signals',
        text: `Conduct an Early Warning analysis based on signals from the TikTok data.

Context: ${CTX}

1. **Danger Signals** — Identify 3–5 most concerning signals in the TikTok conversation.
2. **Risk Level** — For each signal: Low / Medium / High / Critical.
3. **Escalation Potential** — Which topics could go viral through duet chains, trending sounds, or FYP boosting?
4. **Velocity Assessment** — How fast is each negative topic growing in view/comment volume?
5. **Coordinated Activity** — Any signs of coordinated duet/stitch brigading or organized opposition creator networks?
6. **FYP Algorithm Risk** — Is the TikTok algorithm amplifying negative content in this topic space?
7. **Sound Virality Risk** — Are any trending sounds being used to mock or criticize the subject?
8. **Timeline Projection** — When is each risk likely to peak based on TikTok engagement patterns?
9. **Early Mitigation Steps** — Concrete, immediately actionable TikTok-specific steps.
10. **Monitoring Indicators** — What signals, hashtags, sounds, and creators should be watched daily?`
    },

    key_insights: {
        label: 'Key Insights & Actions',
        text: `Create an executive summary with key insights and action items from the TikTok data.

Context: ${CTX}

**EXECUTIVE SUMMARY** (2–3 sentences): Most critical overview of the TikTok conversation.

**5 KEY INSIGHTS:**
1. [Most important insight with TikTok video/creator evidence]
2. [Second insight]
3. [Third insight]
4. [Fourth insight]
5. [Fifth insight]

**TOP 3 RISKS:**
1. [Risk 1 — level, description, evidence]
2. [Risk 2]
3. [Risk 3]

**5 PRIORITY ACTION ITEMS:**
1. [Action 1 — what, who, when]
2. [Action 2]
3. [Action 3]
4. [Action 4]
5. [Action 5]

Keep language executive-level: concise, evidence-based, decision-oriented.`
    },

    comprehensive: {
        label: 'Analisis TikTok Komprehensif',
        text: `Buat laporan analisis TikTok komprehensif untuk project ini dalam format laporan media intelligence profesional.

Context: ${CTX}

Struktur laporan:

# LAPORAN ANALISIS TIKTOK — PROJECT ${PROJECT_ID}
**Periode:** ${START_DATE} s/d ${END_DATE}
**Platform:** TikTok
**Prepared by:** SMADIMENT AI Analyst

---

## 1. RINGKASAN EKSEKUTIF
Gambaran singkat situasi percakapan TikTok dan temuan utama (3-4 kalimat).

## 2. STATISTIK KUNCI
- Volume video & komentar, distribusi sentimen, jumlah creator aktif
- Hashtag teratas dan engagement metrics (views, likes, comments, shares)

## 3. PEMETAAN ISU UTAMA
Identifikasi minimal 5 isu dominan dengan evidence dari data video dan komentar TikTok.

## 4. ANALISIS SENTIMEN & NARASI
Distribusi sentimen, narasi dominan, dan framing yang digunakan dalam caption, sound, dan komentar.

## 5. ANALISIS SWOT
Strengths, Weaknesses, Opportunities, Threats berdasarkan data TikTok.

## 6. PROFIL CREATOR & KOL KUNCI
Top creators dengan follower tier, stance, dan estimasi reach. Identifikasi KOL yang paling berpengaruh.

## 7. ANALISIS ENGAGEMENT
Pola views, likes, comments, shares dan video dengan engagement tertinggi. Format konten terbaik.

## 8. ANALISIS HASHTAG & TREND
Top hashtags, kluster tematik, trending sounds, dan rekomendasi strategi hashtag TikTok.

## 9. SINYAL PERINGATAN DINI
Risiko viral negatif yang perlu diwaspadai dalam 7-14 hari ke depan, termasuk potensi duet/stitch brigading.

## 10. REKOMENDASI STRATEGIS
Minimal 5 rekomendasi konkret dan actionable untuk TikTok communication strategy.

## 11. LAMPIRAN: VIDEO REPRESENTATIF
Kumpulan video TikTok yang paling relevan sebagai evidence.

Gunakan bahasa profesional, berbasis data, dan hindari opini spekulatif tanpa evidence.`
    },
};

// ═══════════════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════════════
let chatHistory   = [];
let isLoading     = false;
let activeChip    = null;
let cachedDataset = null;
let dataReady     = false;

// ═══════════════════════════════════════════════════════════════════
// BOOT
// ═══════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    if (PROJECT_ID) {
        preloadProjectData();
    } else {
        setReady('No project selected — AI will answer general questions only', true);
    }
});

// ═══════════════════════════════════════════════════════════════════
// FETCH TIKTOK DATA
// ═══════════════════════════════════════════════════════════════════
async function preloadProjectData() {
    setStatus('loading', 'Loading data…');

    try {
        const qs = new URLSearchParams({
            project_id : PROJECT_ID,
            start_date : START_DATE,
            end_date   : END_DATE,
            sub        : 'postbylike',
            rows       : 100,
        });

        const [postsRes, sentimentRes, volumeRes] = await Promise.allSettled([
            fetch(`${ROUTES.mostViewedPosts}?${qs}`).then(r => r.json()),
            fetch(`${ROUTES.sentimentTotal}?${new URLSearchParams({ project_id: PROJECT_ID, start_date: START_DATE, end_date: END_DATE })}`).then(r => r.json()),
            fetch(`${ROUTES.volumeTotal}?${new URLSearchParams({ project_id: PROJECT_ID, start_date: START_DATE, end_date: END_DATE })}`).then(r => r.json()),
        ]);

        const posts     = (postsRes.status     === 'fulfilled' && postsRes.value.success)     ? (postsRes.value.data              ?? []) : [];
        const sentiment = (sentimentRes.status === 'fulfilled' && sentimentRes.value.success) ? (sentimentRes.value.data          ?? {}) : {};
        const volume    = (volumeRes.status    === 'fulfilled' && volumeRes.value.success)    ? (volumeRes.value.data?.total      ?? 0)  : 0;

        // Derive hashtags & active creators from posts
        const hashtagMap = {};
        const creatorMap = {};
        posts.forEach(p => {
            const content = p.content || p.caption || p.text || '';
            const matches = content.match(/#([a-zA-Z0-9_\u00C0-\u024F\u0400-\u04FF]+)/gu) || [];
            matches.forEach(h => {
                const tag = h.slice(1).toLowerCase();
                hashtagMap[tag] = (hashtagMap[tag] || 0) + 1;
            });
            const creator = p.author?.name || p.name || '';
            if (creator) creatorMap[creator] = (creatorMap[creator] || 0) + 1;
        });
        const hashtags    = Object.entries(hashtagMap).sort((a,b) => b[1]-a[1]).map(([n,s]) => ({name:n,size:s}));
        const activeUsers = Object.entries(creatorMap).sort((a,b) => b[1]-a[1]).slice(0,10).map(([n,c]) => ({username:n,posts:c}));

        cachedDataset = buildDataset(posts, hashtags, activeUsers, sentiment, volume);
        dataReady = true;

        const pos    = sentiment.positive || 0;
        const neg    = sentiment.negative || 0;
        const neu    = sentiment.neutral  || 0;
        const total  = pos + neg + neu || 1;
        const pctPos = Math.round(pos / total * 100);
        const pctNeg = Math.round(neg / total * 100);

        setReady(
            `🎵 ${posts.length} videos loaded &middot; Positive ${pctPos}% &middot; Negative ${pctNeg}% &middot; ` +
            `${hashtags.length} hashtags &middot; ${activeUsers.length} active creators &middot; ${START_DATE} → ${END_DATE}`
        );

    } catch (err) {
        console.error('[AI] preload failed:', err);
        cachedDataset = `=== DATA TIDAK TERSEDIA ===\nProject ID: ${PROJECT_ID}\nPlatform: TikTok\nPeriode: ${START_DATE} s/d ${END_DATE}\nGunakan pengetahuan umum social media monitoring.`;
        dataReady = true;
        setReady('Data load failed — AI will answer without live project data', true);
    }
}

// ═══════════════════════════════════════════════════════════════════
// BUILD DATASET
// ═══════════════════════════════════════════════════════════════════
function buildDataset(posts, hashtags, activeUsers, sentiment, volume) {
    const lines = [];
    const pos   = sentiment.positive || 0;
    const neg   = sentiment.negative || 0;
    const neu   = sentiment.neutral  || 0;
    const total = pos + neg + neu || 1;

    lines.push(`=== DATA TIKTOK PROJECT ${PROJECT_ID} ===`);
    lines.push(`Periode: ${START_DATE} s/d ${END_DATE}`);
    lines.push(`Total Volume: ${volume.toLocaleString()} video/komentar`);
    lines.push(`Sentimen: Positif ${Math.round(pos/total*100)}%(${pos}) | Negatif ${Math.round(neg/total*100)}%(${neg}) | Netral ${Math.round(neu/total*100)}%(${neu})`);
    lines.push('');

    if (hashtags.length > 0) {
        lines.push(`--- TOP HASHTAGS TIKTOK (${Math.min(hashtags.length, 20)}) ---`);
        hashtags.slice(0, 20).forEach((h, i) => lines.push(`${i+1}. #${h.name} (${h.size} mentions)`));
        lines.push('');
    }

    if (activeUsers.length > 0) {
        lines.push(`--- MOST ACTIVE TIKTOK CREATORS (${activeUsers.length}) ---`);
        activeUsers.forEach((u, i) => lines.push(`${i+1}. @${u.username} — ${u.posts} videos`));
        lines.push('');
    }

    if (posts.length > 0) {
        const negP = posts.filter(p => (p.sentiment_str||'').toLowerCase().includes('neg'));
        const posP = posts.filter(p => (p.sentiment_str||'').toLowerCase().includes('pos'));
        const neuP = posts.filter(p => !negP.includes(p) && !posP.includes(p));
        const sample = [...negP.slice(0,10), ...posP.slice(0,8), ...neuP.slice(0,5)];

        lines.push(`--- TOP TIKTOK VIDEOS BY LIKES (${sample.length} dari ${posts.length}) ---`);
        sample.forEach((p, i) => {
            const date    = (p.date_created || '').substring(0, 10);
            const author  = p.author?.name || p.name || 'Unknown Creator';
            const content = (p.content || p.caption || '').substring(0, 200).replace(/\n/g, ' ');
            const likes   = p.likes   ?? p.num_likes   ?? 0;
            const comments= p.comments?? p.num_comments?? 0;
            const views   = p.view_cnt?? p.views       ?? 0;
            const sent    = p.sentiment_str || 'Neutral';
            lines.push(`[T${i+1}] @${author} | ${date} | ${sent}`);
            lines.push(`   Views: ${views} | Likes: ${likes} | Comments: ${comments}`);
            if (content) lines.push(`   "${content}"`);
        });
    }

    lines.push('=== AKHIR DATASET ===');
    return lines.join('\n');
}

// ═══════════════════════════════════════════════════════════════════
// DATE PICKER
// ═══════════════════════════════════════════════════════════════════
function closeDatePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

(() => {
    let dpStart = null, dpEnd = null, dpSelectingStart = true;
    let dpMonth1 = new Date(), dpMonth2 = new Date();
    dpMonth2.setMonth(dpMonth2.getMonth() + 1);

    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    function dpFmt(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }

    function renderBoth() {
        renderCal(document.getElementById('dpCal1'), dpMonth1);
        renderCal(document.getElementById('dpCal2'), dpMonth2);
        updateDisplay();
    }

    function renderCal(el, month) {
        if (!el) return;
        const y = month.getFullYear(), m = month.getMonth();
        const today = new Date(); today.setHours(0,0,0,0);
        const first = new Date(y, m, 1).getDay();
        const days  = new Date(y, m+1, 0).getDate();
        let html = `<div class="calendar-month">${MONTHS[m]} ${y}</div>`;
        html += '<div class="calendar-weekdays">' + DAYS.map(d => `<span class="weekday">${d}</span>`).join('') + '</div>';
        html += '<div class="calendar-days">';
        for (let i = 0; i < first; i++) html += `<button class="calendar-day other-month" disabled></button>`;
        for (let d = 1; d <= days; d++) {
            const date = new Date(y, m, d); date.setHours(0,0,0,0);
            const dateStr = dpFmt(date);
            const isFuture = date > today;
            let cls = 'calendar-day';
            if (date.getTime() === today.getTime()) cls += ' today';
            if (dpStart && date.getTime() === dpStart.getTime()) cls += ' range-start selected';
            if (dpEnd   && date.getTime() === dpEnd.getTime())   cls += ' range-end selected';
            if (dpStart && dpEnd && date > dpStart && date < dpEnd) cls += ' in-range';
            if (isFuture) cls += ' disabled';
            html += `<button class="calendar-day ${cls}" data-date="${dateStr}" ${isFuture ? 'disabled' : ''}>${d}</button>`;
        }
        const last = new Date(y, m, days).getDay();
        for (let i = 1; i <= 6 - last; i++) html += `<button class="calendar-day other-month" disabled>${i}</button>`;
        html += '</div>';
        el.innerHTML = html;
        el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
            btn.addEventListener('click', () => {
                const date = new Date(btn.dataset.date); date.setHours(0,0,0,0);
                document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
                document.querySelector('[data-preset="custom"]')?.classList.add('active');
                if (dpSelectingStart || date < dpStart) { dpStart = date; dpEnd = date; dpSelectingStart = false; }
                else { dpEnd = date >= dpStart ? date : dpStart; if (date < dpStart) { dpEnd = dpStart; dpStart = date; } dpSelectingStart = true; }
                renderBoth();
            });
        });
    }

    function updateDisplay() {
        const el = document.getElementById('dpDisplay');
        if (el) el.textContent = dpStart && dpEnd ? `${dpFmt(dpStart)}  →  ${dpFmt(dpEnd)}` : 'Select date range';
    }

    function applyPreset(preset) {
        const today = new Date(); today.setHours(0,0,0,0);
        switch(preset) {
            case 'today':      dpStart = new Date(today); dpEnd = new Date(today); break;
            case 'yesterday':  dpStart = new Date(today); dpStart.setDate(today.getDate()-1); dpEnd = new Date(dpStart); break;
            case 'last7days':  dpEnd = new Date(today); dpStart = new Date(today); dpStart.setDate(today.getDate()-6); break;
            case 'last30days': dpEnd = new Date(today); dpStart = new Date(today); dpStart.setDate(today.getDate()-29); break;
            case 'thismonth':  dpStart = new Date(today.getFullYear(),today.getMonth(),1); dpEnd = new Date(today); break;
            case 'lastmonth':  dpStart = new Date(today.getFullYear(),today.getMonth()-1,1); dpEnd = new Date(today.getFullYear(),today.getMonth(),0); break;
        }
        if (preset !== 'custom' && dpStart) { dpMonth1 = new Date(dpStart); dpMonth2 = new Date(dpStart); dpMonth2.setMonth(dpMonth2.getMonth()+1); renderBoth(); }
    }

    function applyDatePicker() {
        if (!dpStart || !dpEnd) return;
        START_DATE = dpFmt(dpStart);
        END_DATE   = dpFmt(dpEnd);
        document.getElementById('dpTriggerLabel').textContent = `${START_DATE} to ${END_DATE}`;
        document.getElementById('headerSubtitle').textContent = `${PROJECT_ID} · ${START_DATE} to ${END_DATE}`;
        closeDatePicker();
        cachedDataset = null; dataReady = false;
        const inp = document.getElementById('chatInput'), sbtn = document.getElementById('sendBtn');
        if (inp)  { inp.disabled = true; inp.placeholder = 'Reloading data…'; }
        if (sbtn) sbtn.disabled = true;
        document.getElementById('ctxText').textContent = `Fetching TikTok data for ${START_DATE} to ${END_DATE}…`;
        preloadProjectData();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (START_DATE) { dpStart = new Date(START_DATE); dpStart.setHours(0,0,0,0); }
        if (END_DATE)   { dpEnd   = new Date(END_DATE);   dpEnd.setHours(0,0,0,0); }
        if (dpStart) { dpMonth1 = new Date(dpStart); dpMonth2 = new Date(dpStart); dpMonth2.setMonth(dpMonth2.getMonth()+1); }
        document.getElementById('datePickerTrigger')?.addEventListener('click', () => { document.getElementById('datePickerModal').classList.add('show'); renderBoth(); });
        document.getElementById('dpApply')?.addEventListener('click', applyDatePicker);
        document.getElementById('dpPrev')?.addEventListener('click', () => { dpMonth1.setMonth(dpMonth1.getMonth()-1); dpMonth2.setMonth(dpMonth2.getMonth()-1); renderBoth(); });
        document.getElementById('dpNext')?.addEventListener('click', () => { dpMonth1.setMonth(dpMonth1.getMonth()+1); dpMonth2.setMonth(dpMonth2.getMonth()+1); renderBoth(); });
        document.querySelectorAll('.date-preset').forEach(btn => {
            btn.addEventListener('click', () => { document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active')); btn.classList.add('active'); applyPreset(btn.dataset.preset); });
        });
        document.addEventListener('keydown', e => { if (e.key==='Escape') closeDatePicker(); });
    });
})();

// ═══════════════════════════════════════════════════════════════════
// CHIP
// ═══════════════════════════════════════════════════════════════════
function useChip(el, key) {
    if (activeChip === key) {
        el.classList.remove('active'); activeChip = null;
        const inp = document.getElementById('chatInput');
        inp.value = ''; inp.placeholder = 'Send a message...'; autoResize(inp); return;
    }
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active'); activeChip = key;
    const inp = document.getElementById('chatInput');
    inp.value = '';
    inp.placeholder = `Template: "${PROMPTS[key].label}" — tekan Send atau tambah pertanyaan…`;
    inp.focus();
}

function isAnalyticalMessage(text) {
    if (!text) return false;
    const keywords = ['analisis','analysis','analyze','analisa','tiktok','tt','video','creator','fyp','for you','duet','stitch','sound','trending','trend','hashtag','like','comment','share','view','follower','sentimen','sentiment','positif','negatif','negative','positive','isu','issue','topik','topic','konten','content','viral','mention','swot','pestle','scct','audience','narasi','narrative','krisis','crisis','komunikasi','communication','engagement','rangkum','ringkas','summarize','summary','pola','pattern','siapa','who','apa','what','bagaimana','how','mengapa','why','data','laporan','report','project','challenge','caption','fyp'];
    const lower = text.toLowerCase();
    return keywords.some(k => lower.includes(k));
}

// ═══════════════════════════════════════════════════════════════════
// SEND MESSAGE
// ═══════════════════════════════════════════════════════════════════
async function sendMessage() {
    if (isLoading || !dataReady) return;

    const chatInput = document.getElementById('chatInput').value.trim();
    let promptTemplate = '', displayLabel = '';

    if (activeChip && chatInput) {
        promptTemplate = PROMPTS[activeChip].text;
        displayLabel   = PROMPTS[activeChip].label + ' — ' + chatInput;
    } else if (activeChip) {
        promptTemplate = PROMPTS[activeChip].text;
        displayLabel   = PROMPTS[activeChip].label;
    } else if (chatInput) {
        promptTemplate = chatInput;
        displayLabel   = chatInput;
    } else { return; }

    document.getElementById('welcomeState')?.remove();
    appendMsg('user', displayLabel);

    const inp = document.getElementById('chatInput');
    inp.value = ''; inp.placeholder = 'Send a message...'; autoResize(inp);
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    activeChip = null;

    isLoading = true;
    document.getElementById('sendBtn').disabled = true;
    const typingEl = appendTypingWithLabel('Menganalisis data TikTok…');

    let finalPrompt = promptTemplate;
    if (chatInput && promptTemplate !== chatInput) finalPrompt += '\n\nPertanyaan tambahan: ' + chatInput;
    if (cachedDataset) finalPrompt += '\n\n' + cachedDataset;

    chatHistory.push({ role: 'user', content: finalPrompt });
    if (chatHistory.length > 40) chatHistory = chatHistory.slice(-40);

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch(ROUTES.aiProxy, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify({ max_tokens: 2000, system: buildSystemPrompt(), messages: chatHistory }),
        });
        const data = await res.json();
        typingEl.remove();

        if (data.error) {
            appendMsg('ai', '⚠️ ' + escHtml(data.error));
        } else {
            const reply = data.content?.[0]?.text ?? data.choices?.[0]?.message?.content ?? data.text ?? '';
            if (reply) { chatHistory.push({ role: 'assistant', content: reply }); appendMsg('ai', reply); }
            else appendMsg('ai', 'Tidak ada respons dari AI. Silakan coba lagi.');
        }
    } catch (err) {
        typingEl.remove();
        appendMsg('ai', '⚠️ Connection error: ' + escHtml(err.message));
    } finally {
        isLoading = false;
        document.getElementById('sendBtn').disabled = false;
    }
}

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan TikTok secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : TikTok
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip nama creator (@username), caption video, jumlah views/likes/comments sebagai evidence.
3. Identifikasi isu nyata dari konten video dan pola engagement TikTok.
4. Gunakan data sentimen, hashtag, dan most active creators untuk mendukung analisis.
5. Perhatikan karakteristik TikTok: short-form video (15 detik - 10 menit), For You Page (FYP) algorithm, duet & stitch culture, trending sounds, creator economy, Gen Z audience, hashtag challenges.
6. Pertimbangkan format konten: POV, tutorial, talking-head, duet, stitch, trending sound participation.
7. Waspadai sinyal viral: rapid view count growth, duet/stitch chains, sound adoption rate, comment section tone.

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Setiap insight harus actionable dan didukung referensi spesifik dari data.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}

// ═══════════════════════════════════════════════════════════════════
// UI HELPERS
// ═══════════════════════════════════════════════════════════════════
function appendMsg(role, text) {
    const container = document.getElementById('aiMessages');
    const now = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const el  = document.createElement('div');
    const isAI = role === 'ai';
    const wrapStyle   = `display:flex;gap:10px;animation:msgIn .22s ease;max-width:100%;flex-direction:${isAI ? 'row' : 'row-reverse'};align-items:flex-start;`;
    const avaStyle    = `width:32px;height:32px;min-width:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;font-family:inherit;color:#ffffff !important;background:${isAI ? 'linear-gradient(135deg,#038047 0%,#026738 100%)' : 'linear-gradient(135deg,#EE1D52 0%,#69C9D0 100%)'} !important;box-shadow:${isAI ? '0 2px 8px rgba(3,128,71,0.25)' : '0 2px 8px rgba(238,29,82,0.25)'};`;
    const bodyStyle   = `display:flex;flex-direction:column;max-width:78%;gap:4px;align-items:${isAI ? 'flex-start' : 'flex-end'};`;
    const bubbleStyle = isAI
        ? `background-color:#ffffff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.75 !important;color:#1a202c !important;box-shadow:0 1px 4px rgba(0,0,0,0.06) !important;word-break:break-word !important;font-family:inherit !important;`
        : `background:linear-gradient(135deg,#EE1D52 0%,#69C9D0 100%) !important;border:none !important;border-radius:14px 3px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.6 !important;color:#ffffff !important;box-shadow:0 2px 10px rgba(238,29,82,0.3) !important;word-break:break-word !important;font-family:inherit !important;`;
    const bubbleContent = isAI ? formatMarkdown(text) : `<span style="color:#fff">${escHtml(text)}</span>`;
    el.style.cssText = wrapStyle;
    el.innerHTML = `
        <div style="${avaStyle}">${isAI ? 'AI' : 'U'}</div>
        <div style="${bodyStyle}">
            <div style="${bubbleStyle}">${bubbleContent}</div>
            <div style="font-size:10px;color:#cbd5e1;padding:0 4px;">${now}</div>
        </div>`;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function appendTypingWithLabel(label) {
    const container = document.getElementById('aiMessages');
    const el = document.createElement('div');
    el.style.cssText = 'display:flex;gap:10px;align-items:flex-start;animation:msgIn .22s ease;';
    el.innerHTML = `
        <div style="width:32px;height:32px;min-width:32px;border-radius:9px;background:linear-gradient(135deg,#038047 0%,#026738 100%) !important;color:#ffffff !important;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;box-shadow:0 2px 8px rgba(3,128,71,0.25);">AI</div>
        <div style="display:flex;flex-direction:column;gap:5px;">
            <div style="display:flex;gap:5px;align-items:center;padding:13px 16px;background-color:#ffffff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                <div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>
            </div>
            <span style="font-size:11px;color:#94a3b8;padding-left:2px;">${escHtml(label)}</span>
        </div>`;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function setStatus(type, text) {
    const pill = document.getElementById('statusPill');
    const span = document.getElementById('statusText');
    if (pill) pill.className = 'status-pill' + (type !== 'online' ? ` ${type}` : '');
    if (span) span.textContent = text;
}

function setReady(ctxMessage, isWarn = false) {
    setStatus(isWarn ? 'error' : 'online', isWarn ? 'Limited' : 'Online');
    const ctxText = document.getElementById('ctxText');
    if (ctxText) ctxText.innerHTML = ctxMessage;
    document.getElementById('dataLoadingBadge')?.remove();
    const inp = document.getElementById('chatInput');
    const btn = document.getElementById('sendBtn');
    if (inp) { inp.disabled = false; inp.placeholder = 'Send a message…'; }
    if (btn) btn.disabled = false;
}

function clearChat() {
    if (!chatHistory.length) return;
    if (!confirm('Clear conversation history?')) return;
    chatHistory = [];
    document.getElementById('aiMessages').innerHTML = `
        <div class="welcome-state" id="welcomeState">
            <div class="welcome-icon-wrap">
                <svg viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
                </svg>
            </div>
            <h3>Ready to Analyze TikTok</h3>
            <p>Select a template below or type your own question to analyze TikTok conversation data for this project.</p>
        </div>`;
}

function autoResize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 120) + 'px'; }

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatMarkdown(text) {
    if (!text) return '';
    let h = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    h = h.replace(/```[\w]*\n?([\s\S]*?)```/g,'<pre style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;overflow-x:auto;margin:8px 0;"><code style="font-size:12px;color:#1a202c;background:transparent;border:none;padding:0;">$1</code></pre>');
    h = h.replace(/`([^`]+)`/g,'<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-size:12px;border:1px solid #e2e8f0;">$1</code>');
    h = h.replace(/^### (.+)$/gm,'<h4 style="font-size:13px;font-weight:700;margin:12px 0 5px;color:#EE1D52;">$1</h4>');
    h = h.replace(/^## (.+)$/gm, '<h3 style="font-size:14px;font-weight:700;margin:14px 0 6px;color:#EE1D52;">$1</h3>');
    h = h.replace(/^# (.+)$/gm,  '<h2 style="font-size:15px;font-weight:700;margin:16px 0 7px;color:#c41443;">$1</h2>');
    h = h.replace(/\*\*\*(.+?)\*\*\*/g,'<strong style="color:#1a202c;font-weight:700;"><em>$1</em></strong>');
    h = h.replace(/\*\*(.+?)\*\*/g,'<strong style="color:#1a202c;font-weight:700;">$1</strong>');
    h = h.replace(/\*(.+?)\*/g,'<em>$1</em>');
    h = h.replace(/^---$/gm,'<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">');
    h = h.replace(/((?:^[-*•] .+(?:\n|$))+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^[-*•] /, '').trim()}</li>`).join('');
        return `<ul style="margin:6px 0 10px;padding-left:20px;color:#1a202c;">${items}</ul>`;
    });
    h = h.replace(/((?:^\d+\. .+(?:\n|$))+)/gm, (block) => {
        const items = block.trim().split('\n').map(l => `<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^\d+\. /, '').trim()}</li>`).join('');
        return `<ol style="margin:6px 0 10px;padding-left:20px;color:#1a202c;">${items}</ol>`;
    });
    h = h.split(/\n{2,}/).map(para => {
        para = para.trim();
        if (!para) return '';
        if (/^<(h[2-4]|ul|ol|pre|hr)/.test(para)) return para;
        return `<p style="margin:0 0 8px;color:#1a202c;">${para.replace(/\n/g, '<br>')}</p>`;
    }).join('\n');
    return h;
}
</script>
@endsection