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

  .ntm-page-hdr { margin-bottom: 22px; }
  .ntm-page-hdr h1 { font-size: 26px; font-weight: 700; color: var(--text); margin: 0 0 4px; letter-spacing: -.3px; }
  .ntm-page-hdr p  { font-size: 13px; color: var(--muted); margin: 0; }

  .ntm-filter {
    background: #fff; border-radius: 14px; padding: 16px 20px;
    margin-bottom: 18px; border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .ntm-filter-lbl { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; }

  .ntm-date-trigger {
    display: flex; align-items: center; gap: 10px; padding: 10px 16px;
    background: #f8fafc; border: 1px solid var(--border); border-radius: 12px;
    font-family: 'Poppins',sans-serif; font-size: 14px; font-weight: 500;
    color: var(--text); cursor: pointer; transition: all .2s; min-width: 280px;
  }
  .ntm-date-trigger:hover { border-color: var(--green); background: #fff; box-shadow: 0 0 0 3px rgba(3,128,71,.08); }
  .ntm-date-trigger svg { width:16px;height:16px;color:var(--muted);flex-shrink:0; }
  .ntm-date-trigger span { flex:1; text-align:left; }

  .ntm-apply {
    display: inline-flex; align-items: center; gap: 7px; padding: 10px 22px;
    background: linear-gradient(135deg, var(--green), var(--green-dark));
    color: #fff; border: none; border-radius: 12px;
    font-family: 'Poppins',sans-serif; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all .22s; box-shadow: 0 4px 12px rgba(3,128,71,.2);
  }
  .ntm-apply:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .ntm-apply svg { width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5; }

  /* Date Picker Modal */
  .ntm-dp-modal {
    position: fixed; inset: 0; z-index: 10000;
    display: none; align-items: center; justify-content: center;
    background: rgba(0,0,0,.5); backdrop-filter: blur(8px);
  }
  .ntm-dp-modal.show { display: flex; }
  .ntm-dp-overlay { position:absolute;inset:0;cursor:pointer; }
  .ntm-dp-container {
    position: relative; z-index: 1;
    background: #fff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,.3);
    display: flex; max-width: 860px; width: 90%; max-height: 90vh;
    animation: ntmDpUp .3s ease-out;
  }
  @keyframes ntmDpUp {
    from { opacity:0; transform:translateY(20px) scale(.95); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }
  .ntm-dp-sidebar {
    width: 170px; background: #f8fafc;
    border-right: 1px solid var(--border);
    padding: 14px 10px;
    border-radius: 16px 0 0 16px;
    display: flex; flex-direction: column; gap: 3px; flex-shrink: 0;
  }
  .ntm-dp-preset {
    padding: 9px 14px; background: transparent; border: none;
    border-radius: 8px; font-family: 'Poppins',sans-serif;
    font-size: 13px; font-weight: 500; color: var(--text);
    text-align: left; cursor: pointer; transition: all .15s;
  }
  .ntm-dp-preset:hover  { background: #fff; color: var(--green); }
  .ntm-dp-preset.active { background: var(--green); color: #fff; }
  .ntm-dp-content {
    flex: 1; padding: 20px 24px;
    display: flex; flex-direction: column; overflow: hidden;
  }
  .ntm-dp-cals-wrap { display: flex; align-items: flex-start; gap: 8px; flex: 1; }
  .ntm-dp-cals { display:flex; gap:24px; flex:1; }
  .ntm-dp-cal  { flex:1; }
  .ntm-dp-nav {
    width:34px;height:34px;border-radius:8px;
    background:#f8fafc;border:1px solid var(--border);
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .15s;flex-shrink:0;margin-top:36px;
  }
  .ntm-dp-nav:hover { background:var(--green);border-color:var(--green);color:#fff; }
  .ntm-dp-nav svg { width:18px;height:18px; }

  .ntm-cal-month { font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;text-align:center; }
  .ntm-cal-wdays { display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-bottom:6px; }
  .ntm-cal-wday  { text-align:center;font-size:10px;font-weight:700;color:var(--muted);padding:6px 0; }
  .ntm-cal-days  { display:grid;grid-template-columns:repeat(7,1fr);gap:3px; }
  .ntm-cal-day {
    aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:500;border-radius:8px;cursor:pointer;
    transition:all .12s;color:var(--text);background:transparent;
    border:none;padding:0;font-family:'Poppins',sans-serif;
  }
  .ntm-cal-day:hover:not(.oth):not(.dis) { background:#f1f5f9; }
  .ntm-cal-day.oth { color:#cbd5e1;cursor:default; }
  .ntm-cal-day.dis { color:#e2e8f0;cursor:not-allowed; }
  .ntm-cal-day.tod { border:2px solid var(--green); }
  .ntm-cal-day.sel { background:var(--green);color:#fff; }
  .ntm-cal-day.inr { background:rgba(3,128,71,.1);color:var(--green); }

  .ntm-dp-display {
    padding:12px 16px;background:#f8fafc;border-radius:10px;
    text-align:center;margin:16px 0;border:1px solid var(--border);
  }
  .ntm-dp-display span { font-size:14px;font-weight:600;color:var(--text); }
  .ntm-dp-footer { display:flex;gap:10px;justify-content:flex-end; }
  .ntm-dp-cancel {
    padding:9px 22px;border-radius:10px;font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;
    background:#f1f5f9;color:var(--text);border:none;
  }
  .ntm-dp-cancel:hover { background:var(--border); }
  .ntm-dp-apply {
    padding:9px 22px;border-radius:10px;font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;
    background:linear-gradient(135deg,var(--green),var(--green-dark));
    color:#fff;border:none;box-shadow:0 4px 12px rgba(3,128,71,.2);
  }
  .ntm-dp-apply:hover { transform:translateY(-1px);box-shadow:0 6px 16px rgba(3,128,71,.3); }

  @media(max-width:768px){
    .ntm-dp-container { flex-direction:column;width:96%; }
    .ntm-dp-sidebar { width:100%;flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--border);border-radius:16px 16px 0 0; }
    .ntm-dp-preset { white-space:nowrap; }
    .ntm-dp-cals { flex-direction:column; }
  }

  /* stats removed */

  /* Main Tab Bar */
  .ntm-maintabs {
    display: flex; gap: 0; margin-bottom: 16px;
    background: #fff; border: 1px solid var(--border);
    border-radius: 12px; padding: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.05);
    width: fit-content;
  }
  .ntm-mtab {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 18px; border-radius: 8px; border: none;
    font-family: 'Poppins',sans-serif; font-size: 13px; font-weight: 600;
    color: var(--muted); background: transparent; cursor: pointer; transition: all .18s;
  }
  .ntm-mtab svg { width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0; }
  .ntm-mtab.on { background: var(--green); color: #fff; box-shadow: 0 3px 10px rgba(3,128,71,.25); }
  .ntm-mtab .ntm-mtab-badge {
    font-size: 10px; font-weight: 700; padding: 1px 7px;
    border-radius: 10px; background: rgba(0,0,0,.1);
    color: inherit; display: none;
  }
  .ntm-mtab.on .ntm-mtab-badge { background: rgba(255,255,255,.25); color: #fff; display: inline; }
  .ntm-mtab-content { display: none !important; }
  .ntm-mtab-content.on { display: block !important; }

  /* Controls */
  .ntm-ctrl {
    background: #fff; border: 1px solid var(--border); border-radius: 12px;
    padding: 11px 16px; margin-bottom: 16px;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
  }
  .ntm-vtog { display: flex; background: #f1f5f9; border-radius: 8px; padding: 3px; gap: 2px; }
  .ntm-vbtn {
    padding: 6px 13px; border: none; border-radius: 6px;
    background: transparent; font-family: 'Poppins',sans-serif;
    font-size: 12px; font-weight: 600; color: var(--muted);
    cursor: pointer; transition: all .15s; display: flex; align-items: center; gap: 5px;
  }
  .ntm-vbtn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2; }
  .ntm-vbtn.on { background: #fff; color: var(--green); box-shadow: 0 1px 4px rgba(0,0,0,.1); }
  .ntm-sml {
    padding: 7px 11px; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 500;
    color: var(--text); background: #fff; outline: none; cursor: pointer;
  }
  .ntm-srch {
    padding: 7px 13px; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px;
    color: var(--text); background: #f8fafc; outline: none; width: 190px;
  }
  .ntm-srch:focus { border-color: var(--green); background: #fff; }
  .ntm-srch::placeholder { color: #94a3b8; }
  .ntm-hint {
    font-size: 11px; color: var(--muted); padding: 3px 8px;
    background: #f8fafc; border-radius: 6px; border: 1px solid var(--border);
  }
  .ntm-hl-tog {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 600;
    color: var(--green); background: #f0fdf4; cursor: pointer; transition: all .15s;
  }
  .ntm-hl-tog:hover { background: #dcfce7; border-color: var(--green); }
  .ntm-hl-tog.off { color: var(--muted); background: #f8fafc; border-color: var(--border); }
  .ntm-rlbtn {
    width: 31px; height: 31px; border-radius: 8px; border: 1px solid var(--border);
    background: #fff; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .18s; color: var(--muted);
  }
  .ntm-rlbtn:hover { border-color: var(--green); color: var(--green); background: #f0fdf4; }
  .ntm-rlbtn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.5; }
  .ntm-rlbtn.spin svg { animation: ntmSpin .7s linear infinite; }
  @keyframes ntmSpin { to { transform: rotate(360deg); } }

  /* Loading */
  .ntm-lding { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 420px; gap: 16px; }
  .ntm-spinner { width: 42px; height: 42px; border: 3px solid rgba(3,128,71,.15); border-top-color: var(--green); border-radius: 50%; animation: ntmSpin .85s linear infinite; }
  .ntm-lding p { font-size: 13px; color: var(--muted); font-weight: 500; }
  .ntm-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 320px; gap: 12px; }
  .ntm-empty svg { width:48px;height:48px;stroke:#cbd5e1;fill:none;stroke-width:1.5; }
  .ntm-empty h4 { font-size:15px;font-weight:700;color:#64748b;margin:0; }
  .ntm-empty p { font-size:13px;color:#94a3b8;margin:0;text-align:center; }

  /* Treemap */
  .ntm-tmap-card {
    background: #fff; border: 1px solid var(--border); border-radius: 14px;
    overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.07);
  }
  .ntm-tmap-header {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px; border-bottom: 1px solid var(--border); background: #f8fafc;
  }
  .ntm-source-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; background: var(--green); color: #fff; }
  .ntm-tmap-btn {
    font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 6px;
    background: #e2e8f0; color: var(--text); border: none; cursor: pointer;
    font-family: 'Poppins',sans-serif;
  }
  .ntm-tmap-btn:hover { background: #cbd5e1; }
  #ntmTreemap { position: relative; width: 100%; overflow: hidden; }

  .ntm-tile {
    position: absolute; overflow: hidden; cursor: pointer;
    box-sizing: border-box; transition: filter .15s;
    border: 1px solid rgba(255,255,255,.07);
  }
  .ntm-tile:hover { filter: brightness(1.18); z-index: 5; }
  .ntm-tile-in { padding: 6px 8px; height: 100%; display: flex; flex-direction: column; gap: 1px; }
  .ntm-tile-cat {
    font-size: 9px; font-weight: 800; text-transform: uppercase;
    letter-spacing: .6px; opacity: .75; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis; flex-shrink: 0; color: rgba(255,255,255,.8);
  }
  .ntm-tile-hl {
    font-weight: 700; line-height: 1.25; flex: 1;
    overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; color: #fff;
  }

  .c0{background:#7f1d1d} .c1{background:#991b1b} .c2{background:#b91c1c}
  .c3{background:#c2410c} .c4{background:#92400e} .c5{background:#78350f}
  .c6{background:#166534} .c7{background:#14532d} .c8{background:#064e3b}
  .c9{background:#1e3a5f} .c10{background:#1e40af} .c11{background:#4338ca}
  .c12{background:#5b21b6} .c13{background:#6b21a8} .c14{background:#86198f}
  .c15{background:#9d174d} .c16{background:#500724} .c17{background:#042f2e}
  .c18{background:#082f49} .c19{background:#1c1917}

  /* List & Bubble */
  .ntm-list-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
  .ntm-li { display: flex; align-items: center; gap: 14px; padding: 13px 20px; border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background .14s; }
  .ntm-li:last-child { border-bottom: none; }
  .ntm-li:hover { background: #f8fafc; }
  .ntm-li-rnk { width:29px;height:29px;border-radius:8px;flex-shrink:0;background:linear-gradient(135deg,var(--green),var(--green-dark));color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700; }
  .ntm-li-rnk.g{background:linear-gradient(135deg,#f59e0b,#d97706)} .ntm-li-rnk.s{background:linear-gradient(135deg,#94a3b8,#64748b)} .ntm-li-rnk.b{background:linear-gradient(135deg,#cd7c2f,#a35c1f)}
  .ntm-li-dot { width:11px;height:11px;border-radius:3px;flex-shrink:0; }
  .ntm-li-info { flex:1;min-width:0; }
  .ntm-li-topic { font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px; }
  .ntm-li-bar { height:5px;background:#f1f5f9;border-radius:10px;overflow:hidden;width:150px;flex-shrink:0; }
  .ntm-li-fill { height:100%;background:linear-gradient(90deg,var(--green),var(--green-light));border-radius:10px;transition:width .8s cubic-bezier(.4,0,.2,1); }
  .ntm-li-cnt { font-size:15px;font-weight:700;color:var(--green);text-align:right;min-width:38px; }
  .ntm-li-lbl { font-size:10px;color:var(--muted);text-align:right; }

  .ntm-bub-card { background:#fff;border:1px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07);height:580px; }
  .ntm-bub-card svg { width:100%;height:100%; }

  /* Detail panels */
  .ntm-detail-wrap {
    background: #fff; border: 1px solid var(--border); border-radius: 14px;
    overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.07);
    animation: ntmFI .22s ease-out;
  }
  .ntm-detail-hdr {
    padding: 12px 20px; border-bottom: 1px solid var(--border);
    background: #f8fafc; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
  }
  .ntm-detail-col { width: 13px; height: 13px; border-radius: 3px; flex-shrink: 0; }
  .ntm-detail-hdr h3 { font-size: 16px; font-weight: 700; color: var(--text); flex: 1; margin: 0; }
  .ntm-detail-badge {
    background: #f0fdf4; color: var(--green); padding: 3px 12px;
    border-radius: 20px; font-size: 11px; font-weight: 700; border: 1px solid #bbf7d0;
  }
  .ntm-detail-hint { font-size: 11px; color: var(--muted); }
  .ntm-detail-back {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
    background: #f1f5f9; border: 1px solid var(--border); border-radius: 8px;
    font-family: 'Poppins',sans-serif; font-size: 12px; font-weight: 600;
    color: var(--muted); cursor: pointer; transition: all .15s; margin-left: auto;
  }
  .ntm-detail-back:hover { background: #e2e8f0; color: var(--text); }
  .ntm-detail-back svg { width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2.5; }
  .ntm-detail-body {
    display: flex; overflow: hidden;
    height: calc(100vh - 340px); min-height: 480px;
  }
  .ntm-p-pub {
    width: 230px; flex-shrink: 0; border-right: 1px solid var(--border);
    overflow-y: auto; background: #fff; display: flex; flex-direction: column;
  }
  .ntm-p-pub-hdr {
    padding: 9px 14px; font-size: 11px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border); background: #f8fafc;
    position: sticky; top: 0; z-index: 1; flex-shrink: 0;
  }
  .ntm-pub-row {
    display: flex; align-items: center; padding: 9px 14px;
    border-bottom: 1px solid #f8fafc; cursor: pointer; transition: background .1s; gap: 8px;
  }
  .ntm-pub-row:hover { background: #f0fdf4; }
  .ntm-pub-row.act { background: #dcfce7; border-left: 3px solid var(--green); padding-left: 11px; }
  .ntm-pub-row.act .ntm-pub-name { color: var(--green); font-weight: 700; }
  .ntm-pub-name { font-size: 12px; font-weight: 500; color: var(--text); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .ntm-pub-docs { font-size: 10px; font-weight: 700; color: #fff; background: var(--green); padding: 1px 6px; border-radius: 9px; flex-shrink: 0; }
  .ntm-pub-arr { color: #cbd5e1; font-size: 13px; flex-shrink: 0; }
  .ntm-pub-row.act .ntm-pub-arr { color: var(--green); }
  .ntm-p-kw {
    width: 290px; flex-shrink: 0; border-right: 1px solid var(--border);
    overflow-y: auto; background: #fff; display: flex; flex-direction: column;
  }
  .ntm-p-kw-hdr {
    padding: 9px 14px; font-size: 11px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border); background: #f8fafc;
    position: sticky; top: 0; z-index: 1; flex-shrink: 0;
  }
  .ntm-kw-tbl { width: 100%; border-collapse: collapse; }
  .ntm-kw-tbl thead th {
    padding: 8px 12px; font-size: 11px; font-weight: 700; color: var(--muted);
    text-align: left; border-bottom: 2px solid var(--border);
    background: #f8fafc; position: sticky; top: 0;
  }
  .ntm-kw-tbl thead th:last-child { text-align: right; }
  .ntm-kw-tbl tbody tr:hover { background: #f8fafc; }
  .ntm-kw-tbl tbody tr { border-bottom: 1px solid #f1f5f9; }
  .ntm-kw-no  { width:34px;padding:7px 6px 7px 12px;font-size:11px;color:#94a3b8;font-weight:600; }
  .ntm-kw-w   { padding:7px 8px;font-size:13px;font-weight:500;color:var(--text); }
  .ntm-kw-f   { padding:7px 12px 7px 8px;font-size:13px;font-weight:700;color:var(--text);text-align:right; }
  .ntm-p-art { flex: 1; overflow-y: auto; background: #fff; }
  .ntm-p-art-hdr {
    padding: 9px 16px; font-size: 11px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border); background: #f8fafc;
    position: sticky; top: 0; z-index: 1;
  }
  .ntm-ac {
    padding: 14px 18px; border-bottom: 1px solid #f1f5f9;
    transition: background .1s; display: flex; gap: 14px;
  }
  .ntm-ac:hover { background: #f8fafc; }
  .ntm-ac-body { flex: 1; min-width: 0; }
  .ntm-ac-src  { font-size: 11px; font-weight: 800; color: var(--green); text-transform: uppercase; margin-bottom: 3px; }
  .ntm-ac-date { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
  .ntm-ac-title {
    font-size: 14px; font-weight: 700; color: #b45309;
    text-decoration: none; line-height: 1.4; display: block; margin-bottom: 5px;
  }
  .ntm-ac-title:hover { color: #92400e; text-decoration: underline; }
  .ntm-ac-snippet { font-size: 12px; color: var(--muted); line-height: 1.55; }
  .ntm-ac-thumb {
    width: 80px; height: 60px; border-radius: 8px; object-fit: cover;
    flex-shrink: 0; background: #f1f5f9;
  }
  .ntm-detail-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    height: 100%; min-height: 320px; gap: 12px; padding: 40px;
  }
  .ntm-detail-empty svg { width:52px;height:52px;stroke:#cbd5e1;fill:none;stroke-width:1.5; }
  .ntm-detail-empty h4 { font-size:15px;font-weight:700;color:#64748b;margin:0; }
  .ntm-detail-empty p { font-size:13px;color:#94a3b8;margin:0;text-align:center;max-width:300px; }

  .ntm-tip { position:fixed;background:#1e293b;color:#fff;padding:7px 12px;border-radius:8px;font-size:12px;font-weight:500;pointer-events:none;opacity:0;transition:opacity .12s;z-index:99999;max-width:280px;white-space:normal;box-shadow:0 6px 20px rgba(0,0,0,.25); }
  .ntm-tip.on { opacity:1; }
  .ntm-tip b { color:#4ade80; }

  @keyframes ntmFI { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }
  .ntm-fi { animation: ntmFI .26s ease-out both; }

  @media(max-width:768px){
    .ntm-wrap{padding:14px;}
    .ntm-p-pub{width:160px;}
    .ntm-p-kw{width:200px;}
    .ntm-li-bar{display:none;}
    .ntm-detail-body{height:calc(100vh - 280px);}
  }
  @media(max-width:560px){
    .ntm-p-pub,.ntm-p-kw{display:none;}
  }
</style>
@endsection

@section('content')
<div class="ntm-wrap">
  <div class="ntm-page-hdr">
    <h1>News Topic Map</h1>
    <p>Visual map of the most-discussed topics in online news articles</p>
  </div>

  @if(!$projectId)
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:12px;padding:16px 20px;color:#92400e;font-size:13px;font-weight:500;">
      No project selected. Please choose a project from the sidebar.
    </div>
  @else

  <!-- Filter -->
  <div class="ntm-filter">
    <span class="ntm-filter-lbl">Date Range</span>
    <button type="button" class="ntm-date-trigger" id="ntmDateBtn">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span id="ntmDateSpan">{{ $startDate }} – {{ $endDate }}</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <button class="ntm-apply" onclick="ntmLoad()">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Apply
    </button>
  </div>

  <!-- DATE PICKER MODAL -->
  <div class="ntm-dp-modal" id="ntmDpModal">
    <div class="ntm-dp-overlay" onclick="NTMDp.close()"></div>
    <div class="ntm-dp-container">
      <div class="ntm-dp-sidebar">
        <button class="ntm-dp-preset" data-p="today">Today</button>
        <button class="ntm-dp-preset" data-p="yesterday">Yesterday</button>
        <button class="ntm-dp-preset" data-p="last7">Last 7 Days</button>
        <button class="ntm-dp-preset" data-p="last14">Last 14 Days</button>
        <button class="ntm-dp-preset" data-p="last30">Last 30 Days</button>
        <button class="ntm-dp-preset" data-p="last60">Last 60 Days</button>
        <button class="ntm-dp-preset" data-p="last90">Last 90 Days</button>
        <button class="ntm-dp-preset" data-p="thismonth">This Month</button>
        <button class="ntm-dp-preset" data-p="lastmonth">Last Month</button>
        <button class="ntm-dp-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="ntm-dp-content">
        <div class="ntm-dp-cals-wrap">
          <button class="ntm-dp-nav" onclick="NTMDp.nav(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="ntm-dp-cals">
            <div class="ntm-dp-cal" id="ntmDpCal1"></div>
            <div class="ntm-dp-cal" id="ntmDpCal2"></div>
          </div>
          <button class="ntm-dp-nav" onclick="NTMDp.nav(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="ntm-dp-display">
          <span id="ntmDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="ntm-dp-footer">
          <button class="ntm-dp-cancel" onclick="NTMDp.close()">Batal</button>
          <button class="ntm-dp-apply" onclick="NTMDp.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats removed -->

  <!-- Main Tab Bar -->
  <div class="ntm-maintabs" id="ntmMainTabs">
    <button class="ntm-mtab on" id="mtabMap" onclick="switchMainTab('map')">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="11"/><rect x="13" y="3" width="8" height="6"/><rect x="13" y="11" width="8" height="10"/><rect x="3" y="16" width="8" height="5"/></svg>
      Topic Map
    </button>
    <button class="ntm-mtab" id="mtabDetail" onclick="switchMainTab('detail')">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
      Detail
      <span class="ntm-mtab-badge" id="mtabDetailBadge">—</span>
    </button>
  </div>

  <!-- TAB 1: TOPIC MAP -->
  <div id="ntmTabMap" class="ntm-mtab-content on">
    <div class="ntm-ctrl">
      <div class="ntm-vtog">
        <button class="ntm-vbtn on" id="vbMap" onclick="ntmView('map')">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="11"/><rect x="13" y="3" width="8" height="6"/><rect x="13" y="11" width="8" height="10"/><rect x="3" y="16" width="8" height="5"/></svg>Map
        </button>
      </div>
      <button class="ntm-hl-tog" id="ntmHlTog" onclick="toggleHL()" title="Show/Hide Headline">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        Headline: <span id="ntmHlLabel">On</span>
      </button>
      <input type="checkbox" id="ntmHlChk" checked style="display:none;" onchange="ntmRender()">
      <button class="ntm-rlbtn" id="ntmRl" onclick="ntmLoad()" title="Refresh data">
        <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
      </button>
    </div>

    <div id="ntmLoading" class="ntm-lding"><div class="ntm-spinner"></div><p>Loading topic map…</p></div>

    <div class="ntm-tmap-card" id="ntmMapCard" style="display:none;">
      <div class="ntm-tmap-header">
        <span class="ntm-source-badge">Online News (Ind)</span>
        <span style="font-size:11px;color:var(--muted);margin-left:auto;" id="ntmMapMeta"></span>
      </div>
      <div id="ntmTreemap"></div>
    </div>

    <div class="ntm-list-card" id="ntmListCard" style="display:none;"><div id="ntmListInner"></div></div>
    <div class="ntm-bub-card" id="ntmBubCard" style="display:none;"><svg id="ntmBubSvg"></svg></div>
    <div class="ntm-empty" id="ntmEmpty" style="display:none;">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <h4>No topic data found</h4>
      <p>Try a wider date range or different media type.</p>
    </div>
  </div>

  <!-- TAB 2: DETAIL -->
  <div id="ntmTabDetail" class="ntm-mtab-content" style="display:none;">
    <div id="ntmDetailEmpty" class="ntm-detail-wrap">
      <div class="ntm-detail-empty">
        <svg viewBox="0 0 24 24"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/><path d="M13 13l6 6"/></svg>
        <h4>Select a topic to explore</h4>
        <p>Go to the <strong>Topic Map</strong> tab and click any topic tile, list item, or bubble to see detail here.</p>
      </div>
    </div>

    <div id="ntmDetailPanel" class="ntm-detail-wrap" style="display:none;">
      <div class="ntm-detail-hdr">
        <div class="ntm-detail-col" id="ntmDcol"></div>
        <h3 id="ntmDtitle">Topic</h3>
        <span class="ntm-detail-badge" id="ntmDbadge">0 mentions</span>
        <span class="ntm-detail-hint" id="ntmDhint"></span>
        <button class="ntm-detail-back" onclick="switchMainTab('map')">
          <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          Back to Map
        </button>
      </div>
      <div class="ntm-detail-body">
        <div class="ntm-p-pub">
          <div class="ntm-p-pub-hdr">Publishers</div>
          <div id="ntmPubList"></div>
        </div>
        <div class="ntm-p-kw">
          <div class="ntm-p-kw-hdr" id="ntmKwTitle">Keywords — All</div>
          <table class="ntm-kw-tbl">
            <thead><tr>
              <th style="width:34px">No</th>
              <th>Keyword</th>
              <th>Frequency</th>
            </tr></thead>
            <tbody id="ntmKwBody"></tbody>
          </table>
        </div>
        <div class="ntm-p-art">
          <div class="ntm-p-art-hdr" id="ntmArtTitle">Articles</div>
          <div id="ntmArtList"></div>
        </div>
      </div>
    </div>
  </div>

  @endif
</div>

<div class="ntm-tip" id="ntmTip"></div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
<script>
(function(){
'use strict';

const CFG = {
  pid:      '{{ $projectId ?? "" }}',
  s:        '{{ $startDate ?? "" }}',
  e:        '{{ $endDate ?? "" }}',
  topicApi: '/mk/api/topic-map',
  artApi:   '/mk/api/news/articles',
  pubApi:   '/mk/api/news/top-publisher',
};

// Drone Emprit style: monochromatic dark red/maroon palette
// variasi kecerahan untuk membedakan tiles, bukan warna berbeda
const PAL = [
  '#7f1d1d','#881e1e','#921f1f','#9b2020','#a52121',
  '#6b1a1a','#5c1616','#731c1c','#7f1d1d','#8b1e1e',
  '#991b1b','#a31c1c','#6f1c1c','#7a1c1c','#851d1d',
  '#601818','#6e1919','#7c1c1c','#8a1e1e','#961f1f',
];

let D  = { raw:[], fil:[], arts:[], allPubs:[], view:'map', busy:false };
let DS = { topic:null, topicIdx:0, arts:[], pubArts:{}, activePub:null };

const $ = id => document.getElementById(id);
const tip = $('ntmTip');

function showTip(e,h){ tip.innerHTML=h; tip.classList.add('on'); moveTip(e); }
function moveTip(e){ tip.style.left=(e.clientX+14)+'px'; tip.style.top=(e.clientY-50)+'px'; }
function hideTip(){ tip.classList.remove('on'); }
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function trunc(s,n){ return s&&s.length>n?s.slice(0,n-1)+'…':s; }

const PANELS=['ntmLoading','ntmMapCard','ntmListCard','ntmBubCard','ntmEmpty'];
function showOnly(id){ PANELS.forEach(p=>$(p).style.display='none'); if(id) $(id).style.display='block'; }
function showLd(){ PANELS.forEach(p=>$(p).style.display='none'); $('ntmLoading').style.display='flex'; }

/* ════════════════════════════════════
   MAIN TAB SWITCH
════════════════════════════════════ */
window.switchMainTab = function(tab){
  ['map','detail'].forEach(t => {
    const btn = $('mtab' + t.charAt(0).toUpperCase() + t.slice(1));
    const cnt = $('ntmTab' + t.charAt(0).toUpperCase() + t.slice(1));
    if(btn) btn.classList.toggle('on', t === tab);
    if(cnt){
      cnt.classList.toggle('on', t === tab);
      cnt.style.display = (t === tab) ? 'block' : 'none';
    }
  });
  if(tab === 'map' && D.raw.length){
    setTimeout(()=>{
      if(D.view === 'map')    doMap(D.fil);
      if(D.view === 'bubble') doBub(D.fil);
    }, 50);
  }
};

/* ════════════════════════════════════
   FIND HEADLINE — SCORED + DEDUP
   Setiap artikel hanya bisa dipakai oleh
   1 topic (yang paling cocok).
   usedHeadlines = Set of article titles
   yang sudah dipakai topic lain.
════════════════════════════════════ */
function findHeadline(topic) {
  return findHeadlineUnique(topic, new Set());
}

function findHeadlineUnique(topic, usedSet) {
  const q     = topic.toLowerCase();
  const words = q.split(/\s+/).filter(w => w.length > 2);

  let bestArt = null, bestScore = 0;

  D.arts.forEach(a => {
    const titleRaw = a.title || '';
    const title    = titleRaw.toLowerCase();
    const content  = (a.content || a.description || '').toLowerCase().slice(0, 400);

    // Skip artikel yang sudah dipakai topic lain
    if(usedSet.has(titleRaw)) return;

    let score = 0;

    // Exact phrase match di title → skor tertinggi
    if(title.includes(q)) score += 100;

    // Semua kata topic ada di title
    if(words.length > 1 && words.every(w => title.includes(w))) score += 60;

    // Partial word match di title
    words.forEach(w => { if(title.includes(w)) score += 15; });

    // Match di content (bobot lebih rendah)
    if(content.includes(q)) score += 20;
    words.forEach(w => { if(content.includes(w)) score += 3; });

    // Prefer title lebih pendek kalau score sama (lebih spesifik)
    if(score > bestScore || (score === bestScore && bestArt && titleRaw.length < bestArt.title.length)){
      bestScore = score;
      bestArt   = a;
    }
  });

  // Threshold minimum 15 — kalau lemah jangan tampilkan
  if(bestScore >= 15 && bestArt){
    usedSet.add(bestArt.title || '');
    return bestArt.title || '';
  }
  return '';
}

/* ════════════════════════════════════
   FETCH ALL ARTICLES — pagination 500/batch
   Karena API MK default limit ~100 rows,
   kita loop sampai habis
════════════════════════════════════ */
async function fetchAllArticles(){
  const batchSize = 500;
  let allArts = [], start = 0, hasMore = true;

  while(hasMore){
    try {
      const res  = await fetch(
        `${CFG.artApi}?project_id=${CFG.pid}&media=doc` +
        `&start_date=${CFG.s}&end_date=${CFG.e}` +
        `&rows=${batchSize}&start=${start}`
      );
      const json = await res.json();
      const batch = json.data || (Array.isArray(json) ? json : []);

      if(!batch.length){ hasMore = false; break; }

      allArts = allArts.concat(batch);

      // Stop kalau dapat lebih sedikit dari batchSize → sudah habis
      if(batch.length < batchSize) hasMore = false;
      else start += batchSize;

      // Safety cap: 5000 artikel cukup untuk headline scoring
      if(allArts.length >= 5000) hasMore = false;

    } catch(e){
      console.warn('[NTM] fetchAllArticles batch error', e);
      hasMore = false;
    }
  }

  return allArts;
}

/* ════════════════════════════════════
   LOAD — D.arts diisi DULU (semua artikel),
   lalu findHeadline() dengan dedup per-topic
════════════════════════════════════ */
window.ntmLoad = async function(){
  if(D.busy) return; D.busy=true;
  const rl=$('ntmRl'); rl&&rl.classList.add('spin');
  switchMainTab('map');
  showLd();

  try {
    // Fetch topic map + semua artikel (paginated) + publishers secara paralel
    const [topicRes, pubRes, allArts] = await Promise.all([
      fetch(`${CFG.topicApi}?project_id=${CFG.pid}&media=doc&start_date=${CFG.s}&end_date=${CFG.e}`),
      fetch(`${CFG.pubApi}?project_id=${CFG.pid}&start_date=${CFG.s}&end_date=${CFG.e}&news_type=article`).catch(()=>null),
      fetchAllArticles(),
    ]);

    const topicJson = await topicRes.json();
    const pubJson   = pubRes ? await pubRes.json().catch(()=>null) : null;

    // ✅ ISI D.arts DULU — sudah semua artikel (bukan cuma 100)
    D.arts = allArts;

    // Publisher data
    if(pubJson && pubJson.success && Array.isArray(pubJson.data) && pubJson.data.length){
      D.allPubs = pubJson.data;
    } else {
      const pm = {};
      D.arts.forEach(a => {
        const p = a.publisher || extractHostname(a.url||'') || 'Unknown';
        pm[p] = (pm[p]||0)+1;
      });
      D.allPubs = Object.entries(pm)
        .map(([domain,count],i)=>({rank:i+1, domain, count}))
        .sort((a,b)=>b.count-a.count);
    }

    // Parse topic data
    let raw = {};
    if(topicJson.data && !Array.isArray(topicJson.data) && typeof topicJson.data==='object') raw=topicJson.data;
    else if(Array.isArray(topicJson.data)) topicJson.data.forEach(item=>{ raw[item.name||item.topic||'']={num_docs:item.weight||item.num_docs||1}; });
    else if(Array.isArray(topicJson)) topicJson.forEach(item=>{ raw[item.name||item.topic||'']={num_docs:item.weight||item.num_docs||1}; });
    else if(typeof topicJson==='object'&&!topicJson.success&&!topicJson.error) raw=topicJson;

    // ✅ Build D.raw SETELAH D.arts terisi + headline dedup
    // Setiap artikel hanya bisa jadi headline untuk 1 topic (yang paling cocok)
    const usedHeadlines = new Set();

    const topicsRanked = Object.entries(raw)
      .map(([topic,val])=>({
        topic,
        count: typeof val==='object'?(val.num_docs||val.count||1):Number(val),
      }))
      .filter(d=>d.topic&&d.count>0)
      .sort((a,b)=>b.count-a.count);

    D.raw = topicsRanked.map((d,i)=>({
      ...d,
      headline: findHeadlineUnique(d.topic, usedHeadlines),
      color: PAL[i % PAL.length],
    }));

    if(!D.raw.length){ showOnly('ntmEmpty'); return; }
    ntmRender();

    if(D.raw[0]) openDetailSilent(D.raw[0], 0);

  } catch(err){
    console.error('[NTM]', err);
    showOnly('ntmEmpty');
  } finally {
    D.busy=false; rl&&rl.classList.remove('spin');
  }
};

function extractHostname(url){
  try { return new URL(url).hostname.replace('www.',''); } catch(e){ return ''; }
}

/* updateStats removed */

/* ════════════════════════════════════
   RENDER
════════════════════════════════════ */
window.ntmRender = function(){
  let data = [...D.raw];
  D.fil = data;
  if(!data.length){ showOnly('ntmEmpty'); return; }
  doMap(data);
};

window.ntmView = function(v){
  D.view=v;
  ['Map','List','Bubble'].forEach(k=>{
    const btn=$('vb'+k); btn&&btn.classList.toggle('on',k.toLowerCase()===v);
  });
  if(D.raw.length) ntmRender();
};

window.toggleHL = function(){
  const c=$('ntmHlChk'); if(!c) return;
  c.checked = !c.checked;
  const tog = $('ntmHlTog');
  const lbl = $('ntmHlLabel');
  if(tog) tog.classList.toggle('off', !c.checked);
  if(lbl) lbl.textContent = c.checked ? 'On' : 'Off';
  ntmRender();
};

/* ════════════════════════════════════
   MAP VIEW — tile seperti Drone Emprit:
   topic kecil di atas, headline besar di bawah
   Kalau tidak ada headline relevan → topic
   name diperbesar jadi judul utama
════════════════════════════════════ */
function doMap(data){
  showOnly('ntmMapCard');
  const showHL = $('ntmHlChk')?.checked !== false;
  const wrap   = $('ntmTreemap');
  wrap.innerHTML = '';

  const W = wrap.offsetWidth || 900;
  const H = Math.min(680, Math.max(560, window.innerHeight * 0.72));
  wrap.style.height = H+'px';

  $('ntmMapMeta').textContent = `${data.length} topics · ${CFG.s} to ${CFG.e}`;

  const root = d3.hierarchy({children:data}).sum(d=>d.count||1);
  d3.treemap()
    .tile(d3.treemapBinary)   // ← binary tiling: lebih mirip Drone Emprit
    .size([W,H])
    .padding(2)
    .round(true)(root);

  root.leaves().forEach((node,i)=>{
    const d  = node.data;
    const tW = node.x1-node.x0;
    const tH = node.y1-node.y0;

    // Skip tile terlalu kecil
    if(tW < 40 || tH < 30) return;

    const tile = document.createElement('div');
    tile.className = 'ntm-tile ntm-fi';
    tile.style.cssText = `left:${node.x0}px;top:${node.y0}px;width:${tW}px;height:${tH}px;` +
      `animation-delay:${i*8}ms;background:${d.color};`;

    // Font sizes responsif terhadap ukuran tile
    const catFs   = Math.max(7, Math.min(10, tW / 18));
    const hlFs    = Math.max(10, Math.min(28, tW / 8));
    const hlLines = Math.max(1, Math.floor((tH - catFs * 2 - 14) / (hlFs * 1.3)));

    let hlHtml = '';
    if(tH > 50){
      if(showHL && d.headline){
        hlHtml = `<div class="ntm-tile-hl" style="font-size:${hlFs}px;-webkit-line-clamp:${hlLines};">${esc(d.headline)}</div>`;
      } else {
        // Tidak ada headline → topic name besar sebagai judul utama
        const topicFs = Math.max(11, Math.min(26, tW / 9));
        hlHtml = `<div class="ntm-tile-hl" style="font-size:${topicFs}px;-webkit-line-clamp:${hlLines};font-weight:800;">${esc(d.topic)}</div>`;
      }
    }

    tile.innerHTML = `<div class="ntm-tile-in">
      <div class="ntm-tile-cat" style="font-size:${catFs}px;">${esc(d.topic)}</div>
      ${hlHtml}
    </div>`;

    tile.addEventListener('mouseenter', e=>showTip(e,`<b>${esc(d.topic)}</b><br><small>${d.count} articles</small>`));
    tile.addEventListener('mousemove', moveTip);
    tile.addEventListener('mouseleave', hideTip);
    tile.addEventListener('click', ()=>{ hideTip(); openDetail(d,i); });
    wrap.appendChild(tile);
  });
}

/* ════════════════════════════════════
   LIST VIEW
════════════════════════════════════ */
function doList(data){
  showOnly('ntmListCard');
  const max = data[0].count;
  let h = '';
  data.forEach((d,i)=>{
    const pct = Math.round(d.count/max*100);
    const rc  = i===0?'g':i===1?'s':i===2?'b':'';
    h += `<div class="ntm-li ntm-fi" style="animation-delay:${i*12}ms;" onclick="openDetail(ntmGD('${d.topic.replace(/'/g,"\\'").replace(/\\/g,"\\\\")}'),${i})">
      <div class="ntm-li-rnk ${rc}">${i+1}</div>
      <div class="ntm-li-dot" style="background:${d.color};"></div>
      <div class="ntm-li-info">
        <div class="ntm-li-topic">${esc(d.topic)}</div>
        <div class="ntm-li-bar"><div class="ntm-li-fill" style="width:0%" data-p="${pct}"></div></div>
      </div>
      <div><div class="ntm-li-cnt">${d.count}</div><div class="ntm-li-lbl">articles</div></div>
    </div>`;
  });
  $('ntmListInner').innerHTML = h;
  requestAnimationFrame(()=>requestAnimationFrame(()=>{
    $('ntmListInner').querySelectorAll('.ntm-li-fill').forEach((b,i)=>{
      setTimeout(()=>{ b.style.width=b.dataset.p+'%'; }, i*12+60);
    });
  }));
}

/* ════════════════════════════════════
   BUBBLE VIEW
════════════════════════════════════ */
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

  function lighten(hex,a){
    const n=parseInt(hex.replace('#',''),16);
    return `rgb(${Math.min(255,((n>>16)&0xff)+Math.round(255*a))},${Math.min(255,((n>>8)&0xff)+Math.round(255*a))},${Math.min(255,(n&0xff)+Math.round(255*a))})`;
  }

  root.leaves().forEach((node,i)=>{
    const gid=`bg${i}`, base=PAL[i%PAL.length];
    const gr=defs.append('radialGradient').attr('id',gid).attr('cx','35%').attr('cy','35%');
    gr.append('stop').attr('offset','0%').attr('stop-color',lighten(base,.28));
    gr.append('stop').attr('offset','100%').attr('stop-color',base);
    node._gid=gid; node._base=base;
  });

  const nodes=svg.selectAll('g').data(root.leaves()).enter().append('g')
    .attr('transform',d=>`translate(${d.x},${d.y})`).style('cursor','pointer');

  nodes.append('circle').attr('r',0)
    .attr('fill',d=>`url(#${d._gid})`).attr('stroke',d=>d._base).attr('stroke-width',1.5).attr('stroke-opacity',.4)
    .transition().duration(500).delay((_,i)=>i*12).ease(d3.easeBounceOut).attr('r',d=>d.r);

  nodes.filter(d=>d.r>20).append('text')
    .attr('text-anchor','middle').attr('dy',d=>d.r>36?'-0.3em':'0.35em')
    .attr('font-family','Poppins,sans-serif').attr('font-weight','700').attr('fill','#fff')
    .attr('font-size',d=>Math.max(9,Math.min(14,d.r*.32))+'px')
    .text(d=>trunc(d.data.topic,Math.floor(d.r/5)))
    .style('opacity',0).transition().duration(300).delay((_,i)=>i*12+350).style('opacity',1);

  nodes.filter(d=>d.r>38).append('text')
    .attr('text-anchor','middle').attr('dy','1.1em')
    .attr('font-family','Poppins,sans-serif').attr('font-weight','500').attr('fill','rgba(255,255,255,.7)')
    .attr('font-size',d=>Math.max(8,Math.min(11,d.r*.21))+'px')
    .text(d=>d.data.count+' articles')
    .style('opacity',0).transition().duration(300).delay((_,i)=>i*12+400).style('opacity',1);

  nodes
    .on('mouseenter',(e,d)=>{ showTip(e,`<b>${d.data.topic}</b><br><small>${d.data.count} articles</small>`); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.9).attr('stroke-width',2.5); })
    .on('mousemove',moveTip)
    .on('mouseleave',e=>{ hideTip(); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.4).attr('stroke-width',1.5); })
    .on('click',(e,d)=>openDetail(d.data, D.raw.indexOf(d.data)));
}

/* ════════════════════════════════════
   OPEN DETAIL
════════════════════════════════════ */
window.ntmGD = function(topic){ return D.fil.find(d=>d.topic===topic)||D.raw.find(d=>d.topic===topic)||{topic,count:0}; };

function buildDetailData(d, idx){
  DS.topic     = d;
  DS.topicIdx  = idx;
  DS.activePub = null;

  const q = d.topic.toLowerCase();
  DS.arts = D.arts.filter(a =>
    ((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase().includes(q)
  );

  DS.pubArts = {};
  DS.arts.forEach(a => {
    const pub = a.publisher || extractHostname(a.url||'') || 'Unknown';
    if(!DS.pubArts[pub]) DS.pubArts[pub] = [];
    DS.pubArts[pub].push(a);
  });

  if(!Object.keys(DS.pubArts).length && D.allPubs.length){
    D.allPubs.forEach(p => { DS.pubArts[p.domain] = []; });
  }

  const badge = $('mtabDetailBadge');
  if(badge) badge.textContent = d.topic.length > 20 ? d.topic.slice(0,18)+'…' : d.topic;

  $('ntmDcol').style.background = PAL[idx%PAL.length];
  $('ntmDtitle').textContent    = d.topic;

  const artCount = DS.arts.length;
  const pubCount = Object.keys(DS.pubArts).filter(k => DS.pubArts[k].length > 0).length;
  $('ntmDbadge').textContent = artCount > 0 ? `${artCount} artikel` : `${d.count} mentions`;
  $('ntmDhint').textContent  = artCount > 0
    ? `${pubCount} publisher · topic score: ${d.count}`
    : `topic score: ${d.count} · artikel tidak ditemukan di cache`;

  $('ntmDetailEmpty').style.display  = 'none';
  $('ntmDetailPanel').style.display  = 'block';

  renderPubPanel();
  renderKwAll();
  renderArts(DS.arts, 'All Publishers');
}

function openDetailSilent(d, idx){
  if(!d) return;
  buildDetailData(d, idx);
}

window.openDetail = function(d, idx){
  if(!d) return;
  buildDetailData(d, idx);
  switchMainTab('detail');
  setTimeout(()=>{
    const el = $('ntmTabDetail');
    if(el) el.scrollIntoView({behavior:'smooth', block:'start'});
  }, 80);
};

/* ── Publisher Panel ── */
function renderPubPanel(){
  let pubs = Object.entries(DS.pubArts)
    .filter(([, arts]) => arts.length > 0)
    .map(([domain, arts]) => ({ domain, count: arts.length }))
    .sort((a,b) => b.count - a.count);

  if(!pubs.length){
    pubs = D.allPubs.slice(0,30).map(p=>({ domain: p.domain, count: p.count, isGlobal: true }));
  }

  let h = '';
  pubs.forEach((p) => {
    const isAct  = DS.activePub === p.domain;
    const label  = p.isGlobal ? `${p.count} docs` : `${p.count} artikel`;
    h += `<div class="ntm-pub-row${isAct?' act':''}" onclick="selectPub(${JSON.stringify(p.domain)})">
      <span class="ntm-pub-name" title="${esc(p.domain)}">${esc(p.domain)}</span>
      <span class="ntm-pub-docs">${label}</span>
      <span class="ntm-pub-arr">›</span>
    </div>`;
  });
  if(!h) h = '<div style="padding:20px 14px;font-size:12px;color:#94a3b8;text-align:center;">No publishers</div>';
  $('ntmPubList').innerHTML = h;
}

window.selectPub = function(domain){
  DS.activePub = domain;
  document.querySelectorAll('#ntmPubList .ntm-pub-row').forEach(el=>{
    const nm = el.querySelector('.ntm-pub-name');
    el.classList.toggle('act', nm && nm.getAttribute('title')===domain);
  });
  const arts = DS.pubArts[domain] || [];
  renderKwForPub(arts, domain);
  renderArts(arts, domain);
};

/* ── Keywords: All mode ── */
function renderKwAll(){
  $('ntmKwTitle').textContent = `Keywords — All Publishers`;
  const totalArts = DS.arts.length;

  if(!totalArts){
    const sorted = [...D.raw].sort((a,b)=>b.count-a.count);
    let h = '';
    sorted.forEach((kw,i) => {
      const isActive = DS.topic && kw.topic === DS.topic.topic;
      h += `<tr style="${isActive?'background:#f0fdf4;':''}">
        <td class="ntm-kw-no">${i+1}</td>
        <td class="ntm-kw-w" style="${isActive?'color:var(--green);font-weight:700;':''}">${esc(kw.topic)}${isActive?' ◀':''}</td>
        <td class="ntm-kw-f" style="${isActive?'color:var(--green);':''}">${kw.count.toLocaleString()}</td>
      </tr>`;
    });
    if(!h) h = '<tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;font-size:12px;">No keywords</td></tr>';
    $('ntmKwBody').innerHTML = h;
    return;
  }

  const kwFreq = {};
  D.raw.forEach(t => {
    const q = t.topic.toLowerCase();
    let cnt = 0;
    DS.arts.forEach(a => {
      const txt = ((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase();
      let pos = 0, found;
      while((found = txt.indexOf(q, pos)) !== -1){ cnt++; pos = found + q.length; }
    });
    if(cnt > 0) kwFreq[t.topic] = { freq: cnt, apiCount: t.count };
  });

  const sorted = Object.entries(kwFreq).sort((a,b) => b[1].freq - a[1].freq);
  let h = '';
  sorted.forEach(([kw, val], i) => {
    const isActive = DS.topic && kw === DS.topic.topic;
    h += `<tr style="${isActive?'background:#f0fdf4;':''}">
      <td class="ntm-kw-no">${i+1}</td>
      <td class="ntm-kw-w" style="${isActive?'color:var(--green);font-weight:700;':''}">${esc(kw)}${isActive?' ◀':''}</td>
      <td class="ntm-kw-f" style="${isActive?'color:var(--green);':''}">${val.freq}</td>
    </tr>`;
  });
  if(!h) h = '<tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;font-size:12px;">No keywords found</td></tr>';
  $('ntmKwBody').innerHTML = h;

  setTimeout(()=>{
    const activeRow = $('ntmKwBody')?.querySelector('tr[style*="#f0fdf4"]');
    if(activeRow) activeRow.scrollIntoView({block:'center', behavior:'smooth'});
  }, 100);
}

/* ── Keywords: per-publisher mode ── */
function renderKwForPub(arts, domain){
  $('ntmKwTitle').textContent = `Keywords — ${domain}`;
  if(!arts.length){
    $('ntmKwBody').innerHTML = '<tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;font-size:12px;">No articles from this publisher for this topic</td></tr>';
    return;
  }
  const kwFreq = {};
  D.raw.forEach(t => {
    const q = t.topic.toLowerCase();
    let cnt = 0;
    arts.forEach(a => {
      const txt = ((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase();
      let pos = 0, found;
      while((found = txt.indexOf(q, pos)) !== -1){ cnt++; pos = found + q.length; }
    });
    if(cnt > 0) kwFreq[t.topic] = cnt;
  });
  const sorted = Object.entries(kwFreq).sort((a,b)=>b[1]-a[1]);
  let h = '';
  sorted.forEach(([kw, cnt], i) => {
    const isActive = DS.topic && kw === DS.topic.topic;
    h += `<tr style="${isActive?'background:#f0fdf4;':''}">
      <td class="ntm-kw-no">${i+1}</td>
      <td class="ntm-kw-w" style="${isActive?'color:var(--green);font-weight:700;':''}">${esc(kw)}${isActive?' ◀':''}</td>
      <td class="ntm-kw-f" style="${isActive?'color:var(--green);':''}">${cnt}</td>
    </tr>`;
  });
  if(!h) h = '<tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;font-size:12px;">No keyword match for this publisher</td></tr>';
  $('ntmKwBody').innerHTML = h;
}

/* ── Articles Panel ── */
function renderArts(arts, pubLabel){
  const total = arts.length;
  $('ntmArtTitle').textContent = `Articles — ${pubLabel} (${total})`;

  if(!total){
    $('ntmArtList').innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;
        padding:48px 24px;gap:12px;color:#94a3b8;">
        <svg viewBox="0 0 24 24" style="width:40px;height:40px;stroke:#cbd5e1;fill:none;stroke-width:1.5;">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
        <div style="font-size:13px;font-weight:600;color:#64748b;">No articles found</div>
        <div style="font-size:12px;text-align:center;">
          No articles from this publisher matching "<strong>${esc(DS.topic?.topic||'')}</strong>"
        </div>
      </div>`;
    return;
  }

  let h = `<div style="padding:8px 18px 4px;font-size:11px;color:#94a3b8;border-bottom:1px solid #f1f5f9;">
    ${total} artikel ditemukan
  </div>`;

  arts.forEach((a, i) => {
    const src     = a.publisher || extractHostname(a.url||'') || 'Unknown';
    const url     = a.url || a.link || '#';
    const date    = a.date_created || a.published_at || a.date || '';
    const snippet = (a.content || a.description || a.summary || '')
      .replace(/<[^>]+>/g, '').trim().slice(0, 220);
    const img     = a.image || a.thumbnail || a.image_url || a.urlToImage || '';
    const title   = a.title || 'Untitled';

    h += `<div class="ntm-ac ntm-fi" style="animation-delay:${Math.min(i,30)*15}ms;">
      <div class="ntm-ac-body">
        <div class="ntm-ac-src">${esc(src.toUpperCase())}</div>
        ${date ? `<div class="ntm-ac-date">${esc(date)}</div>` : ''}
        <a class="ntm-ac-title"
           href="${esc(url)}"
           target="_blank"
           rel="noopener noreferrer"
           title="${esc(title)}"
        >${esc(title)}</a>
        ${snippet ? `<div class="ntm-ac-snippet">… ${esc(snippet)}${snippet.length>=220?'…':''}</div>` : ''}
      </div>
      ${img ? `<img class="ntm-ac-thumb" src="${esc(img)}" alt="" loading="lazy" onerror="this.style.display='none'">` : ''}
    </div>`;
  });

  $('ntmArtList').innerHTML = h;
}

/* ════════════════════════════════════
   BOOT
════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', ()=>{
  const urlP = new URLSearchParams(window.location.search);
  if(urlP.get('start_date')) CFG.s = urlP.get('start_date');
  if(urlP.get('end_date'))   CFG.e = urlP.get('end_date');
  if($('ntmDateSpan')) $('ntmDateSpan').textContent = `${CFG.s} – ${CFG.e}`;
  NTMDp.init();
  if(CFG.pid) ntmLoad();
});

/* ════════════════════════════════════
   DATE PICKER
════════════════════════════════════ */
const NTMDp = (() => {
  let ds = null, de = null, m1 = new Date(), m2 = new Date(), pickStart = true;
  const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD = ['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init(){
    ds = CFG.s ? new Date(CFG.s) : (()=>{ const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = CFG.e ? new Date(CFG.e) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    $('ntmDateBtn')?.addEventListener('click', open);
    document.querySelectorAll('.ntm-dp-preset').forEach(b => b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e => {
      if(e.key === 'Escape' && $('ntmDpModal')?.classList.contains('show')) close();
    });
  }

  function open()  { $('ntmDpModal')?.classList.add('show'); render(); }
  function close() { $('ntmDpModal')?.classList.remove('show'); }

  function apply(){
    CFG.s = fmt(ds);
    CFG.e = fmt(de);
    if($('ntmDateSpan')) $('ntmDateSpan').textContent = `${CFG.s} – ${CFG.e}`;
    close();
    ntmLoad(); // ✅ reload data dengan tanggal baru
  }

  function nav(dir){ m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }

  function onPreset(e){
    document.querySelectorAll('.ntm-dp-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.p){
      case 'today':     ds=new Date(today); de=new Date(today); break;
      case 'yesterday': ds=new Date(today); ds.setDate(today.getDate()-1); de=new Date(ds); break;
      case 'last7':     de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-6); break;
      case 'last14':    de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-13); break;
      case 'last30':    de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-29); break;
      case 'last60':    de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-59); break;
      case 'last90':    de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-89); break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1); de=new Date(today); break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1); de=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if(e.target.dataset.p !== 'custom'){ m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1); }
    updDisp(); render();
  }

  function render(){ renderCal('ntmDpCal1', m1); renderCal('ntmDpCal2', m2); updDisp(); }

  function renderCal(id, month){
    const el = $(id); if(!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const today=new Date(); today.setHours(0,0,0,0);

    let h = `<div class="ntm-cal-month">${MN[mn]} ${y}</div>
      <div class="ntm-cal-wdays">${WD.map(d=>`<div class="ntm-cal-wday">${d}</div>`).join('')}</div>
      <div class="ntm-cal-days">`;

    for(let i=0;i<first.getDay();i++)
      h += `<button type="button" class="ntm-cal-day oth" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;

    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='ntm-cal-day';
      if(sameD(date,today)) cls+=' tod';
      if(date>today) cls+=' dis';
      if(ds&&de){
        if(sameD(date,ds)||sameD(date,de)) cls+=' sel';
        else if(date>ds&&date<de) cls+=' inr';
      }
      h += `<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }

    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h += `<button type="button" class="ntm-cal-day oth" disabled>${i}</button>`;
    h += '</div>';
    el.innerHTML = h;

    el.querySelectorAll('.ntm-cal-day:not(.oth):not(.dis)').forEach(btn=>{
      btn.addEventListener('click', function(){
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.ntm-dp-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ ds=d; de=d; pickStart=false; }
        else{ if(d>=ds) de=d; else{ de=ds; ds=d; } pickStart=true; }
        updDisp(); render();
      });
    });
  }

  function updDisp(){
    const el=$('ntmDpRangeText');
    if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de);
  }

  function fmt(d){
    if(!d) return '';
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  }

  function sameD(a,b){
    return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();
  }

  return { init, open, close, apply, nav };
})();
window.NTMDp = NTMDp;

let rTimer;
window.addEventListener('resize', ()=>{
  clearTimeout(rTimer);
  rTimer = setTimeout(()=>{
    if(D.raw.length && D.view==='map')    doMap(D.fil);
    if(D.raw.length && D.view==='bubble') doBub(D.fil);
  }, 300);
});

})();
</script>
@endsection