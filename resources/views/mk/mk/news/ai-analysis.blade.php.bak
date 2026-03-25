{{--
    ============================================================
    News AI Analysis — Blade
    Struktur SAMA PERSIS dengan mk.x.ai-analysis:
    - Sidebar prompt groups (collapsible)
    - @include('mk.ai.partials.prompts')
    - 1 endpoint aiAnalysisData
    - Proxy ke Gemini via aiProxy
    ============================================================
--}}

@extends('mk.layouts.app')

@section('title', 'AI Analysis - News')

@section('styles')
<style>
    :root {
        --primary: #038047;
        --primary-dark: #026738;
        --primary-light: rgba(3,128,71,0.08);
        --brand: #038047;
        --brand-dark: #026738;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
        --sidebar-w: 260px;
    }

    .ai-shell {
        display: flex;
        height: calc(100vh - 80px);
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    /* ── SIDEBAR ── */
    .prompt-sidebar {
        width: var(--sidebar-w); min-width: var(--sidebar-w);
        background: #f8fafc; border-right: 1px solid var(--border);
        display: flex; flex-direction: column; overflow: hidden;
        transition: width 0.25s ease, min-width 0.25s ease; flex-shrink: 0;
    }
    .prompt-sidebar.collapsed { width: 48px; min-width: 48px; }

    .sidebar-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 14px 10px; border-bottom: 1px solid var(--border); flex-shrink: 0;
    }
    .sidebar-title {
        font-size: 11px; font-weight: 800; color: var(--text-secondary);
        text-transform: uppercase; letter-spacing: 0.08em;
        white-space: nowrap; overflow: hidden; opacity: 1; transition: opacity 0.2s;
    }
    .prompt-sidebar.collapsed .sidebar-title { opacity: 0; }

    .sidebar-toggle {
        width: 26px; height: 26px; border: none; background: transparent;
        border-radius: 7px; cursor: pointer; display: flex; align-items: center;
        justify-content: center; color: var(--text-secondary);
        transition: background 0.15s, color 0.15s; flex-shrink: 0;
    }
    .sidebar-toggle:hover { background: var(--border); color: var(--text-primary); }
    .sidebar-toggle svg { width: 15px; height: 15px; transition: transform 0.25s; }
    .prompt-sidebar.collapsed .sidebar-toggle svg { transform: rotate(180deg); }

    .sidebar-search {
        padding: 10px 12px; border-bottom: 1px solid var(--border);
        flex-shrink: 0; transition: height 0.25s, padding 0.25s, opacity 0.2s;
        height: 50px; opacity: 1; overflow: hidden;
    }
    .prompt-sidebar.collapsed .sidebar-search { height: 0; padding: 0; opacity: 0; }
    .sidebar-search-input {
        width: 100%; padding: 6px 10px 6px 30px;
        border: 1px solid var(--border); border-radius: 8px;
        font-size: 12px; font-family: inherit; background: #fff;
        color: var(--text-primary); outline: none; transition: border-color 0.15s; box-sizing: border-box;
    }
    .sidebar-search-input:focus { border-color: var(--primary); }
    .sidebar-search-wrap { position: relative; }
    .sidebar-search-wrap svg {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 13px; height: 13px; color: var(--text-secondary); pointer-events: none;
    }

    .sidebar-body {
        flex: 1; overflow-y: auto; overflow-x: hidden; padding: 8px 0;
        scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
    }
    .sidebar-body::-webkit-scrollbar { width: 3px; }
    .sidebar-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .prompt-group { margin-bottom: 4px; }
    .prompt-group-header {
        display: flex; align-items: center; gap: 6px;
        padding: 6px 14px 4px; cursor: pointer; user-select: none;
    }
    .prompt-group-header:hover { background: rgba(0,0,0,0.02); }
    .prompt-sidebar.collapsed .prompt-group-header { justify-content: center; padding: 6px; }

    .group-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .group-label {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--text-secondary);
        flex: 1; white-space: nowrap; overflow: hidden; opacity: 1; transition: opacity 0.2s;
    }
    .prompt-sidebar.collapsed .group-label { opacity: 0; width: 0; }
    .group-chevron { width: 12px; height: 12px; color: var(--text-secondary); transition: transform 0.2s; flex-shrink: 0; }
    .prompt-sidebar.collapsed .group-chevron { display: none; }
    .prompt-group.open .group-chevron { transform: rotate(90deg); }
    .prompt-group-items { overflow: hidden; max-height: 0; transition: max-height 0.25s ease; }
    .prompt-group.open .prompt-group-items { max-height: 9999px; }
    .prompt-sidebar.collapsed .prompt-group-items { display: none; }

    .prompt-item {
        display: flex; align-items: center; gap: 8px;
        padding: 7px 14px 7px 24px; cursor: pointer;
        font-size: 12.5px; font-weight: 500; color: var(--text-secondary);
        border: none; background: transparent; font-family: inherit;
        width: 100%; text-align: left; transition: background 0.12s, color 0.12s;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.35;
    }
    .prompt-item:hover { background: rgba(3,128,71,0.06); color: var(--primary); }
    .prompt-item.active {
        background: rgba(3,128,71,0.1); color: var(--primary);
        font-weight: 700; border-right: 3px solid var(--primary);
    }
    .prompt-item-dot {
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--border); flex-shrink: 0; transition: background 0.12s;
    }
    .prompt-item:hover .prompt-item-dot,
    .prompt-item.active .prompt-item-dot { background: var(--primary); }
    .prompt-sidebar.collapsed .prompt-item { display: none; }

    /* ── MAIN ── */
    .ai-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    .ai-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 18px; border-bottom: 1px solid #f1f5f9;
        background: #ffffff; flex-shrink: 0;
    }
    .ai-header-left { display: flex; align-items: center; gap: 10px; }
    .ai-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #038047 0%, #026738 100%);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ai-header-info h4 { font-size: 13.5px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .ai-header-info p  { font-size: 11px; color: var(--text-secondary); margin: 0; }
    .ai-header-right { display: flex; align-items: center; gap: 8px; }

    .date-picker-trigger {
        display: flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 9px;
        border: 1px solid var(--border); background: #f8fafc; cursor: pointer;
        font-size: 11.5px; font-weight: 600; color: var(--text-primary);
        font-family: inherit; transition: all 0.15s;
    }
    .date-picker-trigger:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .date-picker-trigger svg { width: 13px; height: 13px; color: var(--text-secondary); flex-shrink: 0; }

    .status-pill {
        display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px;
        background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 600;
        color: #16a34a; white-space: nowrap;
    }
    .status-pill.loading { background: #fefce8; border-color: #fde68a; color: #ca8a04; }
    .status-pill.error   { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; flex-shrink: 0; }
    .status-pill.loading .status-dot { background: #ca8a04; animation: pulse 1s infinite; }
    .status-pill.error   .status-dot { background: #dc2626; }

    .btn-clear {
        padding: 5px 12px; border-radius: 8px; border: 1px solid var(--border);
        background: #f8fafc; font-size: 11.5px; font-weight: 600; color: #64748b;
        cursor: pointer; font-family: inherit; transition: all 0.15s;
    }
    .btn-clear:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

    .active-prompt-bar {
        padding: 7px 18px; background: rgba(3,128,71,0.06);
        border-bottom: 1px solid rgba(3,128,71,0.12);
        display: none; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .active-prompt-bar.show { display: flex; }
    .active-prompt-tag {
        display: flex; align-items: center; gap: 6px; padding: 3px 10px;
        border-radius: 20px; background: var(--primary); color: #fff;
        font-size: 11px; font-weight: 700;
    }
    .active-prompt-tag button {
        background: none; border: none; color: rgba(255,255,255,0.8);
        cursor: pointer; padding: 0; font-size: 14px; line-height: 1;
    }
    .active-prompt-tag button:hover { color: #fff; }
    .active-prompt-hint { font-size: 11px; color: var(--text-secondary); }

    .ai-messages {
        flex: 1; overflow-y: auto; padding: 18px 20px;
        display: flex; flex-direction: column; gap: 14px; background: #fafbfc;
        scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
    }
    .ai-messages::-webkit-scrollbar { width: 4px; }
    .ai-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }

    .welcome-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; text-align: center; padding: 40px 20px; flex: 1; gap: 10px;
    }
    .welcome-icon-wrap {
        width: 56px; height: 56px; border-radius: 16px;
        background: linear-gradient(135deg, #038047 0%, #026738 100%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(3,128,71,0.28);
    }
    .welcome-state h3 { font-size: 17px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .welcome-state p  { font-size: 12.5px; color: var(--text-secondary); margin: 0; max-width: 340px; line-height: 1.6; }
    .data-loading-badge {
        display: flex; align-items: center; gap: 8px; padding: 7px 14px;
        background: #fff; border: 1px solid var(--border); border-radius: 20px;
        font-size: 11.5px; color: var(--text-secondary);
    }
    .spin {
        width: 13px; height: 13px; border: 2px solid #e2e8f0;
        border-top-color: var(--primary); border-radius: 50%;
        animation: spin 0.8s linear infinite; flex-shrink: 0;
    }

    .ai-input-area { border-top: 1px solid var(--border); background: #ffffff; padding: 10px 16px 12px; flex-shrink: 0; }
    .input-row { display: flex; gap: 8px; align-items: flex-end; }
    .chat-textarea {
        flex: 1; resize: none; border: 1px solid var(--border); border-radius: 12px;
        padding: 10px 14px; font-size: 13px; font-family: inherit; line-height: 1.5;
        background: #f8fafc; color: var(--text-primary); outline: none;
        transition: border-color 0.15s, background 0.15s; min-height: 42px; max-height: 120px; overflow-y: auto;
    }
    .chat-textarea:focus { border-color: var(--primary); background: #fff; }
    .chat-textarea:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-send {
        width: 40px; height: 40px; border-radius: 11px; border: none;
        background: linear-gradient(135deg, #038047 0%, #026738 100%);
        color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 3px 10px rgba(3,128,71,0.32); transition: all 0.15s; flex-shrink: 0;
    }
    .btn-send:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(3,128,71,0.42); }
    .btn-send:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-send svg { width: 16px; height: 16px; }
    .input-hint { font-size: 10.5px; color: #cbd5e1; text-align: center; margin-top: 5px; }
    .typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; animation: typing 1.2s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes spin    { to { transform: rotate(360deg); } }
    @keyframes pulse   { 0%,100% { opacity:1; } 50% { opacity:.4; } }
    @keyframes typing  { 0%,80%,100% { transform:scale(0.8); opacity:.5; } 40% { transform:scale(1.1); opacity:1; } }
    @keyframes msgIn   { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    /* ── DATE PICKER ── */
    .date-picker-modal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.35); backdrop-filter: blur(2px); }
    .date-picker-container {
        position: relative; z-index: 1; background: #fff; border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15); display: flex; overflow: hidden; max-width: 680px; width: 95%;
    }
    .date-picker-sidebar {
        width: 140px; background: #f8fafc; border-right: 1px solid #e2e8f0;
        padding: 16px 10px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
    }
    .date-preset {
        padding: 8px 12px; border-radius: 8px; border: none; background: transparent;
        font-family: inherit; font-size: 12px; font-weight: 600; color: #475569;
        cursor: pointer; text-align: left; transition: all 0.15s;
    }
    .date-preset:hover { background: #e2e8f0; }
    .date-preset.active { background: var(--primary); color: #fff; }
    .date-picker-content { flex: 1; padding: 18px; display: flex; flex-direction: column; gap: 14px; }
    .date-picker-nav { display: flex; align-items: flex-start; gap: 10px; }
    .dp-nav-btn {
        width: 34px; height: 34px; border-radius: 9px; background: #f8fafc;
        border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0; color: #374151;
    }
    .dp-nav-btn:hover { background: var(--primary); border-color: var(--primary); color: #fff; }
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
        transition: all 0.15s; color: #1a202c; background: transparent; border: none; padding: 0; font-family: inherit;
    }
    .calendar-day:hover:not(:disabled):not(.other-month) { background: #f1f5f9; }
    .calendar-day.other-month { color: #e2e8f0; cursor: default; }
    .calendar-day:disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid var(--primary); font-weight: 700; }
    .calendar-day.selected, .calendar-day.range-start, .calendar-day.range-end { background: var(--primary); color: #fff !important; }
    .calendar-day.in-range { background: rgba(3,128,71,0.1); color: var(--primary); }
    .dp-display {
        padding: 10px 16px; background: #f8fafc; border-radius: 10px;
        text-align: center; margin: 14px 0 10px; border: 1px solid #e2e8f0;
        font-size: 13px; font-weight: 600; color: #1a202c;
    }
    .dp-footer { display: flex; gap: 10px; justify-content: flex-end; }
    .dp-cancel {
        padding: 9px 20px; border-radius: 9px; border: 1px solid #e2e8f0; background: #f8fafc;
        color: #374151; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s;
    }
    .dp-cancel:hover { background: #f1f5f9; }
    .dp-apply {
        padding: 9px 20px; border-radius: 9px; border: none;
        background: linear-gradient(135deg, #038047 0%, #026738 100%);
        color: #fff; font-family: inherit; font-size: 13px; font-weight: 600;
        cursor: pointer; box-shadow: 0 3px 12px rgba(3,128,71,0.3); transition: all 0.15s;
    }
    .dp-apply:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(3,128,71,0.4); }

    @media (max-width: 768px) {
        .prompt-sidebar { display: none; }
        .date-picker-sidebar { display: none; }
        .calendars-wrapper { flex-direction: column; }
        .ai-header-right { gap: 5px; }
        .date-picker-trigger span { display: none; }
    }
</style>
@endsection

@section('content')

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

<div class="ai-shell">

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

    <div class="ai-main">

        <div class="ai-header">
            <div class="ai-header-left">
                <div class="ai-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="18" height="18">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                        <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/>
                    </svg>
                </div>
                <div class="ai-header-info">
                    <h4>News AI Research</h4>
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
                <div class="welcome-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="28" height="28">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                        <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/>
                    </svg>
                </div>
                <h3>Ready to Analyze News</h3>
                <p>Pilih template dari panel kiri, atau ketik pertanyaan sendiri untuk menganalisis data pemberitaan online pada project ini.</p>
                <div class="data-loading-badge" id="dataLoadingBadge">
                    <div class="spin"></div>
                    Memuat data berita…
                </div>
            </div>
        </div>

        <div class="ai-input-area">
            <div class="input-row">
                <textarea
                    class="chat-textarea" id="chatInput"
                    placeholder="Memuat data…" rows="1" disabled
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                    oninput="autoResize(this)"
                ></textarea>
                <button class="btn-send" id="sendBtn" onclick="sendMessage()" disabled>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <div class="input-hint">Enter untuk kirim &middot; Shift+Enter untuk baris baru</div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
const PROJECT_ID = '{{ $projectId ?? "" }}';
const PLATFORM   = 'News';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';

const ROUTES = {
    aiAnalysisData : '{{ route("mk.api.news.ai-analysis-data") }}',
    aiProxy        : '{{ route("mk.api.news.ai-proxy") }}',
};
</script>

@include('mk.ai.partials.prompts')

<script>
@verbatim
const PROMPT_GROUPS = [
    { key: 'isu',      label: 'Analisis Isu',          color: '#038047', keys: ['butterfly_effect','isu_positif_negatif','isu_swot','analisis_percakapan','analisis_agenda_setting','narrative_analysis'] },
    { key: 'krisis',   label: 'Krisis & Trust',         color: '#e53e3e', keys: ['krisis_scct','edelman_trust'] },
    { key: 'framing',  label: 'Framing & Wacana',       color: '#7c3aed', keys: ['framing_entman_edelman','framing_entman','cda_fairclough','analisis_wacana_vandijk','analisis_wacana_wodak'] },
    { key: 'strategi', label: 'Strategi & Komunikasi',  color: '#1877F2', keys: ['pestle','uses_gratifications','stakeholder_mapping','strategi_riding_the_wave','strategi_counter_narrative','analisis_isu_parpol'] },
    { key: 'intelijen',label: 'Intelijen',               color: '#d97706', keys: ['analisis_intelijen_mcdowell','analisis_intelijen_prunckun','analisis_intelijen_sherman_kent','hybrid_warfare_info_ops'] },
    { key: 'laporan',  label: 'Laporan Pimpinan',        color: '#0f766e', keys: ['laporan_direksi','laporan_pimpinan_kapolri','laporan_presiden','laporan_harian_bank'] },
];

function renderSidebar(filter = '') {
    const body = document.getElementById('sidebarBody');
    const fLow = filter.toLowerCase();
    body.innerHTML = '';
    PROMPT_GROUPS.forEach(group => {
        const matched = group.keys.filter(k => PROMPTS[k] && (!fLow || PROMPTS[k].label.toLowerCase().includes(fLow)));
        if (!matched.length) return;
        const el = document.createElement('div');
        el.className = 'prompt-group open';
        el.innerHTML = `
            <div class="prompt-group-header" onclick="toggleGroup(this.parentElement)">
                <span class="group-dot" style="background:${group.color}"></span>
                <span class="group-label">${group.label}</span>
                <svg class="group-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div class="prompt-group-items">
                ${matched.map(k => `<button class="prompt-item" data-key="${k}" onclick="selectPrompt('${k}',this)"><span class="prompt-item-dot"></span>${PROMPTS[k].label}</button>`).join('')}
            </div>`;
        body.appendChild(el);
    });
}

function toggleGroup(el)  { el.classList.toggle('open'); }
function filterPrompts(v) { renderSidebar(v); }

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('promptSidebar').classList.toggle('collapsed');
    });
});

let activeChip = null;

function selectPrompt(key, el) {
    if (!PROMPTS[key]) return;
    if (activeChip === key) { clearActivePrompt(); return; }
    document.querySelectorAll('.prompt-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    activeChip = key;
    const inp = document.getElementById('chatInput');
    inp.value = PROMPTS[key].text;
    autoResize(inp); inp.focus();
    document.getElementById('activePromptLabel').textContent = PROMPTS[key].label;
    document.getElementById('activePromptBar').classList.add('show');
}

function clearActivePrompt() {
    activeChip = null;
    document.querySelectorAll('.prompt-item').forEach(i => i.classList.remove('active'));
    document.getElementById('activePromptBar').classList.remove('show');
    const inp = document.getElementById('chatInput');
    inp.value = ''; inp.placeholder = 'Kirim pesan…'; autoResize(inp);
}

let chatHistory = [], isLoading = false, cachedDataset = null, dataReady = false;

document.addEventListener('DOMContentLoaded', () => {
    renderSidebar();
    PROJECT_ID ? preloadProjectData() : setReady('Tidak ada project yang dipilih', true);
});

async function preloadProjectData() {
    setStatus('loading', 'Memuat data…');
    try {
        const qs  = new URLSearchParams({ project_id: PROJECT_ID, start_date: START_DATE, end_date: END_DATE });
        const res = await fetch(`${ROUTES.aiAnalysisData}?${qs}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error);
        cachedDataset = json.data.dataset;
        dataReady     = true;
        const s = json.data.summary;
        setReady(`${s.total_articles} articles · ${s.total_publishers} publishers · ${s.total_keywords} keywords`);
    } catch (err) {
        cachedDataset = `Project ID: ${PROJECT_ID}, Platform: News, Periode: ${START_DATE} s/d ${END_DATE}`;
        dataReady = true;
        setReady('Data gagal dimuat — menjawab tanpa data live', true);
    }
}

function closeDatePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

(() => {
    let dpStart = null, dpEnd = null, dpSel = true;
    let dpM1 = new Date(), dpM2 = new Date();
    dpM2.setMonth(dpM2.getMonth() + 1);
    const MON = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const DAY = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    function fmt(d) { return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
    function both() { cal(document.getElementById('dpCal1'),dpM1); cal(document.getElementById('dpCal2'),dpM2); disp(); }

    function cal(el, m) {
        if (!el) return;
        const y = m.getFullYear(), mo = m.getMonth();
        const today = new Date(); today.setHours(0,0,0,0);
        const first = new Date(y,mo,1).getDay(), days = new Date(y,mo+1,0).getDate();
        let h = `<div class="calendar-month">${MON[mo]} ${y}</div>`;
        h += '<div class="calendar-weekdays">' + DAY.map(d=>`<span class="weekday">${d}</span>`).join('') + '</div>';
        h += '<div class="calendar-days">';
        for (let i=0;i<first;i++) h += `<button class="calendar-day other-month" disabled></button>`;
        for (let d=1;d<=days;d++) {
            const dt = new Date(y,mo,d); dt.setHours(0,0,0,0);
            let c = 'calendar-day';
            if (dt.getTime()===today.getTime()) c+=' today';
            if (dpStart && dt.getTime()===dpStart.getTime()) c+=' range-start selected';
            if (dpEnd   && dt.getTime()===dpEnd.getTime())   c+=' range-end selected';
            if (dpStart && dpEnd && dt>dpStart && dt<dpEnd)  c+=' in-range';
            h += `<button class="${c}" data-date="${fmt(dt)}" ${dt>today?'disabled':''}>${d}</button>`;
        }
        const rem = 6 - new Date(y,mo,days).getDay();
        for (let i=1;i<=rem;i++) h+=`<button class="calendar-day other-month" disabled>${i}</button>`;
        h += '</div>';
        el.innerHTML = h;
        el.querySelectorAll('.calendar-day:not(.other-month):not([disabled])').forEach(btn => {
            btn.addEventListener('click', () => {
                const dt = new Date(btn.dataset.date); dt.setHours(0,0,0,0);
                document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
                document.querySelector('[data-preset="custom"]')?.classList.add('active');
                if (dpSel || dt < dpStart) { dpStart=dt; dpEnd=dt; dpSel=false; }
                else { dpEnd = dt>=dpStart?dt:dpStart; if(dt<dpStart){dpEnd=dpStart;dpStart=dt;} dpSel=true; }
                both();
            });
        });
    }

    function disp() {
        const el = document.getElementById('dpDisplay');
        if (el) el.textContent = dpStart && dpEnd ? `${fmt(dpStart)}  →  ${fmt(dpEnd)}` : 'Select date range';
    }

    function preset(p) {
        const t=new Date(); t.setHours(0,0,0,0);
        const ps={today:()=>{dpStart=new Date(t);dpEnd=new Date(t);},yesterday:()=>{dpStart=new Date(t);dpStart.setDate(t.getDate()-1);dpEnd=new Date(dpStart);},last7days:()=>{dpEnd=new Date(t);dpStart=new Date(t);dpStart.setDate(t.getDate()-6);},last30days:()=>{dpEnd=new Date(t);dpStart=new Date(t);dpStart.setDate(t.getDate()-29);},thismonth:()=>{dpStart=new Date(t.getFullYear(),t.getMonth(),1);dpEnd=new Date(t);},lastmonth:()=>{dpStart=new Date(t.getFullYear(),t.getMonth()-1,1);dpEnd=new Date(t.getFullYear(),t.getMonth(),0);}};
        if(ps[p]){ps[p]();dpM1=new Date(dpStart);dpM2=new Date(dpStart);dpM2.setMonth(dpM2.getMonth()+1);both();}
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (START_DATE) { dpStart=new Date(START_DATE); dpStart.setHours(0,0,0,0); }
        if (END_DATE)   { dpEnd  =new Date(END_DATE);   dpEnd.setHours(0,0,0,0); }
        if (dpStart) { dpM1=new Date(dpStart); dpM2=new Date(dpStart); dpM2.setMonth(dpM2.getMonth()+1); }

        document.getElementById('datePickerTrigger')?.addEventListener('click', ()=>{ document.getElementById('datePickerModal').classList.add('show'); both(); });
        document.getElementById('dpApply')?.addEventListener('click', ()=>{
            if (!dpStart||!dpEnd) return;
            START_DATE=fmt(dpStart); END_DATE=fmt(dpEnd);
            document.getElementById('dpTriggerLabel').textContent=`${START_DATE} to ${END_DATE}`;
            document.getElementById('headerSubtitle').textContent=`${PROJECT_ID} · ${START_DATE} to ${END_DATE}`;
            closeDatePicker();
            cachedDataset=null; dataReady=false;
            const inp=document.getElementById('chatInput'),sbtn=document.getElementById('sendBtn');
            if(inp){inp.disabled=true;inp.placeholder='Memuat ulang data…';}
            if(sbtn) sbtn.disabled=true;
            preloadProjectData();
        });
        document.getElementById('dpPrev')?.addEventListener('click',()=>{dpM1.setMonth(dpM1.getMonth()-1);dpM2.setMonth(dpM2.getMonth()-1);both();});
        document.getElementById('dpNext')?.addEventListener('click',()=>{dpM1.setMonth(dpM1.getMonth()+1);dpM2.setMonth(dpM2.getMonth()+1);both();});
        document.querySelectorAll('.date-preset').forEach(btn=>{
            btn.addEventListener('click',()=>{document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));btn.classList.add('active');preset(btn.dataset.preset);});
        });
        document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeDatePicker(); });
    });
})();

function isAnalyticalMessage(text) {
    if (!text) return false;
    const kw = ['analisis','analysis','berita','news','artikel','article','publisher','media','penerbit','sentimen','sentiment','isu','issue','topik','topic','swot','pestle','scct','stakeholder','narasi','narrative','krisis','crisis','komunikasi','communication','rangkum','ringkas','summarize','tren','trend','pola','siapa','apa','bagaimana','mengapa','data','laporan','report','butterfly','framing','entman','edelman','wacana','fairclough','vandijk','wodak','intelijen','mcdowell','prunckun','sherman','kent','hybrid','warfare','agenda','counter','riding','parpol'];
    const l = text.toLowerCase();
    return kw.some(k=>l.includes(k));
}

async function sendMessage() {
    if (isLoading || !dataReady) return;
    const chatInput = document.getElementById('chatInput').value.trim();
    let promptText='', displayLabel='';
    if (activeChip && PROMPTS[activeChip]) {
        promptText   = chatInput || PROMPTS[activeChip].text;
        displayLabel = PROMPTS[activeChip].label;
    } else if (chatInput) {
        promptText = displayLabel = chatInput;
    } else return;

    document.getElementById('welcomeState')?.remove();
    appendMsg('user', displayLabel);
    const inp = document.getElementById('chatInput');
    inp.value=''; inp.placeholder='Kirim pesan…'; autoResize(inp);
    clearActivePrompt();

    isLoading = true;
    document.getElementById('sendBtn').disabled = true;
    const typingEl = appendTyping('Menganalisis data berita…');

    let finalPrompt = promptText;
    if (isAnalyticalMessage(promptText) && cachedDataset) finalPrompt += '\n\n' + cachedDataset;
    chatHistory.push({ role: 'user', content: finalPrompt });
    if (chatHistory.length > 40) chatHistory = chatHistory.slice(-40);

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res  = await fetch(ROUTES.aiProxy, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify({ max_tokens: 8192, system: buildSystemPrompt(), messages: chatHistory }),
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
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data pemberitaan online secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : News (Online News Media / Portal Berita)
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip judul artikel spesifik, nama publisher, dan tanggal sebagai evidence.
3. Identifikasi isu nyata dari judul dan konten artikel yang tersedia.
4. Jika artikel memuat quote/kutipan narasumber, gunakan sebagai evidence langsung.
5. Sentimen dari setiap artikel sudah tersedia — gunakan untuk analisis distribusi.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "Artikel [1]", "[A5]" atau nomor index apapun.
- SELALU sebut nama publisher secara langsung. Contoh: "Kompas", "Tempo", "Detik".
- Format evidence yang benar: **Publisher** (Tanggal) — *"kutipan singkat judul/konten..."*

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}

function appendMsg(role, text) {
    const container = document.getElementById('aiMessages');
    const now = new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
    const el = document.createElement('div');
    const isAI = role==='ai';
    el.style.cssText = `display:flex;gap:10px;animation:msgIn .22s ease;max-width:100%;flex-direction:${isAI?'row':'row-reverse'};align-items:flex-start;`;
    const ava = `width:32px;height:32px;min-width:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;color:#fff !important;background:${isAI?'linear-gradient(135deg,#038047 0%,#026738 100%)':'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)'} !important;box-shadow:${isAI?'0 2px 8px rgba(3,128,71,.25)':'0 2px 8px rgba(59,130,246,.25)'};`;
    const bub = isAI
        ? `background:#fff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.75 !important;color:#1a202c !important;box-shadow:0 1px 4px rgba(0,0,0,.06) !important;word-break:break-word !important;font-family:inherit !important;`
        : `background:linear-gradient(135deg,#038047 0%,#026738 100%) !important;border:none !important;border-radius:14px 3px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.6 !important;color:#fff !important;box-shadow:0 2px 10px rgba(3,128,71,.3) !important;word-break:break-word !important;font-family:inherit !important;`;
    el.innerHTML = `<div style="${ava}">${isAI?'AI':'U'}</div><div style="display:flex;flex-direction:column;max-width:78%;gap:4px;align-items:${isAI?'flex-start':'flex-end'};"><div style="${bub}">${isAI?formatMarkdown(text):`<span style="color:#fff">${escHtml(text)}</span>`}</div><div style="font-size:10px;color:#cbd5e1;padding:0 4px;">${now}</div></div>`;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function appendTyping(label) {
    const container = document.getElementById('aiMessages');
    const el = document.createElement('div');
    el.style.cssText = 'display:flex;gap:10px;align-items:flex-start;animation:msgIn .22s ease;';
    el.innerHTML = `<div style="width:32px;height:32px;min-width:32px;border-radius:9px;background:linear-gradient(135deg,#038047 0%,#026738 100%) !important;color:#fff !important;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">AI</div><div style="display:flex;flex-direction:column;gap:5px;"><div style="display:flex;gap:5px;align-items:center;padding:13px 16px;background:#fff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px;"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div><span style="font-size:11px;color:#94a3b8;padding-left:2px;">${escHtml(label)}</span></div>`;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function setStatus(type, text) {
    const pill=document.getElementById('statusPill'), span=document.getElementById('statusText');
    if(pill) pill.className='status-pill'+(type!=='online'?` ${type}`:'');
    if(span) span.textContent=text;
}

function setReady(msg, isWarn=false) {
    setStatus(isWarn?'error':'online', isWarn?'Limited':'Online');
    document.getElementById('dataLoadingBadge')?.remove();
    const inp=document.getElementById('chatInput'), btn=document.getElementById('sendBtn');
    if(inp){inp.disabled=false;inp.placeholder='Kirim pesan…';}
    if(btn) btn.disabled=false;
}

function clearChat() {
    if (!chatHistory.length) return;
    if (!confirm('Hapus riwayat percakapan?')) return;
    chatHistory=[];
    document.getElementById('aiMessages').innerHTML=`<div class="welcome-state" id="welcomeState"><div class="welcome-icon-wrap"><svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="28" height="28"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/></svg></div><h3>Ready to Analyze News</h3><p>Pilih template dari panel kiri atau ketik pertanyaan sendiri.</p></div>`;
    clearActivePrompt();
}

function autoResize(el) { el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,120)+'px'; }
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function formatMarkdown(text) {
    if (!text) return '';
    let h = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    h = h.replace(/```[\w]*\n?([\s\S]*?)```/g,'<pre style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;overflow-x:auto;margin:8px 0;"><code style="font-size:12px;color:#1a202c;background:transparent;border:none;padding:0;">$1</code></pre>');
    h = h.replace(/`([^`]+)`/g,'<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-size:12px;border:1px solid #e2e8f0;">$1</code>');
    h = h.replace(/^### (.+)$/gm,'<h4 style="font-size:13px;font-weight:700;margin:12px 0 5px;color:#038047;">$1</h4>');
    h = h.replace(/^## (.+)$/gm, '<h3 style="font-size:14px;font-weight:700;margin:14px 0 6px;color:#038047;">$1</h3>');
    h = h.replace(/^# (.+)$/gm,  '<h2 style="font-size:15px;font-weight:700;margin:16px 0 7px;color:#038047;">$1</h2>');
    h = h.replace(/\*\*\*(.+?)\*\*\*/g,'<strong style="color:#1a202c;font-weight:700;"><em>$1</em></strong>');
    h = h.replace(/\*\*(.+?)\*\*/g,'<strong style="color:#1a202c;font-weight:700;">$1</strong>');
    h = h.replace(/\*(.+?)\*/g,'<em>$1</em>');
    h = h.replace(/^---$/gm,'<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">');
    h = h.replace(/((?:^[-*•] .+(?:\n|$))+)/gm,(b)=>{const i=b.trim().split('\n').map(l=>`<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^[-*•] /,'').trim()}</li>`).join('');return`<ul style="margin:6px 0 10px;padding-left:20px;color:#1a202c;">${i}</ul>`;});
    h = h.replace(/((?:^\d+\. .+(?:\n|$))+)/gm,(b)=>{const i=b.trim().split('\n').map(l=>`<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^\d+\. /,'').trim()}</li>`).join('');return`<ol style="margin:6px 0 10px;padding-left:20px;color:#1a202c;">${i}</ol>`;});
    h = h.split(/\n{2,}/).map(p=>{p=p.trim();if(!p)return'';if(/^<(h[2-4]|ul|ol|pre|hr)/.test(p))return p;return`<p style="margin:0 0 8px;color:#1a202c;">${p.replace(/\n/g,'<br>')}</p>`;}).join('\n');
    return h;
}
@endverbatim
</script>
@endsection