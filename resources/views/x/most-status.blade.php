@extends('layouts.app')

@section('title', 'X Analytics - Most Status')

@section('styles')
<style>
  :root {
    --green:      #038047; --green-dark: #026738;
    --navy:       #273B4A; --white:      #FFFFFF;
    --surface:    #F1F5F8; --border:     #e2e8f0;
    --text-1:     #273B4A; --text-2:     #64748b; --text-3:     #94a3b8;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.08); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --radius:     12px; --radius-sm: 8px;
  }

  .x-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:28px 32px 0;flex-wrap:wrap;gap:16px;
  }

  .x-header-left { display:flex;align-items:center;gap:14px; }

  .x-icon {
    width:44px;height:44px;background:var(--navy);
    border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;
  }

  .x-icon svg { width:22px;height:22px;fill:#fff; }
  .x-header-title h2 { font-size:22px;font-weight:700;color:var(--text-1);letter-spacing:-.4px;margin:0; }
  .x-header-title p { font-size:13px;color:var(--text-2);margin:2px 0 0; }

  .filter-bar { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
  .filter-bar label { font-size:12px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.6px; }
  .filter-bar input[type="date"] {
    padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
    font-family:'Poppins',sans-serif;font-size:13px;color:var(--text-1);
    background:var(--white);outline:none;transition:border-color .2s;
  }
  .filter-bar input[type="date"]:focus { border-color:var(--green); }
  .btn-apply {
    padding:8px 20px;background:var(--green);color:#fff;border:none;
    border-radius:var(--radius-sm);font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;transition:background .2s,transform .15s;
  }
  .btn-apply:hover { background:var(--green-dark);transform:translateY(-1px); }

  .x-body { padding:24px 32px 40px; }

  /* ── Type Tabs ── */
  .type-tabs {
    display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;
  }

  .type-tab {
    padding:7px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);
    font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;
    cursor:pointer;background:var(--white);color:var(--text-2);
    transition:all .2s;white-space:nowrap;
  }

  .type-tab:hover { border-color:var(--green);color:var(--green); }
  .type-tab.active { background:var(--navy);color:#fff;border-color:var(--navy); }

  /* ── Main layout ── */
  .status-layout {
    display:grid;grid-template-columns:2fr 1fr;gap:16px;
  }

  .chart-card {
    background:var(--white);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;
  }

  .chart-head {
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 20px 14px;border-bottom:1px solid var(--border);
  }
  .chart-head h3 { font-size:14px;font-weight:700;color:var(--text-1);margin:0; }
  .chart-head span { font-size:11px;color:var(--text-3);font-weight:500; }

  /* ── Tweet Cards ── */
  .tweet-list { padding:16px;display:flex;flex-direction:column;gap:12px; }

  .tweet-card {
    border:1px solid var(--border);border-radius:var(--radius-sm);
    padding:16px;transition:box-shadow .2s,border-color .2s;
  }

  .tweet-card:hover { box-shadow:var(--shadow-md);border-color:rgba(3,128,71,.2); }

  .tweet-header {
    display:flex;align-items:center;gap:10px;margin-bottom:10px;
  }

  .tweet-avatar {
    width:36px;height:36px;border-radius:50%;
    background:var(--navy);display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:700;color:#fff;flex-shrink:0;
  }

  .tweet-user-name { font-size:13px;font-weight:700;color:var(--text-1); }
  .tweet-user-handle { font-size:11px;color:var(--text-3);margin-top:1px; }

  .tweet-text {
    font-size:13px;color:var(--text-1);line-height:1.6;
    margin-bottom:10px;
    display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;
  }

  .tweet-meta {
    display:flex;gap:16px;flex-wrap:wrap;
  }

  .tweet-metric {
    display:flex;align-items:center;gap:5px;
    font-size:11px;color:var(--text-3);font-weight:600;
  }

  .tweet-metric svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:1.8; }

  .tweet-metric span { color:var(--text-2); }

  /* ── Retweet Section ── */
  .retweet-list { padding:16px;display:flex;flex-direction:column;gap:10px;max-height:600px;overflow-y:auto; }

  .retweet-item {
    display:flex;align-items:flex-start;gap:10px;
    padding:10px;border-radius:var(--radius-sm);
    border:1px solid var(--surface);
    transition:border-color .2s;
  }

  .retweet-item:hover { border-color:var(--border); }

  .retweet-rank {
    width:22px;height:22px;border-radius:50%;
    background:var(--navy);display:flex;align-items:center;justify-content:center;
    font-size:10px;font-weight:700;color:#fff;flex-shrink:0;margin-top:2px;
  }

  .retweet-content { flex:1; }

  .retweet-name { font-size:12px;font-weight:600;color:var(--text-1); }
  .retweet-text {
    font-size:12px;color:var(--text-2);margin-top:3px;line-height:1.5;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  }

  .retweet-count {
    font-size:12px;font-weight:700;color:var(--green);white-space:nowrap;margin-top:4px;
  }

  /* ── Skeleton ── */
  .skeleton {
    background:linear-gradient(90deg,#f1f5f8 25%,#e2e8f0 50%,#f1f5f8 75%);
    background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:6px;
  }
  @keyframes shimmer { 0% { background-position:200% 0; } 100% { background-position:-200% 0; } }

  .tweet-skeleton {
    border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;
    display:flex;flex-direction:column;gap:10px;
  }

  .error-state {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    height:200px;color:var(--text-3);font-size:13px;gap:8px;
  }
  .error-state svg { width:28px;height:28px;stroke:var(--text-3); }

  @media (max-width:1100px) { .status-layout { grid-template-columns:1fr; } }
  @media (max-width:768px) { .x-header,.x-body { padding-left:20px;padding-right:20px; } }
</style>
@endsection

@section('content')

<div class="x-header">
  <div class="x-header-left">
    <div class="x-icon">
      <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
    </div>
    <div class="x-header-title">
      <h2>Most Status X</h2>
      <p>Tweet dan status terpopuler berdasarkan tipe interaksi</p>
    </div>
  </div>

  <div class="filter-bar">
    <label>Dari</label>
    <input type="date" id="startDate" value="{{ $startDate }}">
    <label>Hingga</label>
    <input type="date" id="endDate" value="{{ $endDate }}">
    <button class="btn-apply" onclick="applyFilter()">Terapkan</button>
  </div>
</div>

<div class="x-body">

  {{-- Mention Type Tabs --}}
  <div class="type-tabs">
    <button class="type-tab active" onclick="switchType('view_all', this)">Semua</button>
    <button class="type-tab" onclick="switchType('retweet', this)">Retweet</button>
    <button class="type-tab" onclick="switchType('reply', this)">Reply</button>
    <button class="type-tab" onclick="switchType('quote', this)">Quote</button>
    <button class="type-tab" onclick="switchType('original', this)">Original</button>
  </div>

  <div class="status-layout">

    {{-- Tweet List --}}
    <div class="chart-card">
      <div class="chart-head">
        <h3 id="statusCardTitle">Tweet Terpopuler</h3>
        <span id="statusCount">Memuat...</span>
      </div>
      <div id="tweetListWrap">
        <div class="tweet-list">
          @for($i = 0; $i < 3; $i++)
          <div class="tweet-skeleton">
            <div style="display:flex;gap:10px;">
              <div class="skeleton" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;"></div>
              <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
                <div class="skeleton" style="height:10px;width:40%;"></div>
                <div class="skeleton" style="height:8px;width:25%;"></div>
              </div>
            </div>
            <div class="skeleton" style="height:10px;width:90%;"></div>
            <div class="skeleton" style="height:10px;width:70%;"></div>
            <div class="skeleton" style="height:8px;width:50%;"></div>
          </div>
          @endfor
        </div>
      </div>
    </div>

    {{-- Most Retweet --}}
    <div class="chart-card">
      <div class="chart-head">
        <h3>Paling Banyak Retweet</h3>
        <span id="retweetCount">Memuat...</span>
      </div>
      <div id="retweetWrap">
        <div class="retweet-list">
          @for($i = 0; $i < 5; $i++)
          <div style="display:flex;gap:10px;padding:10px;">
            <div class="skeleton" style="width:22px;height:22px;border-radius:50%;flex-shrink:0;"></div>
            <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
              <div class="skeleton" style="height:9px;width:50%;"></div>
              <div class="skeleton" style="height:8px;width:80%;"></div>
              <div class="skeleton" style="height:8px;width:30%;"></div>
            </div>
          </div>
          @endfor
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  const PROJECT_ID  = '{{ $currentProjectId }}';
  const BASE_PARAMS = () => {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    return `project_id=${PROJECT_ID}&start_date=${s}&end_date=${e}`;
  };

  const API = {
    mostStatus : '/mk/x/api/most-status',
    retweets   : '/mk/x/api/most-retweets',
  };

  let currentType = 'view_all';

  function fmt(n) {
    if (!n && n !== 0) return '-';
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M';
    if (n >= 1_000)     return (n / 1_000).toFixed(1) + 'K';
    return Number(n).toLocaleString('id-ID');
  }

  async function fetchJson(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }

  function errorHtml(msg = 'Gagal memuat data') {
    return `<div class="error-state">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>${msg}</span></div>`;
  }

  // ── Tweet metric icons ─────────────────────────────────────
  function metricIcon(type) {
    const icons = {
      retweet: '<svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>',
      reply  : '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
      view   : '<svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
      like   : '<svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
    };
    return icons[type] || '';
  }

  // ── Status List ───────────────────────────────────────────
  async function loadMostStatus(mentionType = 'view_all') {
    const wrap = document.getElementById('tweetListWrap');
    wrap.innerHTML = `<div class="tweet-list">${
      Array(3).fill(`<div class="tweet-skeleton">
        <div style="display:flex;gap:10px;">
          <div class="skeleton" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;"></div>
          <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
            <div class="skeleton" style="height:10px;width:40%;"></div>
            <div class="skeleton" style="height:8px;width:25%;"></div>
          </div>
        </div>
        <div class="skeleton" style="height:10px;width:90%;"></div>
        <div class="skeleton" style="height:10px;width:70%;"></div>
        <div class="skeleton" style="height:8px;width:50%;"></div>
      </div>`).join('')
    }</div>`;

    try {
      const data  = await fetchJson(`${API.mostStatus}?${BASE_PARAMS()}&mention_type=${mentionType}`);
      const items = (data.data ?? data ?? []).slice(0, 10);

      document.getElementById('statusCount').textContent = items.length + ' tweet';

      if (!items.length) {
        wrap.innerHTML = errorHtml('Tidak ada data');
        return;
      }

      wrap.innerHTML = `<div class="tweet-list">${
        items.map(t => {
          const name     = t.name ?? t.user_name ?? t.screen_name ?? 'User';
          const handle   = t.screen_name ?? t.handle ?? '';
          const text     = t.text ?? t.content ?? t.tweet ?? '';
          const retweets = t.retweet_count ?? t.retweets ?? 0;
          const replies  = t.reply_count ?? t.replies ?? 0;
          const views    = t.view_count ?? t.views ?? 0;
          const likes    = t.favorite_count ?? t.likes ?? 0;
          const initials = name.slice(0, 2).toUpperCase();

          return `
            <div class="tweet-card">
              <div class="tweet-header">
                <div class="tweet-avatar">${initials}</div>
                <div>
                  <div class="tweet-user-name">${name}</div>
                  ${handle ? `<div class="tweet-user-handle">@${handle}</div>` : ''}
                </div>
              </div>
              <div class="tweet-text">${text}</div>
              <div class="tweet-meta">
                ${views    ? `<span class="tweet-metric">${metricIcon('view')}<span>${fmt(views)} tampilan</span></span>` : ''}
                ${retweets ? `<span class="tweet-metric">${metricIcon('retweet')}<span>${fmt(retweets)}</span></span>` : ''}
                ${replies  ? `<span class="tweet-metric">${metricIcon('reply')}<span>${fmt(replies)}</span></span>` : ''}
                ${likes    ? `<span class="tweet-metric">${metricIcon('like')}<span>${fmt(likes)}</span></span>` : ''}
              </div>
            </div>`;
        }).join('')
      }</div>`;

    } catch (err) {
      wrap.innerHTML = errorHtml('Gagal memuat status');
      console.error('loadMostStatus:', err);
    }
  }

  // ── Most Retweets ─────────────────────────────────────────
  async function loadMostRetweets() {
    const wrap = document.getElementById('retweetWrap');
    try {
      const data  = await fetchJson(`${API.retweets}?${BASE_PARAMS()}`);
      const items = (data.data ?? data ?? []).slice(0, 10);

      document.getElementById('retweetCount').textContent = items.length + ' tweet';

      wrap.innerHTML = `<div class="retweet-list">${
        items.map((t, i) => {
          const name  = t.name ?? t.user_name ?? t.screen_name ?? 'User';
          const text  = t.text ?? t.content ?? t.tweet ?? '';
          const count = t.retweet_count ?? t.retweets ?? t.count ?? 0;
          return `
            <div class="retweet-item">
              <div class="retweet-rank">${i + 1}</div>
              <div class="retweet-content">
                <div class="retweet-name">${name}</div>
                <div class="retweet-text">${text}</div>
                <div class="retweet-count">${fmt(count)} retweet</div>
              </div>
            </div>`;
        }).join('')
      }</div>`;

    } catch (err) {
      wrap.innerHTML = errorHtml('Gagal memuat retweet');
      console.error('loadMostRetweets:', err);
    }
  }

  // ── Switch Type ───────────────────────────────────────────
  function switchType(type, btn) {
    currentType = type;
    document.querySelectorAll('.type-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const labels = { view_all: 'Semua Tweet', retweet: 'Retweet Terbanyak', reply: 'Reply Terbanyak', quote: 'Quote Terbanyak', original: 'Tweet Original' };
    document.getElementById('statusCardTitle').textContent = labels[type] ?? 'Tweet Terpopuler';

    loadMostStatus(type);
  }

  function applyFilter() {
    if (!PROJECT_ID) { alert('Pilih project terlebih dahulu.'); return; }
    loadAll();
  }

  function loadAll() {
    loadMostStatus(currentType);
    loadMostRetweets();
  }

  document.addEventListener('DOMContentLoaded', () => { if (PROJECT_ID) loadAll(); });
</script>
@endsection