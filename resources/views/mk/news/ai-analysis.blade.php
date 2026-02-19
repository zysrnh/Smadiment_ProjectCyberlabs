@extends('mk.layouts.app')

@section('title', 'AI Analysis - News')

@section('styles')
<style>
    .ai-page {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 20px;
        height: calc(100vh - 160px);
    }

    /* ─── LEFT: Prompt Selector ─── */
    .prompt-panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .prompt-panel-head {
        padding: 18px 18px 14px;
        border-bottom: 1px solid #f1f5f9;
    }

    .prompt-panel-head h3 {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .prompt-panel-head p {
        font-size: 11px;
        color: #94a3b8;
    }

    /* Category Tabs */
    .cat-tabs {
        display: flex;
        gap: 5px;
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }

    .cat-tab {
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        background: #fff;
        transition: all 0.18s;
        white-space: nowrap;
        font-family: 'Poppins', sans-serif;
    }

    .cat-tab:hover { border-color: #038047; color: #038047; }
    .cat-tab.active { background: #038047; border-color: #038047; color: #fff; }

    /* Prompt Cards */
    .prompt-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .prompt-list::-webkit-scrollbar { width: 3px; }
    .prompt-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

    .prompt-group { display: none; }
    .prompt-group.active { display: block; }

    .prompt-group-label {
        font-size: 10px;
        font-weight: 700;
        color: #cbd5e1;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 6px 4px 5px;
    }

    .prompt-card {
        padding: 11px 13px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        margin-bottom: 7px;
        cursor: pointer;
        transition: all 0.18s;
        background: #fff;
        position: relative;
    }

    .prompt-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: transparent;
        border-radius: 3px 0 0 3px;
        transition: all 0.18s;
    }

    .prompt-card:hover { border-color: #038047; background: #f0fdf7; transform: translateX(2px); }
    .prompt-card:hover::before { background: #038047; }
    .prompt-card.selected { border-color: #038047; background: linear-gradient(135deg,#f0fdf7,#dcfce7); box-shadow: 0 2px 8px rgba(3,128,71,.1); }
    .prompt-card.selected::before { background: #038047; }

    .prompt-card-title {
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .prompt-card-desc {
        font-size: 11px;
        color: #64748b;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom Prompt */
    .custom-area {
        padding: 10px;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
    }

    .custom-area label {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .8px;
        display: block;
        margin-bottom: 6px;
    }

    .custom-prompt-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        color: #1e293b;
        resize: none;
        height: 64px;
        transition: border-color 0.2s;
        background: #fff;
    }

    .custom-prompt-input:focus { outline: none; border-color: #038047; }
    .custom-prompt-input::placeholder { color: #cbd5e1; }

    /* ─── RIGHT: Chat Panel ─── */
    .chat-panel {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .chat-header {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
    }

    .chat-header-left { display: flex; align-items: center; gap: 10px; }

    .ai-avatar {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, #038047, #026738);
        display: flex; align-items: center; justify-content: center;
    }

    .ai-avatar svg { width: 17px; height: 17px; stroke: #fff; fill: none; stroke-width: 2; }

    .chat-header-info h4 { font-size: 13px; font-weight: 700; color: #1e293b; }
    .chat-header-info span { font-size: 11px; color: #94a3b8; }

    .chat-header-right { display: flex; align-items: center; gap: 10px; }

    .status-pill {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 600; color: #038047;
        background: #f0fdf7; border: 1px solid #d1fae5;
        padding: 4px 10px; border-radius: 20px;
    }

    .status-dot {
        width: 6px; height: 6px; border-radius: 50%; background: #038047;
        animation: blink 2s infinite;
    }

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .btn-clear {
        padding: 5px 12px;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.18s;
        font-family: 'Poppins', sans-serif;
    }

    .btn-clear:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* Context Bar */
    .context-bar {
        padding: 7px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        display: flex; gap: 16px; flex-wrap: wrap; align-items: center;
    }

    .ctx-item {
        display: flex; align-items: center; gap: 4px;
        font-size: 11px; color: #64748b;
    }

    .ctx-item .ctx-label { color: #038047; font-weight: 700; }

    /* Messages */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .chat-messages::-webkit-scrollbar { width: 3px; }
    .chat-messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

    /* Welcome State */
    .chat-welcome {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 32px;
        gap: 10px;
    }

    .welcome-icon {
        width: 60px; height: 60px;
        border-radius: 16px;
        background: linear-gradient(135deg, #038047, #026738);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 24px rgba(3,128,71,.2);
        margin-bottom: 6px;
    }

    .welcome-icon svg { width: 28px; height: 28px; stroke: #fff; fill: none; stroke-width: 1.5; }
    .chat-welcome h3 { font-size: 17px; font-weight: 700; color: #1e293b; }
    .chat-welcome p { font-size: 12px; color: #64748b; max-width: 340px; line-height: 1.6; }

    .welcome-chips {
        display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; margin-top: 6px;
    }

    .welcome-chip {
        padding: 5px 11px;
        border-radius: 20px;
        background: #f0fdf7;
        border: 1px solid #d1fae5;
        font-size: 11px;
        font-weight: 600;
        color: #038047;
    }

    /* Message Bubble */
    .msg { display: flex; gap: 9px; animation: fadeUp .3s ease; }
    .msg.user { flex-direction: row-reverse; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

    .msg-ava {
        width: 30px; height: 30px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; flex-shrink: 0;
    }

    .msg.ai .msg-ava { background: linear-gradient(135deg,#038047,#026738); color: #fff; }
    .msg.user .msg-ava { background: linear-gradient(135deg,#2FC6F6,#0ea5e9); color: #fff; }

    .msg-bubble {
        max-width: 78%;
        padding: 11px 15px;
        border-radius: 12px;
        font-size: 12.5px;
        line-height: 1.75;
    }

    .msg.ai .msg-bubble {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        border-top-left-radius: 2px;
    }

    .msg.user .msg-bubble {
        background: linear-gradient(135deg,#038047,#026738);
        color: #fff;
        border-top-right-radius: 2px;
    }

    .msg-bubble p { margin: 0 0 6px; }
    .msg-bubble p:last-child { margin: 0; }
    .msg-bubble strong { font-weight: 700; }
    .msg-bubble h4 { font-size: 12px; font-weight: 700; margin: 8px 0 3px; color: #038047; }
    .msg.user .msg-bubble h4 { color: #dcfce7; }
    .msg.ai .msg-bubble ul { margin: 4px 0 4px 14px; }
    .msg.ai .msg-bubble li { margin-bottom: 3px; }

    .msg-time { font-size: 10px; color: #94a3b8; margin-top: 4px; text-align: right; }
    .msg.ai .msg-time { text-align: left; }

    /* Typing indicator */
    .typing-indicator {
        display: flex; gap: 4px; align-items: center;
        padding: 12px 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px; border-top-left-radius: 2px;
        width: fit-content;
    }

    .typing-dot {
        width: 6px; height: 6px; border-radius: 50%; background: #94a3b8;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: .2s; }
    .typing-dot:nth-child(3) { animation-delay: .4s; }

    @keyframes typing { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-6px)} }

    /* Chat Input */
    .chat-input-area {
        padding: 14px 18px;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
    }

    .selected-prompt-preview {
        display: none;
        padding: 7px 12px;
        background: linear-gradient(135deg, #f0fdf7, #dcfce7);
        border: 1px solid #d1fae5;
        border-radius: 8px;
        margin-bottom: 8px;
        font-size: 11px;
        color: #038047;
        font-weight: 600;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .selected-prompt-preview.visible { display: flex; }

    .preview-close {
        cursor: pointer; font-size: 14px; color: #64748b;
        line-height: 1; flex-shrink: 0;
    }

    .preview-close:hover { color: #ef4444; }

    .input-row {
        display: flex; gap: 10px; align-items: flex-end;
    }

    .chat-textarea {
        flex: 1;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-family: 'Poppins', sans-serif;
        color: #1e293b;
        resize: none;
        min-height: 44px;
        max-height: 120px;
        transition: border-color 0.2s;
        background: #fff;
        line-height: 1.5;
    }

    .chat-textarea:focus { outline: none; border-color: #038047; }
    .chat-textarea::placeholder { color: #cbd5e1; }

    .btn-send {
        width: 44px; height: 44px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #038047, #026738);
        color: #fff;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .btn-send:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(3,128,71,.25); }
    .btn-send:disabled { background: #e2e8f0; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn-send svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2; }

    .input-hint { font-size: 10px; color: #94a3b8; margin-top: 6px; text-align: center; }
</style>
@endsection

@section('content')
<div class="top-bar">
    <div class="page-title">
        <h2>🤖 AI Analysis — News</h2>
        <span class="page-subtitle">Analisis cerdas berbasis data menggunakan Claude AI</span>
    </div>
    <div class="top-actions">
        <span style="font-size:12px;color:#64748b;padding:8px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
            📁 Project: <strong>{{ $projectId ?? '-' }}</strong>
        </span>
        <input type="date" id="startDate" value="{{ $startDate }}"
               class="action-btn" style="cursor:pointer;font-family:Poppins,sans-serif;font-size:13px;">
        <span style="align-self:center;color:#94a3b8;font-size:12px;">s/d</span>
        <input type="date" id="endDate" value="{{ $endDate }}"
               class="action-btn" style="cursor:pointer;font-family:Poppins,sans-serif;font-size:13px;">
    </div>
</div>

<div class="content-wrapper">
<div class="ai-page">

    {{-- ════════════════════════════════════════
         LEFT: PROMPT SELECTOR PANEL
    ════════════════════════════════════════ --}}
    <div class="prompt-panel">
        <div class="prompt-panel-head">
            <h3>🎯 Pilih Analisis</h3>
            <p>Pilih template atau tulis prompt sendiri</p>
        </div>

        {{-- Category Tabs --}}
        <div class="cat-tabs">
            <button class="cat-tab active" onclick="switchCat('umum', this)">Umum</button>
            <button class="cat-tab" onclick="switchCat('sentimen', this)">Sentimen</button>
            <button class="cat-tab" onclick="switchCat('isu', this)">Isu & Risiko</button>
            <button class="cat-tab" onclick="switchCat('strategi', this)">Strategi</button>
        </div>

        <div class="prompt-list">

            {{-- UMUM --}}
            <div class="prompt-group active" id="cat-umum">
                <div class="prompt-group-label">Analisis Umum</div>

                <div class="prompt-card" onclick="selectPrompt(this, 'rangkuman')">
                    <div class="prompt-card-title">📋 Rangkuman Isu Utama</div>
                    <div class="prompt-card-desc">Identifikasi dan rangkum 5 isu paling dominan dalam pemberitaan beserta aktor yang terlibat.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'narasi')">
                    <div class="prompt-card-title">🗣️ Framing & Narasi Dominan</div>
                    <div class="prompt-card-desc">Identifikasi framing media, pola narasi dominan, dan apakah ada tendensi tertentu.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'timeline')">
                    <div class="prompt-card-title">⏱️ Pola Waktu & Tren</div>
                    <div class="prompt-card-desc">Analisis kapan peak pemberitaan, apa yang memicunya, dan prediksi tren ke depan.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'publisher')">
                    <div class="prompt-card-title">📰 Analisis Publisher</div>
                    <div class="prompt-card-desc">Media mana yang paling berpengaruh? Apakah ada perbedaan sudut pandang antar media?</div>
                </div>
            </div>

            {{-- SENTIMEN --}}
            <div class="prompt-group" id="cat-sentimen">
                <div class="prompt-group-label">Analisis Sentimen</div>

                <div class="prompt-card" onclick="selectPrompt(this, 'sentimen_dist')">
                    <div class="prompt-card-title">😊 Distribusi Sentimen</div>
                    <div class="prompt-card-desc">Analisis distribusi positif/negatif/netral, isu apa yang paling negatif, dan faktor penyebabnya.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'sentimen_shift')">
                    <div class="prompt-card-title">🔄 Pergeseran Sentimen</div>
                    <div class="prompt-card-desc">Apakah sentimen berubah dari waktu ke waktu? Identifikasi titik perubahan dan penyebabnya.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'sentimen_platform')">
                    <div class="prompt-card-title">📱 Sentimen per Platform</div>
                    <div class="prompt-card-desc">Bandingkan sentimen antara Online News, Twitter, Instagram, dll. Platform mana yang paling negatif?</div>
                </div>
            </div>

            {{-- ISU & RISIKO --}}
            <div class="prompt-group" id="cat-isu">
                <div class="prompt-group-label">Isu & Manajemen Risiko</div>

                <div class="prompt-card" onclick="selectPrompt(this, 'pestle')">
                    <div class="prompt-card-title">📊 Analisis PESTLE</div>
                    <div class="prompt-card-desc">Klasifikasikan isu dalam dimensi Political, Economic, Social, Technological, Legal, Environmental.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'early_warning')">
                    <div class="prompt-card-title">🚨 Early Warning Signals</div>
                    <div class="prompt-card-desc">Identifikasi sinyal awal yang perlu diwaspadai. Isu mana yang berpotensi berkembang menjadi krisis?</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'swot')">
                    <div class="prompt-card-title">⚖️ Analisis SWOT</div>
                    <div class="prompt-card-desc">Pemetaan Strengths, Weaknesses, Opportunities, Threats berdasarkan data pemberitaan.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'krisis')">
                    <div class="prompt-card-title">🔥 Manajemen Krisis</div>
                    <div class="prompt-card-desc">Apakah ada indikasi krisis? Seberapa parah? Rekomendasi langkah mitigasi segera.</div>
                </div>
            </div>

            {{-- STRATEGI --}}
            <div class="prompt-group" id="cat-strategi">
                <div class="prompt-group-label">Strategi & Rekomendasi</div>

                <div class="prompt-card" onclick="selectPrompt(this, 'komunikasi')">
                    <div class="prompt-card-title">💬 Strategi Komunikasi</div>
                    <div class="prompt-card-desc">Rekomendasi strategi komunikasi berdasarkan tren narasi dan sentimen yang teridentifikasi.</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'konten')">
                    <div class="prompt-card-title">✍️ Strategi Konten</div>
                    <div class="prompt-card-desc">Topik dan angle konten apa yang paling relevan untuk diangkat berdasarkan data pemberitaan?</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'media_outreach')">
                    <div class="prompt-card-title">🎯 Media Outreach</div>
                    <div class="prompt-card-desc">Media mana yang harus diprioritaskan untuk PR? Bagaimana membangun relasi dengan media kunci?</div>
                </div>

                <div class="prompt-card" onclick="selectPrompt(this, 'insight')">
                    <div class="prompt-card-title">💡 Key Insights & Action</div>
                    <div class="prompt-card-desc">3-5 insight kunci dan rekomendasi tindakan konkret yang bisa langsung dieksekusi.</div>
                </div>
            </div>

        </div>

        {{-- Custom Prompt --}}
        <div class="custom-area">
            <label>✏️ Tulis Prompt Sendiri</label>
            <textarea class="custom-prompt-input" id="customPromptInput"
                placeholder="Contoh: Analisis bagaimana media meliput isu ini dari sudut pandang ekonomi..."></textarea>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         RIGHT: CHAT PANEL
    ════════════════════════════════════════ --}}
    <div class="chat-panel">

        {{-- Header --}}
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="ai-avatar">
                    <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/><path d="M12 8v4l3 3"/></svg>
                </div>
                <div class="chat-header-info">
                    <h4>Claude AI Analyst</h4>
                    <span>Media Intelligence · News Section</span>
                </div>
            </div>
            <div class="chat-header-right">
                <div class="status-pill">
                    <div class="status-dot"></div>
                    Online
                </div>
                <button class="btn-clear" onclick="clearChat()">🗑 Bersihkan</button>
            </div>
        </div>

        {{-- Context Bar --}}
        <div class="context-bar">
            <div class="ctx-item">
                <span class="ctx-label">📁</span>
                <span>Project: <strong id="ctx-project">{{ $projectId ?? '-' }}</strong></span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">📅</span>
                <span>{{ $startDate ?? '-' }} → {{ $endDate ?? '-' }}</span>
            </div>
            <div class="ctx-item" id="ctx-prompt-name" style="display:none;">
                <span class="ctx-label">🎯</span>
                <span id="ctx-prompt-text">-</span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">
            <div class="chat-welcome" id="welcomeState">
                <div class="welcome-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M8.46 8.46a5 5 0 0 0 0 7.07"/></svg>
                </div>
                <h3>Siap Menganalisis</h3>
                <p>Pilih template analisis di sebelah kiri atau tulis prompt sendiri, lalu klik Analisis untuk memulai.</p>
                <div class="welcome-chips">
                    <span class="welcome-chip">🔍 Analisis Mendalam</span>
                    <span class="welcome-chip">📊 Data-Driven</span>
                    <span class="welcome-chip">⚡ Real-time</span>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="chat-input-area">
            <div class="selected-prompt-preview" id="promptPreview">
                <span id="previewText">-</span>
                <span class="preview-close" onclick="clearSelectedPrompt()">✕</span>
            </div>
            <div class="input-row">
                <textarea class="chat-textarea" id="chatInput"
                    placeholder="Tulis pertanyaan atau pilih template di sebelah kiri..."
                    rows="1"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage();}"
                    oninput="autoResize(this)"></textarea>
                <button class="btn-send" id="sendBtn" onclick="sendMessage()" title="Kirim (Enter)">
                    <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>
            <div class="input-hint">Enter untuk kirim · Shift+Enter untuk baris baru</div>
        </div>

    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
// ═══════════════════════════════════════════════════
// PROMPT TEMPLATES
// ═══════════════════════════════════════════════════
const PROMPTS = {
    rangkuman: {
        label: '📋 Rangkuman Isu Utama',
        text: `Kamu adalah analis senior media intelligence. Berdasarkan data pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}), lakukan analisis komprehensif:

1. **IDENTIFIKASI ISU UTAMA** — Sebutkan minimal 5 isu dominan yang paling banyak diberitakan
2. **DETAIL TIAP ISU** — Untuk setiap isu: inti persoalan, aktor yang terlibat, platform dominan, dan volume relatif
3. **NARASI UTAMA** — Narasi besar apa yang terbentuk dari keseluruhan pemberitaan?
4. **KESIMPULAN** — Gambaran umum situasi media dalam periode ini

Gunakan bahasa profesional, terstruktur, dan berbasis data.`
    },
    narasi: {
        label: '🗣️ Framing & Narasi',
        text: `Sebagai analis framing media, analisis pola narasi dan framing dalam pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **NARASI DOMINAN** — Narasi positif, negatif, dan netral yang paling dominan
2. **POLA FRAMING** — Bagaimana media membingkai isu? (krisis, konflik, humanis, teknis, dll)
3. **BIAS MEDIA** — Apakah ada tendensi atau bias tertentu dalam pemberitaan?
4. **AKTOR DOMINAN** — Siapa yang paling banyak dikutip/disebut dan apa posisinya?
5. **REKOMENDASI** — Bagaimana merespons narasi yang ada?`
    },
    timeline: {
        label: '⏱️ Pola Waktu & Tren',
        text: `Analisis pola temporal pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **PUNCAK PEMBERITAAN** — Kapan peak coverage terjadi dan apa yang memicunya?
2. **POLA WAKTU** — Ada pola berulang? (hari kerja vs weekend, pagi vs malam?)
3. **TRAJECTORY** — Apakah volume pemberitaan naik, turun, atau stabil?
4. **PREDIKSI** — Berdasarkan tren, ke mana pemberitaan ini akan berkembang?
5. **MOMENTUM** — Kapan waktu terbaik untuk melakukan komunikasi publik?`
    },
    publisher: {
        label: '📰 Analisis Publisher',
        text: `Analisis ekosistem publisher/media dalam pemberitaan project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **DOMINASI MEDIA** — Media mana yang paling banyak memberitakan? Berapa proporsinya?
2. **SUDUT PANDANG** — Apakah ada perbedaan sudut pandang yang signifikan antar media?
3. **MEDIA PRO/KONTRA** — Identifikasi media yang cenderung pro/kontra/netral
4. **REACH & INFLUENCE** — Media mana yang paling berpengaruh secara audience?
5. **STRATEGI ENGAGEMENT** — Rekomendasi media mana yang perlu diprioritaskan`
    },
    sentimen_dist: {
        label: '😊 Distribusi Sentimen',
        text: `Lakukan analisis sentimen mendalam untuk pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **DISTRIBUSI** — Persentase positif/negatif/netral dan maknanya
2. **DRIVER NEGATIF** — Isu/topik apa yang paling banyak memicu sentimen negatif?
3. **DRIVER POSITIF** — Apa yang menghasilkan sentimen positif?
4. **PERBANDINGAN PLATFORM** — Apakah sentimen berbeda antar platform media?
5. **REKOMENDASI** — Strategi untuk mengubah sentimen negatif menjadi lebih konstruktif`
    },
    sentimen_shift: {
        label: '🔄 Pergeseran Sentimen',
        text: `Analisis pergeseran sentimen dalam periode pemberitaan ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **TITIK PERUBAHAN** — Kapan sentimen berubah secara signifikan?
2. **TRIGGER** — Apa yang memicu perubahan sentimen? (event, pernyataan, kebijakan?)
3. **KECEPATAN PERUBAHAN** — Seberapa cepat sentimen berubah?
4. **POLA** — Apakah ada pola siklus sentimen yang bisa diidentifikasi?
5. **PROYEKSI** — Berdasarkan tren, ke mana sentimen akan bergerak?`
    },
    sentimen_platform: {
        label: '📱 Sentimen per Platform',
        text: `Bandingkan sentimen pemberitaan antar platform dalam project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **PETA SENTIMEN** — Distribusi sentimen untuk setiap platform (Online News, Twitter, IG, dll)
2. **PLATFORM PALING NEGATIF** — Mana yang paling banyak sentimen negatif dan mengapa?
3. **KARAKTERISTIK TIAP PLATFORM** — Bagaimana cara masing-masing platform menyikapi isu?
4. **CROSS-PLATFORM PATTERN** — Bagaimana isu menyebar antar platform?
5. **PRIORITAS RESPONS** — Platform mana yang harus direspons paling cepat?`
    },
    pestle: {
        label: '📊 Analisis PESTLE',
        text: `Lakukan analisis PESTLE komprehensif berdasarkan pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

**POLITICAL** — Dampak terhadap stabilitas politik, kebijakan publik, legitimasi institusi. Kutipan evidence dari data.

**ECONOMIC** — Dampak terhadap ekonomi, investasi, pasar, UMKM. Persepsi publik terhadap implikasi ekonomi.

**SOCIAL** — Dampak terhadap opini publik, kepercayaan sosial, polarisasi, moral panic.

**TECHNOLOGICAL** — Isu terkait teknologi, platform digital, keamanan siber, AI, disinformasi.

**LEGAL** — Implikasi hukum, regulasi, potensi pelanggaran, tuntutan hukum.

**ENVIRONMENTAL** — Dampak lingkungan, sustainability, bencana (jika relevan).

Setiap dimensi harus didukung evidence nyata dari data pemberitaan.`
    },
    early_warning: {
        label: '🚨 Early Warning',
        text: `Lakukan analisis early warning berdasarkan sinyal dari pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **SINYAL BAHAYA** — Identifikasi 3-5 sinyal paling mengkhawatirkan dalam pemberitaan
2. **LEVEL RISIKO** — Klasifikasikan setiap sinyal: Rendah / Sedang / Tinggi / Kritis
3. **ESKALASI POTENSIAL** — Isu mana yang berpotensi berkembang menjadi krisis viral?
4. **TIMELINE** — Kapan risiko ini kemungkinan akan memuncak?
5. **MITIGASI** — Langkah konkret yang perlu diambil sebelum krisis terjadi`
    },
    swot: {
        label: '⚖️ Analisis SWOT',
        text: `Lakukan analisis SWOT berdasarkan pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

**STRENGTHS** — Narasi positif, dukungan media/publik, momentum yang menguntungkan saat ini.

**WEAKNESSES** — Kelemahan yang terekspos dalam pemberitaan, narasi negatif yang konsisten.

**OPPORTUNITIES** — Peluang komunikasi yang belum dimanfaatkan, gap narasi yang bisa diisi.

**THREATS** — Ancaman dari pemberitaan negatif yang berpotensi eskalasi, aktor yang berlawanan.

Berikan rekomendasi strategi berdasarkan matriks SWOT ini.`
    },
    krisis: {
        label: '🔥 Manajemen Krisis',
        text: `Analisis potensi dan kondisi krisis dari pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **TINGKAT KRISIS** — Apakah ini masuk kategori: Noise / Emerging Issue / Crisis / Ongoing Crisis?
2. **INDIKATOR KRISIS** — Sebutkan indikator spesifik yang menunjukkan tingkat urgensi
3. **AKTOR & TRIGGER** — Siapa yang memicu dan memperbesar krisis? Apa motifnya?
4. **DAMPAK** — Apa dampak nyata dan potensial dari situasi ini?
5. **RESPONSE PLAN** — Rekomendasi tindakan dalam 24 jam, 1 minggu, dan 1 bulan ke depan
6. **KEY MESSAGE** — Pesan kunci yang harus dikomunikasikan ke publik`
    },
    komunikasi: {
        label: '💬 Strategi Komunikasi',
        text: `Susun rekomendasi strategi komunikasi berdasarkan pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **KONDISI SAAT INI** — Ringkasan situasi komunikasi yang perlu direspons
2. **TARGET AUDIENCE** — Segmen publik mana yang paling perlu diperhatikan?
3. **KEY MESSAGE** — 3-5 pesan utama yang harus dikomunikasikan
4. **CHANNEL PRIORITAS** — Platform mana yang paling efektif untuk menjangkau target?
5. **TIMING** — Kapan waktu terbaik untuk berkomunikasi?
6. **TONE & APPROACH** — Nada komunikasi yang tepat (defensif/proaktif/empatis/edukatif)`
    },
    konten: {
        label: '✍️ Strategi Konten',
        text: `Buat rekomendasi strategi konten berdasarkan tren pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **TOPIK RELEVAN** — Topik apa yang sedang hangat dan bisa dimanfaatkan?
2. **FORMAT KONTEN** — Format apa yang paling efektif? (artikel, infografis, video, thread?)
3. **ANGLE UNIK** — Sudut pandang baru apa yang belum diangkat media?
4. **KONTEN COUNTER-NARRATIVE** — Konten apa yang bisa mengimbangi narasi negatif?
5. **CONTENT CALENDAR** — Rekomendasi jadwal posting dalam 2 minggu ke depan
6. **KPI** — Metrik apa yang harus diukur untuk mengukur keberhasilan?`
    },
    media_outreach: {
        label: '🎯 Media Outreach',
        text: `Susun strategi media outreach berdasarkan ekosistem pemberitaan project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

1. **MEDIA PRIORITAS** — Daftar media yang harus diprioritaskan (berdasarkan reach dan sentimen)
2. **MEDIA YANG PERLU DIDEKATI** — Media yang masih netral/negatif dan potensial untuk dikonversi
3. **PENDEKATAN PER MEDIA** — Strategi berbeda untuk media yang berbeda
4. **EXCLUSIF ANGLE** — Tawaran angle eksklusif untuk media tier-1
5. **MEDIA PARTNER** — Media mana yang bisa dijadikan partner jangka panjang?
6. **DO's & DON'Ts** — Hal yang harus dan tidak boleh dilakukan dalam media engagement`
    },
    insight: {
        label: '💡 Key Insights & Action',
        text: `Buat ringkasan eksekutif dengan insight dan action items dari pemberitaan news project ini (project_id: ${getCtxProject()}, periode: ${getCtxDate()}):

**EXECUTIVE SUMMARY** (2-3 kalimat): Gambaran situasi paling penting.

**5 KEY INSIGHTS**:
1. [Insight terpenting]
2. [Insight kedua]
3. [Insight ketiga]
4. [Insight keempat]
5. [Insight kelima]

**ACTION ITEMS** (spesifik dan bisa langsung dieksekusi):
- Segera (24 jam): ...
- Minggu ini: ...
- Bulan ini: ...

**INDIKATOR KEBERHASILAN**: Bagaimana mengukur bahwa tindakan berhasil?`
    },
};

// Helper untuk ambil context
function getCtxProject() {
    return document.getElementById('ctx-project')?.textContent || '-';
}
function getCtxDate() {
    return "{{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}";
}

// ═══════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════
let selectedPromptKey = null;
let chatHistory = [];
let isLoading = false;

// ═══════════════════════════════════════════════════
// CATEGORY SWITCH
// ═══════════════════════════════════════════════════
function switchCat(cat, btn) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.prompt-group').forEach(g => g.classList.remove('active'));
    document.getElementById('cat-' + cat)?.classList.add('active');
}

// ═══════════════════════════════════════════════════
// SELECT PROMPT
// ═══════════════════════════════════════════════════
function selectPrompt(card, key) {
    // Deselect others
    document.querySelectorAll('.prompt-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');

    selectedPromptKey = key;
    const prompt = PROMPTS[key];

    // Update context bar
    document.getElementById('ctx-prompt-name').style.display = 'flex';
    document.getElementById('ctx-prompt-text').textContent = prompt.label;

    // Show preview
    const preview = document.getElementById('promptPreview');
    preview.classList.add('visible');
    document.getElementById('previewText').textContent = prompt.label;

    // Clear chat textarea & focus
    document.getElementById('chatInput').value = '';
    document.getElementById('chatInput').focus();
}

function clearSelectedPrompt() {
    selectedPromptKey = null;
    document.querySelectorAll('.prompt-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('promptPreview').classList.remove('visible');
    document.getElementById('ctx-prompt-name').style.display = 'none';
}

// ═══════════════════════════════════════════════════
// SEND MESSAGE
// ═══════════════════════════════════════════════════
async function sendMessage() {
    if (isLoading) return;

    const customInput = document.getElementById('customPromptInput').value.trim();
    const chatInput = document.getElementById('chatInput').value.trim();

    // Determine final prompt
    let finalPrompt = '';
    let displayLabel = '';

    if (chatInput) {
        // User typed something in chat → use that, append selected template as system context
        finalPrompt = selectedPromptKey
            ? PROMPTS[selectedPromptKey].text + '\n\n---\n\nPertanyaan tambahan: ' + chatInput
            : chatInput;
        displayLabel = chatInput;
    } else if (selectedPromptKey) {
        finalPrompt = PROMPTS[selectedPromptKey].text;
        displayLabel = PROMPTS[selectedPromptKey].label;
    } else if (customInput) {
        finalPrompt = customInput;
        displayLabel = customInput;
    } else {
        alert('Pilih template analisis atau tulis pesan terlebih dahulu.');
        return;
    }

    // Hide welcome state
    document.getElementById('welcomeState')?.remove();

    // Add user message
    addMessage('user', displayLabel);

    // Clear inputs
    document.getElementById('chatInput').value = '';
    autoResize(document.getElementById('chatInput'));

    // Add to history
    chatHistory.push({ role: 'user', content: finalPrompt });

    // Show typing indicator
    const typingEl = addTypingIndicator();
    isLoading = true;
    document.getElementById('sendBtn').disabled = true;

    try {
        // ✅ Via Laravel proxy — bukan langsung ke Anthropic (avoid CORS)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const response = await fetch('{{ route("mk.api.news.ai-proxy") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                max_tokens: 1500,
                system: buildSystemPrompt(),
                messages: chatHistory,
            })
        });

        const data = await response.json();
        typingEl.remove();

        if (data.error) {
            addMessage('ai', '⚠️ ' + data.error);
        } else if (data.content && data.content[0]) {
            const reply = data.content[0].text;
            chatHistory.push({ role: 'assistant', content: reply });
            addMessage('ai', reply);
        } else {
            addMessage('ai', '⚠️ Gagal mendapatkan respons. Coba lagi.');
        }
    } catch (err) {
        typingEl.remove();
        addMessage('ai', '⚠️ Koneksi error: ' + err.message);
    } finally {
        isLoading = false;
        document.getElementById('sendBtn').disabled = false;
    }
}

// ═══════════════════════════════════════════════════
// SYSTEM PROMPT BUILDER
// ═══════════════════════════════════════════════════
function buildSystemPrompt() {
    const projectId = '{{ $projectId ?? "" }}';
    const startDate = document.getElementById('startDate')?.value || '{{ $startDate ?? "" }}';
    const endDate   = document.getElementById('endDate')?.value   || '{{ $endDate ?? "" }}';

    return `Kamu adalah SMADIMENT AI Analyst — asisten analisis media intelligence profesional.

KONTEKS SESI:
- Project ID  : ${projectId}
- Periode     : ${startDate} s/d ${endDate}
- Section     : News Analytics
- Platform    : Online News, Twitter/X, Instagram, Facebook, TikTok, YouTube

PERANMU:
Kamu adalah analis senior media intelligence yang ahli dalam media monitoring, framing analysis, risk assessment, analisis PESTLE, stakeholder mapping, dan strategic communications.

GAYA RESPONS:
- Bahasa Indonesia yang profesional dan mudah dipahami
- Struktur jelas: gunakan heading (##), bullet points, dan bold untuk highlight poin penting
- Insight harus actionable — bukan hanya deskriptif
- Sertakan rekomendasi konkret di setiap analisis
- Jika data spesifik tidak tersedia, tetap berikan framework analisis yang dapat diterapkan`;
}

// ═══════════════════════════════════════════════════
// UI HELPERS
// ═══════════════════════════════════════════════════
function addMessage(role, text) {
    const container = document.getElementById('chatMessages');
    const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    const el = document.createElement('div');
    el.className = `msg ${role}`;

    const initials = role === 'ai' ? 'AI' : 'U';
    const formattedText = formatText(text);

    el.innerHTML = `
        <div class="msg-ava">${initials}</div>
        <div>
            <div class="msg-bubble">${formattedText}</div>
            <div class="msg-time">${now}</div>
        </div>
    `;

    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function addTypingIndicator() {
    const container = document.getElementById('chatMessages');
    const el = document.createElement('div');
    el.className = 'msg ai';
    el.innerHTML = `
        <div class="msg-ava">AI</div>
        <div class="typing-indicator">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    `;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

function formatText(text) {
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/^### (.*$)/gm, '<h4>$1</h4>')
        .replace(/^## (.*$)/gm, '<h4>$1</h4>')
        .replace(/^# (.*$)/gm, '<h4>$1</h4>')
        .replace(/^\* (.+)$/gm, '<li>$1</li>')
        .replace(/^- (.+)$/gm, '<li>$1</li>')
        .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
        .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
        .replace(/\n\n/g, '</p><p>')
        .replace(/^(?!<[hul])/gm, '')
        .replace(/\n/g, '<br>')
        || `<p>${text}</p>`;
}

function clearChat() {
    if (!confirm('Bersihkan riwayat percakapan?')) return;
    chatHistory = [];
    const container = document.getElementById('chatMessages');
    container.innerHTML = `
        <div class="chat-welcome" id="welcomeState">
            <div class="welcome-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/>
                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M8.46 8.46a5 5 0 0 0 0 7.07"/>
                </svg>
            </div>
            <h3>Siap Menganalisis</h3>
            <p>Pilih template analisis di sebelah kiri atau tulis prompt sendiri, lalu klik Analisis untuk memulai.</p>
            <div class="welcome-chips">
                <span class="welcome-chip">🔍 Analisis Mendalam</span>
                <span class="welcome-chip">📊 Data-Driven</span>
                <span class="welcome-chip">⚡ Real-time</span>
            </div>
        </div>`;
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}
</script>
@endsection