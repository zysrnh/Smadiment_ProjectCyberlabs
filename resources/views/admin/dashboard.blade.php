@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* { box-sizing: border-box; }

:root {
  --green:       #027447;
  --green-dk:    #025a35;
  --green-light: #E6F4EE;
  --dark:        #0F172A;
  --mid:         #475569;
  --muted:       #94A3B8;
  --border:      #E2E8F0;
  --bg:          #F8FAFC;
  --white:       #FFFFFF;
  --r:           12px;
  --font:        'Plus Jakarta Sans', sans-serif;
  --sh-sm:       0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
  --sh-md:       0 4px 16px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.04);
  --sh-lg:       0 12px 40px rgba(15,23,42,.14), 0 4px 12px rgba(15,23,42,.06);

  /* Platform colors */
  --c-news:  #3B82F6;
  --c-twit:  #1DA1F2;
  --c-fb:    #1877F2;
  --c-ig:    #E1306C;
  --c-yt:    #FF0000;
  --c-tt:    #111827;
}

/* ══ Topbar ══════════════════════════════════════ */
.adm-topbar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 20px;
  gap: 12px;
  flex-wrap: wrap;
}
.adm-page-title {
  font-family: var(--font);
  font-size: 22px;
  font-weight: 800;
  color: var(--dark);
  margin: 0 0 2px;
  letter-spacing: -.4px;
}
.adm-page-sub {
  font-family: var(--font);
  font-size: 13px;
  color: var(--muted);
  margin: 0;
  font-weight: 500;
}
.adm-date-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 99px;
  font-family: var(--font);
  font-size: 12px;
  font-weight: 600;
  color: var(--mid);
  box-shadow: var(--sh-sm);
}

