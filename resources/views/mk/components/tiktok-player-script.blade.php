@once
<script>
window.SMADIMENT_bindTtPlayer = function (owner, root, vidId, opts) {
    owner._ttCleanup?.();
    owner._ttCleanup = null;

    const card = root.querySelector('[data-tt-card]');
    const stage = root.querySelector('[data-tt-stage]');
    const btnPlay = root.querySelector('[data-tt-play]');
    const btnClose = root.querySelector('[data-tt-close]');
    if (!card || !stage || !btnPlay) return;

    const fallbackLabel = (opts && opts.fallbackLabel) || 'Buka di TikTok';
    const previewHtml = stage.innerHTML;
    let stallTimer = null;
    let loadTimer = null;
    let onMsg = null;

    const ensurePreconnect = () => {
        if (window.__ttPreconnectDone) return;
        window.__ttPreconnectDone = true;
        [
            'https://www.tiktok.com',
            'https://lf16-tiktok-web.tiktokcdn-us.com',
            'https://sf16-website-login.neutral.ttwstatic.com'
        ].forEach((href) => {
            if (document.head.querySelector(`link[rel="preconnect"][href="${href}"]`)) return;
            const link = document.createElement('link');
            link.rel = 'preconnect';
            link.href = href;
            link.crossOrigin = '';
            document.head.appendChild(link);
        });
    };

    const cleanupPlayer = () => {
        if (stallTimer) { clearTimeout(stallTimer); stallTimer = null; }
        if (loadTimer) { clearTimeout(loadTimer); loadTimer = null; }
        if (onMsg) { window.removeEventListener('message', onMsg); onMsg = null; }
        if (owner._ttCleanup === cleanupPlayer) owner._ttCleanup = null;
    };

    const renderFallbackCard = () => {
        cleanupPlayer();
        const directUrl = (opts && opts.directUrl) || (opts && opts.handle ? `https://www.tiktok.com/@${encodeURIComponent(opts.handle)}/video/${encodeURIComponent(vidId)}` : `https://www.tiktok.com/search?q=${encodeURIComponent(vidId)}`);
        stage.innerHTML = `
            <div style="padding:32px 20px;text-align:center;background:#0f172a;border-radius:6px;min-height:300px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                <div style="width:58px;height:58px;margin:0 auto 14px;background:#1e293b;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fe2c55;font-size:26px;border:1px solid #334155;box-shadow:0 8px 24px rgba(254,44,85,.15);">
                    <svg style="width:26px;height:26px;fill:#fe2c55;" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.82 4.49 6.27 6.27 0 0 0 1.86-4.49V8.72a8.28 8.28 0 0 0 4.84 1.54v-3.5a4.85 4.85 0 0 1-.93-.07z"/></svg>
                </div>
                <div style="font-size:13px;font-weight:700;color:#f8fafc;margin-bottom:6px;">Pemutaran Langsung Dibatasi oleh TikTok</div>
                <div style="font-size:11px;color:#94a3b8;line-height:1.55;max-width:300px;margin:0 auto 18px;">
                    Kreator atau keamanan platform TikTok membatasi pemutaran di situs eksternal. Tonton langsung di platform TikTok untuk kualitas terbaik:
                </div>
                <div style="display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;">
                    <a href="${directUrl}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;background:#fe2c55;color:#fff;padding:8px 18px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;box-shadow:0 4px 14px rgba(254,44,85,.35);">
                        <span>Tonton di TikTok</span>
                        <svg style="width:12px;height:12px;fill:currentColor;" viewBox="0 0 256 256"><path d="M224,104a8,8,0,0,1-16,0V59.32l-66.34,66.34a8,8,0,0,1-11.32-11.32L196.68,48H152a8,8,0,0,1,0-16h72a8,8,0,0,1,8,8Zm-40,80a8,8,0,0,1-8,8H48a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8h56a8,8,0,0,1,0-16H48A24,24,0,0,0,24,80V184a24,24,0,0,0,24,24H176a24,24,0,0,0,24-24V128a8,8,0,0,1-16,0Z"/></svg>
                    </a>
                    <button type="button" class="btn-tt-retry" style="background:#334155;color:#e2e8f0;padding:8px 14px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid #475569;cursor:pointer;">
                        Coba Putar Lagi
                    </button>
                </div>
            </div>
        `;
        stage.querySelector('.btn-tt-retry')?.addEventListener('click', mountIframe);
        if (btnClose) btnClose.style.display = 'inline-flex';
    };

    const mountIframe = () => {
        cleanupPlayer();
        ensurePreconnect();
        stage.querySelectorAll('iframe').forEach(f => { f.src = ''; f.remove(); });
        stage.innerHTML = '';

        let ready = false;
        let ttRef = '';
        try { ttRef = window.top.location.href; } catch (_) { ttRef = window.location.href; }

        const baseSrc = `https://www.tiktok.com/player/v1/${encodeURIComponent(vidId)}?music_info=1&description=1`;

        const ifr = document.createElement('iframe');
        ifr.name = '__tt_embed__v' + Math.random().toString(36).slice(2, 12);
        ifr.sandbox = 'allow-popups allow-popups-to-escape-sandbox allow-scripts allow-top-navigation allow-same-origin';
        ifr.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share');
        ifr.allowFullscreen = true;
        ifr.loading = 'lazy';
        ifr.src = baseSrc;
        ifr.style.cssText = 'width:100%;max-width:325px;height:578px;margin:0 auto;border:none;display:block;background:#0f172a;';

        const bar = document.createElement('div');
        bar.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 12px;background:#1e293b;border-top:1px solid #334155;font-size:10px;color:#cbd5e1;text-align:left;';
        const msg = document.createElement('span');
        msg.textContent = 'Video TikTok siap diputar.';

        const retryBtn = document.createElement('button');
        retryBtn.type = 'button';
        retryBtn.textContent = 'Buka di TikTok';
        retryBtn.style.cssText = 'flex-shrink:0;background:#fe2c55;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-size:10px;font-weight:700;cursor:pointer;';
        const directUrl = (opts && opts.directUrl) || (opts && opts.handle ? `https://www.tiktok.com/@${encodeURIComponent(opts.handle)}/video/${encodeURIComponent(vidId)}` : `https://www.tiktok.com/search?q=${encodeURIComponent(vidId)}`);
        retryBtn.addEventListener('click', () => window.open(directUrl, '_blank', 'noopener,noreferrer'));

        const setStatus = (text) => {
            if (stallTimer) { clearTimeout(stallTimer); stallTimer = null; }
            if (loadTimer) { clearTimeout(loadTimer); loadTimer = null; }
            msg.textContent = text;
        };

        ifr.addEventListener('load', () => {
            if (stallTimer) { clearTimeout(stallTimer); stallTimer = null; }
            if (loadTimer) { clearTimeout(loadTimer); loadTimer = null; }
            msg.textContent = 'Klik tombol play pada video untuk memulai.';
        });

        onMsg = (e) => {
            if (e.origin !== 'https://www.tiktok.com' || e.source !== ifr.contentWindow || !e.data) return;
            const d = typeof e.data === 'string'
                ? (() => { try { return JSON.parse(e.data); } catch (_) { return null; } })()
                : e.data;
            if (!d || !d['x-tiktok-player']) return;

            if (d.type === 'onPlayerReady') {
                ready = true;
                if (stallTimer) { clearTimeout(stallTimer); stallTimer = null; }
                if (loadTimer) { clearTimeout(loadTimer); loadTimer = null; }
                msg.textContent = 'Video siap diputar.';
            } else if (d.type === 'onPlayerError' || d.type === 'onError') {
                renderFallbackCard();
            }
        };

        window.addEventListener('message', onMsg);
        owner._ttCleanup = cleanupPlayer;
        bar.appendChild(msg);
        bar.appendChild(retryBtn);
        stage.appendChild(ifr);
        stage.appendChild(bar);
        if (btnClose) btnClose.style.display = 'inline-flex';
    };

    btnPlay.addEventListener('click', mountIframe);
    if (btnClose) {
        btnClose.addEventListener('click', () => {
            cleanupPlayer();
            stage.querySelectorAll('iframe').forEach(f => { f.src = ''; f.remove(); });
            stage.innerHTML = previewHtml;
            btnClose.style.display = 'none';
            window.SMADIMENT_bindTtPlayer(owner, root, vidId, opts);
        });
    }
};
</script>
@endonce
