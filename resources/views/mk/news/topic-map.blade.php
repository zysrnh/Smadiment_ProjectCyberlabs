@extends('mk.layouts.app')

@section('title', 'News Topic Map - SMADIMENT')

@section('styles')
<style>
  :root {
    --green: #038047; --green-dark: #026738; --green-light: #04995a;
    --text: #1e293b; --muted: #64748b; --border: #e2e8f0;
    --bg: #f1f5f9; --white: #ffffff;
  }
  .ntm-wrap { padding: 24px 28px 40px; background: var(--bg); min-height: 100vh; }

  /* Header */
  .ntm-page-hdr { margin-bottom: 22px; }
  .ntm-page-hdr h1 { font-size: 26px; font-weight: 700; color: var(--text); margin: 0 0 4px; letter-spacing: -.3px; }
  .ntm-page-hdr p  { font-size: 13px; color: var(--muted); margin: 0; }

  /* Filter card */
  .ntm-filter {
    background: var(--white); border-radius: 14px; padding: 16px 20px;
    margin-bottom: 18px; border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .ntm-filter-lbl { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; }
  .ntm-date-btn {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 16px; background: #f8fafc;
    border: 1px solid var(--border); border-radius: 10px;
    font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500;
    color: var(--text); cursor: pointer; transition: border-color .2s, box-shadow .2s;
    flex: 1; max-width: 360px;
  }
  .ntm-date-btn:hover { border-color: var(--green); box-shadow: 0 0 0 3px rgba(3,128,71,.08); }
  .ntm-date-btn svg { width:15px;height:15px;stroke:var(--muted);fill:none;stroke-width:2;flex-shrink:0; }
  .ntm-date-btn span { flex:1; }
  .ntm-sel {
    padding: 9px 13px; border: 1px solid var(--border); border-radius: 10px;
    font-family: 'Poppins',sans-serif; font-size: 13px; font-weight: 500;
    color: var(--text); background: var(--white); outline: none; cursor: pointer;
  }
  .ntm-sel:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(3,128,71,.08); }
  .ntm-apply {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px;
    background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
    color: #fff; border: none; border-radius: 10px;
    font-family: 'Poppins',sans-serif; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all .22s;
    box-shadow: 0 3px 10px rgba(3,128,71,.2);
  }
  .ntm-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(3,128,71,.3); }
  .ntm-apply svg { width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5; }

  /* Stats row */
  .ntm-stats { display: flex; gap: 12px; margin-bottom: 18px; flex-wrap: wrap; }
  .ntm-sp {
    background: var(--white); border: 1px solid var(--border); border-radius: 10px;
    padding: 12px 18px; box-shadow: 0 1px 3px rgba(0,0,0,.05);
  }
  .ntm-sp .sl { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
  .ntm-sp .sv { font-size: 20px; font-weight: 700; color: var(--green); }

  /* Controls bar */
  .ntm-ctrl {
    background: var(--white); border: 1px solid var(--border); border-radius: 12px;
    padding: 11px 16px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
  }
  .ntm-vtog { display: flex; background: #f1f5f9; border-radius: 8px; padding: 3px; gap: 2px; }
  .ntm-vbtn {
    padding: 6px 13px; border: none; border-radius: 6px;
    background: transparent; font-family: 'Poppins',sans-serif;
    font-size: 12px; font-weight: 600; color: var(--muted);
    cursor: pointer; transition: all .15s;
    display: flex; align-items: center; gap: 5px;
  }
  .ntm-vbtn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2; }
  .ntm-vbtn.on { background: var(--white); color: var(--green); box-shadow: 0 1px 4px rgba(0,0,0,.1); }
  .ntm-vbtn:hover:not(.on) { color: var(--text); }
  .ntm-sml {
    padding: 7px 11px; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 500;
    color: var(--text); background: var(--white); outline: none; cursor: pointer;
  }
  .ntm-sml:focus { border-color: var(--green); }
  .ntm-srch {
    padding: 7px 13px; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px;
    color: var(--text); background: #f8fafc; outline: none; width: 190px;
    transition: border-color .2s, background .2s;
  }
  .ntm-srch:focus { border-color: var(--green); background: var(--white); }
  .ntm-srch::placeholder { color: #94a3b8; }
  .ntm-rlbtn {
    width: 31px; height: 31px; border-radius: 8px; border: 1px solid var(--border);
    background: var(--white); display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .18s; color: var(--muted); margin-left: auto;
  }
  .ntm-rlbtn:hover { border-color: var(--green); color: var(--green); background: #f0fdf4; }
  .ntm-rlbtn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.5; }
  .ntm-rlbtn.spin svg { animation: ntmSpin .7s linear infinite; }
  @keyframes ntmSpin { to { transform: rotate(360deg); } }

  /* Loading / empty */
  .ntm-lding {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 420px; gap: 16px;
  }
  .ntm-spinner {
    width: 42px; height: 42px;
    border: 3px solid rgba(3,128,71,.15);
    border-top-color: var(--green); border-radius: 50%;
    animation: ntmSpin .85s linear infinite;
  }
  .ntm-lding p { font-size: 13px; color: var(--muted); font-weight: 500; }
  .ntm-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 320px; gap: 12px;
  }
  .ntm-empty svg { width:48px;height:48px;stroke:#cbd5e1;fill:none;stroke-width:1.5; }
  .ntm-empty h4 { font-size:15px;font-weight:700;color:#64748b;margin:0; }
  .ntm-empty p  { font-size:13px;color:#94a3b8;margin:0;text-align:center; }

  /* ━━━ TREEMAP (main view — like screenshot) ━━━ */
  .ntm-tmap-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
  }
  #ntmTreemap { position: relative; width: 100%; overflow: hidden; }
  .ntm-tile {
    position: absolute; overflow: hidden; cursor: pointer;
    box-sizing: border-box;
    transition: filter .16s, z-index 0s;
  }
  .ntm-tile:hover { filter: brightness(1.13); z-index: 5; }
  .ntm-tile-in { padding: 9px 11px; height: 100%; display: flex; flex-direction: column; gap: 3px; position: relative; }
  .ntm-tile-cat { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; opacity: .8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ntm-tile-hl  { font-weight: 700; line-height: 1.32; flex: 1; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; }
  .ntm-tile-cnt { position: absolute; bottom: 7px; right: 8px; font-size: 9px; font-weight: 700; background: rgba(0,0,0,.22); padding: 2px 7px; border-radius: 20px; }
  /* colors */
  .c0{background:#7f1d1d;color:#fff} .c1{background:#991b1b;color:#fff} .c2{background:#b91c1c;color:#fff}
  .c3{background:#c2410c;color:#fff} .c4{background:#92400e;color:#fff} .c5{background:#78350f;color:#fff}
  .c6{background:#166534;color:#fff} .c7{background:#14532d;color:#fff} .c8{background:#064e3b;color:#fff}
  .c9{background:#1e3a5f;color:#fff} .c10{background:#1e40af;color:#fff} .c11{background:#4338ca;color:#fff}
  .c12{background:#5b21b6;color:#fff} .c13{background:#6b21a8;color:#fff} .c14{background:#86198f;color:#fff}
  .c15{background:#9d174d;color:#fff} .c16{background:#500724;color:#fff} .c17{background:#042f2e;color:#fff}
  .c18{background:#082f49;color:#fff} .c19{background:#1c1917;color:#fff}

  /* ━━━ LIST VIEW ━━━ */
  .ntm-list-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.07);
  }
  .ntm-li {
    display: flex; align-items: center; gap: 14px; padding: 13px 20px;
    border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background .14s;
  }
  .ntm-li:last-child { border-bottom: none; }
  .ntm-li:hover { background: #f8fafc; }
  .ntm-li-rnk {
    width: 29px; height: 29px; border-radius: 8px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
  }
  .ntm-li-rnk.g { background: linear-gradient(135deg,#f59e0b,#d97706); }
  .ntm-li-rnk.s { background: linear-gradient(135deg,#94a3b8,#64748b); }
  .ntm-li-rnk.b { background: linear-gradient(135deg,#cd7c2f,#a35c1f); }
  .ntm-li-dot { width: 11px; height: 11px; border-radius: 3px; flex-shrink: 0; }
  .ntm-li-info { flex: 1; min-width: 0; }
  .ntm-li-topic { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
  .ntm-li-bar { height: 5px; background: #f1f5f9; border-radius: 10px; overflow: hidden; width: 150px; flex-shrink: 0; }
  .ntm-li-fill { height: 100%; background: linear-gradient(90deg,var(--green),var(--green-light)); border-radius:10px; transition: width .8s cubic-bezier(.4,0,.2,1); }
  .ntm-li-cnt { font-size: 15px; font-weight: 700; color: var(--green); text-align: right; min-width: 38px; }
  .ntm-li-lbl { font-size: 10px; color: var(--muted); text-align: right; }

  /* ━━━ BUBBLE VIEW ━━━ */
  .ntm-bub-card {
    background: var(--white); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.07);
    height: 580px;
  }
  .ntm-bub-card svg { width:100%; height:100%; }

  /* ━━━ DETAIL MODAL ━━━ */
  .ntm-mback {
    position: fixed; inset: 0; background: rgba(0,0,0,.55);
    backdrop-filter: blur(5px); z-index: 9998;
    display: none; align-items: center; justify-content: center;
  }
  .ntm-mback.on { display: flex; }
  .ntm-modal {
    background: var(--white); border-radius: 16px; width: 90%; max-width: 660px;
    max-height: 85vh; display: flex; flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,.28);
    animation: ntmSlide .24s ease-out;
  }
  @keyframes ntmSlide { from{transform:translateY(-16px) scale(.96);opacity:0} to{transform:none;opacity:1} }
  .ntm-mhead {
    padding: 18px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
  }
  .ntm-mcol { width: 12px; height: 12px; border-radius: 3px; flex-shrink: 0; }
  .ntm-mhead h3 { font-size: 17px; font-weight: 700; color: var(--text); flex: 1; margin: 0; }
  .ntm-mbadge {
    background: #f0fdf4; color: var(--green);
    padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
  }
  .ntm-mclose {
    width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border);
    background: var(--white); cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: all .16s; color: var(--muted);
  }
  .ntm-mclose:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }
  .ntm-mclose svg { width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5; }
  .ntm-mbody { padding: 18px 22px; overflow-y: auto; flex: 1; }
  .ntm-art { padding: 12px 0; border-bottom: 1px solid #f8fafc; display:flex;flex-direction:column;gap:4px; }
  .ntm-art:last-child { border-bottom: none; }
  .ntm-art a { font-size: 14px; font-weight: 600; color: var(--text); text-decoration: none; line-height: 1.4; }
  .ntm-art a:hover { color: var(--green); text-decoration: underline; }
  .ntm-art-meta { display:flex;gap:10px;align-items:center;font-size:11px;color:var(--muted); }
  .ntm-art-src { font-weight: 700; color: var(--green); }
  .ntm-snm { padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; color: #fff; }

  /* Tooltip */
  .ntm-tip {
    position: fixed; background: #1e293b; color: #fff;
    padding: 8px 13px; border-radius: 9px; font-size: 12px; font-weight: 500;
    pointer-events: none; opacity: 0; transition: opacity .12s;
    z-index: 9999; white-space: nowrap; box-shadow: 0 6px 20px rgba(0,0,0,.25);
  }
  .ntm-tip.on { opacity: 1; }
  .ntm-tip b { color: #4ade80; }

  /* Date picker */
  .ntm-dp {
    position: fixed; inset: 0; z-index: 10000;
    display: none; align-items: center; justify-content: center;
    background: rgba(0,0,0,.52); backdrop-filter: blur(6px);
  }
  .ntm-dp.on { display: flex; }
  .ntm-dp-box {
    background: #fff; border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0,0,0,.28);
    display: flex; max-width: 800px; width: 90%;
    animation: ntmSlide .24s ease-out;
  }
  .ntm-dp-side {
    width: 155px; background: #f8fafc;
    border-right: 1px solid var(--border);
    padding: 14px 10px; border-radius: 16px 0 0 16px;
    display: flex; flex-direction: column; gap: 3px;
  }
  .ntm-dp-pre {
    padding: 8px 13px; border: none; border-radius: 7px;
    background: transparent; font-family: 'Poppins',sans-serif;
    font-size: 12px; font-weight: 500; color: var(--text);
    text-align: left; cursor: pointer; transition: all .14s;
  }
  .ntm-dp-pre:hover { background: #fff; color: var(--green); }
  .ntm-dp-pre.on { background: var(--green); color: #fff; }
  .ntm-dp-body { flex:1; padding: 18px 20px; display: flex; flex-direction: column; }
  .ntm-dp-nav { display:flex;align-items:center;gap:12px;margin-bottom:14px; }
  .ntm-dp-nb {
    width: 30px; height: 30px; border-radius: 7px;
    border: 1px solid var(--border); background: #f8fafc;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer; transition:all .14s; flex-shrink:0;
  }
  .ntm-dp-nb:hover { background: var(--green); border-color:var(--green); color:#fff; }
  .ntm-dp-nb svg { width:17px;height:17px;stroke:currentColor;fill:none;stroke-width:2; }
  .ntm-dp-cals { display:flex;gap:18px;flex:1; }
  .ntm-dp-cal { flex:1; }
  .ntm-dp-cal-t { font-size:14px;font-weight:700;color:var(--text);text-align:center;margin-bottom:10px; }
  .ntm-dp-wds  { display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:5px; }
  .ntm-dp-wd   { text-align:center;font-size:10px;font-weight:700;color:var(--muted);padding:4px 0; }
  .ntm-dp-days { display:grid;grid-template-columns:repeat(7,1fr);gap:2px; }
  .ntm-dp-d {
    aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:500;border-radius:7px;cursor:pointer;
    transition:all .13s;color:var(--text);background:transparent;border:none;padding:0;
  }
  .ntm-dp-d:hover:not(.dim):not(.dis) { background:#f1f5f9; }
  .ntm-dp-d.dim { color:#cbd5e1;cursor:default; }
  .ntm-dp-d.dis { color:#e2e8f0;cursor:not-allowed; }
  .ntm-dp-d.tod { border:2px solid var(--green); }
  .ntm-dp-d.sel { background:var(--green);color:#fff; }
  .ntm-dp-d.rng { background:rgba(3,128,71,.1);color:var(--green); }
  .ntm-dp-disp {
    padding: 11px 15px; background: #f8fafc;
    border-radius: 10px; text-align: center; margin: 12px 0 14px;
    border: 1px solid var(--border); font-size: 13px; font-weight: 600; color: var(--text);
  }
  .ntm-dp-foot { display:flex;gap:10px;justify-content:flex-end; }
  .ntm-dp-can {
    padding:9px 18px;border-radius:8px;background:#f1f5f9;border:none;
    font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;color:var(--text);cursor:pointer;
  }
  .ntm-dp-can:hover { background:var(--border); }
  .ntm-dp-ok {
    padding:9px 20px;border-radius:8px;
    background:linear-gradient(135deg,var(--green) 0%,var(--green-dark) 100%);
    color:#fff;border:none;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;cursor:pointer;
    box-shadow:0 3px 10px rgba(3,128,71,.2);transition:all .18s;
  }
  .ntm-dp-ok:hover { transform:translateY(-1px);box-shadow:0 5px 14px rgba(3,128,71,.3); }

  @keyframes ntmFI { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }
  .ntm-fi { animation: ntmFI .38s ease-out both; }

  @media(max-width:768px){
    .ntm-wrap{padding:14px;}
    .ntm-li-bar{display:none;}
    .ntm-dp-box{flex-direction:column;}
    .ntm-dp-side{width:100%;border-right:none;border-bottom:1px solid var(--border);flex-direction:row;overflow-x:auto;border-radius:16px 16px 0 0;}
    .ntm-dp-cals{flex-direction:column;}
  }
</style>
@endsection

@section('content')
<div class="ntm-wrap">
  <div class="ntm-page-hdr">
    <h1>News Topic Map</h1>
    <p>Visual map of the most-discussed entities and topics in news articles</p>
  </div>

  @if(!$projectId)
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:16px 20px;color:#92400e;font-size:13px;font-weight:500;">
      No project selected. Please choose a project from the sidebar.
    </div>
  @else

  <!-- Filter -->
  <div class="ntm-filter">
    <span class="ntm-filter-lbl">Date Range</span>
    <button type="button" class="ntm-date-btn" id="ntmDateBtn">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span id="ntmDateSpan">{{ $startDate }} to {{ $endDate }}</span>
      <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <select class="ntm-sel" id="ntmMedia">
      <option value="all">All Media</option>
      <option value="doc" selected>Online News</option>
      <option value="print">Print</option>
    </select>
    <button class="ntm-apply" onclick="ntmLoad()">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Apply
    </button>
  </div>

  <!-- Stats -->
  <div class="ntm-stats" id="ntmStats" style="display:none;">
    <div class="ntm-sp"><div class="sl">Total Topics</div><div class="sv" id="ssTopics">—</div></div>
    <div class="ntm-sp"><div class="sl">Total Docs</div><div class="sv" id="ssDocs">—</div></div>
    <div class="ntm-sp"><div class="sl">Top Topic</div><div class="sv" id="ssTop" style="font-size:14px;color:var(--text);">—</div></div>
    <div class="ntm-sp"><div class="sl">Range</div><div class="sv" id="ssRange" style="font-size:11px;color:var(--muted);font-weight:600;">—</div></div>
  </div>

  <!-- Controls -->
  <div class="ntm-ctrl">
    <div class="ntm-vtog">
      <button class="ntm-vbtn on" id="vbMap"    onclick="ntmView('map')">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="11"/><rect x="13" y="3" width="8" height="6"/><rect x="13" y="11" width="8" height="10"/><rect x="3" y="16" width="8" height="5"/></svg>Map
      </button>
      <button class="ntm-vbtn" id="vbList"   onclick="ntmView('list')">
        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="3" cy="6" r="1"/><circle cx="3" cy="12" r="1"/><circle cx="3" cy="18" r="1"/></svg>List
      </button>
      <button class="ntm-vbtn" id="vbBubble" onclick="ntmView('bubble')">
        <svg viewBox="0 0 24 24"><circle cx="7" cy="7" r="4"/><circle cx="17" cy="7" r="3"/><circle cx="12" cy="17" r="5"/></svg>Bubble
      </button>
    </div>
    <select class="ntm-sml" id="ntmSort" onchange="ntmRender()">
      <option value="desc">Most Mentioned</option>
      <option value="asc">Least Mentioned</option>
      <option value="alpha">Alphabetical</option>
    </select>
    <select class="ntm-sml" id="ntmLimit" onchange="ntmRender()">
      <option value="20">Top 20</option>
      <option value="30" selected>Top 30</option>
      <option value="50">Top 50</option>
      <option value="0">All Topics</option>
    </select>
    <input class="ntm-srch" id="ntmSrch" placeholder="Search topic…" oninput="ntmRender()">
    <button class="ntm-rlbtn" id="ntmRl" onclick="ntmLoad()" title="Refresh">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
    </button>
  </div>

  <!-- Views -->
  <div id="ntmLoading" class="ntm-lding"><div class="ntm-spinner"></div><p>Loading topic map…</p></div>
  <div class="ntm-tmap-card" id="ntmMapCard" style="display:none;"><div id="ntmTreemap"></div></div>
  <div class="ntm-list-card" id="ntmListCard" style="display:none;"><div id="ntmListInner"></div></div>
  <div class="ntm-bub-card"  id="ntmBubCard"  style="display:none;"><svg id="ntmBubSvg"></svg></div>
  <div class="ntm-empty"    id="ntmEmpty"    style="display:none;">
    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <h4>No topic data found</h4>
    <p>Try a wider date range or different media type.</p>
  </div>

  @endif
</div>

<!-- Detail Modal -->
<div class="ntm-mback" id="ntmMback">
  <div class="ntm-modal">
    <div class="ntm-mhead">
      <div class="ntm-mcol" id="ntmMcol"></div>
      <h3 id="ntmMtitle">Topic</h3>
      <span class="ntm-mbadge" id="ntmMbadge">0 docs</span>
      <button class="ntm-mclose" onclick="ntmCloseM()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="ntm-mbody" id="ntmMbody"></div>
  </div>
</div>

<!-- Tooltip -->
<div class="ntm-tip" id="ntmTip"></div>

<!-- Date Picker -->
<div class="ntm-dp" id="ntmDp">
  <div style="position:absolute;inset:0;" onclick="ntmDpC()"></div>
  <div class="ntm-dp-box">
    <div class="ntm-dp-side">
      <button class="ntm-dp-pre" data-p="today">Today</button>
      <button class="ntm-dp-pre" data-p="yesterday">Yesterday</button>
      <button class="ntm-dp-pre" data-p="last7">Last 7 Days</button>
      <button class="ntm-dp-pre" data-p="last30">Last 30 Days</button>
      <button class="ntm-dp-pre" data-p="thismonth">This Month</button>
      <button class="ntm-dp-pre" data-p="lastmonth">Last Month</button>
      <button class="ntm-dp-pre on" data-p="custom">Custom</button>
    </div>
    <div class="ntm-dp-body">
      <div class="ntm-dp-nav">
        <button class="ntm-dp-nb" id="ntmDpPrev"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
        <div class="ntm-dp-cals">
          <div class="ntm-dp-cal" id="ntmCal1"></div>
          <div class="ntm-dp-cal" id="ntmCal2"></div>
        </div>
        <button class="ntm-dp-nb" id="ntmDpNext"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
      <div class="ntm-dp-disp" id="ntmDpDisp">Select a date range</div>
      <div class="ntm-dp-foot">
        <button class="ntm-dp-can" onclick="ntmDpC()">Cancel</button>
        <button class="ntm-dp-ok"  id="ntmDpOk">Apply</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script>
(function(){
'use strict';

const CFG = {
  pid:   '{{ $projectId ?? "" }}',
  s:     '{{ $startDate ?? "" }}',
  e:     '{{ $endDate ?? "" }}',
  api:   '/mk/api/topic-map',
  artApi:'/mk/api/news/articles',
};

const PAL = [
  '#7f1d1d','#991b1b','#b91c1c','#c2410c','#92400e',
  '#78350f','#166534','#14532d','#064e3b','#1e3a5f',
  '#1e40af','#4338ca','#5b21b6','#6b21a8','#86198f',
  '#9d174d','#500724','#042f2e','#082f49','#1c1917',
];

let D = { raw:[], fil:[], view:'map', busy:false };

/* ── helpers ── */
const $  = id => document.getElementById(id);
const tip = $('ntmTip');

function showTip(e,h){ tip.innerHTML=h; tip.classList.add('on'); moveTip(e); }
function moveTip(e){ tip.style.left=(e.clientX+14)+'px'; tip.style.top=(e.clientY-40)+'px'; }
function hideTip(){ tip.classList.remove('on'); }
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function trunc(s,n){ return s.length>n?s.slice(0,n-1)+'…':s; }
function lighten(hex,a){
  const n=parseInt(hex.replace('#',''),16);
  return `rgb(${Math.min(255,((n>>16)&0xff)+Math.round(255*a))},${Math.min(255,((n>>8)&0xff)+Math.round(255*a))},${Math.min(255,((n>>0)&0xff)+Math.round(255*a))})`;
}
function sColor(s){
  s=(s||'').toLowerCase();
  return s.includes('pos')?'#10b981':s.includes('neg')?'#ef4444':'#64748b';
}

/* ── show/hide panels ── */
const PANELS=['ntmLoading','ntmMapCard','ntmListCard','ntmBubCard','ntmEmpty'];
function showOnly(id){ PANELS.forEach(p=>$(p).style.display='none'); if(id) $(id).style.display='block'; }
function showLd(){ PANELS.forEach(p=>$(p).style.display='none'); $('ntmLoading').style.display='flex'; }

/* ══════════════════════════════════════
   DATE PICKER
══════════════════════════════════════ */
let dpS=null, dpE=null, dpM1, dpM2, dpSel=true;

function dpFmt(d){ if(!d)return''; const y=d.getFullYear(),m=String(d.getMonth()+1).padStart(2,'0'),dd=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${dd}`; }
function dpSame(a,b){ return a&&b&&a.toDateString()===b.toDateString(); }

function dpOpen(){
  dpS=dpS||new Date(CFG.s); dpE=dpE||new Date(CFG.e);
  dpM1=new Date(dpS); dpM2=new Date(dpS); dpM2.setMonth(dpM2.getMonth()+1);
  dpDraw(); $('ntmDp').classList.add('on');
}
window.ntmDpC=function(){ $('ntmDp').classList.remove('on'); };

function dpDraw(){
  dpCal('ntmCal1',dpM1); dpCal('ntmCal2',dpM2);
  $('ntmDpDisp').textContent=(dpS&&dpE)?`${dpFmt(dpS)}  →  ${dpFmt(dpE)}`:'Select start date';
}

function dpCal(cid,month){
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
  const y=month.getFullYear(),m=month.getMonth();
  const first=new Date(y,m,1),last=new Date(y,m+1,0),today=new Date(); today.setHours(0,0,0,0);
  let h=`<div class="ntm-dp-cal-t">${MN[m]} ${y}</div>
  <div class="ntm-dp-wds">${WD.map(w=>`<div class="ntm-dp-wd">${w}</div>`).join('')}</div>
  <div class="ntm-dp-days">`;
  const fDow=first.getDay(), prev=new Date(y,m,0).getDate();
  for(let i=fDow-1;i>=0;i--) h+=`<button class="ntm-dp-d dim" disabled>${prev-i}</button>`;
  for(let d=1;d<=last.getDate();d++){
    const date=new Date(y,m,d); date.setHours(0,0,0,0);
    let cls='ntm-dp-d';
    if(dpSame(date,today)) cls+=' tod';
    if(date>today) cls+=' dis';
    if(dpSame(date,dpS)||dpSame(date,dpE)) cls+=' sel';
    else if(dpS&&dpE&&date>dpS&&date<dpE) cls+=' rng';
    const ds=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    h+=`<button class="${cls}" ${date>today?'disabled':''} data-dt="${ds}">${d}</button>`;
  }
  const lDow=last.getDay();
  for(let i=1;i<=6-lDow;i++) h+=`<button class="ntm-dp-d dim" disabled>${i}</button>`;
  h+='</div>';
  $(cid).innerHTML=h;
  $(cid).querySelectorAll('.ntm-dp-d:not(.dim):not(.dis)').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const d=new Date(btn.dataset.dt); d.setHours(0,0,0,0);
      if(dpSel||(dpS&&d<dpS)){ dpS=d;dpE=d;dpSel=false; }
      else{ dpE=d>=dpS?d:dpS; if(d<dpS){dpE=dpS;dpS=d;} dpSel=true; }
      dpDraw();
      document.querySelectorAll('.ntm-dp-pre').forEach(b=>b.classList.remove('on'));
      document.querySelector('[data-p="custom"]').classList.add('on');
    });
  });
}

/* ══════════════════════════════════════
   LOAD
══════════════════════════════════════ */
window.ntmLoad = async function(){
  if(D.busy) return; D.busy=true;
  const rl=$('ntmRl'); rl&&rl.classList.add('spin');
  $('ntmStats').style.display='none';
  showLd();
  try{
    const url=`${CFG.api}?project_id=${CFG.pid}&media=${$('ntmMedia')?.value||'all'}&start_date=${CFG.s}&end_date=${CFG.e}`;
    const res=await fetch(url); const json=await res.json();
    let raw={};
    if(json.data&&!Array.isArray(json.data)&&typeof json.data==='object') raw=json.data;
    else if(Array.isArray(json.data)) json.data.forEach(item=>{ raw[item.name||item.topic||'']={num_docs:item.weight||item.num_docs||1}; });
    else if(typeof json==='object'&&!json.success&&!json.error) raw=json;

    D.raw=Object.entries(raw)
      .map(([topic,val],i)=>({ topic, count:typeof val==='object'?(val.num_docs||val.count||1):Number(val), color:PAL[i%PAL.length] }))
      .filter(d=>d.topic&&d.count>0)
      .sort((a,b)=>b.count-a.count)
      .map((d,i)=>({...d,color:PAL[i%PAL.length]}));

    if(!D.raw.length){ showOnly('ntmEmpty'); return; }
    updateStats(); ntmRender();
  } catch(err){ console.error('[NTM]',err); showOnly('ntmEmpty'); }
  finally{ D.busy=false; rl&&rl.classList.remove('spin'); }
};

function updateStats(){
  const tot=D.raw.reduce((s,d)=>s+d.count,0);
  $('ntmStats').style.display='flex';
  $('ssTopics').textContent=D.raw.length;
  $('ssDocs').textContent=tot.toLocaleString();
  $('ssTop').textContent=D.raw[0]?.topic||'—';
  $('ssRange').textContent=`${CFG.s} → ${CFG.e}`;
}

/* ══════════════════════════════════════
   RENDER dispatcher
══════════════════════════════════════ */
window.ntmRender = function(){
  const sort=$('ntmSort')?.value||'desc';
  const limit=parseInt($('ntmLimit')?.value||'30');
  const q=($('ntmSrch')?.value||'').toLowerCase().trim();
  let data=[...D.raw];
  if(q) data=data.filter(d=>d.topic.toLowerCase().includes(q));
  if(sort==='asc')   data.sort((a,b)=>a.count-b.count);
  if(sort==='alpha') data.sort((a,b)=>a.topic.localeCompare(b.topic));
  if(limit>0) data=data.slice(0,limit);
  D.fil=data;
  if(!data.length){ showOnly('ntmEmpty'); return; }
  switch(D.view){ case'map':doMap(data);break; case'list':doList(data);break; case'bubble':doBub(data);break; }
};

window.ntmView = function(v){
  D.view=v;
  ['map','list','bubble'].forEach(k=>{
    const id='vb'+k.charAt(0).toUpperCase()+k.slice(1);
    $(id)&&$(id).classList.toggle('on',k===v);
  });
  if(D.raw.length) ntmRender();
};

/* ══════════════════════════════════════
   MAP VIEW — treemap tiles
══════════════════════════════════════ */
function doMap(data){
  showOnly('ntmMapCard');
  const wrap=$('ntmTreemap');
  wrap.innerHTML='';
  const W=wrap.offsetWidth||900;
  const H=Math.min(680,Math.max(500,window.innerHeight*0.66));
  wrap.style.height=H+'px';

  const root=d3.hierarchy({children:data}).sum(d=>d.count||1);
  d3.treemap().size([W,H]).padding(2).round(true)(root);

  root.leaves().forEach((node,i)=>{
    const d=node.data;
    const tW=node.x1-node.x0, tH=node.y1-node.y0;
    const tile=document.createElement('div');
    tile.className=`ntm-tile c${i%20} ntm-fi`;
    tile.style.cssText=`left:${node.x0}px;top:${node.y0}px;width:${tW}px;height:${tH}px;animation-delay:${i*14}ms;`;
    const fs=Math.max(11,Math.min(20,tW/9));
    const catFs=Math.max(8,fs*0.58);
    const lines=Math.max(1,Math.floor((tH-28)/(fs*1.32)));
    tile.innerHTML=`<div class="ntm-tile-in">
      <div class="ntm-tile-cat" style="font-size:${catFs}px;">${esc(d.topic)}</div>
      ${tH>55?`<div class="ntm-tile-hl" style="font-size:${fs}px;-webkit-line-clamp:${lines};">${esc(d.topic)}</div>`:''}
      <div class="ntm-tile-cnt">${d.count} docs</div>
    </div>`;
    tile.addEventListener('mouseenter',e=>showTip(e,`<b>${d.topic}</b> — ${d.count} articles`));
    tile.addEventListener('mousemove',moveTip);
    tile.addEventListener('mouseleave',hideTip);
    tile.addEventListener('click',()=>openModal(d,i));
    wrap.appendChild(tile);
  });
}

/* ══════════════════════════════════════
   LIST VIEW
══════════════════════════════════════ */
function doList(data){
  showOnly('ntmListCard');
  const max=data[0].count;
  let h='';
  data.forEach((d,i)=>{
    const pct=Math.round((d.count/max)*100);
    const rc=i===0?'g':i===1?'s':i===2?'b':'';
    h+=`<div class="ntm-li ntm-fi" style="animation-delay:${i*16}ms;" onclick="openModal(ntmGD('${d.topic.replace(/'/g,"\\'")}'),${i})">
      <div class="ntm-li-rnk ${rc}">${i+1}</div>
      <div class="ntm-li-dot" style="background:${d.color};"></div>
      <div class="ntm-li-info">
        <div class="ntm-li-topic">${esc(d.topic)}</div>
        <div class="ntm-li-bar"><div class="ntm-li-fill" style="width:0%" data-p="${pct}"></div></div>
      </div>
      <div><div class="ntm-li-cnt">${d.count}</div><div class="ntm-li-lbl">docs</div></div>
    </div>`;
  });
  $('ntmListInner').innerHTML=h;
  requestAnimationFrame(()=>requestAnimationFrame(()=>{
    $('ntmListInner').querySelectorAll('.ntm-li-fill').forEach((b,i)=>{ setTimeout(()=>{ b.style.width=b.dataset.p+'%'; },i*16+60); });
  }));
}

/* ══════════════════════════════════════
   BUBBLE VIEW
══════════════════════════════════════ */
function doBub(data){
  showOnly('ntmBubCard');
  const svgEl=$('ntmBubSvg'); svgEl.innerHTML='';
  const W=svgEl.parentElement.clientWidth||860;
  const H=svgEl.parentElement.clientHeight||580;
  svgEl.setAttribute('viewBox',`0 0 ${W} ${H}`);
  const pack=d3.pack().size([W,H]).padding(5);
  const root=d3.hierarchy({children:data}).sum(d=>d.count||1).sort((a,b)=>b.value-a.value);
  pack(root);
  const svg=d3.select(svgEl);
  const defs=svg.append('defs');
  root.leaves().forEach((node,i)=>{
    const gid=`bg${i}`;
    const gr=defs.append('radialGradient').attr('id',gid).attr('cx','35%').attr('cy','35%');
    const base=PAL[i%PAL.length];
    gr.append('stop').attr('offset','0%').attr('stop-color',lighten(base,.3));
    gr.append('stop').attr('offset','100%').attr('stop-color',base);
    node._gid=gid; node._base=base;
  });
  const nodes=svg.selectAll('g').data(root.leaves()).enter().append('g')
    .attr('transform',d=>`translate(${d.x},${d.y})`).style('cursor','pointer');
  nodes.append('circle').attr('r',0)
    .attr('fill',d=>`url(#${d._gid})`).attr('stroke',d=>d._base)
    .attr('stroke-width',1.5).attr('stroke-opacity',.4)
    .transition().duration(500).delay((_,i)=>i*14).ease(d3.easeBounceOut).attr('r',d=>d.r);
  nodes.filter(d=>d.r>20).append('text')
    .attr('text-anchor','middle').attr('dy',d=>d.r>36?'-0.3em':'0.35em')
    .attr('font-family','Poppins,sans-serif').attr('font-weight','700')
    .attr('fill','#fff').attr('font-size',d=>Math.max(9,Math.min(14,d.r*.32))+'px')
    .text(d=>trunc(d.data.topic,Math.floor(d.r/5)))
    .style('opacity',0).transition().duration(300).delay((_,i)=>i*14+350).style('opacity',1);
  nodes.filter(d=>d.r>38).append('text')
    .attr('text-anchor','middle').attr('dy','1.1em')
    .attr('font-family','Poppins,sans-serif').attr('font-weight','500')
    .attr('fill','rgba(255,255,255,.75)').attr('font-size',d=>Math.max(8,Math.min(11,d.r*.21))+'px')
    .text(d=>d.data.count+' docs')
    .style('opacity',0).transition().duration(300).delay((_,i)=>i*14+400).style('opacity',1);
  nodes
    .on('mouseenter',(e,d)=>{ showTip(e,`<b>${d.data.topic}</b> — ${d.data.count} docs`); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.9).attr('stroke-width',2.5); })
    .on('mousemove',moveTip)
    .on('mouseleave',e=>{ hideTip(); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.4).attr('stroke-width',1.5); })
    .on('click',(e,d)=>openModal(d.data,D.raw.indexOf(d.data)));
}

/* ══════════════════════════════════════
   DETAIL MODAL
══════════════════════════════════════ */
window.ntmGD=function(topic){ return D.fil.find(d=>d.topic===topic)||D.raw.find(d=>d.topic===topic)||{topic,count:0}; };

window.openModal=function(d,idx){
  if(!d) return;
  const col=PAL[idx%PAL.length];
  $('ntmMcol').style.background=col;
  $('ntmMtitle').textContent=d.topic;
  $('ntmMbadge').textContent=`${d.count} docs`;
  $('ntmMback').classList.add('on');
  $('ntmMbody').innerHTML='<p style="color:var(--muted);font-size:13px;padding:10px 0;">Loading articles…</p>';

  const url=`${CFG.artApi}?project_id=${CFG.pid}&media=doc&start_date=${CFG.s}&end_date=${CFG.e}&rows=50`;
  fetch(url).then(r=>r.json()).then(json=>{
    const arts=(json.data||[]).filter(a=>((a.title||'')+(a.content||'')).toLowerCase().includes(d.topic.toLowerCase()));
    if(!arts.length){
      $('ntmMbody').innerHTML=`<div style="padding:24px 0;text-align:center;">
        <p style="font-size:13px;color:var(--muted);">No articles found for "<b>${esc(d.topic)}</b>" in this period.</p>
        <p style="font-size:12px;margin-top:6px;color:#94a3b8;">This topic has <b style="color:var(--green);">${d.count}</b> mentions in the corpus.</p>
      </div>`; return;
    }
    let h=`<p style="font-size:12px;color:var(--muted);margin-bottom:12px;">Found ${arts.length} article(s) mentioning this topic</p>`;
    arts.forEach(a=>{
      h+=`<div class="ntm-art">
        <a href="${esc(a.url||'#')}" target="_blank" rel="noopener">${esc(a.title||'Untitled')}</a>
        <div class="ntm-art-meta">
          <span class="ntm-art-src">${esc(a.publisher||'Unknown')}</span>
          <span>${esc(a.date_created||'')}</span>
          <span class="ntm-snm" style="background:${sColor(a.sentiment)};">${esc(a.sentiment||'Neutral')}</span>
        </div>
      </div>`;
    });
    $('ntmMbody').innerHTML=h;
  }).catch(()=>{
    $('ntmMbody').innerHTML=`<p style="font-size:13px;color:var(--muted);padding:16px 0;">Topic has <b style="color:var(--green);">${d.count}</b> mentions. Article detail unavailable.</p>`;
  });
};
window.ntmCloseM=function(){ $('ntmMback').classList.remove('on'); };
$('ntmMback')?.addEventListener('click',function(e){ if(e.target===this) ntmCloseM(); });

/* ══════════════════════════════════════
   BOOT
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',()=>{
  $('ntmDateBtn')?.addEventListener('click', dpOpen);

  $('ntmDpPrev')?.addEventListener('click',()=>{ dpM1.setMonth(dpM1.getMonth()-1); dpM2.setMonth(dpM2.getMonth()-1); dpDraw(); });
  $('ntmDpNext')?.addEventListener('click',()=>{ dpM1.setMonth(dpM1.getMonth()+1); dpM2.setMonth(dpM2.getMonth()+1); dpDraw(); });

  $('ntmDpOk')?.addEventListener('click',()=>{
    if(!dpS||!dpE) return;
    CFG.s=dpFmt(dpS); CFG.e=dpFmt(dpE);
    $('ntmDateSpan').textContent=`${CFG.s} to ${CFG.e}`;
    $('ssRange').textContent=`${CFG.s} → ${CFG.e}`;
    ntmDpC();
  });

  document.querySelectorAll('.ntm-dp-pre').forEach(btn=>{
    btn.addEventListener('click',()=>{
      document.querySelectorAll('.ntm-dp-pre').forEach(b=>b.classList.remove('on'));
      btn.classList.add('on');
      const today=new Date(); today.setHours(0,0,0,0);
      switch(btn.dataset.p){
        case'today':     dpS=new Date(today);dpE=new Date(today);break;
        case'yesterday': dpS=new Date(today);dpS.setDate(dpS.getDate()-1);dpE=new Date(dpS);break;
        case'last7':     dpE=new Date(today);dpS=new Date(today);dpS.setDate(dpS.getDate()-6);break;
        case'last30':    dpE=new Date(today);dpS=new Date(today);dpS.setDate(dpS.getDate()-29);break;
        case'thismonth': dpS=new Date(today.getFullYear(),today.getMonth(),1);dpE=new Date(today);break;
        case'lastmonth': dpS=new Date(today.getFullYear(),today.getMonth()-1,1);dpE=new Date(today.getFullYear(),today.getMonth(),0);break;
      }
      dpM1=new Date(dpS);dpM2=new Date(dpS);dpM2.setMonth(dpM2.getMonth()+1);
      dpDraw();
    });
  });

  if(CFG.pid) ntmLoad();
});

let rTimer;
window.addEventListener('resize',()=>{
  clearTimeout(rTimer);
  rTimer=setTimeout(()=>{
    if(D.raw.length&&D.view==='map')    doMap(D.fil);
    if(D.raw.length&&D.view==='bubble') doBub(D.fil);
  },300);
});

})();
</script>
@endsection