/* ══ Summary Strip ═══════════════════════════════ */
.summary-strip {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}
.sum-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: var(--sh-sm);
  transition: transform .18s, box-shadow .18s;
}
.sum-card:hover { transform: translateY(-2px); box-shadow: var(--sh-md); }
.sum-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.sum-icon--blue   { background:#EFF6FF; color:#3B82F6; }
.sum-icon--green  { background:#ECFDF5; color:#059669; }
.sum-icon--orange { background:#FFF7ED; color:#F97316; }
.sum-icon--purple { background:#F5F3FF; color:#8B5CF6; }
.sum-info { display: flex; flex-direction: column; gap: 2px; }
.sum-lbl {
  font-family: var(--font); font-size: 11px; font-weight: 600;
  color: var(--muted); text-transform: uppercase; letter-spacing: .5px;
}
.sum-val {
  font-family: var(--font); font-size: 20px; font-weight: 800;
  color: var(--dark); letter-spacing: -.5px; line-height: 1;
}

/* ══ Two-column Main ═════════════════════════════ */
.adm-main {
  display: grid;
  grid-template-columns: 236px 1fr;
  gap: 16px;
  align-items: start;
}

/* ══ Left Sidebar ════════════════════════════════ */
.proj-sidebar {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  box-shadow: var(--sh-sm);
  overflow: hidden;
  position: sticky;
  top: 16px;
}
.proj-sidebar-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 13px 16px;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}
.proj-sidebar-title {
  font-family: var(--font); font-size: 11px; font-weight: 700;
  color: var(--dark); text-transform: uppercase; letter-spacing: .6px;
}
.proj-sidebar-badge {
  background: var(--green); color: #fff;
  padding: 2px 9px; border-radius: 99px;
  font-family: var(--font); font-size: 10px; font-weight: 700;
}
.proj-list { padding: 6px; }
.proj-item {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 10px; border-radius: 8px;
  cursor: pointer;
  transition: background .15s, transform .15s;
  margin-bottom: 2px;
}
.proj-item:hover { background: var(--green-light); transform: translateX(2px); }
.proj-item:hover .proj-dot  { background: var(--green); transform: scale(1.3); }
.proj-item:hover .proj-arr  { opacity: 1; color: var(--green); }
.proj-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #CBD5E1; flex-shrink: 0; transition: all .2s;
}
.proj-info { flex: 1; min-width: 0; }
.proj-name {
  display: block; font-family: var(--font); font-size: 13px; font-weight: 600;
  color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.proj-meta {
  display: block; font-family: var(--font); font-size: 10px;
  color: var(--muted); font-weight: 500; margin-top: 2px;
}
.proj-arr { opacity: 0; transition: opacity .15s; flex-shrink: 0; }

/* ══ Charts Column ═══════════════════════════════ */
.charts-col { display: flex; flex-direction: column; gap: 16px; }

/* ══ Chart Card ══════════════════════════════════ */
.chart-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  box-shadow: var(--sh-sm);
  padding: 20px;
  transition: border-color .3s, box-shadow .3s;
}
.chart-card.hl {
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(2,116,71,.09), var(--sh-md);
}

/* Card Header */
.card-hd {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; margin-bottom: 16px; flex-wrap: wrap;
}
.card-hd-left { display: flex; align-items: center; gap: 10px; }
.card-hd-dot {
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--green); flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(2,116,71,.15);
}
.card-title {
  font-family: var(--font); font-size: 15px; font-weight: 700;
  color: var(--dark); margin: 0 0 2px; letter-spacing: -.2px;
}
.card-sub {
  font-family: var(--font); font-size: 11px;
  color: var(--muted); font-weight: 500;
}
.card-actions { display: flex; align-items: center; gap: 6px; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 14px; background: var(--green); color: #fff;
  border-radius: 8px; font-family: var(--font); font-size: 12px;
  font-weight: 600; text-decoration: none;
  box-shadow: 0 1px 4px rgba(2,116,71,.25);
  transition: background .18s, transform .18s, box-shadow .18s;
}
.btn-primary:hover { background: var(--green-dk); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(2,116,71,.3); color:#fff; }
.btn-icon {
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
  background: var(--bg); border: 1px solid var(--border); border-radius: 8px;
  color: var(--mid); text-decoration: none; transition: all .18s;
}
.btn-icon:hover { background: var(--green); border-color: var(--green); color: #fff; transform: translateY(-1px); }

/* ══ STATS PILLS ══════════════════════════════════
   Each pill is clickable → opens popup
   ════════════════════════════════════════════════ */
.stats-row {
  display: flex; align-items: center; gap: 6px;
  padding: 10px 12px;
  background: var(--bg); border: 1px solid var(--border);
  border-radius: 10px; margin-bottom: 16px;
  overflow-x: auto; scrollbar-width: none;
}
.stats-row::-webkit-scrollbar { display: none; }

.stat-pill {
  display: flex; align-items: center; gap: 7px;
  padding: 7px 11px;
  background: var(--white); border: 1.5px solid var(--border);
  border-radius: 8px; flex-shrink: 0;
  cursor: pointer;
  transition: border-color .15s, box-shadow .15s, transform .15s;
  position: relative;
}
.stat-pill:hover {
  border-color: var(--plat-color, var(--green));
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
  transform: translateY(-1px);
}
.stat-pill.all-pill { border-color: var(--green); background: var(--green-light); }
.stat-pill.all-pill:hover { background: var(--white); }

.stat-dot {
  width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0;
  transition: transform .15s;
}
.stat-pill:hover .stat-dot { transform: scale(1.3); }

.stat-info { display: flex; flex-direction: column; gap: 1px; }
.stat-val {
  font-family: var(--font); font-size: 14px; font-weight: 700;
  color: var(--dark); line-height: 1;
}
.stat-lbl {
  font-family: var(--font); font-size: 9px; font-weight: 600;
  color: var(--muted); text-transform: uppercase; letter-spacing: .4px;
}

/* ── Tooltip hint on hover ── */
.stat-pill::after {
  content: attr(data-tip);
  position: absolute; bottom: calc(100% + 6px); left: 50%;
  transform: translateX(-50%);
  background: var(--dark); color: #fff;
  font-family: var(--font); font-size: 10px; font-weight: 600;
  padding: 3px 8px; border-radius: 5px; white-space: nowrap;
  opacity: 0; pointer-events: none; transition: opacity .15s;
}
.stat-pill:hover::after { opacity: 1; }

/* Card Divider */
.card-divider { height: 1px; background: var(--border); margin-bottom: 16px; }

/* Chart Wrap */
.chart-wrap { height: 230px; position: relative; }

/* Empty */
.empty-main {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 80px 20px; background: var(--white);
  border: 1px dashed var(--border); border-radius: var(--r); text-align: center; gap: 8px;
}
.empty-main h3 { font-family: var(--font); font-size: 16px; font-weight: 700; color: var(--dark); margin: 8px 0 0; }
.empty-main p  { font-family: var(--font); font-size: 13px; color: var(--muted); margin: 0; }

/* ══ MENTIONS POPUP ══════════════════════════════ */
@keyframes popIn { from{opacity:0;transform:translateY(8px) scale(.96)} to{opacity:1;transform:none} }

.mnt-popup {
  position: fixed; z-index: 99999;
  background: var(--white);
  border: 1px solid var(--border); border-radius: 14px;
  box-shadow: var(--sh-lg);
  width: 390px; height: 520px;
  display: none; flex-direction: column; overflow: hidden;
  font-family: var(--font);
  animation: popIn .18s cubic-bezier(.34,1.4,.64,1);
}
.mnt-popup.open { display: flex; }

/* Popup Header */
.mnt-ph {
  display: flex; align-items: center; justify-content: space-between;
  padding: 11px 15px;
  border-bottom: 1px solid var(--border);
  background: var(--bg); flex-shrink: 0;
}
.mnt-ptitle {
  display: flex; align-items: center; gap: 7px;
  font-size: 13px; font-weight: 700; color: var(--dark);
}
.mnt-pdot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.mnt-pclose {
  width: 26px; height: 26px; border-radius: 6px;
  background: transparent; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: var(--muted); font-size: 18px; line-height: 1;
  transition: all .15s;
}
.mnt-pclose:hover { background: #fee2e2; color: #991b1b; }

/* Popup Meta bar */
.mnt-pmeta {
  padding: 6px 15px;
  border-bottom: 1px solid var(--border);
  font-size: 10px; font-weight: 700; color: var(--muted);
  text-transform: uppercase; letter-spacing: .5px;
  display: flex; align-items: center; gap: 7px; flex-shrink: 0;
}
.mnt-pbadge {
  background: var(--green); color: #fff;
  border-radius: 99px; padding: 1px 9px;
  font-size: 11px; font-weight: 800;
}

/* Popup List */
.mnt-plist { overflow-y: auto; flex: 1; padding: 3px 0; min-height: 0; }
.mnt-plist::-webkit-scrollbar { width: 4px; }
.mnt-plist::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.mnt-pitem {
  display: flex; gap: 10px; padding: 10px 15px;
  border-bottom: 1px solid #F8FAFC;
  cursor: pointer; transition: background .1s;
}
.mnt-pitem:last-child { border-bottom: none; }
.mnt-pitem:hover { background: #F0FDF4; }

.mnt-pava {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg, var(--green), var(--green-dk));
  color: #fff; font-weight: 700; font-size: 13px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; overflow: hidden; border: 1.5px solid var(--border);
}
.mnt-pava img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

.mnt-pbody { flex: 1; min-width: 0; }
.mnt-pname {
  font-size: 12px; font-weight: 700; color: var(--dark);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 1px;
}
.mnt-pusn { font-size: 10px; color: var(--muted); font-weight: 500; margin-bottom: 3px; }
.mnt-ptxt {
  font-size: 12px; color: #374151; line-height: 1.5;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
  overflow: hidden; margin-bottom: 4px;
}
.mnt-prow { display: flex; align-items: center; gap: 6px; font-size: 10px; color: var(--muted); }
.mnt-sent {
  padding: 1px 7px; border-radius: 10px;
  font-size: 9px; font-weight: 800;
}
.mnt-sent-p { background: #D1FAE5; color: #065F46; }
.mnt-sent-n { background: #FEE2E2; color: #991B1B; }
.mnt-sent-u { background: #F1F5F9; color: #374151; }

/* yt thumbnail */
.mnt-ythumb {
  width: 56px; height: 38px; border-radius: 5px; object-fit: cover;
  flex-shrink: 0; background: #000; border: 1px solid var(--border);
}

/* Loading / empty states */
.mnt-loading {
  padding: 40px 20px; text-align: center; color: var(--muted);
  font-size: 13px; font-weight: 600;
  display: flex; flex-direction: column; align-items: center; gap: 12px;
}
.mnt-spin {
  width: 28px; height: 28px;
  border: 3px solid var(--border); border-top-color: var(--green);
  border-radius: 50%; animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ══ MENTION DETAIL MODAL ═════════════════════════ */
.mnt-modal-bd {
  position: fixed; inset: 0; z-index: 200000;
  background: rgba(10,15,25,.78); backdrop-filter: blur(10px);
  display: none; align-items: center; justify-content: center; padding: 20px;
}
.mnt-modal-bd.open { display: flex; }
@keyframes mntModalUp { from{opacity:0;transform:translateY(20px) scale(.97)} to{opacity:1;transform:none} }

.mnt-modal {
  width: 100%; max-width: 800px; max-height: calc(100vh - 40px);
  background: var(--white); border-radius: 20px;
  box-shadow: 0 32px 80px rgba(0,0,0,.3);
  display: flex; flex-direction: column; overflow: hidden;
  animation: mntModalUp .22s cubic-bezier(.34,1.3,.64,1);
  font-family: var(--font);
}

/* Modal header */
.mnt-mhd {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 18px; border-bottom: 1px solid var(--border);
  background: var(--bg); flex-shrink: 0;
}
.mnt-mplat {
  display: inline-flex; align-items: center;
  padding: 3px 11px; border-radius: 20px;
  font-size: 10px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .5px; flex-shrink: 0;
}
.mnt-mtitle {
  flex: 1; font-size: 13px; font-weight: 700; color: var(--dark);
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap; min-width: 0;
}
.mnt-mext {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 8px;
  font-size: 11px; font-weight: 700; color: var(--green);
  background: rgba(2,116,71,.08); border: 1px solid rgba(2,116,71,.2);
  cursor: pointer; text-decoration: none; flex-shrink: 0;
  transition: background .15s; white-space: nowrap;
}
.mnt-mext:hover { background: rgba(2,116,71,.16); }
.mnt-mclose {
  width: 30px; height: 30px; border-radius: 8px; border: none;
  background: transparent; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: var(--muted); font-size: 22px; line-height: 1;
  transition: all .15s; flex-shrink: 0;
}
.mnt-mclose:hover { background: #FEE2E2; color: #991B1B; }

/* Modal Author */
.mnt-mauthor {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 18px; border-bottom: 1px solid #F1F5F9;
  flex-shrink: 0;
}
.mnt-mava {
  width: 42px; height: 42px; border-radius: 50%;
  border: 2px solid var(--border); flex-shrink: 0;
  background: linear-gradient(135deg, var(--green), var(--green-dk));
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-weight: 700; font-size: 14px; overflow: hidden;
}
.mnt-mava img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.mnt-maname { font-size: 13px; font-weight: 700; color: var(--dark); }
.mnt-mausn  { font-size: 11px; color: var(--muted); margin-top: 1px; }

/* Modal body */
.mnt-mbody { flex: 1; min-height: 0; overflow-y: auto; }
.mnt-mbody::-webkit-scrollbar { width: 4px; }
.mnt-mbody::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.mnt-mthumb { width: 100%; max-height: 280px; object-fit: cover; display: block; background: #000; }
.mnt-mcontent {
  padding: 18px 20px; font-size: 14px; line-height: 1.75;
  color: var(--dark); word-break: break-word;
  border-bottom: 1px solid #F1F5F9;
}
.mnt-mstats { display: flex; flex-wrap: wrap; gap: 0; border-bottom: 1px solid #F1F5F9; flex-shrink: 0; }
.mnt-mstat { flex: 1; min-width: 80px; padding: 12px 16px; text-align: center; border-right: 1px solid #F1F5F9; }
.mnt-mstat:last-child { border-right: none; }
.mnt-mstat-val { font-size: 17px; font-weight: 800; color: var(--dark); line-height: 1; }
.mnt-mstat-lbl { font-size: 9px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }

.mnt-msent { padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; }
.mnt-msent-p { background: #D1FAE5; color: #065F46; }
.mnt-msent-n { background: #FEE2E2; color: #991B1B; }
.mnt-msent-u { background: #F1F5F9; color: #374151; }

/* YT embed */
.mnt-ytframe { position: relative; padding-bottom: 56.25%; height: 0; background: #000; }
.mnt-ytframe iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: none; }

/* ══ Responsive ══════════════════════════════════ */
@media (max-width: 1200px) { .summary-strip { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 1024px) { .adm-main { grid-template-columns: 1fr; } .proj-sidebar { position: static; } }
@media (max-width: 640px) {
  .summary-strip { grid-template-columns: repeat(2,1fr); gap: 8px; }
  .chart-card { padding: 14px; }
  .card-hd { flex-direction: column; align-items: flex-start; }
  .card-actions { width: 100%; }
  .btn-primary { flex: 1; justify-content: center; }
  .chart-wrap { height: 190px; }
  .adm-topbar { flex-direction: column; align-items: flex-start; }
  .mnt-popup { width: calc(100vw - 24px); }
}
</style>
@endsection

@section('content')

{{-- ── Top Bar ── --}}
<div class="adm-topbar">
  <div>
    <h1 class="adm-page-title">Dashboard</h1>
    <p class="adm-page-sub">Monitor & manage all projects</p>
  </div>
  <div class="adm-date-pill">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
      <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
      <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
    </svg>
    <span id="admDateLabel"></span>
  </div>
</div>

{{-- ── Summary Strip ── --}}
<div class="summary-strip">
  <div class="sum-card">
    <div class="sum-icon sum-icon--blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Total Projects</span>
      <span class="sum-val">{{ count($projects) }}</span>
    </div>
  </div>
  <div class="sum-card">
    <div class="sum-icon sum-icon--green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Total Items</span>
      <span class="sum-val">{{ number_format(collect($projects)->sum(fn($p) => $p['stats']['all'] ?? 0)) }}</span>
    </div>
  </div>
  <div class="sum-card">
    <div class="sum-icon sum-icon--orange">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Active Sources</span>
      <span class="sum-val">6</span>
    </div>
  </div>
  <div class="sum-card">
    <div class="sum-icon sum-icon--purple">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Social Posts</span>
      <span class="sum-val">{{ number_format(collect($projects)->sum(fn($p) => ($p['stats']['twit'] ?? 0) + ($p['stats']['fb'] ?? 0) + ($p['stats']['ig'] ?? 0) + ($p['stats']['yt'] ?? 0) + ($p['stats']['tiktok'] ?? 0))) }}</span>
    </div>
  </div>
</div>

{{-- ── Main 2-col ── --}}
<div class="adm-main">

  {{-- Left: Project List --}}
  <aside class="proj-sidebar">
    <div class="proj-sidebar-head">
      <span class="proj-sidebar-title">Projects</span>
      <span class="proj-sidebar-badge">{{ count($projects) }}</span>
    </div>
    <div class="proj-list">
      @forelse($projects as $project)
      <div class="proj-item" data-id="{{ $project['id'] }}">
        <div class="proj-dot"></div>
        <div class="proj-info">
          <span class="proj-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed' }}</span>
          <span class="proj-meta">#{{ $project['id'] }} · {{ number_format($project['stats']['all'] ?? 0) }} items</span>
        </div>
        <svg class="proj-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="11" height="11">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </div>
      @empty
      <div style="padding:32px 16px;text-align:center;font-family:var(--font);font-size:12px;color:var(--muted);">
        No projects available
      </div>
      @endforelse
    </div>
  </aside>

  {{-- Right: Chart Cards --}}
  <div class="charts-col">
    @forelse($projects as $project)
    <div class="chart-card" id="card-{{ $project['id'] }}">

      {{-- Card Header --}}
      <div class="card-hd">
        <div class="card-hd-left">
          <div class="card-hd-dot"></div>
          <div>
            <h3 class="card-title">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h3>
            <span class="card-sub">Project #{{ $project['id'] }}</span>
          </div>
        </div>
        <div class="card-actions">
          <a href="{{ route('mk.sentiment', ['project_id' => $project['id']]) }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
              <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Analytics
          </a>
          <a href="{{ route('mk.geographic', ['project_id' => $project['id']]) }}" class="btn-icon" title="Geographic">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
              <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
          </a>
          <a href="{{ route('mk.publisher', ['project_id' => $project['id']]) }}" class="btn-icon" title="Publisher">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
          </a>
        </div>
      </div>

      {{-- Stat Pills (clickable) --}}
      @php
        $stats = [
          ['key'=>'all',   'lbl'=>'All',       'color'=>'#027447', 'plat'=>'all'],
          ['key'=>'news',  'lbl'=>'News',      'color'=>'#3B82F6', 'plat'=>'doc'],
          ['key'=>'twit',  'lbl'=>'Twitter',   'color'=>'#1DA1F2', 'plat'=>'twit'],
          ['key'=>'fb',    'lbl'=>'Facebook',  'color'=>'#1877F2', 'plat'=>'fb'],
          ['key'=>'ig',    'lbl'=>'Instagram', 'color'=>'#E1306C', 'plat'=>'ig'],
          ['key'=>'yt',    'lbl'=>'YouTube',   'color'=>'#FF0000', 'plat'=>'yt'],
          ['key'=>'tiktok','lbl'=>'TikTok',    'color'=>'#111827', 'plat'=>'tiktok'],
        ];
        $sd = $dateRange['start'] ?? now()->subDays(6)->format('Y-m-d');
        $ed = $dateRange['end']   ?? now()->format('Y-m-d');
      @endphp

      <div class="stats-row">
        @foreach($stats as $st)
        @php $val = number_format($project['stats'][$st['key']] ?? 0); @endphp
        <div class="stat-pill {{ $st['key'] === 'all' ? 'all-pill' : '' }}"
          data-tip="Lihat mentions {{ $st['lbl'] }}"
          style="--plat-color: {{ $st['color'] }}"
          onclick="openMntPopup(
            '{{ $project['id'] }}',
            '{{ addslashes($project['name'] ?? $project['title'] ?? 'Project') }}',
            '{{ $st['plat'] }}',
            '{{ $st['lbl'] }}',
            '{{ $st['color'] }}',
            '{{ $sd }}',
            '{{ $ed }}',
            event
          )">
          <span class="stat-dot" style="background:{{ $st['color'] }}"></span>
          <div class="stat-info">
            <span class="stat-val">{{ $val }}</span>
            <span class="stat-lbl">{{ $st['lbl'] }}</span>
          </div>
        </div>
        @endforeach
      </div>

      <div class="card-divider"></div>

      {{-- Chart --}}
      <div class="chart-wrap">
        <canvas id="chart-{{ $project['id'] }}"></canvas>
      </div>

    </div>
    @empty
    <div class="empty-main">
      <svg viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" width="56" height="56">
        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
      </svg>
      <h3>No Projects Found</h3>
      <p>There are no projects available at this moment.</p>
    </div>
    @endforelse
  </div>

</div>

{{-- ══ MENTION POPUP ══ --}}
<div class="mnt-popup" id="mntPopup">
  <div class="mnt-ph">
    <div class="mnt-ptitle">
      <div class="mnt-pdot" id="mntPopDot"></div>
      <span id="mntPopTitle">Mentions</span>
    </div>
    <button class="mnt-pclose" onclick="closeMntPopup()">×</button>
  </div>
  <div class="mnt-pmeta">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    <span id="mntPopMeta">—</span>
    <span class="mnt-pbadge" id="mntPopCount">…</span>
    <span>mentions</span>
  </div>
  <div class="mnt-plist" id="mntPopList"></div>
</div>

{{-- ══ DETAIL MODAL ══ --}}
<div class="mnt-modal-bd" id="mntModalBd" onclick="if(event.target===this)closeMntModal()">
  <div class="mnt-modal">
    <div class="mnt-mhd">
      <span class="mnt-mplat" id="mntMdPlat"></span>
      <span class="mnt-mtitle" id="mntMdTitle"></span>
      <a class="mnt-mext" id="mntMdExt" href="#" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="11" height="11"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Buka Link
      </a>
      <button class="mnt-mclose" onclick="closeMntModal()">×</button>
    </div>
    <div class="mnt-mauthor">
      <div class="mnt-mava" id="mntMdAva"></div>
      <div>
        <div class="mnt-maname" id="mntMdAname"></div>
        <div class="mnt-mausn"  id="mntMdAusn"></div>
      </div>
      <div style="margin-left:auto;display:flex;align-items:center;gap:8px">
        <span class="mnt-msent" id="mntMdSent"></span>
        <span style="font-size:11px;color:var(--muted)" id="mntMdDate"></span>
      </div>
    </div>
    <div class="mnt-mbody">
      <div id="mntMdEmbed"></div>
      <div class="mnt-mstats" id="mntMdStats"></div>
      <div class="mnt-mcontent" id="mntMdText"></div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
// ════════════════════════════════════════════════
// CONSTANTS
// ════════════════════════════════════════════════
const PLAT_LABELS = { all:'All', doc:'Online News', twit:'Twitter/X', fb:'Facebook', ig:'Instagram', yt:'YouTube', tiktok:'TikTok' };
const PLAT_COLORS = { all:'#027447', doc:'#3B82F6', twit:'#1DA1F2', fb:'#1877F2', ig:'#E1306C', yt:'#FF0000', tiktok:'#111827' };
const PLAT_PILL_STYLE = {
  doc:   'background:#DBEAFE;color:#1D4ED8',
  twit:  'background:#E0F2FE;color:#0369A1',
  fb:    'background:#EDE9FE;color:#5B21B6',
  ig:    'background:#FCE7F3;color:#9D174D',
  yt:    'background:#FEE2E2;color:#B91C1C',
  tiktok:'background:#F1F5F9;color:#334155',
  all:   'background:#ECFDF5;color:#065F46',
};

// ════════════════════════════════════════════════
// INIT
// ════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  // Date label
  const now = new Date();
  document.getElementById('admDateLabel').textContent =
    now.toLocaleDateString('en-US', { weekday:'short', day:'numeric', month:'short', year:'numeric' });

  // Sidebar scroll
  document.querySelectorAll('.proj-item').forEach(el => {
    el.addEventListener('click', () => {
      const id = el.dataset.id;
      const card = document.getElementById(`card-${id}`);
      if (!card) return;
      card.scrollIntoView({ behavior:'smooth', block:'start' });
      card.classList.add('hl');
      setTimeout(() => card.classList.remove('hl'), 2200);
    });
  });

  // Close popup on outside click
  document.addEventListener('mousedown', e => {
    const popup = document.getElementById('mntPopup');
    if (popup.classList.contains('open') && !popup.contains(e.target)) closeMntPopup();
  });

  // ESC closes modal
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeMntModal(); closeMntPopup(); } });

  // Build charts
  buildCharts();
});

// ════════════════════════════════════════════════
// CHARTS
// ════════════════════════════════════════════════
function buildCharts() {
  const C = { blue:'#5AB9EA', orange:'#F59E0B', gray:'#94A3B8', red:'#F87171', white:'#FFFFFF', light:'#94A3B8', border:'#E2E8F0', dark:'#0F172A' };
  const projects = @json($projects);

  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;

    const tl = project.timeline || {};
    let labels = tl.dates || [];
    let allData, posData, neuData, negData;

    if (!labels.length) {
      labels   = ['20 Feb','21 Feb','22 Feb','23 Feb','24 Feb','25 Feb','26 Feb'];
      allData  = [520, 480, 610, 590, 720, 680, 800];
      posData  = [210, 200, 260, 250, 300, 280, 340];
      neuData  = [200, 185, 230, 220, 270, 255, 300];
      negData  = [110, 95,  120, 120, 150, 145, 160];
    } else {
      allData = tl.values || [];
      if (tl.sentiment?.positive) {
        posData = tl.sentiment.positive;
        neuData = tl.sentiment.neutral;
        negData = tl.sentiment.negative;
      } else {
        posData = allData.map(v => Math.round(v * 0.42));
        neuData = allData.map(v => Math.round(v * 0.38));
        negData = allData.map(v => Math.round(v * 0.20));
      }
    }

    const ds = (label, data, color, dashed = false) => ({
      label, data,
      borderColor: color, backgroundColor: 'transparent',
      borderWidth: dashed ? 1.8 : 2.2,
      borderDash: dashed ? [4,3] : [],
      tension: 0.45,
      pointRadius: 4, pointHoverRadius: 6,
      pointBackgroundColor: color, pointBorderColor: C.white, pointBorderWidth: 2,
      fill: false,
    });

    new Chart(ctx, {
      type: 'line',
      data: { labels, datasets: [
        ds('New', allData, C.blue),
        ds('Positive', posData, C.orange),
        ds('Neutral',  neuData, C.gray,  true),
        ds('Negative', negData, C.red,   true),
      ]},
      options: {
        responsive: true, maintainAspectRatio: false,
        layout: { padding: { top:4, right:8, bottom:0, left:4 } },
        plugins: {
          legend: {
            position:'bottom', align:'start',
            labels: { usePointStyle:true, pointStyle:'circle', padding:16,
              font:{ size:11, weight:'600', family:"'Plus Jakarta Sans',sans-serif" },
              color:C.light, boxWidth:7, boxHeight:7 }
          },
          tooltip: {
            mode:'index', intersect:false,
            backgroundColor:'#fff', titleColor:C.dark, bodyColor:C.dark,
            borderColor:C.border, borderWidth:1, padding:12, cornerRadius:8,
            titleFont:{ size:11,weight:'700',family:"'Plus Jakarta Sans',sans-serif" },
            bodyFont:{ size:11,weight:'500',family:"'Plus Jakarta Sans',sans-serif" },
            displayColors:true, boxWidth:7, boxHeight:7, boxPadding:5,
            callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()}` }
          },
        },
        scales: {
          x: {
            grid:{ display:false }, border:{ display:false },
            ticks:{ font:{size:10,weight:'500',family:"'Plus Jakarta Sans',sans-serif"}, color:C.light, padding:6, maxRotation:0, autoSkip:true, maxTicksLimit:8 }
          },
          y: {
            beginAtZero:true,
            grid:{ color:'rgba(226,232,240,.8)', drawBorder:false, lineWidth:1 },
            border:{ display:false },
            ticks:{ font:{size:10,weight:'500',family:"'Plus Jakarta Sans',sans-serif"}, color:C.light, padding:10, maxTicksLimit:5, callback:v=>v>=1000?(v/1000)+'k':v }
          },
        },
        interaction:{ intersect:false, mode:'index' },
      },
    });
  });
}

// ════════════════════════════════════════════════
// POPUP STATE
// ════════════════════════════════════════════════
let _popupCache    = {};
let _popupItems    = [];
let _popupPlatform = '';

// ════════════════════════════════════════════════
// OPEN POPUP
// ════════════════════════════════════════════════
async function openMntPopup(projectId, projectTitle, platform, platLabel, color, startDate, endDate, event) {
  event.stopPropagation();
  const popup = document.getElementById('mntPopup');

  // Position popup near click
  const vw = window.innerWidth, vh = window.innerHeight;
  const pw = 390, ph = 520;
  let left = event.clientX + 14, top = event.clientY - 40;
  if (left + pw > vw - 8) left = event.clientX - pw - 14;
  if (top + ph > vh - 8) top = vh - ph - 8;
  if (top < 8) top = 8;
  if (left < 8) left = 8;
  popup.style.left = left + 'px';
  popup.style.top  = top + 'px';

  // Set header
  document.getElementById('mntPopDot').style.background = color;
  document.getElementById('mntPopTitle').textContent     = _trunc(projectTitle, 26) + ' · ' + platLabel;
  document.getElementById('mntPopMeta').textContent      = startDate + ' – ' + endDate;
  document.getElementById('mntPopCount').textContent     = '…';

  const list = document.getElementById('mntPopList');
  list.innerHTML = `<div class="mnt-loading"><div class="mnt-spin"></div>Memuat mentions…</div>`;
  popup.classList.add('open');

  const key = `${projectId}_${platform}_${startDate}_${endDate}`;
  try {
    let items = _popupCache[key];
    if (!items) {
      items = await _fetchItems(projectId, platform, startDate, endDate);
      _popupCache[key] = items;
    }
    _popupPlatform = platform;
    _popupItems    = items;
    document.getElementById('mntPopCount').textContent = _fmt(items.length);
    _renderPopupList(list, items, platform);
  } catch (e) {
    list.innerHTML = `<div class="mnt-loading">❌ Gagal memuat<br><small style="font-size:11px;margin-top:4px;display:block;color:#94A3B8">${e.message}</small></div>`;
    document.getElementById('mntPopCount').textContent = '0';
  }
}

function closeMntPopup() {
  document.getElementById('mntPopup').classList.remove('open');
}

// ════════════════════════════════════════════════
// FETCH ITEMS
// ════════════════════════════════════════════════
async function _fetchItems(projectId, platform, startDate, endDate) {
  const q = `project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&rows=300&start=0`;
  const map = {
    all:    `/mk/api/news/mentions?${q}`,
    doc:    `/mk/api/news/mentions?${q}`,
    twit:   `/mk/api/x/most-status?${q}`,
    fb:     `/mk/api/news/fb-top-status?${q}&sub=fblike`,
    ig:     `/mk/api/news/ig-top-status?${q}&sub=postbylike`,
    yt:     `/mk/api/news/ytb-top-status?${q}`,
    tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
  };
  const url = map[platform];
  if (!url) throw new Error('Platform tidak dikenali');

  const ctrl = new AbortController();
  const tid  = setTimeout(() => ctrl.abort(), 25000);
  const res  = await fetch(url, { signal: ctrl.signal });
  clearTimeout(tid);
  if (!res.ok) throw new Error('HTTP ' + res.status);

  const data = await res.json();
  let items = [];
  if (data.success && Array.isArray(data.data)) items = data.data;
  else if (Array.isArray(data.data)) items = data.data;
  else if (Array.isArray(data)) items = data;

  // Filter doc-only from all mentions
  if (platform === 'doc' || platform === 'all') {
    if (platform === 'doc') {
      items = items.filter(m => {
        const tc = String(m.tcode||'').toLowerCase();
        const mt = String(m.media_type||'').toLowerCase();
        return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online';
      });
    }
  }
  return items;
}

// ════════════════════════════════════════════════
// RENDER POPUP LIST
// ════════════════════════════════════════════════
function _renderPopupList(list, items, platform) {
  if (!items.length) {
    list.innerHTML = `<div class="mnt-loading">📭 Tidak ada data untuk periode ini.</div>`;
    return;
  }

  const SHOW = 60;
  let html = items.slice(0, SHOW).map((item, idx) => {
    const name  = _itemName(item, platform);
    const usn   = _itemUsn(item, platform);
    const text  = _itemText(item).slice(0, 180);
    const ini   = _ini(name);
    const sent  = _sent(item);
    const sCls  = sent === '1' ? 'mnt-sent-p' : sent === '-1' ? 'mnt-sent-n' : 'mnt-sent-u';
    const sLbl  = sent === '1' ? 'Pos' : sent === '-1' ? 'Neg' : 'Neu';
    const dt    = _fmtDate(item.date_created || '');
    const av    = _av(item, platform);
    const safeI = ini.replace(/['"]/g, '');

    let avaHtml = ini;
    if (av && av.startsWith('http')) avaHtml = `<img src="${_esc(av)}" onerror="this.parentElement.textContent='${safeI}'">`;

    // YT thumbnail
    const thumb = _thumb(item, platform);
    const thumbHtml = (platform === 'yt' && thumb)
      ? `<img class="mnt-ythumb" src="${_esc(thumb)}" onerror="this.style.display='none'">`
      : '';

    const eng = _eng(item, platform);

    return `<div class="mnt-pitem" onclick="_openMntModal(${idx})">
      <div class="mnt-pava">${avaHtml}</div>
      ${thumbHtml}
      <div class="mnt-pbody">
        <div class="mnt-pname">${_esc(name)}</div>
        ${usn ? `<div class="mnt-pusn">${_esc(usn)}</div>` : ''}
        <div class="mnt-ptxt">${_esc(text || '(tidak ada konten)')}</div>
        <div class="mnt-prow">
          <span class="mnt-sent ${sCls}">${sLbl}</span>
          ${eng ? `<span>${eng}</span>` : ''}
          ${dt ? `<span style="margin-left:auto">${dt}</span>` : ''}
        </div>
      </div>
    </div>`;
  }).join('');

  if (items.length > SHOW) {
    html += `<div style="padding:9px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--muted);background:var(--bg);border-top:1px dashed var(--border);">
      +${_fmt(items.length - SHOW)} mentions lainnya
    </div>`;
  }
  list.innerHTML = html;
}

// ════════════════════════════════════════════════
// DETAIL MODAL
// ════════════════════════════════════════════════
function _openMntModal(idx) {
  const item     = _popupItems[idx];
  const platform = _popupPlatform;
  if (!item) return;

  const name    = _itemName(item, platform);
  const usn     = _itemUsn(item, platform);
  const av      = _av(item, platform);
  const ini     = _ini(name);
  const safeI   = ini.replace(/['"]/g, '');
  const text    = _itemText(item);
  const sent    = _sent(item);
  const url     = _url(item, platform);
  const dt      = _fmtDate(item.date_created || '');

  // Pill
  const pill = document.getElementById('mntMdPlat');
  pill.textContent = PLAT_LABELS[platform] || platform;
  pill.style.cssText = `display:inline-flex;align-items:center;padding:3px 11px;border-radius:20px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;flex-shrink:0;${PLAT_PILL_STYLE[platform]||'background:#F1F5F9;color:#374151'}`;

  // Title
  document.getElementById('mntMdTitle').textContent = _trunc(text || name, 60);

  // Ext link
  const ext = document.getElementById('mntMdExt');
  ext.style.display = url ? 'inline-flex' : 'none';
  if (url) ext.href = url;

  // Avatar
  const avaEl = document.getElementById('mntMdAva');
  if (av && av.startsWith('http')) avaEl.innerHTML = `<img src="${_esc(av)}" onerror="this.parentElement.textContent='${safeI}'">`;
  else avaEl.textContent = ini;

  document.getElementById('mntMdAname').textContent = name;
  document.getElementById('mntMdAusn').textContent  = usn || '';

  // Sentiment
  const sEl = document.getElementById('mntMdSent');
  const sMap = { '1': ['Positive ●','mnt-msent-p'], '-1': ['Negative ●','mnt-msent-n'], '0': ['Neutral ●','mnt-msent-u'] };
  const [sTxt, sCls] = sMap[sent] || sMap['0'];
  sEl.textContent = sTxt; sEl.className = 'mnt-msent ' + sCls;

  document.getElementById('mntMdDate').textContent = dt;

  // Embed
  document.getElementById('mntMdEmbed').innerHTML = _buildEmbed(item, platform, url);

  // Stats
  document.getElementById('mntMdStats').innerHTML = _buildStats(item, platform);

  // Text
  document.getElementById('mntMdText').textContent = text || '(tidak ada konten)';

  document.getElementById('mntModalBd').classList.add('open');
}

function closeMntModal() {
  document.getElementById('mntModalBd').classList.remove('open');
  document.getElementById('mntMdEmbed').innerHTML = '';
}

// ════════════════════════════════════════════════
// ITEM HELPERS
// ════════════════════════════════════════════════
function _itemName(item, plat) {
  const h = item.author_scr_name || item.screen_name || item.author_id || '';
  return plat === 'doc'
    ? (item.author_name || item.publisher || item.hostname || h || 'Unknown')
    : (item.author_name || item.channel_name || h || 'Unknown');
}
function _itemUsn(item, plat) {
  const raw = item.author_scr_name || item.screen_name || item.username || item.author_id || '';
  if (!raw) return '';
  if (plat === 'twit' || plat === 'ig' || plat === 'tiktok') return raw.startsWith('@') ? raw : '@' + raw;
  return raw;
}
function _itemText(item) {
  return ((item.content || item.caption || item.description || item.name || item.title || item.text || '')
    .replace(/<[^>]*>/g, '').trim());
}
function _ini(name) {
  if (!name || name === 'Unknown') return '?';
  const p = name.trim().split(/\s+/);
  return p.length === 1 ? p[0].slice(0,2).toUpperCase() : (p[0][0] + p[p.length-1][0]).toUpperCase();
}
function _sent(item) {
  const s = String(item.class_sentiment || item.sentiment || '0').trim().toLowerCase();
  if (s === '1' || s === 'positive' || s === 'positif') return '1';
  if (s === '-1' || s === '2' || s === 'negative' || s === 'negatif') return '-1';
  return '0';
}
function _av(item, plat) {
  return item.avatar_url || item.profile_image_url || item.profile_img || item.author_image || '';
}
function _thumb(item, plat) {
  if (plat === 'yt') {
    const vid = _ytId(item);
    if (vid) return `https://img.youtube.com/vi/${vid}/mqdefault.jpg`;
  }
  if (plat === 'tiktok') return item.video_cover || item.thumbnail_url || item.cover || '';
  return '';
}
function _ytId(item) {
  const u = item.url || item.link || '';
  const m = u.match(/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  if (m) return m[1];
  return item.video_id || item.youtube_id || '';
}
function _url(item, plat) {
  const raw = item.url || item.link || item.post_url || '';
  if (raw) return raw;
  if (plat === 'twit') {
    const usn = item.author_scr_name || item.screen_name || '';
    const id  = item.post_id || item.id_str || item.id || '';
    if (usn && id) return `https://x.com/${usn}/status/${id}`;
  }
  if (plat === 'yt') { const vid = _ytId(item); if (vid) return `https://www.youtube.com/watch?v=${vid}`; }
  return '';
}
function _fmtDate(str) {
  if (!str) return '';
  try { return new Date(str).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}); }
  catch(e) { return str.split('T')[0]; }
}
function _eng(item, plat) {
  const f = n => n > 0 ? _fmt(n) : null;
  const p = [];
  if (plat === 'twit') { const rt=f(item.num_retweeted||item.retweet_count||0); const lk=f(item.num_likes||item.likes||0); if(rt) p.push('🔁'+rt); if(lk) p.push('❤'+lk); }
  else if (plat === 'yt') { const v=f(item.num_views||item.views||0); const lk=f(item.num_likes||0); if(v) p.push('👁'+v); if(lk) p.push('👍'+lk); }
  else if (plat === 'tiktok') { const v=f(item.views||item.num_views||0); const lk=f(item.likes||item.num_likes||0); if(v) p.push('👁'+v); if(lk) p.push('❤'+lk); }
  else if (plat === 'ig') { const lk=f(item.num_likes||item.likes||0); const c=f(item.num_comments||0); if(lk) p.push('❤'+lk); if(c) p.push('💬'+c); }
  else if (plat === 'fb') { const lk=f(item.likes||item.num_likes||0); const s=f(item.shares||0); if(lk) p.push('👍'+lk); if(s) p.push('📤'+s); }
  return p.join('  ');
}

// Embed builder
function _buildEmbed(item, plat, url) {
  if (plat === 'yt') {
    const vid = _ytId(item);
    if (vid) return `<div class="mnt-ytframe"><iframe src="https://www.youtube.com/embed/${vid}?rel=0" allowfullscreen loading="lazy"></iframe></div>`;
    const th = _thumb(item, plat);
    if (th) return `<img class="mnt-mthumb" src="${_esc(th)}" onerror="this.style.display='none'">`;
  }
  if (plat === 'tiktok') {
    const vid = item.video_id || item.post_id || '';
    if (vid) return `<div style="display:flex;justify-content:center;background:#f8fafc;padding:12px;max-height:480px;overflow-y:auto"><blockquote class="tiktok-embed" cite="${_esc(url)}" data-video-id="${_esc(vid)}" style="max-width:325px;min-width:280px"><section></section></blockquote><script async src="https://www.tiktok.com/embed.js"><\/script></div>`;
  }
  if (plat === 'ig') return `<div style="padding:16px 18px;background:#fdf4ff;border-bottom:1px solid #e2e8f0;font-size:12px;font-weight:600;color:#6b21a8">📸 Instagram — klik "Buka Link" untuk lihat postingan asli.</div>`;
  const img = item.image_url || item.full_picture || item.image || '';
  if (img && img.startsWith('http')) return `<img class="mnt-mthumb" src="${_esc(img)}" onerror="this.style.display='none'">`;
  return '';
}

// Stats builder
function _buildStats(item, plat) {
  const st = (lbl, val) => {
    const n = parseInt(val, 10) || 0;
    if (!n) return '';
    return `<div class="mnt-mstat"><div class="mnt-mstat-val">${_fmt(n)}</div><div class="mnt-mstat-lbl">${lbl}</div></div>`;
  };
  if (plat==='twit') return [st('Retweet',item.num_retweeted||item.retweet_count), st('Likes',item.num_likes||item.likes), st('Replies',item.reply_count)].join('');
  if (plat==='yt')   return [st('Views',item.num_views||item.views), st('Likes',item.num_likes), st('Komentar',item.num_comments)].join('');
  if (plat==='tiktok')return [st('Views',item.views||item.num_views), st('Likes',item.likes||item.num_likes), st('Share',item.shares), st('Komentar',item.num_comments)].join('');
  if (plat==='ig')   return [st('Likes',item.num_likes||item.likes), st('Komentar',item.num_comments)].join('');
  if (plat==='fb')   return [st('Likes',item.likes||item.num_likes), st('Shares',item.shares), st('Komentar',item.num_comments)].join('');
  if (plat==='doc')  return [st('Est. Reach',item.est_reach||item.reach), st('Followers',item.followers)].join('');
  return '';
}

// ════════════════════════════════════════════════
// UTILS
// ════════════════════════════════════════════════
window._openMntModal = _openMntModal;
function _fmt(n) { return new Intl.NumberFormat('en-US').format(n||0); }
function _trunc(s,n) { return s&&s.length>n ? s.slice(0,n)+'…' : (s||''); }
function _esc(s) { return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endsection