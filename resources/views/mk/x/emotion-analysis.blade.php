@extends('mk.layouts.app')

@section('title', 'Emotion Analysis – X (Twitter)')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   DESIGN TOKENS
═══════════════════════════════════════════════════════════ */
:root {
  --space-1:4px;--space-2:8px;--space-3:12px;
  --space-4:16px;--space-5:20px;--space-6:24px;
  --space-7:28px;--space-8:32px;--space-10:40px;

  --radius-sm:8px;--radius-md:12px;
  --radius-lg:16px;--radius-xl:20px;--radius-full:9999px;

  /* Brand */
  --color-brand:       #16a085;
  --color-brand-dark:  #0d6b5a;
  --color-brand-light: #e8f8f4;
  --color-brand-mid:   #b2dfdb;
  --color-brand2:      #2e7d6e;

  /* Emotion palette */
  --emo-joy:           #f59e0b;
  --emo-joy-bg:        #fef3c7;
  --emo-trust:         #10b981;
  --emo-trust-bg:      #d1fae5;
  --emo-fear:          #8b5cf6;
  --emo-fear-bg:       #ede9fe;
  --emo-surprise:      #06b6d4;
  --emo-surprise-bg:   #cffafe;
  --emo-sadness:       #6366f1;
  --emo-sadness-bg:    #e0e7ff;
  --emo-disgust:       #a855f7;
  --emo-disgust-bg:    #f3e8ff;
  --emo-anger:         #ef4444;
  --emo-anger-bg:      #fee2e2;
  --emo-anticipation:  #f97316;
  --emo-anticipation-bg:#ffedd5;

  /* Text */
  --text-primary:   #0f1419;
  --text-secondary: #4b5563;
  --text-muted:     #94a3b8;
  --text-label:     #64748b;

  /* Surfaces */
  --border:         1px solid #e5e9ef;
  --border-strong:  1px solid #d1d9e0;
  --bg-card:        #ffffff;
  --bg-muted:       #f8fafc;
  --bg-subtle:      #f1f5f9;
  --bg-hover:       #f7fbf9;
  --bg-page:        #f0f4f8;

  /* Shadows */
  --shadow-sm:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
  --shadow-md:0 4px 12px rgba(0,0,0,.08),0 2px 6px rgba(0,0,0,.05);
  --shadow-lg:0 10px 30px rgba(0,0,0,.1),0 4px 12px rgba(0,0,0,.06);
  --shadow-xl:0 20px 50px rgba(0,0,0,.14),0 8px 20px rgba(0,0,0,.08);

  --font-sans:'DM Sans',-apple-system,BlinkMacSystemFont,sans-serif;
  --font-mono:'DM Mono',monospace;

  --transition:all .2s cubic-bezier(.4,0,.2,1);
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font-sans);background:var(--bg-page);color:var(--text-primary);}

/* ═══════════════════════════════════════════════════════════
   PAGE WRAPPER
═══════════════════════════════════════════════════════════ */
.emo-page{padding:24px;max-width:1600px;margin:0 auto;}

/* ═══════════════════════════════════════════════════════════
   PAGE HEADER
═══════════════════════════════════════════════════════════ */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.page-header-left h1{font-size:28px;font-weight:800;color:var(--text-primary);letter-spacing:-.5px;line-height:1.15;}
.page-header-left p{font-size:14px;color:var(--text-secondary);font-weight:500;margin-top:4px;}
.last-updated{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text-muted);white-space:nowrap;background:var(--bg-card);border:1px solid #edf0f4;border-radius:var(--radius-full);padding:7px 14px;box-shadow:var(--shadow-sm);}
.last-updated svg{width:13px;height:13px;flex-shrink:0;color:var(--color-brand2);}
.last-updated strong{color:var(--text-secondary);font-weight:600;}

/* ═══════════════════════════════════════════════════════════
   FILTER CARD
═══════════════════════════════════════════════════════════ */
.filter-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);padding:18px 22px;margin-bottom:28px;}
.filter-content{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;}
.filter-group{display:flex;flex-direction:column;gap:6px;}
.filter-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-label);line-height:1;}
.date-picker-trigger{display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg-muted);border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:14px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:var(--transition);min-width:300px;}
.date-picker-trigger:hover{border-color:var(--color-brand);background:var(--bg-card);box-shadow:0 0 0 3px rgba(22,160,133,.08);}
.date-picker-trigger svg{width:16px;height:16px;color:var(--text-secondary);flex-shrink:0;}
.apply-btn{display:flex;align-items:center;gap:8px;padding:10px 24px;background:linear-gradient(135deg,var(--color-brand),var(--color-brand-dark));color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:14px;font-weight:700;cursor:pointer;transition:var(--transition);box-shadow:0 4px 12px rgba(22,160,133,.22);white-space:nowrap;}
.apply-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(22,160,133,.35);}
.apply-btn svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5;}

/* ═══════════════════════════════════════════════════════════
   SECTION HEADER (same pattern as sentiment page)
═══════════════════════════════════════════════════════════ */
.emo-section-header{display:flex;align-items:center;gap:10px;margin-bottom:16px;margin-top:4px;}
.emo-section-icon{width:36px;height:36px;border-radius:var(--radius-sm);background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.emo-section-icon svg{width:18px;height:18px;stroke:var(--color-brand2);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.emo-section-title{font-size:13px;font-weight:700;color:var(--text-label);text-transform:uppercase;letter-spacing:.8px;}
.emo-section-line{flex:1;height:1.5px;background:#e5e9ef;border-radius:1px;}

/* ═══════════════════════════════════════════════════════════
   STAT CARDS (matching sentiment card pattern)
═══════════════════════════════════════════════════════════ */
.emo-stat-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:28px;}
.emo-stat-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-lg);padding:18px 20px;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative;overflow:hidden;cursor:default;}
.emo-stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--accent-color,linear-gradient(90deg,var(--color-brand),var(--color-brand-dark)));opacity:0;transition:opacity .25s;}
.emo-stat-card:hover{box-shadow:var(--shadow-lg);transform:translateY(-2px);}
.emo-stat-card:hover::before{opacity:1;}
.emo-stat-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);margin-bottom:10px;}
.emo-stat-value{font-size:28px;font-weight:800;letter-spacing:-.6px;line-height:1.1;min-height:36px;display:flex;align-items:center;}
.emo-stat-sub{font-size:11.5px;color:var(--text-muted);font-weight:500;margin-top:6px;}
.emo-stat-pct{font-size:13px;font-weight:700;margin-top:4px;}

