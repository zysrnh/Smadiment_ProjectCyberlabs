@extends('mk.layouts.app')

@section('title', 'Most Active Users - X Analytics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/most-active-users.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   DESIGN TOKENS
═══════════════════════════════════════════════════════════ */
:root {
  --space-1: 4px;  --space-2: 8px;  --space-3: 12px;
  --space-4: 16px; --space-5: 20px; --space-6: 24px;
  --space-7: 28px; --space-8: 32px; --space-10: 40px;

  --radius-sm: 8px; --radius-md: 12px;
  --radius-lg: 16px; --radius-xl: 20px; --radius-full: 9999px;

  --color-brand:        #16a085;
  --color-brand-dark:   #0d6b5a;
  --color-brand-light:  #e8f8f4;
  --color-brand-mid:    #b2dfdb;

  --color-x:            #0f1419;
  --color-x-blue:       #1d9bf0;
  --color-x-green:      #00ba7c;
  --color-x-pink:       #f91880;

  --color-pos:          #16a34a;
  --color-pos-bg:       #dcfce7;
  --color-neg:          #dc2626;
  --color-neg-bg:       #fee2e2;
  --color-neu:          #4b5563;
  --color-neu-bg:       #f3f4f6;

  --text-primary:   #0f1419;
  --text-secondary: #4b5563;
  --text-muted:     #94a3b8;
  --text-label:     #64748b;

  --border:         1px solid #e5e9ef;
  --border-strong:  1px solid #d1d9e0;
  --bg-card:        #ffffff;
  --bg-muted:       #f8fafc;
  --bg-subtle:      #f1f5f9;
  --bg-hover:       #f7fbf9;

  --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
  --shadow-lg: 0 12px 40px rgba(0,0,0,.12), 0 4px 16px rgba(0,0,0,.07);
  --shadow-xl: 0 24px 64px rgba(0,0,0,.16), 0 8px 24px rgba(0,0,0,.09);

  --font-sans: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-mono: 'DM Mono', monospace;

  /* Page tokens */
  --color-brand2:       #2e7d6e;
  --color-brand2-dark:  #0f4c35;
  --color-brand2-light: #e8f5f0;
  --bg-page: #f7f9fc;
}

/* ═══════════════════════════════════════════════════════════
   PAGE (unchanged from original — keep working)
═══════════════════════════════════════════════════════════ */
.page-header {
  display:flex; align-items:center; justify-content:space-between;
  gap:var(--space-6); margin-bottom:var(--space-8);
  padding-bottom:var(--space-6); border-bottom:1px solid #edf0f4;
}
.page-header-left h1 { font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-.4px; line-height:1.2; margin:0 0 4px; }
.page-header-left p  { font-size:13px; color:var(--text-secondary); margin:0; line-height:1.5; }
.last-updated { display:flex; align-items:center; gap:7px; font-size:12px; color:var(--text-muted); white-space:nowrap; flex-shrink:0; background:var(--bg-muted); border:1px solid #edf0f4; border-radius:20px; padding:6px 14px; }
.last-updated svg { width:13px; height:13px; flex-shrink:0; color:var(--color-brand2); }
.last-updated strong { color:var(--text-secondary); font-weight:600; }

.filter-card { background:var(--bg-card); border:1px solid #e2e8f0; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); padding:16px 20px; margin-bottom:var(--space-8); }
.filter-content { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
.filter-group { display:flex; flex-direction:column; gap:6px; }
.filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--text-label); line-height:1; }

.table-section { background:var(--bg-card); border:1px solid #e2e8f0; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:var(--space-8); }
.table-header { display:flex; align-items:center; justify-content:space-between; gap:var(--space-4); padding:20px 24px; border-bottom:1px solid #edf0f4; }
.table-title h3 { font-size:15px; font-weight:800; color:var(--text-primary); letter-spacing:-.3px; margin:0 0 3px; line-height:1.2; }
.table-subtitle { font-size:12px; color:var(--text-muted); margin:0; line-height:1.4; }
.table-actions { display:flex; align-items:center; gap:10px; flex-shrink:0; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:var(--bg-muted); border-bottom:1px solid #edf0f4; }
.data-table th { padding:11px 16px; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--text-label); text-align:left; white-space:nowrap; }
.data-table th:nth-child(5), .data-table th:nth-child(6), .data-table th:nth-child(7), .data-table th:nth-child(8), .data-table th:nth-child(9) { text-align:center; }
.data-table td { padding:13px 16px; border-bottom:1px solid #edf0f4; vertical-align:middle; color:var(--text-primary); line-height:1; }
.data-table td:nth-child(5), .data-table td:nth-child(6), .data-table td:nth-child(7), .data-table td:nth-child(8), .data-table td:nth-child(9) { text-align:center; font-weight:600; color:var(--text-secondary); font-variant-numeric:tabular-nums; }
.data-table td:nth-child(9) { color:var(--text-primary); font-weight:800; }
.data-table tbody tr { cursor:pointer; transition:background .12s; }
.data-table tbody tr:hover { background:linear-gradient(90deg,#f0fdf8 0%,#f7fdfb 100%); }
.data-table tbody tr:last-child td { border-bottom:none; }
.data-table td:first-child { font-weight:700; color:var(--text-secondary); font-size:13px; min-width:36px; }
.user-avatar-img, .user-avatar-fallback { width:36px; height:36px; border-radius:50%; display:block; object-fit:cover; border:2px solid #dde3ec; vertical-align:middle; flex-shrink:0; }
.user-avatar-fallback { display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--color-brand2); background:var(--color-brand2-light); border-color:#b8ddd4; }
.pagination { padding:14px 24px; border-top:1px solid #edf0f4; background:var(--bg-muted); }
.pagination-info { font-size:12px; color:var(--text-muted); font-weight:500; }
.page-btn { min-width:32px; height:32px; padding:0 8px; border-radius:8px; border:1px solid #e2e8f0; background:var(--bg-card); color:var(--text-secondary); font-size:12px; font-weight:600; font-family:inherit; cursor:pointer; transition:all .13s; display:inline-flex; align-items:center; justify-content:center; }
.page-btn:hover:not(:disabled) { background:var(--color-brand2-light); border-color:#a8d5c8; color:var(--color-brand2-dark); }
.page-btn.active { background:var(--color-brand2); border-color:var(--color-brand2); color:#fff; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }

.tug-section { margin-bottom:var(--space-8); }
.tug-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:12px; }
.tug-title { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:800; color:var(--text-primary); letter-spacing:-.3px; }
.tug-title svg { width:17px; height:17px; color:var(--color-brand2); flex-shrink:0; }
.tug-period-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; background:var(--bg-subtle); color:var(--text-label); border:1px solid #e2e8f0; }
.tug-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:stretch; }
.tug-card { background:var(--bg-card); border:1px solid #e2e8f0; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s; }
.tug-card:hover { box-shadow:var(--shadow-md); }
.tug-card-head { display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid #edf0f4; flex-shrink:0; min-height:56px; }
.tug-card-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--text-label); }
.tug-card-sub { font-size:11px; font-weight:500; color:var(--text-muted); background:var(--bg-subtle); padding:3px 9px; border-radius:10px; }
.tug-card--donut { padding-bottom:0; }
.tug-donut-wrap { flex:1; width:100%; min-height:400px; position:relative; }
.tug-donut-wrap > div { width:100%!important; height:100%!important; min-height:400px; display:block; position:absolute; inset:0; }
.tug-donut-skel { flex:1; min-height:400px; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:16px; padding:20px; }
.tug-donut-skel-circle { width:180px; height:180px; border-radius:50%; background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%); background-size:200% 100%; animation:tugShim 1.4s ease-in-out infinite; position:relative; }
.tug-donut-skel-circle::after { content:''; position:absolute; inset:36px; border-radius:50%; background:#fff; }
.tug-donut-skel-legend { display:flex; flex-direction:column; gap:8px; width:80%; }
.tug-donut-skel-leg-item { display:flex; align-items:center; gap:8px; }
.tug-donut-skel-dot { width:10px; height:10px; border-radius:50%; background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%); background-size:200% 100%; animation:tugShim 1.4s ease-in-out infinite; flex-shrink:0; }
.tug-donut-skel-bar { flex:1; height:9px; border-radius:4px; background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%); background-size:200% 100%; animation:tugShim 1.4s ease-in-out infinite; }
.tug-card--list { padding-bottom:0; }
.tug-list { flex:1; padding:0; margin:0; display:flex; flex-direction:column; justify-content:stretch; }
.tug-row { display:flex; align-items:center; gap:13px; padding:0 22px; min-height:72px; cursor:pointer; transition:background .13s; border-bottom:1px solid #edf0f4; flex:1; }
.tug-row:last-child { border-bottom:none; }
.tug-row:hover { background:var(--bg-muted); }
.tug-row--first { background:#f4fbf8; }
.tug-row--first:hover { background:#eaf6f1; }
.tug-rank-badge { width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; flex-shrink:0; color:#fff; background:#cbd5e1; line-height:1; }
.tug-rank-badge--1 { background:linear-gradient(135deg,#d4a529,#b59940); }
.tug-rank-badge--2 { background:linear-gradient(135deg,#a0a8b8,#8a94a6); }
.tug-rank-badge--3 { background:linear-gradient(135deg,#b8906a,#a07850); }
.tug-color-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.tug-avatar { width:38px; height:38px; border-radius:50%; object-fit:cover; display:block; border:2px solid #e8edf2; background:var(--bg-subtle); flex-shrink:0; }
.tug-avatar-fallback { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:var(--color-brand2); background:var(--color-brand2-light); border:2px solid #c9e8de; flex-shrink:0; }
.tug-user-info { flex:1; min-width:0; }
.tug-user-name { font-size:13px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.35; }
.tug-row--first .tug-user-name { color:var(--color-brand2-dark); }
.tug-user-handle { font-size:11.5px; font-weight:400; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3; margin-top:2px; }
.tug-user-right { flex-shrink:0; text-align:right; min-width:64px; }
.tug-user-eng { font-size:15px; font-weight:800; color:var(--text-primary); letter-spacing:-.5px; display:block; line-height:1.25; }
.tug-row--first .tug-user-eng { color:var(--color-brand2-dark); }
.tug-user-bar { display:block; width:60px; height:3px; background:var(--bg-subtle); border-radius:3px; margin-top:7px; margin-left:auto; overflow:hidden; }
.tug-user-bar-fill { height:100%; border-radius:3px; transition:width .8s cubic-bezier(.4,0,.2,1); }
.tug-skel { background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%); background-size:200% 100%; animation:tugShim 1.4s ease-in-out infinite; border-radius:4px; }
@keyframes tugShim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.tug-skel--rank { width:24px; height:24px; border-radius:50%; flex-shrink:0; }
.tug-skel--avatar { width:38px; height:38px; border-radius:50%; flex-shrink:0; }
.tug-skel-info { flex:1; display:flex; flex-direction:column; gap:6px; }
.tug-skel--name { width:60%; height:11px; }
.tug-skel--handle { width:38%; height:9px; }
.tug-skel--score { width:48px; height:13px; border-radius:4px; flex-shrink:0; }
.tug-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; padding:40px 20px; gap:8px; color:var(--text-muted); }
.tug-empty svg { width:32px; height:32px; opacity:.25; }
.tug-empty span { font-size:12.5px; font-weight:500; }
@media(max-width:900px){.tug-grid{grid-template-columns:1fr;gap:16px;}.tug-donut-wrap{min-height:320px;}.tug-donut-wrap>div{min-height:320px;}.page-header{flex-direction:column;align-items:flex-start;gap:8px;}}
@media(max-width:640px){.tug-card-head{padding:16px 16px 12px;}.tug-row{padding:12px 16px;gap:10px;}.table-header{flex-direction:column;align-items:flex-start;gap:8px;}.filter-content{gap:8px;}}

/* ═══════════════════════════════════════════════════════════
   USER DETAIL MODAL (UDM) — Redesigned
═══════════════════════════════════════════════════════════ */
.user-detail-modal {
  display:none; position:fixed; inset:0; z-index:9999;
  align-items:center; justify-content:center; padding:16px;
}
.user-detail-modal.show { display:flex; }

.modal-overlay {
  position:absolute; inset:0;
  background:rgba(15,20,25,.6);
  backdrop-filter:blur(8px);
  -webkit-backdrop-filter:blur(8px);
  animation:overlayFadeIn .22s ease forwards;
}
@keyframes overlayFadeIn { from{opacity:0}to{opacity:1} }

.udm-content {
  position:relative; z-index:1;
  width:100%; max-width:760px; max-height:92vh;
  background:var(--bg-card);
  border-radius:20px;
  display:flex; flex-direction:column; overflow:hidden;
  animation:udmEntrance .3s cubic-bezier(.22,1,.36,1) forwards;
  box-shadow:0 32px 80px rgba(0,0,0,.2), 0 8px 32px rgba(0,0,0,.1), 0 0 0 1px rgba(255,255,255,.08);
  font-family:var(--font-sans);
}
@keyframes udmEntrance {
  from { transform:scale(.96) translateY(16px); opacity:0; }
  to   { transform:scale(1)   translateY(0);    opacity:1; }
}

/* ─── Modal Topbar ─── */
.udm-topbar {
  display:flex; align-items:center; justify-content:space-between;
  padding:16px 24px;
  border-bottom:var(--border);
  flex-shrink:0;
  background:var(--bg-card);
}
.udm-topbar-title {
  font-size:15px; font-weight:700; color:var(--text-primary);
  letter-spacing:-.3px;
  display:flex; align-items:center; gap:8px;
}
.udm-topbar-title::before {
  content:'';
  display:inline-block; width:3px; height:16px;
  background:linear-gradient(180deg,var(--color-brand),var(--color-brand-dark));
  border-radius:2px;
}
.udm-close {
  width:32px; height:32px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  background:transparent; border:none; cursor:pointer;
  color:var(--text-secondary); transition:background .15s, color .15s;
}
.udm-close:hover { background:var(--bg-subtle); color:var(--text-primary); }
.udm-close svg { width:16px; height:16px; stroke:currentColor; stroke-width:2.5; }

/* ─── Body scroll ─── */
.udm-body {
  flex:1; overflow-y:auto;
  scrollbar-width:thin; scrollbar-color:#d1d9e0 transparent;
}
.udm-body::-webkit-scrollbar { width:5px; }
.udm-body::-webkit-scrollbar-thumb { background:#d1d9e0; border-radius:4px; }

/* ─── Banner / Profile Header ─── */
.udm-banner {
  height:140px; flex-shrink:0; position:relative; overflow:hidden;
  background:linear-gradient(135deg,#0d1b2a 0%,#162534 30%,#0d3d2e 65%,#0a4a38 100%);
}
.udm-banner::before {
  content:''; position:absolute; inset:0;
  background:
    radial-gradient(ellipse 70% 90% at 10% 50%, rgba(22,160,133,.3) 0%,transparent 60%),
    radial-gradient(ellipse 50% 70% at 85% 30%, rgba(29,155,240,.15) 0%,transparent 55%),
    radial-gradient(ellipse 40% 60% at 60% 80%, rgba(0,186,124,.12) 0%,transparent 50%);
}
.udm-banner::after {
  content:''; position:absolute; inset:0;
  background:
    repeating-linear-gradient(-55deg,transparent,transparent 28px,rgba(255,255,255,.018) 28px,rgba(255,255,255,.018) 29px);
}
/* Banner grid dots */
.udm-banner-dots {
  position:absolute; inset:0; opacity:.18;
  background-image:radial-gradient(circle,rgba(255,255,255,.6) 1px,transparent 1px);
  background-size:24px 24px;
}

/* ─── Profile Section ─── */
.udm-profile-section {
  padding:0 var(--space-6) var(--space-6);
  border-bottom:var(--border);
}

.udm-profile-row {
  display:flex; align-items:flex-end; justify-content:space-between;
  margin-top:-40px; position:relative; z-index:2;
  padding-bottom:var(--space-5);
  gap:var(--space-4);
}

.udm-avatar-wrap { flex-shrink:0; }
.udm-avatar {
  width:80px; height:80px; border-radius:50%;
  object-fit:cover; display:block;
  border:4px solid #fff;
  box-shadow:0 4px 16px rgba(0,0,0,.18);
  background:var(--color-brand-light);
}
.udm-avatar-fb {
  width:80px; height:80px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:26px; font-weight:800; letter-spacing:-.5px;
  color:var(--color-brand); background:var(--color-brand-light);
  border:4px solid #fff; box-shadow:0 4px 16px rgba(0,0,0,.18);
}

.udm-profile-meta {
  flex:1; min-width:0;
  padding-top:48px; /* clear the avatar overlap */
}
.udm-name {
  font-size:20px; font-weight:700; color:var(--text-primary);
  line-height:1.2; letter-spacing:-.4px;
  display:flex; align-items:center; gap:6px; flex-wrap:wrap;
  margin-bottom:3px;
}
.udm-verified-badge {
  display:inline-flex; align-items:center; justify-content:center;
  width:19px; height:19px; flex-shrink:0;
}
.udm-verified-badge svg { width:19px; height:19px; }
.udm-handle {
  font-size:13.5px; color:var(--text-secondary);
  display:flex; align-items:center; gap:4px;
}
.udm-handle a { color:var(--text-secondary); text-decoration:none; }
.udm-handle a:hover { color:var(--color-x-blue); text-decoration:underline; }

.udm-view-btn {
  flex-shrink:0;
  display:inline-flex; align-items:center; gap:6px;
  padding:9px 18px; border-radius:var(--radius-full);
  background:var(--color-x); color:#fff;
  font-size:12.5px; font-weight:700; letter-spacing:.1px;
  text-decoration:none;
  transition:background .15s, transform .12s, box-shadow .15s;
  line-height:1;
  align-self:flex-end;
  box-shadow:0 2px 8px rgba(0,0,0,.18);
  white-space:nowrap;
}
.udm-view-btn:hover { background:#1a1e24; transform:translateY(-1px); box-shadow:0 4px 14px rgba(0,0,0,.22); }
.udm-view-btn svg { flex-shrink:0; }

/* Follower pill */
.udm-follower-chip {
  display:inline-flex; align-items:center; gap:5px;
  margin-top:var(--space-3);
  background:var(--bg-subtle);
  border:var(--border);
  border-radius:var(--radius-full);
  padding:5px 12px;
  font-size:12px; font-weight:500; color:var(--text-secondary);
}
.udm-follower-chip strong { color:var(--text-primary); font-weight:700; }

/* ─── Engagement Stats Strip ─── */
.udm-eng-strip {
  display:grid; grid-template-columns:repeat(3,1fr);
  margin:var(--space-6) var(--space-6) 0;
  border-radius:var(--radius-lg);
  overflow:hidden;
  border:var(--border);
  background:var(--bg-muted);
  box-shadow:var(--shadow-sm);
}
.udm-eng-item {
  padding:var(--space-5) var(--space-4);
  text-align:center;
  border-right:var(--border);
  position:relative;
  transition:background .15s;
}
.udm-eng-item:last-child { border-right:none; }
.udm-eng-item:hover { background:var(--bg-subtle); }

.udm-eng-icon {
  width:32px; height:32px; border-radius:var(--radius-sm);
  display:flex; align-items:center; justify-content:center;
  margin:0 auto var(--space-2);
}
.udm-eng-item--mentions .udm-eng-icon { background:#e8f8f4; }
.udm-eng-item--replies  .udm-eng-icon { background:#e8f4fd; }
.udm-eng-item--retweets .udm-eng-icon { background:#e8f8f1; }
.udm-eng-icon svg { width:15px; height:15px; stroke-width:2; }
.udm-eng-item--mentions .udm-eng-icon svg { stroke:var(--color-brand); }
.udm-eng-item--replies  .udm-eng-icon svg { stroke:var(--color-x-blue); }
.udm-eng-item--retweets .udm-eng-icon svg { stroke:var(--color-x-green); }

.udm-eng-val {
  display:block; font-size:22px; font-weight:700;
  letter-spacing:-.6px; line-height:1.1;
}
.udm-eng-item--mentions .udm-eng-val { color:var(--color-brand); }
.udm-eng-item--replies  .udm-eng-val { color:var(--color-x-blue); }
.udm-eng-item--retweets .udm-eng-val { color:var(--color-x-green); }

.udm-eng-lbl {
  display:block; font-size:10.5px; font-weight:600;
  color:var(--text-muted); text-transform:uppercase;
  letter-spacing:.6px; margin-top:4px;
}

/* Total engagement row */
.udm-total-row {
  display:flex; align-items:center; justify-content:space-between;
  margin:var(--space-3) var(--space-6) var(--space-6);
  padding:var(--space-4) var(--space-5);
  background:linear-gradient(90deg,#edf7f4,#f0faf7);
  border-radius:var(--radius-md);
  border:1px solid var(--color-brand-mid);
}
.udm-total-row-label {
  font-size:12.5px; font-weight:600; color:var(--color-brand-dark);
  display:flex; align-items:center; gap:6px;
}
.udm-total-row-label svg { width:14px; height:14px; stroke:var(--color-brand); stroke-width:2; }
.udm-total-row-val {
  font-size:20px; font-weight:800; color:var(--color-brand-dark);
  letter-spacing:-.6px;
}

/* ─── Mentions Section ─── */
.udm-mentions-wrap { padding:0; }

.udm-mentions-head {
  display:flex; align-items:center; justify-content:space-between;
  padding:var(--space-5) var(--space-6);
  border-bottom:var(--border);
  position:sticky; top:0; z-index:5;
  background:rgba(255,255,255,.97);
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
}
.udm-mentions-title {
  font-size:14.5px; font-weight:700; color:var(--text-primary);
  letter-spacing:-.2px;
  display:flex; align-items:center; gap:8px;
}
.udm-mentions-title-icon {
  width:28px; height:28px; border-radius:8px;
  background:var(--color-brand-light);
  display:flex; align-items:center; justify-content:center;
}
.udm-mentions-title-icon svg { width:13px; height:13px; stroke:var(--color-brand); stroke-width:2.2; }
.udm-mentions-count {
  font-size:11.5px; font-weight:600; color:var(--text-secondary);
  background:var(--bg-subtle); padding:4px 11px; border-radius:var(--radius-full);
  border:var(--border);
}

.udm-mentions-list { padding:var(--space-4) var(--space-6); display:flex; flex-direction:column; gap:var(--space-3); }

/* ─── Tweet Card (inside UDM) ─── */
.udm-card {
  background:var(--bg-card);
  border:var(--border);
  border-radius:var(--radius-lg);
  padding:var(--space-4);
  cursor:pointer;
  transition:
    box-shadow .18s ease,
    border-color .18s ease,
    transform .15s ease,
    background .15s ease;
  position:relative;
}
.udm-card:hover {
  box-shadow:var(--shadow-md);
  border-color:#c7d4e0;
  transform:translateY(-1px);
  background:var(--bg-hover);
}
.udm-card:active { transform:translateY(0); }

.udm-card-header {
  display:flex; align-items:flex-start; gap:var(--space-3);
  margin-bottom:var(--space-3);
}
.udm-card-mini-avatar {
  width:38px; height:38px; border-radius:50%;
  object-fit:cover; flex-shrink:0;
  border:2px solid var(--bg-subtle);
  background:var(--bg-subtle);
}
.udm-card-mini-avatar-fb {
  width:38px; height:38px; border-radius:50%;
  background:var(--color-brand-light);
  display:flex; align-items:center; justify-content:center;
  font-size:12px; font-weight:700; color:var(--color-brand);
  flex-shrink:0; border:2px solid var(--color-brand-mid);
}
.udm-card-meta { flex:1; min-width:0; }
.udm-card-row1 {
  display:flex; justify-content:space-between; align-items:flex-start; gap:8px;
}
.udm-card-name-block { display:flex; flex-direction:column; gap:1px; }
.udm-card-author {
  font-size:13.5px; font-weight:700; color:var(--text-primary); line-height:1.3;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.udm-card-handle {
  font-size:12px; color:var(--text-muted); line-height:1.3;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.udm-card-time { font-size:11.5px; color:var(--text-muted); white-space:nowrap; }
.udm-card-badges { display:flex; align-items:center; gap:5px; margin-top:var(--space-2); flex-wrap:wrap; }

/* Sentiment pill */
.udm-pill {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 9px; border-radius:var(--radius-full);
  font-size:11px; font-weight:700; letter-spacing:.2px; flex-shrink:0;
}
.udm-pill--pos { background:var(--color-pos-bg); color:var(--color-pos); }
.udm-pill--neg { background:var(--color-neg-bg); color:var(--color-neg); }
.udm-pill--neu { background:var(--color-neu-bg); color:var(--color-neu); }
.udm-pill::before {
  content:''; width:5px; height:5px; border-radius:50%; background:currentColor;
}

/* Type badge */
.udm-type-badge {
  display:inline-flex; align-items:center; padding:3px 9px;
  border-radius:var(--radius-full); font-size:11px; font-weight:600;
  letter-spacing:.2px; flex-shrink:0;
}
.udm-type-badge--mention { background:var(--color-brand-light); color:var(--color-brand); }
.udm-type-badge--reply   { background:#e8f4fd; color:var(--color-x-blue); }
.udm-type-badge--retweet { background:#e8f8f1; color:var(--color-x-green); }
.udm-type-badge--tweet   { background:var(--bg-subtle); color:var(--text-secondary); }

/* Tweet text */
.udm-card-text {
  font-size:13.5px; color:var(--text-primary);
  line-height:1.65; word-break:break-word;
  padding:var(--space-3) var(--space-4);
  background:var(--bg-muted);
  border-radius:var(--radius-md);
  border-left:3px solid var(--color-brand);
  margin-bottom:var(--space-3);
}
.udm-text-link { text-decoration:none; font-weight:500; border-radius:2px; transition:color .12s; }
.udm-text-link--mention { color:var(--color-x-blue); }
.udm-text-link--mention:hover { text-decoration:underline; opacity:.85; }
.udm-text-link--hashtag { color:var(--color-x-blue); }
.udm-text-link--hashtag:hover { text-decoration:underline; opacity:.85; }
.udm-text-link--url { color:var(--color-brand); word-break:break-all; }
.udm-text-link--url:hover { text-decoration:underline; opacity:.8; }

/* Card footer */
.udm-card-foot {
  display:flex; align-items:center; justify-content:space-between;
  gap:8px; flex-wrap:wrap; padding-top:var(--space-2);
  border-top:1px solid #f0f4f8;
}
.udm-card-actions { display:flex; align-items:center; gap:var(--space-5); }
.udm-card-action {
  display:flex; align-items:center; gap:5px;
  font-size:12.5px; color:var(--text-muted); font-weight:500;
  transition:color .13s;
  font-variant-numeric:tabular-nums;
}
.udm-card-action svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:1.8; flex-shrink:0; }
.udm-card-action:hover { color:var(--color-x-blue); }
.udm-card-action.likes:hover  { color:var(--color-x-pink); }
.udm-card-action.rts:hover    { color:var(--color-x-green); }

.udm-card-view-link {
  font-size:12px; font-weight:700; color:var(--color-brand);
  text-decoration:none;
  display:flex; align-items:center; gap:4px;
  padding:4px 10px; border-radius:var(--radius-full);
  background:var(--color-brand-light);
  border:1px solid var(--color-brand-mid);
  transition:all .15s;
}
.udm-card-view-link:hover { background:var(--color-brand-mid); color:var(--color-brand-dark); }
.udm-card-view-link svg { width:11px; height:11px; }

/* ─── Pagination (UDM) ─── */
.udm-pagination {
  display:flex; align-items:center; justify-content:space-between;
  gap:8px; padding:var(--space-4) var(--space-6);
  border-top:var(--border); background:var(--bg-muted);
  flex-wrap:wrap;
}
.udm-pg-info { font-size:11.5px; color:var(--text-muted); font-weight:500; flex-shrink:0; }
.udm-pg-btns { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.udm-pg-btn {
  min-width:30px; height:30px; padding:0 7px;
  border-radius:var(--radius-sm); border:var(--border);
  background:var(--bg-card); color:var(--text-secondary);
  font-size:12px; font-weight:600; font-family:inherit;
  cursor:pointer; transition:all .12s;
  display:inline-flex; align-items:center; justify-content:center; gap:3px; line-height:1;
}
.udm-pg-btn:hover:not(:disabled) { background:var(--bg-subtle); border-color:var(--color-brand-mid); color:var(--color-brand-dark); }
.udm-pg-btn.active { background:var(--color-x); border-color:var(--color-x); color:#fff; }
.udm-pg-btn:disabled { opacity:.35; cursor:not-allowed; }
.udm-pg-btn--load { background:var(--color-brand-light); border-color:var(--color-brand-mid); color:var(--color-brand-dark); padding:0 12px; font-weight:700; }
.udm-pg-btn--load:hover:not(:disabled) { background:var(--color-brand-mid); }
.udm-pg-ellipsis { font-size:12px; color:var(--text-muted); padding:0 2px; line-height:30px; }
.udm-pg-loading-dot { display:inline-block; width:8px; height:8px; border-radius:50%; border:2px solid var(--color-brand-dark); border-top-color:transparent; animation:udmSpin .6s linear infinite; flex-shrink:0; }

/* Loading */
.udm-loading {
  display:flex; flex-direction:column; align-items:center;
  justify-content:center; padding:56px 20px; gap:14px; color:var(--text-muted);
}
.udm-spinner {
  width:28px; height:28px;
  border:3px solid var(--bg-subtle); border-top-color:var(--color-brand);
  border-radius:50%; animation:udmSpin .7s linear infinite;
}
@keyframes udmSpin { to{transform:rotate(360deg)} }
.udm-loading-txt { font-size:13.5px; font-weight:500; color:var(--text-muted); }

/* Empty state */
.udm-empty-mentions {
  padding:var(--space-10) var(--space-6);
  display:flex; flex-direction:column; align-items:center; gap:var(--space-3);
}
.udm-empty-mentions svg { width:40px; height:40px; opacity:.2; }
.udm-empty-mentions p { font-size:13.5px; font-weight:500; color:var(--text-muted); margin:0; }

/* ═══════════════════════════════════════════════════════════
   TWEET DETAIL MODAL (TDM)
═══════════════════════════════════════════════════════════ */
.tdm-modal {
  display:none; position:fixed; inset:0; z-index:10999;
  align-items:center; justify-content:center; padding:16px;
}
.tdm-modal.show { display:flex; }

.tdm-overlay {
  position:absolute; inset:0;
  background:rgba(15,20,25,.5);
  backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
  animation:tdmFadeIn .18s ease forwards;
}
@keyframes tdmFadeIn { from{opacity:0}to{opacity:1} }

.tdm-content {
  position:relative; z-index:1;
  width:100%; max-width:600px; max-height:88vh;
  background:var(--bg-card);
  border-radius:20px;
  display:flex; flex-direction:column; overflow:hidden;
  box-shadow:0 28px 72px rgba(0,0,0,.22), 0 0 0 1px rgba(0,0,0,.06);
  animation:tdmEntrance .24s cubic-bezier(.22,1,.36,1) forwards;
  font-family:var(--font-sans);
}
@keyframes tdmEntrance {
  from { transform:scale(.94) translateY(12px); opacity:0; }
  to   { transform:scale(1)   translateY(0);    opacity:1; }
}

/* ─── TDM Topbar ─── */
.tdm-topbar {
  display:flex; align-items:center; gap:10px;
  padding:14px 20px; border-bottom:var(--border);
  flex-shrink:0; background:var(--bg-card);
}
.tdm-back-btn, .tdm-close-btn {
  width:32px; height:32px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  background:transparent; border:none; cursor:pointer;
  color:var(--text-secondary); transition:background .13s, color .13s;
  flex-shrink:0;
}
.tdm-back-btn:hover, .tdm-close-btn:hover { background:var(--bg-subtle); color:var(--text-primary); }
.tdm-back-btn svg, .tdm-close-btn svg { width:16px; height:16px; stroke:currentColor; stroke-width:2.2; fill:none; }
.tdm-topbar-title {
  font-size:15px; font-weight:700; color:var(--text-primary);
  letter-spacing:-.3px; flex:1;
}

/* ─── TDM Body ─── */
.tdm-body {
  flex:1; overflow-y:auto;
  scrollbar-width:thin; scrollbar-color:#d1d9e0 transparent;
}
.tdm-body::-webkit-scrollbar { width:5px; }
.tdm-body::-webkit-scrollbar-thumb { background:#d1d9e0; border-radius:4px; }

/* ─── Author section ─── */
.tdm-author-section {
  padding:var(--space-6) var(--space-6) var(--space-5);
  border-bottom:var(--border);
  display:flex; align-items:center; gap:var(--space-4);
}
.tdm-author-avatar {
  width:52px; height:52px; border-radius:50%;
  object-fit:cover; flex-shrink:0;
  border:2.5px solid var(--bg-subtle);
  background:var(--color-brand-light);
}
.tdm-author-avatar-fb {
  width:52px; height:52px; border-radius:50%;
  background:var(--color-brand-light);
  display:flex; align-items:center; justify-content:center;
  font-size:15px; font-weight:800; color:var(--color-brand);
  flex-shrink:0; border:2.5px solid var(--color-brand-mid);
}
.tdm-author-info { flex:1; min-width:0; }
.tdm-author-name {
  font-size:15px; font-weight:700; color:var(--text-primary); line-height:1.25;
  display:flex; align-items:center; gap:5px;
}
.tdm-author-handle {
  font-size:13px; color:var(--text-muted); margin-top:2px;
}
.tdm-author-handle a { color:var(--text-muted); text-decoration:none; }
.tdm-author-handle a:hover { color:var(--color-x-blue); text-decoration:underline; }

.tdm-view-x-btn {
  display:inline-flex; align-items:center; gap:6px;
  padding:8px 16px; border-radius:var(--radius-full);
  background:var(--color-x); color:#fff;
  font-size:12.5px; font-weight:700;
  text-decoration:none; flex-shrink:0;
  transition:background .13s, transform .1s, box-shadow .15s;
  line-height:1;
  box-shadow:0 2px 8px rgba(0,0,0,.15);
}
.tdm-view-x-btn:hover { background:#1a1e24; transform:translateY(-1px); box-shadow:0 4px 14px rgba(0,0,0,.2); }
.tdm-view-x-btn svg { flex-shrink:0; }

/* ─── Tweet Content ─── */
.tdm-tweet-body {
  padding:var(--space-6);
  border-bottom:var(--border);
}
.tdm-tweet-text {
  font-size:16.5px; line-height:1.7;
  color:var(--text-primary); word-break:break-word;
  margin:0 0 var(--space-5);
  font-weight:400;
}
.tdm-tweet-meta {
  display:flex; align-items:center; gap:8px; flex-wrap:wrap;
}
.tdm-timestamp {
  font-size:12px; color:var(--text-muted);
  margin-left:auto;
  font-variant-numeric:tabular-nums;
  font-family:var(--font-mono);
}

/* Badges */
.tdm-badge {
  display:inline-flex; align-items:center; gap:4px;
  padding:4px 10px; border-radius:var(--radius-full);
  font-size:11.5px; font-weight:700; letter-spacing:.2px;
}
.tdm-badge::before { content:''; width:5px; height:5px; border-radius:50%; background:currentColor; }
.tdm-badge--pos     { background:var(--color-pos-bg); color:var(--color-pos); }
.tdm-badge--neg     { background:var(--color-neg-bg); color:var(--color-neg); }
.tdm-badge--neu     { background:var(--color-neu-bg); color:var(--color-neu); }
.tdm-badge--mention { background:var(--color-brand-light); color:var(--color-brand); }
.tdm-badge--reply   { background:#e8f4fd; color:var(--color-x-blue); }
.tdm-badge--retweet { background:#e8f8f1; color:var(--color-x-green); }
.tdm-badge--tweet   { background:var(--bg-subtle); color:var(--text-secondary); }

/* ─── Stats Strip ─── */
.tdm-stats-strip {
  display:grid; grid-template-columns:repeat(3,1fr);
  margin:var(--space-5) var(--space-6);
  border-radius:var(--radius-lg);
  overflow:hidden;
  border:var(--border);
  background:var(--bg-muted);
}
.tdm-stat-item {
  padding:var(--space-5) var(--space-4);
  text-align:center;
  border-right:var(--border);
  transition:background .15s;
}
.tdm-stat-item:last-child { border-right:none; }
.tdm-stat-item:hover { background:var(--bg-subtle); }
.tdm-stat-icon {
  width:32px; height:32px; border-radius:var(--radius-sm);
  display:flex; align-items:center; justify-content:center;
  margin:0 auto var(--space-2);
}
.tdm-stat-item--likes   .tdm-stat-icon { background:#fce7f3; }
.tdm-stat-item--rts     .tdm-stat-icon { background:#e8f8f1; }
.tdm-stat-item--replies .tdm-stat-icon { background:#e8f4fd; }
.tdm-stat-icon svg { width:15px; height:15px; fill:none; stroke-width:2; }
.tdm-stat-item--likes   .tdm-stat-icon svg { stroke:var(--color-x-pink); }
.tdm-stat-item--rts     .tdm-stat-icon svg { stroke:var(--color-x-green); }
.tdm-stat-item--replies .tdm-stat-icon svg { stroke:var(--color-x-blue); }
.tdm-stat-val {
  display:block; font-size:22px; font-weight:700;
  letter-spacing:-.6px; line-height:1.1;
}
.tdm-stat-item--likes   .tdm-stat-val { color:var(--color-x-pink); }
.tdm-stat-item--rts     .tdm-stat-val { color:var(--color-x-green); }
.tdm-stat-item--replies .tdm-stat-val { color:var(--color-x-blue); }
.tdm-stat-lbl {
  display:block; font-size:10.5px; font-weight:600;
  color:var(--text-muted); text-transform:uppercase;
  letter-spacing:.6px; margin-top:3px;
}

/* ─── TDM Footer ─── */
.tdm-footer {
  padding:var(--space-4) var(--space-6);
  display:flex; align-items:center; justify-content:flex-end; gap:10px;
  flex-shrink:0; border-top:var(--border); background:var(--bg-muted);
}
.tdm-footer-btn {
  display:inline-flex; align-items:center; gap:6px;
  padding:8px 16px; border-radius:var(--radius-full);
  font-size:12.5px; font-weight:700;
  text-decoration:none; transition:all .15s;
  cursor:pointer;
}
.tdm-footer-btn--outline {
  border:var(--border-strong); background:var(--bg-card);
  color:var(--text-secondary);
}
.tdm-footer-btn--outline:hover { background:var(--bg-subtle); color:var(--text-primary); border-color:#b0bec5; }
.tdm-footer-btn--primary {
  background:var(--color-brand); color:#fff; border:1px solid var(--color-brand-dark);
  box-shadow:0 2px 8px rgba(22,160,133,.25);
}
.tdm-footer-btn--primary:hover { background:var(--color-brand-dark); box-shadow:0 4px 12px rgba(22,160,133,.3); transform:translateY(-1px); }
.tdm-footer-btn svg { width:13px; height:13px; }

/* TDM link styles */
.tdm-link { text-decoration:none; font-weight:500; border-radius:2px; transition:opacity .12s; }
.tdm-link:hover { opacity:.8; text-decoration:underline; }
.tdm-link--url     { color:var(--color-brand); word-break:break-all; }
.tdm-link--mention { color:var(--color-x-blue); }
.tdm-link--hashtag { color:var(--color-x-blue); }

/* ─── Legacy Suppression ─── */
.mention-sentiment-edit, .sentiment-edit, .sentiment-ctrl,
.sentiment-form, .sent-ctrl, .edit-sentiment,
[class*="sentiment-edit"], [class*="sentiment-ctrl"],
[class*="sentiment-form"], [class*="sent-edit"],
[class*="edit-sent"] { display:none!important; }
.sentiment-btn, .sent-btn, .btn-sentiment,
.btn-positive, .btn-negative, .btn-neutral,
[class*="sentiment-btn"], [class*="sent-btn"],
[data-sentiment] { display:none!important; }
.relevance-ctrl, .relevance-form, .relevance-btn,
.btn-relevant, .btn-irrelevant, .relevant-btn, .irrelevant-btn,
[class*="relevance"], [class*="relevant"],
[class*="irrelevant"], [data-relevance] { display:none!important; }
.set-mention-as, .mention-action-panel, .mention-moderation,
.mention-controls, .moderation-panel, .moderation-ctrl,
.moderation-actions, .mod-panel, .mod-ctrl, .mod-actions,
[class*="moderat"], [class*="mod-panel"],
[class*="mod-ctrl"] { display:none!important; }
.mention-source, .source-panel, .source-twitter,
.tweet-source, .mention-source-panel,
[class*="source-panel"], [class*="mention-source"] { display:none!important; }
.udm-body .action-panel, .udm-body .ctrl-panel,
.udm-body .legacy-panel, .udm-body .old-controls,
.udm-body [class*="action-panel"], .udm-body [class*="ctrl-panel"],
.udm-body [class*="legacy"] { display:none!important; }
.udm-card { display:block!important; }
.udm-card-header { display:flex!important; }
.udm-card-text { display:block!important; }
.udm-card-foot { display:flex!important; }

/* ─── Responsive ─── */
@media(max-width:680px) {
  .udm-content { max-width:100%; border-radius:20px 20px 0 0; align-self:flex-end; max-height:95vh; }
  .tdm-content { max-width:100%; border-radius:20px 20px 0 0; align-self:flex-end; max-height:95vh; }
  .user-detail-modal, .tdm-modal { align-items:flex-end; padding:0; }
  .udm-eng-strip { margin:var(--space-5) var(--space-4) 0; }
  .udm-total-row { margin:var(--space-3) var(--space-4) var(--space-5); }
  .udm-mentions-list { padding:var(--space-3) var(--space-4); }
  .udm-mentions-head { padding:var(--space-4); }
  .tdm-stats-strip { margin:var(--space-4); }
  .tdm-footer { padding:var(--space-3) var(--space-4); }
  .tdm-tweet-body { padding:var(--space-4); }
  .tdm-author-section { padding:var(--space-4); }
}
</style>
@endsection


@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <h1>Most Active Users</h1>
      <p>Top users with the highest activity based on total interactions (mentions, replies, retweets)</p>
    </div>
    <div class="last-updated">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      <span>Last Updated: <strong>Twitter ({{ now()->diffForHumans() }})</strong></span>
    </div>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Most Active Users data.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.most-active-users') }}">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED"  value="{{ $endDate }}">
      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="msProjSel" onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id']==$projectId)?'selected':'' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #'.$p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif
        <div class="filter-group">
          <label class="filter-label">Date Range</label>
          <button type="button" class="date-picker-trigger" id="msDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="msDpDisplay">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;">‎</label>
          <div class="platform-selector"></div>
        </div>
        <div class="filter-group">
          <label class="filter-label" style="opacity:0;">‎</label>
          <div class="category-tag">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            <span>Monitoring</span> • Prabowo
          </div>
        </div>
        <div class="filter-group">
          <label class="filter-label" style="opacity:0;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Apply
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- DATE PICKER MODAL -->
  <div class="date-picker-modal" id="msDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="MSDp.close()"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button class="date-preset" data-p="today">Today</button>
        <button class="date-preset" data-p="yesterday">Yesterday</button>
        <button class="date-preset" data-p="last7">Last 7 Days</button>
        <button class="date-preset" data-p="last30">Last 30 Days</button>
        <button class="date-preset" data-p="thismonth">This Month</button>
        <button class="date-preset" data-p="lastmonth">Last Month</button>
        <button class="date-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button class="nav-btn" onclick="MSDp.nav(-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper">
            <div class="calendar" id="msDpCal1"></div>
            <div class="calendar" id="msDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="MSDp.nav(1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display">
          <span id="msDpRangeText">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="MSDp.close()">Cancel</button>
          <button class="apply-date-btn" onclick="MSDp.apply()">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Grid — hidden -->
  <div class="stats-grid" style="display:none;">
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-trend"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>+12.5%</div>
      </div>
      <div class="stat-label">Total Users</div>
      <div id="statTotalUsers" class="stat-value-wrapper"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </div>
        <div class="stat-trend"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>+8.3%</div>
      </div>
      <div class="stat-label">Total Interactions</div>
      <div id="statTotalInteractions" class="stat-value-wrapper"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="stat-trend"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"><polyline points="18 15 12 9 6 15"/></svg>+24.7%</div>
      </div>
      <div class="stat-label">Avg. Engagement</div>
      <div id="statMostActive" class="stat-value-wrapper"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
    </div>
  </div>

  {{-- TOP USERS GRID --}}
  <div class="tug-section">
    <div class="tug-header">
      <div class="tug-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Top Users by Engagement
      </div>
      <span class="tug-period-badge" id="topUsersPeriod">Last 24h</span>
    </div>

    <div class="tug-grid">
      <div class="tug-card tug-card--donut">
        <div class="tug-card-head">
          <span class="tug-card-label">Engagement Share</span>
          <span class="tug-card-sub">Top 5 users</span>
        </div>
        <div class="tug-donut-skel" id="tugDonutSkel">
          <div class="tug-donut-skel-circle"></div>
          <div class="tug-donut-skel-legend">
            @for($i = 0; $i < 5; $i++)
            <div class="tug-donut-skel-leg-item">
              <div class="tug-donut-skel-dot"></div>
              <div class="tug-donut-skel-bar" style="width:{{ 80 - $i * 12 }}%"></div>
            </div>
            @endfor
          </div>
        </div>
        <div class="tug-donut-wrap" id="tugDonutWrap" style="display:none;">
          <div id="tugDonutChart" style="width:100%;height:100%;"></div>
        </div>
      </div>

      <div class="tug-card tug-card--list">
        <div class="tug-card-head">
          <span class="tug-card-label">Top Contributors</span>
          <span class="tug-card-sub">Ranked by interactions</span>
        </div>
        <div class="tug-list" id="tugRankedList">
          @for ($s = 0; $s < 5; $s++)
          <div class="tug-row {{ $s === 0 ? 'tug-row--first' : '' }}" style="pointer-events:none;">
            <div class="tug-skel tug-skel--rank"></div>
            <div class="tug-skel tug-skel--avatar"></div>
            <div class="tug-skel-info">
              <div class="tug-skel tug-skel--name"></div>
              <div class="tug-skel tug-skel--handle"></div>
            </div>
            <div class="tug-skel tug-skel--score"></div>
          </div>
          @endfor
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Grid — hidden -->
  <div class="charts-grid" style="display:none;">
    <div class="chart-card">
      <div class="chart-header"><h3>Engagement Timeline</h3><span class="badge">Hourly</span></div>
      <div id="engagementTimelineChart" class="chart-wrapper"></div>
    </div>
    <div class="chart-card">
      <div class="chart-header"><h3>Interaction Distribution</h3><span class="badge">By Type</span></div>
      <div id="interactionDistributionChart" class="chart-wrapper"></div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="table-section" data-lazy-load="usersTable">
    <div class="table-header">
      <div class="table-title">
        <h3>Active Users Ranking</h3>
        <p class="table-subtitle">Sorted by total activity — click user to view detailed analytics</p>
      </div>
      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="searchInput" placeholder="Search users..." onkeyup="filterTable()">
        </div>
        <div class="actions-dropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsDropdown(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
            Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="actions-dropdown-menu" id="actionsDropdownMenu">
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();exportCSV()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Export to CSV
            </a>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();refreshData()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>Refresh Data
            </a>
            <div class="actions-dropdown-divider"></div>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();printTable()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Print Table
            </a>
          </div>
        </div>
      </div>
    </div>
    <div id="tableLoading" class="loading-skeleton" style="height:400px;"></div>
    <div id="tableWrapper" style="display:none;overflow-x:auto;"></div>
    <div id="paginationWrapper" class="pagination" style="display:none;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;"></div>
    <div id="emptyState" style="display:none;text-align:center;padding:60px 20px;color:var(--text-secondary);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 16px;opacity:.4;display:block;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <p style="font-size:15px;font-weight:500;">No user data found for the selected date range.</p>
    </div>
  </div>

  @endif
</div>

<!-- ═══════════════════════════════════════════════════════
     USER DETAIL MODAL (UDM)
═══════════════════════════════════════════════════════ -->
<div class="user-detail-modal" id="userDetailModal" role="dialog" aria-modal="true">
  <div class="modal-overlay" onclick="UDM.close()"></div>
  <div class="udm-content">
    <div class="udm-topbar">
      <span class="udm-topbar-title">User Profile</span>
      <button class="udm-close" onclick="UDM.close()" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round">
          <line x1="18" y1="6"  x2="6"  y2="18"/>
          <line x1="6"  y1="6"  x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="udm-body" id="udmBody"></div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     TWEET DETAIL MODAL (TDM)
═══════════════════════════════════════════════════════ -->
<div class="tdm-modal" id="tweetDetailModal" role="dialog" aria-modal="true">
  <div class="tdm-overlay" onclick="TDM.close()"></div>
  <div class="tdm-content">
    <div class="tdm-topbar">
      <button class="tdm-back-btn" onclick="TDM.close()" aria-label="Back">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="tdm-topbar-title">Tweet Detail</span>
      <button class="tdm-close-btn" onclick="TDM.close()" aria-label="Close">
        <svg viewBox="0 0 24 24" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6"  y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="tdm-body" id="tdmBody"></div>
  </div>
</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const MSCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
};

let allData           = [];
let currentPage       = 1;
let usersPerPage      = 20;
let tugDonutInst      = null;

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
const numFmt = n => (!n && n !== 0) ? '0' : new Intl.NumberFormat('en-US').format(n);
const numK   = n => {
  n = parseInt(n || 0);
  return n >= 1e6 ? (n/1e6).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'K' : String(n);
};
const getInitials = name => {
  if (!name || name === 'Unknown') return '?';
  const p = name.trim().split(/\s+/);
  return p.length === 1 ? p[0].substring(0,2).toUpperCase() : (p[0][0]+p[p.length-1][0]).toUpperCase();
};
const eng = u => parseInt(u.engagement || u.posts || u.y || 0);

function _esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function _escAttr(s) {
  return String(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
}
function _escHref(s) {
  return String(s||'').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ══════════════════════════════════════════════════════
   ECHARTS REGISTRY
══════════════════════════════════════════════════════ */
const MSCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer:'canvas' });
    this._i[id] = c;
    return c;
  },
  disposeAll() {
    Object.values(this._i).forEach(c => { try { c.dispose(); } catch(e){} });
    this._i = {};
  }
};
window.addEventListener('resize', () => {
  Object.values(MSCharts._i).forEach(c => { try { if (!c.isDisposed()) c.resize(); } catch(e){} });
});

/* ══════════════════════════════════════════════════════
   TIME RANGE
══════════════════════════════════════════════════════ */
const MSTimeRange = {
  set(range) {
    document.querySelectorAll('.time-range-btn').forEach(b => b.classList.toggle('active', b.dataset.range === range));
    if (range === 'custom') { MSDp.open(); return; }
    const today = new Date(), start = new Date();
    if (range==='24h') start.setDate(today.getDate()-1);
    if (range==='7d')  start.setDate(today.getDate()-7);
    if (range==='30d') start.setDate(today.getDate()-30);
    document.getElementById('hSD').value = _fmtDate(start);
    document.getElementById('hED').value = _fmtDate(today);
    document.getElementById('msDpDisplay').innerHTML = _fmtDisp(start)+' – '+_fmtDisp(today);
    const pm = {'24h':'Last 24h','7d':'Last 7 Days','30d':'Last 30 Days'};
    document.getElementById('topUsersPeriod').textContent = pm[range]||'Last 24h';
    MSPage.reload();
  }
};
function _fmtDate(d){ return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
function _fmtDisp(d){ const M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; return `${M[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${d.getFullYear()}`; }

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const MSDp = (() => {
  let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init(){
    const si=document.getElementById('hSD'),ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):(()=>{const d=new Date();d.setDate(d.getDate()-6);return d;})();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('msDpTrigger').addEventListener('click',open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&document.getElementById('msDpModal').classList.contains('show'))close();});
  }
  function open(){const m=document.getElementById('msDpModal');m.style.display='flex';requestAnimationFrame(()=>m.classList.add('show'));render();}
  function close(){const m=document.getElementById('msDpModal');m.classList.remove('show');setTimeout(()=>{m.style.display='none';},250);}
  function apply(){
    document.getElementById('hSD').value=fmt(ds);
    document.getElementById('hED').value=fmt(de);
    document.getElementById('msDpDisplay').textContent=_fmtDisp(ds)+' – '+_fmtDisp(de);
    document.getElementById('topUsersPeriod').textContent='Custom Range';
    close();MSPage.reload();
  }
  function nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
  function onPreset(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    const today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.p){
      case 'today':     ds=new Date(today);de=new Date(today);break;
      case 'yesterday': ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case 'last7':     de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case 'last30':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.p!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);}
    updDisp();render();
  }
  function render(){renderCal('msDpCal1',m1);renderCal('msDpCal2',m2);updDisp();}
  function renderCal(id,month){
    const el=document.getElementById(id);if(!el)return;
    const y=month.getFullYear(),mn=month.getMonth();
    const first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
    const today=new Date();today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d);date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameD(date,today))cls+=' today';if(date>today)cls+=' disabled';
      if(ds&&de){if(sameD(date,ds)||sameD(date,de))cls+=' selected';else if(date>ds&&date<de)cls+=' in-range';}
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date);d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}
        else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
        updDisp();render();
      });
    });
  }
  function updDisp(){const el=document.getElementById('msDpRangeText');if(el&&ds&&de)el.textContent=_fmtDisp(ds)+' – '+_fmtDisp(de);}
  function fmt(d){if(!d)return '';return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sameD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
  return {init,open,close,apply,nav};
})();

/* ══════════════════════════════════════════════════════
   TOP USERS — Top 5
══════════════════════════════════════════════════════ */
const TUG5_PALETTE = ['#16a085','#1d9bf0','#00ba7c','#8b5cf6','#f59e0b'];

const MSTopUsers = {
  _data: [],
  updateData(users) {
    this._data = [...users].sort((a,b) => eng(b)-eng(a)).slice(0,5);
    const ar = document.querySelector('.time-range-btn.active')?.dataset.range || '24h';
    const pm = {'24h':'Last 24h','7d':'Last 7 Days','30d':'Last 30 Days','custom':'Custom Range'};
    document.getElementById('topUsersPeriod').textContent = pm[ar] || 'Last 24h';
    this._renderList();
    requestAnimationFrame(() => this._renderDonut());
  },
  _renderDonut() {
    const skel = document.getElementById('tugDonutSkel');
    const wrap = document.getElementById('tugDonutWrap');
    if (!wrap) return;
    if (!this._data.length) {
      if (skel) skel.style.display = 'none';
      wrap.style.display = 'block';
      wrap.innerHTML = `<div class="tug-empty" style="min-height:400px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>No data available</span></div>`;
      return;
    }
    if (skel) skel.style.display = 'none';
    wrap.style.display = 'block';
    if (tugDonutInst) { try { tugDonutInst.dispose(); } catch(e){} tugDonutInst = null; }
    setTimeout(() => {
      const dom = document.getElementById('tugDonutChart');
      if (!dom) return;
      tugDonutInst = echarts.init(dom, null, { renderer:'canvas' });
      MSCharts._i['tugDonutChart'] = tugDonutInst;
      const total = this._data.reduce((s,u) => s+eng(u), 0);
      const seriesData = this._data.map((u,i) => ({
        name: u.name || u.username || 'Unknown',
        value: eng(u), username: u.username || '',
        itemStyle: { color: TUG5_PALETTE[i], borderColor:'#fff', borderWidth:3 },
      }));
      tugDonutInst.setOption({
        backgroundColor:'transparent',
        animation:true, animationDuration:800, animationEasing:'cubicOut',
        tooltip:{
          trigger:'item',backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,
          padding:[10,14],textStyle:{color:'#f8fafc',fontFamily:"'DM Sans',sans-serif",fontSize:12},
          extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);',
          formatter: p => {
            const pct = total > 0 ? ((p.value/total)*100).toFixed(1) : '0.0';
            return `<div style="font-weight:700;margin-bottom:5px;font-size:13px;">${p.name}<br><span style="color:#94a3b8;font-weight:400;font-size:11px;">@${p.data.username}</span></div>
                    <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Engagements</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                    <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#16a085;">${pct}%</span></div>`;
          },
        },
        legend:{ show:false },
        series:[{
          name:'Engagements', type:'pie',
          radius:['46%','64%'], center:['50%','52%'],
          avoidLabelOverlap:true, minAngle:8,
          label:{
            show:true, position:'outside', alignTo:'labelLine',
            bleedMargin:12, distanceToLabelLine:6, lineHeight:18,
            fontFamily:"'DM Sans',sans-serif", fontSize:11.5, color:'#374151',
            formatter: p => {
              const name   = p.name.length > 13 ? p.name.slice(0,12)+'…' : p.name;
              const handle = ('@'+p.data.username).length > 13 ? ('@'+p.data.username).slice(0,12)+'…' : '@'+p.data.username;
              return `{name|${name}}\n{handle|${handle}}\n{eng|${numK(p.value)}}`;
            },
            rich:{
              name:  { fontWeight:'700', fontSize:12, color:'#1a202c', lineHeight:20 },
              handle:{ fontWeight:'400', fontSize:10.5, color:'#64748b', lineHeight:17 },
              eng:   { fontWeight:'700', fontSize:11, color:'#16a085', lineHeight:17, backgroundColor:'#e8f8f4', borderRadius:4, padding:[1,5] },
            },
          },
          labelLine:{
            show:true, length:18, length2:22, smooth:0.3, minTurnAngle:90,
            lineStyle:{ color:'#c4cdd8', width:1.3, type:'solid' },
          },
          emphasis:{
            scale:true, scaleSize:5,
            itemStyle:{ shadowBlur:10, shadowColor:'rgba(0,0,0,.12)' },
            label:{ fontSize:12, fontWeight:'700' },
          },
          data: seriesData,
        }],
        graphic:[
          { type:'text', left:'center', top:'47%', z:100, style:{ text:numK(total), fill:'#0f172a', font:"700 26px 'DM Sans',sans-serif", textAlign:'center' } },
          { type:'text', left:'center', top:'55%', z:100, style:{ text:'TOTAL', fill:'#94a3b8', font:"600 10px 'DM Sans',sans-serif", textAlign:'center', letterSpacing:2 } },
        ],
      });
      tugDonutInst.resize();
      tugDonutInst.on('click', p => {
        if (p.componentType === 'series') {
          const user = this._data.find(u => (u.name||u.username) === p.name);
          if (user) UDM.open(user);
        }
      });
    }, 50);
  },
  _renderList() {
    const container = document.getElementById('tugRankedList');
    if (!container) return;
    if (!this._data.length) {
      container.innerHTML = `<div class="tug-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg><span>No users found</span></div>`;
      return;
    }
    const maxE = eng(this._data[0]) || 1;
    const rankCls = i => i===0?'tug-rank-badge--1':i===1?'tug-rank-badge--2':i===2?'tug-rank-badge--3':'';
    container.innerHTML = this._data.map((user, i) => {
      const name  = user.name || user.username || 'Unknown';
      const uname = user.username || '';
      const e     = eng(user);
      const pct   = Math.round((e/maxE)*100);
      const color = TUG5_PALETTE[i];
      const av    = user.profile_image_url || user.avatar || '';
      const hasAv = av && !av.startsWith('/external');
      const src   = hasAv ? av : `https://unavatar.io/twitter/${uname}`;
      const init  = getInitials(name);
      const ujson = _escAttr(JSON.stringify(user));
      const sM    = parseInt(user.mentions  || 0);
      const sR    = parseInt(user.replies   || 0);
      const sRt   = parseInt(user.retweets  || 0);
      return `
        <div class="tug-row ${i===0?'tug-row--first':''}" onclick="UDM.open(${ujson}, ${sM}, ${sR}, ${sRt})">
          <span class="tug-rank-badge ${rankCls(i)}">${i+1}</span>
          <span class="tug-color-dot" style="background:${color};"></span>
          <img src="${src}" alt="${_esc(name)}" class="tug-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
          <div class="tug-avatar-fallback" style="display:none;">${_esc(init)}</div>
          <div class="tug-user-info">
            <div class="tug-user-name">${_esc(name)}</div>
            <div class="tug-user-handle">@${_esc(uname)}</div>
          </div>
          <div class="tug-user-right">
            <span class="tug-user-eng">${numK(e)}</span>
            <span class="tug-user-bar">
              <span class="tug-user-bar-fill" data-pct="${pct}" style="width:0%;background:${color};"></span>
            </span>
          </div>
        </div>`;
    }).join('');
    requestAnimationFrame(() => {
      container.querySelectorAll('.tug-user-bar-fill').forEach(bar => { bar.style.width = bar.dataset.pct + '%'; });
    });
  }
};

/* ══════════════════════════════════════════════════════
   USER DETAIL MODAL (UDM)
══════════════════════════════════════════════════════ */
const UDM = (() => {
  let _user = null, _statMentions = 0, _statReplies = 0, _statRetweets = 0;
  let _mentions = [], _hasMore = false, _apiStart = 0, _page = 1;
  const _PER_PAGE = 10;
  let _abortCtrl = null;

  function _resetState(user, sM, sR, sRt) {
    if (_abortCtrl) { try { _abortCtrl.abort(); } catch(e){} }
    _abortCtrl    = new AbortController();
    _user         = user;
    _statMentions = sM  || user.mentions  || 0;
    _statReplies  = sR  || user.replies   || 0;
    _statRetweets = sRt || user.retweets  || 0;
    _mentions     = [];
    _hasMore      = false;
    _apiStart     = 0;
    _page         = 1;
  }

  async function open(userOrJson, statMentions, statReplies, statRetweets) {
    let user;
    try { user = (typeof userOrJson === 'string') ? JSON.parse(userOrJson) : userOrJson; }
    catch(e) { console.error('UDM.open: bad input', e); return; }

    _resetState(user, statMentions, statReplies, statRetweets);

    const modal = document.getElementById('userDetailModal');
    const body  = document.getElementById('udmBody');
    body.innerHTML = '';
    modal.style.display = 'flex';
    requestAnimationFrame(() => modal.classList.add('show'));

    body.innerHTML = _profileHTML(user) + _mentionsSkeletonHTML();
    body.scrollTop = 0;

    const username = user.username || '';
    const signal   = _abortCtrl.signal;
    const data = await _fetch(username, 0, signal);

    if (signal.aborted) return;
    if (data) {
      _mentions = data.mentions  || [];
      _hasMore  = data.has_more  || false;
      _apiStart = data.next_api_start || 0;
    }

    const skelWrap = document.getElementById('udmMentionsSkeleton');
    if (skelWrap && !signal.aborted) {
      const tmp = document.createElement('div');
      tmp.innerHTML = _mentionsHTML();
      skelWrap.replaceWith(tmp.firstElementChild);
    }
  }

  function close() {
    if (_abortCtrl) { try { _abortCtrl.abort(); } catch(e){} _abortCtrl = null; }
    const modal = document.getElementById('userDetailModal');
    modal.classList.remove('show');
    setTimeout(() => {
      modal.style.display = 'none';
      document.getElementById('udmBody').innerHTML = '';
    }, 240);
  }

  async function _fetch(username, apiStart, signal) {
    try {
      const u = _user || {};
      const url = `/mk/api/x/user-detailed-mentions?project_id=${MSCfg.pid}`
                + `&username=${encodeURIComponent(username)}`
                + `&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`
                + `&api_start=${apiStart}`
                + `&stat_mentions=${u.mentions||0}&stat_replies=${u.replies||0}&stat_retweets=${u.retweets||0}`;
      const r = await fetch(url, signal ? { signal } : {});
      if (!r.ok) return null;
      const j = await r.json();
      return j.success ? j.data : null;
    } catch(e) {
      if (e.name === 'AbortError') return null;
      console.error('UDM fetch:', e); return null;
    }
  }

  /* ─── Profile HTML ─── */
  function _profileHTML(u) {
    const name      = u.name || u.username || 'Unknown';
    const uname     = u.username || '';
    const src       = _avatarSrc(u);
    const init      = getInitials(name);
    const followers = parseInt(u.followers || u.author_followers_count || u.followers_count || 0);
    const statMentions  = _statMentions;
    const statReplies   = _statReplies;
    const statRetweets  = _statRetweets;
    const statTotal     = statMentions + statReplies + statRetweets;

    return `
      <!-- Banner -->
      <div class="udm-banner">
        <div class="udm-banner-dots"></div>
      </div>

      <!-- Profile section -->
      <div class="udm-profile-section">
        <div class="udm-profile-row">
          <div class="udm-avatar-wrap">
            <img src="${src}" class="udm-avatar"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                 alt="${_esc(name)}">
            <div class="udm-avatar-fb" style="display:none;">${_esc(init)}</div>
          </div>
          <a href="https://twitter.com/${_esc(uname)}" target="_blank" class="udm-view-btn">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            View on X
          </a>
        </div>

        <div class="udm-name">
          ${_esc(name)}
          <span class="udm-verified-badge" title="X User">
            <svg viewBox="0 0 24 24" fill="#1d9bf0">
              <path d="M22.25 12c0-1.43-.88-2.67-2.19-3.34.46-1.39.2-2.9-.81-3.91s-2.52-1.27-3.91-.81c-.66-1.31-1.91-2.19-3.34-2.19s-2.67.88-3.33 2.19c-1.4-.46-2.91-.2-3.92.81s-1.26 2.52-.8 3.91C1.88 9.33 1 10.57 1 12s.88 2.67 2.19 3.34c-.46 1.39-.2 2.9.8 3.91s2.52 1.26 3.92.8c.66 1.31 1.9 2.19 3.33 2.19s2.68-.88 3.34-2.19c1.39.46 2.9.2 3.91-.8s1.27-2.52.81-3.91C21.37 14.67 22.25 13.43 22.25 12zm-6.47-1.53L11.11 15.1a.75.75 0 01-1.06 0L7.72 12.77a.75.75 0 011.06-1.06l1.8 1.8 4.13-4.13a.75.75 0 111.06 1.06l.01.03z"/>
            </svg>
          </span>
        </div>

        <div class="udm-handle">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="#94a3b8">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
          <a href="https://twitter.com/${_esc(uname)}" target="_blank">@${_esc(uname)}</a>
        </div>

        <div class="udm-follower-chip">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <strong>${numFmt(followers)}</strong> Followers
        </div>
      </div>

      <!-- Engagement stats -->
      <div class="udm-eng-strip">
        <div class="udm-eng-item udm-eng-item--mentions">
          <div class="udm-eng-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <span class="udm-eng-val">${numFmt(statMentions)}</span>
          <span class="udm-eng-lbl">Mentions</span>
        </div>
        <div class="udm-eng-item udm-eng-item--replies">
          <div class="udm-eng-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
            </svg>
          </div>
          <span class="udm-eng-val">${numFmt(statReplies)}</span>
          <span class="udm-eng-lbl">Replies</span>
        </div>
        <div class="udm-eng-item udm-eng-item--retweets">
          <div class="udm-eng-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/>
              <polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>
            </svg>
          </div>
          <span class="udm-eng-val">${numFmt(statRetweets)}</span>
          <span class="udm-eng-lbl">Retweets</span>
        </div>
      </div>

      <!-- Total engagement -->
      <div class="udm-total-row">
        <div class="udm-total-row-label">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
          Total Engagement
        </div>
        <div class="udm-total-row-val">${numFmt(statTotal)}</div>
      </div>`;
  }

  /* ─── Mentions Skeleton ─── */
  function _mentionsSkeletonHTML() {
    return `
      <div class="udm-mentions-wrap" id="udmMentionsSkeleton">
        <div class="udm-mentions-head">
          <div class="udm-mentions-title">
            <div class="udm-mentions-title-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            Tweets & Mentions
          </div>
          <span class="udm-mentions-count">Loading…</span>
        </div>
        <div class="udm-loading">
          <div class="udm-spinner"></div>
          <span class="udm-loading-txt">Fetching tweets…</span>
        </div>
      </div>`;
  }

  /* ─── Mentions section HTML ─── */
  function _mentionsHTML() {
    const total      = _mentions.length;
    const totalPages = Math.ceil(total / _PER_PAGE);
    const startIdx   = (_page - 1) * _PER_PAGE;
    const endIdx     = Math.min(startIdx + _PER_PAGE, total);
    const pageMentions = _mentions.slice(startIdx, endIdx);
    const countLabel = _hasMore ? `${total} loaded · more available` : `${total} found`;

    let html = `
      <div class="udm-mentions-wrap">
        <div class="udm-mentions-head">
          <div class="udm-mentions-title">
            <div class="udm-mentions-title-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            Tweets & Mentions
          </div>
          <span class="udm-mentions-count">${countLabel}</span>
        </div>`;

    if (!total && !_hasMore) {
      html += `<div class="udm-empty-mentions">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <p>No mentions found for this period.</p>
      </div></div>`;
      return html;
    }

    html += `<div class="udm-mentions-list">`;
    pageMentions.forEach(m => { html += _cardHTML(m); });
    html += `</div>`;

    if (total > _PER_PAGE || _hasMore) {
      const isLastPage  = _page >= totalPages;
      const canLoadMore = isLastPage && _hasMore;
      html += `<div class="udm-pagination" id="udmPagination">`;
      html += `<span class="udm-pg-info">Page ${_page} of ${totalPages}${_hasMore?'+':''} &nbsp;·&nbsp; ${startIdx+1}–${endIdx} of ${total}${_hasMore?'+':''}</span>`;
      html += `<div class="udm-pg-btns">`;
      html += `<button class="udm-pg-btn" ${_page<=1?'disabled':''} onclick="UDM.goPage(${_page-1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>`;
      const maxShow = 5;
      let pStart = Math.max(1, _page-2), pEnd = Math.min(totalPages, pStart+maxShow-1);
      if (pEnd-pStart < maxShow-1) pStart = Math.max(1, pEnd-maxShow+1);
      if (pStart > 1) html += `<button class="udm-pg-btn" onclick="UDM.goPage(1)">1</button>`;
      if (pStart > 2) html += `<span class="udm-pg-ellipsis">…</span>`;
      for (let p = pStart; p <= pEnd; p++) {
        html += `<button class="udm-pg-btn ${p===_page?'active':''}" onclick="UDM.goPage(${p})">${p}</button>`;
      }
      if (pEnd < totalPages-1) html += `<span class="udm-pg-ellipsis">…</span>`;
      if (pEnd < totalPages)   html += `<button class="udm-pg-btn" onclick="UDM.goPage(${totalPages})">${totalPages}</button>`;
      if (canLoadMore) {
        html += `<button class="udm-pg-btn udm-pg-btn--load" id="udmPgLoadMore" onclick="UDM.fetchNextPage()">Load More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12"><polyline points="9 18 15 12 9 6"/></svg></button>`;
      } else {
        html += `<button class="udm-pg-btn" ${_page>=totalPages?'disabled':''} onclick="UDM.goPage(${_page+1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button>`;
      }
      html += `</div></div>`;
    }

    html += '</div>';
    return html;
  }

  function goPage(p) {
    const totalPages = Math.ceil(_mentions.length / _PER_PAGE);
    if (p < 1 || p > totalPages) return;
    _page = p; _redrawMentions();
    const head = document.querySelector('#udmBody .udm-mentions-head');
    if (head) head.scrollIntoView({ behavior:'smooth', block:'nearest' });
  }

  async function fetchNextPage() {
    const btn = document.getElementById('udmPgLoadMore');
    if (btn) { btn.disabled = true; btn.innerHTML = `<span class="udm-pg-loading-dot"></span> Loading…`; }
    const signal = _abortCtrl?.signal;
    const data   = await _fetch(_user?.username||'', _apiStart, signal);
    if (!data || signal?.aborted) {
      if (btn) { btn.disabled = false; btn.innerHTML = 'Load More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12"><polyline points="9 18 15 12 9 6"/></svg>'; }
      return;
    }
    _mentions  = [..._mentions, ...(data.mentions || [])];
    _hasMore   = data.has_more || false;
    _apiStart  = data.next_api_start || 0;
    const totalPages = Math.ceil(_mentions.length / _PER_PAGE);
    _page = Math.min(_page+1, totalPages);
    _redrawMentions();
  }

  function _redrawMentions() {
    const wrap = document.querySelector('#udmBody .udm-mentions-wrap');
    if (!wrap) return;
    const tmp = document.createElement('div');
    tmp.innerHTML = _mentionsHTML();
    wrap.replaceWith(tmp.firstElementChild);
  }

  /* ─── Linkify ─── */
  function _linkifyText(raw) {
    if (!raw) return '<em style="color:#94a3b8;">No content available</em>';
    let t = raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    t = t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g, (url) => {
      const href = url.replace(/&amp;/g,'&');
      return `<a href="${href}" target="_blank" rel="noopener" class="udm-text-link udm-text-link--url">${url}</a>`;
    });
    t = t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,
      '<a href="https://twitter.com/$1" target="_blank" rel="noopener" class="udm-text-link udm-text-link--mention">@$1</a>'
    );
    t = t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F\u0400-\u04FF]+)/g,
      '<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" class="udm-text-link udm-text-link--hashtag">#$1</a>'
    );
    return t;
  }

  /* ─── Single tweet card ─── */
  function _cardHTML(m) {
    const rawTs = m.timestamp || m.created_at || '';
    let dtStr = '';
    if (rawTs) {
      const d = new Date(rawTs);
      if (!isNaN(d)) dtStr = d.toLocaleString('id-ID',{ day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', timeZone:'Asia/Jakarta' }) + ' WIB';
    }
    const sent    = m.sentiment || 'neutral';
    const sentCls = sent==='positive'?'pos':sent==='negative'?'neg':'neu';
    const sentLbl = sent==='positive'?'Positive':sent==='negative'?'Negative':'Neutral';
    const author  = m.author_name || m.author || _user?.username || '';
    const handle  = m.author || _user?.username || '';
    const mtype   = (m.mention_type || m.tcode || 'tweet').toLowerCase();
    let typeCls='tweet', typeLbl='Tweet';
    if (mtype.includes('reply')   || mtype==='rep') { typeCls='reply';   typeLbl='Reply'; }
    if (mtype.includes('retweet') || mtype==='rt')  { typeCls='retweet'; typeLbl='Retweet'; }
    if (mtype.includes('mention') || mtype==='men') { typeCls='mention'; typeLbl='Mention'; }
    const likes   = parseInt(m.likes    || m.num_likes    || 0);
    const rts     = parseInt(m.retweets || m.num_shares   || 0);
    const reps    = parseInt(m.replies  || m.num_comments || 0);
    const text    = (m.text || '') ? _linkifyText(m.text||'') : '<em style="color:#94a3b8;">No content available</em>';
    const viewLink = (m.url && m.url !== '#')
      ? `<a href="${_escHref(m.url)}" target="_blank" rel="noopener" class="udm-card-view-link" onclick="event.stopPropagation();">
           <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
           View Tweet
         </a>` : '';
    const miniSrc  = `https://unavatar.io/twitter/${handle}`;
    const miniInit = getInitials(author);
    const mjson    = _escAttr(JSON.stringify(m));

    return `
      <div class="udm-card" onclick="TDM.open(${mjson}); event.stopPropagation();">
        <div class="udm-card-header">
          <img src="${miniSrc}" class="udm-card-mini-avatar"
               onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
               alt="${_esc(author)}">
          <div class="udm-card-mini-avatar-fb" style="display:none;">${_esc(miniInit)}</div>
          <div class="udm-card-meta">
            <div class="udm-card-row1">
              <div class="udm-card-name-block">
                <span class="udm-card-author">${_esc(author)}</span>
                <span class="udm-card-handle">@${_esc(handle)}${dtStr ? ` · ${dtStr}` : ''}</span>
              </div>
              <span class="udm-pill udm-pill--${sentCls}">${sentLbl}</span>
            </div>
            <div class="udm-card-badges">
              <span class="udm-type-badge udm-type-badge--${typeCls}">${typeLbl}</span>
            </div>
          </div>
        </div>

        <div class="udm-card-text">${text}</div>

        <div class="udm-card-foot">
          <div class="udm-card-actions">
            <span class="udm-card-action likes">
              <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
              ${numFmt(likes)}
            </span>
            <span class="udm-card-action rts">
              <svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
              ${numFmt(rts)}
            </span>
            <span class="udm-card-action">
              <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              ${numFmt(reps)}
            </span>
          </div>
          ${viewLink}
        </div>
      </div>`;
  }

  function _avatarSrc(u) {
    const av = u.profile_image_url || u.avatar || '';
    return (av && !av.startsWith('/external')) ? av : `https://unavatar.io/twitter/${u.username||''}`;
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && document.getElementById('userDetailModal')?.classList.contains('show')) close();
  });

  return { open, close, goPage, fetchNextPage };
})();

function openUserModal(arg) { UDM.open(arg); }
function closeUserModal()   { UDM.close(); }

/* ══════════════════════════════════════════════════════
   TWEET DETAIL MODAL (TDM)
══════════════════════════════════════════════════════ */
const TDM = (() => {

  function open(mentionOrJson) {
    let m;
    try { m = (typeof mentionOrJson === 'string') ? JSON.parse(mentionOrJson) : mentionOrJson; }
    catch(e) { console.error('TDM.open: bad input', e); return; }

    const body = document.getElementById('tdmBody');
    if (!body) return;
    body.innerHTML = _buildHTML(m);
    body.scrollTop = 0;
    body.querySelectorAll('a').forEach(a => a.addEventListener('click', e => e.stopPropagation()));

    const modal = document.getElementById('tweetDetailModal');
    modal.style.display = 'flex';
    requestAnimationFrame(() => modal.classList.add('show'));
  }

  function close() {
    const modal = document.getElementById('tweetDetailModal');
    modal.classList.remove('show');
    setTimeout(() => {
      modal.style.display = 'none';
      document.getElementById('tdmBody').innerHTML = '';
    }, 200);
  }

  function _buildHTML(m) {
    const author     = m.author_name || m.author || '';
    const handle     = m.author || '';
    const avatarSrc  = `https://unavatar.io/twitter/${handle}`;
    const init       = getInitials(author);
    const rawTs      = m.timestamp || m.created_at || '';
    let dtStr = '';
    if (rawTs) {
      const d = new Date(rawTs);
      if (!isNaN(d)) dtStr = d.toLocaleString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit', timeZone:'Asia/Jakarta' }) + ' WIB';
    }
    const likes  = parseInt(m.likes    || m.num_likes    || 0);
    const rts    = parseInt(m.retweets || m.num_shares   || 0);
    const reps   = parseInt(m.replies  || m.num_comments || 0);
    const sent   = m.sentiment || 'neutral';
    const sentCls = sent==='positive'?'pos':sent==='negative'?'neg':'neu';
    const sentLbl = sent==='positive'?'Positive':sent==='negative'?'Negative':'Neutral';
    const mtype  = (m.mention_type || m.tcode || 'tweet').toLowerCase();
    let typeCls='tweet', typeLbl='Tweet';
    if (mtype.includes('reply')   || mtype==='rep') { typeCls='reply';   typeLbl='Reply'; }
    if (mtype.includes('retweet') || mtype==='rt')  { typeCls='retweet'; typeLbl='Retweet'; }
    if (mtype.includes('mention') || mtype==='men') { typeCls='mention'; typeLbl='Mention'; }
    const rawText  = m.text || '';
    const text     = rawText ? _linkify(rawText) : '<em style="color:#94a3b8;">No content available</em>';
    const tweetUrl = (m.url && m.url !== '#') ? m.url : `https://twitter.com/${handle}`;

    return `
      <!-- Author -->
      <div class="tdm-author-section">
        <img src="${avatarSrc}" class="tdm-author-avatar"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
             alt="${_esc(author)}">
        <div class="tdm-author-avatar-fb" style="display:none;">${_esc(init)}</div>
        <div class="tdm-author-info">
          <div class="tdm-author-name">
            ${_esc(author)}
            <svg viewBox="0 0 24 24" width="16" height="16" fill="#1d9bf0" style="flex-shrink:0;">
              <path d="M22.25 12c0-1.43-.88-2.67-2.19-3.34.46-1.39.2-2.9-.81-3.91s-2.52-1.27-3.91-.81c-.66-1.31-1.91-2.19-3.34-2.19s-2.67.88-3.33 2.19c-1.4-.46-2.91-.2-3.92.81s-1.26 2.52-.8 3.91C1.88 9.33 1 10.57 1 12s.88 2.67 2.19 3.34c-.46 1.39-.2 2.9.8 3.91s2.52 1.26 3.92.8c.66 1.31 1.9 2.19 3.33 2.19s2.68-.88 3.34-2.19c1.39.46 2.9.2 3.91-.8s1.27-2.52.81-3.91C21.37 14.67 22.25 13.43 22.25 12zm-6.47-1.53L11.11 15.1a.75.75 0 01-1.06 0L7.72 12.77a.75.75 0 011.06-1.06l1.8 1.8 4.13-4.13a.75.75 0 111.06 1.06l.01.03z"/>
            </svg>
          </div>
          <div class="tdm-author-handle">
            <a href="https://twitter.com/${_esc(handle)}" target="_blank" rel="noopener">@${_esc(handle)}</a>
          </div>
        </div>
        <a href="${_escHref(tweetUrl)}" target="_blank" rel="noopener" class="tdm-view-x-btn">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
          View on X
        </a>
      </div>

      <!-- Tweet body -->
      <div class="tdm-tweet-body">
        <div class="tdm-tweet-text">${text}</div>
        <div class="tdm-tweet-meta">
          <span class="tdm-badge tdm-badge--${sentCls}">${sentLbl}</span>
          <span class="tdm-badge tdm-badge--${typeCls}">${typeLbl}</span>
          ${dtStr ? `<span class="tdm-timestamp">${dtStr}</span>` : ''}
        </div>
      </div>

      <!-- Stats strip -->
      <div class="tdm-stats-strip">
        <div class="tdm-stat-item tdm-stat-item--likes">
          <div class="tdm-stat-icon">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <span class="tdm-stat-val">${numFmt(likes)}</span>
          <span class="tdm-stat-lbl">Likes</span>
        </div>
        <div class="tdm-stat-item tdm-stat-item--rts">
          <div class="tdm-stat-icon">
            <svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
          </div>
          <span class="tdm-stat-val">${numFmt(rts)}</span>
          <span class="tdm-stat-lbl">Retweets</span>
        </div>
        <div class="tdm-stat-item tdm-stat-item--replies">
          <div class="tdm-stat-icon">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <span class="tdm-stat-val">${numFmt(reps)}</span>
          <span class="tdm-stat-lbl">Replies</span>
        </div>
      </div>

      <!-- Footer actions -->
      <div class="tdm-footer">
        <a href="https://twitter.com/${_esc(handle)}" target="_blank" rel="noopener"
           class="tdm-footer-btn tdm-footer-btn--outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          </svg>
          View Profile
        </a>
        <a href="${_escHref(tweetUrl)}" target="_blank" rel="noopener"
           class="tdm-footer-btn tdm-footer-btn--primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
          </svg>
          Open Tweet
        </a>
      </div>`;
  }

  function _linkify(raw) {
    if (!raw) return '<em style="color:#94a3b8;">No content available</em>';
    let t = raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    t = t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g, (url) => {
      const href = url.replace(/&amp;/g,'&');
      return `<a href="${href}" target="_blank" rel="noopener" class="tdm-link tdm-link--url">${url}</a>`;
    });
    t = t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,
      '<a href="https://twitter.com/$1" target="_blank" rel="noopener" class="tdm-link tdm-link--mention">@$1</a>'
    );
    t = t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F\u0400-\u04FF]+)/g,
      '<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" class="tdm-link tdm-link--hashtag">#$1</a>'
    );
    return t;
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      const tdm = document.getElementById('tweetDetailModal');
      if (tdm?.classList.contains('show')) { close(); return; }
    }
  });

  return { open, close };
})();

/* ══════════════════════════════════════════════════════
   LAZY LOAD
══════════════════════════════════════════════════════ */
const loadedComponents = new Set();
const lazyLoadObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const id = entry.target.dataset.lazyLoad;
      if (!loadedComponents.has(id)) {
        loadedComponents.add(id);
        if (id==='userStats'||id==='usersTable') loadData();
        lazyLoadObserver.unobserve(entry.target);
      }
    }
  });
}, { rootMargin:'50px', threshold:.01 });

document.addEventListener('DOMContentLoaded', () => {
  if (MSCfg.pid) {
    loadData();
    document.querySelectorAll('[data-lazy-load]').forEach(el => lazyLoadObserver.observe(el));
  }
  MSDp.init();
});

/* ══════════════════════════════════════════════════════
   MAIN DATA LOAD
══════════════════════════════════════════════════════ */
let dataLoaded = false;

async function loadData() {
  if (dataLoaded) return;
  dataLoaded = true;
  try {
    const res    = await fetch(`/mk/api/x/most-active-users?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const result = await res.json();
    console.log('mostActiveUsers FULL response:', result);
    let rows = null;
    if      (Array.isArray(result?.data?.data))          rows = result.data.data;
    else if (Array.isArray(result?.data?.users))         rows = result.data.users;
    else if (Array.isArray(result?.data?.items))         rows = result.data.items;
    else if (Array.isArray(result?.data))                rows = result.data;
    else if (Array.isArray(result?.users))               rows = result.users;
    else if (Array.isArray(result?.items))               rows = result.items;
    else if (Array.isArray(result))                      rows = result;
    else if (result?.data && typeof result.data === 'object') {
      const vals = Object.values(result.data);
      if (vals.length > 0 && typeof vals[0] === 'object') rows = vals;
    }
    if (rows && rows.length > 0) {
      allData = rows;
      allData.sort((a,b) => eng(b)-eng(a));
      MSTopUsers.updateData(allData);
      renderTable(); updatePagination();
      document.getElementById('tableLoading').style.display      = 'none';
      document.getElementById('tableWrapper').style.display      = 'block';
      document.getElementById('paginationWrapper').style.display = 'flex';
      document.getElementById('emptyState').style.display        = 'none';
    } else {
      console.warn('mostActiveUsers: no rows found, falling back to sample');
      document.getElementById('tableLoading').style.display = 'none';
      document.getElementById('emptyState').style.display   = 'block';
      _sample();
    }
  } catch(err) {
    console.error('loadData error:', err);
    document.getElementById('tableLoading').style.display = 'none';
    document.getElementById('emptyState').style.display   = 'block';
    _sample();
  } finally {
    document.querySelector('[data-lazy-load="usersTable"]')?.classList.add('loaded');
  }
}


/* ══════════════════════════════════════════════════════
   TABLE
══════════════════════════════════════════════════════ */
function renderTable() {
  const si = (currentPage-1)*usersPerPage;
  const cd = allData.slice(si, si+usersPerPage);
  let html = `<table class="data-table"><thead><tr>
    <th>RANK</th><th>AVATAR</th><th>USERNAME</th><th>NAME</th>
    <th>FOLLOWERS</th><th>MENTIONS</th><th>REPLIES</th><th>RETWEETS</th>
    <th style="text-align:center;">ENGAGEMENT</th><th></th>
  </tr></thead><tbody>`;
  cd.forEach((u,i) => {
    const rank  = si+i+1, name = u.name||u.username||'Unknown', uname = u.username||'';
    const av    = u.profile_image_url||'', init = getInitials(name);
    const hasAv = av && !av.startsWith('/external');
    const src   = hasAv ? av : `https://unavatar.io/twitter/${uname}`;
    const ujson = _escAttr(JSON.stringify(u));
    const sM=parseInt(u.mentions||0), sR=parseInt(u.replies||0), sRt=parseInt(u.retweets||0);
    const avatarHtml = `<img src="${src}" alt="${_esc(name)}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="user-avatar-fallback" style="display:none;">${_esc(init)}</div>`;
    html += `<tr onclick="UDM.open(${ujson}, ${sM}, ${sR}, ${sRt})">
      <td><strong>${rank}</strong></td>
      <td>${avatarHtml}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${uname}</a></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${_esc(name)}</a></td>
      <td><div class="activity-stat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>${numFmt(u.followers||0)}</div></td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.mentions||0)}</td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.replies||0)}</td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.retweets||0)}</td>
      <td style="text-align:center;"><div class="activity-stat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>${numFmt(eng(u))}</div></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();"><svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>View</a></td>
    </tr>`;
  });
  html += '</tbody></table>';
  document.getElementById('tableWrapper').innerHTML = html;
}

function getPageRange(cur, total) {
  if (total<=7)      return Array.from({length:total},(_,i)=>i+1);
  if (cur<=4)        return [1,2,3,4,5,'...',total];
  if (cur>=total-3)  return [1,'...',total-4,total-3,total-2,total-1,total];
  return [1,'...',cur-1,cur,cur+1,'...',total];
}
function updatePagination() {
  const tp  = Math.ceil(allData.length/usersPerPage);
  const w   = document.getElementById('paginationWrapper');
  const from= allData.length?(currentPage-1)*usersPerPage+1:0;
  const to  = Math.min(currentPage*usersPerPage,allData.length);
  let html  = `<div class="pagination-info">Showing ${numFmt(from)}–${numFmt(to)} of ${numFmt(allData.length)} users</div><div style="display:flex;align-items:center;gap:6px;">`;
  html += `<button class="page-btn" onclick="changePage(${currentPage-1})" ${currentPage===1?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg></button>`;
  getPageRange(currentPage,tp).forEach(p => {
    html += p==='...'
      ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
      : `<button class="page-btn ${p===currentPage?'active':''}" onclick="changePage(${p})">${p}</button>`;
  });
  html += `<button class="page-btn" onclick="changePage(${currentPage+1})" ${currentPage===tp?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg></button></div>`;
  w.innerHTML = html;
  w.style.display = allData.length>0 ? 'flex' : 'none';
}
function changePage(p) {
  const tp = Math.ceil(allData.length/usersPerPage);
  if (p<1||p>tp) return;
  currentPage=p; renderTable(); updatePagination();
  document.querySelector('.table-section').scrollIntoView({behavior:'smooth',block:'start'});
}
function filterTable() {
  const term = document.getElementById('searchInput').value.toLowerCase();
  if (!term) { currentPage=1; renderTable(); updatePagination(); return; }
  const filtered = allData.filter(u => ((u.name||'')+' '+(u.username||'')).toLowerCase().includes(term));
  let html = `<table class="data-table"><thead><tr>
    <th>RANK</th><th>AVATAR</th><th>USERNAME</th><th>NAME</th>
    <th>FOLLOWERS</th><th>MENTIONS</th><th>REPLIES</th><th>RETWEETS</th>
    <th style="text-align:center;">TOTAL</th><th></th>
  </tr></thead><tbody>`;
  filtered.forEach((u,i) => {
    const name=u.name||u.username||'Unknown', uname=u.username||'';
    const av=u.profile_image_url||'', init=getInitials(name);
    const hasAv=av&&!av.startsWith('/external');
    const src=hasAv?av:`https://unavatar.io/twitter/${uname}`;
    const ujson=_escAttr(JSON.stringify(u));
    const sM=parseInt(u.mentions||0), sR=parseInt(u.replies||0), sRt=parseInt(u.retweets||0);
    const avatarHtml=`<img src="${src}" alt="${_esc(name)}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="user-avatar-fallback" style="display:none;">${_esc(init)}</div>`;
    html+=`<tr onclick="UDM.open(${ujson}, ${sM}, ${sR}, ${sRt})">
      <td><strong>${i+1}</strong></td><td>${avatarHtml}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${uname}</a></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${_esc(name)}</a></td>
      <td>${numFmt(u.followers||0)}</td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.mentions||0)}</td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.replies||0)}</td>
      <td style="color:var(--text-secondary);font-weight:600">${numFmt(u.retweets||0)}</td>
      <td style="text-align:center;">${numFmt(eng(u))}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();">View</a></td>
    </tr>`;
  });
  html += '</tbody></table>';
  document.getElementById('tableWrapper').innerHTML = html;
  document.getElementById('paginationWrapper').style.display = 'none';
}

/* ══════════════════════════════════════════════════════
   ACTIONS DROPDOWN
══════════════════════════════════════════════════════ */
function toggleActionsDropdown(e) { e.stopPropagation(); document.getElementById('actionsDropdownMenu').classList.toggle('show'); }
document.addEventListener('click', () => document.getElementById('actionsDropdownMenu')?.classList.remove('show'));
function exportCSV() {
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  if (!allData.length) return;
  let csv = 'Rank,Username,Name,Followers,Following,Mentions,Replies,Retweets,Total\n';
  allData.forEach((u,i) => {
    const n=(u.name||'').replace(/,/g,' ').replace(/"/g,'""');
    csv += `${i+1},"@${u.username||''}","${n}",${u.followers||0},${u.following||0},${u.mentions||0},${u.replies||0},${u.retweets||0},${eng(u)}\n`;
  });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));
  a.download = `most_active_users_${MSCfg.sd}_${MSCfg.ed}.csv`;
  a.click();
}
function refreshData() { document.getElementById('actionsDropdownMenu').classList.remove('show'); window.location.reload(); }
function printTable() {
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  const content = document.getElementById('tableWrapper').innerHTML;
  const w = window.open('','_blank');
  w.document.write(`<!DOCTYPE html><html><head><title>Most Active Users - X</title><style>body{font-family:Arial,sans-serif;padding:20px;}table{width:100%;border-collapse:collapse;}th{background:#f8fafc;padding:10px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;}td{padding:10px;font-size:12px;border-bottom:1px solid #f1f5f9;}</style></head><body><h1>Most Active Users — X (Twitter)</h1><p style="color:#64748b;font-size:13px;">Date Range: ${MSCfg.sd} to ${MSCfg.ed}</p>${content}</body></html>`);
  w.document.close(); w.focus(); setTimeout(()=>{w.print();w.close();},250);
}

/* ══════════════════════════════════════════════════════
   MSPage
══════════════════════════════════════════════════════ */
const MSPage = {
  reload() {
    MSCharts.disposeAll();
    if (tugDonutInst) { try { tugDonutInst.dispose(); } catch(e){} tugDonutInst=null; }
    const skel = document.getElementById('tugDonutSkel');
    const wrap = document.getElementById('tugDonutWrap');
    if (skel) skel.style.display = 'flex';
    if (wrap) wrap.style.display = 'none';
    dataLoaded=false; allData=[]; currentPage=1;
    loadData();
  }
};

/* ══════════════════════════════════════════════════════
   LEGACY MODERATION PURGE
══════════════════════════════════════════════════════ */
(function() {
  const LEGACY_SELECTORS = [
    '.mention-sentiment-edit','.sentiment-edit','.sentiment-ctrl','.sentiment-form','.sent-ctrl','.edit-sentiment',
    '[class*="sentiment-edit"]','[class*="sentiment-ctrl"]','[class*="edit-sent"]',
    '.sentiment-btn','.sent-btn','.btn-sentiment','.btn-positive','.btn-negative','.btn-neutral','[data-sentiment]',
    '.relevance-ctrl','.relevance-form','.relevance-btn','.btn-relevant','.btn-irrelevant','.relevant-btn','.irrelevant-btn',
    '[class*="relevance"]','[class*="irrelevant"]','[data-relevance]',
    '.set-mention-as','.mention-action-panel','.mention-moderation','.mention-controls',
    '.moderation-panel','.moderation-ctrl','.mod-panel','.mod-ctrl','.mod-actions',
    '.mention-source','.source-panel','.source-twitter','.tweet-source','.mention-source-panel',
    '.action-panel','.ctrl-panel','.legacy-panel','.old-controls',
  ].join(',');
  function purgeLegacy(root) {
    try { root.querySelectorAll(LEGACY_SELECTORS).forEach(el => el.remove()); } catch(e) {}
  }
  const udmBody = document.getElementById('udmBody');
  if (udmBody) {
    const observer = new MutationObserver(() => purgeLegacy(udmBody));
    observer.observe(udmBody, { childList:true, subtree:true });
  }
})();
</script>
@endsection