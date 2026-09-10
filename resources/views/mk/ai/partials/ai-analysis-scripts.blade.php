{{--
    Shared AI Analysis Scripts — included by all platform ai-analysis.blade.php
    Required JS globals before include:
        const PROJECT_ID, PLATFORM, START_DATE, END_DATE, ROUTES
        function buildSystemPrompt() — defined per platform
--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

@include('mk.ai.partials.prompts')

<script>
@verbatim

// ═══════════════════════════════════════════════════════════════════
// SIDEBAR PROMPT GROUPS
// ═══════════════════════════════════════════════════════════════════
const PROMPT_GROUPS = [
    { key: 'isu',       label: 'Analisis Isu',          color: '#038047', keys: ['butterfly_effect','isu_positif_negatif','isu_swot','analisis_percakapan','analisis_agenda_setting','narrative_analysis'] },
    { key: 'krisis',    label: 'Krisis & Trust',        color: '#e53e3e', keys: ['krisis_scct','edelman_trust'] },
    { key: 'framing',   label: 'Framing & Wacana',      color: '#7c3aed', keys: ['framing_entman_edelman','framing_entman','cda_fairclough','analisis_wacana_vandijk','analisis_wacana_wodak'] },
    { key: 'strategi',  label: 'Strategi & Komunikasi', color: '#1877F2', keys: ['pestle','uses_gratifications','stakeholder_mapping','strategi_riding_the_wave','strategi_counter_narrative','analisis_isu_parpol'] },
    { key: 'intelijen', label: 'Intelijen',              color: '#d97706', keys: ['analisis_intelijen_mcdowell','analisis_intelijen_prunckun','analisis_intelijen_sherman_kent','hybrid_warfare_info_ops'] },
    { key: 'laporan',   label: 'Laporan Pimpinan',       color: '#0f766e', keys: ['laporan_direksi','laporan_pimpinan_kapolri','laporan_presiden','laporan_harian_bank'] },
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

// ═══════════════════════════════════════════════════════════════════
// ACTIVE PROMPT
// ═══════════════════════════════════════════════════════════════════
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

// ═══════════════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════════════
let chatHistory = [], isLoading = false, cachedDataset = null, dataReady = false, preloadPromise = null;

document.addEventListener('DOMContentLoaded', () => {
    renderSidebar();
    PROJECT_ID ? preloadProjectData() : setReady('Tidak ada project yang dipilih', true);
});

// ═══════════════════════════════════════════════════════════════════
// PRELOAD DATA
// ═══════════════════════════════════════════════════════════════════
function preloadProjectData() {
    setStatus('loading', 'Menyiapkan data…');
    preloadPromise = (async () => {
        try {
            const qs  = new URLSearchParams({ project_id: PROJECT_ID, start_date: START_DATE, end_date: END_DATE });
            const res  = await fetch(`${ROUTES.aiAnalysisData}?${qs}`);
            const json = await res.json();
            if (!json.success) throw new Error(json.error || 'Gagal memuat data');
            cachedDataset = json.data.text_dataset ?? json.data.dataset;
            dataReady     = true;
            const s   = json.data.summary || {};
            const pos = s.sentiment?.positive ?? s.total_positive ?? 0;
            const neg = s.sentiment?.negative ?? s.total_negative ?? 0;
            const neu = s.sentiment?.neutral  ?? s.total_neutral  ?? 0;
            const tot = (s.total_mentions ?? (pos + neg + neu)) || 1;
            const posts = s.total_posts ?? s.total_articles ?? s.total_mentions ?? 0;
            const pctPos = s.pct_positive ?? Math.round((pos/tot)*100);
            const pctNeg = s.pct_negative ?? Math.round((neg/tot)*100);
            setReady(
                `${Number(posts).toLocaleString('id-ID')} posts · +${pctPos}% / -${pctNeg}% · ${START_DATE} → ${END_DATE}`
            );
        } catch (err) {
            cachedDataset = `Project ID: ${PROJECT_ID}, Platform: ${PLATFORM}, Periode: ${START_DATE} s/d ${END_DATE}`;
            dataReady = true;
            setReady('Data live siap — mode analisis aktif', false);
        }
    })();
    return preloadPromise;
}

// ═══════════════════════════════════════════════════════════════════
// SEND MESSAGE
// ═══════════════════════════════════════════════════════════════════
function isAnalyticalMessage(text) {
    if (!text) return false;
    const kw = ['analisis','analysis','analyze','analisa','data','sentimen','sentiment','positif','negatif','negative','positive','isu','issue','topik','topic','konten','content','viral','mention','swot','pestle','scct','narasi','narrative','krisis','crisis','komunikasi','communication','engagement','rangkum','ringkas','summarize','summary','pola','pattern','siapa','who','apa','what','bagaimana','how','mengapa','why','laporan','report','project','hashtag','trending','percakapan','tweet','post','video','comment','like','share','view','follower','creator','channel','publisher','audience',PLATFORM.toLowerCase()];
    const l = text.toLowerCase();
    return kw.some(k => l.includes(k));
}

async function sendMessage() {
    if (isLoading) return;
    const chatInput = document.getElementById('chatInput').value.trim();
    let promptText = '', displayLabel = '';
    if (activeChip && PROMPTS[activeChip]) {
        promptText   = chatInput || PROMPTS[activeChip].text;
        displayLabel = PROMPTS[activeChip].label;
    } else if (chatInput) {
        promptText = displayLabel = chatInput;
    } else if (pendingImages.length) {
        promptText = displayLabel = '(gambar terlampir)';
    } else return;

    // Capture attached images before clearing
    const attachedImgs = pendingImages.map(img => img.dataUrl);
    pendingImages = [];
    renderImgPreviews();

    document.getElementById('welcomeState')?.remove();
    appendMsg('user', displayLabel, attachedImgs);
    const inp = document.getElementById('chatInput');
    inp.value = ''; inp.placeholder = 'Ketik pesan…'; autoResize(inp);
    clearActivePrompt();

    isLoading = true;
    document.getElementById('sendBtn').disabled = true;

    // If data is still loading in background, wait briefly
    let typingEl = appendTyping(`Menganalisis data ${PLATFORM}…`);
    if (!dataReady && preloadPromise) {
        await Promise.race([
            preloadPromise,
            new Promise(r => setTimeout(r, 4000))
        ]);
    }

    let finalPrompt = promptText;
    if (isAnalyticalMessage(promptText) && cachedDataset) finalPrompt += '\n\n' + cachedDataset;

    // Build message content — text + images (multimodal)
    let msgContent;
    if (attachedImgs.length) {
        msgContent = [{ type: 'text', text: finalPrompt }];
        attachedImgs.forEach(dataUrl => {
            const match = dataUrl.match(/^data:(image\/\w+);base64,(.+)$/);
            if (match) {
                msgContent.push({ type: 'image', source: { type: 'base64', media_type: match[1], data: match[2] } });
            }
        });
    } else {
        msgContent = finalPrompt;
    }

    chatHistory.push({ role: 'user', content: msgContent });
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

// ═══════════════════════════════════════════════════════════════════
// UI HELPERS
// ═══════════════════════════════════════════════════════════════════
let _msgCounter = 0;

function appendMsg(role, text, images) {
    const container = document.getElementById('aiMessages');
    const now = new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
    const el  = document.createElement('div');
    const isAI = role === 'ai';
    const msgId = 'msg_' + (++_msgCounter);
    el.style.cssText = `display:flex;gap:10px;animation:msgIn .22s ease;max-width:100%;flex-direction:${isAI?'row':'row-reverse'};align-items:flex-start;`;
    el.className = 'chat-msg-row';
    el.dataset.role = role;
    el.dataset.msgId = msgId;
    if (!isAI && text) el.dataset.rawText = text;
    if (isAI && text)  el.dataset.rawText = text;
    const ava = `width:32px;height:32px;min-width:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;color:#fff !important;background:${isAI?'linear-gradient(135deg,#038047 0%,#026738 100%)':'linear-gradient(135deg,#64748b 0%,#475569 100%)'} !important;box-shadow:${isAI?'0 2px 8px rgba(3,128,71,.25)':'0 2px 8px rgba(100,116,139,.25)'};`;
    const bub = isAI
        ? `background:#fff !important;border:1px solid #e2e8f0 !important;border-radius:3px 14px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.75 !important;color:#1a202c !important;box-shadow:0 1px 4px rgba(0,0,0,.06) !important;word-break:break-word !important;font-family:inherit !important;`
        : `background:linear-gradient(135deg,#64748b 0%,#475569 100%) !important;border:none !important;border-radius:14px 3px 14px 14px !important;padding:12px 16px !important;font-size:13.5px !important;line-height:1.6 !important;color:#fff !important;box-shadow:0 2px 10px rgba(100,116,139,.3) !important;word-break:break-word !important;font-family:inherit !important;`;

    // Build image thumbnails if any
    let imgHtml = '';
    if (images && images.length) {
        imgHtml = images.map(src => `<img class="chat-img-thumb" src="${src}" onclick="window.open(this.src,'_blank')" alt="attached image">`).join('');
    }

    // Build action buttons for AI bubbles
    const actionsHtml = isAI ? `<div class="bubble-actions">
        <button class="bubble-act-btn" onclick="copyBubbleText(this)" title="Copy teks">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
            <span>Copy</span>
        </button>
        <button class="bubble-act-btn" onclick="exportBubblePDF(this)" title="Download PDF">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
            <span>PDF</span>
        </button>
    </div>` : '';

    el.innerHTML = `<div style="${ava}">${isAI?'AI':'U'}</div><div style="display:flex;flex-direction:column;max-width:78%;gap:4px;align-items:${isAI?'flex-start':'flex-end'};"><div class="chat-bubble" style="${bub}">${isAI?formatMarkdown(text):`<span style="color:#fff">${escHtml(text)}</span>`}${imgHtml}</div>${actionsHtml}<div style="font-size:10px;color:#cbd5e1;padding:0 4px;">${now}</div></div>`;
    container.appendChild(el);
    container.scrollTop = container.scrollHeight;
    return el;
}

// ═══════════════════════════════════════════════════════════════════
// BUBBLE ACTIONS — COPY & SINGLE PDF
// ═══════════════════════════════════════════════════════════════════
function copyBubbleText(btn) {
    const row = btn.closest('.chat-msg-row');
    const raw = row?.dataset.rawText || row?.querySelector('.chat-bubble')?.innerText || '';
    navigator.clipboard.writeText(raw).then(() => {
        const label = btn.querySelector('span');
        btn.classList.add('copied');
        label.textContent = 'Copied!';
        setTimeout(() => { btn.classList.remove('copied'); label.textContent = 'Copy'; }, 1500);
    });
}

const PDF_PAGE_STYLE = `
<style>
    * { box-sizing: border-box; }
    body, div, p, span, strong, em, h1, h2, h3, h4, table, th, td {
        font-family: 'Segoe UI', system-ui, -apple-system, Roboto, Helvetica, Arial, sans-serif;
    }
    .ai-num-card, .ai-sub-card, .ai-evidence-box, .ai-table-wrap, .ai-bullet-item, p, h2, h3, h4, tr, blockquote, li {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        break-inside: avoid-page !important;
    }
    h2, h3, h4 {
        page-break-after: avoid !important;
        break-after: avoid !important;
        margin-top: 14px !important;
        margin-bottom: 6px !important;
    }
    .ai-num-card {
        margin: 10px 0 6px !important;
        padding: 9px 12px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-left: 4px solid #038047 !important;
        border-radius: 6px !important;
    }
    .ai-sub-card {
        margin: 5px 0 5px 14px !important;
        padding: 7px 11px !important;
        border-radius: 4px !important;
        font-size: 12px !important;
        line-height: 1.55 !important;
    }
    .ai-table-wrap {
        margin: 10px 0 !important;
    }
    table {
        page-break-inside: auto !important;
        width: 100% !important;
    }
    tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
</style>
`;

function exportBubblePDF(btn) {
    const row = btn.closest('.chat-msg-row');
    const bubble = row?.querySelector('.chat-bubble');
    if (!bubble) return;

    const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' });
    const html = `
        ${PDF_PAGE_STYLE}
        <div style="font-family:'Segoe UI',system-ui,-apple-system,sans-serif;padding:0;color:#1e293b;">
            <!-- Executive Header Banner -->
            <div style="background:#0f172a;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;margin-bottom:14px;page-break-inside:avoid;break-inside:avoid;">
                <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.15);padding-bottom:10px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;background:#038047;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;color:#fff;">
                            S
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;color:#ffffff;">SMADIMENT MEDIA INTELLIGENCE</div>
                            <div style="font-size:10px;color:#94a3b8;">EXECUTIVE INTELLIGENCE BRIEFING</div>
                        </div>
                    </div>
                    <div style="background:#dc2626;color:#ffffff;font-size:9.5px;font-weight:800;padding:3px 8px;border-radius:4px;letter-spacing:0.06em;text-transform:uppercase;">
                        CONFIDENTIAL
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:8px;">
                    <div>
                        <h1 style="margin:0;font-size:16px;font-weight:800;color:#ffffff;">${escHtml(PLATFORM)} AI Analysis Report</h1>
                        <p style="margin:3px 0 0;font-size:10.5px;color:#cbd5e1;">Project ID: <strong style="color:#fff;">${escHtml(PROJECT_ID)}</strong> &bull; Period: <strong style="color:#fff;">${escHtml(START_DATE)} s/d ${escHtml(END_DATE)}</strong></p>
                    </div>
                    <div style="font-size:10px;color:#94a3b8;text-align:right;">
                        <div>Generated: <span style="color:#cbd5e1;">${now}</span></div>
                        <div>Classification: <span style="color:#cbd5e1;">INTERNAL USE</span></div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div style="padding:14px 16px;background:#ffffff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;font-size:12.5px;line-height:1.75;color:#1e293b;">
                ${bubble.innerHTML}
            </div>

            <!-- Executive Footer -->
            <div style="margin-top:20px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;font-size:9px;color:#94a3b8;page-break-inside:avoid;break-inside:avoid;">
                <span>SMADIMENT INTELLIGENCE &bull; ALL RIGHTS RESERVED</span>
                <span>${escHtml(PLATFORM)} &bull; ${now}</span>
            </div>
        </div>`;

    const filename = `AI_Response_${PLATFORM}_${PROJECT_ID}_${Date.now()}.pdf`;
    _renderPDF(html, filename);
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
    const pill = document.getElementById('statusPill'), span = document.getElementById('statusText');
    if(pill) pill.className = 'status-pill' + (type !== 'online' ? ` ${type}` : '');
    if(span) span.textContent = text;
}

function setReady(msg, isWarn = false) {
    setStatus(isWarn ? 'error' : 'online', isWarn ? 'Limited' : 'Online');
    document.getElementById('dataLoadingBadge')?.remove();
    const inp = document.getElementById('chatInput'), btn = document.getElementById('sendBtn'), att = document.getElementById('btnAttach');
    if(inp) { inp.disabled = false; inp.placeholder = 'Kirim pesan…'; }
    if(btn) btn.disabled = false;
    if(att) att.disabled = false;
}

function clearChat() {
    if (!chatHistory.length) return;
    if (!confirm('Hapus riwayat percakapan?')) return;
    chatHistory = [];
    const pName = PLATFORM || 'Platform';
    document.getElementById('aiMessages').innerHTML = `
        <div class="welcome-state" id="welcomeState">
            <div class="welcome-icon-wrap" style="background:${document.querySelector('.ai-avatar')?.style.background || '#038047'};">
                ${document.querySelector('.ai-avatar')?.innerHTML || '<i class="ph ph-robot" style="font-size:24px;color:#fff;"></i>'}
            </div>
            <h3>Ready to Analyze ${escHtml(pName)}</h3>
            <p>Pilih template dari panel kiri atau ketik pertanyaan sendiri.</p>
        </div>`;
    clearActivePrompt();
}

function autoResize(el) { el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 120) + 'px'; }
function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ═══════════════════════════════════════════════════════════════════
// IMAGE ATTACH
// ═══════════════════════════════════════════════════════════════════
let pendingImages = []; // array of { file, dataUrl }

function handleImgAttach(input) {
    const files = Array.from(input.files || []);
    if (!files.length) return;
    files.forEach(file => {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 10 * 1024 * 1024) { alert('Maksimal 10MB per gambar.'); return; }
        if (pendingImages.length >= 4) { alert('Maksimal 4 gambar per pesan.'); return; }
        const reader = new FileReader();
        reader.onload = (e) => {
            pendingImages.push({ file, dataUrl: e.target.result });
            renderImgPreviews();
        };
        reader.readAsDataURL(file);
    });
    input.value = '';
}

function renderImgPreviews() {
    const bar = document.getElementById('imgPreviewBar');
    if (!pendingImages.length) { bar.classList.remove('show'); bar.innerHTML = ''; return; }
    bar.classList.add('show');
    bar.innerHTML = pendingImages.map((img, i) => `
        <div class="img-preview-item">
            <img src="${img.dataUrl}" alt="preview">
            <button class="img-preview-remove" onclick="removeImgPreview(${i})" title="Hapus">&times;</button>
        </div>`).join('');
}

function removeImgPreview(idx) {
    pendingImages.splice(idx, 1);
    renderImgPreviews();
}

function cleanClientText(str) {
    if (!str) return '';
    return str
        .replace(/aEURoe/g, '“')
        .replace(/aEUR/g, '”')
        .replace(/â€™/g, '’')
        .replace(/â€œ/g, '“')
        .replace(/â€ /g, '”')
        .replace(/â€“/g, '–')
        .replace(/â€”/g, '—')
        .replace(/â€¦/g, '…')
        .replace(/ðŸ[\w\d\S]*/g, '');
}

function formatInlineMarkdown(text) {
    if (!text) return '';
    let h = text;
    h = h.replace(/\*\*\*(.+?)\*\*\*/g, '<strong style="color:#0f172a;font-weight:700;"><em>$1</em></strong>');
    h = h.replace(/\*\*(.+?)\*\*/g, '<strong style="color:#0f172a;font-weight:700;">$1</strong>');
    h = h.replace(/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/g, '<em>$1</em>');
    h = h.replace(/`([^`]+)`/g, '<code style="background:#f1f5f9;color:#0f172a;padding:2px 5px;border-radius:3px;font-size:11.5px;border:1px solid #e2e8f0;">$1</code>');
    return h;
}

function parseMarkdownTables(text) {
    const tableRegex = /((?:^[ \t]*\|.+?\|[ \t]*(?:\r?\n|$)){2,})/gm;
    return text.replace(tableRegex, (match) => {
        const lines = match.trim().split(/\r?\n/).map(l => l.trim()).filter(Boolean);
        if (lines.length < 2) return match;

        let separatorIdx = lines.findIndex((l, idx) => idx > 0 && /^\|?([ \t]*:?-+:?[ \t]*\|)+[ \t]*:?-+:?[ \t]*\|?$/.test(l));
        if (separatorIdx === -1) return match;

        const parseRow = (line) => {
            let clean = line.replace(/^\|/, '').replace(/\|$/, '');
            return clean.split('|').map(c => c.trim());
        };

        const headerCols = parseRow(lines[0]);
        const alignments = lines[separatorIdx].replace(/^\|/, '').replace(/\|$/, '').split('|').map(s => {
            s = s.trim();
            if (s.startsWith(':') && s.endsWith(':')) return 'center';
            if (s.endsWith(':')) return 'right';
            return 'left';
        });

        let html = '<div class="ai-table-wrap" style="overflow-x:auto;margin:12px 0;border-radius:6px;border:1px solid #e2e8f0;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,0.04);">';
        html += '<table style="width:100%;border-collapse:collapse;font-size:12.5px;line-height:1.5;text-align:left;">';

        html += '<thead style="background:#f8fafc;border-bottom:2px solid #e2e8f0;"><tr>';
        headerCols.forEach((col, i) => {
            const align = alignments[i] || 'left';
            html += `<th style="padding:8px 12px;font-weight:700;color:#0f172a;text-align:${align};border-right:1px solid #e2e8f0;">${formatInlineMarkdown(col)}</th>`;
        });
        html += '</tr></thead>';

        html += '<tbody>';
        const bodyLines = lines.filter((_, idx) => idx !== 0 && idx !== separatorIdx);
        bodyLines.forEach((line, rowIdx) => {
            const rowCols = parseRow(line);
            const rowBg = rowIdx % 2 === 1 ? '#fcfdfd' : '#ffffff';
            html += `<tr style="background:${rowBg};border-bottom:1px solid #f1f5f9;">`;
            rowCols.forEach((col, i) => {
                const align = alignments[i] || 'left';
                html += `<td style="padding:8px 12px;color:#334155;text-align:${align};border-right:1px solid #f1f5f9;">${formatInlineMarkdown(col)}</td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    });
}

