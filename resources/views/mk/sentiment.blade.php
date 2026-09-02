@extends('mk.layouts.app')

@section('title', 'Sentiment Analysis - SMADIMENT')

@section('styles')
  <style>
    :root {
      --primary: #038047;--primary-rgb: 3, 128, 71;--primary-lt: rgba(3, 128, 71, .10);
      --dark: #273B4A;--white: #FFFFFF;--bg: #F1F5F8;--green: #038047;--green-light: #E8F5EE;
      --red: #EF4444;--red-light: #FEF2F2;--amber: #F59E0B;--amber-light: #FFFBEB;
      --cyan: #06B6D4;--cyan-light: #ECFEFF;
      --slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;
      --slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;
      --slate-800:#1E293B;--slate-900:#0F172A;
      --radius:8px;--radius-sm:5px;
      --shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
      --shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12);
      --sent-pos:#2FC6F6;--sent-neg:#ef4444;--sent-neu:#94a3b8;
      --primary-green:#038047;--primary-green-light:rgba(3,128,71,.08);--primary-green-border:rgba(3,128,71,.2)
    }
    @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    @keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
    @keyframes spin{to{transform:rotate(360deg)}}
    @keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
    @keyframes slideOutRight{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}
    @keyframes overlayIn{from{opacity:0}to{opacity:1}}
    @keyframes overlayOut{from{opacity:1}to{opacity:0}}
    @keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}
    @keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}
    .kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
    .sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
    .spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
    .spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}
    .kpi-card-hover{will-change:auto;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important}.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1}.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}.kpi-card-hover:active{transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important}
    .chart-container{position:relative}.chart-loading{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s}.chart-loading.hidden{opacity:0;pointer-events:none}.chart-loading span{font-size:11px;font-weight:600;color:var(--slate-400)}.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}.chart-empty i{font-size:34px;color:var(--slate-300);display:block}
    .snt-skel{background:linear-gradient(90deg,var(--slate-50) 25%,#e2e8f0 50%,var(--slate-50) 75%);background-size:200% 100%;animation:shimmer 1.5s ease-in-out infinite;border-radius:4px}.snt-skel-overlay{position:absolute;inset:0;z-index:3;border-radius:inherit}
    .snt-legend{display:flex;align-items:center;gap:14px;flex-wrap:wrap}.snt-legend-item{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500)}.snt-legend-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
    .snt-media-list{display:flex;flex-direction:column;gap:6px}.snt-media-row{display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--slate-50);border:1px solid var(--slate-100);border-radius:var(--radius-sm);transition:background .13s,border-color .13s}.snt-media-row:hover{border-color:var(--primary-green-border);background:#fff;box-shadow:var(--shadow-sm)}.snt-media-icon{width:28px;height:28px;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0}.snt-media-name{font-size:11px;font-weight:700;color:var(--slate-900);min-width:80px}.snt-media-bars{flex:1;display:flex;flex-direction:column;gap:2px}.snt-media-bar-row{display:flex;align-items:center;gap:5px}.snt-media-bar-track{flex:1;height:4px;background:var(--slate-100);border-radius:2px;overflow:hidden}.snt-media-bar-fill{height:100%;border-radius:2px;transition:width .8s cubic-bezier(.4,0,.2,1)}.snt-media-bar-val{font-size:10px;font-weight:700;color:var(--slate-500);min-width:36px;text-align:right;white-space:nowrap}.snt-media-total{font-size:11px;font-weight:700;color:var(--slate-900);min-width:48px;text-align:right}
    /* SAFARI FIX: backdrop-filter needs -webkit- prefix */
    .do-panel-overlay{position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);display:none}.do-panel-overlay.show{display:block;animation:overlayIn .22s ease-out}.do-panel-overlay.hiding{animation:overlayOut .22s ease-out forwards}
    #sntPopup{position:fixed;top:0;right:0;bottom:0;z-index:9001;width:480px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16)}
    #sntPopup.show{display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1)}#sntPopup.hiding{animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards}
    .sntp-header{display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}.sntp-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}.sntp-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sntp-count{display:none}.sntp-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;font-size:16px;color:var(--slate-500);display:flex;align-items:center;justify-content:center;transition:all .14s;flex-shrink:0}.sntp-close:hover{background:var(--red);border-color:var(--red);color:#fff}
    .sntp-actions{display:flex;align-items:center;gap:7px;padding:7px 12px;border-bottom:1px solid var(--slate-200);background:#fff;flex-shrink:0}.sntp-meta{flex:1;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px;overflow:hidden}.sntp-meta__label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .sntp-sent-tabs{display:flex;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;gap:2px}.sntp-sent-tab{padding:3px 9px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:700;cursor:pointer;transition:all .13s;color:var(--slate-500);white-space:nowrap}.sntp-sent-tab:hover{background:#fff}.sntp-sent-tab.active{background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08)}.sntp-sent-tab.active[data-s="all"]{color:var(--primary)}.sntp-sent-tab.neg.active{color:#ef4444}.sntp-sent-tab.pos.active{color:#0ea5e9}.sntp-sent-tab.neu.active{color:var(--slate-500)}
    .sntp-list{overflow-y:auto;-webkit-overflow-scrolling:touch;flex:1;padding:2px 0;min-height:0}.sntp-list::-webkit-scrollbar{width:4px}.sntp-list::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
    .sntp-item{display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--slate-100);transition:background .1s;cursor:pointer;align-items:flex-start}.sntp-item:last-child{border-bottom:none}.sntp-item:hover{background:#f0f9ff}.sntp-item.hidden{display:none}
    .sntp-avatar{width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden}.sntp-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
    .sntp-item-body{flex:1;min-width:0}.sntp-item-author{font-size:12px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.sntp-item-handle{font-size:10px;color:var(--slate-400);font-weight:500;margin-bottom:2px}.sntp-item-text{font-size:11px;color:var(--slate-500);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px}.sntp-item-footer{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap}
    .sntp-sent-badge{padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase}.sntp-sent-badge--pos{background:#dbeafe;color:#1d4ed8}.sntp-sent-badge--neg{background:#fee2e2;color:#991b1b}.sntp-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
    .sntp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:var(--slate-500);font-size:13px;font-weight:600}.sntp-spinner{width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
    #sntDetailPanel{position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1)}#sntDetailPanel.visible{display:flex}
    .sntdp-header{display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}.sntdp-back{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;flex-shrink:0}.sntdp-back:hover{background:var(--primary-lt);color:var(--primary);border-color:var(--primary)}.sntdp-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sntdp-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;font-size:16px;color:var(--slate-500);display:flex;align-items:center;justify-content:center;transition:all .14s}.sntdp-close:hover{background:var(--red);border-color:var(--red);color:#fff}
    .sntdp-body{overflow-y:auto;-webkit-overflow-scrolling:touch;flex:1;padding:16px}.sntdp-body::-webkit-scrollbar{width:4px}.sntdp-body::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
    .sntdp-avatar-row{display:flex;align-items:center;gap:10px;margin-bottom:12px}.sntdp-avatar-lg{width:46px;height:46px;border-radius:50%;color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;border:2px solid var(--slate-200);overflow:hidden;flex-shrink:0}.sntdp-avatar-lg img{width:100%;height:100%;object-fit:cover;border-radius:50%}.sntdp-author-name{font-size:14px;font-weight:700;color:var(--slate-900)}.sntdp-author-handle{font-size:11px;color:var(--slate-400);font-weight:500}
    .sntdp-sent-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px}.sntdp-sent-badge--pos{background:#dbeafe;color:#1d4ed8}.sntdp-sent-badge--neg{background:#fee2e2;color:#991b1b}.sntdp-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
    .sntdp-content-text{font-size:12px;color:var(--slate-600);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word}.sntdp-meta-row{display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px}
    .sntdp-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px}.sntdp-stat-box{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}.sntdp-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}.sntdp-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
    .sntdp-media-wrap{border-radius:var(--radius-sm);overflow:hidden;margin-bottom:10px;background:#000}.sntdp-media-img{width:100%;max-height:220px;object-fit:cover;display:block}
    .sntdp-link-btn{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;width:100%;margin-top:4px}.sntdp-link-btn:hover{filter:brightness(1.1);color:#fff}.sntdp-link-btn i{font-size:14px}
    #sntPlatPicker{position:fixed;z-index:20000;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:5px;min-width:175px;animation:fadeUp .14s ease-out;display:none}#sntPlatPicker.visible{display:block}
    .sntpp-header{padding:4px 9px 6px;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--slate-100);margin-bottom:3px}.sntpp-btn{display:flex;align-items:center;gap:7px;padding:7px 10px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;background:transparent;border:none;width:100%;text-align:left;color:var(--slate-500);transition:background .12s}.sntpp-btn:hover{background:var(--primary-lt);color:var(--primary)}.sntpp-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-left:auto}
    .kpi-card-hover h3{font-size:1.2rem}
    /* SAFARI FIX: chart-wrap containers need explicit height, not 100% in flex */
    .snt-chart-wrap{position:relative;width:100%;}
    .snt-chart-wrap > div[id^="ch"]{width:100%;display:block;}

    /* ════════════════════════════════════════
       EXPORT STYLES
    ════════════════════════════════════════ */
    .page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
    .page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
    .page-export-bar-left i{font-size:15px;color:var(--primary)}
    .page-export-bar-right{display:flex;gap:8px}
    .page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit}
    .page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
    .page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
    .page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
    .page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
    .page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
    .page-export-btn .export-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
    .page-export-btn.exporting .export-spinner{display:inline-block}
    .page-export-btn.exporting .export-icon{display:none}
    .card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent}
    .card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
    .card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
    .card-exp-btn-img{color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt)}
    .card-exp-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
    .card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
    .card-exp-btn .export-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
    .card-exp-btn.exporting .export-spinner{display:inline-block}
    .card-exp-btn.exporting .export-icon{display:none}
    .export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
    .export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    .export-toast.success{background:#065f46}
    .export-toast.error{background:#991b1b}

    /* Trend chart clickable cursor */
    #chTrend { cursor: pointer; }

    @media(max-width:640px){#sntPopup{width:100vw}}
  </style>
@endsection

@section('page-title', 'Sentiment Analysis')
@section('content')
  @php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $media = request()->get('media', 'all');
    $projects = $projects ?? [];
  @endphp
  <script>const OV_PID ={{ $projectId ? (int) $projectId : 'null' }}; const OV_SD = '{{ $startDate }}'; const OV_ED = '{{ $endDate }}'; const OV_MEDIA = '{{ $media }}';</script>
  @include('mk.layouts.partials.filter-datepicker')

  {{-- Media Filter --}}
  <div class="mb-3" style="margin-top:-8px;">
    <div style="display:flex;flex-direction:column;gap:5px;">
      <label style="font-size:10px;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;">Media</label>
      <select class="form-select form-select-sm" id="sntMediaFilter" style="max-width:220px;">
        <option value="all" {{ $media === 'all' ? 'selected' : '' }}>All Media</option>
        <option value="doc" {{ $media === 'doc' ? 'selected' : '' }}>Mass Media (Online News)</option>
        <option value="twitter" {{ $media === 'twitter' ? 'selected' : '' }}>X / Twitter</option>
        <option value="facebook" {{ $media === 'facebook' ? 'selected' : '' }}>Facebook</option>
        <option value="instagram" {{ $media === 'instagram' ? 'selected' : '' }}>Instagram</option>
        <option value="youtube" {{ $media === 'youtube' ? 'selected' : '' }}>YouTube</option>
        <option value="tiktok" {{ $media === 'tiktok' ? 'selected' : '' }}>TikTok</option>
      </select>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var mf = document.getElementById('sntMediaFilter');
      if (mf) { mf.addEventListener('change', function () { var form = document.getElementById('doFilterForm'); if (!form) return; var existing = form.querySelector('input[name="media"]'); if (!existing) { existing = document.createElement('input'); existing.type = 'hidden'; existing.name = 'media'; form.appendChild(existing) } existing.value = this.value; form.submit() }) }
    });
  </script>

  {{-- ════ PAGE EXPORT WRAPPER ════ --}}
  <div id="pageExportArea">

  {{-- ══ Page Export Toolbar ══ --}}
  <div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
      <i class="ph ph-export"></i>
      <span>Export Halaman</span>
      <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">PDF 2 Hal · PNG</span>
    </div>
    <div class="page-export-bar-right">
      <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
              onclick="SNTExport.run('pdf', this)" title="Export PDF 2 halaman">
        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
      </button>
      <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
              onclick="SNTExport.run('image', this)" title="Export halaman sebagai PNG">
        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
      </button>
    </div>
  </div>

  {{-- ════ HALAMAN 1 EXPORT: KPI + Overview + Mass/Social + ByType/ByPlat ════ --}}
  <div id="exportPage1">

  {{-- KPI --}}
  <div class="row mb-3">
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d1" style="cursor:pointer;" onclick="SNTPopup.openSentiment('pos')">
        <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Positive</p><h3 class="mb-0 text-white f-w-300" id="valPos">—</h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctPos"><i class="ph ph-chart-line-up me-1"></i>Loading…</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div></div></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d3" style="cursor:pointer;" onclick="SNTPopup.openSentiment('neg')">
        <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Negative</p><h3 class="mb-0 text-white f-w-300" id="valNeg">—</h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeg"><i class="ph ph-chart-line-up me-1"></i>Loading…</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div></div></div>
      </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d2" style="cursor:pointer;" onclick="SNTPopup.openSentiment('neu')">
        <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Neutral</p><h3 class="mb-0 text-white f-w-300" id="valNeu">—</h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeu"><i class="ph ph-chart-line-up me-1"></i>Loading…</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div></div></div></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d4">
        <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p><h3 class="mb-0 text-white f-w-300" id="valTot">—</h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12"><i class="ph ph-list-numbers me-1"></i>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div></div></div>
      </div>
    </div>
  </div>

  {{-- Overview Bar + SOV Doughnut --}}
  <div class="row mb-3">
    <div class="col-lg-8 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-export-overview">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Total Mentions by Sentiments</h6><small class="text-muted">Perbandingan volume Negative / Positive / Neutral</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyOverview()" title="Copy CSV"><i class="ph ph-copy me-1"></i>CSV</button>
            <span class="badge bg-light-primary text-primary">Overview</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-overview','overview','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-overview','overview','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- SAFARI FIX: explicit height on wrapper AND inner div --}}
          <div class="snt-chart-wrap" style="height:220px;" id="chOverviewWrap">
            <div id="chOverview" style="width:100%;height:220px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skOverview"></div>
          </div>
          <div class="snt-legend" style="margin-top:14px;justify-content:center;">
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Positive</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neutral</div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
        <div id="card-export-sov">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Share of Voice</h6><small class="text-muted">Distribusi sentimen</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">SOV</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-sov','sov','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-sov','sov','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;">
          <div class="snt-chart-wrap" style="height:210px;">
            <div id="chSovTotal" style="width:100%;height:210px;"></div>
            <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSovTotal"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Mass Media + Social Media --}}
  <div class="row mb-3">
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .26s both;">
        <div id="card-export-mass">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-newspaper f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments in Mass Media</h6><small class="text-muted">Online News / Artikel</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyMass()" title="CSV"><i class="ph ph-copy me-1"></i>CSV</button>
            <span class="badge bg-light-primary text-primary">Mass</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-mass','mass','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-mass','mass','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:260px;" id="chMassWrap">
            <div id="chMass" style="width:100%;height:260px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skMass"></div>
          </div>
          <div class="snt-legend" style="margin-top:12px;justify-content:center;">
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .30s both;">
        <div id="card-export-social">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-share-network f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments in Social Media</h6><small class="text-muted"> (X) Twitter · Facebook · Instagram · YouTube · TikTok</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copySocial()" title="CSV"><i class="ph ph-copy me-1"></i>CSV</button>
            <span class="badge bg-light-primary text-primary">Social</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-social','social','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-social','social','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:260px;" id="chSocialWrap">
            <div id="chSocial" style="width:100%;height:260px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skSocial"></div>
          </div>
          <div class="snt-legend" style="margin-top:12px;justify-content:center;">
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  {{-- By Type % + By Platform Grouped --}}
  <div class="row mb-3">
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .34s both;">
        <div id="card-export-bytype">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar-horizontal f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments by Media Types</h6><small class="text-muted">Persentase per platform (%)</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">% Share</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-bytype','bytype','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-bytype','bytype','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:300px;" id="chByTypeWrap">
            <div id="chByType" style="width:100%;height:300px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skByType"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .38s both;">
        <div id="card-export-byplat">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-columns f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments by Media Platforms</h6><small class="text-muted">Mass Media vs Social Media</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">Grouped</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-byplat','byplat','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-byplat','byplat','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:300px;" id="chByPlatWrap">
            <div id="chByPlat" style="width:100%;height:300px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skByPlat"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  </div>{{-- /exportPage1 --}}

  {{-- ════ HALAMAN 2 EXPORT: Mass SOV + Social SOV + Trend + Weekday + Hour ════ --}}
  <div id="exportPage2">

  {{-- Mass Pie + Social Pie --}}
  <div class="row mb-3">
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .42s both;">
        <div id="card-export-masspie">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-newspaper f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Mass Media SOV</h6><small class="text-muted">Share of Voice — Online News</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">Mass</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-masspie','masspie','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-masspie','masspie','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;">
          <div class="snt-chart-wrap" style="height:260px;">
            <div id="chMassPie" style="width:100%;height:260px;"></div>
            <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skMassPie"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .46s both;">
        <div id="card-export-socialpie">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-share-network f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Social Media SOV</h6><small class="text-muted">Share of Voice — Social platforms</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">Social</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-socialpie','socialpie','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-socialpie','socialpie','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;">
          <div class="snt-chart-wrap" style="height:260px;">
            <div id="chSocialPie" style="width:100%;height:260px;"></div>
            <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSocialPie"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Trend Line --}}
  <div class="row mb-3">
    <div class="col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .54s both;">
        <div id="card-export-trend">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-trend-up f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiment Trends in All Media</h6><small class="text-muted">Tren harian Total / Positive / Neutral / Negative</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyTrend()" title="Copy CSV"><i class="ph ph-copy me-1"></i>CSV</button>
            <span class="badge bg-light-primary text-primary" id="trendBadge">Loading…</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-trend','trend','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-trend','trend','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- SAFARI FIX: ApexCharts needs explicit pixel height on wrapper too --}}
          <div class="snt-chart-wrap" style="height:380px;" id="chTrendWrap">
            <div id="chTrend" style="width:100%;height:380px;min-height:380px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skTrend"></div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Weekday + Hour --}}
  <div class="row mb-3">
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .58s both;">
        <div id="card-export-weekday">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-calendar f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments by Weekday</h6><small class="text-muted">Volume per hari dalam seminggu</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">7 Hari</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-weekday','weekday','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-weekday','weekday','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:320px;">
            <div id="chWeekday" style="width:100%;height:320px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skWeekday"></div>
          </div>
          <div class="snt-legend" style="margin-top:12px;justify-content:center;">
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .62s both;">
        <div id="card-export-hour">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-clock f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Sentiments by Hour</h6><small class="text-muted">Distribusi per jam (00–23)</small></div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary">24 Jam</span>
            <div class="d-flex gap-1" data-html2canvas-ignore="true">
              <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-hour','hour','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
              <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-hour','hour','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="snt-chart-wrap" style="height:320px;">
            <div id="chHour" style="width:100%;height:320px;"></div>
            <div class="snt-skel snt-skel-overlay" id="skHour"></div>
          </div>
          <div class="snt-legend" style="margin-top:12px;justify-content:center;">
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div>
            <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  </div>{{-- /exportPage2 --}}

  {{-- /pageExportArea --}}
  </div>

  {{-- Export Toast --}}
  <div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
  </div>

  {{-- Slide Panel --}}
  <div class="do-panel-overlay" id="sntPanelOverlay" onclick="SNTPopup.close()"></div>
  <div id="sntPopup">
    <div class="sntp-header" id="sntPopHeader">
      <div class="sntp-dot" id="sntPopDot"></div><span class="sntp-title" id="sntPopTitle">Mentions</span><span class="sntp-count" id="sntPopCount">…</span><button class="sntp-close" onclick="SNTPopup.close()">×</button>
    </div>
    <div class="sntp-actions">
      <div class="sntp-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span class="sntp-meta__label" id="sntPopMeta">—</span></div>
      <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
        <div class="sntp-sent-tabs" id="sntPopSentTabs">
          <button class="sntp-sent-tab active" data-s="all" onclick="SNTPopup.filterSent('all')">Semua</button>
           <button class="sntp-sent-tab pos" data-s="pos" onclick="SNTPopup.filterSent('pos')">Pos</button>
           <button class="sntp-sent-tab neg" data-s="neg" onclick="SNTPopup.filterSent('neg')">Neg</button>
           <button class="sntp-sent-tab neu" data-s="neu" onclick="SNTPopup.filterSent('neu')">Neu</button>
         
         
         
        </div>
      </div>
    </div>
    <div class="sntp-list" id="sntPopList"></div>
    <div id="sntDetailPanel">
      <div class="sntdp-header"><button class="sntdp-back" onclick="SNTDetail.close()"><i class="ph ph-caret-left"></i></button><span class="sntdp-title" id="sntDpTitle">Detail</span><button class="sntdp-close" onclick="SNTPopup.close()">×</button></div>
      <div class="sntdp-body" id="sntDpBody"></div>
    </div>
  </div>
  <div id="sntPlatPicker">
    <div class="sntpp-header">Pilih Platform</div>
    <button class="sntpp-btn" onclick="SNTPopup.openPlatform('twit','all')">X / Twitter <span class="sntpp-dot" style="background:#1d9bf0;"></span></button>
    <button class="sntpp-btn" onclick="SNTPopup.openPlatform('fb','all')">Facebook <span class="sntpp-dot" style="background:#1877f2;"></span></button>
    <button class="sntpp-btn" onclick="SNTPopup.openPlatform('ig','all')">Instagram <span class="sntpp-dot" style="background:#e1306c;"></span></button>
    <button class="sntpp-btn" onclick="SNTPopup.openPlatform('yt','all')">YouTube <span class="sntpp-dot" style="background:#ff0000;"></span></button>
    <button class="sntpp-btn" onclick="SNTPopup.openPlatform('tiktok','all')">TikTok <span class="sntpp-dot" style="background:#111827;"></span></button>
  </div>
