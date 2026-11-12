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

    const mountIframe = () => {
        cleanupPlayer();
        ensurePreconnect();
        stage.querySelectorAll('iframe').forEach(f => { f.src = ''; f.remove(); });
        stage.innerHTML = '';

        let ready = false;
        let ttRef = '';
        try { ttRef = window.top.location.href; } catch (_) { ttRef = window.location.href; }

        const baseSrc = `https://www.tiktok.com/player/v1/${encodeURIComponent(vidId)}?referrer=${encodeURIComponent(ttRef)}&embed_from=embed_video&autoplay=1&muted=1&music_info=0&description=0&ab_version=v1`;

        const ifr = document.createElement('iframe');
        ifr.name = '__tt_embed__v' + Math.random().toString(36).slice(2, 12);
        ifr.sandbox = 'allow-popups allow-popups-to-escape-sandbox allow-scripts allow-top-navigation allow-same-origin';
        ifr.setAttribute('allow', 'fullscreen; autoplay');
        ifr.allowFullscreen = true;
        ifr.loading = 'lazy';
        ifr.src = baseSrc;
        ifr.style.cssText = 'width:100%;max-width:325px;height:575px;margin:0 auto;border:none;display:block;background:#0f172a;';

        const bar = document.createElement('div');
        bar.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 12px;background:#1e293b;border-top:1px solid #334155;font-size:10px;color:#cbd5e1;text-align:left;';
        const msg = document.createElement('span');
        msg.textContent = 'Menyiapkan video TikTok...';

        const retryBtn = document.createElement('button');
        retryBtn.type = 'button';
        retryBtn.textContent = 'Coba Lagi';
        retryBtn.style.cssText = 'flex-shrink:0;background:#334155;color:#f8fafc;border:1px solid #475569;padding:4px 10px;border-radius:4px;font-size:10px;font-weight:700;cursor:pointer;';
        retryBtn.addEventListener('click', mountIframe);

        const setStatus = (text) => {
            if (stallTimer) { clearTimeout(stallTimer); stallTimer = null; }
            if (loadTimer) { clearTimeout(loadTimer); loadTimer = null; }
            msg.textContent = text;
        };

        ifr.addEventListener('load', () => {
            if (ready || loadTimer) return;
            loadTimer = setTimeout(() => {
                loadTimer = null;
                if (!ready && ifr.isConnected) {
                    setStatus(`Video belum merespons. Klik Retry / Coba Lagi atau ${fallbackLabel}.`);
                }
            }, 3500);
        });

        stallTimer = setTimeout(() => {
            stallTimer = null;
            if (!ready && ifr.isConnected) {
                setStatus(`Video sedang dibatasi sementara. Klik Retry / Coba Lagi atau ${fallbackLabel}.`);
            }
        }, 7000);

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
                setStatus(`Video belum dapat dimuat. Klik Retry / Coba Lagi atau ${fallbackLabel}.`);
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