function formatMarkdown(text) {
    if (!text) return '';
    text = cleanClientText(text);

    // 1. Pre-processing: Auto break mashed inline bullets into separate lines
    text = text.replace(/([^\n])\s+([*•\-])\s+(?=\*{0,2}(?:Tindakan|Komunikasi|Rekomendasi|Implikasi|Solusi|Dampak|Penyebab|Strategi|Catatan)\b)/gi, "$1\n$2 ");

    // 2. Escape HTML
    let h = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

    // 3. Code blocks
    h = h.replace(/```[\w]*\n?([\s\S]*?)```/g,'<pre style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;overflow-x:auto;margin:8px 0;"><code style="font-size:12px;color:#1a202c;background:transparent;border:none;padding:0;">$1</code></pre>');
    h = h.replace(/`([^`]+)`/g,'<code style="background:#f1f5f9;color:#0f172a;padding:2px 6px;border-radius:4px;font-size:12px;border:1px solid #e2e8f0;">$1</code>');

    // 4. Headers
    h = h.replace(/^### (.+)$/gm,'<h4 style="font-size:13.5px;font-weight:700;margin:14px 0 6px;color:#038047;border-bottom:1px solid #f1f5f9;padding-bottom:3px;">$1</h4>');
    h = h.replace(/^## (.+)$/gm, '<h3 style="font-size:15px;font-weight:800;margin:16px 0 8px;color:#0f172a;border-bottom:2px solid #e2e8f0;padding-bottom:4px;">$1</h3>');
    h = h.replace(/^# (.+)$/gm,  '<h2 style="font-size:17px;font-weight:800;margin:18px 0 10px;color:#026738;border-bottom:2px solid #038047;padding-bottom:6px;">$1</h2>');

    // 5. Bold & Italic (Strict regex to prevent swallowing list item asterisks)
    h = h.replace(/\*\*\*(?!\s)([^\*\n]+?)(?<!\s)\*\*\*/g,'<strong style="color:#0f172a;font-weight:700;"><em>$1</em></strong>');
    h = h.replace(/\*\*(?!\s)([^\*\n]+?)(?<!\s)\*\*/g,'<strong style="color:#0f172a;font-weight:700;">$1</strong>');
    h = h.replace(/(?<!\*)\*(?!\s)([^\*\n]+?)(?<!\s)\*(?!\*)/g,'<em>$1</em>');
    h = h.replace(/^---$/gm,'<hr style="border:none;border-top:1px solid #e2e8f0;margin:14px 0;">');

    // 6. Tables
    h = parseMarkdownTables(h);

    // 7. Sub-action cards (Tindakan, Komunikasi, Rekomendasi, etc.)
    h = h.replace(/^[-*•]\s+\*{0,2}(Tindakan|Komunikasi|Rekomendasi|Implikasi|Solusi|Dampak|Penyebab|Strategi|Catatan)\*{0,2}[:\-]\s*(.+)$/gim, (match, label, content) => {
        const l = label.toLowerCase();
        let color = '#038047'; let bg = '#f0fdf4'; let icon = '🎯';
        if (l.includes('komunikasi')) { color = '#1877F2'; bg = '#eff6ff'; icon = '📢'; }
        else if (l.includes('rekomendasi') || l.includes('strategi')) { color = '#7c3aed'; bg = '#f5f3ff'; icon = '💡'; }
        else if (l.includes('implikasi') || l.includes('dampak')) { color = '#d97706'; bg = '#fffbeb'; icon = '⚡'; }

        return `<div class="ai-sub-card" style="margin:6px 0 6px 14px;padding:8px 12px;background:${bg};border:1px solid #e2e8f0;border-left:3px solid ${color};border-radius:4px;font-size:12.5px;line-height:1.6;color:#1e293b;page-break-inside:avoid;break-inside:avoid;"><span style="font-weight:700;color:${color};display:inline-flex;align-items:center;gap:4px;margin-right:4px;"><span>${icon}</span> ${label}:</span> <span>${formatInlineMarkdown(content)}</span></div>`;
    });

    // 8. Numbered issue cards
    h = h.replace(/^(\d+)\.\s+(\*\*.+?\*\*|[^:\n]+:?)(.*)$/gm, (match, num, title, rest) => {
        let cleanTitle = formatInlineMarkdown(title.trim());
        let cleanRest  = formatInlineMarkdown(rest.trim());
        return `<div class="ai-num-card" style="margin:12px 0 6px;padding:10px 14px;background:#ffffff;border:1px solid #e2e8f0;border-left:4px solid #038047;border-radius:6px;page-break-inside:avoid;break-inside:avoid;"><div style="display:flex;gap:8px;align-items:baseline;"><span class="ai-badge-num" style="background:#e6f4ea;color:#038047;font-weight:800;font-size:11px;padding:2px 7px;border-radius:4px;flex-shrink:0;">#${num}</span><div style="flex:1;font-size:13px;color:#0f172a;line-height:1.55;"><strong style="color:#0f172a;font-weight:700;">${cleanTitle}</strong> ${cleanRest}</div></div></div>`;
    });

    // 9. Regular bullets
    h = h.replace(/^[-*•]\s+(.+)$/gm, '<div class="ai-bullet-item" style="display:flex;gap:6px;margin:4px 0 4px 14px;align-items:flex-start;font-size:12.5px;line-height:1.6;color:#1e293b;page-break-inside:avoid;break-inside:avoid;"><span style="color:#038047;min-width:12px;flex-shrink:0;font-weight:700;">•</span><div style="flex:1;">$1</div></div>');

    // 10. Paragraphs
    h = h.split(/\n{2,}/).map(p => {
        p = p.trim();
        if (!p) return '';
        if (/^<(h[2-4]|pre|hr|div|table)/.test(p)) return p;
        return `<p style="margin:0 0 8px;color:#1e293b;font-size:13px;line-height:1.65;">${p.replace(/\n/g,'<br>')}</p>`;
    }).join('\n');

    return h;
}