@endsection

@section('scripts')
  {{-- Export dependencies --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
 <script>
    'use strict';

    /* ══ SAFARI DETECTION ══ */
    const _isSafari = (function () {
      const ua = navigator.userAgent;
      return /^((?!chrome|android).)*safari/i.test(ua);
    })();

    /* ══ CONFIG ══ */
    const SNTCfg = {
      pid: OV_PID, sd: OV_SD, ed: OV_ED, media: OV_MEDIA,
      colors: { neg: '#ef4444', pos: '#2FC6F6', neu: '#94a3b8' },
    };

    /* ── UTILS ── */
    const numFmt = n => parseInt(n || 0).toLocaleString('id-ID');
    const numK   = n => { n = parseInt(n || 0); return n >= 1e6 ? (n/1e6).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'k' : String(n); };
    const hideSk = id => { const e = document.getElementById(id); if (e) e.style.display = 'none'; };
    const pct    = (v,t) => t > 0 ? ((v/t)*100).toFixed(1)+'%' : '0%';
    const emptyHtml = msg => `<div class="chart-empty"><i class="ph ph-warning-circle"></i><span>${msg}</span></div>`;

    /* ── SAFARI: wait for DOM paint ── */
    function _rafReady(fn, extraMs = 0) {
      if (_isSafari) {
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            if (extraMs > 0) setTimeout(fn, extraMs);
            else fn();
          });
        });
      } else {
        requestAnimationFrame(fn);
      }
    }

    /* ── SAFARI: force explicit px size on chart DOM node ── */
    function _ensureDim(dom) {
      if (!dom) return;
      if (dom.offsetHeight > 0 && dom.offsetWidth > 0) return;
      // SAFARI FIX: cek computed style juga, bukan hanya inline style
      let el = dom.parentElement;
      while (el) {
        const inlineH = parseInt(el.style.height);
        if (inlineH > 0) { dom.style.height = inlineH + 'px'; break; }
        const computedH = parseInt(getComputedStyle(el).height);
        if (computedH > 0) { dom.style.height = computedH + 'px'; break; }
        el = el.parentElement;
      }
      if (!dom.style.height) dom.style.height = '300px';
      const computedW = parseInt(getComputedStyle(dom.parentElement || dom).width);
      if (!dom.offsetWidth) dom.style.width = (computedW > 0 ? computedW : 400) + 'px';
    }

    /* ── ECHARTS REGISTRY ── */
    const SNTCharts = {
      _i: {},
      make(id) {
        if (this._i[id]) { try { this._i[id].dispose(); } catch(e) {} }
        const dom = document.getElementById(id);
        if (!dom) return null;

        // SAFARI FIX: pastikan dimensi ada sebelum init
        _ensureDim(dom);

        const w = dom.offsetWidth  || (dom.parentElement ? dom.parentElement.offsetWidth  : 400);
        const h = dom.offsetHeight || (dom.parentElement ? dom.parentElement.offsetHeight : 300);

        const c = echarts.init(dom, null, {
          renderer: _isSafari ? 'svg' : 'canvas',
          width:  w > 0 ? w : undefined,
          height: h > 0 ? h : undefined,
          devicePixelRatio: _isSafari ? 1 : (window.devicePixelRatio || 1),
        });
        this._i[id] = c;

        // SAFARI FIX: force resize setelah init
        if (_isSafari) setTimeout(() => { try { c.resize(); } catch(e){} }, 80);

        return c;
      },
      disposeAll() {
        Object.values(this._i).forEach(c => { try { c.dispose(); } catch(e) {} });
        this._i = {};
      }
    };

    /* ── RESIZE HANDLER ── */
    (function _setupResize() {
      const resizeAll = () => {
        Object.values(SNTCharts._i).forEach(c => {
          try { if (!c.isDisposed()) c.resize(); } catch(e) {}
        });
      };
      if (window.ResizeObserver) {
        const ro = new ResizeObserver(resizeAll);
        document.addEventListener('DOMContentLoaded', () => {
          const area = document.getElementById('pageExportArea');
          if (area) ro.observe(area);
        });
      }
      window.addEventListener('resize', resizeAll);
    })();

    let _apexTrend = null;

    const EC_TIP = {
      backgroundColor: '#1a202c', borderColor: '#334155', borderWidth: 1,
      padding: [10, 14], textStyle: { color: '#fff', fontFamily: "'Poppins',sans-serif", fontSize: 12 },
      extraCssText: 'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
    };

    /* ═══════════════════════════════════════════════════════
       DATA STORE
    ═══════════════════════════════════════════════════════ */
    const SNTData = {
      totals: { neg:0, pos:0, neu:0 },
      byMedia: [],
      trend: [],
      weekday: null,
      hour: null,
    };

    /* ═══════════════════════════════════════════════════════
       LOAD
    ═══════════════════════════════════════════════════════ */
    async function loadSentiment() {
      if (!SNTCfg.pid) {
        ['valNeg','valPos','valNeu','valTot'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent='—';});
        ['pctNeg','pctPos','pctNeu'].forEach(id=>{const el=document.getElementById(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>No Project';});
        ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie','skTrend','skWeekday','skHour'].forEach(hideSk);
        return;
      }
      try {
        if (document.fonts && document.fonts.ready) await document.fonts.ready;

        const res  = await fetch(`/mk/api/sentiment/totals?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&media=${SNTCfg.media}`);
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        SNTData.totals  = data.totals   || { neg:0, pos:0, neu:0 };
        SNTData.byMedia = data.by_media || [];
        SNTData.trend   = data.trend    || [];

        _rafReady(renderAll, _isSafari ? 120 : 0);
      } catch(err) {
        console.error('loadSentiment error:', err);
        ['valNeg','valPos','valNeu','valTot'].forEach(id=>{const el=document.getElementById(id);if(el)el.innerHTML='<span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">Error</span>';});
        ['pctNeg','pctPos','pctNeu'].forEach(id=>{const el=document.getElementById(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>Gagal memuat';});
        ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie'].forEach(hideSk);
      }
    }

    async function loadTimeData() {
      if (!SNTCfg.pid) return;
      try {
        if (document.fonts && document.fonts.ready) await document.fonts.ready;

        const res  = await fetch(`/mk/api/sentiment/by-time?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}`);
        const data = await res.json();
        if (data.error) throw new Error(data.error);
        SNTData.weekday = data.weekday;
        SNTData.hour    = data.hour;

        _rafReady(renderWeekdayHour, _isSafari ? 120 : 0);
      } catch(err) {
        console.error('loadTimeData error:', err);
        ['skWeekday','skHour'].forEach(hideSk);
      }
    }

    /* ═══════════════════════════════════════════════════════
       RENDER ALL
    ═══════════════════════════════════════════════════════ */
    function renderAll() {
      const { neg, pos, neu } = SNTData.totals;
      const tot = neg + pos + neu;
      document.getElementById('valNeg').textContent = numFmt(neg);
      document.getElementById('valPos').textContent = numFmt(pos);
      document.getElementById('valNeu').textContent = numFmt(neu);
      document.getElementById('valTot').textContent = numFmt(tot);
      document.getElementById('pctNeg').innerHTML = `<i class="ph ph-chart-line-up me-1"></i>${pct(neg, tot)}`;
      document.getElementById('pctPos').innerHTML = `<i class="ph ph-chart-line-up me-1"></i>${pct(pos, tot)}`;
      document.getElementById('pctNeu').innerHTML = `<i class="ph ph-chart-line-up me-1"></i>${pct(neu, tot)}`;
      renderOverviewBar();
      renderSovDoughnut('chSovTotal', ['Negative','Positive','Neutral'], [neg,pos,neu], ['#ef4444','#2FC6F6','#94a3b8'], true);
      hideSk('skSovTotal');
      renderMassSocialBars();
      renderByTypePct();
      renderByPlatGrouped();
      renderMassSocialPies();
      renderTrend();
    }

    /* ─── Overview Bar ─── */
    function renderOverviewBar() {
      hideSk('skOverview');
      const { neg, pos, neu } = SNTData.totals;
      const tot = neg + pos + neu;
      if (tot === 0) { document.getElementById('chOverview').parentElement.innerHTML = emptyHtml('Tidak ada data sentimen'); return; }
      const chart = SNTCharts.make('chOverview');
      if (!chart) return;
      chart.setOption({
        animation: true, animationDuration: _isSafari ? 400 : 900, animationEasing: 'elasticOut', backgroundColor: '#fff',
        tooltip: {
          ...EC_TIP, trigger: 'axis',
          axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.06)' } },
          formatter: params => {
            const p = params[0]; const v = p.value;
            return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                  <div style="display:flex;justify-content:space-between;gap:20px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(v)}</span></div>
                  <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct(v,tot)}</span></div>`;
          }
        },
        grid: { top:28, right:20, bottom:40, left:60 },
        xAxis: { type:'category', data:['Negative','Positive','Neutral'], axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:12,fontWeight:'600',color:'#374151'} },
        yAxis: { type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK} },
        series: [{
          type:'bar', barMaxWidth:60,
          data: [
            { value:neg, itemStyle:{ color:'#ef4444', borderRadius:[6,6,0,0] } },
            { value:pos, itemStyle:{ color:'#2FC6F6', borderRadius:[6,6,0,0] } },
            { value:neu, itemStyle:{ color:'#94a3b8', borderRadius:[6,6,0,0] } },
          ],
          label:{ show:true, position:'top', fontFamily:"'Poppins',sans-serif", fontWeight:'700', fontSize:11, color:'#64748b', formatter: p => numK(p.value) }
        }]
      });
      if (_isSafari) setTimeout(() => { try { chart.resize(); } catch(e){} }, 60);
    }

    /* ─── SOV Doughnut ─── */
    function renderSovDoughnut(domId, labels, values, colors, ready = false) {
      const tot = values.reduce((a,b) => a+b, 0);
      const chart = SNTCharts.make(domId);
      if (!chart) return;
      chart.setOption({
        animation: true, animationDuration: _isSafari ? 400 : 800, animationEasing: 'cubicOut', backgroundColor: 'transparent',
        tooltip: {
          ...EC_TIP, trigger:'item',
          formatter: p => {
            const pct2 = tot > 0 ? ((p.value/tot)*100).toFixed(1) : '0.0';
            return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                  <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                  <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct2}%</span></div>`;
          }
        },
        legend: { show:false },
        series: [{
          type:'pie', radius:['42%','62%'], center:['50%','50%'],
          avoidLabelOverlap:true, minAngle:5,
          itemStyle:{ borderColor:'#fff', borderWidth:3, borderRadius:5 },
          label: {
            show:true, alignTo:'edge', edgeDistance:10, lineHeight:18,
            fontFamily:"'Poppins',sans-serif", fontSize:11, color:'#374151',
            formatter: p => {
              const pc = tot > 0 ? (p.value/tot*100) : 0; if (pc < 3) return '';
              return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
            },
            rich: {
              name:{ fontWeight:'700', fontSize:11, color:'#1a202c', lineHeight:18 },
              pct:{ fontWeight:'700', fontSize:10, color:'#038047', lineHeight:16, backgroundColor:'#edf7f3', borderRadius:4, padding:[1,5] },
            }
          },
          labelLine:{ show:true, length:12, length2:16, smooth:.4, lineStyle:{color:'#c4cdd8',width:1.2} },
          emphasis:{ scale:true, scaleSize:5, itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'} },
          data: labels.map((l,i) => ({ name:l, value:values[i], itemStyle:{color:colors[i]} }))
        }],
        graphic: [
          { type:'text', left:'center', top:'44%', z:100, style:{ text:numK(tot), fill:'#0f172a', font:"800 17px 'Poppins',sans-serif", textAlign:'center' } },
          { type:'text', left:'center', top:'53%', z:100, style:{ text:'TOTAL', fill:'#94a3b8', font:"600 8px 'Poppins',sans-serif", textAlign:'center', letterSpacing:2 } },
        ]
      });
      if (_isSafari) setTimeout(() => { try { chart.resize(); } catch(e){} }, 60);
    }

    /* ─── Mass vs Social stacked bars ─── */
    function renderMassSocialBars() {
      const bm = SNTData.byMedia;
      const massPlt   = bm.filter(m => m.key === 'doc');
      const socialPlt = bm.filter(m => m.key !== 'doc');
      hideSk('skMass');
      const massChart = SNTCharts.make('chMass');
      if (massChart) {
        const mLabels = massPlt.map(m => m.label), mNeg = massPlt.map(m => m.neg), mPos = massPlt.map(m => m.pos), mNeu = massPlt.map(m => m.neu), mTot = massPlt.map(m => m.neg+m.pos+m.neu);
        if (mLabels.length && mTot.some(v => v > 0)) massChart.setOption(makeStackedBarOption(mLabels,mNeg,mPos,mNeu,mLabels,mTot));
        else document.getElementById('chMass').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');
        if (_isSafari) setTimeout(() => { try { massChart.resize(); } catch(e){} }, 60);
      }
      hideSk('skSocial');
      const socialChart = SNTCharts.make('chSocial');
      if (socialChart) {
        const sLabels = socialPlt.map(m => m.label), sNeg = socialPlt.map(m => m.neg), sPos = socialPlt.map(m => m.pos), sNeu = socialPlt.map(m => m.neu), sTot = socialPlt.map(m => m.neg+m.pos+m.neu);
        if (sLabels.length && sTot.some(v => v > 0)) socialChart.setOption(makeStackedBarOption(sLabels,sNeg,sPos,sNeu,sLabels,sTot));
        else document.getElementById('chSocial').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
        if (_isSafari) setTimeout(() => { try { socialChart.resize(); } catch(e){} }, 60);
      }
    }

    function makeStackedBarOption(xLabels, negData, posData, neuData, tooltipLabels, totals) {
      const makeData = (arr, col) => arr.map(v => ({ value:v, itemStyle:{color:col} }));
      return {
        animation:true, animationDuration: _isSafari ? 400 : 900, animationEasing:'elasticOut', backgroundColor:'#fff',
        tooltip: {
          ...EC_TIP, trigger:'axis', axisPointer:{ type:'shadow', shadowStyle:{color:'rgba(3,128,71,.06)'} },
          formatter: params => {
            const idx = params[0]?.dataIndex ?? 0; const tot = totals[idx] || 0;
            const rows = params.slice().reverse().map(p => `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
            return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${xLabels[idx]||''}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;"><span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span></div>`;
          }
        },
        grid: { top:28, right:16, bottom:36, left:60 },
        xAxis: { type:'category', data:xLabels, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,formatter:v=>v.length>11?v.slice(0,10)+'…':v} },
        yAxis: { type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK} },
        series: [
          { name:'Neutral',  type:'bar', stack:'s', data:makeData(neuData,'#94a3b8'), itemStyle:{borderRadius:[0,0,0,0]} },
          { name:'Positive', type:'bar', stack:'s', data:makeData(posData,'#2FC6F6') },
          { name:'Negative', type:'bar', stack:'s', barMaxWidth:80, data:negData.map((v,i) => ({ value:v, itemStyle:{color:'#ef4444',borderRadius:[6,6,0,0]} })), label:{ show:true, position:'top', fontFamily:"'Poppins',sans-serif", fontWeight:'700', fontSize:11, color:'#64748b', formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):'' } }
        ]
      };
    }

    /* ─── By Media Type % ─── */
    function renderByTypePct() {
      hideSk('skByType');
      const bm = SNTData.byMedia;
      if (!bm.length) { document.getElementById('chByType').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }
      const chart = SNTCharts.make('chByType'); if (!chart) return;
      const labels  = bm.map(m => m.label);
      const negPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neg/t*100).toFixed(1)):0; });
      const posPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.pos/t*100).toFixed(1)):0; });
      const neuPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neu/t*100).toFixed(1)):0; });
      chart.setOption({
        animation:true, animationDuration: _isSafari ? 400 : 900, animationEasing:'elasticOut', backgroundColor:'#fff',
        tooltip: {
          ...EC_TIP, trigger:'axis', axisPointer:{ type:'shadow' }, formatter: params => {
            const idx = params[0]?.dataIndex ?? 0; const m = bm[idx]; if (!m) return '';
            const tot = m.neg+m.pos+m.neu;
            return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${m.label}</div>${[['Negative','#ef4444',m.neg],['Positive','#2FC6F6',m.pos],['Neutral','#94a3b8',m.neu]].map(([n,c,v]) => `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div><div style="display:flex;gap:10px;"><span style="font-size:12px;font-weight:700;">${numFmt(v)}</span><span style="font-size:10px;color:#94a3b8;">${tot>0?(v/tot*100).toFixed(1):'0'}%</span></div></div>`).join('')}`;
          }
        },
        legend: { bottom:0, data:['Negative','Positive','Neutral'], textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}, icon:'circle', itemWidth:10, itemHeight:10, itemGap:20 },
        grid: { top:12, right:16, bottom:50, left:100 },
        xAxis: { type:'value', max:100, min:0, axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'} },
        yAxis: { type:'category', data:labels, inverse:false, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#374151',margin:10} },
        series: [
          { name:'Negative', type:'bar', stack:'pct', data:negPcts, itemStyle:{color:'#ef4444'}, barMaxWidth:30, emphasis:{focus:'series'}, label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'} },
          { name:'Positive', type:'bar', stack:'pct', data:posPcts, itemStyle:{color:'#2FC6F6'}, barMaxWidth:30, emphasis:{focus:'series'}, label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'} },
          { name:'Neutral',  type:'bar', stack:'pct', data:neuPcts, itemStyle:{color:'#94a3b8',borderRadius:[0,4,4,0]}, barMaxWidth:30, emphasis:{focus:'series'} },
        ]
      });
      if (_isSafari) setTimeout(() => { try { chart.resize(); } catch(e){} }, 60);
    }

    /* ─── By Platform Grouped ─── */
    function renderByPlatGrouped() {
      hideSk('skByPlat');
      const bm = SNTData.byMedia;
      if (!bm.length) { document.getElementById('chByPlat').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }
      const chart = SNTCharts.make('chByPlat'); if (!chart) return;
      const massDat   = bm.filter(m => m.key === 'doc');
      const socialDat = bm.filter(m => m.key !== 'doc');
      const groups    = ['Mass Media','Social Media'];
      const mNeg=massDat.reduce((s,m)=>s+m.neg,0), mPos=massDat.reduce((s,m)=>s+m.pos,0), mNeu=massDat.reduce((s,m)=>s+m.neu,0), mTot=mNeg+mPos+mNeu;
      const sNeg=socialDat.reduce((s,m)=>s+m.neg,0), sPos=socialDat.reduce((s,m)=>s+m.pos,0), sNeu=socialDat.reduce((s,m)=>s+m.neu,0), sTot=sNeg+sPos+sNeu;
      const negPct=[mTot>0?(mNeg/mTot*100).toFixed(1):0, sTot>0?(sNeg/sTot*100).toFixed(1):0];
      const posPct=[mTot>0?(mPos/mTot*100).toFixed(1):0, sTot>0?(sPos/sTot*100).toFixed(1):0];
      const neuPct=[mTot>0?(mNeu/mTot*100).toFixed(1):0, sTot>0?(sNeu/sTot*100).toFixed(1):0];
      chart.setOption({
        animation:true, animationDuration: _isSafari ? 400 : 900, backgroundColor:'#fff',
        tooltip: {
          ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow'}, formatter: params => {
            const idx=params[0]?.dataIndex??0; const lbl=groups[idx];
            const neg=[mNeg,sNeg][idx], pos=[mPos,sPos][idx], neu=[mNeu,sNeu][idx], tot=[mTot,sTot][idx];
            return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;">${lbl}</div>${[['Negative','#ef4444',neg],['Positive','#2FC6F6',pos],['Neutral','#94a3b8',neu]].map(([n,c,v]) => `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(v)} <span style="color:#94a3b8;font-size:10px;">(${tot>0?(v/tot*100).toFixed(1):'0'}%)</span></span></div>`).join('')}`;
          }
        },
        legend: { bottom:0, data:['Negative','Positive','Neutral'], textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}, icon:'circle', itemWidth:10, itemHeight:10, itemGap:20 },
        grid: { top:24, right:16, bottom:50, left:72 },
        xAxis: { type:'category', data:groups, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'700',color:'#374151'} },
        yAxis: { type:'value', max:100, min:0, axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'} },
        series: [
          { name:'Neutral',  type:'bar', stack:'s', data:neuPct.map(v=>parseFloat(v)), itemStyle:{color:'#94a3b8'}, barMaxWidth:90, emphasis:{focus:'series'} },
          { name:'Positive', type:'bar', stack:'s', data:posPct.map(v=>parseFloat(v)), itemStyle:{color:'#2FC6F6'}, barMaxWidth:90, emphasis:{focus:'series'}, label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'} },
          { name:'Negative', type:'bar', stack:'s', data:negPct.map(v=>parseFloat(v)), itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]}, barMaxWidth:90, emphasis:{focus:'series'}, label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'} },
        ]
      });
      if (_isSafari) setTimeout(() => { try { chart.resize(); } catch(e){} }, 60);
    }

    /* ─── Mass + Social Pies ─── */
    function renderMassSocialPies() {
      const bm = SNTData.byMedia;
      const massDat = bm.filter(m => m.key === 'doc');
      const mN=massDat.reduce((s,m)=>s+m.neg,0), mP=massDat.reduce((s,m)=>s+m.pos,0), mNe=massDat.reduce((s,m)=>s+m.neu,0);
      hideSk('skMassPie');
      if (mN+mP+mNe > 0) renderSovDoughnut('chMassPie',['Negative','Positive','Neutral'],[mN,mP,mNe],['#ef4444','#2FC6F6','#94a3b8']);
      else document.getElementById('chMassPie').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');
      const sD=bm.filter(m=>m.key!=='doc');
      const sN=sD.reduce((s,m)=>s+m.neg,0), sP=sD.reduce((s,m)=>s+m.pos,0), sNe=sD.reduce((s,m)=>s+m.neu,0);
      hideSk('skSocialPie');
      if (sN+sP+sNe > 0) renderSovDoughnut('chSocialPie',['Negative','Positive','Neutral'],[sN,sP,sNe],['#ef4444','#2FC6F6','#94a3b8']);
      else document.getElementById('chSocialPie').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
    }

    /* ═══════════════════════════════════════════════════════
       TREND  (ApexCharts)
    ═══════════════════════════════════════════════════════ */
    function renderTrend() {
      hideSk('skTrend');
      const trend = SNTData.trend;
      if (!trend.length) {
        document.getElementById('trendBadge').textContent = 'No Data';
        document.getElementById('chTrend').innerHTML = emptyHtml('Data trend tidak tersedia untuk periode ini');
        return;
      }
      const dates   = trend.map(d => d.date);
      const negVals = trend.map(d => d.neg || 0);
      const posVals = trend.map(d => d.pos || 0);
      const neuVals = trend.map(d => d.neu || 0);
      const totVals = trend.map(d => (d.neg||0)+(d.pos||0)+(d.neu||0));
      const xLabels = dates.map(d => { const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}/${dt.getMonth()+1}`; });
      const fmtB    = d => { const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
      document.getElementById('trendBadge').textContent = `${fmtB(dates[0])} – ${fmtB(dates[dates.length-1])}`;

      if (_apexTrend) { try { _apexTrend.destroy(); } catch(e) {} _apexTrend = null; }
      const el = document.getElementById('chTrend');
      if (!el) return;

      // SAFARI FIX: ensure element has explicit height before ApexCharts mounts
      if (_isSafari && el.offsetHeight < 10) el.style.height = '380px';

      const sentMap = {0:'all', 1:'pos', 2:'neu', 3:'neg'};

      _apexTrend = new ApexCharts(el, {
        chart: {
          type: 'area', height: 380,
          fontFamily: 'inherit',
          background: 'transparent',
          toolbar: { show: false },
          // SAFARI FIX: disable animasi di Safari
          animations: {
            enabled: !_isSafari,
            easing: 'linear',
            dynamicAnimation: { speed: 1000 }
          },
          events: {
            click: (e, ctx, cfg) => {
              let date = null;
              let sIdx = 0;
              if (cfg && typeof cfg.dataPointIndex === 'number' && cfg.dataPointIndex >= 0) {
                date = dates[cfg.dataPointIndex];
                if (typeof cfg.seriesIndex === 'number' && cfg.seriesIndex >= 0) sIdx = cfg.seriesIndex;
              } else {
                const target = e.target;
                const textEl = target.closest('text') || target;
                const jAttr = textEl.getAttribute('j') || textEl.getAttribute('data:realIndex') || textEl.getAttribute('index');
                const iAttr = textEl.getAttribute('i') || textEl.getAttribute('data:seriesIndex');
                if (jAttr !== null && !isNaN(parseInt(jAttr, 10))) {
                  const dpIdx = parseInt(jAttr, 10);
                  if (dates[dpIdx]) {
                    date = dates[dpIdx];
                    if (iAttr !== null && !isNaN(parseInt(iAttr, 10))) sIdx = parseInt(iAttr, 10);
                  }
                } else {
                  const txt = textEl.textContent?.trim();
                  if (txt) {
                    const xIdx = xLabels.indexOf(txt);
                    if (xIdx >= 0 && dates[xIdx]) date = dates[xIdx];
                  }
                }
              }
              if (date) {
                SNTPopup.open('all', sentMap[sIdx] || 'all', date, date);
              }
            },
            markerClick: (e, ctx, cfg) => {
              const date = (cfg && typeof cfg.dataPointIndex === 'number' && cfg.dataPointIndex >= 0) ? dates[cfg.dataPointIndex] : null;
              SNTPopup.open('all', sentMap[cfg.seriesIndex] || 'all', date, date);
            },
            legendClick: (ctx, seriesIndex) => {
              SNTPopup.open('all', sentMap[seriesIndex] || 'all');
            },
            dataPointSelection: (e, ctx, cfg) => {
              const date = (cfg && typeof cfg.dataPointIndex === 'number' && cfg.dataPointIndex >= 0) ? dates[cfg.dataPointIndex] : null;
              SNTPopup.open('all', sentMap[cfg.seriesIndex] || 'all', date, date);
            },
          }
        },
        series: [
          { name:'Total',    data:totVals },
          { name:'Positive', data:posVals },
          { name:'Neutral',  data:neuVals },
          { name:'Negative', data:negVals },
        ],
        colors: ['#4680ff','#10B981','#94A3B8','#EF4444'],
        xaxis: {
          categories: xLabels,
          axisBorder: { show:false }, axisTicks: { show:false },
          labels: { style:{ fontFamily:'inherit', fontSize:'11px', fontWeight:600, colors:'#94A3B8' } }
        },
        yaxis: {
          labels: {
            formatter: v => numK(v),
            style: { fontFamily:'inherit', fontSize:'10px', fontWeight:600, colors:'#94A3B8' }
          },
          axisBorder: { show:false }, axisTicks: { show:false }
        },
        // SAFARI FIX: gunakan solid fill, bukan gradient
        fill: _isSafari
          ? { opacity: 0.15, type: 'solid' }
          : { opacity: 0.30 },
        stroke: { curve:'smooth', width:2.5 },
        markers: { size:5, strokeWidth:2, strokeColors:'#fff', hover:{ size:7 } },
        dataLabels: {
          enabled: xLabels.length <= 31,
          formatter: v => v > 0 ? numFmt(v) : '',
          style: { fontSize:'10px', fontFamily:'inherit', fontWeight:'800' },
          background: { enabled:true, borderRadius:3, borderWidth:0, padding:3, opacity:0.9 },
          offsetY: -8,
        },
        grid: {
          borderColor: 'rgba(226,232,240,.55)',
          strokeDashArray: 3,
          xaxis: { lines: { show: false } },
          padding: { top: 15, right: 10, left: 10, bottom: 0 }
        },
        legend: { position:'bottom', horizontalAlign:'left', fontFamily:'inherit', fontSize:'11px', fontWeight:'600', labels:{ colors:'#94A3B8' }, markers:{ width:9, height:9, radius:50 }, itemMargin:{ horizontal:14, vertical:4 } },
        tooltip: { shared:false, intersect:true, style:{ fontFamily:'inherit', fontSize:'12px' }, y:{ formatter:v => numFmt(v)+' mentions' } },
      });
      _apexTrend.render();

      // SAFARI FIX: force layout recalculation setelah render
      if (_isSafari) {
        setTimeout(() => {
          try { _apexTrend.updateOptions({}, false, false, false); } catch(e) {}
        }, 250);
      }
    }

    /* ─── Weekday & Hour ─── */
    function renderWeekdayHour() {
      const wNeg = SNTData.weekday?.neg||[];
      const wPos = SNTData.weekday?.pos||[];
      const wNeu = SNTData.weekday?.neu||[];
      const wTot = wNeg.map((n, i) => n + (wPos[i]||0) + (wNeu[i]||0));
      
      const hNeg = SNTData.hour?.neg||[];
      const hPos = SNTData.hour?.pos||[];
      const hNeu = SNTData.hour?.neu||[];
      const hTot = hNeg.map((n, i) => n + (hPos[i]||0) + (hNeu[i]||0));

      renderTimeChart('chWeekday','skWeekday', SNTData.weekday?.weekdays||[], wNeg, wPos, wNeu, wTot, false);
      renderTimeChart('chHour','skHour', SNTData.hour?.hours||[], hNeg, hPos, hNeu, hTot, true);
    }

    function renderTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour = false) {
      hideSk(skelId);
      if (!labels.length || !totals.some(v=>v>0)) {
        document.getElementById(domId).parentElement.innerHTML = emptyHtml('Data tidak tersedia untuk periode ini');
        return;
      }
      const chart = SNTCharts.make(domId); if (!chart) return;
      const makeS = (name,data,color) => ({
        name, type:'bar', stack:'s',
        data: data.map(v => ({ value:v, itemStyle:{color,borderRadius:[0,0,0,0]} })),
        emphasis: { focus:'series' }
      });
      chart.setOption({
        animation:true, animationDuration: _isSafari ? 400 : 800, animationEasing:'elasticOut', backgroundColor:'#fff',
        tooltip: {
          ...EC_TIP, trigger:'axis',
          axisPointer:{ type:'shadow', shadowStyle:{color:'rgba(3,128,71,.06)'} },
          formatter: params => {
            const idx  = params[0]?.dataIndex ?? 0;
            const lbl  = labels[idx] || '';
            const tot  = totals[idx] || 0;
            const rows = [...params].reverse()
              .map(p => `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                <div style="display:flex;align-items:center;gap:6px;">
                  <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span>
                  <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
                </div>
                <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
              </div>`).join('');
            return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">
                      ${isHour ? 'Jam ' : ''}${lbl}
                    </div>
                    ${rows}
                    <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                      <span style="font-size:11px;color:#94a3b8;">Total</span>
                      <span style="font-weight:700;">${numFmt(tot)}</span>
                    </div>`;
          }
        },
        grid: { top:24, right:16, bottom: isHour ? 65 : 40, left:56 },
        xAxis: {
          type:'category', data:labels, axisLine:{show:false}, axisTick:{show:false},
          axisLabel:{ fontFamily:"'Poppins',sans-serif", fontSize: isHour ? 10 : 11, fontWeight:'600', color:'#64748b', interval: isHour ? 2 : 0, rotate: isHour ? 45 : 0, margin: isHour ? 10 : 8 }
        },
        yAxis: { type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK} },
        series: [
          makeS('Neutral',  neuData, '#94a3b8'),
          makeS('Positive', posData, '#2FC6F6'),
          {
            name:'Negative', type:'bar', stack:'s',
            barMaxWidth: isHour ? 16 : 56,
            data: negData.map((v,i) => ({ value:v, itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]} })),
            label: { show:true, position:'top', fontFamily:"'Poppins',sans-serif", fontWeight:'700', fontSize: isHour ? 10 : 12, color:'#64748b', rotate: isHour ? 45 : 0, formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):'' },
            emphasis: { focus:'series' }
          }
        ]
      });
      if (_isSafari) setTimeout(() => { try { chart.resize(); } catch(e){} }, 60);
    }

    /* ═══════════════════════════════════════════════════════
       CSV EXPORT
    ═══════════════════════════════════════════════════════ */
    const SNTCsv = {
      _copy(text) {
        navigator.clipboard?.writeText(text).catch(() => { const ta=document.createElement('textarea'); ta.value=text; ta.style.cssText='position:fixed;opacity:0;'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); });
        alert('CSV data tersalin ke clipboard!');
      },
      copyOverview() { const {neg,pos,neu}=SNTData.totals; const tot=neg+pos+neu; this._copy(['sentiment;count;percentage',`Negative;${neg};${tot>0?(neg/tot*100).toFixed(1):0}`,`Positive;${pos};${tot>0?(pos/tot*100).toFixed(1):0}`,`Neutral;${neu};${tot>0?(neu/tot*100).toFixed(1):0}`,`Total;${tot};100`].join('\n')); },
      copyMass()     { const bm=SNTData.byMedia.filter(m=>m.key==='doc'); const lines=['platform;negative;positive;neutral;total']; bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`)); this._copy(lines.join('\n')); },
      copySocial()   { const bm=SNTData.byMedia.filter(m=>m.key!=='doc'); const lines=['platform;negative;positive;neutral;total']; bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`)); this._copy(lines.join('\n')); },
      copyTrend()    { const lines=['date;negative;positive;neutral;total']; SNTData.trend.forEach(d=>lines.push(`${d.date};${d.neg};${d.pos};${d.neu};${d.neg+d.pos+d.neu}`)); this._copy(lines.join('\n')); }
    };

    /* ═══════════════════════════════════════════════════════
       PAGE CONTROLLER
    ═══════════════════════════════════════════════════════ */
    const SNTPage = {
      reload() {
        if (_apexTrend) { try { _apexTrend.destroy(); } catch(e) {} _apexTrend = null; }
        SNTCharts.disposeAll();
        SNTData.trend=[]; SNTData.byMedia=[]; SNTData.weekday=null; SNTData.hour=null;
        loadSentiment(); loadTimeData();
      },
      init() { loadSentiment(); loadTimeData(); }
    };

    // SAFARI FIX: delay init agar layout selesai dulu
    document.addEventListener('DOMContentLoaded', () => {
      if (_isSafari) {
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            setTimeout(() => SNTPage.init(), 120);
          });
        });
      } else {
        SNTPage.init();
      }
    });

    /* ══════════════════════════════════════════════════════════
       SNTExport v2 — 2-Page PDF Split
    ══════════════════════════════════════════════════════════ */