/* ═══════════════════════════════════════════════════════════
   CARD (base component)
═══════════════════════════════════════════════════════════ */
.emo-card{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-lg);overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative;}
.emo-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--color-brand),var(--color-brand-dark));opacity:0;transition:opacity .3s;}
.emo-card:hover{box-shadow:var(--shadow-lg);border-color:rgba(22,160,133,.2);}
.emo-card:hover::before{opacity:1;}
.emo-card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #edf0f4;flex-shrink:0;gap:10px;}
.emo-card-head-left{display:flex;align-items:center;gap:12px;min-width:0;}
.emo-head-icon{width:38px;height:38px;border-radius:var(--radius-sm);background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.emo-head-icon svg{width:19px;height:19px;fill:none;stroke:var(--color-brand2);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.emo-card-title{font-size:14px;font-weight:800;color:var(--text-primary);letter-spacing:-.2px;}
.emo-card-subtitle{font-size:11px;color:var(--text-muted);font-weight:500;margin-top:2px;}
.emo-badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:var(--radius-full);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;background:var(--bg-subtle);color:var(--text-label);border:1px solid #e2e8f0;white-space:nowrap;flex-shrink:0;}
.emo-card-body{padding:20px;flex:1;}

/* ═══════════════════════════════════════════════════════════
   GRID LAYOUTS
═══════════════════════════════════════════════════════════ */
.emo-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.emo-grid-2-3{display:grid;grid-template-columns:1fr 1.6fr;gap:20px;margin-bottom:20px;}
.emo-grid-3-2{display:grid;grid-template-columns:1.6fr 1fr;gap:20px;margin-bottom:20px;}
.emo-mb20{margin-bottom:20px;}

/* ═══════════════════════════════════════════════════════════
   CHART HEIGHTS
═══════════════════════════════════════════════════════════ */
.emo-ch-260{position:relative;height:260px;}
.emo-ch-300{position:relative;height:300px;}
.emo-ch-320{position:relative;height:320px;}
.emo-ch-360{position:relative;height:360px;}
.emo-ch-400{position:relative;height:400px;}

/* ═══════════════════════════════════════════════════════════
   SKELETON / LOADING
═══════════════════════════════════════════════════════════ */
.emo-skel{background:linear-gradient(90deg,#f1f5f9 25%,#e8edf2 50%,#f1f5f9 75%);background-size:200% 100%;animation:eShim 1.4s ease-in-out infinite;border-radius:4px;}
@keyframes eShim{0%{background-position:200% 0}100%{background-position:-200% 0}}
.emo-skel-overlay{position:absolute;inset:0;z-index:3;border-radius:inherit;}

/* ═══════════════════════════════════════════════════════════
   EMOTION LEGEND STRIP
═══════════════════════════════════════════════════════════ */
.emo-legend{display:flex;align-items:center;gap:16px;flex-wrap:wrap;padding:12px 20px;border-top:1px solid #edf0f4;background:var(--bg-muted);}
.emo-leg-item{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:opacity .15s;user-select:none;}
.emo-leg-item.hidden{opacity:.3;}
.emo-leg-dot{width:10px;height:3px;border-radius:2px;flex-shrink:0;}

/* ═══════════════════════════════════════════════════════════
   EMOTION BADGE
═══════════════════════════════════════════════════════════ */
.emo-badge-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:700;text-transform:capitalize;line-height:1.4;}
.emo-badge-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}

/* ═══════════════════════════════════════════════════════════
   PLATFORM BREAKDOWN LIST (emotion per bucket)
═══════════════════════════════════════════════════════════ */
.emo-media-list{display:flex;flex-direction:column;gap:8px;}
.emo-media-row{display:flex;align-items:center;gap:12px;padding:11px 16px;background:var(--bg-muted);border:1px solid #edf0f4;border-radius:var(--radius-sm);transition:var(--transition);cursor:pointer;}
.emo-media-row:hover{border-color:rgba(22,160,133,.3);background:var(--bg-card);box-shadow:var(--shadow-sm);}
.emo-media-icon{width:34px;height:34px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;}
.emo-media-name{font-size:13px;font-weight:700;color:var(--text-primary);min-width:100px;text-transform:capitalize;}
.emo-media-bars{flex:1;display:flex;flex-direction:column;gap:3px;}
.emo-media-bar-row{display:flex;align-items:center;gap:6px;}
.emo-media-bar-track{flex:1;height:7px;background:var(--bg-subtle);border-radius:4px;overflow:hidden;}
.emo-media-bar-fill{height:100%;border-radius:4px;transition:width .8s cubic-bezier(.4,0,.2,1);}
.emo-media-bar-val{font-size:10px;font-weight:700;color:var(--text-secondary);min-width:38px;text-align:right;white-space:nowrap;}
.emo-media-total{font-size:13px;font-weight:800;color:var(--text-primary);min-width:56px;text-align:right;}

/* ═══════════════════════════════════════════════════════════
   TABLE
═══════════════════════════════════════════════════════════ */
.emo-table-section{background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow:hidden;margin-top:20px;}
.emo-table-header{display:flex;align-items:center;justify-content:space-between;gap:var(--space-4);padding:18px 22px;border-bottom:1px solid #edf0f4;}
.emo-table-title h3{font-size:15px;font-weight:800;color:var(--text-primary);letter-spacing:-.3px;margin:0 0 3px;}
.emo-table-subtitle{font-size:12px;color:var(--text-muted);margin:0;}
.emo-table-actions{display:flex;align-items:center;gap:10px;flex-shrink:0;}
.table-search{position:relative;}
.table-search input{padding:9px 14px 9px 36px;border:1px solid #e2e8f0;border-radius:var(--radius-full);font-family:var(--font-sans);font-size:13px;color:var(--text-primary);background:var(--bg-muted);outline:none;transition:var(--transition);width:220px;}
.table-search input:focus{border-color:var(--color-brand);background:var(--bg-card);box-shadow:0 0 0 3px rgba(22,160,133,.08);}
.table-search svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-muted);pointer-events:none;}
.emo-filter-select{height:37px;padding:0 12px;border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:13px;color:var(--text-secondary);background:var(--bg-card);cursor:pointer;outline:none;transition:var(--transition);min-width:150px;}
.emo-filter-select:focus{border-color:var(--color-brand);box-shadow:0 0 0 3px rgba(22,160,133,.08);}
.data-table{width:100%;border-collapse:collapse;font-size:13px;}
.data-table thead tr{background:var(--bg-muted);border-bottom:1px solid #edf0f4;}
.data-table th{padding:11px 16px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-label);text-align:left;white-space:nowrap;}
.data-table td{padding:13px 16px;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
.data-table tbody tr:last-child td{border-bottom:none;}
.data-table tbody tr:hover td{background:var(--bg-muted);}
.data-table tbody tr{cursor:pointer;transition:background .12s;}
.tweet-text-cell{max-width:420px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-primary);font-weight:500;}

/* pagination */
.pagination-row{display:flex;align-items:center;justify-content:space-between;padding:12px 22px;border-top:1px solid #edf0f4;background:var(--bg-muted);gap:16px;flex-wrap:wrap;}
.pagination-info{font-size:12.5px;color:var(--text-muted);}
.pagination-info strong{color:var(--text-secondary);font-weight:600;}
.pagination-btns{display:flex;align-items:center;gap:4px;}
.page-btn{min-width:32px;height:32px;padding:0 8px;border-radius:var(--radius-sm);border:1px solid #e2e8f0;background:var(--bg-card);font-family:var(--font-sans);font-size:12.5px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);display:flex;align-items:center;justify-content:center;}
.page-btn:hover:not(:disabled):not(.active){border-color:var(--color-brand);color:var(--color-brand);background:var(--color-brand-light);}
.page-btn.active{background:var(--color-brand);color:#fff;border-color:var(--color-brand);box-shadow:0 2px 8px rgba(22,160,133,.25);}
.page-btn:disabled{opacity:.4;cursor:default;}
.page-btn svg{width:13px;height:13px;stroke:currentColor;stroke-width:2.5;}

/* export btn */
.emo-csv-btn{display:flex;align-items:center;gap:5px;padding:7px 14px;background:var(--bg-subtle);border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);}
.emo-csv-btn:hover{background:var(--color-brand);border-color:var(--color-brand);color:#fff;}
.emo-csv-btn svg{width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2;}

/* ═══════════════════════════════════════════════════════════
   DATE PICKER MODAL
═══════════════════════════════════════════════════════════ */
.dp-modal{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:16px;}
.dp-modal.show{display:flex;}
.dp-overlay{position:absolute;inset:0;background:rgba(15,20,25,.45);backdrop-filter:blur(6px);animation:dpOvIn .2s ease forwards;}
@keyframes dpOvIn{from{opacity:0}to{opacity:1}}
.dp-content{position:relative;z-index:1;background:var(--bg-card);border-radius:var(--radius-xl);box-shadow:var(--shadow-xl);width:100%;max-width:740px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;animation:dpIn .25s cubic-bezier(.22,1,.36,1) forwards;}
@keyframes dpIn{from{transform:scale(.96) translateY(12px);opacity:0;}to{transform:scale(1) translateY(0);opacity:1;}}
.dp-top{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:var(--border);flex-shrink:0;}
.dp-top h3{font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;}
.dp-close{width:30px;height:30px;border-radius:50%;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);transition:background .15s;}
.dp-close:hover{background:var(--bg-subtle);}
.dp-close svg{width:15px;height:15px;stroke:currentColor;stroke-width:2.5;}
.dp-body{display:flex;flex:1;overflow:hidden;}
.dp-sidebar{width:160px;padding:16px 12px;border-right:var(--border);display:flex;flex-direction:column;gap:4px;flex-shrink:0;background:var(--bg-muted);}
.date-preset{padding:9px 12px;border-radius:var(--radius-sm);border:none;background:transparent;font-family:var(--font-sans);font-size:12.5px;font-weight:500;color:var(--text-secondary);cursor:pointer;text-align:left;transition:all .13s;}
.date-preset:hover{background:var(--bg-subtle);color:var(--text-primary);}
.date-preset.active{background:var(--color-brand-light);color:var(--color-brand);font-weight:700;}
.dp-main{flex:1;padding:20px 24px;display:flex;flex-direction:column;gap:16px;overflow-y:auto;}
.dp-calendars{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.dp-cal{display:flex;flex-direction:column;gap:10px;}
.dp-cal-nav{display:flex;align-items:center;justify-content:space-between;gap:8px;}
.dp-cal-nav-btn{width:28px;height:28px;border-radius:50%;border:1px solid #e2e8f0;background:var(--bg-card);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .13s;}
.dp-cal-nav-btn:hover{background:var(--bg-subtle);border-color:var(--color-brand);}
.dp-cal-nav-btn svg{width:12px;height:12px;stroke:var(--text-secondary);stroke-width:2.5;}
.dp-cal-month{font-size:13px;font-weight:700;color:var(--text-primary);text-align:center;flex:1;}
.dp-cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;}
.dp-cal-wd{font-size:9.5px;font-weight:700;color:var(--text-muted);text-align:center;text-transform:uppercase;padding:4px 0 6px;}
.dp-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;transition:all .1s;color:var(--text-primary);}
.dp-day:hover:not(.dp-day--disabled):not(.dp-day--selected){background:var(--color-brand-light);color:var(--color-brand);}
.dp-day--disabled{color:var(--text-muted);cursor:default;pointer-events:none;}
.dp-day--selected{background:var(--color-brand);color:#fff;font-weight:700;}
.dp-day--range{background:var(--color-brand-light);color:var(--color-brand-dark);}
.dp-day--today{font-weight:800;box-shadow:inset 0 0 0 1.5px var(--color-brand-mid);}
.dp-range-display{display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--bg-muted);border-radius:var(--radius-sm);border:1px solid #e2e8f0;}
.dp-range-display span{font-size:13px;font-weight:500;color:var(--text-primary);}
.dp-range-display .dp-range-sep{color:var(--text-muted);font-size:16px;}
.dp-actions{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:var(--border);flex-shrink:0;}
.dp-cancel-btn{padding:9px 20px;border:1px solid #e2e8f0;border-radius:var(--radius-sm);background:var(--bg-card);font-family:var(--font-sans);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all .15s;}
.dp-cancel-btn:hover{border-color:#cbd5e1;background:var(--bg-subtle);}
.dp-apply-btn{padding:9px 24px;border:none;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--color-brand),var(--color-brand-dark));font-family:var(--font-sans);font-size:13px;font-weight:700;color:#fff;cursor:pointer;transition:all .15s;box-shadow:0 3px 10px rgba(22,160,133,.2);}
.dp-apply-btn:hover{transform:translateY(-1px);box-shadow:0 5px 16px rgba(22,160,133,.3);}

/* ═══════════════════════════════════════════════════════════
   TWEET DETAIL MODAL
═══════════════════════════════════════════════════════════ */
.tweet-modal{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:16px;}
.tweet-modal.show{display:flex;}
.tm-overlay{position:absolute;inset:0;background:rgba(15,20,25,.55);backdrop-filter:blur(8px);animation:dpOvIn .2s ease forwards;}
.tm-content{position:relative;z-index:1;width:100%;max-width:620px;max-height:90vh;background:var(--bg-card);border-radius:var(--radius-xl);display:flex;flex-direction:column;overflow:hidden;animation:dpIn .28s cubic-bezier(.22,1,.36,1) forwards;box-shadow:var(--shadow-xl);}
.tm-topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 22px;border-bottom:1px solid #edf0f4;flex-shrink:0;}
.tm-topbar-title{font-size:14px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:8px;}
.tm-topbar-title::before{content:'';display:inline-block;width:3px;height:14px;background:linear-gradient(180deg,var(--color-brand),var(--color-brand-dark));border-radius:2px;}
.tm-close{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:transparent;border:none;cursor:pointer;color:var(--text-secondary);transition:background .15s;}
.tm-close:hover{background:var(--bg-subtle);}
.tm-close svg{width:15px;height:15px;stroke:currentColor;stroke-width:2.5;}
.tm-body{flex:1;overflow-y:auto;padding:22px;}

/* empty state */
.emo-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:44px 20px;gap:10px;}
.emo-empty svg{width:40px;height:40px;stroke:#e2e8f0;fill:none;stroke-width:1.5;}
.emo-empty-text{font-size:13px;font-weight:600;color:var(--text-secondary);}

/* ═══════════════════════════════════════════════════════════
   ACTIONS DROPDOWN
═══════════════════════════════════════════════════════════ */
.actions-dropdown{position:relative;display:inline-block;}
.actions-dropdown-btn{display:flex;align-items:center;gap:7px;padding:8px 15px;background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-sm);font-family:var(--font-sans);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);}
.actions-dropdown-btn:hover{border-color:#cbd5e1;background:var(--bg-muted);}
.actions-dropdown-btn svg{width:16px;height:16px;}
.actions-dropdown-menu{position:absolute;top:calc(100% + 8px);right:0;background:var(--bg-card);border:1px solid #e2e8f0;border-radius:var(--radius-md);box-shadow:var(--shadow-lg);min-width:200px;padding:8px;z-index:1000;display:none;}
.actions-dropdown-menu.show{display:block;animation:dropFadeIn .18s ease-out;}
@keyframes dropFadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.actions-dropdown-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:13px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:all .13s;}
.actions-dropdown-item:hover{background:var(--bg-muted);color:var(--color-brand);}
.actions-dropdown-item svg{width:15px;height:15px;color:var(--text-secondary);}
.actions-dropdown-divider{height:1px;background:#edf0f4;margin:6px 0;}

@media(max-width:1280px){
  .emo-stat-grid{grid-template-columns:repeat(3,1fr);}
  .emo-grid-2,.emo-grid-2-3,.emo-grid-3-2{grid-template-columns:1fr;}
}
@media(max-width:768px){
  .emo-page{padding:16px;}
  .emo-stat-grid{grid-template-columns:repeat(2,1fr);}
}
</style>
@endsection

@section('content')
<div class="emo-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;width:22px;height:22px;vertical-align:-4px;margin-right:8px;color:var(--color-brand2);stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
        Emotion Analysis
      </h1>
      <p>Analisis emosi dari mention — joy, trust, fear, anger, surprise, sadness, disgust, anticipation</p>
    </div>
    <div class="last-updated" id="lastUpdatedBadge">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span>Last updated: <strong id="lastUpdatedText">—</strong></span>
    </div>
  </div>

  {{-- FILTER --}}
  <form id="filterForm" method="GET" action="{{ route('mk.x.emotion-analysis') }}" style="display:none;">
    <input type="hidden" name="project_id" value="{{ $project_id ?? '' }}">
    <input type="hidden" id="hSD" name="start_date" value="{{ $start_date ?? '' }}">
    <input type="hidden" id="hED" name="end_date"   value="{{ $end_date ?? '' }}">
  </form>
  <div class="filter-card">
    <div class="filter-content">
      <div class="filter-group">
        <span class="filter-label">Date Range</span>
        <button type="button" class="date-picker-trigger" id="emoDpTrigger">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span id="emoDpDisplay">Select date range</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
      </div>
      <button type="button" class="apply-btn" onclick="loadData()">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Apply
      </button>
    </div>
  </div>

  {{-- ═══════════ SECTION 1 — SUMMARY STATS ═══════════ --}}
  <div class="emo-section-header">
    <div class="emo-section-icon">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
    </div>
    <span class="emo-section-title">Emotion Summary</span>
    <div class="emo-section-line"></div>
  </div>

  <div class="emo-stat-grid" id="emoSummaryRow">
    @for($i=0;$i<5;$i++)
    <div class="emo-stat-card">
      <div class="emo-skel" style="width:70%;height:10px;border-radius:4px;margin-bottom:8px;"></div>
      <div class="emo-skel" style="width:55%;height:28px;border-radius:6px;margin-bottom:6px;"></div>
      <div class="emo-skel" style="width:80%;height:9px;border-radius:4px;"></div>
    </div>
    @endfor
  </div>

  {{-- ═══════════ SECTION 2 — OVERVIEW CHARTS ═══════════ --}}
  <div class="emo-section-header">
    <div class="emo-section-icon">
      <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    </div>
    <span class="emo-section-title">Emotion Distribution</span>
    <div class="emo-section-line"></div>
  </div>

  {{-- Bar Chart + Doughnut --}}
  <div class="emo-grid-3-2 emo-mb20">
    {{-- Bar Chart --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <div>
            <div class="emo-card-title">Emotions Overview</div>
            <div class="emo-card-subtitle">Volume per emosi — klik bar untuk lihat tweet</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <button class="emo-csv-btn" onclick="EmoCsv.copyOverview()">
            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy CSV
          </button>
          <span class="emo-badge" id="emoBarBadge">Loading…</span>
        </div>
      </div>
      <div class="emo-card-body">
        <div class="emo-ch-360" id="chBarWrap">
          <div id="chBar" style="width:100%;height:100%;"></div>
          <div class="emo-skel emo-skel-overlay" id="skBar"></div>
        </div>
      </div>
    </div>

    {{-- Doughnut / Radar --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon"><svg viewBox="0 0 24 24"><polygon points="12 2 2 7 2 17 12 22 22 17 22 7"/></svg></span>
          <div>
            <div class="emo-card-title">Share of Emotions</div>
            <div class="emo-card-subtitle">Distribusi persentase per emosi</div>
          </div>
        </div>
        <span class="emo-badge" id="emoSovBadge">SOV</span>
      </div>
      <div class="emo-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:360px;width:100%;">
          <div id="chSov" style="width:100%;height:100%;"></div>
          <div class="emo-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSov"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════ SECTION 3 — POSITIVE / NEGATIVE / NEUTRAL BREAKDOWN ═══════════ --}}
  <div class="emo-section-header">
    <div class="emo-section-icon">
      <svg viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
    </div>
    <span class="emo-section-title">Sentimen Bucket → Emosi Detail</span>
    <div class="emo-section-line"></div>
  </div>

  <div class="emo-grid-2 emo-mb20">
    {{-- Positive emotions --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon" style="background:#d1fae5;"><svg viewBox="0 0 24 24" style="stroke:#10b981;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>
          <div>
            <div class="emo-card-title">Positive Emotions</div>
            <div class="emo-card-subtitle">Joy · Trust · Anticipation</div>
          </div>
        </div>
        <span class="emo-badge" style="background:#d1fae5;color:#065f46;border-color:#6ee7b7;">Positive</span>
      </div>
      <div class="emo-card-body">
        <div class="emo-ch-260">
          <div id="chPos" style="width:100%;height:100%;"></div>
          <div class="emo-skel emo-skel-overlay" id="skPos"></div>
        </div>
      </div>
    </div>

    {{-- Negative emotions --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon" style="background:#fee2e2;"><svg viewBox="0 0 24 24" style="stroke:#ef4444;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>
          <div>
            <div class="emo-card-title">Negative Emotions</div>
            <div class="emo-card-subtitle">Anger · Fear · Sadness · Disgust</div>
          </div>
        </div>
        <span class="emo-badge" style="background:#fee2e2;color:#991b1b;border-color:#fca5a5;">Negative</span>
      </div>
      <div class="emo-card-body">
        <div class="emo-ch-260">
          <div id="chNeg" style="width:100%;height:100%;"></div>
          <div class="emo-skel emo-skel-overlay" id="skNeg"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Neutral + Radar --}}
  <div class="emo-grid-2 emo-mb20">
    {{-- Neutral emotions --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon" style="background:#cffafe;"><svg viewBox="0 0 24 24" style="stroke:#06b6d4;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
          <div>
            <div class="emo-card-title">Neutral Emotions</div>
            <div class="emo-card-subtitle">Surprise · Anticipation (neutral part)</div>
          </div>
        </div>
        <span class="emo-badge" style="background:#cffafe;color:#155e75;border-color:#67e8f9;">Neutral</span>
      </div>
      <div class="emo-card-body">
        <div class="emo-ch-260">
          <div id="chNeu" style="width:100%;height:100%;"></div>
          <div class="emo-skel emo-skel-overlay" id="skNeu"></div>
        </div>
      </div>
    </div>

    {{-- Emotion Radar --}}
    <div class="emo-card">
      <div class="emo-card-head">
        <div class="emo-card-head-left">
          <span class="emo-head-icon"><svg viewBox="0 0 24 24"><polygon points="12 2 2 7 2 17 12 22 22 17 22 7"/></svg></span>
          <div>
            <div class="emo-card-title">Emotion Radar</div>
            <div class="emo-card-subtitle">Peta distribusi 8 dimensi emosi</div>
          </div>
        </div>
        <span class="emo-badge" id="emoRadarBadge">8 Emotions</span>
      </div>
      <div class="emo-card-body" style="display:flex;align-items:center;justify-content:center;">
        <div style="position:relative;height:260px;width:100%;">
          <div id="chRadar" style="width:100%;height:100%;"></div>
          <div class="emo-skel" style="position:absolute;inset:0;border-radius:8px;" id="skRadar"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Breakdown per bucket (bar list) --}}
  <div class="emo-card emo-mb20">
    <div class="emo-card-head">
      <div class="emo-card-head-left">
        <span class="emo-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
        <div>
          <div class="emo-card-title">Breakdown Emosi per Sentimen Bucket</div>
          <div class="emo-card-subtitle">Positive / Negative / Neutral → 8 kategori emosi</div>
        </div>
      </div>
      <span class="emo-badge">All Emotions</span>
    </div>
    <div class="emo-card-body">
      <div id="emoBucketBreakdown" class="emo-media-list">
        @foreach(['Joy','Trust','Fear','Surprise','Sadness','Disgust','Anger','Anticipation'] as $e)
        <div class="emo-media-row">
          <div class="emo-media-icon">😐</div>
          <div class="emo-media-name">{{ $e }}</div>
          <div class="emo-media-bars">
            <div class="emo-media-bar-row"><div class="emo-skel" style="height:7px;width:100%;border-radius:4px;"></div></div>
          </div>
          <div class="emo-skel" style="height:18px;width:50px;border-radius:4px;margin-left:10px;"></div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ═══════════ SECTION 4 — TREND ═══════════ --}}
  <div class="emo-section-header">
    <div class="emo-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="emo-section-title">Emotion Trends</span>
    <div class="emo-section-line"></div>
  </div>

  <div class="emo-card emo-mb20">
    <div class="emo-card-head">
      <div class="emo-card-head-left">
        <span class="emo-head-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
        <div>
          <div class="emo-card-title">Trends of Posts by Emotion</div>
          <div class="emo-card-subtitle">Tren harian semua dimensi emosi</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <button class="emo-csv-btn" onclick="EmoCsv.copyTrend()">
          <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copy CSV
        </button>
        <span class="emo-badge" id="emoTrendBadge">Loading…</span>
      </div>
    </div>
    <div style="position:relative;height:360px;">
      <div id="chTrend" style="width:100%;height:100%;"></div>
      <div class="emo-skel emo-skel-overlay" id="skTrend"></div>
    </div>
    <div class="emo-legend" id="emoLegend" style="display:none;"></div>
  </div>

  {{-- ═══════════ SECTION 5 — TWEET TABLE ═══════════ --}}
  <div class="emo-table-section">
    <div class="emo-table-header">
      <div>
        <h3>Posts by Emotion</h3>
        <p class="emo-table-subtitle">Sorted by engagement — klik baris untuk detail tweet</p>
      </div>
      <div class="emo-table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="tableSearch" placeholder="Cari teks tweet atau author…" oninput="filterTable()" autocomplete="off">
        </div>
        <select id="emoFilter" class="emo-filter-select" onchange="filterTable()">
          <option value="">All Emotions</option>
          <option value="joy">Joy</option>
          <option value="trust">Trust</option>
          <option value="anticipation">Anticipation</option>
          <option value="anger">Anger</option>
          <option value="fear">Fear</option>
          <option value="sadness">Sadness</option>
          <option value="disgust">Disgust</option>
          <option value="surprise">Surprise</option>
        </select>
        <div class="actions-dropdown" id="actionsDropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsMenu()" type="button">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
            Actions
          </button>
          <div class="actions-dropdown-menu" id="actionsMenu">
            <div class="actions-dropdown-item" onclick="exportCSV()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Export CSV
            </div>
            <div class="actions-dropdown-item" onclick="refreshData()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
              Refresh Data
            </div>
            <div class="actions-dropdown-divider"></div>
            <div class="actions-dropdown-item" onclick="window.print()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
              Print
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="overflow-x:auto;position:relative;" id="tableWrap">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:48px;">#</th>
            <th>TEXT</th>
            <th style="width:140px;">EMOTION</th>
            <th style="width:120px;">AUTHOR</th>
            <th style="width:100px;">DATE</th>
            <th style="width:80px;">LIKES</th>
            <th style="width:80px;">RETWEETS</th>
          </tr>
        </thead>
        <tbody id="emoTableBody">
          @for($i=0;$i<8;$i++)
          <tr>
            <td><div class="emo-skel" style="width:20px;height:14px;border-radius:4px;"></div></td>
            <td><div class="emo-skel" style="width:90%;height:13px;border-radius:4px;"></div></td>
            <td><div class="emo-skel" style="width:80px;height:22px;border-radius:10px;"></div></td>
            <td><div class="emo-skel" style="width:70%;height:13px;border-radius:4px;"></div></td>
            <td><div class="emo-skel" style="width:70%;height:13px;border-radius:4px;"></div></td>
            <td><div class="emo-skel" style="width:40px;height:13px;border-radius:4px;"></div></td>
            <td><div class="emo-skel" style="width:40px;height:13px;border-radius:4px;"></div></td>
          </tr>
          @endfor
        </tbody>
      </table>
    </div>
    <div class="pagination-row" id="emoPagination" style="display:none;">
      <span class="pagination-info" id="emoPageInfo"></span>
      <div class="pagination-btns" id="emoPageBtns"></div>
    </div>
  </div>

</div>{{-- /emo-page --}}

{{-- DATE PICKER MODAL --}}
<div class="dp-modal" id="emoDpModal">
  <div class="dp-overlay" onclick="EmoDp.close()"></div>
  <div class="dp-content">
    <div class="dp-top">
      <h3>Select Date Range</h3>
      <button class="dp-close" onclick="EmoDp.close()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="dp-body">
      <div class="dp-sidebar">
        <button class="date-preset" data-p="today">Today</button>
        <button class="date-preset" data-p="yesterday">Yesterday</button>
        <button class="date-preset active" data-p="last7">Last 7 days</button>
        <button class="date-preset" data-p="last30">Last 30 days</button>
        <button class="date-preset" data-p="thismonth">This month</button>
        <button class="date-preset" data-p="lastmonth">Last month</button>
        <button class="date-preset" data-p="custom">Custom range</button>
      </div>
      <div class="dp-main">
        <div class="dp-range-display">
          <span id="emoDpRangeFrom">—</span>
          <span class="dp-range-sep">→</span>
          <span id="emoDpRangeTo">—</span>
        </div>
        <div class="dp-calendars">
          <div class="dp-cal" id="emoDpCal1"></div>
          <div class="dp-cal" id="emoDpCal2"></div>
        </div>
      </div>
    </div>
    <div class="dp-actions">
      <button class="dp-cancel-btn" onclick="EmoDp.close()">Cancel</button>
      <button class="dp-apply-btn" onclick="EmoDp.apply()">Apply Range</button>
    </div>
  </div>
</div>

{{-- TWEET DETAIL MODAL --}}
<div class="tweet-modal" id="tweetDetailModal">
  <div class="tm-overlay" onclick="TweetModal.close()"></div>
  <div class="tm-content">
    <div class="tm-topbar">
      <div class="tm-topbar-title">Tweet Detail</div>
      <button class="tm-close" onclick="TweetModal.close()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="tm-body" id="tweetModalBody"></div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const EmoCfg = {
  pid: '{{ $project_id ?? "" }}',
  sd:  '{{ $start_date ?? "" }}',
  ed:  '{{ $end_date ?? "" }}',
};

/* ══════════════════════════════════════════════════════
   EMOTION META
══════════════════════════════════════════════════════ */
const EMO_META = {
  joy:          { color:'#f59e0b', bg:'#fef3c7', label:'Joy',          bucket:'positive' },
  trust:        { color:'#10b981', bg:'#d1fae5', label:'Trust',        bucket:'positive' },
  anticipation: { color:'#f97316', bg:'#ffedd5', label:'Anticipation', bucket:'positive' },
  anger:        { color:'#ef4444', bg:'#fee2e2', label:'Anger',        bucket:'negative' },
  fear:         { color:'#8b5cf6', bg:'#ede9fe', label:'Fear',         bucket:'negative' },
  sadness:      { color:'#6366f1', bg:'#e0e7ff', label:'Sadness',      bucket:'negative' },
  disgust:      { color:'#a855f7', bg:'#f3e8ff', label:'Disgust',      bucket:'negative' },
  surprise:     { color:'#06b6d4', bg:'#cffafe', label:'Surprise',     bucket:'neutral'  },
};

/**
 * Proporsi emosi per sentimen bucket.
 * Ini menentukan bagaimana post dari tiap bucket
 * didistribusikan ke 8 emosi.
 */
const EMO_MAP = {
  positive: { joy:0.50, trust:0.30, anticipation:0.20 },
  negative: { anger:0.35, fear:0.25, sadness:0.25, disgust:0.15 },
  neutral:  { surprise:0.55, anticipation:0.45 },
};

// Urutan tampil
const EMO_ORDER = ['joy','trust','anticipation','anger','fear','sadness','disgust','surprise'];

/* ══════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════ */
const numFmt = n => (!n && n!==0)?'0':new Intl.NumberFormat('en-US').format(n);
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'K':String(n); };
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';
function _esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function _fmtDate(d){ return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
function _fmtDisp(d){ const M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; return `${M[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${d.getFullYear()}`; }
function _linkify(t){
  return _esc(t)
    .replace(/(https?:\/\/[^\s&]+)/g,'<a href="$1" target="_blank" rel="noopener">$1</a>')
    .replace(/@(\w+)/g,'<a href="https://twitter.com/$1" target="_blank">@$1</a>')
    .replace(/#(\w+)/g,'<a href="https://twitter.com/hashtag/$1" target="_blank">#$1</a>');
}

const emptyHtml = msg =>
  `<div class="emo-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="emo-empty-text">${msg}</span></div>`;

/* ══════════════════════════════════════════════════════
   ECHARTS REGISTRY
══════════════════════════════════════════════════════ */
const EmoCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer:'canvas' });
    this._i[id] = c;
    return c;
  },
};
window.addEventListener('resize', () => {
  Object.values(EmoCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{color:'#fff',fontFamily:"'DM Sans',sans-serif",fontSize:12},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

/* ══════════════════════════════════════════════════════
   STATE
══════════════════════════════════════════════════════ */
let allData      = {};
let filteredTweets = [];
let currentPage  = 1;
const PER_PAGE   = 15;
let hiddenEmos   = new Set();
let trendInst    = null;

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const EmoDp = (() => {
  let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init(){
    const si=document.getElementById('hSD'),ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):(()=>{const d=new Date();d.setDate(d.getDate()-6);return d;})();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('emoDpTrigger').addEventListener('click',open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&document.getElementById('emoDpModal').classList.contains('show'))close();});
  }
  function open(){const m=document.getElementById('emoDpModal');m.style.display='flex';requestAnimationFrame(()=>m.classList.add('show'));render();}
  function close(){const m=document.getElementById('emoDpModal');m.classList.remove('show');setTimeout(()=>{m.style.display='none';},250);}
  function apply(){
    document.getElementById('hSD').value=_fmtDate(ds);
    document.getElementById('hED').value=_fmtDate(de);
    document.getElementById('emoDpDisplay').textContent=_fmtDisp(ds)+' – '+_fmtDisp(de);
    close();loadData();
  }
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
  function render(){renderCal('emoDpCal1',m1);renderCal('emoDpCal2',m2);updDisp();}
  function updDisp(){
    if(ds)document.getElementById('emoDpRangeFrom').textContent=_fmtDisp(ds);
    if(de)document.getElementById('emoDpRangeTo').textContent=_fmtDisp(de);
    document.getElementById('emoDpDisplay').textContent=(ds&&de)?_fmtDisp(ds)+' – '+_fmtDisp(de):'Select date range';
  }
  function renderCal(id,month){
    const c=document.getElementById(id);if(!c)return;
    const y=month.getFullYear(),mo=month.getMonth();
    const first=new Date(y,mo,1).getDay();
    const days=new Date(y,mo+1,0).getDate();
    const today=new Date();today.setHours(0,0,0,0);
    let h=`<div class="dp-cal-nav">
      <button class="dp-cal-nav-btn" onclick="EmoDp._nav(${id==='emoDpCal1'?-1:1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">${id==='emoDpCal1'?'<polyline points="15 18 9 12 15 6"/>':'<polyline points="9 18 15 12 9 6"/>'}</svg></button>
      <div class="dp-cal-month">${MN[mo]} ${y}</div>
      <button class="dp-cal-nav-btn" onclick="EmoDp._nav(${id==='emoDpCal1'?1:-1})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">${id==='emoDpCal1'?'<polyline points="9 18 15 12 9 6"/>':'<polyline points="15 18 9 12 15 6"/>'}</svg></button>
    </div><div class="dp-cal-grid">`;
    WD.forEach(w=>{h+=`<div class="dp-cal-wd">${w}</div>`;});
    for(let i=0;i<first;i++)h+=`<div></div>`;
    for(let d=1;d<=days;d++){
      const dt=new Date(y,mo,d);dt.setHours(0,0,0,0);
      let cls='dp-day';
      if(ds&&de&&dt>ds&&dt<de)cls+=' dp-day--range';
      if((ds&&dt.getTime()===ds.getTime())||(de&&dt.getTime()===de.getTime()))cls+=' dp-day--selected';
      if(dt.getTime()===today.getTime())cls+=' dp-day--today';
      h+=`<div class="${cls}" onclick="EmoDp._pick(${y},${mo},${d})">${d}</div>`;
    }
    h+='</div>';c.innerHTML=h;
  }
  function _pick(y,mo,d){
    const dt=new Date(y,mo,d);dt.setHours(0,0,0,0);
    if(pickStart||dt<ds){ds=dt;de=null;pickStart=false;}
    else{de=dt;pickStart=true;if(de<ds){const t=ds;ds=de;de=t;}}
    render();
  }
  function _nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
  return {init,open,close,apply,_pick,_nav};
})();

/* ══════════════════════════════════════════════════════
   LOAD DATA
══════════════════════════════════════════════════════ */
async function loadData() {
  const sd = document.getElementById('hSD').value;
  const ed = document.getElementById('hED').value;
  showLoadingState();
  try {
    const url = `/mk/api/x/emotion-analysis?project_id=${EmoCfg.pid}&start_date=${sd}&end_date=${ed}`;
    const r   = await fetch(url);
    if (!r.ok) throw new Error('API error ' + r.status);
    const j   = await r.json();
    if (!j.success) throw new Error(j.message || 'Failed');
    allData = j.data;
    renderAll();
  } catch(e) {
    console.error('Emotion API:', e);
    renderError(e.message);
  }
}

function showLoadingState(){
  document.getElementById('emoBarBadge').textContent = 'Loading…';
  document.getElementById('emoTrendBadge').textContent = '—';
  document.getElementById('emoSovBadge').textContent = 'SOV';
}

function renderError(msg) {
  document.getElementById('emoBarBadge').textContent = 'Error';
  document.getElementById('emoTableBody').innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#ef4444;font-size:13px;">${_esc(msg)}</td></tr>`;
}

function renderAll() {
  renderSummary();
  renderBarChart();
  renderSovDoughnut();
  renderPosBucketChart();
  renderNegBucketChart();
  renderNeuBucketChart();
  renderRadar();
  renderBucketBreakdown();
  renderTrend();
  renderTable();
  updateLastUpdated();
}

/* ══════════════════════════════════════════════════════
   RENDER SUMMARY CARDS
══════════════════════════════════════════════════════ */
function renderSummary() {
  const s    = allData.summary || {};
  const emos = allData.emotions || {};
  const total = Object.values(emos).reduce((a,v)=>a+(v.count||0), 0);
  const dominant = Object.entries(emos).sort((a,b)=>b[1].count-a[1].count)[0];
  const domName  = dominant?.[0] || 'unknown';
  const domMeta  = EMO_META[domName] || { color:'#94a3b8', bg:'#f1f5f9', label:domName };

  const cards = [
    {
      label:'Total Posts', value: numFmt(s.total_posts || total),
      sub: 'semua emosi', accent:'linear-gradient(90deg,var(--color-brand),var(--color-brand-dark))',
    },
    {
      label:'Dominant Emotion', value: domMeta.label,
      sub: `${dominant?.[1]?.pct || 0}% dari seluruh post`,
      accent:`linear-gradient(90deg,${domMeta.color},${domMeta.color}99)`,
      valueColor: domMeta.color, small: true,
    },
    {
      label:'Positive Ratio', value: (s.positive_pct||0)+'%',
      sub: 'Joy + Trust + Anticipation',
      accent:'linear-gradient(90deg,#10b981,#059669)', valueColor:'#10b981',
    },
    {
      label:'Negative Ratio', value: (s.negative_pct||0)+'%',
      sub: 'Anger + Fear + Sadness + Disgust',
      accent:'linear-gradient(90deg,#ef4444,#dc2626)', valueColor:'#ef4444',
    },
    {
      label:'Period', value: s.days_count ? s.days_count+'d' : '—',
      sub: (() => {
        const _fB = d => { if(!d) return ''; const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][dt.getMonth()]} ${dt.getFullYear()}`; };
        const pSd = document.getElementById('hSD').value;
        const pEd = document.getElementById('hED').value;
        const from = pSd ? _fB(pSd) : (s.start_date||'');
        const to   = pEd ? _fB(pEd) : (s.end_date||'');
        return from + (to ? ' – '+to : '');
      })(),
      accent:'linear-gradient(90deg,#7c3aed,#5b21b6)', valueColor:'#7c3aed',
    },
  ];

  document.getElementById('emoSummaryRow').innerHTML = cards.map(c=>`
    <div class="emo-stat-card" style="--accent-color:${c.accent||''};">
      <div class="emo-stat-label">${_esc(c.label)}</div>
      <div class="emo-stat-value" style="color:${c.valueColor||'var(--text-primary)'};${c.small?'font-size:18px;':''}">${_esc(c.value)}</div>
      <div class="emo-stat-sub">${_esc(c.sub)}</div>
    </div>`).join('');

  // Attach click handlers on stat cards
  document.querySelectorAll('.emo-stat-card').forEach((card,i) => {
    if (i===2) { // Positive Ratio
      card.style.cursor='pointer';
      card.title='Klik untuk lihat emosi positif';
      card.addEventListener('click', () => filterByBucket('positive'));
    }
    if (i===3) { // Negative Ratio
      card.style.cursor='pointer';
      card.title='Klik untuk lihat emosi negatif';
      card.addEventListener('click', () => filterByBucket('negative'));
    }
  });
}

function filterByBucket(bucket) {
  const bucketToEmo = {
    positive: 'joy',   // set filter to first emotion of bucket
    negative: 'anger',
    neutral:  'surprise',
  };
  const select = document.getElementById('emoFilter');
  if (select) {
    select.value = bucketToEmo[bucket] || '';
    filterTable();
    document.getElementById('emoTableBody')?.closest('.emo-table-section')?.scrollIntoView({behavior:'smooth',block:'start'});
  }
}

/* ══════════════════════════════════════════════════════
   RENDER HORIZONTAL BAR CHART
══════════════════════════════════════════════════════ */
function renderBarChart() {
  hideSk('skBar');
  const emos  = allData.emotions || {};
  const total = Object.values(emos).reduce((a,v)=>a+(v.count||0),0);

  const sorted = EMO_ORDER
    .map(k=>({ key:k, count:emos[k]?.count||0, meta:EMO_META[k] }))
    .sort((a,b)=>a.count-b.count);

  const dominant = [...sorted].sort((a,b)=>b.count-a.count)[0];
  if (dominant) {
    const dm = dominant.meta;
    document.getElementById('emoBarBadge').innerHTML =
      `<span style="background:${dm.bg};color:${dm.color};padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;">${dm.label} dominant</span>`;
  }

  if (!sorted.some(e=>e.count>0)) {
    document.getElementById('chBar').innerHTML = emptyHtml('Tidak ada data emosi');
    return;
  }

  const chart = EmoCharts.make('chBar');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(22,160,133,.06)'}},
      formatter: params => {
        const p    = params[0];
        const item = sorted.find(e=>e.meta.label===p.name);
        const pc   = total>0?Math.round((item.count/total)*100):0;
        const bucketLabel = { positive:'Positive', negative:'Negative', neutral:'Neutral' }[item.meta.bucket||'neutral'] || '';
        return `<div style="font-family:'DM Sans',sans-serif;padding:2px 4px;">
          <div style="font-weight:700;color:#fff;margin-bottom:4px;font-size:13px;">${p.name}</div>
          <div style="color:#94a3b8;font-size:11px;margin-bottom:6px;">${bucketLabel}</div>
          <div style="display:flex;justify-content:space-between;gap:16px;">
            <span style="color:#94a3b8;">Posts</span>
            <strong style="color:${item.meta.color};">${numFmt(item.count)} (${pc}%)</strong>
          </div>
        </div>`;
      }
    },
    grid:{top:8,right:90,bottom:8,left:100,containLabel:false},
    xAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontSize:11,color:'#94a3b8',fontFamily:"'DM Sans',sans-serif",formatter:v=>numK(v)},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
    },
    yAxis:{
      type:'category', data:sorted.map(e=>e.meta.label),
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontSize:12.5,color:'#374151',fontFamily:"'DM Sans',sans-serif",fontWeight:'600'},
    },
    series:[{
      type:'bar', barMaxWidth:32,
      data: sorted.map(e=>({
        value:e.count,
        itemStyle:{
          color:{ type:'linear',x:0,y:0,x2:1,y2:0,
            colorStops:[{offset:0,color:e.meta.color+'cc'},{offset:1,color:e.meta.color}]},
          borderRadius:[0,4,4,0],
        },
      })),
      label:{
        show:true, position:'right',
        fontFamily:"'DM Sans',sans-serif",fontSize:11,color:'#64748b',fontWeight:'700',
        formatter: p => {
          const item = sorted[p.dataIndex];
          const pc   = total>0?Math.round((item.count/total)*100):0;
          return `${numFmt(p.value)}  (${pc}%)`;
        }
      },
      emphasis:{itemStyle:{opacity:.85}},
    }],
  });

  // Click → filter table by that emotion
  chart.off('click');
  chart.on('click', params => {
    const item = sorted.find(e=>e.meta.label===params.name);
    if (item) {
      const select = document.getElementById('emoFilter');
      if (select) { select.value = item.key; filterTable(); }
      document.getElementById('emoTableBody')?.closest('.emo-table-section')?.scrollIntoView({behavior:'smooth',block:'start'});
    }
  });
  chart.on('mouseover', ()=>{ chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ══════════════════════════════════════════════════════
   RENDER SOV DOUGHNUT
══════════════════════════════════════════════════════ */
function renderSovDoughnut() {
  hideSk('skSov');
  const emos  = allData.emotions || {};
  const total = Object.values(emos).reduce((a,v)=>a+(v.count||0),0);
  if (!total) { document.getElementById('chSov').innerHTML = emptyHtml('No data'); return; }

  const data = EMO_ORDER.map(k=>({ name:EMO_META[k].label, value:emos[k]?.count||0, itemStyle:{color:EMO_META[k].color} }));

  const chart = EmoCharts.make('chSov');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:800,
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pc = total>0?(p.value/total*100).toFixed(1):0;
        return `<div style="font-family:'DM Sans',sans-serif;">
          <div style="font-weight:700;margin-bottom:5px;">${p.name}</div>
          <div style="display:flex;justify-content:space-between;gap:16px;">
            <span style="color:#94a3b8;">Posts</span><strong>${numFmt(p.value)}</strong>
          </div>
          <div style="display:flex;justify-content:space-between;gap:16px;margin-top:2px;">
            <span style="color:#94a3b8;">Share</span><strong style="color:#34d399;">${pc}%</strong>
          </div></div>`;
      }
    },
    legend:{show:false},
    series:[{
      type:'pie', radius:['42%','64%'], center:['50%','50%'],
      avoidLabelOverlap:true, minAngle:5,
      itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
      label:{
        show:true, alignTo:'edge', edgeDistance:8, lineHeight:17,
        fontFamily:"'DM Sans',sans-serif", fontSize:11, color:'#374151',
        formatter: p => {
          const pc = total>0?(p.value/total*100):0;
          if(pc<3) return '';
          return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
        },
        rich:{
          name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:17},
          pct:{fontWeight:'700',fontSize:10,color:'#16a085',lineHeight:15,backgroundColor:'#e8f8f4',borderRadius:4,padding:[1,5]},
        }
      },
      labelLine:{show:true,length:12,length2:14,smooth:.4,lineStyle:{color:'#c4cdd8',width:1.2}},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'}},
      data,
    }],
    graphic:[
      {type:'text',left:'center',top:'43%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 22px 'DM Sans',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'52%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px 'DM Sans',sans-serif",textAlign:'center',letterSpacing:2}},
    ]
  });
  document.getElementById('emoSovBadge').textContent = '8 Emotions';
}

/* ══════════════════════════════════════════════════════
   RENDER POSITIVE BUCKET CHART (Joy, Trust, Anticipation)
══════════════════════════════════════════════════════ */
function renderPosBucketChart() {
  hideSk('skPos');
  const emos = allData.emotions || {};
  const keys = ['joy','trust','anticipation'];
  const data = keys.map(k=>({ name:EMO_META[k].label, value:emos[k]?.count||0, itemStyle:{color:EMO_META[k].color} }));
  renderBucketPie('chPos', data, '#10b981');
}

function renderNegBucketChart() {
  hideSk('skNeg');
  const emos = allData.emotions || {};
  const keys = ['anger','fear','sadness','disgust'];
  const data = keys.map(k=>({ name:EMO_META[k].label, value:emos[k]?.count||0, itemStyle:{color:EMO_META[k].color} }));
  renderBucketPie('chNeg', data, '#ef4444');
}

function renderNeuBucketChart() {
  hideSk('skNeu');
  const emos = allData.emotions || {};
  const keys = ['surprise'];
  // Anticipation juga masuk neutral (sebagian)
  const data = [
    { name:'Surprise', value:emos['surprise']?.count||0, itemStyle:{color:'#06b6d4'} },
    { name:'Anticipation (neutral)', value: Math.round((emos['anticipation']?.count||0)*0.5), itemStyle:{color:'#f97316'} },
  ];
  renderBucketPie('chNeu', data, '#06b6d4');
}

function renderBucketPie(domId, data, accentColor) {
  const total = data.reduce((a,v)=>a+(v.value||0),0);
  if (!total) { document.getElementById(domId).innerHTML = emptyHtml('Tidak ada data'); return; }

  const chart = EmoCharts.make(domId);
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:800,
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pc = total>0?(p.value/total*100).toFixed(1):0;
        return `<div style="font-family:'DM Sans',sans-serif;">
          <div style="font-weight:700;margin-bottom:4px;">${p.name}</div>
          <div style="display:flex;justify-content:space-between;gap:16px;">
            <span style="color:#94a3b8;">Posts</span><strong>${numFmt(p.value)}</strong>
          </div>
          <div style="display:flex;justify-content:space-between;gap:16px;margin-top:2px;">
            <span style="color:#94a3b8;">Share</span><strong style="color:${accentColor};">${pc}%</strong>
          </div></div>`;
      }
    },
    legend:{
      bottom:0, icon:'circle', itemWidth:10, itemHeight:10, itemGap:16,
      textStyle:{fontFamily:"'DM Sans',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
    },
    series:[{
      type:'pie', radius:['38%','62%'], center:['50%','46%'],
      avoidLabelOverlap:true, minAngle:8,
      itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
      label:{
        show:true, alignTo:'edge', edgeDistance:5,
        fontFamily:"'DM Sans',sans-serif", fontSize:10, color:'#374151',
        formatter: p => {
          const pc = total>0?(p.value/total*100):0;
          if(pc<5) return '';
          return `{pct|${pc.toFixed(0)}%}`;
        },
        rich:{ pct:{fontWeight:'800',fontSize:10,lineHeight:14} }
      },
      labelLine:{show:true,length:8,length2:10,smooth:.4,lineStyle:{color:'#c4cdd8',width:1}},
      emphasis:{scale:true,scaleSize:4},
      data,
    }],
    graphic:[
      {type:'text',left:'center',top:'40%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 18px 'DM Sans',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'49%',z:100,style:{text:'POSTS',fill:'#94a3b8',font:"600 8px 'DM Sans',sans-serif",textAlign:'center',letterSpacing:2}},
    ]
  });
}

/* ══════════════════════════════════════════════════════
   RENDER RADAR
══════════════════════════════════════════════════════ */
function renderRadar() {
  hideSk('skRadar');
  const emos   = allData.emotions || {};
  const maxVal = Math.max(...EMO_ORDER.map(k=>emos[k]?.count||0), 1);

  const chart = EmoCharts.make('chRadar');
  if (!chart) return;

  chart.setOption({
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        if(!p.value) return '';
        return '<div style="font-family:\'DM Sans\',sans-serif;">' +
          EMO_ORDER.map((k,i)=>{
            const m=EMO_META[k];
            return `<div style="display:flex;align-items:center;gap:6px;margin:2px 0;">
              <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${m.color};flex-shrink:0;"></span>
              <span style="font-size:12px;color:#94a3b8;">${m.label}</span>
              <strong style="margin-left:auto;padding-left:12px;font-size:12px;">${numFmt(p.value[i]||0)}</strong></div>`;
          }).join('') + '</div>';
      }
    },
    radar:{
      indicator: EMO_ORDER.map(k=>({ name:EMO_META[k].label, max:maxVal })),
      center:['50%','50%'], radius:'65%', nameGap:8,
      name:{textStyle:{fontSize:11,fontFamily:"'DM Sans',sans-serif",color:'#64748b',fontWeight:'600'}},
      splitLine:{lineStyle:{color:'#e2e8f0',width:1}},
      splitArea:{areaStyle:{color:['rgba(248,250,252,.8)','rgba(241,245,249,.4)']}},
      axisLine:{lineStyle:{color:'#e2e8f0'}},
    },
    series:[{
      type:'radar',
      data:[{
        value: EMO_ORDER.map(k=>emos[k]?.count||0),
        areaStyle:{color:'rgba(22,160,133,.12)'},
        lineStyle:{color:'rgba(22,160,133,.7)',width:2.5},
        itemStyle:{color:p=>EMO_META[EMO_ORDER[p.dataIndex]]?.color||'#16a085'},
        symbol:'circle', symbolSize:8,
      }],
    }],
  });
  document.getElementById('emoRadarBadge').textContent = '8 Emotions';
}

/* ══════════════════════════════════════════════════════
   RENDER BUCKET BREAKDOWN LIST
══════════════════════════════════════════════════════ */
function renderBucketBreakdown() {
  const emos = allData.emotions || {};
  const maxCount = Math.max(...EMO_ORDER.map(k=>emos[k]?.count||0), 1);

  document.getElementById('emoBucketBreakdown').innerHTML = EMO_ORDER.map(k => {
    const m     = EMO_META[k];
    const count = emos[k]?.count || 0;
    const widthPct = maxCount>0?((count/maxCount)*100).toFixed(1):0;
    const totalAll = Object.values(emos).reduce((a,v)=>a+(v.count||0),0);
    const sharePct = totalAll>0?((count/totalAll)*100).toFixed(1):0;
    const bucketBadge = {
      positive:'<span style="background:#d1fae5;color:#065f46;font-size:9px;font-weight:700;padding:1px 7px;border-radius:10px;">Positive</span>',
      negative:'<span style="background:#fee2e2;color:#991b1b;font-size:9px;font-weight:700;padding:1px 7px;border-radius:10px;">Negative</span>',
      neutral: '<span style="background:#cffafe;color:#155e75;font-size:9px;font-weight:700;padding:1px 7px;border-radius:10px;">Neutral</span>',
    }[m.bucket||'neutral'] || '';

    return `<div class="emo-media-row" onclick="(function(){const s=document.getElementById('emoFilter');if(s){s.value='${k}';filterTable();}document.getElementById('emoTableBody')?.closest('.emo-table-section')?.scrollIntoView({behavior:'smooth',block:'start'})})()">
      <div class="emo-media-icon" style="background:${m.bg};"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${m.color};"></span></div>
      <div class="emo-media-name">${m.label} ${bucketBadge}</div>
      <div class="emo-media-bars">
        <div class="emo-media-bar-row">
          <div class="emo-media-bar-track">
            <div class="emo-media-bar-fill" style="width:${widthPct}%;background:${m.color};"></div>
          </div>
          <div class="emo-media-bar-val" style="color:${m.color};">${numK(count)}</div>
        </div>
      </div>
      <div class="emo-media-total">${sharePct}%</div>
    </div>`;
  }).join('');
}

/* ══════════════════════════════════════════════════════
   RENDER TREND LINE CHART
══════════════════════════════════════════════════════ */
function renderTrend() {
  hideSk('skTrend');
  const trend = allData.trend || [];
  if (!trend.length) {
    document.getElementById('chTrend').innerHTML = emptyHtml('Data trend tidak tersedia untuk periode ini');
    document.getElementById('emoTrendBadge').textContent = 'No Data';
    return;
  }

  const dates   = [...new Set(trend.map(t=>t.date))].sort();
  const xLabels = dates.map(d => {
    const dt=new Date(d+'T00:00:00');
    return `${dt.getDate()} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][dt.getMonth()]}`;
  });

  const series = EMO_ORDER.map(emo => {
    const m   = EMO_META[emo];
    const vals = dates.map(d => { const r=trend.find(t=>t.date===d&&t.emotion===emo); return r?r.count:0; });
    return {
      name:m.label, type:'line', data:vals, smooth:.4,
      symbol:'circle', symbolSize:dates.length<=30?5:0, showSymbol:dates.length<=30,
      itemStyle:{color:m.color,borderColor:'#fff',borderWidth:2},
      lineStyle:{color:m.color,width:2.5},
      areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:m.color+'22'},{offset:1,color:m.color+'03'}]}},
      emphasis:{focus:'series',lineStyle:{width:3.5}},
    };
  });

  trendInst = EmoCharts.make('chTrend');
  if (!trendInst) return;

  trendInst.setOption({
    animation:true, animationDuration:900, animationEasing:'cubicInOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter: params => {
        const di   = params[0]?.dataIndex??0;
        const date = dates[di]||'';
        const fullDt = date?new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}):'';
        const sorted = [...params].sort((a,b)=>b.value-a.value);
        const rows = sorted.map(p => {
          const key = EMO_ORDER.find(k=>EMO_META[k].label===p.seriesName);
          const m   = EMO_META[key]||{color:p.color};
          return `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${m.color};"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <strong style="font-size:12px;">${numFmt(p.value)}</strong></div>`;
        }).join('');
        return `<div style="font-family:'DM Sans',sans-serif;padding:2px;">
          <div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${fullDt||date}</div>
          ${rows}</div>`;
      }
    },
    legend:{show:false},
    grid:{top:20,left:60,right:20,bottom:50,containLabel:false},
    xAxis:[{
      type:'category', data:xLabels, boundaryGap:false,
      axisLine:{lineStyle:{color:'#e2e8f0'}}, axisTick:{show:false},
      axisLabel:{fontFamily:"'DM Sans',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
    }],
    yAxis:[{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},
      axisLabel:{fontFamily:"'DM Sans',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>numK(v)},
    }],
    series,
  });

  // Trend badge — gunakan date picker, fallback ke data range
  const fmtB = d=>{const dt=new Date(d+'T00:00:00');return `${dt.getDate()} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][dt.getMonth()]}`;};
  const pickerSd = document.getElementById('hSD').value;
  const pickerEd = document.getElementById('hED').value;
  const badgeFrom = pickerSd ? fmtB(pickerSd) : fmtB(dates[0]);
  const badgeTo   = pickerEd ? fmtB(pickerEd) : fmtB(dates[dates.length-1]);
  document.getElementById('emoTrendBadge').textContent = `${badgeFrom} – ${badgeTo}`;

  // Legend
  const legendEl = document.getElementById('emoLegend');
  legendEl.style.display = 'flex';
  legendEl.innerHTML = EMO_ORDER.map(k=>{
    const m = EMO_META[k];
    return `<div class="emo-leg-item" data-emo="${k}" onclick="toggleTrend('${k}')">
      <div class="emo-leg-dot" style="background:${m.color};"></div>
      ${m.label}
    </div>`;
  }).join('');
}

function toggleTrend(emo) {
  if (!trendInst) return;
  if (hiddenEmos.has(emo)) {
    hiddenEmos.delete(emo);
    trendInst.dispatchAction({type:'legendSelect',name:EMO_META[emo].label});
  } else {
    hiddenEmos.add(emo);
    trendInst.dispatchAction({type:'legendUnSelect',name:EMO_META[emo].label});
  }
  document.querySelectorAll(`.emo-leg-item[data-emo="${emo}"]`).forEach(el=>{
    el.classList.toggle('hidden',hiddenEmos.has(emo));
  });
}

/* ══════════════════════════════════════════════════════
   RENDER TABLE
══════════════════════════════════════════════════════ */
function renderTable() {
  filteredTweets = allData.tweets || [];
  currentPage    = 1;
  _renderTablePage();
}

function filterTable() {
  const q    = document.getElementById('tableSearch').value.toLowerCase().trim();
  const emoF = document.getElementById('emoFilter').value.toLowerCase();
  const tweets = allData.tweets || [];

  filteredTweets = tweets.filter(t => {
    const textMatch = !q ||
      (t.text||'').toLowerCase().includes(q) ||
      (t.author||'').toLowerCase().includes(q);
    const emoMatch  = !emoF || (t.emotion||'').toLowerCase() === emoF;
    return textMatch && emoMatch;
  });

  currentPage = 1;
  _renderTablePage();
}

function _renderTablePage() {
  const si = (currentPage-1)*PER_PAGE;
  const cd = filteredTweets.slice(si, si+PER_PAGE);
  const tbody = document.getElementById('emoTableBody');

  if (!cd.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);font-size:13px;">No tweets found</td></tr>`;
    document.getElementById('emoPagination').style.display='none';
    return;
  }

  tbody.innerHTML = cd.map((t,i)=>{
    const rank = si+i+1;
    const emo  = (t.emotion||'unknown').toLowerCase();
    const meta = EMO_META[emo]||{color:'#94a3b8',bg:'#f1f5f9',label:emo};
    const txt  = (t.text||'').substring(0,120)+((t.text||'').length>120?'…':'');
    const rawTs= t.timestamp||t.created_at||'';
    let dtStr  = '—';
    if(rawTs){const d=new Date(rawTs);if(!isNaN(d))dtStr=d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}
    const tjson = JSON.stringify(t).replace(/'/g,"\\'").replace(/"/g,'&quot;');
    return `<tr onclick="TweetModal.open('${tjson}')">
      <td style="color:var(--text-muted);font-weight:700;font-family:var(--font-mono);font-size:12px;">${rank}</td>
      <td class="tweet-text-cell">${_esc(txt)}</td>
      <td><span class="emo-badge-pill" style="background:${meta.bg};color:${meta.color};">
        <span class="emo-badge-dot" style="background:${meta.color};"></span>
        ${_esc(meta.label)}
      </span></td>
      <td style="color:var(--text-secondary);font-size:12.5px;">@${_esc(t.author||t.screen_name||'')}</td>
      <td style="color:var(--text-muted);font-size:12px;white-space:nowrap;">${dtStr}</td>
      <td style="color:#e91e8c;font-weight:700;">${numFmt(t.likes||t.num_likes||0)}</td>
      <td style="color:#00ba7c;font-weight:700;">${numFmt(t.retweets||t.num_shares||0)}</td>
    </tr>`;
  }).join('');

  updatePagination();
}

function updatePagination() {
  const total = filteredTweets.length;
  const pages = Math.ceil(total/PER_PAGE);
  const si    = (currentPage-1)*PER_PAGE;
  const ei    = Math.min(si+PER_PAGE, total);
  const pag   = document.getElementById('emoPagination');
  if (total <= PER_PAGE) { pag.style.display='none'; return; }
  pag.style.display = 'flex';
  document.getElementById('emoPageInfo').innerHTML = `Showing <strong>${si+1}–${ei}</strong> of <strong>${numFmt(total)}</strong> tweets`;
  const getRange = (cur,tot) => {
    if(tot<=7) return Array.from({length:tot},(_,i)=>i+1);
    if(cur<=4)     return [1,2,3,4,5,'…',tot];
    if(cur>=tot-3) return [1,'…',tot-4,tot-3,tot-2,tot-1,tot];
    return [1,'…',cur-1,cur,cur+1,'…',tot];
  };
  const btnHtml = (label,page,active=false,disabled=false) => {
    const svg = label==='‹'?'<svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>'
               :label==='›'?'<svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>':label;
    return `<button class="page-btn${active?' active':''}" onclick="goPage(${page})" ${disabled?'disabled':''}>${svg}</button>`;
  };
  let html = btnHtml('‹',currentPage-1,false,currentPage===1);
  getRange(currentPage,pages).forEach(p=>{
    if(p==='…') html+=`<button class="page-btn" disabled style="cursor:default;">…</button>`;
    else html+=btnHtml(p,p,p===currentPage);
  });
  html += btnHtml('›',currentPage+1,false,currentPage===pages);
  document.getElementById('emoPageBtns').innerHTML = html;
}

function goPage(p) {
  const pages = Math.ceil(filteredTweets.length/PER_PAGE);
  if(p<1||p>pages) return;
  currentPage=p;
  _renderTablePage();
  document.getElementById('emoTableBody')?.closest('.emo-table-section')?.scrollIntoView({behavior:'smooth',block:'start'});
}

/* ══════════════════════════════════════════════════════
   TWEET DETAIL MODAL
══════════════════════════════════════════════════════ */
const TweetModal = (() => {
  function open(tOrJson) {
    let t;
    try{ t=(typeof tOrJson==='string')?JSON.parse(tOrJson.replace(/&quot;/g,'"')):tOrJson; }catch(e){return;}
    const emo    = (t.emotion||'unknown').toLowerCase();
    const meta   = EMO_META[emo]||{color:'#94a3b8',bg:'#f1f5f9',label:emo};
    const rawTs  = t.timestamp||t.created_at||'';
    let dtStr    = '';
    if(rawTs){const d=new Date(rawTs);if(!isNaN(d))dtStr=d.toLocaleString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}
    const handle  = t.author||t.screen_name||'';
    const name    = t.name||t.full_name||handle;
    const turl    = (t.url&&t.url!=='#')?t.url:`https://twitter.com/${handle}`;
    const avatar  = t.avatar||t.profile_image||'';
    const bucket  = meta.bucket||'neutral';
    const bucketColors = {positive:{bg:'#d1fae5',color:'#065f46',label:'Positive'},negative:{bg:'#fee2e2',color:'#991b1b',label:'Negative'},neutral:{bg:'#e0f2fe',color:'#0c4a6e',label:'Neutral'}};
    const bc = bucketColors[bucket]||bucketColors.neutral;
    const likes    = numFmt(t.likes||t.num_likes||0);
    const retweets = numFmt(t.retweets||t.num_shares||0);
    const replies  = numFmt(t.replies||t.num_comments||0);
    const views    = numFmt(t.views||t.impressions||t.view_count||0);
    const followers= numFmt(t.followers||t.followers_count||0);

    const avatarEl = avatar
      ? `<img src="${_esc(avatar)}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
         <div style="display:none;width:44px;height:44px;border-radius:50%;background:var(--color-brand-light);align-items:center;justify-content:center;flex-shrink:0;">
           <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--color-brand2)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
         </div>`
      : `<div style="width:44px;height:44px;border-radius:50%;background:var(--color-brand-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
           <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--color-brand2)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
         </div>`;

    document.getElementById('tweetModalBody').innerHTML = `
      {{-- Author header --}}
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding:14px 16px;background:var(--bg-muted);border-radius:12px;border:1px solid #edf0f4;">
        <div style="display:flex;align-items:center;flex-shrink:0;">${avatarEl}</div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:800;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${_esc(name)}</div>
          <div style="font-size:12px;color:var(--text-muted);font-weight:500;">@${_esc(handle)}${followers?` &nbsp;·&nbsp; <strong style="color:var(--text-secondary);">${followers}</strong> followers`:''}</div>
        </div>
        <span style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:700;background:${bc.bg};color:${bc.color};">
          <span style="width:6px;height:6px;border-radius:50%;background:${bc.color};display:inline-block;"></span>
          ${bc.label}
        </span>
      </div>

      {{-- Emotion label --}}
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:var(--radius-full);font-size:12px;font-weight:700;background:${meta.bg};color:${meta.color};border:1px solid ${meta.color}33;">
          <span style="width:7px;height:7px;border-radius:50%;background:${meta.color};display:inline-block;flex-shrink:0;"></span>
          ${_esc(meta.label)} Emotion
        </span>
        ${dtStr?`<span style="font-size:11px;color:var(--text-muted);margin-left:auto;">${dtStr}</span>`:''}
      </div>

      {{-- Tweet text --}}
      <div style="font-size:14.5px;line-height:1.8;color:var(--text-primary);margin-bottom:16px;padding:16px;background:var(--bg-muted);border-radius:12px;border-left:3px solid ${meta.color};">
        ${_linkify(t.text||'')}
      </div>

      {{-- Engagement stats grid --}}
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:16px;">
        <div style="text-align:center;padding:10px 8px;background:var(--bg-muted);border-radius:10px;border:1px solid #edf0f4;">
          <div style="font-size:17px;font-weight:800;color:#e91e8c;line-height:1.2;">${likes}</div>
          <div style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Likes</div>
        </div>
        <div style="text-align:center;padding:10px 8px;background:var(--bg-muted);border-radius:10px;border:1px solid #edf0f4;">
          <div style="font-size:17px;font-weight:800;color:#00ba7c;line-height:1.2;">${retweets}</div>
          <div style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Retweets</div>
        </div>
        <div style="text-align:center;padding:10px 8px;background:var(--bg-muted);border-radius:10px;border:1px solid #edf0f4;">
          <div style="font-size:17px;font-weight:800;color:#1d9bf0;line-height:1.2;">${replies}</div>
          <div style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Replies</div>
        </div>
        <div style="text-align:center;padding:10px 8px;background:var(--bg-muted);border-radius:10px;border:1px solid #edf0f4;">
          <div style="font-size:17px;font-weight:800;color:#7c3aed;line-height:1.2;">${views||'—'}</div>
          <div style="font-size:9.5px;color:var(--text-muted);font-weight:700;margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Views</div>
        </div>
      </div>

      {{-- Action buttons --}}
      <div style="display:flex;gap:10px;">
        <a href="https://twitter.com/${_esc(handle)}" target="_blank" rel="noopener"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 16px;border:1px solid #e2e8f0;border-radius:10px;font-size:13px;font-weight:600;color:var(--text-secondary);text-decoration:none;transition:all .15s;background:var(--bg-card);"
           onmouseover="this.style.background='var(--bg-subtle)'" onmouseout="this.style.background='var(--bg-card)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          View Profile
        </a>
        <a href="${_esc(turl)}" target="_blank" rel="noopener"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px 16px;background:linear-gradient(135deg,var(--color-brand),var(--color-brand-dark));border-radius:10px;font-size:13px;font-weight:700;color:#fff;text-decoration:none;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Open Tweet
        </a>
      </div>`;
    const modal = document.getElementById('tweetDetailModal');
    modal.style.display='flex';
    requestAnimationFrame(()=>modal.classList.add('show'));
  }
  function close(){
    const modal=document.getElementById('tweetDetailModal');
    modal.classList.remove('show');
    setTimeout(()=>{modal.style.display='none';document.getElementById('tweetModalBody').innerHTML='';},200);
  }
  return {open,close};
})();

/* ══════════════════════════════════════════════════════
   CSV EXPORT
══════════════════════════════════════════════════════ */
const EmoCsv = {
  _copy(text) {
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea');ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;';document.body.appendChild(ta);
      ta.select();document.execCommand('copy');document.body.removeChild(ta);
    });
    alert('CSV tersalin ke clipboard!');
  },
  copyOverview() {
    const emos  = allData.emotions||{};
    const total = Object.values(emos).reduce((a,v)=>a+(v.count||0),0);
    const lines = ['emotion;bucket;count;percentage'];
    EMO_ORDER.forEach(k=>{
      const m=EMO_META[k], c=emos[k]?.count||0;
      lines.push(`${m.label};${m.bucket};${c};${total>0?(c/total*100).toFixed(1):0}`);
    });
    this._copy(lines.join('\n'));
  },
  copyTrend() {
    const trend = allData.trend||[];
    const lines = ['date;emotion;count'];
    trend.forEach(t=>lines.push(`${t.date};${t.emotion};${t.count}`));
    this._copy(lines.join('\n'));
  },
};