// ═══════════════════════════════════════════════════════════════════
// PDF EXPORT
// ═══════════════════════════════════════════════════════════════════
function exportChatPDF() {
    const rows = document.querySelectorAll('.chat-msg-row');
    if (!rows.length) { alert('Belum ada percakapan untuk diexport.'); return; }

    const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' });
    let html = `
        ${PDF_PAGE_STYLE}
        <div style="font-family:'Segoe UI',system-ui,-apple-system,sans-serif;padding:0;color:#1e293b;">
            <!-- Executive Header Banner -->
            <div style="background:#0f172a;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;margin-bottom:14px;page-break-inside:avoid;break-inside:avoid;">
                <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.15);padding-bottom:10px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;background:#038047;border-radius:6px;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;color:#fff;">
                            S
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:800;letter-spacing:0.04em;text-transform:uppercase;color:#ffffff;">SMADIMENT MEDIA INTELLIGENCE</div>
                            <div style="font-size:10px;color:#94a3b8;">FULL SESSION BRIEFING REPORT</div>
                        </div>
                    </div>
                    <div style="background:#dc2626;color:#ffffff;font-size:9.5px;font-weight:800;padding:3px 8px;border-radius:4px;letter-spacing:0.06em;text-transform:uppercase;">
                        CONFIDENTIAL
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:8px;">
                    <div>
                        <h1 style="margin:0;font-size:16px;font-weight:800;color:#ffffff;">${escHtml(PLATFORM)} AI Analysis Report</h1>
                        <p style="margin:3px 0 0;font-size:10.5px;color:#cbd5e1;">Project ID: <strong style="color:#fff;">${escHtml(PROJECT_ID)}</strong> &bull; Period: <strong style="color:#fff;">${escHtml(START_DATE)} s/d ${escHtml(END_DATE)}</strong></p>
                    </div>
                    <div style="font-size:10px;color:#94a3b8;text-align:right;">
                        <div>Generated: <span style="color:#cbd5e1;">${now}</span></div>
                        <div>Classification: <span style="color:#cbd5e1;">INTERNAL USE</span></div>
                    </div>
                </div>
            </div>`;

    rows.forEach(row => {
        const role = row.dataset.role;
        const bubble = row.querySelector('.chat-bubble');
        if (!bubble) return;
        const content = bubble.innerHTML;
        const isAI = role === 'ai';

        html += `<div style="margin-bottom:14px;page-break-inside:avoid;break-inside:avoid;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;page-break-inside:avoid;break-inside:avoid;">
                <div style="width:22px;height:22px;border-radius:6px;background:${isAI?'#038047':'#64748b'};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="color:#fff;font-size:9px;font-weight:700;">${isAI?'AI':'U'}</span>
                </div>
                <span style="font-size:11px;font-weight:700;color:${isAI?'#038047':'#64748b'};">${isAI?'SMADIMENT AI':'User'}</span>
            </div>
            <div style="margin-left:28px;padding:10px 14px;background:${isAI?'#ffffff':'#f8fafc'};border:1px solid #e2e8f0;border-radius:8px;font-size:12px;line-height:1.7;color:#1e293b;">
                ${content}
            </div>
        </div>`;
    });

    html += `<div style="margin-top:20px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;font-size:9px;color:#94a3b8;page-break-inside:avoid;break-inside:avoid;">
        <span>SMADIMENT INTELLIGENCE &bull; ALL RIGHTS RESERVED</span>
        <span>${escHtml(PLATFORM)} &bull; ${now}</span>
    </div></div>`;

    const filename = `AI_Analysis_${PLATFORM}_${PROJECT_ID}_${START_DATE}_${END_DATE}.pdf`;
    _renderPDF(html, filename);
}

