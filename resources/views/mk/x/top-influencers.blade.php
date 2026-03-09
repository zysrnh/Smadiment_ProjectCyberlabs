@extends('mk.layouts.app')

@section('title', 'Top Influencers – X (Twitter)')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   DESIGN TOKENS — identical to Most Active Users
═══════════════════════════════════════════════════════════ */
:root {
  --space-1:4px;--space-2:8px;--space-3:12px;
  --space-4:16px;--space-5:20px;--space-6:24px;
  --space-7:28px;--space-8:32px;--space-10:40px;

  --radius-sm:8px;--radius-md:12px;
  --radius-lg:16px;--radius-xl:20px;--radius-full:9999px;

  --color-brand:       #16a085;
  --color-brand-dark:  #0d6b5a;
  --color-brand-light: #e8f8f4;
  --color-brand-mid:   #b2dfdb;

  --color-x:           #0f1419;
  --color-x-blue:      #1d9bf0;
  --color-x-green:     #00ba7c;
  --color-x-pink:      #f91880;

  --color-pos:         #16a34a;
  --color-pos-bg:      #dcfce7;
  --color-neg:         #dc2626;
  --color-neg-bg:      #fee2e2;
  --color-neu:         #4b5563;
  --color-neu-bg:      #f3f4f6;

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

  --shadow-sm:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:0 4px 12px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.05);
  --shadow-lg:0 12px 40px rgba(0,0,0,.12),0 4px 16px rgba(0,0,0,.07);
  --shadow-xl:0 24px 64px rgba(0,0,0,.16),0 8px 24px rgba(0,0,0,.09);

  --font-sans:'DM Sans',-apple-system,BlinkMacSystemFont,sans-serif;
  --font-mono:'DM Mono',monospace;

  --color-brand2:       #2e7d6e;
  --color-brand2-dark:  #0f4c35;
  --color-brand2-light: #e8f5f0;
  --bg-page:#f7f9fc;

  /* Legacy compat */
  --primary-green:      #038047;
  --primary-green-dark: #026738;
  --border-color:       #e2e8f0;
  --card-shadow:        0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
  --card-shadow-hover:  0 4px 12px rgba(0,0,0,.1);
  --accent-blue:        #1d9bf0;
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font-sans);background:var(--bg-page);color:var(--text-primary);}

/* ═══════════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════════ */
.page-header {
  display:flex;align-items:center;justify-content:space-between;
  gap:var(--space-6);margin-bottom:var(--space-8);
  padding-bottom:var(--space-6);border-bottom:1px solid #edf0f4;
}
.page-header-left h1{font-size:22px;font-weight:800;color:var(--text-primary);letter-spacing:-.4px;line-height:1.2;margin:0 0 4px;}
.page-header-left p{font-size:13px;color:var(--text-secondary);margin:0;line-height:1.5;}
.last-updated{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text-muted);white-space:nowrap;flex-shrink:0;background:var(--bg-muted);border:1px solid #edf0f4;border-radius:20px;padding:6px 14px;}
.last-updated svg{width:13px;height:13px;flex-shrink:0;color:var(--color-brand2);}
.last-updated strong{color:var(--text-secondary);font-weight:600;}

/* ═══════════════════════════════════════════════════════════
   FILTER CARD
═══════════════════════════════════════════════════════════ */
.filter-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);padding:16px 20px;margin-bottom:var(--space-8);}
.filter-content{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
.filter-group{display:flex;flex-direction:column;gap:6px;}
.filter-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-label);line-height:1;}
.date-picker-trigger{display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg-muted);border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:14px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:all .18s;min-width:300px;}
.date-picker-trigger:hover{border-color:var(--color-brand);background:var(--bg-card);box-shadow:0 0 0 3px rgba(22,160,133,.08);}
.date-picker-trigger svg{width:16px;height:16px;color:var(--text-secondary);flex-shrink:0;}
.date-picker-trigger span{flex:1;text-align:left;}
.apply-btn{display:flex;align-items:center;gap:8px;padding:10px 24px;background:linear-gradient(135deg,var(--color-brand) 0%,var(--color-brand-dark) 100%);color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:14px;font-weight:600;cursor:pointer;transition:all .18s;box-shadow:0 4px 12px rgba(22,160,133,.2);white-space:nowrap;}
.apply-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(22,160,133,.3);}
.apply-btn svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;}

/* ═══════════════════════════════════════════════════════════
   TAB NAV — styled like summary strip, not old pill tabs
═══════════════════════════════════════════════════════════ */
.inf-tab-section{margin-bottom:var(--space-8);}
.inf-tabs{display:flex;gap:0;background:var(--bg-subtle);border-radius:var(--radius-md);padding:4px;width:fit-content;border:1px solid #e2e8f0;}
.inf-tab-btn{padding:9px 22px;border-radius:var(--radius-sm);border:none;background:transparent;font-family:var(--font-sans);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all .2s;white-space:nowrap;}
.inf-tab-btn.active{background:var(--bg-card);color:var(--color-brand);box-shadow:0 1px 6px rgba(0,0,0,.08);}
.inf-tab-btn:hover:not(.active){color:var(--text-primary);}

/* ═══════════════════════════════════════════════════════════
   SUMMARY STAT CARDS — exact match MAU
═══════════════════════════════════════════════════════════ */
.inf-summary-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:var(--space-8);}
.inf-stat-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);padding:16px 18px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;gap:4px;}
.inf-stat-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);}
.inf-stat-value{font-size:26px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;line-height:1.1;}
.inf-stat-sub{font-size:11.5px;color:var(--text-secondary);font-weight:500;}