const SNTExport = (() => {
    'use strict';

    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t   = document.getElementById('exportToast'),
              m   = document.getElementById('exportToastMsg'),
              ico = document.getElementById('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show' + (type !== 'default' ? ' ' + type : '');
        ico.className = 'ph ' + ({ success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' }[type] || 'ph-spinner');
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
    }

    function _btnState(btns, loading) {
        [].concat(btns).forEach(b => { if (!b) return; b.disabled = loading; b.classList.toggle('exporting', loading); });
    }

    /* ── Tunggu ECharts selesai render ── */
    function _waitEChartsFinished(instance, ms = 2500) {
        return new Promise(resolve => {
            let done = false;
            const finish = () => { if (!done) { done = true; resolve(); } };
            const t = setTimeout(finish, ms);
            try { instance.on('finished', () => { clearTimeout(t); finish(); }); }
            catch(e) { clearTimeout(t); finish(); }
        });
    }

    /* ── Snapshot satu ECharts instance ── */
    async function _getEChartSnapshot(instance) {
        if (!instance || instance.isDisposed?.()) return null;
        try {
            instance.setOption({ animation: false }, { silent: true });
            await _waitEChartsFinished(instance, 1500);
            await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
            const dataUrl = instance.getDataURL({
                type: 'png',
                pixelRatio: 2,
                backgroundColor: '#ffffff',
                excludeComponents: ['toolbox'],
            });
            return (dataUrl && dataUrl !== 'data:,') ? dataUrl : null;
        } catch(e) {
            console.warn('[SNTExport] echarts snapshot failed:', e);
            return null;
        } finally {
            try { instance.setOption({ animation: true }, { silent: true }); } catch(e) {}
        }
    }

    /* ── Snapshot semua ECharts yang ada di SNTCharts._i ── */
    async function _getAllEChartSnapshots(el) {
        const snaps = {};
        const ids = ['chOverview','chSovTotal','chMass','chSocial','chByType','chByPlat','chMassPie','chSocialPie','chWeekday','chHour'];
        for (const id of ids) {
            const container = document.getElementById(id);
            if (!container || (el && !el.contains(container))) continue;
            const instance = SNTCharts._i[id];
            if (!instance || instance.isDisposed?.()) continue;
            const snap = await _getEChartSnapshot(instance);
            if (snap) snaps[id] = snap;
        }
        return snaps;
    }

    /* ── Snapshot ApexCharts trend ── */
    async function _getApexSnapshot() {
        if (typeof _apexTrend === 'undefined' || !_apexTrend) return null;
        try {
            _apexTrend.updateOptions({
                fill: { opacity: 1, type: 'solid' },
                chart: { animations: { enabled: false } }
            }, false, false, false);
            await new Promise(r => setTimeout(r, 400));
            const result = await _apexTrend.dataURI({ scale: 2 });
            return result?.imgURI || null;
        } catch(e) {
            console.warn('[SNTExport] Apex snapshot failed:', e);
            return null;
        } finally {
            try {
                _apexTrend.updateOptions({
                    fill: _isSafari ? { opacity: 0.15, type: 'solid' } : { opacity: 0.30 },
                    chart: { animations: { enabled: !_isSafari } }
                }, false, false, false);
            } catch(e) {}
        }
    }

    /* ── onclone: cleanup + inject semua snapshots ── */
    function _makeOnClone(ecSnaps, apexSnap) {
        return (clonedDoc) => {
            /* Freeze semua animasi */
            const s = clonedDoc.createElement('style');
            s.textContent = `
                *, *::before, *::after {
                    animation: none !important;
                    transition: none !important;
                    animation-play-state: paused !important;
                }
                [data-html2canvas-ignore] { display: none !important; }
                .sk-block,.snt-skel,.snt-skel-overlay { animation: none !important; background: #e2e8f0 !important; display: none !important; }
                .kpi-card-hover { transform: none !important; filter: none !important; opacity: 1 !important; }
                .kpi-card-hover::before { display: none !important; }
                .chart-loading { display: none !important; }
                .export-toast  { display: none !important; }
                .page-export-bar { display: none !important; }
                .spin-ring { animation: none !important; }
            `;
            clonedDoc.head.appendChild(s);

            /* Sembunyikan elemen noise */
            clonedDoc.querySelectorAll(
                '#sntPopup,.do-panel-overlay,#sntPanelOverlay,#sntPlatPicker,' +
                '#sntDetailPanel,.export-toast,[data-html2canvas-ignore],' +
                '.page-export-bar,.chart-loading,.snt-skel,.snt-skel-overlay'
            ).forEach(el => {
                el.style.cssText += 'display:none!important;visibility:hidden!important;';
            });

            /* Sembunyikan iframe */
            clonedDoc.querySelectorAll('iframe').forEach(f => {
                f.style.cssText += 'display:none!important;';
            });

            /* Force visible semua card */
            clonedDoc.querySelectorAll(
                '.card,.card-body,.card-header,.row,[class*="col-"],' +
                '.kpi-card-hover,#pageExportArea,#exportPage1,#exportPage2'
            ).forEach(el => {
                el.style.opacity    = '1';
                el.style.transform  = 'none';
                el.style.visibility = 'visible';
                el.style.filter     = 'none';
            });

            /* Force visible KPI values */
            ['valNeg','valPos','valNeu','valTot'].forEach(id => {
                const el = clonedDoc.getElementById(id);
                if (el) { el.style.opacity = '1'; el.style.visibility = 'visible'; }
            });

            /* ★ Inject ECharts snapshots ── kunci Safari ── */
            for (const [id, dataUrl] of Object.entries(ecSnaps || {})) {
                const container = clonedDoc.getElementById(id);
                if (!container || !dataUrl) continue;
                const orig = document.getElementById(id);
                const h = orig ? Math.max(orig.scrollHeight, orig.offsetHeight, 240) : 280;
                container.innerHTML = '';
                const img = clonedDoc.createElement('img');
                img.src = dataUrl;
                img.style.cssText = `width:100%;height:${h}px;display:block;object-fit:contain;background:#fff;`;
                container.appendChild(img);
                container.style.cssText += 'display:block!important;opacity:1!important;visibility:visible!important;overflow:visible!important;';

                /* Fix parent container height */
                const parent = container.parentElement;
                if (parent) {
                    parent.style.height   = 'auto';
                    parent.style.minHeight = h + 'px';
                    parent.style.overflow = 'visible';
                }
            }

            /* ★ Inject ApexCharts trend snapshot ── */
            if (apexSnap) {
                const apexEl = clonedDoc.getElementById('chTrend');
                if (apexEl) {
                    apexEl.innerHTML = '';
                    apexEl.style.cssText = 'display:block!important;width:100%;height:380px;opacity:1!important;visibility:visible!important;';
                    const img = clonedDoc.createElement('img');
                    img.src = apexSnap;
                    img.style.cssText = 'width:100%;height:380px;object-fit:contain;display:block;background:#fff;';
                    apexEl.appendChild(img);

                    /* Fix parent height */
                    const parent = apexEl.parentElement;
                    if (parent) {
                        parent.style.height   = 'auto';
                        parent.style.minHeight = '380px';
                        parent.style.overflow = 'visible';
                    }
                }
            }
        };
    }

    /* ── Core capture: snapshot dulu, baru html2canvas ── */
    async function _doCapture(el, bg, ecSnaps, apexSnap) {
        window.scrollTo(0, 0);
        await new Promise(r => setTimeout(r, 80));

        /* Force visible */
        el.querySelectorAll('.card,.kpi-card-hover,[class*="col-"]')
          .forEach(e => {
              e.style.opacity    = '1';
              e.style.transform  = 'none';
              e.style.visibility = 'visible';
          });

        /* Decode semua snapshot sebelum html2canvas */
        const allSnaps = [
            ...Object.values(ecSnaps || {}),
            apexSnap
        ].filter(Boolean);

        await Promise.allSettled(allSnaps.map(src =>
            new Promise(resolve => {
                const img = new Image();
                img.onload = img.onerror = resolve;
                setTimeout(resolve, 4000);
                img.src = src;
            })
        ));

        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
        await new Promise(r => setTimeout(r, 200));

        return await html2canvas(el, {
            scale          : 2,
            useCORS        : true,
            allowTaint     : false,
            backgroundColor: bg || '#f1f5f9',
            logging        : false,
            removeContainer: true,
            imageTimeout   : 10000,
            scrollX        : 0,
            scrollY        : 0,
            width          : el.offsetWidth,
            height         : el.scrollHeight,
            onclone        : d => _makeOnClone(ecSnaps, apexSnap)(d),
            ignoreElements : e =>
                e.hasAttribute('data-html2canvas-ignore') ||
                ['exportToast','sntPopup','sntPanelOverlay','sntPlatPicker'].includes(e.id) ||
                (e.tagName === 'IMG'
                    && e.src
                    && !e.src.startsWith('data:')
                    && !e.src.startsWith('blob:')),
        });
    }

    const _stamp = () => new Date().toISOString().slice(0, 10).replace(/-/g, '');

    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — Sentiment Analysis' + (label ? '  ·  ' + label : ''), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', {
            day:'2-digit', month:'short', year:'numeric',
            hour:'2-digit', minute:'2-digit',
        });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text(`Halaman ${page} / ${total}`, pW / 2, pH - 3, { align: 'center' });
    }

    function _addCanvasAsPage(pdf, canvas, margin, pW, pH, label, page, total) {
        _drawHeader(pdf, pW, pH, label, page, total);
        const usableW = pW - margin * 2, usableH = pH - 14 - 8;
        const ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        const dw = canvas.width * ratio, dh = canvas.height * ratio;
        pdf.addImage(
            canvas.toDataURL('image/png'), 'PNG',
            margin + (usableW - dw) / 2, 14,
            dw, dh
        );
    }

    function _paginate(pdf, canvas, margin, pW, pH, label) {
        const uw = pW - margin * 2, uh = pH - 14 - 8;
        const ratio  = uw / canvas.width, sliceH = uh / ratio;
        const total  = Math.max(1, Math.ceil((canvas.height * ratio) / uh));
        let srcY = 0, pg = 1;
        while (srcY < canvas.height) {
            if (pg > 1) pdf.addPage();
            _drawHeader(pdf, pW, pH, label, pg, total);
            const srcSlice = Math.min(sliceH, canvas.height - srcY);
            const slice    = document.createElement('canvas');
            slice.width    = canvas.width;
            slice.height   = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(
                canvas, 0, srcY, canvas.width, srcSlice,
                0, 0,           canvas.width, srcSlice
            );
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, srcSlice * ratio);
            srcY += srcSlice; pg++;
        }
        return total;
    }

    const _cardLabels = {
        overview  : 'Total Mentions by Sentiments',
        sov       : 'Share of Voice',
        mass      : 'Sentiments in Mass Media',
        social    : 'Sentiments in Social Media',
        bytype    : 'Sentiments by Media Types',
        byplat    : 'Sentiments by Media Platforms',
        masspie   : 'Mass Media SOV',
        socialpie : 'Social Media SOV',
        trend     : 'Sentiment Trends',
        weekday   : 'Sentiments by Weekday',
        hour      : 'Sentiments by Hour',
    };

    /* ── Per-card export ── */
    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error');       return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        try {
            const area = document.getElementById(areaId);
            if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

            /* Snapshot hanya chart yang ada di dalam card ini */
            const ecSnaps   = await _getAllEChartSnapshots(area);
            const apexSnap  = area.contains(document.getElementById('chTrend'))
                ? await _getApexSnapshot()
                : null;

            const canvas = await _doCapture(area, '#ffffff', ecSnaps, apexSnap);
            const fname  = `sentiment_${cardKey}_${OV_PID}_${_stamp()}`;
            const label  = _cardLabels[cardKey] || cardKey;

            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = fname + '.png';
                a.href     = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const landscape = canvas.width > canvas.height * 1.2;
                const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
                const pW  = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
                const M   = 10, uw = pW - M * 2, uh = pH - 14 - 8;
                const fitsOne = (canvas.height * (uw / canvas.width)) <= uh;

                if (fitsOne) {
                    _addCanvasAsPage(pdf, canvas, M, pW, pH, label, 1, 1);
                } else {
                    _paginate(pdf, canvas, M, pW, pH, label);
                }
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[SNTExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally { _btnState(btn, false); }
    }

    /* ── Export seluruh halaman (2-page PDF) ── */
    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error');       return; }

        const btnPdf = document.getElementById('pageExportPdfBtn'),
              btnImg = document.getElementById('pageExportImgBtn');
        _btnState([btnPdf, btnImg], true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF 2 halaman…' : 'Mengambil gambar halaman…', 'default', 99999);

        try {
            window.scrollTo({ top: 0 });
            const stamp = _stamp();

            /* Ambil semua snapshots sekali sebelum capture */
            _toast('Menyiapkan snapshot charts…', 'default', 99999);
            const ecSnaps  = await _getAllEChartSnapshots(null); /* null = semua */
            const apexSnap = await _getApexSnapshot();

            if (type === 'image') {
                const area = document.getElementById('pageExportArea');
                if (!area) throw new Error('pageExportArea tidak ditemukan');
                const canvas = await _doCapture(area, '#f1f5f9', ecSnaps, apexSnap);
                const a      = document.createElement('a');
                a.download   = `sentiment_analysis_${OV_PID}_${stamp}.png`;
                a.href       = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
                return;
            }

            /* PDF: 2 halaman terpisah */
            const pg1El = document.getElementById('exportPage1'),
                  pg2El = document.getElementById('exportPage2');
            if (!pg1El || !pg2El) throw new Error('Wrapper #exportPage1 / #exportPage2 tidak ditemukan.');

            _toast('Menangkap Halaman 1…', 'default', 99999);
            const canvas1 = await _doCapture(pg1El, '#f1f5f9', ecSnaps, apexSnap);

            _toast('Menangkap Halaman 2…', 'default', 99999);
            const canvas2 = await _doCapture(pg2El, '#f1f5f9', ecSnaps, apexSnap);

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
            const pW  = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
            _addCanvasAsPage(pdf, canvas1, 10, pW, pH, 'KPI & Overview Charts', 1, 2);
            pdf.addPage();
            _addCanvasAsPage(pdf, canvas2, 10, pW, pH, 'SOV, Trend & Time Analysis', 2, 2);
            pdf.save(`sentiment_analysis_${OV_PID}_${stamp}.pdf`);
            _toast('PDF 2 halaman berhasil diunduh!', 'success');

        } catch(err) {
            console.error('[SNTExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState([btnPdf, btnImg], false);
        }
    }

    return { run, runCard };
})();
    /* ══════════════════════════════════════════════════════
       SENTIMENT MENTION POPUP
    ══════════════════════════════════════════════════════ */
    /* ══════════════════════════════════════════════════════
       SENTIMENT MENTION POPUP & DETAIL (SYNC WITH MEDIA STAT)
    ══════════════════════════════════════════════════════ */
    const SNTPlatMeta = {
      doc:   { label:'Online News',  color:'#0284c7' }, twit: { label:'X ( Twitter )', color:'#1d9bf0' },
      fb:    { label:'Facebook',     color:'#1877f2' }, ig:   { label:'Instagram',     color:'#e1306c' },
      yt:    { label:'YouTube',      color:'#ff0000' }, tiktok:{ label:'TikTok',       color:'#111827' },
      neg:   { label:'Negative',     color:'#ef4444' }, pos:  { label:'Positive',      color:'#2FC6F6' },
      neu:   { label:'Neutral',      color:'#94a3b8' },
    };
    
    const sntEsc = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');

    const SNTPopup = {
      _cache:{}, _allItems:[], _curSent:'all', _curPlat:null, _curSd:null, _curEd:null,
      init() {
        document.addEventListener('mousedown', e=>{ const pp=document.getElementById('sntPlatPicker'); if(pp?.classList.contains('visible')&&!pp.contains(e.target)) pp.classList.remove('visible'); });
        document.addEventListener('keydown', e=>{ if(e.key==='Escape') this.close(); });
      },
      async open(platform,sentiment,customStartDate,customEndDate) {
        const popup=document.getElementById('sntPopup'), overlay=document.getElementById('sntPanelOverlay');
        if(!popup) return;
        SNTDetail.close();
        this._curPlat=platform; this._curSent=sentiment||'all';
        this._curSd = customStartDate || SNTCfg.sd;
        this._curEd = customEndDate || SNTCfg.ed;

        let dotColor,title;
        if (sentiment&&sentiment!=='all') {
          dotColor=SNTPlatMeta[sentiment]?.color||'#038047';
          const sentLabel={neg:'Negative',pos:'Positive',neu:'Neutral'}[sentiment]||sentiment;
          const platLabel=platform==='all'?'All Media':platform==='social'?'Social Media':(SNTPlatMeta[platform]?.label||platform);
          title=`${sentLabel} — ${platLabel}`;
        } else {
          dotColor=platform==='all'||platform==='social'?'#038047':(SNTPlatMeta[platform]?.color||'#038047');
          title=platform==='all'?'All Media':platform==='social'?'Social Media':(SNTPlatMeta[platform]?.label||platform);
        }
        document.getElementById('sntPopDot').style.background=dotColor;
        document.getElementById('sntPopTitle').textContent=title;
        document.getElementById('sntPopMeta').textContent = (this._curSd === this._curEd) ? this._curSd : (this._curSd + ' – ' + this._curEd);
        document.getElementById('sntPopCount').textContent='…';
        
        document.querySelectorAll('.sntp-sent-tab').forEach(b=>b.classList.toggle('active',b.dataset.s===this._curSent));
        const list=document.getElementById('sntPopList');
        list.innerHTML=`<div class="sntp-loading"><div class="sntp-spinner"></div>Memuat mentions…</div>`;
        popup.classList.remove('hiding'); popup.classList.add('show');
        if(overlay){overlay.classList.remove('hiding');overlay.classList.add('show');}
        document.body.style.overflow='hidden';
        
        const cacheKey=`${SNTCfg.pid}_${platform}_${this._curSd}_${this._curEd}`;
        try {
          if(!this._cache[cacheKey]) this._cache[cacheKey]=await this._fetch(platform, this._curSd, this._curEd);
          this._allItems=this._cache[cacheKey];
          this._renderFiltered(list);
        } catch(err) {
          list.innerHTML=`<div class="sntp-loading" style="color:#94a3b8;">Gagal memuat data</div>`;
          document.getElementById('sntPopCount').textContent='0';
        }
      },
      openSentiment(sentiment, customStartDate, customEndDate){ this.open('all', sentiment, customStartDate, customEndDate); },
      openPlatform(platform,sentiment, customStartDate, customEndDate){ const pp=document.getElementById('sntPlatPicker'); if(pp) pp.classList.remove('visible'); this.open(platform, sentiment||'all', customStartDate, customEndDate); },
      filterSent(sent) {
        this._curSent=sent;
        document.querySelectorAll('.sntp-sent-tab').forEach(b=>b.classList.toggle('active',b.dataset.s===sent));
        this._renderFiltered(document.getElementById('sntPopList'));
      },
      close() {
        const popup=document.getElementById('sntPopup'), overlay=document.getElementById('sntPanelOverlay');
        if(!popup||!popup.classList.contains('show')) return;
        SNTDetail.close();
        const dpBody=document.getElementById('sntDpBody');
        if(dpBody) dpBody.querySelectorAll('iframe').forEach(f=>{f.src='';f.remove();});
        popup.classList.add('hiding');
        if(overlay) overlay.classList.add('hiding');
        setTimeout(()=>{popup.classList.remove('show','hiding');if(overlay) overlay.classList.remove('show','hiding');document.body.style.overflow='';},240);
      },
      async _fetch(platform, sd, ed) {
        const useSd = sd || this._curSd || SNTCfg.sd;
        const useEd = ed || this._curEd || SNTCfg.ed;
        const platforms = platform==='social' ? ['twit','fb','ig','yt','tiktok'] : platform==='all' ? ['doc','twit','fb','ig','yt','tiktok'] : [platform];
        const results = await Promise.allSettled(platforms.map(p=>this._fetchOne(p, useSd, useEd)));
        let merged = [];
        results.forEach((r, i) => {
            if (r.status === 'fulfilled') {
                r.value.forEach(item => { if (!item._type) item._type = platforms[i]; });
                merged = merged.concat(r.value);
            }
        });
        merged.sort((a,b)=>(b.date_created||b.created_at||'').localeCompare(a.date_created||a.created_at||''));
        return merged;
      },
      async _fetchOne(platform, sd, ed) {
        const useSd = sd || this._curSd || SNTCfg.sd;
        const useEd = ed || this._curEd || SNTCfg.ed;
        const q=`project_id=${SNTCfg.pid}&start_date=${useSd}&end_date=${useEd}&rows=500&start=0`;
        const _normArr = d => {
          if (Array.isArray(d?.data?.data)) return d.data.data;
          if (Array.isArray(d?.data)) return d.data;
          if (Array.isArray(d?.statuses)) return d.statuses;
          if (Array.isArray(d?.results)) return d.results;
          if (Array.isArray(d?.posts)) return d.posts;
          if (Array.isArray(d)) return d;
          if (d?.data && typeof d.data === 'object' && !Array.isArray(d.data)) {
            const vals = Object.values(d.data);
            if (vals.length && typeof vals[0] === 'object') return vals;
          }
          return [];
        };

        if(platform==='ig'){
          for(const sub of ['postbylike', 'postbycomment', 'postbydate', '']){
            try{ 
              const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),15000); 
              const res=await fetch(`/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`,{signal:ctrl.signal}); 
              clearTimeout(tid); if(!res.ok) continue; 
              const d=await res.json(); let items = _normArr(d);
              if(items.length>0){ items.forEach(it=>{it._type='ig';}); return items; } 
            } catch(e){continue;}
          } return [];
        }
        if(platform==='yt'){
          for(const sub of ['postbylike', 'postbyview', 'postbydate', 'postbycomment', '']){
            try{ 
              const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),15000); 
              const res=await fetch(`/mk/api/news/ytb-top-status?${q}${sub?'&sub='+sub:''}`,{signal:ctrl.signal}); 
              clearTimeout(tid); if(!res.ok) continue; 
              const d=await res.json(); let items = _normArr(d);
              if(items.length>0){ items.forEach(it=>{it._type='yt';}); return items; } 
            } catch(e){continue;}
          } return [];
        }
        if(platform==='doc'){
          const docQ=`project_id=${SNTCfg.pid}&start_date=${useSd}&end_date=${useEd}&rows=500&start=0&media=doc`;
          try{
            const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),25000);
            const res = await fetch(`/mk/api/news/articles?${docQ}`, {signal:ctrl.signal}); clearTimeout(tid);
            if(!res.ok) return [];
            const d = await res.json();
            const raw = Array.isArray(d?.data)?d.data:(Array.isArray(d)?d:[]);
            return raw.map(i => ({
                ...i, _type: 'doc',
                content: i.content || i.summary || i.text || '',
                title: i.title || i.subject || 'Untitled',
                publisher: i.publisher || i.name || i.source_name || '',
                url: i.url || i.link || i.post_url || i.article_url || '',
                class_sentiment: String(i.class_sentiment || i.sentiment_class || i.sentiment || '0')
            }));
          }catch(e){ return []; }
        }
        
        const eps={twit:`/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,fb:`/mk/api/news/fb-top-status?${q}&sub=fblike`,tiktok:`/mk/api/news/tiktok-top-status?${q}&sub=postbylike`};
        const url=eps[platform]; if(!url) return [];
        const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),20000);
        try {
          const res=await fetch(url,{signal:ctrl.signal}); clearTimeout(tid); 
          if(!res.ok){ if(platform==='twit') throw new Error('Twitter fail'); return []; }
          const d=await res.json(); let items = _normArr(d);
          
          // Twitter Fallback
          if (platform === 'twit' && items.length === 0) {
            try {
              const r2 = await fetch(`/mk/api/news/mentions?${q}`);
              const d2 = await r2.json();
              let allM = _normArr(d2);
              items = allM.filter(m => {
                const tc=String(m.tcode||'').toLowerCase(), mt=String(m.media_type||'').toLowerCase(), id2=String(m.id||m.docid||'').toLowerCase(), url2=String(m.url||'').toLowerCase();
                return tc==='twit'||tc==='rt'||mt==='twit'||mt==='twitter'||mt==='x'||id2.startsWith('tw-')||url2.includes('twitter.com')||url2.includes('x.com');
              });
            } catch(e2) {}
          }
          items.forEach(it=>{ it._type=platform; });
          return items;
        } catch(e) { return []; }
      },
      _normSent(item){ 
        const raw=String(item.sentiment_class||item.class_sentiment||item.sentiment||item.sentiment_label||item.sentiment_str||'0').toLowerCase().trim(); 
        if(['1','positive','positif','pos'].includes(raw)) return 'pos'; 
        if(['-1','2','negative','negatif','neg'].includes(raw)) return 'neg'; 
        return 'neu'; 
      },
      _getFiltered(){ return this._curSent==='all'?this._allItems:this._allItems.filter(item=>this._normSent(item)===this._curSent); },
      _renderFiltered(list) {
        const items=this._getFiltered();
        document.getElementById('sntPopCount').textContent=items.length.toLocaleString();
        const badge=document.getElementById('sntPopCount');
        const bColors={neg:'#ef4444',pos:'#2FC6F6',neu:'#94a3b8',all:'var(--primary)'};
        if(badge) badge.style.background=bColors[this._curSent]||'var(--primary)';
        list.innerHTML=''; list.scrollTop=0;
        this._renderedCount=0;
        this.loadMore(list);
      },
      loadMore(list=document.getElementById('sntPopList')) {
        const items=this._getFiltered();
        const btn=document.getElementById('sntPopLoadMoreBtn'); if(btn) btn.remove();
        if(!items.length){list.innerHTML=`<div class="sntp-loading" style="color:#94a3b8;padding:50px 20px;text-align:center;">Tidak ada mention untuk filter ini</div>`;return;}
        
        const limit=20, start=this._renderedCount||0, chunk=items.slice(start,start+limit);
        const getPlat=item=>{ if(item._type) return item._type; return this._curPlat||'doc'; };
        
        const html=chunk.map(item=>{
          const plat=getPlat(item),meta=SNTPlatMeta[plat]||{color:'#038047'},color=meta.color;
          const ao0 = (()=>{ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })();
          const rawName = (()=>{
            if (plat==='fb')     return item.from_name || item.page_name || item.author_name || ao0?.name || item.author_handle || null;
            if (plat==='ig')     return item.username || item.user_name || null;
            if (plat==='tiktok') return item.author_nickname || item.nickname || ao0?.nickname || item.author_name || null;
            if (plat==='yt')     return item.channel_title || item.channel_name || item.author_name || null;
            if (plat==='twit')   return item.name || ao0?.name || ao0?.scr_name || item.author_name || item.author_scr_name || null;
            return null;
          })();
          let name = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || item.name || '').trim();
          if(!name || name.toLowerCase()==='tidak diketahui' || name.toLowerCase()==='unknown' || /^\d{8,}$/.test(name)){
            const alt = (item.author_handle||item.author_scr_name||item.screen_name||ao0?.scr_name||ao0?.username||item.username||item.nickname||'').trim();
            if(alt && !/^\d{8,}$/.test(alt)) name = alt;
            else if(!name) name = 'Tidak diketahui';
          }
          const dName = name;
          const rawH = ((plat==='ig'?item.username:'')||item.author_handle||item.author_scr_name||item.screen_name||ao0?.scr_name||item.username||'').trim();
          const handle = (()=>{
            if (!rawH) return '';
            const w = ['twit','ig','tiktok'].includes(plat) ? (rawH.startsWith('@')?rawH:'@'+rawH) : rawH;
            return w.replace(/^@/,'').toLowerCase() === dName.toLowerCase() ? '' : w;
          })();

          const text = (()=>{
            if (plat === 'doc') {
              const c = (item.content||'').replace(/<[^>]*>/g,'').trim();
              return c ? c.slice(0,155) : (item.title||'').slice(0,155);
            }
            return (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,155);
          })();
          const artTitle = (plat === 'doc') ? (item.title||'').replace(/<[^>]*>/g,'').trim() : '';
          const url = (item.url||item.link||'').trim();
          const av = (item.avatar_url||item.profile_image_url||item.author_image||ao0?.image||item.profile_image||item.thumbnail||item.picture||'').trim();

          const words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
          const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||dName[0]||'?')).toUpperCase().replace(/['"]/g,'');
          const avHtml=(av&&(av.startsWith('http://')||av.startsWith('https://')))?`<img src="${sntEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}'">`:(plat==='doc'?`<i class="ph ph-newspaper" style="font-size:14px;color:#fff;"></i>`:ini);
          
          const sent=this._normSent(item),sentLbl={neg:'Neg',pos:'Pos',neu:'Neu'}[sent]||'Neu';
          const dt=(item.date_created||item.created_at||item.publish_date||'').split('T')[0];
          const itemData=sntEsc(JSON.stringify(item));

          /* Doc view */
          if(plat==='doc' && artTitle){
            return `<div class="sntp-item" data-item='${itemData}' data-plat="${plat}" onclick="SNTPopup._onItemClick(this)">
              <div class="sntp-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
              <div class="sntp-item-body">
                <div class="sntp-item-author" style="font-size:10px;color:#64748b;font-weight:600;">${sntEsc(dName)}</div>
                <div style="font-size:12px;font-weight:700;color:#1e293b;line-height:1.35;margin:3px 0 4px;">${sntEsc(artTitle.slice(0,100))}</div>
                <div class="sntp-item-text" style="font-size:11px;">${sntEsc(text||'(tidak ada konten)')}</div>
                <div class="sntp-item-footer" style="justify-content:space-between;flex-wrap:nowrap;margin-top:2px;">
                  <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                    <span class="sntp-sent-badge sntp-sent-badge--${sent}">${sentLbl}</span>
                    <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${color};flex-shrink:0;"></span>
                    <span style="font-size:10px;font-weight:600;color:${color};">${meta.label}</span>
                    ${dt?`<span style="margin-left:2px;font-size:10px;color:var(--slate-400);">${dt}</span>`:''}
                  </div>
                </div>
              </div>
            </div>`;
          }

          /* Social view (TIDAK ADA THUMBNAIL) */
          return `<div class="sntp-item" data-item='${itemData}' data-plat="${plat}" onclick="SNTPopup._onItemClick(this)">
            <div class="sntp-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
            <div class="sntp-item-body">
              <div class="sntp-item-author">${sntEsc(dName)}</div>
              ${handle?`<div class="sntp-item-handle">${sntEsc(handle)}</div>`:''}
              <div class="sntp-item-text">${sntEsc(text||'(tidak ada konten)')}</div>
              <div class="sntp-item-footer" style="justify-content:space-between;flex-wrap:nowrap;margin-top:2px;">
                <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                  <span class="sntp-sent-badge sntp-sent-badge--${sent}">${sentLbl}</span>
                  <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${color};flex-shrink:0;"></span>
                  <span style="font-size:10px;font-weight:600;color:${color};">${meta.label}</span>
                  ${dt?`<span style="margin-left:2px;font-size:10px;color:var(--slate-400);">${dt}</span>`:''}
                </div>
              </div>
            </div>
          </div>`;
        }).join('');
        
        list.insertAdjacentHTML('beforeend',html);
        this._renderedCount=start+chunk.length;
        if(items.length>this._renderedCount){
          list.insertAdjacentHTML('beforeend',`<div id="sntPopLoadMoreBtn" style="padding:16px;text-align:center;background:var(--slate-50);border-top:1px dashed var(--slate-200);"><button id="_doLMBtn" onclick="SNTPopup.loadMore()" style="background:var(--primary);color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;box-shadow:0 2px 4px rgba(3,128,71,.2);" onmouseover="this.style.filter='brightness(1.1)';" onmouseout="this.style.filter='';">Muat Lebih Banyak</button></div>`);
        }
      },
      _onItemClick(el) {
        try{ 
            const raw = el.getAttribute('data-item'); 
            const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"').replace(/&#39;/g, "'")); 
            SNTDetail.open(item, el.dataset.plat || this._curPlat || 'doc'); 
        } catch(e){ console.warn('SNT Detail parse error:', e); }
      }
    };

    const SNTDetail = {
      open(item,platform) {
        const panel=document.getElementById('sntDetailPanel'),body=document.getElementById('sntDpBody'),title=document.getElementById('sntDpTitle');if(!panel||!body)return;
        const meta=SNTPlatMeta[platform]||{label:platform,color:'#038047'};
        const SM={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
        const sent=SM[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()]||'neu';
        const SLBL={pos:'Positive',neg:'Negative',neu:'Neutral'};
        const SBGS={pos:'sntdp-sent-badge--pos',neg:'sntdp-sent-badge--neg',neu:'sntdp-sent-badge--neu'};
        
        const ao3=(()=>{ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })();
        const rawName=(()=>{if(platform==='fb')return item.from_name||item.page_name||item.author_name||ao3?.name||item.author_handle||null;if(platform==='ig')return item.username||null;if(platform==='tiktok')return item.author_nickname||item.nickname||ao3?.nickname||null;if(platform==='yt')return item.channel_title||item.channel_name||null;if(platform==='twit')return item.name||ao3?.name||ao3?.scr_name||item.author_name||item.author_scr_name||null;return null;})();
        let name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'').trim();
        if(!name || /^\d{5,}$/.test(name) || name.toLowerCase()==='unknown' || name.toLowerCase()==='tidak diketahui'){
          const altN=(item.author_handle||item.author_scr_name||item.screen_name||ao3?.scr_name||ao3?.username||item.username||item.nickname||'').trim();
          if(altN && !/^\d{5,}$/.test(altN)) name=altN;
          else if(!name) name='Tidak diketahui';
        }
        const displayName = name;
        const handle=((platform==='ig'?item.username:'')||item.author_handle||item.author_scr_name||item.screen_name||ao3?.scr_name||item.username||'').trim();
        const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
        const av=(item.avatar_url||item.profile_image_url||ao3?.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
        const url=item.url||item.link||'';const dt=item.date_created||item.created_at||item.publish_date||'';
        title.textContent=displayName;
        const words=displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
        const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||displayName[0]||'?')).toUpperCase().replace(/['"]/g,'');
        const avHtml=(av&&av.startsWith('http'))?`<img src="${sntEsc(av)}" onerror="this.parentElement.textContent='${ini}';">`:ini;
        let dtFmt='';if(dt){try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];}}
        
        // Exact Detail Media Logic dari Media Statistic
        let mediaHtml='';
        if(platform==='yt'){
            let ytId=''; if(url){ const m=url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|\/vi\/)([a-zA-Z0-9_-]{11})/); if(m) ytId=m[1]; }
            if(!ytId){
                var flds = ['video_id','youtube_id','yt_id','id_str','post_id','docid','id','sub_id'];
                for(var i=0; i<flds.length; i++){
                    var f = flds[i]; var v = item[f]; if(!v) continue;
                    var s = String(v).replace(/^(yt[-_])/i, '');
                    if(s.length===11){ ytId=s; break; }
                }
            }
            if(!ytId && item.snippet) ytId = (item.snippet.videoId || (item.snippet.resourceId && item.snippet.resourceId.videoId) || '');
            const thumb = item.thumbnail || item.image_url || item.picture || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '');
            if(ytId){
                const eid = `yt_${ytId}_${Date.now()}`;
                mediaHtml = `<div id="${eid}" class="sntdp-media-wrap" style="position:relative;cursor:pointer;background:#f1f5f9;height:220px;"
                    onclick="document.getElementById('${eid}').innerHTML='<iframe width=\\'100%\\' height=\\'220\\' src=\\'https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\' frameborder=\\'0\\' allowfullscreen style=\\'border-radius:6px;\\'></iframe>'; document.getElementById('${eid}').style.height='auto';">
                    <img src="${thumb||`https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'">
                </div>`;
            } else if(thumb) {
                mediaHtml=`<div class="sntdp-media-wrap" style="background:#f1f5f9;"><img src="${sntEsc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius-sm);"></div>`;
            }
        }
        else if(platform==='tiktok'){
            let tid=''; 
            if(item.video_id) tid=String(item.video_id);
            else if(item.aweme_id) tid=String(item.aweme_id);
            else if(url && url.match(/\/video\/(\d+)/)) tid=url.match(/\/video\/(\d+)/)[1];
            else if(item.id) { const m=String(item.id).match(/(\d{15,})/); if(m) tid=m[1]; }
            
            const thumb=item.image_url||item.thumbnail||item.media_url||item.picture||'';
            if(tid) {
                const eid = `tt_${tid}_${Date.now()}`;
                mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;background:#f1f5f9;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:240px;margin-bottom:10px;"
                    onclick="document.getElementById('${eid}').innerHTML='<iframe src=\\'https://www.tiktok.com/embed/v2/${tid}\\' style=\\'width:100%;height:480px;border:none;display:block;\\' allow=\\'autoplay; encrypted-media; picture-in-picture\\' allowfullscreen></iframe>'; document.getElementById('${eid}').style.height='auto';">
                    ${thumb ? `<img src="${sntEsc(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;pointer-events:none;">` : ''}
                    <div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.1);"><i class="ph ph-play-circle" style="font-size:48px;color:#fff;filter:drop-shadow(0 2px 8px rgba(0,0,0,0.3));"></i></div>
                </div>`;
            } else if(thumb) {
                mediaHtml=`<div class="sntdp-media-wrap" style="background:#f1f5f9;"><img src="${sntEsc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius-sm);"></div>`;
            }
        }
        else {
            const thumb=item.image_url||item.thumbnail||item.media_url||item.picture||'';
            if(thumb) mediaHtml=`<div class="sntdp-media-wrap" style="background:#f1f5f9;"><img src="${sntEsc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius-sm);"></div>`;
        }

        const statsMap={twit:[['Views',item.view_cnt||item.num_views||0],['Retweet',item.rt||item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0]],fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],ig:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],yt:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0]],tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]};
        const stats=statsMap[platform]||[];
        const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="sntdp-stats-grid">${stats.map(([l,v])=>`<div class="sntdp-stat-box"><div class="sntdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="sntdp-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
        const handleDisp=handle&&handle.replace('@','').toLowerCase()!==displayName.toLowerCase().slice(0,handle.replace('@','').length)?(handle.startsWith('@')?handle:'@'+handle):'';
        
        let sourceUrl=item.url||item.link||item.post_url||item.article_url||item.source_url||item.permalink||item.news_url||item.article_link||item.full_url||item.web_url||item.source||item.original_url||'';
        if(!sourceUrl){
            if(platform==='twit'){ 
                const ao=(()=>{ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })(); 
                const scr=(item.author_scr_name||item.screen_name||ao?.scr_name||ao?.username||'').replace(/^@/,''); 
                const sid=item.sub_id||item.tweet_id||item.id_str||item.id||''; 
                if(scr&&sid) sourceUrl=`https://twitter.com/${scr}/status/${sid}`; else if(scr) sourceUrl=`https://twitter.com/${scr}`; 
            }
            else if(platform==='ig'){ 
                const sc=item.shortcode||item.code||item.media_id||''; 
                if(sc) sourceUrl=`https://www.instagram.com/p/${sc}/`; else if(item.username) sourceUrl=`https://www.instagram.com/${item.username}/`; 
            }
            else if(platform==='yt'){ 
                let yi=''; if(url){ const m=url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|\/vi\/)([a-zA-Z0-9_-]{11})/); if(m) yi=m[1]; }
                if(!yi){
                    var flds = ['video_id','youtube_id','yt_id','id_str','post_id','docid','id','sub_id'];
                    for(var i=0; i<flds.length; i++){
                        var f = flds[i]; var v = item[f]; if(!v) continue;
                        var s = String(v).replace(/^(yt[-_])/i, '');
                        if(s.length===11){ yi=s; break; }
                    }
                }
                if(yi) sourceUrl=`https://www.youtube.com/watch?v=${yi}`; else if(item.channel_id) sourceUrl=`https://www.youtube.com/channel/${item.channel_id}`; 
            }
            else if(platform==='tiktok'){ 
                const ti=item.video_id||item.aweme_id||item.id||''; 
                const ni=item.author_nickname||item.nickname||item.unique_id||item.author?.unique_id||''; 
                if(ti&&ni) sourceUrl=`https://www.tiktok.com/@${ni}/video/${ti}`; else if(ti) sourceUrl=`https://vm.tiktok.com/${ti}`; 
            }
            else if(platform==='fb'){ 
                const fbUrl2=item.url||item.link||item.post_url||'';
                if(fbUrl2 && fbUrl2.startsWith('http') && (fbUrl2.includes('facebook.com')||fbUrl2.includes('fb.com')||fbUrl2.includes('fb.watch'))){
                    sourceUrl=fbUrl2;
                } else {
                    const pi=item.post_id||item.story_id||item.id||(item.docid||'').replace(/^fb-/,'')||''; 
                    const pn=item.page_name||item.from_name||item.author_name||item.author_handle||ao3?.username||''; 
                    if(pn&&pi) sourceUrl=`https://www.facebook.com/${pn}/posts/${pi}`;
                    else if(pn) sourceUrl=`https://www.facebook.com/${pn}`;
                }
            }
        }

        let sourceBtnHtml = '';
        if (platform === 'doc') {
            const _cleanUrl = v => (v && typeof v === 'string' && v !== 'null' && v !== 'undefined' && v.trim() !== '' && v.startsWith('http')) ? v.trim() : '';
            const docSourceUrl = _cleanUrl(item.url)||_cleanUrl(item.link)||_cleanUrl(item.article_url)||_cleanUrl(item.source_url)||_cleanUrl(item.news_url)||_cleanUrl(item.permalink)||_cleanUrl(item.web_url)||_cleanUrl(item.full_url)||_cleanUrl(item.original_url)||'';
            if (docSourceUrl) {
                sourceBtnHtml = `<a href="${sntEsc(docSourceUrl)}" target="_blank" rel="noopener noreferrer" class="sntdp-link-btn" style="background:#0284c7;"><i class="ph ph-newspaper"></i> Baca Artikel Asli</a>`;
            } else {
                sourceBtnHtml = `<div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;font-size:11px;color:#991b1b;display:flex;align-items:flex-start;gap:7px;line-height:1.4;"><i class="ph ph-warning-circle" style="font-size:15px;flex-shrink:0;"></i><span>Link artikel spesifik tidak tersedia dari sumber data.</span></div>`;
            }
            const artTitle = (item.title||'').replace(/<[^>]*>/g,'').trim();
            body.innerHTML=`
                <div class="sntdp-avatar-row">
                    <div class="sntdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);"><i class="ph ph-newspaper" style="font-size:20px;color:#fff;"></i></div>
                    <div>
                        <div class="sntdp-author-name">${sntEsc(displayName)}</div>
                        ${artTitle ? `<div style="font-size:13px;font-weight:700;color:#1e293b;margin-top:4px;line-height:1.35;">${sntEsc(artTitle)}</div>` : ''}
                        <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
                    </div>
                </div>
                ${dtFmt?`<div class="sntdp-meta-row"><span>${dtFmt}</span></div>`:''}
                <div class="sntdp-sent-badge ${SBGS[sent]}">${SLBL[sent]}</div>
                ${content?`<div class="sntdp-content-text">${sntEsc(content)}</div>`:''}
                ${statsHtml}
                ${sourceBtnHtml}`;
        } else {
            if (sourceUrl) {
                sourceBtnHtml = `<a href="${sntEsc(sourceUrl)}" target="_blank" rel="noopener noreferrer" class="sntdp-link-btn"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>`;
            } else {
                sourceBtnHtml = `
                    <div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;font-size:11px;color:#991b1b;display:flex;align-items:flex-start;gap:7px;line-height:1.4;">
                        <i class="ph ph-warning-circle" style="font-size:15px;flex-shrink:0;"></i>
                        <span>Link sumber spesifik tidak tersedia dari sumber data.</span>
                    </div>`;
            }
            body.innerHTML=`
                <div class="sntdp-avatar-row">
                    <div class="sntdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                    <div>
                        <div class="sntdp-author-name">${sntEsc(displayName)}</div>
                        ${handleDisp?`<div class="sntdp-author-handle">${sntEsc(handleDisp)}</div>`:''}
                        <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
                    </div>
                </div>
                ${dtFmt?`<div class="sntdp-meta-row"><span>${dtFmt}</span></div>`:''}
                <div class="sntdp-sent-badge ${SBGS[sent]}">${SLBL[sent]}</div>
                ${mediaHtml}
                ${content?`<div class="sntdp-content-text">${sntEsc(content)}</div>`:''}
                ${statsHtml}
                ${sourceBtnHtml}`;
        }
        panel.classList.add('visible');
      },
      close(){ 
        const panel=document.getElementById('sntDetailPanel');if(!panel)return;
        panel.classList.remove('visible');
        panel.querySelectorAll('iframe').forEach(f=>{try{f.src=f.src;}catch(e){}});
      }
    };
	 