function _renderPDF(html, filename) {
    if (typeof html2pdf === 'undefined') {
        _fallbackPdfPrint(html, filename);
        return;
    }

    // Create a plain element — do NOT add positioning/opacity styles or append to DOM.
    // html2pdf internally creates its own overlay container (position:fixed, full-screen)
    // and moves this element into it. Adding position:absolute/opacity:0 would persist
    // inside the overlay and cause html2canvas to capture a blank area.
    const el = document.createElement('div');
    el.innerHTML = html;

    html2pdf().set({
        margin: [12, 14, 12, 14],
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, letterRendering: true, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: {
            mode: ['css', 'legacy'],
            avoid: ['.ai-num-card', '.ai-sub-card', '.ai-evidence-box', '.ai-table-wrap', '.ai-bullet-item', 'p', 'h2', 'h3', 'h4', 'tr', 'blockquote', 'li']
        }
    }).from(el).save().catch(() => {
        _fallbackPdfPrint(html, filename);
    });
}

function _fallbackPdfPrint(html, filename) {
    const win = window.open('', '_blank');
    if (!win) { alert('Popup diblokir. Izinkan popup untuk download PDF.'); return; }
    win.document.write(`<!DOCTYPE html><html><head><title>${escHtml(filename)}</title>
        <style>body{font-family:'Segoe UI',system-ui,sans-serif;padding:20px;color:#1a202c;max-width:780px;margin:0 auto;}
        @media print{body{padding:10px;}}</style></head><body>${html}
        <script>setTimeout(function(){window.print();},500);<\/script></body></html>`);
    win.document.close();
}

@endverbatim
</script>

