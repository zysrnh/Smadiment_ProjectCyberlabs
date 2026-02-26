@extends('mk.layouts.app')

@section('title', 'Compare Projects - SMADIMENT')

@section('styles')
<style>
  :root {
    --green: #038047;
    --green-dark: #026738;
    --green-light: #e6f5ee;
    --text: #1a202c;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --bg: #f8fafc;
    --white: #ffffff;
    --shadow: 0 1px 3px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
  }

  .cmp-container {
    padding: 24px;
    background: var(--bg);
    min-height: 100vh;
    max-width: 1600px;
    margin: 0 auto;
  }

  /* ── Page Header ── */
  .cmp-header { margin-bottom: 28px; }
  .cmp-header h1 { font-size: 26px; font-weight: 700; color: var(--text); margin: 0 0 6px; }
  .cmp-header p  { font-size: 13px; color: var(--text-muted); margin: 0; }

  /* ── Config Card ── */
  .config-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    margin-bottom: 24px;
  }

  .config-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 16px;
    align-items: end;
  }

  @media (max-width: 900px) { .config-grid { grid-template-columns: 1fr; } }

  .form-label {
    font-size: 12px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block;
  }

  /* ── Project Selector ── */
  .project-search-input {
    width: 100%; padding: 11px 16px; border: 1px solid var(--border);
    border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 13px;
    background: var(--bg); color: var(--text); outline: none; transition: all 0.2s; margin-bottom: 8px;
  }
  .project-search-input:focus {
    border-color: var(--green); background: var(--white);
    box-shadow: 0 0 0 3px rgba(3,128,71,0.1);
  }

  .project-dropdown {
    border: 1px solid var(--border); border-radius: 10px; max-height: 220px;
    overflow-y: auto; background: var(--white); box-shadow: var(--shadow-md); scrollbar-width: thin;
  }
  .project-dropdown::-webkit-scrollbar { width: 4px; }
  .project-dropdown::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

  .project-option {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.15s;
    font-size: 13px; color: var(--text);
  }
  .project-option:last-child { border-bottom: none; }
  .project-option:hover { background: var(--bg); }
  .project-option input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--green); cursor: pointer; flex-shrink: 0; }
  .project-option-info { flex: 1; min-width: 0; }
  .project-option-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .project-option-meta { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
  .project-option-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--green-light); color: var(--green); flex-shrink: 0; }

  .selected-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; min-height: 32px; }
  .selected-tag {
    display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px;
    background: var(--green); color: white; border-radius: 20px;
    font-size: 12px; font-weight: 600; animation: tagIn 0.2s ease-out;
  }
  @keyframes tagIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .selected-tag button { background: none; border: none; color: rgba(255,255,255,0.8); cursor: pointer; font-size: 14px; line-height: 1; padding: 0; display: flex; align-items: center; transition: color 0.15s; }
  .selected-tag button:hover { color: white; }

  .date-row { display: flex; align-items: center; gap: 10px; }
  .date-input {
    flex: 1; padding: 11px 14px; border: 1px solid var(--border); border-radius: 10px;
    font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--text);
    outline: none; transition: all 0.2s; background: var(--bg);
  }
  .date-input:focus { border-color: var(--green); background: var(--white); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }
  .date-sep { font-size: 12px; color: var(--text-muted); font-weight: 600; white-space: nowrap; }

  .compare-btn {
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
    color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s;
    display: flex; align-items: center; gap: 8px; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(3,128,71,0.25);
  }
  .compare-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.35); }
  .compare-btn:disabled { opacity: 0.5; cursor: not-allowed; }
  .compare-btn svg { width: 18px; height: 18px; }

  /* ── Results ── */
  #resultsSection { display: none; animation: fadeUp 0.4s ease-out; }
  @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

  .projects-row { display: grid; gap: 16px; margin-bottom: 24px; }

  .project-summary-card {
    background: var(--white); border-radius: 14px; padding: 20px;
    border: 2px solid var(--border); transition: border-color 0.2s;
    position: relative; overflow: hidden;
  }
  .project-summary-card .card-title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0 0 4px; line-height: 1.3; }
  .project-summary-card .card-meta  { font-size: 11px; color: var(--text-muted); margin-bottom: 16px; }

  .project-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
  .pstat { text-align: center; }
  .pstat-value { font-size: 22px; font-weight: 700; color: var(--text); line-height: 1; }
  .pstat-label { font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 4px; }

  .sentiment-mini { display: flex; gap: 6px; margin-top: 12px; }
  .sent-bar-wrap { flex: 1; }
  .sent-bar-label { display: flex; justify-content: space-between; font-size: 10px; font-weight: 600; margin-bottom: 4px; }
  .sent-bar { height: 5px; border-radius: 3px; background: #e2e8f0; overflow: hidden; }
  .sent-bar-fill { height: 100%; border-radius: 3px; transition: width 1s ease-out; }

  .section-card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }
  .section-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--bg); }
  .section-head h3 { font-size: 16px; font-weight: 700; color: var(--text); margin: 0 0 4px; }
  .section-sub { font-size: 12px; color: var(--text-muted); }
  .chart-wrap { position: relative; height: 300px; }

  .rank-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .rank-table th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 2px solid var(--bg); }
  .rank-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: var(--text); }
  .rank-table tbody tr:last-child td { border-bottom: none; }

  .rank-num { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; background: var(--bg); color: var(--text-muted); }
  .rank-num.gold   { background: #fef3c7; color: #b45309; }
  .rank-num.silver { background: #f1f5f9; color: #475569; }
  .rank-num.bronze { background: #fef0e7; color: #c2410c; }

  .bar-cell { min-width: 120px; }
  .bar-track { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
  .bar-fill { height: 100%; border-radius: 4px; transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1); }

  .sentiment-grid { display: grid; gap: 16px; margin-top: 4px; }
  .sentiment-project-row { display: grid; grid-template-columns: 180px 1fr; gap: 16px; align-items: center; }
  @media (max-width: 640px) { .sentiment-project-row { grid-template-columns: 1fr; } }
  .sentiment-project-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .sentiment-bars { display: flex; height: 28px; border-radius: 8px; overflow: hidden; }
  .sentiment-seg { display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; transition: width 1.2s ease-out; white-space: nowrap; overflow: hidden; }

  .chart-legend { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 12px; }
  .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-muted); }
  .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

  .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
  .empty-state svg { width: 48px; height: 48px; opacity: 0.3; margin-bottom: 16px; }
  .empty-state h3 { font-size: 16px; font-weight: 600; margin: 0 0 8px; }
  .empty-state p  { font-size: 13px; margin: 0; }

  .loading-overlay {
    position: fixed; inset: 0; background: rgba(255,255,255,0.8);
    backdrop-filter: blur(4px); z-index: 9999; display: none;
    flex-direction: column; align-items: center; justify-content: center; gap: 16px;
  }
  .loading-overlay.show { display: flex; }
  .loading-spinner { width: 48px; height: 48px; border: 4px solid rgba(3,128,71,0.2); border-top-color: var(--green); border-radius: 50%; animation: spin 0.8s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-text { font-size: 14px; font-weight: 600; color: var(--text-muted); }

  .tab-nav { display: flex; gap: 4px; background: var(--bg); border-radius: 10px; padding: 4px; }
  .tab-btn { padding: 7px 16px; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text-muted); background: transparent; transition: all 0.2s; }
  .tab-btn.active { background: var(--white); color: var(--green); box-shadow: var(--shadow); }

  .media-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 10px; }
  .media-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }

  /* ── Mentions Popup ── */
  @keyframes cmpPopIn { from{opacity:0;transform:translateY(10px) scale(.95)} to{opacity:1;transform:none} }
  #cmpMentionPopup .cph { display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0; }
  #cmpMentionPopup .cpt { font-size:13px;font-weight:700;color:#1a202c;display:flex;align-items:center;gap:8px; }
  #cmpMentionPopup .cpd { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
  #cmpMentionPopup .cpc { width:26px;height:26px;border-radius:6px;background:transparent;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:18px;line-height:1;transition:all .15s; }
  #cmpMentionPopup .cpc:hover { background:#fee2e2;color:#991b1b; }
  #cmpMentionPopup .cpm { padding:7px 16px;border-bottom:1px solid #e2e8f0;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px;flex-shrink:0; }
  #cmpMentionPopup .cpb { background:#038047;color:#fff;border-radius:10px;padding:1px 9px;font-size:11px;font-weight:800; }
  #cmpMentionPopup .cpl { overflow-y:auto;flex:1;padding:4px 0;min-height:0; }
  #cmpMentionPopup .cpl::-webkit-scrollbar { width:5px; }
  #cmpMentionPopup .cpl::-webkit-scrollbar-thumb { background:#e2e8f0;border-radius:4px; }
  #cmpMentionPopup .cpi { display:flex;gap:10px;padding:10px 16px;border-bottom:1px solid #f8fafc;transition:background .1s;cursor:pointer; }
  #cmpMentionPopup .cpi:last-child { border-bottom:none; }
  #cmpMentionPopup .cpi:hover { background:#f0fdf4; }
  #cmpMentionPopup .cpa { width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#038047,#026738);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;border:1.5px solid #e2e8f0; }
  #cmpMentionPopup .cpa img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  #cmpMentionPopup .cpbd { flex:1;min-width:0; }
  #cmpMentionPopup .cpan { font-size:12px;font-weight:700;color:#1a202c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:1px; }
  #cmpMentionPopup .cpusn { font-size:10px;color:#94a3b8;font-weight:500;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  #cmpMentionPopup .cptx { font-size:12px;color:#374151;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px; }
  #cmpMentionPopup .cprw { display:flex;align-items:center;gap:6px;font-size:10px;color:#94a3b8; }
  #cmpMentionPopup .css  { padding:1px 7px;border-radius:10px;font-size:9px;font-weight:800; }
  #cmpMentionPopup .css-p { background:#d1fae5;color:#065f46; }
  #cmpMentionPopup .css-n { background:#fee2e2;color:#991b1b; }
  #cmpMentionPopup .css-u { background:#f1f5f9;color:#374151; }
  #cmpMentionPopup .cploading { padding:40px 20px;text-align:center;color:#64748b;font-size:13px;font-weight:600;display:flex;flex-direction:column;align-items:center;gap:12px; }
  #cmpMentionPopup .cpspin { width:32px;height:32px;border:3px solid #e2e8f0;border-top-color:#038047;border-radius:50%;animation:spin .7s linear infinite; }
  #cmpMentionPopup .cpempty { padding:40px 20px;text-align:center;color:#94a3b8;font-size:13px;font-weight:600; }
  /* thumbnail di list untuk youtube */
  #cmpMentionPopup .cpthumb { width:60px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;background:#000; }

  /* ── Mention Detail Modal ── */
  .cmp-modal-backdrop {
    position:fixed;inset:0;z-index:200000;
    background:rgba(10,15,25,.78);backdrop-filter:blur(10px);
    display:none;align-items:center;justify-content:center;padding:20px;
  }
  .cmp-modal-backdrop.open { display:flex; }
  @keyframes cmpModalUp { from{opacity:0;transform:translateY(22px) scale(.97)} to{opacity:1;transform:none} }
  .cmp-modal-shell {
    position:relative;width:100%;max-width:820px;max-height:calc(100vh - 40px);
    background:#fff;border-radius:20px;
    box-shadow:0 32px 80px rgba(0,0,0,.35);
    display:flex;flex-direction:column;
    animation:cmpModalUp .22s cubic-bezier(.34,1.3,.64,1);
    overflow:hidden;font-family:'Poppins',sans-serif;
  }
  .cmp-modal-head {
    display:flex;align-items:center;gap:10px;padding:12px 18px;
    border-bottom:1px solid #e2e8f0;background:#f8fafc;flex-shrink:0;
  }
  .cmp-modal-plat-pill {
    display:inline-flex;align-items:center;padding:3px 11px;border-radius:20px;
    font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;flex-shrink:0;
  }
  .cmp-modal-title { flex:1;font-size:13px;font-weight:700;color:#1a202c;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;min-width:0; }
  .cmp-modal-ext {
    display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;
    font-size:11px;font-weight:700;color:#038047;background:rgba(3,128,71,.08);
    border:1px solid rgba(3,128,71,.2);cursor:pointer;text-decoration:none;
    flex-shrink:0;transition:all .15s;white-space:nowrap;
  }
  .cmp-modal-ext:hover { background:rgba(3,128,71,.15); }
  .cmp-modal-close {
    width:30px;height:30px;border-radius:8px;border:none;background:transparent;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:#64748b;font-size:22px;line-height:1;transition:all .15s;flex-shrink:0;
  }
  .cmp-modal-close:hover { background:#fee2e2;color:#991b1b; }
  .cmp-modal-author {
    display:flex;align-items:center;gap:12px;padding:10px 18px;
    border-bottom:1px solid #f1f5f9;background:#fff;flex-shrink:0;
  }
  .cmp-modal-ava {
    width:42px;height:42px;border-radius:50%;border:2px solid #e2e8f0;flex-shrink:0;
    background:linear-gradient(135deg,#038047,#026738);
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:700;font-size:14px;overflow:hidden;
  }
  .cmp-modal-ava img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .cmp-modal-aname { font-size:13px;font-weight:700;color:#1a202c; }
  .cmp-modal-ausn  { font-size:11px;color:#94a3b8;margin-top:1px; }
  .cmp-modal-body  { flex:1;min-height:0;overflow-y:auto; }
  .cmp-modal-body::-webkit-scrollbar { width:5px; }
  .cmp-modal-body::-webkit-scrollbar-thumb { background:#e2e8f0;border-radius:4px; }
  /* embed area */
  .cmp-embed-wrap { position:relative;width:100%;background:#000; }
  .cmp-embed-wrap iframe { width:100%;border:none;display:block; }
  .cmp-embed-loader {
    position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;
    justify-content:center;gap:10px;background:#f8fafc;color:#64748b;
    font-size:13px;font-weight:600;pointer-events:none;
  }
  .cmp-embed-loader.hidden { display:none; }
  .cmp-embed-spin { width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:#038047;border-radius:50%;animation:spin .7s linear infinite; }
  /* thumbnail hero */
  .cmp-thumb-hero { width:100%;max-height:280px;object-fit:cover;display:block;background:#000; }
  /* content text */
  .cmp-modal-content { padding:18px 20px;font-size:14px;line-height:1.75;color:#1a202c;word-break:break-word;border-bottom:1px solid #f1f5f9; }
  /* stats row */
  .cmp-stats-row { display:flex;flex-wrap:wrap;gap:0;border-bottom:1px solid #f1f5f9;flex-shrink:0; }
  .cmp-stat { flex:1;min-width:80px;padding:12px 16px;text-align:center;border-right:1px solid #f1f5f9; }
  .cmp-stat:last-child { border-right:none; }
  .cmp-stat-val { font-size:17px;font-weight:800;color:#1a202c;line-height:1; }
  .cmp-stat-lbl { font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-top:3px; }
  /* sent badge in modal */
  .cmp-modal-sent { padding:3px 12px;border-radius:20px;font-size:11px;font-weight:800; }
  .cmp-modal-sent-p { background:#d1fae5;color:#065f46; }
  .cmp-modal-sent-n { background:#fee2e2;color:#991b1b; }
  .cmp-modal-sent-u { background:#f1f5f9;color:#374151; }
  /* url bar */
  .cmp-url-bar {
    display:flex;align-items:center;gap:10px;margin:14px 20px;padding:10px 14px;
    background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;
    text-decoration:none;transition:all .15s;
  }
  .cmp-url-bar:hover { border-color:#038047; }
  .cmp-url-fav { width:16px;height:16px;border-radius:3px;flex-shrink:0; }
  .cmp-url-text { font-size:12px;font-weight:600;color:#038047;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  /* tiktok */
  .cmp-tiktok-wrap { display:flex;justify-content:center;background:#f8fafc;padding:16px;min-height:400px;align-items:flex-start;overflow-y:auto; }
</style>
@endsection

@section('content')
<div class="cmp-container">

  <!-- Page Header -->
  <div class="cmp-header">
    <h1>
      <svg viewBox="0 0 24 24" style="width:26px;height:26px;display:inline;vertical-align:-4px;margin-right:8px;stroke:var(--green);fill:none;stroke-width:2">
        <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
      </svg>
      Compare Projects
    </h1>
    <p>Select 2–10 projects and a date range to compare volume, sentiment, and author metrics side-by-side</p>
  </div>

  <!-- Config Card -->
  <div class="config-card">
    <div class="config-grid">

      <!-- Project Selector -->
      <div>
        <label class="form-label">Select Projects (min 2)</label>
        <div class="project-selector">
          <input type="text" class="project-search-input" id="projectSearch" placeholder="🔍 Search projects…" autocomplete="off">
          <div class="project-dropdown" id="projectDropdown">
            <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Loading projects…</div>
          </div>
        </div>
        <div class="selected-tags" id="selectedTags">
          <span style="font-size:12px;color:var(--text-muted);align-self:center;">No projects selected</span>
        </div>
      </div>

      <!-- Date Range -->
      <div>
        <label class="form-label">Date Range</label>
        <div class="date-row">
          <input type="date" class="date-input" id="startDate" value="{{ $startDate }}">
          <span class="date-sep">to</span>
          <input type="date" class="date-input" id="endDate" value="{{ $endDate }}">
        </div>
        <div style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap;">
          @foreach([['7d','Last 7 Days'],['30d','Last 30 Days'],['1m','This Month']] as [$key,$label])
          <button onclick="applyPreset('{{ $key }}')"
            style="padding:4px 10px;font-size:11px;font-weight:600;border:1px solid var(--border);border-radius:6px;background:var(--bg);cursor:pointer;color:var(--text-muted);font-family:'Poppins',sans-serif;transition:all 0.15s"
            onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">{{ $label }}</button>
          @endforeach
        </div>
      </div>

      <!-- Action -->
      <div>
        <label class="form-label" style="opacity:0">Action</label>
        <button class="compare-btn" id="compareBtn" onclick="runCompare()" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
          </svg>
          Compare Now
        </button>
      </div>
    </div>
  </div>

  <!-- Results -->
  <div id="resultsSection">

    <!-- Project Summary Cards -->
    <div class="projects-row" id="projectsRow"></div>

    <!-- Charts Grid -->
    <div style="display:grid;grid-template-columns:1.4fr 0.6fr;gap:24px;margin-bottom:24px;">

      <!-- Volume Trend -->
      <div class="section-card">
        <div class="section-head">
          <div>
            <h3>Volume Trend</h3>
            <div class="section-sub">Daily posting volume across selected projects</div>
          </div>
          <div class="tab-nav">
            <button class="tab-btn active" onclick="switchVolumeView('line', this)">Line</button>
            <button class="tab-btn" onclick="switchVolumeView('bar', this)">Bar</button>
          </div>
        </div>
        <div class="chart-wrap"><canvas id="volumeChart"></canvas></div>
        <div class="chart-legend" id="volumeLegend"></div>
      </div>

      <!-- Total Volume Ranking -->
      <div class="section-card">
        <div class="section-head">
          <div>
            <h3>Total Volume</h3>
            <div class="section-sub">Ranked by total posts</div>
          </div>
        </div>
        <div id="volumeRanking"></div>
      </div>
    </div>

    <!-- Media Breakdown -->
    <div class="section-card">
      <div class="section-head">
        <div>
          <h3>Volume by Media</h3>
          <div class="section-sub">Breakdown per platform per project — klik bar untuk lihat mentions</div>
        </div>
      </div>
      <div id="mediaBreakdown"></div>
    </div>

    <!-- Sentiment Section -->
    <div class="section-card">
      <div class="section-head">
        <div>
          <h3>Sentiment Comparison</h3>
          <div class="section-sub">Positive · Neutral · Negative breakdown per project</div>
        </div>
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchSentView('bar', this)">Bar</button>
          <button class="tab-btn" onclick="switchSentView('pie', this)">Donut</button>
        </div>
      </div>
      <div id="sentimentBars" style="display:block;">
        <div class="sentiment-grid" id="sentimentGrid"></div>
      </div>
      <div id="sentimentDonut" style="display:none;">
        <div style="display:grid;gap:16px;" id="sentimentDonutGrid"></div>
      </div>
    </div>

  </div>

  <!-- Empty State -->
  <div id="emptyState" class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
    </svg>
    <h3>Select projects to compare</h3>
    <p>Pick at least 2 projects from the dropdown above, set a date range, and click <strong>Compare Now</strong></p>
  </div>

</div>

<!-- Mention Detail Modal (built dynamically by JS, but backdrop declared here for SSR fallback) -->
<div id="cmpModalMount"></div>

<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-spinner"></div>
  <div class="loading-text">Fetching comparison data…</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ═══════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════
const PALETTE = [
  '#038047','#3b82f6','#f59e0b','#ef4444',
  '#8b5cf6','#10b981','#ec4899','#0ea5e9','#f97316','#6366f1',
];
const SENT_COLORS = { pos: '#10b981', net: '#64748b', neg: '#ef4444' };
const MEDIA_LABELS = { doc:'Online News', twit:'X (Twitter)', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };

// Map warna per platform (untuk popup & bar)
const MEDIA_COLORS = {
  doc: '#3b82f6', twit: '#0ea5e9', fb: '#6366f1',
  instagram: '#ec4899', youtube: '#ef4444', tiktok: '#6b7280'
};

// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════
let allProjects       = [];
let selectedIds       = new Set();
let compareData       = null;
let volumeChart       = null;
let currentVolumeType = 'line';

// Popup state
let _cmpPopup      = null;
let _cmpPopupCache = {};
let _platPicker    = null;

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  loadProjects();
  @foreach($selectedIds ?? [] as $sid)
    selectedIds.add('{{ $sid }}');
  @endforeach
  document.getElementById('projectSearch').addEventListener('input', filterDropdown);
});

// ═══════════════════════════════════════════════
// PROJECTS LOADER
// ═══════════════════════════════════════════════
async function loadProjects() {
  try {
    const res  = await fetch('/mk/api/compare/projects');
    const data = await res.json();
    allProjects = data.data || [];
    renderDropdown(allProjects);
    if (selectedIds.size > 0) { updateSelectedTags(); updateCompareBtn(); }
  } catch (e) {
    document.getElementById('projectDropdown').innerHTML =
      '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px;">Failed to load projects</div>';
  }
}

function renderDropdown(list) {
  const dd = document.getElementById('projectDropdown');
  if (!list.length) {
    dd.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">No projects found</div>';
    return;
  }
  dd.innerHTML = list.map(p => `
    <label class="project-option">
      <input type="checkbox" value="${p.id}" ${selectedIds.has(String(p.id)) ? 'checked' : ''}
             onchange="toggleProject('${p.id}', '${escHtml(p.title)}')">
      <div class="project-option-info">
        <div class="project-option-name">${escHtml(p.title)}</div>
        <div class="project-option-meta">${escHtml(p.group_name || p.project_type)}</div>
      </div>
      ${selectedIds.has(String(p.id)) ? '<span class="project-option-badge">✓</span>' : ''}
    </label>
  `).join('');
}

function filterDropdown() {
  const q = document.getElementById('projectSearch').value.toLowerCase();
  const filtered = q
    ? allProjects.filter(p => p.title.toLowerCase().includes(q) || (p.group_name||'').toLowerCase().includes(q))
    : allProjects;
  renderDropdown(filtered);
}

function toggleProject(id, title) {
  id = String(id);
  if (selectedIds.has(id)) {
    selectedIds.delete(id);
  } else {
    if (selectedIds.size >= 10) {
      alert('Maximum 10 projects can be compared at once.');
      const cb = document.querySelector(`input[value="${id}"]`);
      if (cb) cb.checked = false;
      return;
    }
    selectedIds.add(id);
  }
  updateSelectedTags(); updateCompareBtn(); filterDropdown();
}

function removeProject(id) {
  selectedIds.delete(id);
  updateSelectedTags(); updateCompareBtn(); filterDropdown();
}

function updateSelectedTags() {
  const container = document.getElementById('selectedTags');
  if (!selectedIds.size) {
    container.innerHTML = '<span style="font-size:12px;color:var(--text-muted);align-self:center;">No projects selected</span>';
    return;
  }
  container.innerHTML = [...selectedIds].map(id => {
    const p = allProjects.find(x => String(x.id) === id);
    const title = p ? truncate(p.title, 28) : 'Project #' + id;
    return `<span class="selected-tag">${escHtml(title)}<button onclick="removeProject('${id}')" title="Remove">✕</button></span>`;
  }).join('');
}

function updateCompareBtn() {
  document.getElementById('compareBtn').disabled = selectedIds.size < 2;
}

// ═══════════════════════════════════════════════
// DATE PRESETS
// ═══════════════════════════════════════════════
function applyPreset(key) {
  const today = new Date();
  let start;
  if (key === '7d')  start = new Date(today - 6 * 86400000);
  if (key === '30d') start = new Date(today - 29 * 86400000);
  if (key === '1m')  start = new Date(today.getFullYear(), today.getMonth(), 1);
  document.getElementById('startDate').value = fmtDate(start);
  document.getElementById('endDate').value   = fmtDate(today);
}

// ═══════════════════════════════════════════════
// COMPARE
// ═══════════════════════════════════════════════
async function runCompare() {
  if (selectedIds.size < 2) return;
  const startDate = document.getElementById('startDate').value;
  const endDate   = document.getElementById('endDate').value;
  if (!startDate || !endDate) { alert('Please select a date range.'); return; }

  // Reset popup cache saat compare baru dijalankan
  _cmpPopupCache = {};

  showLoading(true);
  try {
    const ids = [...selectedIds].join(',');
    const url = `/mk/api/compare/all?project_ids=${ids}&start_date=${startDate}&end_date=${endDate}`;
    const res = await fetch(url);
    compareData = await res.json();
    if (!compareData.success) throw new Error(compareData.error || 'API error');
    renderResults(compareData);
    document.getElementById('resultsSection').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
  } catch (e) {
    alert('Compare failed: ' + e.message);
    console.error(e);
  } finally {
    showLoading(false);
  }
}

// ═══════════════════════════════════════════════
// RENDER RESULTS
// ═══════════════════════════════════════════════
function renderResults(data) {
  const details   = data.project_details || {};
  const volume    = data.data?.volumetotal    || {};
  const sentiment = data.data?.sentimenttotal || {};
  const ids       = data.project_ids || [];

  renderProjectCards(ids, details, volume, sentiment);
  renderVolumeChart(ids, details, volume);
  renderVolumeRanking(ids, details, volume);
  renderMediaBreakdown(ids, details, volume);
  renderSentimentBars(ids, details, sentiment);
}

// ── Project Summary Cards ──────────────────────
function renderProjectCards(ids, details, volume, sentiment) {
  const row = document.getElementById('projectsRow');
  const cols = Math.min(ids.length <= 3 ? ids.length : Math.ceil(ids.length / 2), 4);
  row.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

  row.innerHTML = ids.map((id, i) => {
    const p     = details[id] || details[String(id)] || {};
    const color = PALETTE[i % PALETTE.length];
    const vol   = extractVolumeTotal(volume, id);
    const sent  = extractSentiment(sentiment, id);
    const { pos, neg, net } = sent;
    const total = pos + neg + net || 1;

    const byMedia = extractByMedia(volume, id);
    const mediaBadges = Object.entries(byMedia)
      .filter(([,v]) => v > 0)
      .sort((a,b) => b[1]-a[1])
      .map(([k,v]) => `<span class="media-badge">${MEDIA_LABELS[k]||k}: ${fmtNum(v)}</span>`)
      .join('');

    return `
      <div class="project-summary-card" style="border-color:${color}33">
        <div style="position:absolute;top:0;left:0;right:0;height:4px;background:${color};border-radius:14px 14px 0 0"></div>
        <div class="card-title">${escHtml(p.title || 'Project #' + id)}</div>
        <div class="card-meta">${escHtml(p.group_name || p.project_type || '')}</div>
        <div class="project-stats-grid">
          <div class="pstat">
            <div class="pstat-value" style="color:${color}">${fmtNum(vol)}</div>
            <div class="pstat-label">Volume</div>
          </div>
          <div class="pstat">
            <div class="pstat-value" style="color:#10b981">${((pos/total)*100).toFixed(1)}%</div>
            <div class="pstat-label">Positive</div>
          </div>
          <div class="pstat">
            <div class="pstat-value" style="color:#ef4444">${((neg/total)*100).toFixed(1)}%</div>
            <div class="pstat-label">Negative</div>
          </div>
        </div>
        <div class="sentiment-mini">
          ${['pos','net','neg'].map(k => {
            const val = k==='pos' ? pos : k==='net' ? net : neg;
            const pct = ((val/total)*100).toFixed(0);
            const lbl = k==='pos' ? 'Pos' : k==='net' ? 'Neu' : 'Neg';
            return `<div class="sent-bar-wrap">
              <div class="sent-bar-label">
                <span style="color:${SENT_COLORS[k]}">${lbl}</span>
                <span>${pct}%</span>
              </div>
              <div class="sent-bar">
                <div class="sent-bar-fill" style="width:0%;background:${SENT_COLORS[k]}" data-pct="${pct}"></div>
              </div>
            </div>`;
          }).join('')}
        </div>
        ${mediaBadges ? `<div class="media-badges">${mediaBadges}</div>` : ''}
      </div>
    `;
  }).join('');

  setTimeout(() => {
    document.querySelectorAll('.sent-bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Volume Chart ───────────────────────────────
function renderVolumeChart(ids, details, volume) {
  if (volumeChart) { volumeChart.destroy(); volumeChart = null; }
  const canvas = document.getElementById('volumeChart');
  const legend = document.getElementById('volumeLegend');

  const totals = ids.map(id => extractVolumeTotal(volume, id));
  const labels = ids.map(id => {
    const p = details[id] || details[String(id)] || {};
    return truncate(p.title || 'Project #' + id, 25);
  });

  if (totals.every(t => t === 0)) {
    canvas.parentElement.innerHTML = '<div class="empty-state" style="padding:40px"><p>No volume data available</p></div>';
    return;
  }

  volumeChart = new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Total Volume',
        data: totals,
        backgroundColor: ids.map((_, i) => PALETTE[i % PALETTE.length] + 'cc'),
        borderColor:     ids.map((_, i) => PALETTE[i % PALETTE.length]),
        borderWidth: 2,
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a202c', padding: 12, cornerRadius: 8,
          titleColor: '#fff', bodyColor: '#fff',
          titleFont: { size: 12, weight: '700', family: 'Poppins' },
          bodyFont: { size: 12, family: 'Poppins' },
          callbacks: { label: ctx => ' ' + fmtNum(ctx.raw) + ' posts' }
        }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 }, callback: v => fmtNum(v) } },
        x: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } } }
      }
    }
  });

  legend.innerHTML = ids.map((id, i) => {
    const p = details[id] || details[String(id)] || {};
    return `<div class="legend-item"><div class="legend-dot" style="background:${PALETTE[i%PALETTE.length]}"></div><span>${escHtml(truncate(p.title||'Project #'+id, 30))}</span></div>`;
  }).join('');
}

// ── Volume Ranking — baris bisa diklik ────────
function renderVolumeRanking(ids, details, volume) {
  const container = document.getElementById('volumeRanking');
  const startDate = document.getElementById('startDate').value;
  const endDate   = document.getElementById('endDate').value;

  const ranked = ids.map((id, i) => ({
    id, i,
    title: (details[id] || details[String(id)] || {}).title || 'Project #' + id,
    total: extractVolumeTotal(volume, id),
  })).sort((a, b) => b.total - a.total);

  const max = ranked[0]?.total || 1;
  const rankClasses = ['gold','silver','bronze'];

  container.innerHTML = `
    <div style="overflow-x:auto">
      <table class="rank-table">
        <thead><tr><th>#</th><th>Project</th><th>Total</th><th>Bar</th></tr></thead>
        <tbody>
          ${ranked.map((item, rank) => {
            const pct   = ((item.total / max) * 100).toFixed(0);
            const color = PALETTE[item.i % PALETTE.length];
            const jsTitle = item.title.replace(/\\/g, '\\\\').replace(/'/g, "\\'");
            return `
              <tr
                style="cursor:pointer;transition:background .15s"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background=''"
                onclick="_openPlatPicker(event,'${item.id}','${jsTitle}','${startDate}','${endDate}')"
                title="Klik untuk lihat mentions per platform"
              >
                <td><div class="rank-num ${rankClasses[rank]||''}">${rank+1}</div></td>
                <td>
                  <div style="font-weight:600;font-size:13px">${escHtml(truncate(item.title,35))}</div>
                  <div style="font-size:10px;color:${color};font-weight:700;margin-top:2px">●</div>
                </td>
                <td style="font-weight:700;font-size:15px">${fmtNum(item.total)}</td>
                <td class="bar-cell">
                  <div class="bar-track">
                    <div class="bar-fill" style="width:0%;background:${color}" data-pct="${pct}"></div>
                  </div>
                </td>
              </tr>`;
          }).join('')}
        </tbody>
      </table>
    </div>
    <div style="padding:8px 14px;font-size:11px;color:var(--text-muted);text-align:center;border-top:1px solid #f1f5f9;font-weight:500">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;vertical-align:-1px;margin-right:3px"><path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
      Klik baris untuk lihat mentions per platform
    </div>`;

  setTimeout(() => {
    container.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Media Breakdown — bar bisa diklik ─────────
function renderMediaBreakdown(ids, details, volume) {
  const container = document.getElementById('mediaBreakdown');
  const mediaKeys = ['doc','twit','fb','instagram','youtube','tiktok'];
  const startDate = document.getElementById('startDate').value;
  const endDate   = document.getElementById('endDate').value;

  container.innerHTML = ids.map((id, i) => {
    const p       = details[id] || details[String(id)] || {};
    const color   = PALETTE[i % PALETTE.length];
    const byMedia = extractByMedia(volume, id);
    const total   = extractVolumeTotal(volume, id) || 1;
    const jsTitle = (p.title || 'Project #' + id).replace(/\\/g, '\\\\').replace(/'/g, "\\'");

    const bars = mediaKeys.map(k => {
      const val = byMedia[k] || 0;
      if (!val) return '';
      const pct       = ((val / total) * 100).toFixed(1);
      const platColor = MEDIA_COLORS[k] || color;

      return `
        <div style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;align-items:center;font-size:11px;font-weight:600;margin-bottom:4px">
            <span style="color:var(--text-muted)">${MEDIA_LABELS[k]||k}</span>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="color:var(--text)">${fmtNum(val)} <span style="color:var(--text-muted);font-weight:500">(${pct}%)</span></span>
              <button
                type="button"
                onclick="openCmpMentionPopup('${id}','${jsTitle}','${k}','${startDate}','${endDate}',event.clientX,event.clientY);event.stopPropagation()"
                style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:6px;border:1px solid ${platColor}40;background:${platColor}12;color:${platColor};font-size:10px;font-weight:700;cursor:pointer;font-family:'Poppins',sans-serif;transition:all .15s"
                onmouseover="this.style.background='${platColor}28';this.style.borderColor='${platColor}'"
                onmouseout="this.style.background='${platColor}12';this.style.borderColor='${platColor}40'"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Lihat
              </button>
            </div>
          </div>
          <div
            class="bar-track"
            style="height:8px;cursor:pointer"
            title="Klik untuk lihat mentions ${MEDIA_LABELS[k]||k}"
            onclick="openCmpMentionPopup('${id}','${jsTitle}','${k}','${startDate}','${endDate}',event.clientX,event.clientY)"
          >
            <div class="bar-fill" style="width:0%;background:${platColor}" data-pct="${pct}"></div>
          </div>
        </div>`;
    }).join('');

    return `
      <div style="padding:16px;border:1px solid var(--border);border-radius:12px;border-top:3px solid ${color}">
        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px">${escHtml(truncate(p.title||'Project #'+id,35))}</div>
        ${bars || '<p style="font-size:12px;color:var(--text-muted)">No media data</p>'}
      </div>`;
  }).join('');

  container.style.display = 'grid';
  container.style.gridTemplateColumns = `repeat(${Math.min(ids.length, 3)}, 1fr)`;
  container.style.gap = '16px';

  setTimeout(() => {
    container.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Sentiment Bars ─────────────────────────────
function renderSentimentBars(ids, details, sentiment) {
  const grid = document.getElementById('sentimentGrid');

  grid.innerHTML = ids.map(id => {
    const p    = details[id] || details[String(id)] || {};
    const sent = extractSentiment(sentiment, id);
    const { pos, neg, net } = sent;
    const total = pos + neg + net || 1;
    const pp  = ((pos/total)*100).toFixed(1);
    const np  = ((net/total)*100).toFixed(1);
    const ngp = ((neg/total)*100).toFixed(1);

    return `
      <div class="sentiment-project-row">
        <div class="sentiment-project-name" title="${escHtml(p.title||'Project #'+id)}">
          ${escHtml(truncate(p.title||'Project #'+id, 22))}
          <div style="font-size:10px;color:var(--text-muted);font-weight:500;margin-top:2px">${fmtNum(pos+neg+net)} total</div>
        </div>
        <div>
          <div class="sentiment-bars">
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.pos}" data-pct="${pp}" title="Positive: ${pp}%">${pp > 10 ? pp+'%' : ''}</div>
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.net}" data-pct="${np}" title="Neutral: ${np}%">${np > 10 ? np+'%' : ''}</div>
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.neg}" data-pct="${ngp}" title="Negative: ${ngp}%">${ngp > 10 ? ngp+'%' : ''}</div>
          </div>
          <div style="display:flex;gap:16px;margin-top:4px">
            <span style="font-size:10px;color:${SENT_COLORS.pos};font-weight:700">● Pos ${pp}%</span>
            <span style="font-size:10px;color:${SENT_COLORS.net};font-weight:700">● Neu ${np}%</span>
            <span style="font-size:10px;color:${SENT_COLORS.neg};font-weight:700">● Neg ${ngp}%</span>
          </div>
        </div>
      </div>`;
  }).join('');

  setTimeout(() => {
    document.querySelectorAll('.sentiment-seg').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ═══════════════════════════════════════════════
// VIEW SWITCHES
// ═══════════════════════════════════════════════
function switchVolumeView(type, btn) {
  currentVolumeType = type;
  btn.closest('.tab-nav').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  if (compareData) {
    const { project_ids: ids, project_details: details, data } = compareData;
    renderVolumeChart(ids, details, data.volumetotal || {});
  }
}

function switchSentView(type, btn) {
  document.getElementById('sentimentBars').style.display  = type === 'bar' ? 'block' : 'none';
  document.getElementById('sentimentDonut').style.display = type === 'pie' ? 'block' : 'none';
  btn.closest('.tab-nav').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

// ═══════════════════════════════════════════════
// DATA EXTRACTION
// ═══════════════════════════════════════════════
function getProjectNode(data, projectId) {
  if (!data || typeof data !== 'object') return null;
  return data[String(projectId)] ?? data[parseInt(projectId)] ?? null;
}

function extractVolumeTotal(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node) return 0;
  if (node.volume_total?.all?.total !== undefined) return int(node.volume_total.all.total);
  if (typeof node === 'number') return node;
  return 0;
}

function extractByMedia(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node?.volume_total?.bymedia) return {};
  const bm = node.volume_total.bymedia;
  const result = {};
  for (const [k, v] of Object.entries(bm)) result[k] = int(v);
  return result;
}

function extractSentiment(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node) return { pos: 0, neg: 0, net: 0 };
  const s = node.sentiment_total || node;
  return {
    pos: int(s.pos ?? s.positive ?? 0),
    neg: int(s.neg ?? s.negative ?? 0),
    net: int(s.net ?? s.neutral  ?? 0),
  };
}

// ═══════════════════════════════════════════════
// UTIL
// ═══════════════════════════════════════════════
function showLoading(show) { document.getElementById('loadingOverlay').classList.toggle('show', show); }
function fmtNum(n) { return new Intl.NumberFormat('en-US').format(n || 0); }
function fmtDate(d) { return d.toISOString().split('T')[0]; }
function int(v) { return parseInt(v, 10) || 0; }
function truncate(s, n) { return s && s.length > n ? s.slice(0, n) + '…' : (s || ''); }
function escHtml(s) { return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// ═══════════════════════════════════════════════
// ════════════════════════════════════════════════
// MENTIONS POPUP
// ════════════════════════════════════════════════
// ═══════════════════════════════════════════════

// ── Build popup DOM (sekali saja) ─────────────
function _buildCmpPopup() {
  if (_cmpPopup) return;
  _cmpPopup = document.createElement('div');
  _cmpPopup.id = 'cmpMentionPopup';
  _cmpPopup.style.cssText = `
    position:fixed;z-index:99999;
    background:#fff;border:1px solid #e2e8f0;border-radius:14px;
    box-shadow:0 24px 64px rgba(0,0,0,.2),0 4px 16px rgba(0,0,0,.08);
    width:400px;height:540px;display:none;flex-direction:column;
    overflow:hidden;pointer-events:auto;
    animation:cmpPopIn .18s cubic-bezier(.34,1.56,.64,1);
    font-family:'Poppins',sans-serif;
  `;
  _cmpPopup.innerHTML = `
    <div class="cph">
      <div class="cpt"><div class="cpd" id="cmpPopDot"></div><span id="cmpPopTitle">Mentions</span></div>
      <button class="cpc" onclick="closeCmpPopup()">×</button>
    </div>
    <div class="cpm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span id="cmpPopMeta">—</span>
      <span class="cpb" id="cmpPopCount">…</span>
      <span>mentions</span>
    </div>
    <div class="cpl" id="cmpPopList"></div>
  `;
  document.body.appendChild(_cmpPopup);

  // Tutup kalau klik di luar
  document.addEventListener('mousedown', function(e) {
    if (_cmpPopup && _cmpPopup.style.display === 'flex' && !_cmpPopup.contains(e.target)) {
      closeCmpPopup();
    }
  }, false);
}

// ── Posisi popup ──────────────────────────────
function _positionCmpPopup(x, y) {
  const pw = 400, ph = 540, vw = window.innerWidth, vh = window.innerHeight;
  let left = x + 16, top = y - 40;
  if (left + pw > vw - 12) left = x - pw - 16;
  if (top + ph > vh - 12) top = vh - ph - 12;
  if (top < 8) top = 8;
  if (left < 8) left = 8;
  _cmpPopup.style.left = left + 'px';
  _cmpPopup.style.top  = top + 'px';
}

// ── Buka popup ────────────────────────────────
async function openCmpMentionPopup(projectId, projectTitle, platform, startDate, endDate, x, y) {
  _buildCmpPopup();

  const color     = MEDIA_COLORS[platform] || '#038047';
  const platLabel = MEDIA_LABELS[platform] || platform;

  document.getElementById('cmpPopDot').style.background = color;
  document.getElementById('cmpPopTitle').textContent    = truncate(projectTitle, 28) + ' · ' + platLabel;
  document.getElementById('cmpPopMeta').textContent     = startDate + ' – ' + endDate;
  document.getElementById('cmpPopCount').textContent    = '…';

  const list = document.getElementById('cmpPopList');
  list.innerHTML = `<div class="cploading"><div class="cpspin"></div>Memuat mentions…</div>`;

  _cmpPopup.style.display = 'flex';
  _positionCmpPopup(x, y);

  const cacheKey = `${projectId}_${platform}_${startDate}_${endDate}`;

  try {
    let items = _cmpPopupCache[cacheKey];
    if (!items) {
      items = await _fetchCmpMentions(projectId, platform, startDate, endDate);
      _cmpPopupCache[cacheKey] = items;
    }
    document.getElementById('cmpPopCount').textContent = fmtNum(items.length);
    _renderCmpList(list, items, platform);
  } catch (e) {
    list.innerHTML = `<div class="cpempty">❌ Gagal memuat data<br><small style="font-size:11px;color:#94a3b8;margin-top:4px;display:block">${escHtml(e.message)}</small></div>`;
    document.getElementById('cmpPopCount').textContent = '0';
  }
}

function closeCmpPopup() {
  if (_cmpPopup) _cmpPopup.style.display = 'none';
}

// ── Fetch mentions dari API ───────────────────
async function _fetchCmpMentions(projectId, platform, startDate, endDate) {
  const base = '/mk/api/news';
  const q    = `project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&rows=500&start=0`;

  const endpointMap = {
    doc:       `${base}/mentions?${q}`,
    twit:      `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
    fb:        `${base}/fb-top-status?${q}&sub=fblike`,
    instagram: `${base}/ig-top-status?${q}&sub=postbylike`,
    youtube:   `${base}/ytb-top-status?${q}`,
    tiktok:    `${base}/tiktok-top-status?${q}&sub=postbylike`,
  };

  const url = endpointMap[platform];
  if (!url) throw new Error('Platform tidak dikenali: ' + platform);

  const ctrl = new AbortController();
  const tid  = setTimeout(() => ctrl.abort(), 30000);
  const res  = await fetch(url, { signal: ctrl.signal });
  clearTimeout(tid);

  if (!res.ok) throw new Error('HTTP ' + res.status);
  const data = await res.json();

  let items = [];
  if (data.success === true && Array.isArray(data.data)) items = data.data;
  else if (Array.isArray(data.data)) items = data.data;
  else if (Array.isArray(data)) items = data;

  // Filter doc-only dari mentions campuran
  if (platform === 'doc') {
    items = items.filter(m => {
      const tc = String(m.tcode || '').toLowerCase();
      const mt = String(m.media_type || '').toLowerCase();
      return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article';
    });
  }

  return items;
}

// ════════════════════════════════════════════════
// ITEM DATA HELPERS
// ════════════════════════════════════════════════

function _cmpName(item, platform) {
  const h = item.author_scr_name || item.screen_name || item.author_id || item.username || '';
  return platform === 'doc'
    ? (item.author_name || item.publisher || item.source_name || item.hostname || h || 'Unknown')
    : (item.author_name || item.channel_name || h || 'Unknown');
}

function _cmpUsername(item, platform) {
  // return @handle or channel ID depending on platform
  const raw =
    item.author_scr_name || item.screen_name || item.username ||
    item.author_id       || item.channel_id  || '';
  if (!raw) return '';
  if (platform === 'twit') return raw.startsWith('@') ? raw : '@' + raw;
  if (platform === 'youtube') return item.channel_name ? '' : raw; // don't show if already in name
  if (platform === 'instagram') return raw.startsWith('@') ? raw : '@' + raw;
  if (platform === 'tiktok') return raw.startsWith('@') ? raw : '@' + raw;
  return '';
}

function _cmpAvatar(item, platform) {
  // returns best available avatar URL
  return item.avatar_url   || item.profile_image_url ||
         item.profile_img  || item.author_image      ||
         item.channel_image|| item.thumbnail_url     || '';
}

function _cmpThumbnail(item, platform) {
  // video thumbnail (YouTube, TikTok)
  if (platform === 'youtube') {
    const vid = _ytbVideoId(item);
    if (vid) return `https://img.youtube.com/vi/${vid}/mqdefault.jpg`;
    return item.thumbnail || item.thumbnail_url || '';
  }
  if (platform === 'tiktok') return item.video_cover || item.thumbnail_url || item.cover || '';
  return '';
}

function _cmpUrl(item, platform) {
  const raw = item.url || item.link || item.post_url || item.video_url || item.permalink || '';
  if (raw) return raw;
  if (platform === 'twit') {
    const usn = item.author_scr_name || item.screen_name || '';
    const id  = item.post_id || item.tweet_id || item.id_str || item.id || '';
    if (usn && id) return `https://x.com/${usn}/status/${id}`;
  }
  if (platform === 'youtube') {
    const vid = _ytbVideoId(item);
    if (vid) return `https://www.youtube.com/watch?v=${vid}`;
  }
  if (platform === 'instagram') {
    const sc = item.shortcode || item.post_shortcode || '';
    if (sc) return `https://www.instagram.com/p/${sc}/`;
  }
  if (platform === 'tiktok') {
    const uid = item.author_id || item.user_id || '';
    const vid = item.video_id  || item.post_id || '';
    if (uid && vid) return `https://www.tiktok.com/@${uid}/video/${vid}`;
  }
  if (platform === 'fb') {
    const pid = item.post_id || item.id || '';
    if (pid) return `https://www.facebook.com/${pid}`;
  }
  return '';
}

function _ytbVideoId(item) {
  const u = item.url || item.link || item.video_url || '';
  const m = u.match(/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (m) return m[1];
  return item.video_id || item.youtube_id || '';
}

function _cmpContent(item) {
  return ((item.content || item.caption || item.description || item.name || item.title || item.text || '')
    .replace(/<[^>]*>/g, '').trim());
}

function _cmpIni(name) {
  if (!name || name === 'Unknown') return '?';
  const p = name.trim().split(/\s+/);
  return p.length === 1 ? p[0].slice(0,2).toUpperCase() : (p[0][0]+p[p.length-1][0]).toUpperCase();
}

function _cmpSent(v) {
  const s = String(v).toLowerCase().trim();
  if (s==='1'||s==='positive'||s==='positif') return '1';
  if (s==='-1'||s==='2'||s==='negative'||s==='negatif') return '-1';
  return '0';
}

function _cmpDate(str) {
  if (!str) return '';
  try { return new Date(str).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}); }
  catch(e) { return str.split('T')[0]; }
}

function _cmpEng(item, platform) {
  const fmt = n => (n > 0) ? fmtNum(n) : null;
  const parts = [];
  if (platform === 'twit') {
    const rt  = fmt(item.num_retweeted || item.retweet_count || 0);
    const lk  = fmt(item.num_likes || item.likes || item.favorite_count || 0);
    const rep = fmt(item.reply_count || item.num_replies || 0);
    if (rt)  parts.push('🔁 ' + rt);
    if (lk)  parts.push('❤ ' + lk);
    if (rep) parts.push('💬 ' + rep);
  } else if (platform === 'youtube') {
    const v   = fmt(item.num_views || item.views || item.view_count || 0);
    const lk  = fmt(item.num_likes || item.likes || item.like_count || 0);
    const com = fmt(item.num_comments || item.comment_count || 0);
    if (v)   parts.push('👁 ' + v);
    if (lk)  parts.push('👍 ' + lk);
    if (com) parts.push('💬 ' + com);
  } else if (platform === 'tiktok') {
    const v   = fmt(item.views || item.num_views || item.play_count || 0);
    const lk  = fmt(item.likes || item.num_likes || item.digg_count || 0);
    const sh  = fmt(item.shares || item.share_count || 0);
    const com = fmt(item.num_comments || item.comment_count || 0);
    if (v)   parts.push('👁 ' + v);
    if (lk)  parts.push('❤ ' + lk);
    if (sh)  parts.push('📤 ' + sh);
    if (com) parts.push('💬 ' + com);
  } else if (platform === 'instagram') {
    const lk  = fmt(item.num_likes || item.likes || item.like_count || 0);
    const com = fmt(item.num_comments || item.comment_count || 0);
    if (lk)  parts.push('❤ ' + lk);
    if (com) parts.push('💬 ' + com);
  } else if (platform === 'fb') {
    const lk  = fmt(item.likes || item.num_likes || item.like_count || 0);
    const sh  = fmt(item.shares || item.share_count || 0);
    const com = fmt(item.num_comments || item.comment_count || 0);
    if (lk)  parts.push('👍 ' + lk);
    if (sh)  parts.push('📤 ' + sh);
    if (com) parts.push('💬 ' + com);
  }
  return parts.join('  ');
}

// ════════════════════════════════════════════════
// RENDER LIST (popup compact)
// ════════════════════════════════════════════════
// Store current platform for modal access
let _cmpCurrentPlatform = '';
let _cmpCurrentItems    = [];

function _renderCmpList(list, items, platform) {
  _cmpCurrentPlatform = platform;
  _cmpCurrentItems    = items;

  if (!items.length) {
    list.innerHTML = `<div class="cpempty">📭 Tidak ada mentions untuk periode ini.</div>`;
    return;
  }

  const SHOW = 60;

  let html = items.slice(0, SHOW).map((item, idx) => {
    const name    = _cmpName(item, platform);
    const usn     = _cmpUsername(item, platform);
    const text    = _cmpContent(item).slice(0, 180);
    const ini     = _cmpIni(name);
    const sent    = _cmpSent(item.class_sentiment || item.sentiment || '0');
    const sntCls  = sent === '1' ? 'css-p' : sent === '-1' ? 'css-n' : 'css-u';
    const sntLbl  = sent === '1' ? 'Pos'   : sent === '-1' ? 'Neg'   : 'Neu';
    const dt      = _cmpDate(item.date_created || '');
    const eng     = _cmpEng(item, platform);
    const safeIni = ini.replace(/['"]/g, '');

    // Avatar
    const av = _cmpAvatar(item, platform);
    let avaHtml = ini;
    if (av && av.startsWith('http')) {
      avaHtml = `<img src="${escHtml(av)}" onerror="this.parentElement.textContent='${safeIni}'">`;
    }

    // YouTube thumbnail in list
    const thumb = _cmpThumbnail(item, platform);
    const thumbHtml = (platform === 'youtube' && thumb)
      ? `<div class="cpthumb" style="width:60px;height:40px;border-radius:6px;overflow:hidden;flex-shrink:0;border:1px solid #e2e8f0;background:#000">
           <img src="${escHtml(thumb)}" style="width:100%;height:100%;object-fit:cover" onerror="this.parentElement.style.display='none'">
         </div>`
      : '';

    return `<div class="cpi" onclick="_openCmpModal(${idx})" style="align-items:${platform==='youtube'?'flex-start':'center'}">
      <div class="cpa">${avaHtml}</div>
      ${thumbHtml}
      <div class="cpbd">
        <div class="cpan">${escHtml(name)}</div>
        ${usn ? `<div class="cpusn">${escHtml(usn)}</div>` : ''}
        <div class="cptx">${escHtml(text || '(tidak ada konten)')}</div>
        <div class="cprw">
          <span class="css ${sntCls}">${sntLbl}</span>
          ${eng ? `<span style="color:#64748b">${eng}</span>` : ''}
          ${dt ? `<span style="margin-left:auto">${dt}</span>` : ''}
        </div>
      </div>
    </div>`;
  }).join('');

  if (items.length > SHOW) {
    html += `<div style="padding:9px 16px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:#f8fafc;border-top:1px dashed #e2e8f0;">
      +${fmtNum(items.length - SHOW)} mentions lainnya
    </div>`;
  }

  list.innerHTML = html;
}

// ════════════════════════════════════════════════
// MENTION DETAIL MODAL
// ════════════════════════════════════════════════
let _cmpModalEl = null;

function _buildCmpModal() {
  if (_cmpModalEl) return;
  _cmpModalEl = document.createElement('div');
  _cmpModalEl.className = 'cmp-modal-backdrop';
  _cmpModalEl.innerHTML = `
    <div class="cmp-modal-shell" onclick="event.stopPropagation()">
      <!-- Header -->
      <div class="cmp-modal-head">
        <span class="cmp-modal-plat-pill" id="cmpMdPill"></span>
        <span class="cmp-modal-title" id="cmpMdTitle"></span>
        <a class="cmp-modal-ext" id="cmpMdExt" href="#" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Buka Link
        </a>
        <button class="cmp-modal-close" onclick="_closeCmpModal()">×</button>
      </div>
      <!-- Author row -->
      <div class="cmp-modal-author">
        <div class="cmp-modal-ava" id="cmpMdAva"></div>
        <div>
          <div class="cmp-modal-aname" id="cmpMdAname"></div>
          <div class="cmp-modal-ausn"  id="cmpMdAusn"></div>
        </div>
        <div style="margin-left:auto;display:flex;align-items:center;gap:8px">
          <span class="cmp-modal-sent" id="cmpMdSent"></span>
          <span style="font-size:11px;color:#94a3b8" id="cmpMdDate"></span>
        </div>
      </div>
      <!-- Scrollable body -->
      <div class="cmp-modal-body">
        <!-- Embed / thumbnail / content injected here -->
        <div id="cmpMdEmbed"></div>
        <!-- Stats -->
        <div class="cmp-stats-row" id="cmpMdStats"></div>
        <!-- Text -->
        <div class="cmp-modal-content" id="cmpMdText"></div>
      </div>
    </div>`;
  document.body.appendChild(_cmpModalEl);
  // Close on backdrop click
  _cmpModalEl.addEventListener('click', _closeCmpModal);
  // ESC key
  document.addEventListener('keydown', e => { if (e.key === 'Escape') _closeCmpModal(); });
}

function _closeCmpModal() {
  if (_cmpModalEl) {
    _cmpModalEl.classList.remove('open');
    // Destroy any iframe to stop video
    const emb = document.getElementById('cmpMdEmbed');
    if (emb) emb.innerHTML = '';
  }
}

const PLAT_PILL_STYLE = {
  doc:       'background:#dbeafe;color:#1d4ed8',
  twit:      'background:#e0f2fe;color:#0369a1',
  fb:        'background:#ede9fe;color:#5b21b6',
  instagram: 'background:#fce7f3;color:#9d174d',
  youtube:   'background:#fee2e2;color:#b91c1c',
  tiktok:    'background:#f1f5f9;color:#334155',
};

function _openCmpModal(idx) {
  _buildCmpModal();
  const item     = _cmpCurrentItems[idx];
  const platform = _cmpCurrentPlatform;
  if (!item) return;

  const name    = _cmpName(item, platform);
  const usn     = _cmpUsername(item, platform);
  const av      = _cmpAvatar(item, platform);
  const ini     = _cmpIni(name);
  const safeIni = ini.replace(/['"]/g, '');
  const text    = _cmpContent(item);
  const sent    = _cmpSent(item.class_sentiment || item.sentiment || '0');
  const url     = _cmpUrl(item, platform);
  const dt      = _cmpDate(item.date_created || '');
  const platLabel = MEDIA_LABELS[platform] || platform;

  // ── Pill ──
  const pill = document.getElementById('cmpMdPill');
  pill.textContent = platLabel;
  pill.style.cssText = `display:inline-flex;align-items:center;padding:3px 11px;border-radius:20px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;flex-shrink:0;${PLAT_PILL_STYLE[platform]||'background:#f1f5f9;color:#374151'}`;

  // ── Title = first 60 chars of content ──
  document.getElementById('cmpMdTitle').textContent = truncate(text || name, 60);

  // ── Ext link ──
  const extBtn = document.getElementById('cmpMdExt');
  if (url) { extBtn.href = url; extBtn.style.display = 'inline-flex'; }
  else     { extBtn.style.display = 'none'; }

  // ── Author ──
  const avaEl = document.getElementById('cmpMdAva');
  if (av && av.startsWith('http')) {
    avaEl.innerHTML = `<img src="${escHtml(av)}" onerror="this.parentElement.textContent='${safeIni}'">`;
  } else {
    avaEl.textContent = ini;
  }
  document.getElementById('cmpMdAname').textContent = name;
  document.getElementById('cmpMdAusn').textContent  = usn || '';

  // ── Sentiment ──
  const sentEl = document.getElementById('cmpMdSent');
  const sentMap = { '1': ['Positive ●','cmp-modal-sent-p'], '-1': ['Negative ●','cmp-modal-sent-n'], '0': ['Neutral ●','cmp-modal-sent-u'] };
  const [sentTxt, sentCls] = sentMap[sent] || sentMap['0'];
  sentEl.textContent  = sentTxt;
  sentEl.className    = 'cmp-modal-sent ' + sentCls;

  // ── Date ──
  document.getElementById('cmpMdDate').textContent = dt;

  // ── Embed / thumbnail ──
  const embedEl = document.getElementById('cmpMdEmbed');
  embedEl.innerHTML = _buildEmbed(item, platform, url);

  // ── Stats ──
  document.getElementById('cmpMdStats').innerHTML = _buildStats(item, platform);

  // ── Text ──
  document.getElementById('cmpMdText').textContent = text || '(tidak ada konten)';

  // Show modal
  _cmpModalEl.classList.add('open');
}

function _buildEmbed(item, platform, url) {
  // ── YouTube — embed video ──
  if (platform === 'youtube') {
    const vid = _ytbVideoId(item);
    if (vid) {
      return `<div style="position:relative;padding-bottom:56.25%;height:0;background:#000">
        <iframe src="https://www.youtube.com/embed/${vid}?autoplay=0&rel=0"
          style="position:absolute;inset:0;width:100%;height:100%;border:none"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen loading="lazy"></iframe>
      </div>`;
    }
    // fallback thumbnail
    const th = _cmpThumbnail(item, platform);
    if (th) return `<img src="${escHtml(th)}" class="cmp-thumb-hero" onerror="this.style.display='none'">`;
  }

  // ── Twitter/X — embed tweet ──
  if (platform === 'twit' && url && url.includes('x.com')) {
    const tweetUrl = url.replace('x.com','twitter.com');
    return `<div class="cmp-embed-wrap" style="min-height:200px;background:#f8fafc;padding:16px;display:flex;align-items:flex-start;justify-content:center;overflow-y:auto;max-height:420px">
      <div class="cmp-embed-loader" id="twitterLoader">
        <div class="cmp-embed-spin"></div><span>Memuat tweet…</span>
      </div>
      <div id="twitterEmbedTarget" style="width:100%;max-width:550px"></div>
    </div>
    <script>
    (function(){
      var t = document.getElementById('twitterEmbedTarget');
      var l = document.getElementById('twitterLoader');
      function doEmbed(){
        if(window.twttr && window.twttr.widgets){
          window.twttr.widgets.createTweet('${url.split('/status/')[1]?.split('?')[0]||''}','${item.author_scr_name||item.screen_name||''}',t,{theme:'light',dnt:true})
            .then(function(){ if(l) l.style.display='none'; })
            .catch(function(){ if(l) l.innerHTML='<span style="font-size:12px;color:#94a3b8">Embed tidak tersedia</span>'; });
        }
      }
      if(!window.twttr){ var s=document.createElement('script'); s.src='https://platform.twitter.com/widgets.js'; s.onload=doEmbed; document.head.appendChild(s); }
      else doEmbed();
    })();
    <\/script>`;
  }

  // ── TikTok — embed ──
  if (platform === 'tiktok') {
    const vid = item.video_id || item.post_id || '';
    const th  = _cmpThumbnail(item, platform);
    if (vid) {
      return `<div style="display:flex;justify-content:center;background:#f8fafc;padding:12px;max-height:500px;overflow-y:auto">
        <blockquote class="tiktok-embed" cite="${escHtml(url)}"
          data-video-id="${escHtml(vid)}" style="max-width:325px;min-width:280px">
          <section></section>
        </blockquote>
        <script async src="https://www.tiktok.com/embed.js"><\/script>
      </div>`;
    }
    if (th) return `<img src="${escHtml(th)}" class="cmp-thumb-hero" onerror="this.style.display='none'">`;
  }

  // ── Instagram — thumbnail (embed blocked by Meta) ──
  if (platform === 'instagram') {
    const img = item.image_url || item.media_url || item.thumbnail_url || '';
    if (img && img.startsWith('http')) {
      return `<img src="${escHtml(img)}" class="cmp-thumb-hero" onerror="this.style.display='none'">`;
    }
    return `<div style="padding:20px 18px;background:#fdf4ff;border-bottom:1px solid #e2e8f0">
      <div style="font-size:12px;font-weight:600;color:#6b21a8">📸 Instagram — preview tidak tersedia karena pembatasan Meta. Klik "Buka Link" untuk lihat postingan asli.</div>
    </div>`;
  }

  // ── Facebook — thumbnail ──
  if (platform === 'fb') {
    const img = item.image_url || item.full_picture || item.thumbnail_url || '';
    if (img && img.startsWith('http')) {
      return `<img src="${escHtml(img)}" class="cmp-thumb-hero" onerror="this.style.display='none'">`;
    }
  }

  // ── Online News — gambar artikel ──
  if (platform === 'doc') {
    const img = item.image_url || item.image || item.thumbnail || '';
    if (img && img.startsWith('http')) {
      return `<img src="${escHtml(img)}" class="cmp-thumb-hero" onerror="this.style.display='none'">`;
    }
  }

  return '';
}

function _buildStats(item, platform) {
  const stat = (lbl, val) => {
    const n = parseInt(val, 10) || 0;
    if (!n) return '';
    return `<div class="cmp-stat"><div class="cmp-stat-val">${fmtNum(n)}</div><div class="cmp-stat-lbl">${lbl}</div></div>`;
  };

  if (platform === 'twit') return [
    stat('Retweet',  item.num_retweeted || item.retweet_count),
    stat('Likes',    item.num_likes     || item.likes || item.favorite_count),
    stat('Replies',  item.reply_count   || item.num_replies),
    stat('Impressi', item.impressions   || item.impression_count),
  ].join('');

  if (platform === 'youtube') return [
    stat('Views',    item.num_views  || item.views   || item.view_count),
    stat('Likes',    item.num_likes  || item.likes   || item.like_count),
    stat('Komentar', item.num_comments|| item.comment_count),
    stat('Subscriber', item.subscriber_count || item.subscribers),
  ].join('');

  if (platform === 'tiktok') return [
    stat('Views',    item.views     || item.num_views || item.play_count),
    stat('Likes',    item.likes     || item.num_likes || item.digg_count),
    stat('Share',    item.shares    || item.share_count),
    stat('Komentar', item.num_comments || item.comment_count),
  ].join('');

  if (platform === 'instagram') return [
    stat('Likes',    item.num_likes  || item.likes  || item.like_count),
    stat('Komentar', item.num_comments || item.comment_count),
    stat('Views',    item.num_views  || item.views),
    stat('Followers', item.followers || item.follower_count),
  ].join('');

  if (platform === 'fb') return [
    stat('Likes',    item.likes    || item.num_likes || item.like_count),
    stat('Shares',   item.shares   || item.share_count),
    stat('Komentar', item.num_comments || item.comment_count),
    stat('Views',    item.num_views || item.views),
  ].join('');

  if (platform === 'doc') return [
    stat('Est. Reach', item.est_reach || item.reach),
    stat('Followers',  item.followers),
    stat('AdValue',    item.ad_value  || item.advalue),
  ].join('');

  return '';
}

// ── Platform Picker (klik row ranking) ────────
function _openPlatPicker(event, projectId, projectTitle, startDate, endDate) {
  event.stopPropagation();
  if (_platPicker) { _platPicker.remove(); _platPicker = null; }

  const PLATS = [
    { key:'doc',       label:'Online News', icon:'📰' },
    { key:'twit',      label:'Twitter/X',   icon:'🐦' },
    { key:'fb',        label:'Facebook',    icon:'📘' },
    { key:'instagram', label:'Instagram',   icon:'📸' },
    { key:'youtube',   label:'YouTube',     icon:'▶️' },
    { key:'tiktok',    label:'TikTok',      icon:'🎵' },
  ];

  _platPicker = document.createElement('div');
  _platPicker.style.cssText = `
    position:fixed;z-index:999999;
    background:#fff;border:1px solid #e2e8f0;border-radius:12px;
    box-shadow:0 16px 40px rgba(0,0,0,.18),0 4px 12px rgba(0,0,0,.08);
    padding:6px;min-width:168px;
    animation:cmpPopIn .15s ease-out;font-family:'Poppins',sans-serif;
  `;

  const hdr = document.createElement('div');
  hdr.style.cssText = 'padding:5px 10px 8px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #f1f5f9;margin-bottom:4px;';
  hdr.textContent = 'Pilih Platform';
  _platPicker.appendChild(hdr);

  PLATS.forEach(p => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.style.cssText = "display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:transparent;border:none;font-family:'Poppins',sans-serif;width:100%;text-align:left;color:#374151;transition:background .12s;";
    btn.innerHTML = `<span style="font-size:14px">${p.icon}</span><span>${p.label}</span><span style="margin-left:auto;width:9px;height:9px;border-radius:50%;background:${MEDIA_COLORS[p.key]||'#94a3b8'};flex-shrink:0;display:inline-block"></span>`;
    btn.onmouseover = () => btn.style.background = '#f8fafc';
    btn.onmouseout  = () => btn.style.background = 'transparent';
    btn.onclick = (e) => {
      e.stopPropagation();
      if (_platPicker) { _platPicker.remove(); _platPicker = null; }
      openCmpMentionPopup(projectId, projectTitle, p.key, startDate, endDate, event.clientX, event.clientY);
    };
    _platPicker.appendChild(btn);
  });

  document.body.appendChild(_platPicker);

  // Posisi picker
  const pw = 168, ph = PLATS.length * 34 + 50;
  const vw = window.innerWidth, vh = window.innerHeight;
  let left = event.clientX + 10, top = event.clientY - 10;
  if (left + pw > vw - 8) left = event.clientX - pw - 10;
  if (top + ph > vh - 8) top = vh - ph - 8;
  if (top < 8) top = 8;
  _platPicker.style.left = left + 'px';
  _platPicker.style.top  = top + 'px';

  // Tutup kalau klik di luar
  setTimeout(() => {
    document.addEventListener('mousedown', function _close(e) {
      if (_platPicker && !_platPicker.contains(e.target)) {
        _platPicker.remove(); _platPicker = null;
        document.removeEventListener('mousedown', _close);
      }
    });
  }, 50);
}

// ── Global expose ─────────────────────────────
window._openCmpModal    = _openCmpModal;
window._closeCmpModal   = _closeCmpModal;
window._openPlatPicker  = _openPlatPicker;
window.openCmpMentionPopup = openCmpMentionPopup;
window.closeCmpPopup       = closeCmpPopup;
</script>
@endsection