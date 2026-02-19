@extends('mk.layouts.app')

@section('title', 'AI Analysis - X (Twitter)')

@section('styles')
<style>
    :root {
        --primary: #038047;
        --primary-dark: #026738;
        --primary-light: rgba(3,128,71,0.08);
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
    }

    /* ── Wrapper ────────────────────────────────────────────── */
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

    /* ── Header ─────────────────────────────────────────────── */
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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(3,128,71,0.25);
    }

    .ai-avatar svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 1.8; }

    .ai-header-info h4 { font-size: 14px; font-weight: 700; color: #1a202c; margin: 0 0 2px; letter-spacing: -0.2px; }
    .ai-header-info p  { font-size: 11px; color: #94a3b8; margin: 0; }

    .ai-header-right { display: flex; align-items: center; gap: 8px; }

    .status-pill {
        display: flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 600; color: var(--primary);
        background: rgba(3,128,71,0.08);
        border: 1px solid rgba(3,128,71,0.18);
        padding: 5px 11px; border-radius: 20px;
        transition: all 0.3s;
    }

    .status-pill.loading { color: #d97706; background: rgba(217,119,6,0.08); border-color: rgba(217,119,6,0.2); }
    .status-pill.error   { color: #dc2626; background: rgba(220,38,38,0.08); border-color: rgba(220,38,38,0.2); }

    .status-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: currentColor; animation: blink 2.5s infinite;
    }

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.35} }

    .btn-clear {
        padding: 6px 13px; border-radius: 8px;
        border: 1px solid #e2e8f0; background: #ffffff;
        color: #94a3b8; font-size: 12px; font-weight: 600;
        cursor: pointer; transition: all 0.15s; font-family: inherit;
    }

    .btn-clear:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* ── Context Bar ─────────────────────────────────────────── */
    #ctxBar {
        padding: 7px 18px !important;
        background: #f0fdf4 !important;
        border-bottom: 1px solid #bbf7d0 !important;
        display: flex !important;
        gap: 6px !important;
        align-items: center !important;
        font-size: 11.5px !important;
        color: #374151 !important;
        flex-shrink: 0 !important;
        min-height: 33px !important;
    }

    /* ── Messages ────────────────────────────────────────────── */
    .ai-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        background: #fafcff;
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 transparent;
    }

    .ai-messages::-webkit-scrollbar { width: 4px; }
    .ai-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

    /* ── Welcome State ───────────────────────────────────────── */
    .welcome-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 10px;
        padding: 40px 32px;
        min-height: 220px;
    }

    .welcome-icon-wrap {
        width: 56px; height: 56px; border-radius: 16px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(3,128,71,.22); margin-bottom: 8px;
    }

    .welcome-icon-wrap svg { width: 26px; height: 26px; stroke: #fff; fill: none; stroke-width: 1.5; }
    .welcome-state h3 { font-size: 18px; font-weight: 700; color: #1a202c; margin: 0; letter-spacing: -0.3px; }
    .welcome-state p  { font-size: 13px; line-height: 1.7; max-width: 420px; margin: 0; color: #64748b; }

    .data-loading-badge {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(3,128,71,0.07); border: 1px solid rgba(3,128,71,0.15);
        color: var(--primary); font-size: 12px; font-weight: 600;
        padding: 6px 16px; border-radius: 20px; margin-top: 6px;
    }

    .data-loading-badge .spin {
        width: 11px; height: 11px; border: 2px solid rgba(3,128,71,0.2);
        border-top-color: var(--primary); border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Message Bubbles ─────────────────────────────────────── */
    .msg { display: flex; gap: 10px; animation: msgIn .22s ease; max-width: 100%; }
    .msg.user { flex-direction: row-reverse; }

    @keyframes msgIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

    .msg-ava {
        width: 32px; height: 32px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; flex-shrink: 0;
        letter-spacing: 0.2px;
    }

    .msg.ai   .msg-ava {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(3,128,71,0.2);
    }

    .msg.user .msg-ava {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(59,130,246,0.2);
    }

    .msg-body { display: flex; flex-direction: column; max-width: 78%; gap: 4px; }
    .msg.user .msg-body { align-items: flex-end; }

    .msg.ai .msg-bubble {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-top-left-radius: 3px !important;
        border-top-right-radius: 14px !important;
        border-bottom-right-radius: 14px !important;
        border-bottom-left-radius: 14px !important;
        padding: 12px 16px !important;
        font-size: 13.5px !important;
        line-height: 1.75 !important;
        color: #1a202c !important;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05) !important;
        word-break: break-word !important;
    }

    .msg.user .msg-bubble {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-top-right-radius: 3px !important;
        border-top-left-radius: 14px !important;
        border-bottom-right-radius: 14px !important;
        border-bottom-left-radius: 14px !important;
        padding: 12px 16px !important;
        font-size: 13.5px !important;
        line-height: 1.6 !important;
        box-shadow: 0 2px 10px rgba(3,128,71,0.22) !important;
        word-break: break-word !important;
    }

    .msg.ai .msg-bubble p { margin: 0 0 8px; color: #1a202c; }
    .msg.ai .msg-bubble p:last-child { margin: 0; }
    .msg.ai .msg-bubble ul,
    .msg.ai .msg-bubble ol { margin: 6px 0 10px; padding-left: 20px; color: #1a202c; }
    .msg.ai .msg-bubble li { margin-bottom: 4px; }
    .msg.ai .msg-bubble h2 { font-size: 15px; font-weight: 700; margin: 16px 0 8px; color: var(--primary); }
    .msg.ai .msg-bubble h3 { font-size: 14px; font-weight: 700; margin: 14px 0 6px; color: var(--primary); }
    .msg.ai .msg-bubble h4 { font-size: 13px; font-weight: 700; margin: 12px 0 5px; color: var(--primary); }
    .msg.ai .msg-bubble code { background: #f1f5f9; color: #0f172a; padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid #e2e8f0; }
    .msg.ai .msg-bubble hr { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0; }
    .msg.ai .msg-bubble strong { color: #1a202c; font-weight: 700; }
    .msg.ai .msg-bubble pre { background: #f8fafc !important; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; overflow-x: auto; margin: 8px 0; }
    .msg.ai .msg-bubble pre code { background: transparent; border: none; padding: 0; }

    .msg.user .msg-bubble p { margin: 0; color: #fff; }
    .msg.user .msg-bubble strong { color: #fff; }

    .msg-time { font-size: 10px; color: #cbd5e1; padding: 0 4px; }

    /* ── Typing Indicator ────────────────────────────────────── */
    .typing-wrap { display: flex; gap: 10px; animation: msgIn .22s ease; }

    .typing-bubble {
        display: flex; gap: 5px; align-items: center;
        padding: 13px 16px;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 3px 14px 14px 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    .typing-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #94a3b8; animation: tdot 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: .2s; }
    .typing-dot:nth-child(3) { animation-delay: .4s; }

    @keyframes tdot { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-6px)} }

    /* ── Input Area ──────────────────────────────────────────── */
    .ai-input-area {
        flex-shrink: 0;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
        box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
    }

    /* ── Chips ───────────────────────────────────────────────── */
    .prompt-chips {
        display: flex;
        gap: 6px;
        padding: 10px 16px 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .prompt-chips::-webkit-scrollbar { display: none; }

    .chip {
        padding: 6px 13px; border-radius: 20px;
        border: 1px solid #e2e8f0;
        font-size: 12px; font-weight: 500;
        color: #64748b; background: #f8fafc;
        cursor: pointer; white-space: nowrap;
        transition: all 0.15s; font-family: inherit; flex-shrink: 0;
    }

    .chip:hover { border-color: var(--primary); color: var(--primary); background: rgba(3,128,71,0.05); }
    .chip.active { background: var(--primary); border-color: var(--primary); color: #fff; }

    .chip.chip-featured {
        border-color: var(--primary);
        color: var(--primary);
        background: rgba(3,128,71,0.06);
        font-weight: 600;
    }

    .chip.chip-featured:hover { background: rgba(3,128,71,0.12); }
    .chip.chip-featured.active { background: var(--primary); color: #fff; }

    /* ── Input Row ───────────────────────────────────────────── */
    .input-row {
        display: flex; gap: 10px; align-items: flex-end;
        padding: 8px 16px 12px;
    }

    .chat-textarea {
        flex: 1; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 11px 15px; font-size: 13.5px; font-family: inherit;
        color: #1a202c; resize: none;
        min-height: 44px; max-height: 120px;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #f8fafc !important;
        line-height: 1.5;
    }

    .chat-textarea:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(3,128,71,0.1);
    }

    .chat-textarea:disabled { opacity: .45; cursor: not-allowed; }
    .chat-textarea::placeholder { color: #c8d5e3; }

    .btn-send {
        width: 44px; height: 44px; border-radius: 12px; border: none;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(3,128,71,0.25);
    }

    .btn-send:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(3,128,71,0.35); }
    .btn-send:active { transform: translateY(0); }
    .btn-send:disabled { background: #e2e8f0; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn-send svg { width: 17px; height: 17px; stroke: #fff; fill: none; stroke-width: 2.2; }

    .input-hint { font-size: 11px; color: #cbd5e1; text-align: center; padding-bottom: 10px; }

    /* ── Date Picker Modal ───────────────────────────────────── */
    .date-picker-trigger {
        display: flex; align-items: center; gap: 7px;
        padding: 7px 13px;
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 9px; font-family: inherit; font-size: 12px;
        font-weight: 500; color: #374151;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
    }

    .date-picker-trigger:hover { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(3,128,71,0.08); }
    .date-picker-trigger svg { width: 14px; height: 14px; flex-shrink: 0; color: var(--primary); }

    .date-picker-modal {
        position: fixed; inset: 0; z-index: 10000;
        display: none; align-items: center; justify-content: center;
        background: rgba(15,23,42,0.55); backdrop-filter: blur(8px);
    }

    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; inset: 0; cursor: pointer; }

    .date-picker-container {
        position: relative; background: #ffffff; border-radius: 18px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.05);
        display: flex; max-width: 820px; width: 90%; max-height: 90vh;
        z-index: 10001; animation: dpSlideUp .25s cubic-bezier(.34,1.56,.64,1);
    }

    @keyframes dpSlideUp { from{transform:translateY(20px) scale(0.97);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }

    .date-picker-sidebar {
        width: 155px; background: #f8fafc;
        border-right: 1px solid #e2e8f0; padding: 14px 10px;
        border-radius: 18px 0 0 18px; display: flex; flex-direction: column; gap: 3px; flex-shrink: 0;
    }

    .date-preset {
        padding: 9px 14px; background: transparent; border: none;
        border-radius: 9px; font-family: inherit; font-size: 12px;
        font-weight: 500; color: #374151; text-align: left;
        cursor: pointer; transition: all 0.15s;
    }

    .date-preset:hover { background: #ffffff; color: var(--primary); }
    .date-preset.active { background: var(--primary); color: #ffffff; }

    .date-picker-content { flex: 1; padding: 20px; display: flex; flex-direction: column; overflow: hidden; }

    .date-picker-nav { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }

    .dp-nav-btn {
        width: 34px; height: 34px; border-radius: 9px;
        background: #f8fafc; border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0;
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
        transition: all 0.15s; color: #1a202c;
        background: transparent; border: none; padding: 0; font-family: inherit;
    }

    .calendar-day:hover:not(:disabled):not(.other-month) { background: #f1f5f9; }
    .calendar-day.other-month { color: #e2e8f0; cursor: default; }
    .calendar-day:disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid var(--primary); font-weight: 700; }
    .calendar-day.selected,
    .calendar-day.range-start,
    .calendar-day.range-end { background: var(--primary); color: #fff !important; }
    .calendar-day.in-range { background: rgba(3,128,71,0.1); color: var(--primary); }

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
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: #fff; font-family: inherit; font-size: 13px; font-weight: 600;
        cursor: pointer; box-shadow: 0 3px 12px rgba(3,128,71,0.25); transition: all 0.15s;
    }

    .dp-apply:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(3,128,71,0.35); }

    @media (max-width: 640px) {
        .date-picker-container { flex-direction: column; width: 95%; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid #e2e8f0; border-radius: 18px 18px 0 0; flex-direction: row; overflow-x: auto; }
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
                <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </div>
            <div class="ai-header-info">
                <h4>X (Twitter) AI Research</h4>
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

    {{-- Context Bar --}}
    <div id="ctxBar">
        <span id="ctxText" style="color:#374151 !important;">Fetching X (Twitter) data…</span>
    </div>

    {{-- Messages --}}
    <div class="ai-messages" id="aiMessages">
        <div class="welcome-state" id="welcomeState">
            <div class="welcome-icon-wrap">
                <svg viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </div>
            <h3>Ready to Analyze X (Twitter)</h3>
            <p>Select a template below or type your own question to analyze X/Twitter conversation data for this project.</p>
            <div class="data-loading-badge" id="dataLoadingBadge">
                <div class="spin"></div>
                Loading X data…
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="ai-input-area">
        <div class="prompt-chips" id="promptChips">
            <button class="chip" onclick="useChip(this,'issue_summary')">Issue Summary</button>
            <button class="chip" onclick="useChip(this,'sentiment_breakdown')">Sentiment Breakdown</button>
            <button class="chip" onclick="useChip(this,'top_users')">Top Users Analysis</button>
            <button class="chip" onclick="useChip(this,'hashtag_analysis')">Hashtag Analysis</button>
            <button class="chip" onclick="useChip(this,'swot')">SWOT Analysis</button>
            <button class="chip" onclick="useChip(this,'viral_potential')">Viral Potential</button>
            <button class="chip" onclick="useChip(this,'crisis')">Crisis Situation (SCCT)</button>
            <button class="chip" onclick="useChip(this,'narrative')">Dominant Narrative</button>
            <button class="chip" onclick="useChip(this,'influencer')">Influencer Mapping</button>
            <button class="chip" onclick="useChip(this,'early_warning')">Early Warning Signals</button>
            <button class="chip" onclick="useChip(this,'communication')">Communication Strategy</button>
            <button class="chip" onclick="useChip(this,'key_insights')">Key Insights & Actions</button>
            <button class="chip chip-featured" onclick="useChip(this,'comprehensive')">Analisis X Komprehensif</button>
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
                <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
        <div class="input-hint">Enter to send &middot; Shift+Enter for new line</div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════
// CONFIG
// ═══════════════════════════════════════════════════════════════════
const PROJECT_ID = '{{ $projectId ?? "" }}';
let START_DATE = '{{ $startDate ?? "" }}';
let END_DATE   = '{{ $endDate ?? "" }}';

const ROUTES = {
    mostStatus   : '{{ route("mk.api.x.most-status") }}',
    mostRetweets : '{{ route("mk.api.x.most-retweets") }}',
    topHashtags  : '{{ route("mk.api.x.top-hashtags-data") }}',
    mostActive   : '{{ route("mk.api.x.most-active-users") }}',
    volumeTotal  : '{{ route("mk.api.x.volume-total") }}',
    sentimentTotal:'{{ route("mk.api.x.sentiment-total") }}',
    aiProxy      : '{{ route("mk.api.news.ai-proxy") }}',
};

// ═══════════════════════════════════════════════════════════════════
// PROMPT TEMPLATES — X/Twitter specific
// ═══════════════════════════════════════════════════════════════════
const CTX = `Project ID: ${PROJECT_ID}, Platform: X (Twitter), Period: ${START_DATE} to ${END_DATE}`;

const PROMPTS = {
    issue_summary: {
        label: 'Issue Summary',
        text: `Analyze the X (Twitter) conversation data from this context and perform a comprehensive issue identification.

Context: ${CTX}

Tasks:
1. **MAIN ISSUES** — Identify at least 5 dominant issues in the Twitter conversation. For each: core problem, key accounts involved, estimated tweet volume.
2. **DOMINANT NARRATIVE** — What overarching narrative emerges from the overall Twitter conversation?
3. **ISSUE CLASSIFICATION** — Classify each issue as: political, economic, social, legal, or environmental.
4. **CONCLUSION** — Overall picture of the Twitter conversation landscape in this period.

Use professional, structured, data-driven language.`
    },

    sentiment_breakdown: {
        label: 'Sentiment Breakdown',
        text: `Analyze the sentiment distribution of X (Twitter) data for this project.

Context: ${CTX}

Tasks:
1. **Overall Sentiment Distribution** — Breakdown of positive, negative, and neutral posts with percentages.
2. **Sentiment Drivers** — What topics/events are driving positive vs negative sentiment?
3. **Sentiment Trends** — How has sentiment shifted over the period?
4. **Most Positive Posts** — Key posts driving positive sentiment with their content and engagement.
5. **Most Negative Posts** — Key posts driving negative sentiment with their content and retweet counts.
6. **Sentiment by Influence** — Are high-follower accounts positive or negative overall?
7. **Recommendations** — How to amplify positive sentiment and mitigate negative sentiment.

Provide concrete evidence from the tweet data.`
    },

    top_users: {
        label: 'Top Users Analysis',
        text: `Analyze the top and most active X (Twitter) users engaging with this project's topics.

Context: ${CTX}

Tasks:
1. **Most Active Users** — Who posted the most and what are their key messages?
2. **Most Retweeted Users** — Whose content spread the most? Why?
3. **Influencer Profiles** — Categorize users as: journalist, activist, politician, brand, public figure, regular user.
4. **User Sentiment Alignment** — Are top users mostly positive, negative, or mixed?
5. **Network Effect** — Which users are connecting different communities?
6. **Key Opinion Leaders (KOLs)** — Identify the 5 most influential voices and their stance.
7. **Engagement Strategy** — Which users should be prioritized for outreach or monitoring?`
    },

    hashtag_analysis: {
        label: 'Hashtag Analysis',
        text: `Conduct a deep analysis of hashtags appearing in the X (Twitter) data for this project.

Context: ${CTX}

Tasks:
1. **Top Hashtags** — List the most used hashtags with their mention counts.
2. **Hashtag Sentiment** — What is the dominant sentiment associated with each hashtag?
3. **Hashtag Clusters** — Group related hashtags into thematic clusters.
4. **Emerging Hashtags** — Which hashtags are growing rapidly?
5. **Cross-Platform Hashtags** — Are any hashtags trending beyond Twitter?
6. **Branded vs Organic** — Which hashtags are brand-initiated vs organically created?
7. **Recommendations** — Which hashtags should be adopted, monitored, or countered?`
    },

    swot: {
        label: 'SWOT Analysis',
        text: `Conduct a SWOT analysis based on X (Twitter) conversation data from this project.

Context: ${CTX}

**STRENGTHS** — Positive narratives, key supporters, favorable trending topics visible in tweets.

**WEAKNESSES** — Weaknesses exposed in Twitter conversation, recurring negative narratives or criticism.

**OPPORTUNITIES** — Trending topics that can be leveraged, influential supporters, viral potential.

**THREATS** — Negative trending topics, coordinated criticism, bot activity, potential viral backlash.

For each quadrant provide: specific evidence from the tweet data, key accounts involved, and estimated impact level (High/Medium/Low).

Conclude with strategic recommendations based on this SWOT matrix.`
    },

    viral_potential: {
        label: 'Viral Potential',
        text: `Analyze the viral potential of content and conversations in this X (Twitter) dataset.

Context: ${CTX}

Tasks:
1. **High Engagement Posts** — Identify posts with exceptional retweet/like ratios and explain why they went viral.
2. **Viral Content Patterns** — What content formats, topics, or styles drive the most engagement?
3. **Amplification Network** — Who are the key nodes amplifying content (super-spreaders)?
4. **Timing Analysis** — When do posts get the most engagement in this dataset?
5. **Emotional Triggers** — What emotions (anger, joy, surprise, fear) are driving shares?
6. **Risk of Negative Virality** — Which negative posts have the highest viral potential?
7. **Recommendations** — How to create content that leverages these viral patterns positively?`
    },

    crisis: {
        label: 'Crisis Situation (SCCT)',
        text: `Apply the Situational Crisis Communication Theory (SCCT) framework to analyze crisis potential in this X (Twitter) data.

Context: ${CTX}

1. **Crisis Type Classification** — Is this: Victim Cluster / Accident Cluster / Preventable Cluster? Explain based on Twitter evidence.
2. **Crisis Velocity** — How fast is the negative narrative spreading on Twitter?
3. **Key Crisis Accounts** — Who are the most vocal critics and what is their influence?
4. **Hashtag Crisis Signals** — Are there crisis-related hashtags trending?
5. **SCCT Response Strategy** — Based on crisis type and attribution, recommend: Deny / Diminish / Rebuild / Bolster.
6. **Key Messages** — Draft 3 Twitter-appropriate key messages aligned with the SCCT-recommended strategy.
7. **Twitter Response Timeline** — Immediate (1h), short-term (24h), medium-term (1 week).`
    },

    narrative: {
        label: 'Dominant Narrative',
        text: `Analyze the framing and dominant narrative patterns in X (Twitter) data from this project.

Context: ${CTX}

1. **Dominant Narratives** — Most prominent positive, negative, and neutral narratives being constructed on Twitter.
2. **Framing Patterns** — How do users frame the issues in their tweets?
3. **Narrative Evolution** — Has the dominant narrative shifted over the coverage period?
4. **Counter-Narratives** — Are there organized counter-narratives emerging?
5. **Key Voices** — Who is driving each narrative and what is their reach?
6. **Meme & Cultural References** — Are specific memes or cultural references being used to frame issues?
7. **Recommendations** — How to respond to and reframe existing negative narratives on Twitter?`
    },

    influencer: {
        label: 'Influencer Mapping',
        text: `Create a comprehensive influencer map based on X (Twitter) data from this project.

Context: ${CTX}

1. **Tier 1 Influencers** (100k+ followers) — Who are they, what's their stance, estimated reach?
2. **Tier 2 Influencers** (10k-100k followers) — Most active mid-tier voices and their positions.
3. **Micro-Influencers** (1k-10k followers) — High engagement rate accounts with niche influence.
4. **Sentiment by Tier** — How does sentiment differ across influencer tiers?
5. **Cross-Platform Presence** — Do these influencers have significant presence elsewhere?
6. **Engagement Quality** — Which influencers drive genuine conversation vs broadcast only?
7. **Collaboration Opportunities** — Which influencers could be potential partners or brand advocates?
8. **Risk Accounts** — Which influencers pose the greatest reputational risk?`
    },

    early_warning: {
        label: 'Early Warning Signals',
        text: `Conduct an Early Warning analysis based on signals from the X (Twitter) data.

Context: ${CTX}

1. **Danger Signals** — Identify 3–5 most concerning signals in the Twitter conversation.
2. **Risk Level** — For each signal: Low / Medium / High / Critical.
3. **Escalation Potential** — Which topics could develop into viral crises on Twitter?
4. **Velocity Assessment** — How fast is each negative topic growing in tweet volume?
5. **Bot/Coordinated Activity** — Any signs of coordinated inauthentic behavior?
6. **Timeline Projection** — When is each risk likely to peak based on tweet patterns?
7. **Early Mitigation Steps** — Concrete, immediately actionable Twitter-specific steps.
8. **Monitoring Indicators** — What signals and hashtags should be watched hourly/daily?`
    },

    communication: {
        label: 'Communication Strategy',
        text: `Develop an X (Twitter) communication strategy based on conversation data from this project.

Context: ${CTX}

1. **Platform Situation Assessment** — Summary of the current Twitter conversation environment.
2. **Target Audiences on Twitter** — Which user segments are most important to reach?
3. **Key Messages** — 3–5 core messages tailored for Twitter's format and culture.
4. **Content Types** — Which content formats work best (threads, polls, images, videos)?
5. **Optimal Timing** — When to post based on engagement patterns in this dataset.
6. **Hashtag Strategy** — Which hashtags to use, create, or avoid.
7. **Engagement Tactics** — How to respond to mentions, retweets, and criticism.
8. **Success Metrics** — How to measure whether the Twitter communication strategy is effective.`
    },

    key_insights: {
        label: 'Key Insights & Actions',
        text: `Create an executive summary with key insights and action items from the X (Twitter) data.

Context: ${CTX}

**EXECUTIVE SUMMARY** (2–3 sentences): Most critical overview of the Twitter conversation.

**5 KEY INSIGHTS:**
1. [Most important insight with tweet evidence]
2. [Second insight]
3. [Third insight]
4. [Fourth insight]
5. [Fifth insight]

**ACTION ITEMS:**
- Immediate (1 hour): ...
- Today: ...
- This week: ...

**RISKS TO MONITOR:** Top 2–3 Twitter-specific risks if no action is taken.
**SUCCESS INDICATORS:** How to measure whether actions were effective on Twitter.`
    },

    comprehensive: {
        label: 'Analisis X Komprehensif',
        text: `Anda adalah analis senior media sosial dan digital intelligence yang berpengalaman dalam menganalisis percakapan X (Twitter) menggunakan pendekatan data-driven dan framing analysis.

Konteks Data: ${CTX}

### TUJUAN ANALISIS
1. Mengidentifikasi isu-isu utama yang muncul di X (Twitter).
2. Mengelompokkan isu berdasarkan tema besar.
3. Mengidentifikasi narasi dominan, framing, sentimen, dan viral patterns.
4. Mengutip contoh tweet nyata dari data sebagai evidence.
5. Menyusun analisis SWOT berbasis temuan isu dan narasi publik di Twitter.
6. Menyusun kesimpulan strategis dan rekomendasi.

## LANGKAH ANALISIS

### 1. Pemetaan Isu Utama
- Identifikasi minimal 5 isu utama di Twitter.
- Jelaskan: inti isu, akun paling aktif, hashtag dominan, volume tweet dan sentimen dominan.
- Berikan 2–3 contoh tweet/retweet dari data untuk setiap isu.

### 2. Analisis Narasi & Framing
- Identifikasi: narasi positif, narasi negatif, narasi netral/informasi.
- Jelaskan pola framing di Twitter.
- Sertakan contoh tweet pendukung.

### 3. Analisis SWOT Berbasis Percakapan Twitter
**Strengths** — Faktor yang dipersepsikan positif (+ contoh tweet)
**Weaknesses** — Kelemahan yang sering disorot (+ contoh tweet)
**Opportunities** — Momentum yang bisa dimanfaatkan (+ contoh tweet)
**Threats** — Risiko viral negatif, koordinasi serangan, trending negatif (+ contoh tweet)

### 4. Analisis Risiko Twitter
Klasifikasikan sebagai: Noise / Emerging Issue / Potential Viral Crisis / Ongoing Crisis

### 5. Profil Akun Kunci
- Top 5 akun paling berpengaruh dan posisi mereka.
- Risiko dari akun-akun kritikal.

### 6. Insight Strategis
- 3 insight utama berbasis data Twitter
- 3 rekomendasi strategis untuk platform Twitter

## FORMAT OUTPUT
1. **Ringkasan Eksekutif**
2. **Pemetaan Isu Utama**
3. **Analisis Narasi & Framing**
4. **Analisis SWOT**
5. **Analisis Risiko Twitter**
6. **Profil Akun Kunci**
7. **Insight & Rekomendasi Strategis**
8. **Lampiran: Kumpulan Tweet Representatif**

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
// FETCH X DATA
// ═══════════════════════════════════════════════════════════════════
async function preloadProjectData() {
    setStatus('loading', 'Loading data…');

    try {
        const qs = new URLSearchParams({
            project_id : PROJECT_ID,
            start_date : START_DATE,
            end_date   : END_DATE,
        });

        const [statusRes, retweetsRes, hashtagsRes, activeRes, sentimentRes] = await Promise.allSettled([
            fetch(`${ROUTES.mostStatus}?${qs}`).then(r => r.json()),
            fetch(`${ROUTES.mostRetweets}?${qs}`).then(r => r.json()),
            fetch(`${ROUTES.topHashtags}?${qs}`).then(r => r.json()),
            fetch(`${ROUTES.mostActive}?${qs}`).then(r => r.json()),
            fetch(`${ROUTES.sentimentTotal}?${qs}`).then(r => r.json()),
        ]);

        const posts      = (statusRes.status    === 'fulfilled' && statusRes.value.success)    ? (statusRes.value.data    ?? []) : [];
        const retweets   = (retweetsRes.status  === 'fulfilled' && retweetsRes.value.success)  ? (retweetsRes.value.data  ?? []) : [];
        const hashtags   = (hashtagsRes.status  === 'fulfilled' && hashtagsRes.value.success)  ? (hashtagsRes.value.data?.hashtags ?? []) : [];
        const activeUsers= (activeRes.status    === 'fulfilled' && activeRes.value.success)    ? (activeRes.value.data?.data ?? []) : [];
        const sentiment  = (sentimentRes.status === 'fulfilled' && sentimentRes.value.success) ? (sentimentRes.value.data  ?? {}) : {};

        cachedDataset = buildDataset(posts, retweets, hashtags, activeUsers, sentiment);
        dataReady = true;

        const pos = sentiment.positive || 0;
        const neg = sentiment.negative || 0;
        const neu = sentiment.neutral  || 0;
        const total = pos + neg + neu || 1;
        const pctPos = Math.round(pos / total * 100);
        const pctNeg = Math.round(neg / total * 100);

        setReady(
            `${posts.length} posts &middot; ${retweets.length} retweets &middot; ${hashtags.length} hashtags &middot; ` +
            `Positive ${pctPos}% &middot; Negative ${pctNeg}% &middot; ${activeUsers.length} active users &middot; ${START_DATE} → ${END_DATE}`
        );

    } catch (err) {
        console.error('[AI] preload failed:', err);
        cachedDataset = `=== DATA TIDAK TERSEDIA ===\nProject ID: ${PROJECT_ID}\nPlatform: X (Twitter)\nPeriode: ${START_DATE} s/d ${END_DATE}\nGunakan pengetahuan umum social media monitoring.`;
        dataReady = true;
        setReady('Data load failed — AI will answer without live project data', true);
    }
}

// ═══════════════════════════════════════════════════════════════════
// BUILD DATASET FROM X DATA
// ═══════════════════════════════════════════════════════════════════
function buildDataset(posts, retweets, hashtags, activeUsers, sentiment) {
    const lines = [];
    const pos = sentiment.positive || 0;
    const neg = sentiment.negative || 0;
    const neu = sentiment.neutral  || 0;
    const total = pos + neg + neu || 1;

    lines.push(`=== DATA X (TWITTER) PROJECT ${PROJECT_ID} ===`);
    lines.push(`Periode: ${START_DATE} s/d ${END_DATE}`);
    lines.push(`Sentimen: Positif ${Math.round(pos/total*100)}%(${pos}) | Negatif ${Math.round(neg/total*100)}%(${neg}) | Netral ${Math.round(neu/total*100)}%(${neu})`);
    lines.push('');

    // Top Hashtags
    if (hashtags.length > 0) {
        lines.push(`--- TOP HASHTAGS (${Math.min(hashtags.length, 20)}) ---`);
        hashtags.slice(0, 20).forEach((h, i) => {
            lines.push(`${i+1}. #${h.name} (${h.size} mentions)`);
        });
        lines.push('');
    }

    // Most Active Users
    if (activeUsers.length > 0) {
        lines.push(`--- MOST ACTIVE USERS (${Math.min(activeUsers.length, 10)}) ---`);
        activeUsers.slice(0, 10).forEach((u, i) => {
            lines.push(`${i+1}. @${u.username} — ${u.posts} posts | ${u.followers?.toLocaleString() ?? 0} followers | Mentions: ${u.mentions}`);
        });
        lines.push('');
    }

    // Most Retweeted Posts
    if (retweets.length > 0) {
        lines.push(`--- MOST RETWEETED POSTS (${Math.min(retweets.length, 15)}) ---`);
        retweets.slice(0, 15).forEach((t, i) => {
            const date    = (t.date_created || '').substring(0, 10);
            const author  = t.author?.scr_name || t.name || 'unknown';
            const content = (t.content || '').substring(0, 200).replace(/\n/g, ' ');
            const sent    = t.sentiment_str || 'Neutral';
            lines.push(`[RT${i+1}] @${author} (${t.freq} RT) | ${date} | ${sent}`);
            if (content) lines.push(`   "${content}"`);
        });
        lines.push('');
    }

    // Most Viewed Posts
    if (posts.length > 0) {
        const negPosts  = posts.filter(p => (p.sentiment_str||'').toLowerCase().includes('neg'));
        const posPosts  = posts.filter(p => (p.sentiment_str||'').toLowerCase().includes('pos'));
        const neuPosts  = posts.filter(p => !negPosts.includes(p) && !posPosts.includes(p));
        const sample    = [...negPosts.slice(0,10), ...posPosts.slice(0,8), ...neuPosts.slice(0,5)];

        lines.push(`--- MOST VIEWED POSTS (${sample.length} dari ${posts.length}) ---`);
        sample.forEach((p, i) => {
            const date    = (p.date_created || '').substring(0, 10);
            const author  = p.author?.scr_name || p.name || 'unknown';
            const content = (p.content || '').substring(0, 200).replace(/\n/g, ' ');
            const views   = p.view_cnt ?? 0;
            const sent    = p.sentiment_str || 'Neutral';
            lines.push(`[P${i+1}] @${author} (${views.toLocaleString()} views) | ${date} | ${sent}`);
            if (content) lines.push(`   "${content}"`);
        });
    }

    lines.push('=== AKHIR DATASET ===');
    return lines.join('\n');
}

// ═══════════════════════════════════════════════════════════════════
// DATE PICKER
// ═══════════════════════════════════════════════════════════════════
(function() {
    let dpStart = null, dpEnd = null;
    let dpMonth1 = new Date(), dpMonth2 = new Date();
    let dpSelectingStart = true;

    function dpFmt(d) {
        if (!d) return '';
        return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    }

    function dpSameDay(a,b) { return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }

    window.closeDatePicker = function() { document.getElementById('datePickerModal').classList.remove('show'); };

    function renderBoth() { renderCal('dpCal1',dpMonth1); renderCal('dpCal2',dpMonth2); updateDisplay(); }

    function renderCal(id, month) {
        const el = document.getElementById(id); if (!el) return;
        const y = month.getFullYear(), m = month.getMonth();
        const first = new Date(y,m,1), last = new Date(y,m+1,0);
        const today = new Date(); today.setHours(0,0,0,0);
        const names = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const days  = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        let html = `<div class="calendar-month">${names[m]} ${y}</div>
        <div class="calendar-weekdays">${days.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
        <div class="calendar-days">`;
        for (let i = first.getDay()-1; i >= 0; i--) html += `<button class="calendar-day other-month" disabled>${new Date(y,m,0).getDate()-i}</button>`;
        for (let d = 1; d <= last.getDate(); d++) {
            const date = new Date(y,m,d); date.setHours(0,0,0,0);
            const ds = dpFmt(date);
            let cls = 'calendar-day';
            if (dpSameDay(date,today)) cls += ' today';
            if (date > today) cls += ' disabled';
            if (dpStart && dpEnd) {
                if (dpSameDay(date,dpStart)) cls += ' selected range-start';
                else if (dpSameDay(date,dpEnd)) cls += ' selected range-end';
                else if (date > dpStart && date < dpEnd) cls += ' in-range';
            } else if (dpStart && dpSameDay(date,dpStart)) cls += ' selected';
            html += `<button type="button" class="${cls}" data-date="${ds}" ${date>today?'disabled':''}>${d}</button>`;
        }
        const rem = 6 - last.getDay();
        for (let i = 1; i <= rem; i++) html += `<button class="calendar-day other-month" disabled>${i}</button>`;
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
        const lbl = document.getElementById('dpTriggerLabel'); if (lbl) lbl.textContent = `${START_DATE} to ${END_DATE}`;
        const sub = document.getElementById('headerSubtitle'); if (sub) sub.textContent = `${PROJECT_ID} · ${START_DATE} to ${END_DATE}`;
        closeDatePicker();
        cachedDataset = null; dataReady = false;
        const inp = document.getElementById('chatInput'), sbtn = document.getElementById('sendBtn');
        if (inp)  { inp.disabled = true; inp.placeholder = 'Reloading data…'; }
        if (sbtn) sbtn.disabled = true;
        const ctxText = document.getElementById('ctxText');
        if (ctxText) ctxText.textContent = `Fetching X data for ${START_DATE} to ${END_DATE}…`;
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
    const keywords = ['analisis','analysis','analyze','analisa','tweet','twitter','x ','retweet','hashtag','sentimen','sentiment','positif','negatif','negative','positive','isu','issue','topik','topic','user','akun','account','viral','trending','mention','follower','swot','pestle','scct','influencer','narasi','narrative','krisis','crisis','komunikasi','communication','rangkum','ringkas','summarize','summary','tren','trend','pola','pattern','siapa','who','apa','what','bagaimana','how','mengapa','why','data','laporan','report','project'];
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
    const typingEl = appendTypingWithLabel('Menganalisis data X (Twitter)…');

    const isAnalysisRequest = activeChip !== null || isAnalyticalMessage(chatInput);
    let finalPrompt = promptTemplate;
    if (chatInput && promptTemplate !== chatInput) finalPrompt += '\n\nPertanyaan tambahan: ' + chatInput;
    if (isAnalysisRequest && cachedDataset) finalPrompt += '\n\n' + cachedDataset;

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
        console.error('[AI] send error:', err);
    } finally {
        isLoading = false;
        document.getElementById('sendBtn').disabled = false;
    }
}

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan X (Twitter) secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : X (Twitter)
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip username (@handle), konten tweet, jumlah retweet/views sebagai evidence.
3. Identifikasi isu nyata dari konten tweet dan pola engagement.
4. Gunakan data sentimen, hashtag, dan most active users untuk mendukung analisis.
5. Perhatikan pola viral, retweet networks, dan influencer dynamics.

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

    const wrapStyle = `
        display:flex;
        gap:10px;
        animation:msgIn .22s ease;
        max-width:100%;
        flex-direction:${isAI ? 'row' : 'row-reverse'};
        align-items:flex-start;
    `;

    const avaStyle = `
        width:32px;
        height:32px;
        min-width:32px;
        border-radius:9px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:11px;
        font-weight:700;
        flex-shrink:0;
        font-family:inherit;
        color:#ffffff !important;
        background:${isAI
            ? 'linear-gradient(135deg,#038047 0%,#026738 100%)'
            : 'linear-gradient(135deg,#3b82f6 0%,#2563eb 100%)'
        } !important;
        box-shadow:${isAI
            ? '0 2px 8px rgba(3,128,71,0.25)'
            : '0 2px 8px rgba(59,130,246,0.25)'
        };
    `;

    const bodyStyle = `
        display:flex;
        flex-direction:column;
        max-width:78%;
        gap:4px;
        align-items:${isAI ? 'flex-start' : 'flex-end'};
    `;

    const bubbleStyle = isAI ? `
        background-color:#ffffff !important;
        background:#ffffff !important;
        border:1px solid #e2e8f0 !important;
        border-radius:3px 14px 14px 14px !important;
        padding:12px 16px !important;
        font-size:13.5px !important;
        line-height:1.75 !important;
        color:#1a202c !important;
        box-shadow:0 1px 4px rgba(0,0,0,0.06) !important;
        word-break:break-word !important;
        font-family:inherit !important;
    ` : `
        background:linear-gradient(135deg,#038047 0%,#026738 100%) !important;
        background-color:#038047 !important;
        border:none !important;
        border-radius:14px 3px 14px 14px !important;
        padding:12px 16px !important;
        font-size:13.5px !important;
        line-height:1.6 !important;
        color:#ffffff !important;
        box-shadow:0 2px 10px rgba(3,128,71,0.25) !important;
        word-break:break-word !important;
        font-family:inherit !important;
    `;

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
            <div style="display:flex;gap:5px;align-items:center;padding:13px 16px;background-color:#ffffff !important;background:#ffffff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
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
                <svg viewBox="0 0 24 24" fill="white"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </div>
            <h3>Ready to Analyze X (Twitter)</h3>
            <p>Select a template below or type your own question to analyze X/Twitter conversation data for this project.</p>
        </div>`;
}

function autoResize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 120) + 'px'; }

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatMarkdown(text) {
    if (!text) return '';
    let h = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

    h = h.replace(/```[\w]*\n?([\s\S]*?)```/g,
        '<pre style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;overflow-x:auto;margin:8px 0;"><code style="font-size:12px;color:#1a202c;background:transparent;border:none;padding:0;">$1</code></pre>');

    h = h.replace(/`([^`]+)`/g,
        '<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-size:12px;border:1px solid #e2e8f0;">$1</code>');

    h = h.replace(/^### (.+)$/gm, '<h4 style="font-size:13px;font-weight:700;margin:12px 0 5px;color:#038047;">$1</h4>');
    h = h.replace(/^## (.+)$/gm,  '<h3 style="font-size:14px;font-weight:700;margin:14px 0 6px;color:#038047;">$1</h3>');
    h = h.replace(/^# (.+)$/gm,   '<h2 style="font-size:15px;font-weight:700;margin:16px 0 7px;color:#038047;">$1</h2>');

    h = h.replace(/\*\*\*(.+?)\*\*\*/g, '<strong style="color:#1a202c;font-weight:700;"><em>$1</em></strong>');
    h = h.replace(/\*\*(.+?)\*\*/g,      '<strong style="color:#1a202c;font-weight:700;">$1</strong>');
    h = h.replace(/\*(.+?)\*/g,           '<em>$1</em>');

    h = h.replace(/^---$/gm, '<hr style="border:none;border-top:1px solid #e2e8f0;margin:12px 0;">');

    h = h.replace(/((?:^[-*•] .+(?:\n|$))+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^[-*•] /, '').trim()}</li>`)
            .join('');
        return `<ul style="margin:6px 0 10px;padding-left:20px;color:#1a202c;">${items}</ul>`;
    });

    h = h.replace(/((?:^\d+\. .+(?:\n|$))+)/gm, (block) => {
        const items = block.trim().split('\n')
            .map(l => `<li style="margin-bottom:4px;color:#1a202c;">${l.replace(/^\d+\. /, '').trim()}</li>`)
            .join('');
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