/* ══ Patch Chart Click Handlers ══ */
    const _origOverviewBar = renderOverviewBar;
    window.renderOverviewBar = function(){
      _origOverviewBar();
      const c = SNTCharts._i['chOverview']; if (!c) return;
      c.on('click', p => {
        if (p.componentType !== 'series') return;
        const sm = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
        SNTPopup.open('all', sm[p.name] || 'all');
      });
      c.on('mouseover', p => { if (p.componentType === 'series') c.getDom().style.cursor = 'pointer'; });
      c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
    };

    const _origSovDoughnut = renderSovDoughnut;
    window.renderSovDoughnut = function(domId, labels, values, colors, ready = false) {
      _origSovDoughnut(domId, labels, values, colors, ready);
      const c = SNTCharts._i[domId]; if (!c) return;
      c.on('click', p => {
        const sm   = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
        const sent = sm[p.name] || 'all';
        if      (domId === 'chSovTotal')   SNTPopup.open('all',    sent);
        else if (domId === 'chMassPie')    SNTPopup.open('doc',    sent);
        /* ★ Social pie → langsung buka panel social (semua platform), tidak pakai platform picker */
        else if (domId === 'chSocialPie')  SNTPopup.open('social', sent);
      });
      c.on('mouseover', () => { c.getDom().style.cursor = 'pointer'; });
      c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
    };

    const _origMassSocialBars = renderMassSocialBars;
    window.renderMassSocialBars = function() {
      _origMassSocialBars();
      const lk = { 
        'Mass Media':'doc', 'Online News':'doc', 
        'X ( Twitter )':'twit', 'X (Twitter)':'twit', 'X / Twitter':'twit', 'Twitter':'twit', 
        'X / Twit…':'twit', 'X ( Twit…':'twit',
        'Facebook':'fb', 'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok' 
      };
      ['chMass', 'chSocial'].forEach(id => {
        const c      = SNTCharts._i[id]; if (!c) return;
        const isMass = id === 'chMass';
        c.on('click', p => {
          if (p.componentType !== 'series') return;
          const sm   = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
          const sent = sm[p.seriesName] || 'all';
          const plat = lk[p.name] || (isMass ? 'doc' : 'social');
          SNTPopup.open(plat, sent);
        });
        c.on('mouseover', p => { if (p.componentType === 'series') c.getDom().style.cursor = 'pointer'; });
        c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
      });
    };

    const _origByTypePct = renderByTypePct;
    window.renderByTypePct = function() {
      _origByTypePct();
      const c  = SNTCharts._i['chByType']; if (!c) return;
      const lk = { 
        'Mass Media':'doc', 'Online News':'doc', 
        'X ( Twitter )':'twit', 'X (Twitter)':'twit', 'X / Twitter':'twit', 'Twitter':'twit', 
        'X / Twit…':'twit', 'X ( Twit…':'twit',
        'Facebook':'fb', 'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok',
        'Social Media':'social'
      };
      c.on('click', p => {
        if (p.componentType !== 'series') return;
        const plat = lk[p.name] || 'doc';
        const sm   = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
        SNTPopup.open(plat, sm[p.seriesName] || 'all');
      });
      c.on('mouseover', p => { if (p.componentType === 'series') c.getDom().style.cursor = 'pointer'; });
      c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
    };

    const _origByPlatGrouped = renderByPlatGrouped;
    window.renderByPlatGrouped = function() {
      _origByPlatGrouped();
      const c     = SNTCharts._i['chByPlat']; if (!c) return;
      const grps  = ['Mass Media', 'Social Media'];
      c.on('click', p => {
        if (p.componentType !== 'series') return;
        const sm   = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
        const sent = sm[p.seriesName] || 'all';
        /* ★ Social group → langsung buka panel social */
        SNTPopup.open(grps[p.dataIndex] === 'Mass Media' ? 'doc' : 'social', sent);
      });
      c.on('mouseover', p => { if (p.componentType === 'series') c.getDom().style.cursor = 'pointer'; });
      c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
    };

    const _origTimeChart = renderTimeChart;
    window.renderTimeChart = function(domId, skelId, labels, negData, posData, neuData, totals, isHour = false) {
      _origTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour);
      const c = SNTCharts._i[domId]; if (!c) return;
      c.on('click', p => {
        if (p.componentType !== 'series') return;
        const sm = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
        SNTPopup.open('all', sm[p.seriesName] || 'all');
      });
      c.on('mouseover', p => { if (p.componentType === 'series') c.getDom().style.cursor = 'pointer'; });
      c.on('mouseout',  () => { c.getDom().style.cursor = 'default'; });
    };

    const _origSNTPageInit = SNTPage.init.bind(SNTPage);
    SNTPage.init = function() { SNTPopup.init(); _origSNTPageInit(); };

    const _origSNTPageReload = SNTPage.reload.bind(SNTPage);
    SNTPage.reload = function() { SNTPopup._cache = {}; _origSNTPageReload(); };
  </script>
@endsection