/* ══════════════════════════════════════════════════════
   LAST UPDATED
══════════════════════════════════════════════════════ */
function updateLastUpdated() {
  const s = allData.summary||{};
  const txt = s.last_updated || new Date().toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'});
  document.getElementById('lastUpdatedText').textContent = txt;
}

/* ══════════════════════════════════════════════════════
   ACTIONS DROPDOWN
══════════════════════════════════════════════════════ */
function toggleActionsMenu() {
  document.getElementById('actionsMenu').classList.toggle('show');
}
document.addEventListener('click', e => {
  if (!document.getElementById('actionsDropdown')?.contains(e.target))
    document.getElementById('actionsMenu')?.classList.remove('show');
});

function exportCSV() {
  document.getElementById('actionsMenu').classList.remove('show');
  const tweets = filteredTweets;
  if (!tweets.length) return;
  const cols = ['#','text','emotion','bucket','author','date','likes','retweets'];
  const rows  = tweets.map((t,i)=>{
    const emo  = (t.emotion||'').toLowerCase();
    const bucket = EMO_META[emo]?.bucket || '';
    return [
      i+1,
      '"'+(t.text||'').replace(/"/g,'""')+'"',
      t.emotion||'',
      bucket,
      t.author||'',
      t.timestamp||t.created_at||'',
      t.likes||t.num_likes||0,
      t.retweets||t.num_shares||0,
    ].join(',');
  });
  const csv  = [cols.join(','),...rows].join('\n');
  const link = document.createElement('a');
  link.href  = 'data:text/csv;charset=utf-8,\uFEFF'+encodeURIComponent(csv);
  link.download = `emotion-analysis-${_fmtDate(new Date())}.csv`;
  link.click();
}

function refreshData() {
  document.getElementById('actionsMenu').classList.remove('show');
  loadData();
}

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  EmoDp.init();
  loadData();
  document.addEventListener('keydown', e => {
    if (e.key==='Escape') TweetModal.close();
  });
});
</script>
@endsection