{{--
    Shared AI Analysis CSS — included by all platform ai-analysis.blade.php
    Required: $aiPlatform['color'], ['accent'], ['accent2']
--}}
@php
    $pColor  = $aiPlatform['color']   ?? '#038047';
    $pAccent = $aiPlatform['accent']  ?? $pColor;
    $pDark   = $aiPlatform['dark']    ?? $pColor;
    $pGrad   = $aiPlatform['gradient'] ?? "linear-gradient(135deg, {$pAccent} 0%, {$pDark} 100%)";
@endphp
<style>
    :root {
        --ai-primary:      {{ $pColor }};
        --ai-primary-dark: {{ $pDark }};
        --ai-accent:       {{ $pAccent }};
        --ai-gradient:     {{ $pGrad }};
        --ai-brand:        #038047;
        --ai-brand-dark:   #026738;
        --text-primary:    #1a202c;
        --text-secondary:  #64748b;
        --bg-white:        #ffffff;
        --bg-gray-50:      #f8fafc;
        --border:          #e2e8f0;
        --shadow-sm:       0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
        --sidebar-w:       260px;
    }

    /* ── LAYOUT SHELL ── */
    .ai-shell {
        display: flex; height: calc(100vh - 80px); background: #fff;
        border-radius: 16px; border: 1px solid var(--border);
        box-shadow: var(--shadow-sm); overflow: hidden;
    }

    /* ══ SIDEBAR ══ */
    .prompt-sidebar {
        width: var(--sidebar-w); min-width: var(--sidebar-w); background: #f8fafc;
        border-right: 1px solid var(--border); display: flex; flex-direction: column;
        overflow: hidden; transition: width 0.25s ease, min-width 0.25s ease; flex-shrink: 0;
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
        padding: 10px 12px; border-bottom: 1px solid var(--border); flex-shrink: 0;
        overflow: hidden; transition: height 0.25s, padding 0.25s, opacity 0.2s;
        height: 50px; opacity: 1;
    }
    .prompt-sidebar.collapsed .sidebar-search { height: 0; padding: 0; opacity: 0; overflow: hidden; }
    .sidebar-search-input {
        width: 100%; padding: 6px 10px 6px 30px; border: 1px solid var(--border);
        border-radius: 8px; font-size: 12px; font-family: inherit; background: #fff;
        color: var(--text-primary); outline: none; transition: border-color 0.15s; box-sizing: border-box;
    }
    .sidebar-search-input:focus { border-color: var(--ai-brand); }
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
        display: flex; align-items: center; gap: 6px; padding: 6px 14px 4px;
        cursor: pointer; user-select: none;
    }
    .prompt-group-header:hover { background: rgba(0,0,0,0.02); }
    .prompt-sidebar.collapsed .prompt-group-header { justify-content: center; padding: 6px; }
    .group-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .group-label {
        font-size: 10px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--text-secondary); flex: 1;
        white-space: nowrap; overflow: hidden; opacity: 1; transition: opacity 0.2s;
    }
    .prompt-sidebar.collapsed .group-label { opacity: 0; width: 0; }
    .group-chevron {
        width: 12px; height: 12px; color: var(--text-secondary);
        transition: transform 0.2s; flex-shrink: 0;
    }
    .prompt-sidebar.collapsed .group-chevron { display: none; }
    .prompt-group.open .group-chevron { transform: rotate(90deg); }
    .prompt-group-items { overflow: hidden; max-height: 0; transition: max-height 0.25s ease; }
    .prompt-group.open .prompt-group-items { max-height: 9999px; }
    .prompt-sidebar.collapsed .prompt-group-items { display: none; }
    .prompt-item {
        display: flex; align-items: center; gap: 8px; padding: 7px 14px 7px 24px;
        cursor: pointer; font-size: 12.5px; font-weight: 500; color: var(--text-secondary);
        border: none; background: transparent; font-family: inherit; width: 100%;
        text-align: left; transition: background 0.12s, color 0.12s;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.35;
    }
    .prompt-item:hover { background: rgba(3,128,71,0.06); color: var(--ai-brand); }
    .prompt-item.active {
        background: rgba(3,128,71,0.1); color: var(--ai-brand);
        font-weight: 700; border-right: 3px solid var(--ai-brand);
    }
    .prompt-item-dot {
        width: 5px; height: 5px; border-radius: 50%; background: var(--border);
        flex-shrink: 0; transition: background 0.12s;
    }
    .prompt-item:hover .prompt-item-dot,
    .prompt-item.active .prompt-item-dot { background: var(--ai-brand); }
    .prompt-sidebar.collapsed .prompt-item { display: none; }

    /* ══ MAIN CHAT AREA ══ */
    .ai-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
    .ai-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 18px; border-bottom: 1px solid #f1f5f9;
        background: #ffffff; flex-shrink: 0;
    }
    .ai-header-left { display: flex; align-items: center; gap: 10px; }
    .ai-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: var(--ai-accent); display: flex; align-items: center;
        justify-content: center; flex-shrink: 0; position: relative; overflow: hidden;
    }
    .ai-avatar svg { width: 20px; height: 20px; position: relative; z-index: 1; }
    .ai-header-info h4 { font-size: 13.5px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .ai-header-info p  { font-size: 11px; color: var(--text-secondary); margin: 0; }
    .ai-header-right { display: flex; align-items: center; gap: 8px; }
    .status-pill {
        display: flex; align-items: center; gap: 6px; padding: 5px 10px;
        border-radius: 20px; background: #f0fdf4; border: 1px solid #bbf7d0;
        font-size: 11px; font-weight: 600; color: #16a34a; white-space: nowrap;
    }
    .status-pill.loading { background: #fefce8; border-color: #fde68a; color: #ca8a04; }
    .status-pill.error   { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #16a34a; flex-shrink: 0; }
    .status-pill.loading .status-dot { background: #ca8a04; animation: pulse 1s infinite; }
    .status-pill.error   .status-dot { background: #dc2626; }
    .btn-clear, .btn-pdf {
        padding: 5px 12px; border-radius: 8px; border: 1px solid var(--border);
        background: #f8fafc; font-size: 11.5px; font-weight: 600; color: #64748b;
        cursor: pointer; font-family: inherit; transition: all 0.15s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-clear:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    .btn-pdf:hover { background: rgba(3,128,71,0.08); border-color: #038047; color: #038047; }
    .btn-pdf i { font-size: 13px; }

    .active-prompt-bar {
        padding: 7px 18px; background: rgba(3,128,71,0.06);
        border-bottom: 1px solid rgba(3,128,71,0.12);
        display: none; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .active-prompt-bar.show { display: flex; }
    .active-prompt-tag {
        display: flex; align-items: center; gap: 6px; padding: 3px 10px;
        border-radius: 20px; background: var(--ai-brand); color: #fff;
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
        display: flex; flex-direction: column; gap: 14px;
        background: #fafbfc; scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
    }
    .ai-messages::-webkit-scrollbar { width: 4px; }
    .ai-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .welcome-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; text-align: center;
        padding: 40px 20px; flex: 1; gap: 10px;
    }
    .welcome-icon-wrap {
        width: 56px; height: 56px; border-radius: 16px; background: var(--ai-accent);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(0,0,0,.2);
    }
    .welcome-state h3 { font-size: 17px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .welcome-state p  { font-size: 12.5px; color: var(--text-secondary); margin: 0; max-width: 340px; line-height: 1.6; }
    .data-loading-badge {
        display: flex; align-items: center; gap: 8px; padding: 7px 14px;
        background: #fff; border: 1px solid var(--border);
        border-radius: 20px; font-size: 11.5px; color: var(--text-secondary);
    }
    .spin {
        width: 13px; height: 13px; border: 2px solid #e2e8f0;
        border-top-color: var(--ai-brand); border-radius: 50%;
        animation: spin 0.8s linear infinite; flex-shrink: 0;
    }

    .ai-input-area {
        border-top: 1px solid var(--border); background: #ffffff;
        padding: 10px 16px 12px; flex-shrink: 0;
    }
    .input-row { display: flex; gap: 8px; align-items: flex-end; }
    .chat-textarea {
        flex: 1; resize: none; border: 1px solid var(--border); border-radius: 12px;
        padding: 10px 14px; font-size: 13px; font-family: inherit; line-height: 1.5;
        background: #f8fafc; color: var(--text-primary); outline: none;
        transition: border-color 0.15s, background 0.15s;
        min-height: 42px; max-height: 120px; overflow-y: auto;
    }
    .chat-textarea:focus { border-color: var(--ai-brand); background: #fff; }
    .chat-textarea:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-send {
        width: 40px; height: 40px; border-radius: 11px; border: none;
        background: linear-gradient(135deg, #038047 0%, #026738 100%); color: #fff;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 3px 10px rgba(3,128,71,0.32); transition: all 0.15s; flex-shrink: 0;
    }
    .btn-send:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(3,128,71,0.45); }
    .btn-send:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-send svg { width: 16px; height: 16px; }
    .input-hint { font-size: 10.5px; color: #cbd5e1; text-align: center; margin-top: 5px; }
    .typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #94a3b8; animation: typing 1.2s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    /* ══ BUBBLE ACTION BUTTONS ══ */
    .bubble-actions {
        display: flex; align-items: center; gap: 2px; opacity: 0;
        transition: opacity 0.15s; padding: 2px 0;
    }
    .chat-msg-row:hover .bubble-actions { opacity: 1; }
    .bubble-act-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 8px; border-radius: 6px; border: 1px solid transparent;
        background: transparent; font-size: 10.5px; font-weight: 600;
        color: #94a3b8; cursor: pointer; font-family: inherit;
        transition: all 0.12s; white-space: nowrap;
    }
    .bubble-act-btn:hover { background: #f1f5f9; border-color: #e2e8f0; color: #64748b; }
    .bubble-act-btn.copied { color: #038047; }
    .bubble-act-btn svg, .bubble-act-btn i { width: 12px; height: 12px; font-size: 12px; flex-shrink: 0; }

    /* ══ IMAGE ATTACH (hidden for now) ══ */
    .btn-attach {
        display: none !important;
        background: #f8fafc; color: var(--text-secondary); cursor: pointer;
        align-items: center; justify-content: center;
        transition: all 0.15s; flex-shrink: 0;
    }
    .btn-attach:hover { background: #f1f5f9; border-color: #cbd5e1; color: var(--text-primary); }
    .btn-attach:disabled { opacity: 0.4; cursor: not-allowed; }
    .btn-attach svg { width: 18px; height: 18px; }
    .img-preview-bar {
        display: none;
        flex-wrap: wrap;
    }
    .img-preview-bar.show { display: flex; }
    .img-preview-item {
        position: relative; width: 56px; height: 56px; border-radius: 8px;
        overflow: hidden; border: 1px solid var(--border); flex-shrink: 0;
    }
    .img-preview-item img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .img-preview-remove {
        position: absolute; top: 2px; right: 2px; width: 18px; height: 18px;
        border-radius: 50%; background: rgba(0,0,0,0.6); color: #fff;
        border: none; cursor: pointer; display: flex; align-items: center;
        justify-content: center; font-size: 11px; line-height: 1;
        transition: background 0.12s;
    }
    .img-preview-remove:hover { background: #dc2626; }
    .chat-img-thumb {
        max-width: 240px; max-height: 180px; border-radius: 8px;
        margin-top: 6px; cursor: pointer; transition: transform 0.15s;
    }
    .chat-img-thumb:hover { transform: scale(1.02); }

    @keyframes spin    { to { transform: rotate(360deg); } }
    @keyframes pulse   { 0%,100% { opacity:1; } 50% { opacity:.4; } }
    @keyframes typing  { 0%,80%,100% { transform:scale(0.8); opacity:.5; } 40% { transform:scale(1.1); opacity:1; } }
    @keyframes msgIn   { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    /* ══ PDF EXPORT STYLES ══ */
    .pdf-export-area { display: none; }
    @media print {
        body * { visibility: hidden; }
        .pdf-export-area, .pdf-export-area * { visibility: visible; display: block !important; }
        .pdf-export-area { position: absolute; left: 0; top: 0; width: 100%; }
    }

    @media (max-width: 768px) {
        .prompt-sidebar { display: none; }
        .ai-header-right { gap: 5px; }
    }
</style>