/* ═══════════════════════════════════════════════════════════
   TOP USERS GRID — same layout as MAU tug-grid
═══════════════════════════════════════════════════════════ */
.tug-section{margin-bottom:var(--space-8);}
.tug-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;}
.tug-title{display:flex;align-items:center;gap:8px;font-size:15px;font-weight:800;color:var(--text-primary);letter-spacing:-.3px;}
.tug-title svg{width:17px;height:17px;color:var(--color-brand2);flex-shrink:0;}
.tug-period-badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;background:var(--bg-subtle);color:var(--text-label);border:1px solid #e2e8f0;}
.tug-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:stretch;}
.tug-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s;}
.tug-card:hover{box-shadow:var(--shadow-md);}
.tug-card-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px 16px;border-bottom:1px solid #edf0f4;flex-shrink:0;min-height:56px;}
.tug-card-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--text-label);}
.tug-card-sub{font-size:11px;font-weight:500;color:var(--text-muted);background:var(--bg-subtle);padding:3px 9px;border-radius:10px;}
.tug-card--donut{padding-bottom:0;}
.tug-donut-wrap{flex:1;width:100%;min-height:400px;position:relative;}
.tug-donut-wrap>div{width:100%!important;height:100%!important;min-height:400px;display:block;position:absolute;inset:0;}
.tug-donut-skel{flex:1;min-height:400px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;padding:20px;}
.tug-donut-skel-circle{width:180px;height:180px;border-radius:50%;background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:tugShim 1.4s ease-in-out infinite;position:relative;}
.tug-donut-skel-circle::after{content:'';position:absolute;inset:36px;border-radius:50%;background:#fff;}
.tug-donut-skel-legend{display:flex;flex-direction:column;gap:8px;width:80%;}
.tug-donut-skel-leg-item{display:flex;align-items:center;gap:8px;}
.tug-donut-skel-dot{width:10px;height:10px;border-radius:50%;background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:tugShim 1.4s ease-in-out infinite;flex-shrink:0;}
.tug-donut-skel-bar{flex:1;height:9px;border-radius:4px;background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:tugShim 1.4s ease-in-out infinite;}
.tug-card--list{padding-bottom:0;}
.tug-list{flex:1;padding:0;margin:0;display:flex;flex-direction:column;justify-content:stretch;}
.tug-row{display:flex;align-items:center;gap:13px;padding:0 22px;min-height:72px;cursor:pointer;transition:background .13s;border-bottom:1px solid #edf0f4;flex:1;}
.tug-row:last-child{border-bottom:none;}
.tug-row:hover{background:var(--bg-muted);}
.tug-row--first{background:#f4fbf8;}
.tug-row--first:hover{background:#eaf6f1;}
.tug-rank-badge{width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0;color:#fff;background:#cbd5e1;line-height:1;}
.tug-rank-badge--1{background:linear-gradient(135deg,#d4a529,#b59940);}
.tug-rank-badge--2{background:linear-gradient(135deg,#a0a8b8,#8a94a6);}
.tug-rank-badge--3{background:linear-gradient(135deg,#b8906a,#a07850);}
.tug-color-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.tug-avatar{width:38px;height:38px;border-radius:50%;object-fit:cover;display:block;border:2px solid #e8edf2;background:var(--bg-subtle);flex-shrink:0;}
.tug-avatar-fallback{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--color-brand2);background:var(--color-brand2-light);border:2px solid #c9e8de;flex-shrink:0;}
.tug-user-info{flex:1;min-width:0;}
.tug-user-name{font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.35;}
.tug-row--first .tug-user-name{color:var(--color-brand2-dark);}
.tug-user-handle{font-size:11.5px;font-weight:400;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3;margin-top:2px;}
.tug-user-right{flex-shrink:0;text-align:right;min-width:64px;}
.tug-user-eng{font-size:15px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;display:block;line-height:1.25;}
.tug-row--first .tug-user-eng{color:var(--color-brand2-dark);}
.tug-user-bar{display:block;width:60px;height:3px;background:var(--bg-subtle);border-radius:3px;margin-top:7px;margin-left:auto;overflow:hidden;}
.tug-user-bar-fill{height:100%;border-radius:3px;transition:width .8s cubic-bezier(.4,0,.2,1);}
.tug-skel{background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:tugShim 1.4s ease-in-out infinite;border-radius:4px;}
@keyframes tugShim{0%{background-position:200% 0}100%{background-position:-200% 0}}
.tug-skel--rank{width:24px;height:24px;border-radius:50%;flex-shrink:0;}
.tug-skel--avatar{width:38px;height:38px;border-radius:50%;flex-shrink:0;}
.tug-skel-info{flex:1;display:flex;flex-direction:column;gap:6px;}
.tug-skel--name{width:60%;height:11px;}
.tug-skel--handle{width:38%;height:9px;}
.tug-skel--score{width:48px;height:13px;border-radius:4px;flex-shrink:0;}
.tug-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;padding:40px 20px;gap:8px;color:var(--text-muted);}
.tug-empty svg{width:32px;height:32px;opacity:.25;}
.tug-empty span{font-size:12.5px;font-weight:500;}

/* ═══════════════════════════════════════════════════════════
   TABLE SECTION — identical to MAU
═══════════════════════════════════════════════════════════ */
.table-section{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:var(--space-8);}
.table-header{display:flex;align-items:center;justify-content:space-between;gap:var(--space-4);padding:20px 24px;border-bottom:1px solid #edf0f4;}
.table-title h3{font-size:15px;font-weight:800;color:var(--text-primary);letter-spacing:-.3px;margin:0 0 3px;line-height:1.2;}
.table-subtitle{font-size:12px;color:var(--text-muted);margin:0;line-height:1.4;}
.table-actions{display:flex;align-items:center;gap:10px;flex-shrink:0;}
.table-search{position:relative;}
.table-search input{padding:9px 14px 9px 36px;border:1px solid #e2e8f0;border-radius:var(--radius-full);font-family:var(--font-sans);font-size:13.5px;color:var(--text-primary);background:var(--bg-muted);outline:none;transition:all .18s;width:240px;}
.table-search input:focus{border-color:var(--color-brand);background:var(--bg-card);box-shadow:0 0 0 3px rgba(22,160,133,.08);}
.table-search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;}
.data-table{width:100%;border-collapse:collapse;font-size:13px;}
.data-table thead tr{background:var(--bg-muted);border-bottom:1px solid #edf0f4;}
.data-table th{padding:11px 16px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-label);text-align:left;white-space:nowrap;cursor:pointer;user-select:none;}
.data-table th:hover{color:var(--color-brand);}
.data-table th.sorted{color:var(--color-brand);}
.data-table td{padding:13px 16px;border-bottom:1px solid #edf0f4;vertical-align:middle;color:var(--text-primary);line-height:1;}
.data-table tbody tr{cursor:pointer;transition:background .12s;}
.data-table tbody tr:hover{background:linear-gradient(90deg,#f0fdf8 0%,#f7fdfb 100%);}
.data-table tbody tr:last-child td{border-bottom:none;}
.user-avatar-img,.user-avatar-fallback{width:36px;height:36px;border-radius:50%;display:block;object-fit:cover;border:2px solid #dde3ec;vertical-align:middle;flex-shrink:0;}
.user-avatar-fallback{display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--color-brand2);background:var(--color-brand2-light);border-color:#b8ddd4;}

.inf-rank{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0;}
.inf-rank-1{background:#fef3c7;color:#d97706;}
.inf-rank-2{background:#f1f5f9;color:#64748b;}
.inf-rank-3{background:#fef2f2;color:#dc2626;}
.inf-rank-n{background:#f1f5f9;color:var(--text-secondary);}

.username-link{color:#ea580c;text-decoration:none;font-weight:600;transition:all .15s;}
.username-link:hover{color:var(--color-brand);text-decoration:underline;}
.account-name-link{color:var(--text-primary);text-decoration:none;font-weight:700;}
.account-name-link:hover{color:var(--color-brand);text-decoration:underline;}

.inf-eng-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:12.5px;font-weight:700;}
.inf-eng-badge.mentions{background:rgba(22,160,133,.1);color:var(--color-brand);}
.inf-eng-badge.retweets{background:rgba(29,155,240,.1);color:#1d9bf0;}

.activity-stat{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--text-primary);}
.activity-stat svg{width:14px;height:14px;color:var(--color-brand);}

.view-profile-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#000;color:#fff;border:none;border-radius:8px;font-family:var(--font-sans);font-size:11px;font-weight:600;cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;}
.view-profile-btn:hover{background:#1d1d1d;transform:translateY(-1px);}
.view-profile-btn svg{width:11px;height:11px;fill:white;}

/* Pagination */
.pagination{padding:14px 24px;border-top:1px solid #edf0f4;background:var(--bg-muted);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
.pagination-info{font-size:12px;color:var(--text-muted);font-weight:500;}
.page-btn{min-width:32px;height:32px;padding:0 8px;border-radius:8px;border:1px solid #e2e8f0;background:var(--bg-card);color:var(--text-secondary);font-size:12px;font-weight:600;font-family:inherit;cursor:pointer;transition:all .13s;display:inline-flex;align-items:center;justify-content:center;}
.page-btn:hover:not(:disabled){background:var(--color-brand2-light);border-color:#a8d5c8;color:var(--color-brand2-dark);}
.page-btn.active{background:var(--color-brand2);border-color:var(--color-brand2);color:#fff;}
.page-btn:disabled{opacity:.4;cursor:not-allowed;}

/* Loading */
.loading-skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:skimmer 1.5s ease-in-out infinite;border-radius:8px;}
@keyframes skimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

.inf-empty{text-align:center;padding:60px 20px;color:var(--text-muted);}
.inf-empty svg{width:40px;height:40px;opacity:.2;margin:0 auto 12px;display:block;}
.inf-empty p{font-size:14px;font-weight:600;}

/* Actions dropdown */
.actions-dropdown{position:relative;}
.actions-dropdown-btn{display:flex;align-items:center;gap:8px;padding:8px 14px;background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-full);font-family:var(--font-sans);font-size:13px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:all .15s;}
.actions-dropdown-btn:hover{background:var(--bg-muted);border-color:var(--color-brand);}
.actions-dropdown-btn svg{width:16px;height:16px;color:var(--text-secondary);}
.actions-dropdown-menu{position:absolute;top:calc(100% + 8px);right:0;background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);min-width:200px;padding:8px;z-index:1000;display:none;}
.actions-dropdown-menu.show{display:block;animation:dropFadeIn .18s ease-out;}
@keyframes dropFadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.actions-dropdown-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:13px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:all .13s;text-decoration:none;}
.actions-dropdown-item:hover{background:var(--bg-muted);color:var(--color-brand);}
.actions-dropdown-item svg{width:16px;height:16px;color:var(--text-secondary);}
.actions-dropdown-item:hover svg{color:var(--color-brand);}
.actions-dropdown-divider{height:1px;background:#edf0f4;margin:6px 0;}

/* ═══════════════════════════════════════════════════════════
   USER DETAIL MODAL (UDM) — same as MAU
═══════════════════════════════════════════════════════════ */
.user-detail-modal{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:16px;}
.user-detail-modal.show{display:flex;}
.modal-overlay{position:absolute;inset:0;background:rgba(15,20,25,.6);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);animation:overlayFadeIn .22s ease forwards;}
@keyframes overlayFadeIn{from{opacity:0}to{opacity:1}}
.udm-content{position:relative;z-index:1;width:100%;max-width:760px;max-height:92vh;background:var(--bg-card);border-radius:20px;display:flex;flex-direction:column;overflow:hidden;animation:udmEntrance .3s cubic-bezier(.22,1,.36,1) forwards;box-shadow:0 32px 80px rgba(0,0,0,.2),0 8px 32px rgba(0,0,0,.1),0 0 0 1px rgba(255,255,255,.08);font-family:var(--font-sans);}
@keyframes udmEntrance{from{transform:scale(.96) translateY(16px);opacity:0;}to{transform:scale(1) translateY(0);opacity:1;}}
.udm-topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:var(--border);flex-shrink:0;background:var(--bg-card);}
.udm-topbar-title{font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;display:flex;align-items:center;gap:8px;}
.udm-topbar-title::before{content:'';display:inline-block;width:3px;height:16px;background:linear-gradient(180deg,var(--color-brand),var(--color-brand-dark));border-radius:2px;}
.udm-close{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;cursor:pointer;color:var(--text-secondary);transition:background .15s,color .15s;}
.udm-close:hover{background:var(--bg-subtle);color:var(--text-primary);}
.udm-close svg{width:16px;height:16px;stroke:currentColor;stroke-width:2.5;}
.udm-body{flex:1;overflow-y:auto;scrollbar-width:thin;scrollbar-color:#d1d9e0 transparent;}
.udm-body::-webkit-scrollbar{width:5px;}
.udm-body::-webkit-scrollbar-thumb{background:#d1d9e0;border-radius:4px;}

/* Banner */
.udm-banner{height:140px;flex-shrink:0;position:relative;overflow:hidden;background:linear-gradient(135deg,#0d1b2a 0%,#162534 30%,#0d3d2e 65%,#0a4a38 100%);}
.udm-banner::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 90% at 10% 50%,rgba(22,160,133,.3) 0%,transparent 60%),radial-gradient(ellipse 50% 70% at 85% 30%,rgba(29,155,240,.15) 0%,transparent 55%),radial-gradient(ellipse 40% 60% at 60% 80%,rgba(0,186,124,.12) 0%,transparent 50%);}
.udm-banner::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(-55deg,transparent,transparent 28px,rgba(255,255,255,.018) 28px,rgba(255,255,255,.018) 29px);}
.udm-banner-dots{position:absolute;inset:0;opacity:.18;background-image:radial-gradient(circle,rgba(255,255,255,.6) 1px,transparent 1px);background-size:24px 24px;}

/* Profile section */
.udm-profile-section{padding:0 var(--space-6) var(--space-6);border-bottom:var(--border);}
.udm-profile-row{display:flex;align-items:flex-end;justify-content:space-between;margin-top:-40px;position:relative;z-index:2;padding-bottom:var(--space-5);gap:var(--space-4);}
.udm-avatar-wrap{flex-shrink:0;}
.udm-avatar{width:80px;height:80px;border-radius:50%;object-fit:cover;display:block;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.18);background:var(--color-brand-light);}
.udm-avatar-fb{width:80px;height:80px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;letter-spacing:-.5px;color:var(--color-brand);background:var(--color-brand-light);border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.18);}
.udm-profile-meta{flex:1;min-width:0;padding-top:48px;}
.udm-name{font-size:20px;font-weight:700;color:var(--text-primary);line-height:1.2;letter-spacing:-.4px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:3px;}
.udm-verified-badge{display:inline-flex;align-items:center;justify-content:center;width:19px;height:19px;flex-shrink:0;}
.udm-handle{font-size:13.5px;color:var(--text-secondary);display:flex;align-items:center;gap:4px;}
.udm-handle a{color:var(--text-secondary);text-decoration:none;}
.udm-handle a:hover{color:var(--color-x-blue);text-decoration:underline;}
.udm-view-btn{flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:var(--radius-full);background:var(--color-x);color:#fff;font-size:12.5px;font-weight:700;letter-spacing:.1px;text-decoration:none;transition:background .15s,transform .12s,box-shadow .15s;line-height:1;align-self:flex-end;box-shadow:0 2px 8px rgba(0,0,0,.18);white-space:nowrap;}
.udm-view-btn:hover{background:#1a1e24;transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,0,0,.22);}
.udm-view-btn svg{flex-shrink:0;}
.udm-follower-chip{display:inline-flex;align-items:center;gap:5px;margin-top:var(--space-3);background:var(--bg-subtle);border:var(--border);border-radius:var(--radius-full);padding:5px 12px;font-size:12px;font-weight:500;color:var(--text-secondary);}
.udm-follower-chip strong{color:var(--text-primary);font-weight:700;}

/* Engagement strip */
.udm-eng-strip{display:grid;grid-template-columns:repeat(3,1fr);margin:var(--space-6) var(--space-6) 0;border-radius:var(--radius-lg);overflow:hidden;border:var(--border);background:var(--bg-muted);box-shadow:var(--shadow-sm);}
.udm-eng-item{padding:var(--space-5) var(--space-4);text-align:center;border-right:var(--border);position:relative;transition:background .15s;}
.udm-eng-item:last-child{border-right:none;}
.udm-eng-item:hover{background:var(--bg-subtle);}
.udm-eng-icon{width:32px;height:32px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-2);}
.udm-eng-item--mentions .udm-eng-icon{background:#e8f8f4;}
.udm-eng-item--followers .udm-eng-icon{background:#e8f4fd;}
.udm-eng-item--retweets  .udm-eng-icon{background:#e8f8f1;}
.udm-eng-icon svg{width:15px;height:15px;stroke-width:2;}
.udm-eng-item--mentions  .udm-eng-icon svg{stroke:var(--color-brand);}
.udm-eng-item--followers .udm-eng-icon svg{stroke:var(--color-x-blue);}
.udm-eng-item--retweets  .udm-eng-icon svg{stroke:var(--color-x-green);}
.udm-eng-val{display:block;font-size:22px;font-weight:700;letter-spacing:-.6px;line-height:1.1;}
.udm-eng-item--mentions  .udm-eng-val{color:var(--color-brand);}
.udm-eng-item--followers .udm-eng-val{color:var(--color-x-blue);}
.udm-eng-item--retweets  .udm-eng-val{color:var(--color-x-green);}
.udm-eng-lbl{display:block;font-size:10.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-top:4px;}
.udm-total-row{display:flex;align-items:center;justify-content:space-between;margin:var(--space-3) var(--space-6) var(--space-6);padding:var(--space-4) var(--space-5);background:linear-gradient(90deg,#edf7f4,#f0faf7);border-radius:var(--radius-md);border:1px solid var(--color-brand-mid);}
.udm-total-row-label{font-size:12.5px;font-weight:600;color:var(--color-brand-dark);display:flex;align-items:center;gap:6px;}
.udm-total-row-label svg{width:14px;height:14px;stroke:var(--color-brand);stroke-width:2;}
.udm-total-row-val{font-size:20px;font-weight:800;color:var(--color-brand-dark);letter-spacing:-.6px;}

/* Mentions section */
.udm-mentions-wrap{padding:0;}
.udm-mentions-head{display:flex;align-items:center;justify-content:space-between;padding:var(--space-5) var(--space-6);border-bottom:var(--border);position:sticky;top:0;z-index:5;background:rgba(255,255,255,.97);backdrop-filter:blur(10px);}
.udm-mentions-title{font-size:14.5px;font-weight:700;color:var(--text-primary);letter-spacing:-.2px;display:flex;align-items:center;gap:8px;}
.udm-mentions-title-icon{width:28px;height:28px;border-radius:8px;background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;}
.udm-mentions-title-icon svg{width:13px;height:13px;stroke:var(--color-brand);stroke-width:2.2;}
.udm-mentions-count{font-size:11.5px;font-weight:600;color:var(--text-secondary);background:var(--bg-subtle);padding:4px 11px;border-radius:var(--radius-full);border:var(--border);}
.udm-mentions-list{padding:var(--space-4) var(--space-6);display:flex;flex-direction:column;gap:var(--space-3);}

/* Tweet card in UDM */
.udm-card{background:var(--bg-card);border:var(--border);border-radius:var(--radius-lg);padding:var(--space-4);cursor:pointer;transition:box-shadow .18s,border-color .18s,transform .15s,background .15s;position:relative;}
.udm-card:hover{box-shadow:var(--shadow-md);border-color:#c7d4e0;transform:translateY(-1px);background:var(--bg-hover);}
.udm-card-header{display:flex;align-items:flex-start;gap:var(--space-3);margin-bottom:var(--space-3);}
.udm-card-mini-avatar{width:38px;height:38px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--bg-subtle);background:var(--bg-subtle);}
.udm-card-mini-avatar-fb{width:38px;height:38px;border-radius:50%;background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--color-brand);flex-shrink:0;border:2px solid var(--color-brand-mid);}
.udm-card-meta{flex:1;min-width:0;}
.udm-card-row1{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;}
.udm-card-name-block{display:flex;flex-direction:column;gap:1px;}
.udm-card-author{font-size:13.5px;font-weight:700;color:var(--text-primary);line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.udm-card-handle{font-size:12px;color:var(--text-muted);line-height:1.3;}
.udm-card-time{font-size:11.5px;color:var(--text-muted);white-space:nowrap;}
.udm-card-badges{display:flex;align-items:center;gap:5px;margin-top:var(--space-2);flex-wrap:wrap;}
.udm-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:var(--radius-full);font-size:11px;font-weight:700;letter-spacing:.2px;flex-shrink:0;}
.udm-pill--pos{background:var(--color-pos-bg);color:var(--color-pos);}
.udm-pill--neg{background:var(--color-neg-bg);color:var(--color-neg);}
.udm-pill--neu{background:var(--color-neu-bg);color:var(--color-neu);}
.udm-pill::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;}
.udm-type-badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:var(--radius-full);font-size:11px;font-weight:600;letter-spacing:.2px;flex-shrink:0;}
.udm-type-badge--mention{background:var(--color-brand-light);color:var(--color-brand);}
.udm-type-badge--reply{background:#e8f4fd;color:var(--color-x-blue);}
.udm-type-badge--retweet{background:#e8f8f1;color:var(--color-x-green);}
.udm-type-badge--tweet{background:var(--bg-subtle);color:var(--text-secondary);}
.udm-card-text{font-size:13.5px;color:var(--text-primary);line-height:1.65;word-break:break-word;padding:var(--space-3) var(--space-4);background:var(--bg-muted);border-radius:var(--radius-md);border-left:3px solid var(--color-brand);margin-bottom:var(--space-3);}
.udm-text-link{text-decoration:none;font-weight:500;border-radius:2px;transition:color .12s;}
.udm-text-link--mention{color:var(--color-x-blue);}
.udm-text-link--mention:hover{text-decoration:underline;opacity:.85;}
.udm-text-link--hashtag{color:var(--color-x-blue);}
.udm-text-link--hashtag:hover{text-decoration:underline;opacity:.85;}
.udm-text-link--url{color:var(--color-brand);word-break:break-all;}
.udm-text-link--url:hover{text-decoration:underline;opacity:.8;}
.udm-card-foot{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;padding-top:var(--space-2);border-top:1px solid #f0f4f8;}
.udm-card-actions{display:flex;align-items:center;gap:var(--space-5);}
.udm-card-action{display:flex;align-items:center;gap:5px;font-size:12.5px;color:var(--text-muted);font-weight:500;transition:color .13s;font-variant-numeric:tabular-nums;}
.udm-card-action svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:1.8;flex-shrink:0;}
.udm-card-action:hover{color:var(--color-x-blue);}
.udm-card-action.likes:hover{color:var(--color-x-pink);}
.udm-card-action.rts:hover{color:var(--color-x-green);}
.udm-card-view-link{font-size:12px;font-weight:700;color:var(--color-brand);text-decoration:none;display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:var(--radius-full);background:var(--color-brand-light);border:1px solid var(--color-brand-mid);transition:all .15s;}
.udm-card-view-link:hover{background:var(--color-brand-mid);color:var(--color-brand-dark);}
.udm-card-view-link svg{width:11px;height:11px;}

/* UDM Pagination */
.udm-pagination{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:var(--space-4) var(--space-6);border-top:var(--border);background:var(--bg-muted);flex-wrap:wrap;}
.udm-pg-info{font-size:11.5px;color:var(--text-muted);font-weight:500;flex-shrink:0;}
.udm-pg-btns{display:flex;align-items:center;gap:4px;flex-wrap:wrap;}
.udm-pg-btn{min-width:30px;height:30px;padding:0 7px;border-radius:var(--radius-sm);border:var(--border);background:var(--bg-card);color:var(--text-secondary);font-size:12px;font-weight:600;font-family:inherit;cursor:pointer;transition:all .12s;display:inline-flex;align-items:center;justify-content:center;gap:3px;line-height:1;}
.udm-pg-btn:hover:not(:disabled){background:var(--bg-subtle);border-color:var(--color-brand-mid);color:var(--color-brand-dark);}
.udm-pg-btn.active{background:var(--color-x);border-color:var(--color-x);color:#fff;}
.udm-pg-btn:disabled{opacity:.35;cursor:not-allowed;}
.udm-pg-btn--load{background:var(--color-brand-light);border-color:var(--color-brand-mid);color:var(--color-brand-dark);padding:0 12px;font-weight:700;}
.udm-pg-btn--load:hover:not(:disabled){background:var(--color-brand-mid);}
.udm-pg-loading-dot{display:inline-block;width:8px;height:8px;border-radius:50%;border:2px solid var(--color-brand-dark);border-top-color:transparent;animation:udmSpin .6s linear infinite;flex-shrink:0;}

/* Loading/empty states */
.udm-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:14px;color:var(--text-muted);}
.udm-spinner{width:28px;height:28px;border:3px solid var(--bg-subtle);border-top-color:var(--color-brand);border-radius:50%;animation:udmSpin .7s linear infinite;}
@keyframes udmSpin{to{transform:rotate(360deg)}}
.udm-loading-txt{font-size:13.5px;font-weight:500;color:var(--text-muted);}
.udm-empty-mentions{padding:var(--space-10) var(--space-6);display:flex;flex-direction:column;align-items:center;gap:var(--space-3);}
.udm-empty-mentions svg{width:40px;height:40px;opacity:.2;}
.udm-empty-mentions p{font-size:13.5px;font-weight:500;color:var(--text-muted);margin:0;}

/* ═══════════════════════════════════════════════════════════
   TWEET DETAIL MODAL (TDM) — same as MAU
═══════════════════════════════════════════════════════════ */
.tdm-modal{display:none;position:fixed;inset:0;z-index:10999;align-items:center;justify-content:center;padding:16px;}
.tdm-modal.show{display:flex;}
.tdm-overlay{position:absolute;inset:0;background:rgba(15,20,25,.5);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);animation:tdmFadeIn .18s ease forwards;}
@keyframes tdmFadeIn{from{opacity:0}to{opacity:1}}
.tdm-content{position:relative;z-index:1;width:100%;max-width:600px;max-height:88vh;background:var(--bg-card);border-radius:20px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 28px 72px rgba(0,0,0,.22),0 0 0 1px rgba(0,0,0,.06);animation:tdmEntrance .24s cubic-bezier(.22,1,.36,1) forwards;font-family:var(--font-sans);}
@keyframes tdmEntrance{from{transform:scale(.94) translateY(12px);opacity:0;}to{transform:scale(1) translateY(0);opacity:1;}}
.tdm-topbar{display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:var(--border);flex-shrink:0;background:var(--bg-card);}
.tdm-back-btn,.tdm-close-btn{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;cursor:pointer;color:var(--text-secondary);transition:background .13s,color .13s;flex-shrink:0;}
.tdm-back-btn:hover,.tdm-close-btn:hover{background:var(--bg-subtle);color:var(--text-primary);}
.tdm-back-btn svg,.tdm-close-btn svg{width:16px;height:16px;stroke:currentColor;stroke-width:2.2;fill:none;}
.tdm-topbar-title{font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;flex:1;}
.tdm-body{flex:1;overflow-y:auto;scrollbar-width:thin;scrollbar-color:#d1d9e0 transparent;}
.tdm-body::-webkit-scrollbar{width:5px;}
.tdm-body::-webkit-scrollbar-thumb{background:#d1d9e0;border-radius:4px;}
.tdm-author-section{padding:var(--space-6) var(--space-6) var(--space-5);border-bottom:var(--border);display:flex;align-items:center;gap:var(--space-4);}
.tdm-author-avatar{width:52px;height:52px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2.5px solid var(--bg-subtle);background:var(--color-brand-light);}
.tdm-author-avatar-fb{width:52px;height:52px;border-radius:50%;background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:var(--color-brand);flex-shrink:0;border:2.5px solid var(--color-brand-mid);}
.tdm-author-info{flex:1;min-width:0;}
.tdm-author-name{font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.25;display:flex;align-items:center;gap:5px;}
.tdm-author-handle{font-size:13px;color:var(--text-muted);margin-top:2px;}
.tdm-author-handle a{color:var(--text-muted);text-decoration:none;}
.tdm-author-handle a:hover{color:var(--color-x-blue);text-decoration:underline;}
.tdm-view-x-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius-full);background:var(--color-x);color:#fff;font-size:12.5px;font-weight:700;text-decoration:none;flex-shrink:0;transition:background .13s,transform .1s,box-shadow .15s;line-height:1;box-shadow:0 2px 8px rgba(0,0,0,.15);}
.tdm-view-x-btn:hover{background:#1a1e24;transform:translateY(-1px);box-shadow:0 4px 14px rgba(0,0,0,.2);}
.tdm-tweet-body{padding:var(--space-6);border-bottom:var(--border);}
.tdm-tweet-text{font-size:16.5px;line-height:1.7;color:var(--text-primary);word-break:break-word;margin:0 0 var(--space-5);font-weight:400;}
.tdm-tweet-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.tdm-timestamp{font-size:12px;color:var(--text-muted);margin-left:auto;font-variant-numeric:tabular-nums;font-family:var(--font-mono);}
.tdm-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:var(--radius-full);font-size:11.5px;font-weight:700;letter-spacing:.2px;}
.tdm-badge::before{content:'';width:5px;height:5px;border-radius:50%;background:currentColor;}
.tdm-badge--pos{background:var(--color-pos-bg);color:var(--color-pos);}
.tdm-badge--neg{background:var(--color-neg-bg);color:var(--color-neg);}
.tdm-badge--neu{background:var(--color-neu-bg);color:var(--color-neu);}
.tdm-badge--mention{background:var(--color-brand-light);color:var(--color-brand);}
.tdm-badge--reply{background:#e8f4fd;color:var(--color-x-blue);}
.tdm-badge--retweet{background:#e8f8f1;color:var(--color-x-green);}
.tdm-badge--tweet{background:var(--bg-subtle);color:var(--text-secondary);}
.tdm-stats-strip{display:grid;grid-template-columns:repeat(3,1fr);margin:var(--space-5) var(--space-6);border-radius:var(--radius-lg);overflow:hidden;border:var(--border);background:var(--bg-muted);}
.tdm-stat-item{padding:var(--space-5) var(--space-4);text-align:center;border-right:var(--border);transition:background .15s;}
.tdm-stat-item:last-child{border-right:none;}
.tdm-stat-item:hover{background:var(--bg-subtle);}
.tdm-stat-icon{width:32px;height:32px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-2);}
.tdm-stat-item--likes   .tdm-stat-icon{background:#fce7f3;}
.tdm-stat-item--rts     .tdm-stat-icon{background:#e8f8f1;}
.tdm-stat-item--replies .tdm-stat-icon{background:#e8f4fd;}
.tdm-stat-icon svg{width:15px;height:15px;fill:none;stroke-width:2;}
.tdm-stat-item--likes   .tdm-stat-icon svg{stroke:var(--color-x-pink);}
.tdm-stat-item--rts     .tdm-stat-icon svg{stroke:var(--color-x-green);}
.tdm-stat-item--replies .tdm-stat-icon svg{stroke:var(--color-x-blue);}
.tdm-stat-val{display:block;font-size:22px;font-weight:700;letter-spacing:-.6px;line-height:1.1;}
.tdm-stat-item--likes   .tdm-stat-val{color:var(--color-x-pink);}
.tdm-stat-item--rts     .tdm-stat-val{color:var(--color-x-green);}
.tdm-stat-item--replies .tdm-stat-val{color:var(--color-x-blue);}
.tdm-stat-lbl{display:block;font-size:10.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-top:3px;}
.tdm-footer{padding:var(--space-4) var(--space-6);display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-shrink:0;border-top:var(--border);background:var(--bg-muted);}
.tdm-footer-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius-full);font-size:12.5px;font-weight:700;text-decoration:none;transition:all .15s;cursor:pointer;}
.tdm-footer-btn--outline{border:var(--border-strong);background:var(--bg-card);color:var(--text-secondary);}
.tdm-footer-btn--outline:hover{background:var(--bg-subtle);color:var(--text-primary);border-color:#b0bec5;}
.tdm-footer-btn--primary{background:var(--color-brand);color:#fff;border:1px solid var(--color-brand-dark);box-shadow:0 2px 8px rgba(22,160,133,.25);}
.tdm-footer-btn--primary:hover{background:var(--color-brand-dark);box-shadow:0 4px 12px rgba(22,160,133,.3);transform:translateY(-1px);}
.tdm-footer-btn svg{width:13px;height:13px;}
.tdm-link{text-decoration:none;font-weight:500;border-radius:2px;transition:opacity .12s;}
.tdm-link:hover{opacity:.8;text-decoration:underline;}
.tdm-link--url{color:var(--color-brand);word-break:break-all;}
.tdm-link--mention{color:var(--color-x-blue);}
.tdm-link--hashtag{color:var(--color-x-blue);}

/* DATE PICKER MODAL */
.date-picker-modal{position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);}
.date-picker-modal.show{display:flex;}
.date-picker-overlay{position:absolute;inset:0;cursor:pointer;}
.date-picker-container{position:relative;z-index:1;background:#fff;border-radius:var(--radius-lg);box-shadow:0 25px 50px rgba(0,0,0,.3);display:flex;max-width:900px;width:90%;max-height:90vh;animation:dpUp .3s ease-out;}
@keyframes dpUp{from{opacity:0;transform:translateY(20px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
.date-picker-sidebar{width:180px;background:var(--bg-muted);border-right:1px solid #e2e8f0;padding:16px 12px;border-radius:var(--radius-lg) 0 0 var(--radius-lg);display:flex;flex-direction:column;gap:4px;flex-shrink:0;}
.date-preset{padding:10px 16px;background:transparent;border:none;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:13px;font-weight:500;color:var(--text-primary);text-align:left;cursor:pointer;transition:all .15s;}
.date-preset:hover{background:#fff;color:var(--color-brand);}
.date-preset.active{background:var(--color-brand);color:#fff;}
.date-picker-content{flex:1;padding:24px;display:flex;flex-direction:column;overflow:hidden;}
.date-picker-header{display:flex;align-items:flex-start;gap:20px;margin-bottom:20px;}
.nav-btn{width:36px;height:36px;border-radius:var(--radius-sm);background:var(--bg-muted);border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;flex-shrink:0;}
.nav-btn:hover{background:var(--color-brand);border-color:var(--color-brand);color:#fff;}
.nav-btn svg{width:20px;height:20px;}
.calendars-wrapper{display:flex;gap:24px;flex:1;}
.calendar{flex:1;display:flex;flex-direction:column;}
.calendar-month{font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px;text-align:center;}
.calendar-weekdays{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:8px;}
.weekday{text-align:center;font-size:11px;font-weight:700;color:var(--text-secondary);padding:8px 0;}
.calendar-days{display:grid;grid-template-columns:repeat(7,1fr);gap:4px;}
.calendar-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;border-radius:var(--radius-sm);cursor:pointer;transition:all .15s;color:var(--text-primary);background:transparent;border:none;padding:0;font-family:var(--font-sans);}
.calendar-day:hover:not(.disabled):not(.other-month){background:var(--bg-subtle);}
.calendar-day.other-month{color:#cbd5e1;cursor:default;}
.calendar-day.disabled{color:#e2e8f0;cursor:not-allowed;}
.calendar-day.today{border:2px solid var(--color-brand);}
.calendar-day.selected{background:var(--color-brand);color:#fff;}
.calendar-day.in-range{background:var(--color-brand-light);color:var(--color-brand);}
.date-picker-display{padding:16px 20px;background:var(--bg-muted);border-radius:var(--radius-sm);text-align:center;margin-bottom:20px;border:1px solid #e2e8f0;}
.date-picker-display span{font-size:14px;font-weight:600;color:var(--text-primary);}
.date-picker-footer{display:flex;gap:12px;justify-content:flex-end;}
.cancel-btn,.apply-date-btn{padding:10px 24px;border-radius:10px;font-family:var(--font-sans);font-size:14px;font-weight:600;cursor:pointer;transition:all .15s;border:none;}
.cancel-btn{background:var(--bg-subtle);color:var(--text-primary);}
.cancel-btn:hover{background:#e2e8f0;}
.apply-date-btn{background:linear-gradient(135deg,var(--color-brand) 0%,var(--color-brand-dark) 100%);color:#fff;box-shadow:0 4px 12px rgba(22,160,133,.2);}
.apply-date-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(22,160,133,.3);}

/* Responsive */
@media(max-width:900px){.tug-grid{grid-template-columns:1fr;gap:16px;}.tug-donut-wrap{min-height:320px;}.tug-donut-wrap>div{min-height:320px;}.page-header{flex-direction:column;align-items:flex-start;gap:8px;}}
@media(max-width:680px){.udm-content{max-width:100%;border-radius:20px 20px 0 0;align-self:flex-end;max-height:95vh;}.tdm-content{max-width:100%;border-radius:20px 20px 0 0;align-self:flex-end;max-height:95vh;}.user-detail-modal,.tdm-modal{align-items:flex-end;padding:0;}.udm-eng-strip{margin:var(--space-5) var(--space-4) 0;}.udm-total-row{margin:var(--space-3) var(--space-4) var(--space-5);}.udm-mentions-list{padding:var(--space-3) var(--space-4);}.udm-mentions-head{padding:var(--space-4);}.tdm-stats-strip{margin:var(--space-4);}.tdm-footer{padding:var(--space-3) var(--space-4);}.tdm-tweet-body{padding:var(--space-4);}.tdm-author-section{padding:var(--space-4);}
.date-picker-container{flex-direction:column;max-height:85vh;overflow-y:auto;width:96%;}.date-picker-sidebar{width:100%;flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid #e2e8f0;border-radius:var(--radius-lg) var(--radius-lg) 0 0;}.calendars-wrapper{flex-direction:column;gap:16px;}}
</style>
@endsection

@section('content')
@php
  $projectId = $projectId ?? request()->get('project_id');
  $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = $endDate   ?? request()->get('end_date',   now()->format('Y-m-d'));
  $projects  = $projects  ?? [];
@endphp

<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <h1>Top Influencers</h1>
      <p>X (Twitter) · Users with the highest engagement on your project</p>
    </div>
    <div class="last-updated">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
      <span>Last Updated: <strong>Twitter ({{ now()->diffForHumans() }})</strong></span>
    </div>
  </div>

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.top-influencers') }}">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED"  value="{{ $endDate }}">
      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="infProjSel" onchange="document.getElementById('hPid').value=this.value" style="padding:10px 14px;border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:14px;font-weight:500;color:var(--text-primary);background:var(--bg-muted);outline:none;min-width:200px;cursor:pointer;">
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
          <button type="button" class="date-picker-trigger" id="infDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="infDpDisplay">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Apply
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Date Picker Modal -->
  <div class="date-picker-modal" id="infDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="InfDp.close()"></div>
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
          <button class="nav-btn" onclick="InfDp.nav(-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper">
            <div class="calendar" id="infDpCal1"></div>
            <div class="calendar" id="infDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="InfDp.nav(1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display">
          <span id="infDpRangeText">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="InfDp.close()">Cancel</button>
          <button class="apply-date-btn" onclick="InfDp.apply()">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab Nav -->
  <div class="inf-tab-section">
    <div class="inf-tabs">
      <button class="inf-tab-btn active" id="tabBtnMentions" onclick="switchTab('rt')">By Collected Mentions</button>
      <button class="inf-tab-btn" id="tabBtnRetweets" onclick="switchTab('rt_all')">By Total Retweets</button>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="inf-summary-row">
    <div class="inf-stat-card">
      <span class="inf-stat-label">Total Influencers</span>
      <span class="inf-stat-value" id="statTotal">–</span>
      <span class="inf-stat-sub">accounts tracked</span>
    </div>
    <div class="inf-stat-card">
      <span class="inf-stat-label" id="statEngLabel">Total Engagements</span>
      <span class="inf-stat-value" id="statEngagements">–</span>
      <span class="inf-stat-sub" id="statEngSub">RT + Reply count</span>
    </div>
    <div class="inf-stat-card">
      <span class="inf-stat-label">Top Account</span>
      <span class="inf-stat-value" style="font-size:16px;" id="statTopAccount">–</span>
      <span class="inf-stat-sub" id="statTopEngagement">– engagements</span>
    </div>
    <div class="inf-stat-card">
      <span class="inf-stat-label">Avg Followers</span>
      <span class="inf-stat-value" id="statAvgFollowers">–</span>
      <span class="inf-stat-sub">per influencer</span>
    </div>
  </div>

  <!-- TOP 5 GRID — same as MAU tug-section -->
  <div class="tug-section">
    <div class="tug-header">
      <div class="tug-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
        Top Influencers by Engagement
      </div>
      <span class="tug-period-badge" id="topInfPeriod">Custom Range</span>
    </div>
    <div class="tug-grid">
      <!-- Donut Chart -->
      <div class="tug-card tug-card--donut">
        <div class="tug-card-head">
          <span class="tug-card-label">Engagement Share</span>
          <span class="tug-card-sub" id="donutSubLabel">Top 5 users</span>
        </div>
        <div class="tug-donut-skel" id="infDonutSkel">
          <div class="tug-donut-skel-circle"></div>
          <div class="tug-donut-skel-legend">
            @for($i=0;$i<5;$i++)
            <div class="tug-donut-skel-leg-item">
              <div class="tug-donut-skel-dot"></div>
              <div class="tug-donut-skel-bar" style="width:{{ 80-$i*12 }}%"></div>
            </div>
            @endfor
          </div>
        </div>
        <div class="tug-donut-wrap" id="infDonutWrap" style="display:none;">
          <div id="infDonutChart" style="width:100%;height:100%;"></div>
        </div>
      </div>
      <!-- Ranked List -->
      <div class="tug-card tug-card--list">
        <div class="tug-card-head">
          <span class="tug-card-label">Top Contributors</span>
          <span class="tug-card-sub">Ranked by interactions</span>
        </div>
        <div class="tug-list" id="infRankedList">
          @for($s=0;$s<5;$s++)
          <div class="tug-row {{ $s===0?'tug-row--first':'' }}" style="pointer-events:none;">
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

  <!-- Table Section -->
  <div class="table-section">
    <div class="table-header">
      <div class="table-title">
        <h3>Influencer Ranking</h3>
        <p class="table-subtitle">Sorted by total engagement — click user to view detailed profile</p>
      </div>
      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="infSearchInput" placeholder="Search by name or handle…" oninput="filterTable()">
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
    <div id="tableLoading" class="loading-skeleton" style="height:400px;margin:20px;border-radius:12px;"></div>
    <div id="tableWrapper" style="display:none;overflow-x:auto;"></div>
    <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    <div id="emptyState" class="inf-empty" style="display:none;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <p>No influencer data found for the selected date range.</p>
    </div>
  </div>

</div>

<!-- ═══════════════════════════════════════════════════════
     USER DETAIL MODAL (UDM)
═══════════════════════════════════════════════════════ -->
<div class="user-detail-modal" id="userDetailModal" role="dialog" aria-modal="true">
  <div class="modal-overlay" onclick="UDM.close()"></div>
  <div class="udm-content">
    <div class="udm-topbar">
      <span class="udm-topbar-title">Influencer Profile</span>
      <button class="udm-close" onclick="UDM.close()" aria-label="Close">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
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
        <svg viewBox="0 0 24 24" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
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
const InfCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
};

const TAB_CONFIG = {
  rt: {
    colHeader:  'RT + Reply Count',
    engLabel:   'Total RT + Reply',
    engSub:     'RT + Reply count',
    donutSub:   'Top 5 by RT + Reply',
    badgeClass: 'mentions',
    showReplies: true,
  },
  rt_all: {
    colHeader:  'Total Retweets',
    engLabel:   'Total Retweets',
    engSub:     'Total retweet count',
    donutSub:   'Top 5 by Retweets',
    badgeClass: 'retweets',
    showReplies: false,
  },
};

const TUG5_PALETTE = ['#16a085','#1d9bf0','#00ba7c','#8b5cf6','#f59e0b'];
const PER_PAGE = 20;

let allData      = [];
let filteredData = [];
let currentPage  = 1;
let currentSub   = 'rt';
let sortKey      = 'total';
let sortAsc      = false;
let infDonutInst = null;
let dataLoaded   = false;

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
const numFmt = n => (!n && n !== 0) ? '0' : new Intl.NumberFormat('en-US').format(n);
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'K':String(n); };
const getInitials = name => {
  if (!name||name==='Unknown') return '?';
  const p=name.trim().split(/\s+/);
  return p.length===1?p[0].substring(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase();
};

/* ─── Avatar helper: prioritize profile_image from API, fallback unavatar.io ─── */
function _avatarUrl(u, size) {
  if (!u) return '';
  // 1. Try direct profile_image field (Twitter CDN URL)
  const img = u.profile_image || u.profile_image_url || u.avatar || '';
  if (img && img.startsWith('http')) {
    // Twitter _normal = 48x48, _bigger = 73x73, _200x200 = 200x200
    if (size === 'large' || size === '200') {
      return img.replace(/_normal(?=\.)/, '_200x200')
                .replace(/_bigger(?=\.)/, '_200x200')
                .replace(/_400x400(?=\.)/, '_200x200');
    }
    if (size === 'big' || size === '73') {
      return img.replace(/_normal(?=\.)/, '_bigger')
                .replace(/_200x200(?=\.)/, '_bigger')
                .replace(/_400x400(?=\.)/, '_bigger');
    }
    // default: keep as-is (_normal or whatever)
    return img;
  }
  // 2. Fallback: unavatar.io resolves Twitter avatar by handle
  const handle = u.screen_name || u.username || '';
  if (handle) return `https://unavatar.io/twitter/${handle}`;
  return '';
}
// Keep for compat
function _hashStr(s){ let h=0; for(let i=0;i<s.length;i++){h=((h<<5)-h)+s.charCodeAt(i);h|=0;} return Math.abs(h); }
const totalEng = u => parseInt(u.total || (parseInt(u.retweets||0)+parseInt(u.replies||0)) || 0);

function _esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function _escAttr(s){ return String(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;'); }
function _escHref(s){ return String(s||'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
function _fmtDate(d){ return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
function _fmtDisp(d){ const M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; return `${M[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${d.getFullYear()}`; }

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const InfDp = (() => {
  let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
  function init(){
    const si=document.getElementById('hSD'),ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):(()=>{const d=new Date();d.setDate(d.getDate()-6);return d;})();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('infDpTrigger').addEventListener('click',open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&document.getElementById('infDpModal').classList.contains('show'))close();});
  }
  function open(){const m=document.getElementById('infDpModal');m.style.display='flex';requestAnimationFrame(()=>m.classList.add('show'));render();}
  function close(){const m=document.getElementById('infDpModal');m.classList.remove('show');setTimeout(()=>{m.style.display='none';},250);}
  function apply(){
    document.getElementById('hSD').value=fmt(ds);
    document.getElementById('hED').value=fmt(de);
    document.getElementById('infDpDisplay').textContent=_fmtDisp(ds)+' – '+_fmtDisp(de);
    close();document.getElementById('filterForm').submit();
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
  function render(){renderCal('infDpCal1',m1);renderCal('infDpCal2',m2);updDisp();}
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
  function updDisp(){const el=document.getElementById('infDpRangeText');if(el&&ds&&de)el.textContent=_fmtDisp(ds)+' – '+_fmtDisp(de);}
  function fmt(d){return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sameD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
  return {init,open,close,apply,nav};
})();

/* ══════════════════════════════════════════════════════
   TAB SWITCH
══════════════════════════════════════════════════════ */
function switchTab(sub) {
  currentSub = sub;
  document.getElementById('tabBtnMentions').classList.toggle('active', sub==='rt');
  document.getElementById('tabBtnRetweets').classList.toggle('active', sub==='rt_all');
  const cfg = TAB_CONFIG[sub];
  document.getElementById('statEngLabel').textContent = cfg.engLabel;
  document.getElementById('statEngSub').textContent   = cfg.engSub;
  document.getElementById('donutSubLabel').textContent = cfg.donutSub;
  document.getElementById('infSearchInput').value = '';
  currentPage=1; sortKey='total'; sortAsc=false;
  allData=[];filteredData=[];dataLoaded=false;
  loadData();
}

/* ══════════════════════════════════════════════════════
   DATA LOAD
══════════════════════════════════════════════════════ */
async function loadData() {
  if (dataLoaded) return;
  dataLoaded = true;
  document.getElementById('tableLoading').style.display = 'block';
  document.getElementById('tableWrapper').style.display = 'none';
  document.getElementById('paginationWrapper').style.display = 'none';
  document.getElementById('emptyState').style.display = 'none';
  try {
    const url = `/mk/api/x/top-influencers?project_id=${InfCfg.pid}&start_date=${InfCfg.sd}&end_date=${InfCfg.ed}&sub=${currentSub}`;
    const res  = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest'} });
    if (!res.ok) throw new Error('HTTP '+res.status);
    const json = await res.json();
    allData = (json.data || []).sort((a,b) => totalEng(b)-totalEng(a));
    filteredData = [...allData];
    renderSummary();
    renderTop5List();
    requestAnimationFrame(()=>renderTop5Donut());
    renderTable();
    updatePagination();
    document.getElementById('tableLoading').style.display      = 'none';
    document.getElementById('tableWrapper').style.display      = 'block';
    document.getElementById('paginationWrapper').style.display = 'flex';
  } catch(err) {
    console.error('loadData error:', err);
    document.getElementById('tableLoading').style.display = 'none';
    document.getElementById('emptyState').style.display   = 'block';
  }
}

/* ══════════════════════════════════════════════════════
   SUMMARY
══════════════════════════════════════════════════════ */
function renderSummary() {
  document.getElementById('statTotal').textContent = numK(allData.length);
  const engSum = allData.reduce((s,d)=>s+totalEng(d),0);
  document.getElementById('statEngagements').textContent = numK(engSum);
  if (allData.length>0) {
    const top = allData[0];
    document.getElementById('statTopAccount').textContent    = top.name||('@'+top.screen_name)||'–';
    document.getElementById('statTopEngagement').textContent = numFmt(totalEng(top))+' engagements';
    const avgFol = Math.round(allData.reduce((s,d)=>s+(parseInt(d.followers_count||d.author_followers_count||0)),0)/allData.length);
    document.getElementById('statAvgFollowers').textContent  = numK(avgFol);
  }
}

/* ══════════════════════════════════════════════════════
   TOP 5 DONUT — same as MAU
══════════════════════════════════════════════════════ */
function renderTop5Donut() {
  const skel = document.getElementById('infDonutSkel');
  const wrap = document.getElementById('infDonutWrap');
  const top5 = allData.slice(0,5);
  if (!top5.length) {
    if(skel)skel.style.display='none';
    wrap.style.display='block';
    wrap.innerHTML=`<div class="tug-empty" style="min-height:400px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/></svg><span>No data available</span></div>`;
    return;
  }
  if(skel)skel.style.display='none';
  wrap.style.display='block';
  if(infDonutInst){try{infDonutInst.dispose();}catch(e){}infDonutInst=null;}
  setTimeout(()=>{
    const dom=document.getElementById('infDonutChart');
    if(!dom)return;
    infDonutInst=echarts.init(dom,null,{renderer:'canvas'});
    const total=top5.reduce((s,u)=>s+totalEng(u),0);
    const seriesData=top5.map((u,i)=>({
      name:u.name||('@'+(u.screen_name||u.username||'')),
      value:totalEng(u),
      username:u.screen_name||u.username||'',
      itemStyle:{color:TUG5_PALETTE[i],borderColor:'#fff',borderWidth:3},
    }));
    infDonutInst.setOption({
      backgroundColor:'transparent',
      animation:true,animationDuration:800,animationEasing:'cubicOut',
      tooltip:{
        trigger:'item',backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,
        padding:[10,14],textStyle:{color:'#f8fafc',fontFamily:"'DM Sans',sans-serif",fontSize:12},
        extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);',
        formatter:p=>{
          const pct=total>0?((p.value/total)*100).toFixed(1):'0.0';
          return `<div style="font-weight:700;margin-bottom:5px;font-size:13px;">${p.name}<br><span style="color:#94a3b8;font-weight:400;font-size:11px;">@${p.data.username}</span></div>
                  <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Engagements</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                  <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#16a085;">${pct}%</span></div>`;
        },
      },
      legend:{show:false},
      series:[{
        name:'Engagements',type:'pie',radius:['46%','64%'],center:['50%','52%'],
        avoidLabelOverlap:true,minAngle:8,
        label:{
          show:true,position:'outside',alignTo:'labelLine',bleedMargin:12,distanceToLabelLine:6,lineHeight:18,
          fontFamily:"'DM Sans',sans-serif",fontSize:11.5,color:'#374151',
          formatter:p=>{
            const name=p.name.length>13?p.name.slice(0,12)+'…':p.name;
            const handle=('@'+p.data.username).length>13?('@'+p.data.username).slice(0,12)+'…':'@'+p.data.username;
            return `{name|${name}}\n{handle|${handle}}\n{eng|${numK(p.value)}}`;
          },
          rich:{
            name: {fontWeight:'700',fontSize:12,color:'#1a202c',lineHeight:20},
            handle:{fontWeight:'400',fontSize:10.5,color:'#64748b',lineHeight:17},
            eng:  {fontWeight:'700',fontSize:11,color:'#16a085',lineHeight:17,backgroundColor:'#e8f8f4',borderRadius:4,padding:[1,5]},
          },
        },
        labelLine:{show:true,length:18,length2:22,smooth:.3,minTurnAngle:90,lineStyle:{color:'#c4cdd8',width:1.3,type:'solid'}},
        emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'},label:{fontSize:12,fontWeight:'700'}},
        data:seriesData,
      }],
      graphic:[
        {type:'text',left:'center',top:'47%',z:100,style:{text:numK(total),fill:'#0f172a',font:"700 26px 'DM Sans',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'55%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 10px 'DM Sans',sans-serif",textAlign:'center',letterSpacing:2}},
      ],
    });
    infDonutInst.resize();
    infDonutInst.on('click',p=>{
      if(p.componentType==='series'){
        const user=top5.find(u=>(u.name||('@'+(u.screen_name||'')))=== p.name);
        if(user)UDM.open(user);
      }
    });
  },50);
}
window.addEventListener('resize',()=>{ if(infDonutInst&&!infDonutInst.isDisposed())infDonutInst.resize(); });

/* ══════════════════════════════════════════════════════
   TOP 5 LIST — same as MAU
══════════════════════════════════════════════════════ */
function renderTop5List() {
  const container = document.getElementById('infRankedList');
  if (!container) return;
  const top5 = allData.slice(0,5);
  if (!top5.length) {
    container.innerHTML=`<div class="tug-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/></svg><span>No users found</span></div>`;
    return;
  }
  const maxE = totalEng(top5[0])||1;
  const rankCls=i=>i===0?'tug-rank-badge--1':i===1?'tug-rank-badge--2':i===2?'tug-rank-badge--3':'';
  container.innerHTML=top5.map((u,i)=>{
    const name   = u.name||u.screen_name||'Unknown';
    const uname  = u.screen_name||u.username||'';
    const e      = totalEng(u);
    const pct    = Math.round((e/maxE)*100);
    const color  = TUG5_PALETTE[i];
    const src    = _avatarUrl(u, 'big');
    const init   = getInitials(name);
    const ujson  = _escAttr(JSON.stringify(u));
    const avatarEl = src
      ? `<img src="${src}" alt="${_esc(name)}" class="tug-avatar" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="tug-avatar-fallback" style="display:none;">${_esc(init)}</div>`
      : `<div class="tug-avatar-fallback">${_esc(init)}</div>`;
    return `
      <div class="tug-row ${i===0?'tug-row--first':''}" onclick="UDM.open(${ujson})">
        <span class="tug-rank-badge ${rankCls(i)}">${i+1}</span>
        <span class="tug-color-dot" style="background:${color};"></span>
        ${avatarEl}
        <div class="tug-user-info">
          <div class="tug-user-name">${_esc(name)}</div>
          <div class="tug-user-handle">@${_esc(uname)}</div>
        </div>
        <div class="tug-user-right">
          <span class="tug-user-eng">${numK(e)}</span>
          <span class="tug-user-bar"><span class="tug-user-bar-fill" data-pct="${pct}" style="width:0%;background:${color};"></span></span>
        </div>
      </div>`;
  }).join('');
  requestAnimationFrame(()=>{
    container.querySelectorAll('.tug-user-bar-fill').forEach(bar=>{bar.style.width=bar.dataset.pct+'%';});
  });
}

/* ══════════════════════════════════════════════════════
   TABLE — same layout as MAU
══════════════════════════════════════════════════════ */
function renderTable() {
  const cfg = TAB_CONFIG[currentSub];
  const si  = (currentPage-1)*PER_PAGE;
  const cd  = filteredData.slice(si, si+PER_PAGE);
  let html  = `<table class="data-table"><thead><tr>
    <th>RANK</th><th>AVATAR</th><th>ACCOUNT</th>
    <th>FOLLOWERS</th>
    <th class="sorted">${cfg.colHeader} ↓</th>
    <th>RETWEETS</th>
    ${cfg.showReplies?'<th>REPLIES</th>':''}
    <th></th>
  </tr></thead><tbody>`;
  cd.forEach((u,i)=>{
    const rank = si+i+1;
    const name = u.name||u.screen_name||'Unknown';
    const uname= u.screen_name||u.username||'';
    const src  = _avatarUrl(u, 'big');
    const init = getInitials(name);
    const cfg2 = TAB_CONFIG[currentSub];
    const rankCls = rank===1?'inf-rank-1':rank===2?'inf-rank-2':rank===3?'inf-rank-3':'inf-rank-n';
    const ujson = _escAttr(JSON.stringify(u));
    const avatarCell = src
      ? `<img src="${src}" class="user-avatar-img" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="${_esc(name)}"><div class="user-avatar-fallback" style="display:none;">${_esc(init)}</div>`
      : `<div class="user-avatar-fallback">${_esc(init)}</div>`;
    html+=`<tr onclick="UDM.open(${ujson})">
      <td><div class="inf-rank ${rankCls}">${rank}</div></td>
      <td>${avatarCell}</td>
      <td>
        <div style="display:flex;flex-direction:column;gap:2px;">
          <a href="https://twitter.com/${uname}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${_esc(name)}</a>
          <a href="https://twitter.com/${uname}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${_esc(uname)}</a>
        </div>
      </td>
      <td><div class="activity-stat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>${numFmt(u.followers_count||u.author_followers_count||0)}</div></td>
      <td><span class="inf-eng-badge ${cfg2.badgeClass}">${numFmt(u.total||totalEng(u))}</span></td>
      <td style="color:var(--text-secondary);font-weight:600;">${numFmt(u.retweets||0)}</td>
      ${cfg.showReplies?`<td style="color:var(--text-secondary);font-weight:600;">${numFmt(u.replies||0)}</td>`:''}
      <td><a href="https://twitter.com/${uname}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();"><svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>View</a></td>
    </tr>`;
  });
  html+='</tbody></table>';
  document.getElementById('tableWrapper').innerHTML=html;
}

function getPageRange(cur,total) {
  if(total<=7) return Array.from({length:total},(_,i)=>i+1);
  if(cur<=4)   return [1,2,3,4,5,'...',total];
  if(cur>=total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
  return [1,'...',cur-1,cur,cur+1,'...',total];
}
function updatePagination() {
  const tp  = Math.ceil(filteredData.length/PER_PAGE);
  const w   = document.getElementById('paginationWrapper');
  const from= filteredData.length?(currentPage-1)*PER_PAGE+1:0;
  const to  = Math.min(currentPage*PER_PAGE,filteredData.length);
  let html  = `<span class="pagination-info">Showing ${numFmt(from)}–${numFmt(to)} of ${numFmt(filteredData.length)} influencers</span><div style="display:flex;align-items:center;gap:6px;">`;
  html+=`<button class="page-btn" onclick="changePage(${currentPage-1})" ${currentPage===1?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg></button>`;
  getPageRange(currentPage,tp).forEach(p=>{
    html+=p==='...'?`<button class="page-btn" disabled style="cursor:default;">…</button>`:`<button class="page-btn ${p===currentPage?'active':''}" onclick="changePage(${p})">${p}</button>`;
  });
  html+=`<button class="page-btn" onclick="changePage(${currentPage+1})" ${currentPage===tp?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg></button></div>`;
  w.innerHTML=html;
  w.style.display=filteredData.length>0?'flex':'none';
}
function changePage(p) {
  const tp=Math.ceil(filteredData.length/PER_PAGE);
  if(p<1||p>tp)return;
  currentPage=p;renderTable();updatePagination();
  document.querySelector('.table-section').scrollIntoView({behavior:'smooth',block:'start'});
}
function filterTable() {
  const q=document.getElementById('infSearchInput').value.toLowerCase().trim();
  filteredData=q?allData.filter(d=>((d.name||'')+(d.screen_name||'')+(d.username||'')).toLowerCase().includes(q)):[...allData];
  currentPage=1;renderTable();updatePagination();
}

/* ══════════════════════════════════════════════════════
   ACTIONS DROPDOWN
══════════════════════════════════════════════════════ */
function toggleActionsDropdown(e){e.stopPropagation();document.getElementById('actionsDropdownMenu').classList.toggle('show');}
document.addEventListener('click',()=>document.getElementById('actionsDropdownMenu')?.classList.remove('show'));

function exportCSV() {
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  if(!filteredData.length)return;
  const cfg=TAB_CONFIG[currentSub];
  let csv=`Rank,Name,Handle,Followers,${cfg.colHeader},Retweets${cfg.showReplies?',Replies':''}\n`;
  filteredData.forEach((u,i)=>{
    const n=(u.name||'').replace(/,/g,' ').replace(/"/g,'""');
    csv+=`${i+1},"${n}","@${u.screen_name||u.username||''}",${u.followers_count||0},${u.total||totalEng(u)||0},${u.retweets||0}${cfg.showReplies?','+( u.replies||0):''}\n`;
  });
  const a=document.createElement('a');
  a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));
  a.download=`top-influencers-${currentSub}-${InfCfg.sd}-${InfCfg.ed}.csv`;
  a.click();
}
function refreshData(){document.getElementById('actionsDropdownMenu').classList.remove('show');window.location.reload();}
function printTable(){
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  const c=document.getElementById('tableWrapper').innerHTML;
  const w=window.open('','_blank');
  w.document.write(`<!DOCTYPE html><html><head><title>Top Influencers</title><style>body{font-family:Arial,sans-serif;padding:20px;}table{width:100%;border-collapse:collapse;}th{background:#f8fafc;padding:10px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;}td{padding:10px;font-size:12px;border-bottom:1px solid #f1f5f9;}</style></head><body><h1>Top Influencers — X (Twitter)</h1><p style="color:#64748b;font-size:13px;">${InfCfg.sd} to ${InfCfg.ed}</p>${c}</body></html>`);
  w.document.close();w.focus();setTimeout(()=>{w.print();w.close();},250);
}

/* ══════════════════════════════════════════════════════
   USER DETAIL MODAL (UDM) — same as MAU
══════════════════════════════════════════════════════ */
const UDM = (() => {
  let _user=null,_mentions=[],_hasMore=false,_apiStart=0,_page=1;
  const _PP=10;
  let _abortCtrl=null;

  function _reset(user){
    if(_abortCtrl){try{_abortCtrl.abort();}catch(e){}}
    _abortCtrl=new AbortController();
    _user=user;_mentions=[];_hasMore=false;_apiStart=0;_page=1;
  }

  async function open(userOrJson){
    let user;
    try{user=(typeof userOrJson==='string')?JSON.parse(userOrJson):userOrJson;}
    catch(e){console.error('UDM.open error',e);return;}
    _reset(user);
    const modal=document.getElementById('userDetailModal');
    const body=document.getElementById('udmBody');
    body.innerHTML='';
    modal.style.display='flex';
    requestAnimationFrame(()=>modal.classList.add('show'));
    body.innerHTML=_profileHTML(user)+_skelHTML();
    body.scrollTop=0;
    const uname=user.screen_name||user.username||'';
    const signal=_abortCtrl.signal;
    const data=await _fetch(uname,0,signal);
    if(signal.aborted)return;
    if(data){_mentions=data.mentions||[];_hasMore=data.has_more||false;_apiStart=data.next_api_start||0;}
    const skel=document.getElementById('udmMentionsSkeleton');
    if(skel&&!signal.aborted){const t=document.createElement('div');t.innerHTML=_mentionsHTML();skel.replaceWith(t.firstElementChild);}
  }

  function close(){
    if(_abortCtrl){try{_abortCtrl.abort();}catch(e){}_abortCtrl=null;}
    const modal=document.getElementById('userDetailModal');
    modal.classList.remove('show');
    setTimeout(()=>{modal.style.display='none';document.getElementById('udmBody').innerHTML='';},240);
  }

  async function _fetch(username,apiStart,signal){
    try{
      const u=_user||{};
      const url=`/mk/api/x/user-detailed-mentions?project_id=${InfCfg.pid}`
               +`&username=${encodeURIComponent(username)}`
               +`&start_date=${InfCfg.sd}&end_date=${InfCfg.ed}`
               +`&api_start=${apiStart}`
               +`&stat_mentions=${u.mentions||0}&stat_replies=${u.replies||0}&stat_retweets=${u.retweets||0}`;
      const r=await fetch(url,signal?{signal}:{});
      if(!r.ok)return null;
      const j=await r.json();
      return j.success?j.data:null;
    }catch(e){if(e.name==='AbortError')return null;console.error('UDM fetch:',e);return null;}
  }

  function _profileHTML(u){
    const name    = u.name||u.screen_name||'Unknown';
    const uname   = u.screen_name||u.username||'';
    const src     = _avatarUrl(u, 'large');
    const init    = getInitials(name);
    const udmAvatarEl = src
      ? `<img src="${src}" class="udm-avatar" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="${_esc(name)}"><div class="udm-avatar-fb" style="display:none;">${_esc(init)}</div>`
      : `<div class="udm-avatar-fb">${_esc(init)}</div>`;
    const followers= parseInt(u.followers_count||u.author_followers_count||0);
    const rt      = parseInt(u.retweets||0);
    const rep     = parseInt(u.replies||0);
    const tot     = parseInt(u.total||0)||rt+rep;
    return `
      <div class="udm-banner"><div class="udm-banner-dots"></div></div>
      <div class="udm-profile-section">
        <div class="udm-profile-row">
          <div class="udm-avatar-wrap">
            ${udmAvatarEl}
          </div>
          <a href="https://twitter.com/${_esc(uname)}" target="_blank" class="udm-view-btn">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            View on X
          </a>
        </div>
        <div class="udm-name">
          ${_esc(name)}
          <span class="udm-verified-badge"><svg viewBox="0 0 24 24" width="19" height="19" fill="#1d9bf0"><path d="M22.25 12c0-1.43-.88-2.67-2.19-3.34.46-1.39.2-2.9-.81-3.91s-2.52-1.27-3.91-.81c-.66-1.31-1.91-2.19-3.34-2.19s-2.67.88-3.33 2.19c-1.4-.46-2.91-.2-3.92.81s-1.26 2.52-.8 3.91C1.88 9.33 1 10.57 1 12s.88 2.67 2.19 3.34c-.46 1.39-.2 2.9.8 3.91s2.52 1.26 3.92.8c.66 1.31 1.9 2.19 3.33 2.19s2.68-.88 3.34-2.19c1.39.46 2.9.2 3.91-.8s1.27-2.52.81-3.91C21.37 14.67 22.25 13.43 22.25 12zm-6.47-1.53L11.11 15.1a.75.75 0 01-1.06 0L7.72 12.77a.75.75 0 011.06-1.06l1.8 1.8 4.13-4.13a.75.75 0 111.06 1.06l.01.03z"/></svg></span>
        </div>
        <div class="udm-handle">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="#94a3b8"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          <a href="https://twitter.com/${_esc(uname)}" target="_blank">@${_esc(uname)}</a>
        </div>
        <div class="udm-follower-chip">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <strong>${numFmt(followers)}</strong> Followers
        </div>
      </div>
      <div class="udm-eng-strip">
        <div class="udm-eng-item udm-eng-item--mentions">
          <div class="udm-eng-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          <span class="udm-eng-val">${numFmt(rep)}</span>
          <span class="udm-eng-lbl">Replies</span>
        </div>
        <div class="udm-eng-item udm-eng-item--followers">
          <div class="udm-eng-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
          <span class="udm-eng-val">${numFmt(followers)}</span>
          <span class="udm-eng-lbl">Followers</span>
        </div>
        <div class="udm-eng-item udm-eng-item--retweets">
          <div class="udm-eng-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div>
          <span class="udm-eng-val">${numFmt(rt)}</span>
          <span class="udm-eng-lbl">Retweets</span>
        </div>
      </div>
      <div class="udm-total-row">
        <div class="udm-total-row-label">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Total Engagement
        </div>
        <div class="udm-total-row-val">${numFmt(tot)}</div>
      </div>`;
  }

  function _skelHTML(){
    return `<div class="udm-mentions-wrap" id="udmMentionsSkeleton">
      <div class="udm-mentions-head">
        <div class="udm-mentions-title">
          <div class="udm-mentions-title-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          Tweets & Mentions
        </div>
        <span class="udm-mentions-count">Loading…</span>
      </div>
      <div class="udm-loading"><div class="udm-spinner"></div><span class="udm-loading-txt">Fetching tweets…</span></div>
    </div>`;
  }

  function _mentionsHTML(){
    const total=_mentions.length;
    const totalPages=Math.ceil(total/_PP);
    const si=(_page-1)*_PP,ei=Math.min(si+_PP,total);
    const page=_mentions.slice(si,ei);
    const countLabel=_hasMore?`${total} loaded · more available`:`${total} found`;
    let html=`<div class="udm-mentions-wrap">
      <div class="udm-mentions-head">
        <div class="udm-mentions-title">
          <div class="udm-mentions-title-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          Tweets & Mentions
        </div>
        <span class="udm-mentions-count">${countLabel}</span>
      </div>`;
    if(!total&&!_hasMore){
      html+=`<div class="udm-empty-mentions"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>No mentions found for this period.</p></div></div>`;
      return html;
    }
    html+=`<div class="udm-mentions-list">`;
    page.forEach(m=>{html+=_cardHTML(m);});
    html+=`</div>`;
    if(total>_PP||_hasMore){
      const isLast=_page>=totalPages,canLoad=isLast&&_hasMore;
      html+=`<div class="udm-pagination" id="udmPagination"><span class="udm-pg-info">Page ${_page} of ${totalPages}${_hasMore?'+':''} · ${si+1}–${ei} of ${total}${_hasMore?'+':''}</span><div class="udm-pg-btns">`;
      html+=`<button class="udm-pg-btn" ${_page<=1?'disabled':''} onclick="UDM.goPage(${_page-1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>`;
      let pS=Math.max(1,_page-2),pE=Math.min(totalPages,pS+4);
      if(pE-pS<4)pS=Math.max(1,pE-4);
      if(pS>1)html+=`<button class="udm-pg-btn" onclick="UDM.goPage(1)">1</button>`;
      if(pS>2)html+=`<span style="align-self:center;color:var(--text-muted);font-size:12px;">…</span>`;
      for(let p=pS;p<=pE;p++)html+=`<button class="udm-pg-btn ${p===_page?'active':''}" onclick="UDM.goPage(${p})">${p}</button>`;
      if(pE<totalPages-1)html+=`<span style="align-self:center;color:var(--text-muted);font-size:12px;">…</span>`;
      if(pE<totalPages)html+=`<button class="udm-pg-btn" onclick="UDM.goPage(${totalPages})">${totalPages}</button>`;
      if(canLoad)html+=`<button class="udm-pg-btn udm-pg-btn--load" id="udmPgLoadMore" onclick="UDM.fetchNextPage()">Load More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12"><polyline points="9 18 15 12 9 6"/></svg></button>`;
      else html+=`<button class="udm-pg-btn" ${_page>=totalPages?'disabled':''} onclick="UDM.goPage(${_page+1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button>`;
      html+=`</div></div>`;
    }
    html+='</div>';
    return html;
  }

  function goPage(p){
    const tp=Math.ceil(_mentions.length/_PP);
    if(p<1||p>tp)return;
    _page=p;_redraw();
    document.querySelector('#udmBody .udm-mentions-head')?.scrollIntoView({behavior:'smooth',block:'nearest'});
  }

  async function fetchNextPage(){
    const btn=document.getElementById('udmPgLoadMore');
    if(btn){btn.disabled=true;btn.innerHTML=`<span class="udm-pg-loading-dot"></span> Loading…`;}
    const signal=_abortCtrl?.signal;
    const data=await _fetch(_user?.screen_name||_user?.username||'',_apiStart,signal);
    if(!data||signal?.aborted){if(btn){btn.disabled=false;btn.innerHTML='Load More <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="12" height="12"><polyline points="9 18 15 12 9 6"/></svg>';}return;}
    _mentions=[..._mentions,...(data.mentions||[])];
    _hasMore=data.has_more||false;_apiStart=data.next_api_start||0;
    const tp=Math.ceil(_mentions.length/_PP);
    _page=Math.min(_page+1,tp);
    _redraw();
  }

  function _redraw(){
    const wrap=document.querySelector('#udmBody .udm-mentions-wrap');
    if(!wrap)return;
    const t=document.createElement('div');t.innerHTML=_mentionsHTML();
    wrap.replaceWith(t.firstElementChild);
  }

  function _linkify(raw){
    if(!raw)return '<em style="color:#94a3b8;">No content available</em>';
    let t=raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    t=t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g,url=>{const h=url.replace(/&amp;/g,'&');return `<a href="${h}" target="_blank" rel="noopener" class="udm-text-link udm-text-link--url">${url}</a>`;});
    t=t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,'<a href="https://twitter.com/$1" target="_blank" rel="noopener" class="udm-text-link udm-text-link--mention">@$1</a>');
    t=t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F\u0400-\u04FF]+)/g,'<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" class="udm-text-link udm-text-link--hashtag">#$1</a>');
    return t;
  }

  function _cardHTML(m){
    const rawTs=m.timestamp||m.created_at||'';
    let dtStr='';
    if(rawTs){const d=new Date(rawTs);if(!isNaN(d))dtStr=d.toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}
    const sent=m.sentiment||'neutral';
    const sentCls=sent==='positive'?'pos':sent==='negative'?'neg':'neu';
    const sentLbl=sent==='positive'?'Positive':sent==='negative'?'Negative':'Neutral';
    const author=m.author_name||m.author||_user?.screen_name||'';
    const handle=m.author||_user?.screen_name||'';
    const mtype=(m.mention_type||m.tcode||'tweet').toLowerCase();
    let typeCls='tweet',typeLbl='Tweet';
    if(mtype.includes('reply')||mtype==='rep'){typeCls='reply';typeLbl='Reply';}
    if(mtype.includes('retweet')||mtype==='rt'){typeCls='retweet';typeLbl='Retweet';}
    if(mtype.includes('mention')||mtype==='men'){typeCls='mention';typeLbl='Mention';}
    const likes=parseInt(m.likes||m.num_likes||0);
    const rts=parseInt(m.retweets||m.num_shares||0);
    const reps=parseInt(m.replies||m.num_comments||0);
    const text=m.text?_linkify(m.text):'<em style="color:#94a3b8;">No content available</em>';
    const viewLink=(m.url&&m.url!=='#')?`<a href="${_escHref(m.url)}" target="_blank" rel="noopener" class="udm-card-view-link" onclick="event.stopPropagation();"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>View Tweet</a>`:'';
    const miniSrc = m.author_profile_image||m.profile_image||_avatarUrl(_user,'big')||(handle?`https://unavatar.io/twitter/${handle}`:'');
    const miniInit=getInitials(author);
    const mjson=_escAttr(JSON.stringify(m));
    const miniAvatarEl = miniSrc
      ? `<img src="${miniSrc}" class="udm-card-mini-avatar" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="${_esc(author)}"><div class="udm-card-mini-avatar-fb" style="display:none;">${_esc(miniInit)}</div>`
      : `<div class="udm-card-mini-avatar-fb">${_esc(miniInit)}</div>`;
    return `<div class="udm-card" onclick="TDM.open(${mjson});event.stopPropagation();">
      <div class="udm-card-header">
        ${miniAvatarEl}
        <div class="udm-card-meta">
          <div class="udm-card-row1">
            <div class="udm-card-name-block">
              <span class="udm-card-author">${_esc(author)}</span>
              <span class="udm-card-handle">@${_esc(handle)}${dtStr?' · '+dtStr:''}</span>
            </div>
            <span class="udm-pill udm-pill--${sentCls}">${sentLbl}</span>
          </div>
          <div class="udm-card-badges"><span class="udm-type-badge udm-type-badge--${typeCls}">${typeLbl}</span></div>
        </div>
      </div>
      <div class="udm-card-text">${text}</div>
      <div class="udm-card-foot">
        <div class="udm-card-actions">
          <span class="udm-card-action likes"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>${numFmt(likes)}</span>
          <span class="udm-card-action rts"><svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>${numFmt(rts)}</span>
          <span class="udm-card-action"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>${numFmt(reps)}</span>
        </div>
        ${viewLink}
      </div>
    </div>`;
  }

  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&document.getElementById('userDetailModal')?.classList.contains('show'))close();});
  return{open,close,goPage,fetchNextPage};
})();

/* ══════════════════════════════════════════════════════
   TWEET DETAIL MODAL (TDM) — same as MAU
══════════════════════════════════════════════════════ */
const TDM = (() => {
  function open(mentionOrJson){
    let m;try{m=(typeof mentionOrJson==='string')?JSON.parse(mentionOrJson):mentionOrJson;}catch(e){return;}
    const body=document.getElementById('tdmBody');if(!body)return;
    body.innerHTML=_build(m);body.scrollTop=0;
    body.querySelectorAll('a').forEach(a=>a.addEventListener('click',e=>e.stopPropagation()));
    const modal=document.getElementById('tweetDetailModal');
    modal.style.display='flex';requestAnimationFrame(()=>modal.classList.add('show'));
  }
  function close(){
    const modal=document.getElementById('tweetDetailModal');
    modal.classList.remove('show');
    setTimeout(()=>{modal.style.display='none';document.getElementById('tdmBody').innerHTML='';},200);
  }
  function _build(m){
    const author=m.author_name||m.author||'';
    const handle=m.author||'';
    const avatarSrc = m.author_profile_image||m.profile_image||(handle?`https://unavatar.io/twitter/${handle}`:'');
    const avatarEl  = avatarSrc
      ? `<img src="${avatarSrc}" class="tdm-author-avatar" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';" alt="${_esc(author)}"><div class="tdm-author-avatar-fb" style="display:none;">${_esc(init)}</div>`
      : `<div class="tdm-author-avatar-fb">${_esc(init)}</div>`;
    const init=getInitials(author);
    const rawTs=m.timestamp||m.created_at||'';
    let dtStr='';
    if(rawTs){const d=new Date(rawTs);if(!isNaN(d))dtStr=d.toLocaleString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}
    const likes=parseInt(m.likes||m.num_likes||0);
    const rts=parseInt(m.retweets||m.num_shares||0);
    const reps=parseInt(m.replies||m.num_comments||0);
    const sent=m.sentiment||'neutral';
    const sentCls=sent==='positive'?'pos':sent==='negative'?'neg':'neu';
    const sentLbl=sent==='positive'?'Positive':sent==='negative'?'Negative':'Neutral';
    const mtype=(m.mention_type||m.tcode||'tweet').toLowerCase();
    let typeCls='tweet',typeLbl='Tweet';
    if(mtype.includes('reply')||mtype==='rep'){typeCls='reply';typeLbl='Reply';}
    if(mtype.includes('retweet')||mtype==='rt'){typeCls='retweet';typeLbl='Retweet';}
    if(mtype.includes('mention')||mtype==='men'){typeCls='mention';typeLbl='Mention';}
    const text=m.text?_linkify(m.text):'<em style="color:#94a3b8;">No content available</em>';
    const tweetUrl=(m.url&&m.url!=='#')?m.url:`https://twitter.com/${handle}`;
    return `
      <div class="tdm-author-section">
        ${avatarEl}
        <div class="tdm-author-info">
          <div class="tdm-author-name">${_esc(author)}<svg viewBox="0 0 24 24" width="16" height="16" fill="#1d9bf0" style="flex-shrink:0;"><path d="M22.25 12c0-1.43-.88-2.67-2.19-3.34.46-1.39.2-2.9-.81-3.91s-2.52-1.27-3.91-.81c-.66-1.31-1.91-2.19-3.34-2.19s-2.67.88-3.33 2.19c-1.4-.46-2.91-.2-3.92.81s-1.26 2.52-.8 3.91C1.88 9.33 1 10.57 1 12s.88 2.67 2.19 3.34c-.46 1.39-.2 2.9.8 3.91s2.52 1.26 3.92.8c.66 1.31 1.9 2.19 3.33 2.19s2.68-.88 3.34-2.19c1.39.46 2.9.2 3.91-.8s1.27-2.52.81-3.91C21.37 14.67 22.25 13.43 22.25 12zm-6.47-1.53L11.11 15.1a.75.75 0 01-1.06 0L7.72 12.77a.75.75 0 011.06-1.06l1.8 1.8 4.13-4.13a.75.75 0 111.06 1.06l.01.03z"/></svg></div>
          <div class="tdm-author-handle"><a href="https://twitter.com/${_esc(handle)}" target="_blank" rel="noopener">@${_esc(handle)}</a></div>
        </div>
        <a href="${_escHref(tweetUrl)}" target="_blank" rel="noopener" class="tdm-view-x-btn">
          <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          View on X
        </a>
      </div>
      <div class="tdm-tweet-body">
        <div class="tdm-tweet-text">${text}</div>
        <div class="tdm-tweet-meta">
          <span class="tdm-badge tdm-badge--${sentCls}">${sentLbl}</span>
          <span class="tdm-badge tdm-badge--${typeCls}">${typeLbl}</span>
          ${dtStr?`<span class="tdm-timestamp">${dtStr}</span>`:''}
        </div>
      </div>
      <div class="tdm-stats-strip">
        <div class="tdm-stat-item tdm-stat-item--likes"><div class="tdm-stat-icon"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div><span class="tdm-stat-val">${numFmt(likes)}</span><span class="tdm-stat-lbl">Likes</span></div>
        <div class="tdm-stat-item tdm-stat-item--rts"><div class="tdm-stat-icon"><svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div><span class="tdm-stat-val">${numFmt(rts)}</span><span class="tdm-stat-lbl">Retweets</span></div>
        <div class="tdm-stat-item tdm-stat-item--replies"><div class="tdm-stat-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div><span class="tdm-stat-val">${numFmt(reps)}</span><span class="tdm-stat-lbl">Replies</span></div>
      </div>
      <div class="tdm-footer">
        <a href="https://twitter.com/${_esc(handle)}" target="_blank" rel="noopener" class="tdm-footer-btn tdm-footer-btn--outline">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          View Profile
        </a>
        <a href="${_escHref(tweetUrl)}" target="_blank" rel="noopener" class="tdm-footer-btn tdm-footer-btn--primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Open Tweet
        </a>
      </div>`;
  }
  function _linkify(raw){
    if(!raw)return '';
    let t=raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    t=t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g,url=>{const h=url.replace(/&amp;/g,'&');return `<a href="${h}" target="_blank" rel="noopener" class="tdm-link tdm-link--url">${url}</a>`;});
    t=t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,'<a href="https://twitter.com/$1" target="_blank" rel="noopener" class="tdm-link tdm-link--mention">@$1</a>');
    t=t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F\u0400-\u04FF]+)/g,'<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" class="tdm-link tdm-link--hashtag">#$1</a>');
    return t;
  }
  document.addEventListener('keydown',e=>{if(e.key==='Escape'){const t=document.getElementById('tweetDetailModal');if(t?.classList.contains('show')){close();return;}}});
  return{open,close};
})();

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded',()=>{
  InfDp.init();
  if(InfCfg.pid) loadData();
});
</script>
@endsection