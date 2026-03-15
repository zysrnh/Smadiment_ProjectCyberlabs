@extends('mk.layouts.app')

@section('title', 'Sentiment Analysis - SMADIMENT')

@section('styles')
<style>
:root{--primary:#038047;--primary-rgb:3,128,71;--primary-lt:rgba(3,128,71,.10);--dark:#273B4A;--white:#FFFFFF;--bg:#F1F5F8;--green:#038047;--green-light:#E8F5EE;--red:#EF4444;--red-light:#FEF2F2;--amber:#F59E0B;--amber-light:#FFFBEB;--cyan:#06B6D4;--cyan-light:#ECFEFF;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;--slate-800:#1E293B;--slate-900:#0F172A;--radius:8px;--radius-sm:5px;--shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12);--sent-pos:#2FC6F6;--sent-neg:#ef4444;--sent-neu:#94a3b8;--primary-green:#038047;--primary-green-light:rgba(3,128,71,.08);--primary-green-border:rgba(3,128,71,.2)}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}@keyframes spin{to{transform:rotate(360deg)}}@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes slideOutRight{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}@keyframes overlayIn{from{opacity:0}to{opacity:1}}@keyframes overlayOut{from{opacity:1}to{opacity:0}}@keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}@keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
.spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}
.kpi-card-hover{will-change:transform,box-shadow;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important}.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1}.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}.kpi-card-hover:active{transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important}
.chart-container{position:relative}.chart-loading{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s}.chart-loading.hidden{opacity:0;pointer-events:none}.chart-loading span{font-size:11px;font-weight:600;color:var(--slate-400)}.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}.chart-empty i{font-size:34px;color:var(--slate-300);display:block}
.snt-skel{background:linear-gradient(90deg,var(--slate-50) 25%,#e2e8f0 50%,var(--slate-50) 75%);background-size:200% 100%;animation:shimmer 1.5s ease-in-out infinite;border-radius:4px}.snt-skel-overlay{position:absolute;inset:0;z-index:3;border-radius:inherit}
.snt-legend{display:flex;align-items:center;gap:14px;flex-wrap:wrap}.snt-legend-item{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500)}.snt-legend-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

  /* ── PLATFORM MINI LIST ── */
  .snt-media-list{display:flex;flex-direction:column;gap:6px}.snt-media-row{display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--slate-50);border:1px solid var(--slate-100);border-radius:var(--radius-sm);transition:background .13s,border-color .13s}.snt-media-row:hover{border-color:var(--primary-green-border);background:#fff;box-shadow:var(--shadow-sm)}.snt-media-icon{width:28px;height:28px;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0}.snt-media-name{font-size:11px;font-weight:700;color:var(--slate-900);min-width:80px}.snt-media-bars{flex:1;display:flex;flex-direction:column;gap:2px}.snt-media-bar-row{display:flex;align-items:center;gap:5px}.snt-media-bar-track{flex:1;height:4px;background:var(--slate-100);border-radius:2px;overflow:hidden}.snt-media-bar-fill{height:100%;border-radius:2px;transition:width .8s cubic-bezier(.4,0,.2,1)}.snt-media-bar-val{font-size:10px;font-weight:700;color:var(--slate-500);min-width:36px;text-align:right;white-space:nowrap}.snt-media-total{font-size:11px;font-weight:700;color:var(--slate-900);min-width:48px;text-align:right}
/* Panel */
.do-panel-overlay{position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);display:none}.do-panel-overlay.show{display:block;animation:overlayIn .22s ease-out}.do-panel-overlay.hiding{animation:overlayOut .22s ease-out forwards}
#sntPopup{position:fixed;top:0;right:0;bottom:0;z-index:9001;width:480px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16)}#sntPopup.show{display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1)}#sntPopup.hiding{animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards}
.sntp-header{display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}.sntp-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}.sntp-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sntp-count{display:none}.sntp-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;font-size:16px;color:var(--slate-500);display:flex;align-items:center;justify-content:center;transition:all .14s;flex-shrink:0}.sntp-close:hover{background:var(--red);border-color:var(--red);color:#fff}
.sntp-actions{display:flex;align-items:center;gap:7px;padding:7px 12px;border-bottom:1px solid var(--slate-200);background:#fff;flex-shrink:0}.sntp-meta{flex:1;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px;overflow:hidden}.sntp-meta__label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sntp-sent-tabs{display:flex;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;gap:2px}.sntp-sent-tab{padding:3px 9px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:700;cursor:pointer;transition:all .13s;color:var(--slate-500);white-space:nowrap}.sntp-sent-tab:hover{background:#fff}.sntp-sent-tab.active{background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08)}.sntp-sent-tab.active[data-s="all"]{color:var(--primary)}.sntp-sent-tab.neg.active{color:#ef4444}.sntp-sent-tab.pos.active{color:#0ea5e9}.sntp-sent-tab.neu.active{color:var(--slate-500)}
.sntp-export-btn{display:flex;align-items:center;gap:4px;padding:4px 10px;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);font-size:10px;font-weight:700;cursor:pointer;transition:filter .13s;white-space:nowrap}.sntp-export-btn:hover{filter:brightness(1.1)}.sntp-export-btn i{font-size:12px}
.sntp-list{overflow-y:auto;flex:1;padding:2px 0;min-height:0}.sntp-list::-webkit-scrollbar{width:4px}.sntp-list::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
.sntp-item{display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--slate-100);transition:background .1s;cursor:pointer;align-items:flex-start}.sntp-item:last-child{border-bottom:none}.sntp-item:hover{background:#f0f9ff}.sntp-item.hidden{display:none}.sntp-avatar{width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden}.sntp-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}.sntp-item-body{flex:1;min-width:0}.sntp-item-author{font-size:12px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.sntp-item-handle{font-size:10px;color:var(--slate-400);font-weight:500;margin-bottom:2px}.sntp-item-text{font-size:11px;color:var(--slate-500);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px}.sntp-item-footer{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap}
.sntp-sent-badge{padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase}.sntp-sent-badge--pos{background:#dbeafe;color:#1d4ed8}.sntp-sent-badge--neg{background:#fee2e2;color:#991b1b}.sntp-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
.sntp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:var(--slate-500);font-size:13px;font-weight:600}.sntp-spinner{width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
/* Detail sub-panel */
#sntDetailPanel{position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1)}#sntDetailPanel.visible{display:flex}
.sntdp-header{display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}.sntdp-back{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;flex-shrink:0}.sntdp-back:hover{background:var(--primary-lt);color:var(--primary);border-color:var(--primary)}.sntdp-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sntdp-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;font-size:16px;color:var(--slate-500);display:flex;align-items:center;justify-content:center;transition:all .14s}.sntdp-close:hover{background:var(--red);border-color:var(--red);color:#fff}
.sntdp-body{overflow-y:auto;flex:1;padding:16px}.sntdp-body::-webkit-scrollbar{width:4px}.sntdp-body::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}.sntdp-avatar-row{display:flex;align-items:center;gap:10px;margin-bottom:12px}.sntdp-avatar-lg{width:46px;height:46px;border-radius:50%;color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;border:2px solid var(--slate-200);overflow:hidden;flex-shrink:0}.sntdp-avatar-lg img{width:100%;height:100%;object-fit:cover;border-radius:50%}.sntdp-author-name{font-size:14px;font-weight:700;color:var(--slate-900)}.sntdp-author-handle{font-size:11px;color:var(--slate-400);font-weight:500}
.sntdp-sent-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px}.sntdp-sent-badge--pos{background:#dbeafe;color:#1d4ed8}.sntdp-sent-badge--neg{background:#fee2e2;color:#991b1b}.sntdp-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}.sntdp-content-text{font-size:12px;color:var(--slate-600);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word}.sntdp-meta-row{display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px}.sntdp-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px}.sntdp-stat-box{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}.sntdp-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}.sntdp-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}.sntdp-media-wrap{border-radius:var(--radius-sm);overflow:hidden;margin-bottom:10px;background:#000}.sntdp-media-img{width:100%;max-height:220px;object-fit:cover;display:block}.sntdp-link-btn{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;width:100%;margin-top:4px}.sntdp-link-btn:hover{filter:brightness(1.1);color:#fff}.sntdp-link-btn i{font-size:14px}
/* Platform Picker */
#sntPlatPicker{position:fixed;z-index:20000;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:5px;min-width:175px;animation:fadeUp .14s ease-out;display:none}#sntPlatPicker.visible{display:block}.sntpp-header{padding:4px 9px 6px;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--slate-100);margin-bottom:3px}.sntpp-btn{display:flex;align-items:center;gap:7px;padding:7px 10px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;background:transparent;border:none;width:100%;text-align:left;color:var(--slate-500);transition:background .12s}.sntpp-btn:hover{background:var(--primary-lt);color:var(--primary)}.sntpp-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-left:auto}
.kpi-card-hover h3{font-size:1.5rem}@media(max-width:640px){#sntPopup{width:100vw}}
</style>
@endsection

@section('page-title', 'Sentiment Analysis')
@section('content')
@php
  $projectId = $projectId ?? request()->get('project_id');
  $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
  $media     = request()->get('media', 'all');
  $projects  = $projects ?? [];
@endphp
<script>const OV_PID={{ $projectId?(int)$projectId:'null' }};const OV_SD='{{ $startDate }}';const OV_ED='{{ $endDate }}';const OV_MEDIA='{{ $media }}';</script>
@include('mk.layouts.partials.filter-datepicker')
{{-- Media Filter --}}
<div class="mb-3" style="margin-top:-8px;">
    <div style="display:flex;flex-direction:column;gap:5px;">
        <label style="font-size:10px;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:.5px;">Media</label>
        <select class="form-select form-select-sm" id="sntMediaFilter" style="max-width:220px;">
            <option value="all"       {{ $media === 'all'       ? 'selected' : '' }}>All Media</option>
            <option value="doc"       {{ $media === 'doc'       ? 'selected' : '' }}>Mass Media (Online News)</option>
            <option value="twitter"   {{ $media === 'twitter'   ? 'selected' : '' }}>X / Twitter</option>
            <option value="facebook"  {{ $media === 'facebook'  ? 'selected' : '' }}>Facebook</option>
            <option value="instagram" {{ $media === 'instagram' ? 'selected' : '' }}>Instagram</option>
            <option value="youtube"   {{ $media === 'youtube'   ? 'selected' : '' }}>YouTube</option>
            <option value="tiktok"    {{ $media === 'tiktok'    ? 'selected' : '' }}>TikTok</option>
        </select>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var mf=document.getElementById('sntMediaFilter');
  if(mf){mf.addEventListener('change',function(){var form=document.getElementById('doFilterForm');if(!form)return;var existing=form.querySelector('input[name="media"]');if(!existing){existing=document.createElement('input');existing.type='hidden';existing.name='media';form.appendChild(existing)}existing.value=this.value;form.submit()})}
});
</script>
{{-- KPI --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3"><div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d1" style="cursor:pointer;" onclick="SNTPopup.openSentiment('neg')"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Negative</p><h3 class="mb-0 text-white f-w-300" id="valNeg"><span class="sk-block" style="width:80px;height:24px;display:inline-block;background:rgba(255,255,255,.18);"></span></h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeg">—</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d2" style="cursor:pointer;" onclick="SNTPopup.openSentiment('pos')"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Positive</p><h3 class="mb-0 text-white f-w-300" id="valPos"><span class="sk-block" style="width:80px;height:24px;display:inline-block;background:rgba(255,255,255,.18);"></span></h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctPos">—</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d3" style="cursor:pointer;" onclick="SNTPopup.openSentiment('neu')"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Neutral</p><h3 class="mb-0 text-white f-w-300" id="valNeu"><span class="sk-block" style="width:80px;height:24px;display:inline-block;background:rgba(255,255,255,.18);"></span></h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeu">—</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div></div></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d4"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1"><p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p><h3 class="mb-0 text-white f-w-300" id="valTot"><span class="sk-block" style="width:80px;height:24px;display:inline-block;background:rgba(255,255,255,.18);"></span></h3><p class="mb-0 mt-2 text-white text-opacity-75 f-12">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p></div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div></div></div></div></div>
</div>
{{-- Overview Bar + SOV Doughnut --}}
<div class="row mb-3">
    <div class="col-lg-8 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div><div><h6 class="mb-0">Total Mentions by Sentiments</h6><small class="text-muted">Perbandingan volume Negative / Positive / Neutral</small></div></div><div class="d-flex align-items-center gap-2"><button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyOverview()" title="Copy CSV"><i class="ph ph-copy me-1"></i>CSV</button><span class="badge bg-light-primary text-primary">Overview</span></div></div><div class="card-body"><div style="position:relative;height:300px;" id="chOverviewWrap"><div id="chOverview" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skOverview"></div></div><div class="snt-legend" style="margin-top:14px;justify-content:center;"><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Positive</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neutral</div></div></div></div></div>
    <div class="col-lg-4 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div><div><h6 class="mb-0">Share of Voice</h6><small class="text-muted">Distribusi sentimen</small></div></div><span class="badge bg-light-primary text-primary">SOV</span></div><div class="card-body" style="display:flex;flex-direction:column;align-items:center;"><div style="position:relative;height:280px;width:100%;"><div id="chSovTotal" style="width:100%;height:100%;"></div><div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSovTotal"></div></div></div></div></div>
</div>
{{-- Mass Media + Social Media --}}
<div class="row mb-3">
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .26s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-newspaper f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments in Mass Media</h6><small class="text-muted">Online News / Artikel</small></div></div><div class="d-flex align-items-center gap-2"><button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyMass()" title="CSV"><i class="ph ph-copy me-1"></i>CSV</button><span class="badge bg-light-primary text-primary">Mass</span></div></div><div class="card-body"><div style="position:relative;height:260px;" id="chMassWrap"><div id="chMass" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skMass"></div></div><div class="snt-legend" style="margin-top:12px;justify-content:center;"><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div></div></div></div></div>
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .30s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-share-network f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments in Social Media</h6><small class="text-muted">Twitter · Facebook · Instagram · YouTube · TikTok</small></div></div><div class="d-flex align-items-center gap-2"><button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copySocial()" title="CSV"><i class="ph ph-copy me-1"></i>CSV</button><span class="badge bg-light-primary text-primary">Social</span></div></div><div class="card-body"><div style="position:relative;height:260px;" id="chSocialWrap"><div id="chSocial" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skSocial"></div></div><div class="snt-legend" style="margin-top:12px;justify-content:center;"><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div></div></div></div></div>
</div>
{{-- By Type % + By Platform Grouped --}}
<div class="row mb-3">
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .34s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar-horizontal f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments by Media Types</h6><small class="text-muted">Persentase per platform (%)</small></div></div><span class="badge bg-light-primary text-primary">% Share</span></div><div class="card-body"><div style="position:relative;height:300px;" id="chByTypeWrap"><div id="chByType" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skByType"></div></div></div></div></div>
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .38s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-columns f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments by Media Platforms</h6><small class="text-muted">Mass Media vs Social Media</small></div></div><span class="badge bg-light-primary text-primary">Grouped</span></div><div class="card-body"><div style="position:relative;height:300px;" id="chByPlatWrap"><div id="chByPlat" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skByPlat"></div></div></div></div></div>
</div>
{{-- Mass Pie + Social Pie --}}
<div class="row mb-3">
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .42s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-newspaper f-18 text-primary"></i></div><div><h6 class="mb-0">Mass Media SOV</h6><small class="text-muted">Share of Voice — Online News</small></div></div><span class="badge bg-light-primary text-primary">Mass</span></div><div class="card-body" style="display:flex;flex-direction:column;align-items:center;"><div style="position:relative;height:260px;width:100%;"><div id="chMassPie" style="width:100%;height:100%;"></div><div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skMassPie"></div></div></div></div></div>
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .46s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-share-network f-18 text-primary"></i></div><div><h6 class="mb-0">Social Media SOV</h6><small class="text-muted">Share of Voice — Social platforms</small></div></div><span class="badge bg-light-primary text-primary">Social</span></div><div class="card-body" style="display:flex;flex-direction:column;align-items:center;"><div style="position:relative;height:260px;width:100%;"><div id="chSocialPie" style="width:100%;height:100%;"></div><div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSocialPie"></div></div></div></div></div>
</div>
{{-- Trend Line --}}
<div class="row mb-3">
    <div class="col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .54s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-trend-up f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiment Trends in All Media</h6><small class="text-muted">Tren harian Total / Positive / Neutral / Negative</small></div></div><div class="d-flex align-items-center gap-2"><button class="btn btn-outline-secondary btn-sm" onclick="SNTCsv.copyTrend()" title="Copy CSV"><i class="ph ph-copy me-1"></i>CSV</button><span class="badge bg-light-primary text-primary" id="trendBadge">Loading…</span></div></div><div class="card-body"><div style="position:relative;height:380px;" id="chTrendWrap"><div id="chTrend" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skTrend"></div></div></div></div></div>
</div>
{{-- Weekday + Hour --}}
<div class="row mb-3">
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .58s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-calendar f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments by Weekday</h6><small class="text-muted">Volume per hari dalam seminggu</small></div></div><span class="badge bg-light-primary text-primary">7 Hari</span></div><div class="card-body"><div style="position:relative;height:320px;"><div id="chWeekday" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skWeekday"></div></div><div class="snt-legend" style="margin-top:12px;justify-content:center;"><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div></div></div></div></div>
    <div class="col-lg-6 col-12"><div class="card mb-3" style="animation:fadeUp .38s ease-out .62s both;"><div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2"><div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-clock f-18 text-primary"></i></div><div><h6 class="mb-0">Sentiments by Hour</h6><small class="text-muted">Distribusi per jam (00–23)</small></div></div><span class="badge bg-light-primary text-primary">24 Jam</span></div><div class="card-body"><div style="position:relative;height:320px;"><div id="chHour" style="width:100%;height:100%;"></div><div class="snt-skel snt-skel-overlay" id="skHour"></div></div><div class="snt-legend" style="margin-top:12px;justify-content:center;"><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Neg</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#2FC6F6;"></span>Pos</div><div class="snt-legend-item"><span class="snt-legend-dot" style="background:#94a3b8;"></span>Neu</div></div></div></div></div>
</div>
{{-- Slide Panel --}}
<div class="do-panel-overlay" id="sntPanelOverlay" onclick="SNTPopup.close()"></div>
<div id="sntPopup">
    <div class="sntp-header" id="sntPopHeader"><div class="sntp-dot" id="sntPopDot"></div><span class="sntp-title" id="sntPopTitle">Mentions</span><span class="sntp-count" id="sntPopCount">…</span><button class="sntp-close" onclick="SNTPopup.close()">×</button></div>
    <div class="sntp-actions"><div class="sntp-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span class="sntp-meta__label" id="sntPopMeta">—</span></div><div style="display:flex;align-items:center;gap:6px;flex-shrink:0;"><div class="sntp-sent-tabs" id="sntPopSentTabs"><button class="sntp-sent-tab active" data-s="all" onclick="SNTPopup.filterSent('all')">Semua</button><button class="sntp-sent-tab neg" data-s="neg" onclick="SNTPopup.filterSent('neg')">Neg</button><button class="sntp-sent-tab pos" data-s="pos" onclick="SNTPopup.filterSent('pos')">Pos</button><button class="sntp-sent-tab neu" data-s="neu" onclick="SNTPopup.filterSent('neu')">Neu</button></div><button class="sntp-export-btn" onclick="SNTPopup.exportCsv()"><i class="ph ph-download-simple"></i> Export CSV</button></div></div>
    <div class="sntp-list" id="sntPopList"></div>
    <div id="sntDetailPanel"><div class="sntdp-header"><button class="sntdp-back" onclick="SNTDetail.close()"><i class="ph ph-caret-left"></i></button><span class="sntdp-title" id="sntDpTitle">Detail</span><button class="sntdp-close" onclick="SNTPopup.close()">×</button></div><div class="sntdp-body" id="sntDpBody"></div></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const SNTCfg = {
  pid: OV_PID,
  sd:  OV_SD,
  ed:  OV_ED,
  media: OV_MEDIA,
  colors: { neg:'#ef4444', pos:'#2FC6F6', neu:'#94a3b8' },
};

/* ── UTILS ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const pct    = (v,t) => t>0 ? ((v/t)*100).toFixed(1)+'%' : '0%';
const emptyHtml = msg => `<div class="chart-empty"><i class="ph ph-warning-circle"></i><span>${msg}</span></div>`;

/* ── ECHARTS REGISTRY ── */
const SNTCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer:'canvas' });
    this._i[id] = c;
    return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{ try{c.dispose();}catch(e){} }); this._i={}; }
};
window.addEventListener('resize', () => {
  Object.values(SNTCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

/* ═══════════════════════════════════════════════════════
   DATA STORE
═══════════════════════════════════════════════════════ */
const SNTData = {
  totals:   { neg:0, pos:0, neu:0 },
  byMedia:  [],
  trend:    [],
  weekday:  null,
  hour:     null,
};

/* ═══════════════════════════════════════════════════════
   LOAD — SENTIMENT TOTALS & PER MEDIA
═══════════════════════════════════════════════════════ */
async function loadSentiment() {
  if (!SNTCfg.pid) {
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie','skTrend','skWeekday','skHour'].forEach(hideSk);
    return;
  }

  try {
    const res  = await fetch(`/mk/api/sentiment/totals?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&media=${SNTCfg.media}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    SNTData.totals  = data.totals  || { neg:0, pos:0, neu:0 };
    SNTData.byMedia = data.by_media || [];
    SNTData.trend   = data.trend   || [];

    renderAll();
  } catch(err) {
    console.error('loadSentiment error:', err);
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie'].forEach(hideSk);
  }
}

async function loadTimeData() {
  if (!SNTCfg.pid) return;
  try {
    const res  = await fetch(`/mk/api/sentiment/by-time?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    SNTData.weekday = data.weekday;
    SNTData.hour    = data.hour;
    renderWeekdayHour();
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

  // Stat cards
  document.getElementById('valNeg').textContent = numFmt(neg);
  document.getElementById('valPos').textContent = numFmt(pos);
  document.getElementById('valNeu').textContent = numFmt(neu);
  document.getElementById('valTot').textContent = numFmt(tot);
  document.getElementById('pctNeg').textContent = pct(neg, tot);
  document.getElementById('pctPos').textContent = pct(pos, tot);
  document.getElementById('pctNeu').textContent = pct(neu, tot);

  renderOverviewBar();
  renderSovDoughnut('chSovTotal', ['Negative','Positive','Neutral'], [neg,pos,neu], ['#ef4444','#2FC6F6','#94a3b8'], true);
  hideSk('skSovTotal');

  renderMassSocialBars();
  renderByTypePct();
  renderByPlatGrouped();
  renderMassSocialPies();
  renderTrend();
}

/* ─── Overview stacked bar ─── */
function renderOverviewBar() {
  hideSk('skOverview');
  const { neg, pos, neu } = SNTData.totals;
  const tot = neg + pos + neu;
  if (tot === 0) { document.getElementById('chOverview').parentElement.innerHTML = emptyHtml('Tidak ada data sentimen'); return; }

  const chart = SNTCharts.make('chOverview');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const p = params[0];
        const v = p.value;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;">
                  <span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(v)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct(v,tot)}</span>
                </div>`;
      }
    },
    grid:{top:20,right:20,bottom:40,left:60},
    xAxis:{
      type:'category', data:['Negative','Positive','Neutral'],
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'600',color:'#374151'}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[{
      type:'bar', barMaxWidth:80,
      data:[
        {value:neg, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#ef4444'},{offset:1,color:'#fca5a555'}]},borderRadius:[8,8,0,0]}},
        {value:pos, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#2FC6F6'},{offset:1,color:'#7dd3fc55'}]},borderRadius:[8,8,0,0]}},
        {value:neu, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#94a3b8'},{offset:1,color:'#cbd5e155'}]},borderRadius:[8,8,0,0]}},
      ],
      label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
    }]
  });
}

/* ─── SOV Doughnut ─── */
/* ================================================================
   PERUBAHAN: label slice sekarang tampilkan PERSENTASE (xx.x%)
   bukan count — konsisten dengan Drone Emprit
================================================================ */
function renderSovDoughnut(domId, labels, values, colors, ready=false) {
  const tot = values.reduce((a,b)=>a+b,0);
  const chart = SNTCharts.make(domId);
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'cubicOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pct2 = tot>0 ? ((p.value/tot)*100).toFixed(1) : '0.0';
        return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;">
                  <span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(p.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct2}%</span>
                </div>`;
      }
    },
    legend:{show:false},
    series:[{
      type:'pie', radius:['42%','62%'], center:['50%','50%'],
      avoidLabelOverlap:true, minAngle:5,
      itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
      label:{
        show:true, alignTo:'edge', edgeDistance:10, lineHeight:18,
        fontFamily:"'Poppins',sans-serif", fontSize:11, color:'#374151',
        formatter: p => {
          const pc = tot>0?(p.value/tot*100):0;
          if(pc<3) return '';
          // ← DIUBAH: tampilkan persentase (xx.x%) bukan count
          return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
        },
        rich:{
          name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:18},
          // ← DIUBAH: style label pct (hijau badge)
          pct:{fontWeight:'700',fontSize:10,color:'#038047',lineHeight:16,backgroundColor:'#edf7f3',borderRadius:4,padding:[1,5]},
        }
      },
      labelLine:{show:true,length:12,length2:16,smooth:.4,lineStyle:{color:'#c4cdd8',width:1.2}},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'}},
      data: labels.map((l,i)=>({name:l,value:values[i],itemStyle:{color:colors[i]}}))
    }],
    graphic:[
      {type:'text',left:'center',top:'44%',z:100,style:{text:numK(tot),fill:'#0f172a',font:"800 24px 'Poppins',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center',letterSpacing:2}},
    ]
  });
}

/* ─── Mass vs Social stacked bars ─── */
function renderMassSocialBars() {
  const bm = SNTData.byMedia;

  const massPlt  = bm.filter(m => m.key === 'doc');
  const socialPlt = bm.filter(m => m.key !== 'doc');

  // ── MASS: tiap platform mass punya bar sendiri (biasanya hanya 1: Online News) ──
  hideSk('skMass');
  const massChart = SNTCharts.make('chMass');
  if (massChart) {
    const mLabels = massPlt.map(m => m.label);
    const mNeg    = massPlt.map(m => m.neg);
    const mPos    = massPlt.map(m => m.pos);
    const mNeu    = massPlt.map(m => m.neu);
    const mTot    = massPlt.map(m => m.neg+m.pos+m.neu);
    if (mLabels.length && mTot.some(v=>v>0)) {
      massChart.setOption(makeStackedBarOption(mLabels, mNeg, mPos, mNeu, mLabels, mTot));
    } else {
      document.getElementById('chMass').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');
    }
  }

  // ── SOCIAL: tiap platform punya bar stacked sendiri ──
  hideSk('skSocial');
  const socialChart = SNTCharts.make('chSocial');
  if (socialChart) {
    const sLabels = socialPlt.map(m => m.label);
    const sNeg    = socialPlt.map(m => m.neg);
    const sPos    = socialPlt.map(m => m.pos);
    const sNeu    = socialPlt.map(m => m.neu);
    const sTot    = socialPlt.map(m => m.neg+m.pos+m.neu);
    if (sLabels.length && sTot.some(v=>v>0)) {
      socialChart.setOption(makeStackedBarOption(sLabels, sNeg, sPos, sNeu, sLabels, sTot));
    } else {
      document.getElementById('chSocial').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
    }
  }
}

function makeStackedBarOption(xLabels, negData, posData, neuData, tooltipLabels, totals) {
  const makeData = (arr, col) => arr.map((v,i)=>({ value:v, itemStyle:{color:col} }));

  return {
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const tot = totals[idx]||0;
        const rows = params.slice().reverse().map(p =>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${xLabels[idx]||''}</div>
                ${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span>
                  <span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    grid:{top:28,right:16,bottom:36,left:60},
    xAxis:{
      type:'category', data:xLabels,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,
        formatter:v=>v.length>11?v.slice(0,10)+'…':v}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      { name:'Neutral',  type:'bar', stack:'s', data:makeData(neuData,'#94a3b8'), itemStyle:{borderRadius:[0,0,0,0]} },
      { name:'Positive', type:'bar', stack:'s', data:makeData(posData,'#2FC6F6') },
      {
        name:'Negative', type:'bar', stack:'s', barMaxWidth:80,
        data:negData.map((v,i)=>({ value:v, itemStyle:{color:'#ef4444',borderRadius:[6,6,0,0]} })),
        label:{
          show:true, position:'top',
          fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',
          formatter: p => totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''
        }
      },
    ]
  };
}

/* ─── By Media Type % (horizontal stacked) ─── */
function renderByTypePct() {
  hideSk('skByType');
  const bm = SNTData.byMedia;
  if (!bm.length) { document.getElementById('chByType').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }

  const chart = SNTCharts.make('chByType');
  if (!chart) return;

  const labels  = bm.map(m => m.label);
  const negPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neg/t*100).toFixed(1)):0; });
  const posPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.pos/t*100).toFixed(1)):0; });
  const neuPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neu/t*100).toFixed(1)):0; });

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow'},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const m   = bm[idx]; if(!m) return '';
        const tot = m.neg+m.pos+m.neu;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${m.label}</div>
                ${[['Negative','#ef4444',m.neg],['Positive','#2FC6F6',m.pos],['Neutral','#94a3b8',m.neu]].map(([n,c,v])=>
                  `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                    <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div>
                    <div style="display:flex;gap:10px;"><span style="font-size:12px;font-weight:700;">${numFmt(v)}</span><span style="font-size:10px;color:#94a3b8;">${tot>0?(v/tot*100).toFixed(1):'0'}%</span></div>
                  </div>`).join('')}`;
      }
    },
    legend:{
      bottom:0, data:['Negative','Positive','Neutral'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:20,
    },
    grid:{top:12,right:16,bottom:50,left:100},
    xAxis:{
      type:'value', max:100, min:0,
      axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}
    },
    yAxis:{
      type:'category', data:labels, inverse:false,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#374151',margin:10}
    },
    series:[
      {name:'Negative',type:'bar',stack:'pct',data:negPcts,itemStyle:{color:'#ef4444'},barMaxWidth:30,
       emphasis:{focus:'series'},label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'}},
      {name:'Positive',type:'bar',stack:'pct',data:posPcts,itemStyle:{color:'#2FC6F6'},barMaxWidth:30,
       emphasis:{focus:'series'},label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'}},
      {name:'Neutral', type:'bar',stack:'pct',data:neuPcts,itemStyle:{color:'#94a3b8',borderRadius:[0,4,4,0]},barMaxWidth:30,
       emphasis:{focus:'series'}},
    ]
  });
}

/* ─── By Platform Grouped Bars (Mass vs Social %) ─── */
function renderByPlatGrouped() {
  hideSk('skByPlat');
  const bm = SNTData.byMedia;
  if (!bm.length) { document.getElementById('chByPlat').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }

  const chart = SNTCharts.make('chByPlat');
  if (!chart) return;

  const massDat  = bm.filter(m=>m.key==='doc');
  const socialDat= bm.filter(m=>m.key!=='doc');

  const groups = ['Mass Media','Social Media'];
  const mNeg   = massDat.reduce((s,m)=>s+m.neg,0);
  const mPos   = massDat.reduce((s,m)=>s+m.pos,0);
  const mNeu   = massDat.reduce((s,m)=>s+m.neu,0);
  const mTot   = mNeg+mPos+mNeu;
  const sNeg   = socialDat.reduce((s,m)=>s+m.neg,0);
  const sPos   = socialDat.reduce((s,m)=>s+m.pos,0);
  const sNeu   = socialDat.reduce((s,m)=>s+m.neu,0);
  const sTot   = sNeg+sPos+sNeu;

  const negPct = [mTot>0?(mNeg/mTot*100).toFixed(1):0, sTot>0?(sNeg/sTot*100).toFixed(1):0];
  const posPct = [mTot>0?(mPos/mTot*100).toFixed(1):0, sTot>0?(sPos/sTot*100).toFixed(1):0];
  const neuPct = [mTot>0?(mNeu/mTot*100).toFixed(1):0, sTot>0?(sNeu/sTot*100).toFixed(1):0];

  chart.setOption({
    animation:true, animationDuration:900,
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow'},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const lbl = groups[idx];
        const neg = [mNeg,sNeg][idx], pos=[mPos,sPos][idx], neu=[mNeu,sNeu][idx], tot=[mTot,sTot][idx];
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;">${lbl}</div>
                ${[['Negative','#ef4444',neg],['Positive','#2FC6F6',pos],['Neutral','#94a3b8',neu]].map(([n,c,v])=>
                  `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                    <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div>
                    <span style="font-size:12px;font-weight:700;">${numFmt(v)} <span style="color:#94a3b8;font-size:10px;">(${tot>0?(v/tot*100).toFixed(1):'0'}%)</span></span>
                  </div>`).join('')}`;
      }
    },
    legend:{
      bottom:0, data:['Negative','Positive','Neutral'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:20,
    },
    grid:{top:24,right:16,bottom:50,left:72},
    xAxis:{
      type:'category', data:groups,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'700',color:'#374151'}
    },
    yAxis:{
      type:'value', max:100, min:0,
      axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}
    },
    series:[
      {name:'Neutral', type:'bar',stack:'s',data:neuPct.map(v=>parseFloat(v)),itemStyle:{color:'#94a3b8'},barMaxWidth:90,emphasis:{focus:'series'}},
      {name:'Positive',type:'bar',stack:'s',data:posPct.map(v=>parseFloat(v)),itemStyle:{color:'#2FC6F6'},barMaxWidth:90,emphasis:{focus:'series'},
       label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
      {name:'Negative',type:'bar',stack:'s',data:negPct.map(v=>parseFloat(v)),itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]},barMaxWidth:90,emphasis:{focus:'series'},
       label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
    ]
  });
}

/* ─── Mass Pie + Social Pie ─── */
function renderMassSocialPies() {
  const bm = SNTData.byMedia;

  const massDat = bm.filter(m=>m.key==='doc');
  const mN=massDat.reduce((s,m)=>s+m.neg,0), mP=massDat.reduce((s,m)=>s+m.pos,0), mNe=massDat.reduce((s,m)=>s+m.neu,0);
  hideSk('skMassPie');
  if(mN+mP+mNe>0) renderSovDoughnut('chMassPie',['Negative','Positive','Neutral'],[mN,mP,mNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chMassPie').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');

  const sD = bm.filter(m=>m.key!=='doc');
  const sN=sD.reduce((s,m)=>s+m.neg,0), sP=sD.reduce((s,m)=>s+m.pos,0), sNe=sD.reduce((s,m)=>s+m.neu,0);
  hideSk('skSocialPie');
  if(sN+sP+sNe>0) renderSovDoughnut('chSocialPie',['Negative','Positive','Neutral'],[sN,sP,sNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chSocialPie').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
}

/* ─── Trend Line ─── */
function renderTrend() {
  hideSk('skTrend');
  const trend = SNTData.trend;
  if (!trend.length) {
    document.getElementById('trendBadge').textContent='No Data';
    document.getElementById('chTrend').parentElement.innerHTML=emptyHtml('Data trend tidak tersedia untuk periode ini');
    return;
  }

  const dates   = trend.map(d=>d.date);
  const xLabels = dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}/${dt.getMonth()+1}`; });
  const negVals = trend.map(d=>d.neg||0);
  const posVals = trend.map(d=>d.pos||0);
  const neuVals = trend.map(d=>d.neu||0);
  const totVals = trend.map(d=>(d.neg||0)+(d.pos||0)+(d.neu||0));

  const fmtB = d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
  document.getElementById('trendBadge').textContent = `${fmtB(dates[0])} – ${fmtB(dates[dates.length-1])}`;

  const chart = SNTCharts.make('chTrend');
  if (!chart) return;

  const makeSeries = (name, data, color) => ({
    name, type:'line', data, smooth:true,
    symbol:'circle', symbolSize:6, showSymbol:dates.length<=30,
    itemStyle:{color,borderColor:'#fff',borderWidth:2},
    lineStyle:{color,width:2.5},
    areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color+'4D'},{offset:1,color:color+'05'}]}},
    emphasis:{focus:'series',lineStyle:{width:3.5},itemStyle:{symbolSize:10,shadowBlur:10,shadowColor:color+'88'}}
  });

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'cubicInOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter: params => {
        const di   = params[0]?.dataIndex ?? 0;
        const date = dates[di]||'';
        const fullDt = date ? new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}) : '';
        const rows = params.map(p=>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">
                  ${fullDt||date}
                </div>${rows}`;
      }
    },
    legend:{
      bottom:0, data:['Total','Positive','Neutral','Negative'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:24,
    },
    grid:{top:28,right:20,bottom:50,left:64},
    xAxis:{
      type:'category', data:xLabels, boundaryGap:false,
      axisLine:{lineStyle:{color:'#e2e8f0'}}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      makeSeries('Total',   totVals,'#4680ff'),
      makeSeries('Positive',posVals,'#10B981'),
      makeSeries('Neutral', neuVals,'#94A3B8'),
      makeSeries('Negative',negVals,'#EF4444'),
    ]
  });
}

/* ─── Weekday & Hour ─── */
function renderWeekdayHour() {
  renderTimeChart('chWeekday','skWeekday', SNTData.weekday?.weekdays||[], SNTData.weekday?.neg||[], SNTData.weekday?.pos||[], SNTData.weekday?.neu||[], SNTData.weekday?.total||[], false);
  renderTimeChart('chHour','skHour', SNTData.hour?.hours||[], SNTData.hour?.neg||[], SNTData.hour?.pos||[], SNTData.hour?.neu||[], SNTData.hour?.total||[], true);
}

function renderTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour=false) {
  hideSk(skelId);
  if (!labels.length || !totals.some(v=>v>0)) {
    document.getElementById(domId).parentElement.innerHTML = emptyHtml('Data tidak tersedia untuk periode ini');
    return;
  }

  const chart = SNTCharts.make(domId);
  if (!chart) return;

  const makeS = (name,data,color) => ({
    name, type:'bar', stack:'s',
    data: data.map(v=>({value:v,itemStyle:{color,borderRadius:[0,0,0,0]}})),
    emphasis:{focus:'series'}
  });

  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const lbl = labels[idx]||'';
        const tot = totals[idx]||0;
        const rows = [...params].reverse().map(p=>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${isHour?'Jam ':''}${lbl}</div>
                ${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    grid:{top:24,right:16,bottom:40,left:56},
    xAxis:{
      type:'category', data:labels,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:isHour?9:11,fontWeight:'600',color:'#64748b',
        interval:isHour?1:0, rotate:isHour?45:0}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      makeS('Neutral', neuData,'#94a3b8'),
      makeS('Positive',posData,'#2FC6F6'),
      {
        name:'Negative', type:'bar', stack:'s', barMaxWidth:isHour?20:56,
        data:negData.map((v,i)=>({value:v,itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]}})),
        label:{
          show:true, position:'top',
          fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:isHour?9:10,color:'#64748b',
          formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''
        },
        emphasis:{focus:'series'}
      }
    ]
  });
}

/* ═══════════════════════════════════════════════════════
   CSV EXPORT
═══════════════════════════════════════════════════════ */
const SNTCsv = {
  _copy(text) {
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea'); ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;'; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    });
    alert('CSV data tersalin ke clipboard!');
  },
  copyOverview() {
    const {neg,pos,neu}=SNTData.totals; const tot=neg+pos+neu;
    const lines=['sentiment;count;percentage',
      `Negative;${neg};${tot>0?(neg/tot*100).toFixed(1):0}`,
      `Positive;${pos};${tot>0?(pos/tot*100).toFixed(1):0}`,
      `Neutral;${neu};${tot>0?(neu/tot*100).toFixed(1):0}`,
      `Total;${tot};100`,
    ];
    this._copy(lines.join('\n'));
  },
  copyMass() {
    const bm=SNTData.byMedia.filter(m=>m.key==='doc');
    const lines=['platform;negative;positive;neutral;total'];
    bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`));
    this._copy(lines.join('\n'));
  },
  copySocial() {
    const bm=SNTData.byMedia.filter(m=>m.key!=='doc');
    const lines=['platform;negative;positive;neutral;total'];
    bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`));
    this._copy(lines.join('\n'));
  },
  copyTrend() {
    const lines=['date;negative;positive;neutral;total'];
    SNTData.trend.forEach(d=>lines.push(`${d.date};${d.neg};${d.pos};${d.neu};${d.neg+d.pos+d.neu}`));
    this._copy(lines.join('\n'));
  }
};

/* ═══════════════════════════════════════════════════════
   PAGE CONTROLLER
═══════════════════════════════════════════════════════ */
const SNTPage = {
  reload() {
    SNTCharts.disposeAll();
    SNTData.trend=[];SNTData.byMedia=[];SNTData.weekday=null;SNTData.hour=null;
    loadSentiment();
    loadTimeData();
  },
  init() {
    loadSentiment();
    loadTimeData();
  }
};

document.addEventListener('DOMContentLoaded', () => SNTPage.init());

/* ══════════════════════════════════════════════════════
   SENTIMENT MENTION POPUP
══════════════════════════════════════════════════════ */

const SNTPlatMeta = {
  doc:    { label:'Online News',  color:'#0284c7' },
  twit:   { label:'X / Twitter', color:'#0ea5e9' },
  fb:     { label:'Facebook',    color:'#1877f2' },
  ig:     { label:'Instagram',   color:'#e1306c' },
  yt:     { label:'YouTube',     color:'#ff0000' },
  tiktok: { label:'TikTok',      color:'#111827' },
  neg:    { label:'Negative',    color:'#ef4444' },
  pos:    { label:'Positive',    color:'#2FC6F6' },
  neu:    { label:'Neutral',     color:'#94a3b8' },
};

/* ── ESC helper ── */
const sntEsc = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');

/* ══════════════════════════════════════════════════════
   SENTIMENT POPUP
══════════════════════════════════════════════════════ */
const SNTPopup = {
  _cache:{}, _allItems:[], _curSent:'all', _curPlat:null,

  init() {
    document.addEventListener('mousedown', e => {
      const pp = document.getElementById('sntPlatPicker');
      if (pp?.classList.contains('visible') && !pp.contains(e.target)) pp.classList.remove('visible');
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') this.close();
    });
  },

  async open(platform, sentiment) {
    const popup   = document.getElementById('sntPopup');
    const overlay = document.getElementById('sntPanelOverlay');
    if (!popup) return;

    SNTDetail.close();
    this._curPlat = platform;
    this._curSent = sentiment || 'all';

    let dotColor, title;
    if (sentiment && sentiment !== 'all') {
      dotColor = SNTPlatMeta[sentiment]?.color || '#038047';
      const sentLabel = { neg:'Negative', pos:'Positive', neu:'Neutral' }[sentiment] || sentiment;
      const platLabel = platform === 'all' ? 'All Media'
        : platform === 'social' ? 'Social Media'
        : (SNTPlatMeta[platform]?.label || platform);
      title = `${sentLabel} — ${platLabel}`;
    } else {
      dotColor = platform === 'all' ? '#038047'
        : platform === 'social' ? '#038047'
        : (SNTPlatMeta[platform]?.color || '#038047');
      title = platform === 'all' ? 'All Media'
        : platform === 'social' ? 'Social Media'
        : (SNTPlatMeta[platform]?.label || platform);
    }

    document.getElementById('sntPopDot').style.background = dotColor;
    document.getElementById('sntPopTitle').textContent     = title;
    document.getElementById('sntPopMeta').textContent      = SNTCfg.sd + ' – ' + SNTCfg.ed;
    document.getElementById('sntPopCount').textContent     = '…';

    this._curSent = sentiment || 'all';
    document.querySelectorAll('.sntp-sent-tab').forEach(b => {
      b.classList.toggle('active', b.dataset.s === this._curSent);
    });

    const list = document.getElementById('sntPopList');
    list.innerHTML = `<div class="sntp-loading"><div class="sntp-spinner"></div>Memuat mentions…</div>`;

    // Slide-in panel + overlay
    popup.classList.remove('hiding');
    popup.classList.add('show');
    if (overlay) { overlay.classList.remove('hiding'); overlay.classList.add('show'); }
    document.body.style.overflow = 'hidden';

    const cacheKey = `${SNTCfg.pid}_${platform}_${SNTCfg.sd}_${SNTCfg.ed}`;
    try {
      if (!this._cache[cacheKey]) {
        this._cache[cacheKey] = await this._fetch(platform);
      }
      this._allItems = this._cache[cacheKey];
      this._renderFiltered(list);
    } catch(err) {
      list.innerHTML = `<div class="sntp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Gagal memuat data
      </div>`;
      document.getElementById('sntPopCount').textContent = '0';
    }
  },

  openSentiment(sentiment) {
    this.open('all', sentiment);
  },

  showPlatPicker(x, y, sentiment) {
    const pp = document.getElementById('sntPlatPicker');
    if (!pp) return;
    pp.dataset.sentiment = sentiment || 'all';
    const pw=185, ph=240, vw=window.innerWidth, vh=window.innerHeight;
    let left=x+10, top=y-10;
    if (left+pw > vw-8) left = x-pw-10;
    if (top+ph  > vh-8) top  = vh-ph-8;
    if (top < 8) top = 8;
    pp.style.left = left+'px';
    pp.style.top  = top+'px';
    pp.classList.add('visible');

    pp.querySelectorAll('.sntpp-btn').forEach(btn => {
      const plat = btn.getAttribute('onclick').match(/'([^']+)'/)?.[1];
      if (plat) {
        btn.setAttribute('onclick', `SNTPopup.openPlatform('${plat}','${sentiment||'all'}')`);
      }
    });
  },

  openPlatform(platform, sentiment) {
    const pp = document.getElementById('sntPlatPicker');
    if (pp) pp.classList.remove('visible');
    this.open(platform, sentiment||'all');
  },

  filterSent(sent) {
    this._curSent = sent;
    document.querySelectorAll('.sntp-sent-tab').forEach(b => {
      b.classList.toggle('active', b.dataset.s === sent);
    });
    const list = document.getElementById('sntPopList');
    this._renderFiltered(list);
  },

  close() {
    const popup   = document.getElementById('sntPopup');
    const overlay = document.getElementById('sntPanelOverlay');
    if (!popup || !popup.classList.contains('show')) return;
    SNTDetail.close();
    // Kill any iframes (TikTok/YT embeds) to stop playback
    const dpBody = document.getElementById('sntDpBody');
    if(dpBody) dpBody.querySelectorAll('iframe').forEach(f=>{f.src='';f.remove();});
    popup.classList.add('hiding');
    if (overlay) overlay.classList.add('hiding');
    setTimeout(() => {
      popup.classList.remove('show','hiding');
      if (overlay) overlay.classList.remove('show','hiding');
      document.body.style.overflow = '';
    }, 240);
  },

  exportCsv() {
    const items = this._getFiltered();
    if (!items.length) { alert('Tidak ada data untuk diekspor.'); return; }
    const platLabel = { doc:'Online_News', twit:'Twitter', fb:'Facebook', ig:'Instagram', yt:'YouTube', tiktok:'TikTok', all:'All_Media', social:'Social_Media' };
    const rows = items.map((item, idx) => {
      const name    = (item.author_name||item.channel_name||item.publisher||item.source_name||item.name||item.author_scr_name||item.screen_name||'').trim();
      const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500);
      const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
      const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'Positif':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'Negatif':'Netral';
      const url     = item.url||item.link||'';
      const date    = (item.date_created||item.created_at||'').split('T')[0];
      const esc2    = s => String(s||'').replace(/;/g,',').replace(/\n/g,' ');
      return `${idx};${esc2(name)};${esc2(sent)};${esc2(date)};${esc2(url)};${esc2(content)}`;
    });
    const header = 'index;nama;sentimen;tanggal;url;konten';
    const csv    = [header,...rows].join('\r\n');
    const blob   = new Blob(['\uFEFF'+csv], { type:'text/csv;charset=utf-8;' });
    const url    = URL.createObjectURL(blob);
    const a      = document.createElement('a');
    const lbl    = platLabel[this._curPlat] || this._curPlat;
    const snt    = this._curSent !== 'all' ? `_${this._curSent}` : '';
    a.href = url; a.download = `sentiment_${lbl}${snt}_${SNTCfg.sd}_${SNTCfg.ed}.csv`;
    a.click(); URL.revokeObjectURL(url);
  },

  async _fetch(platform) {
    const q = `project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&rows=500&start=0`;

    if (platform === 'social') {
      const socials = ['twit','fb','ig','yt','tiktok'];
      const results = await Promise.allSettled(socials.map(p => this._fetchOne(p, q)));
      let merged = [];
      results.forEach(r => { if (r.status==='fulfilled') merged = merged.concat(r.value); });
      return merged;
    }

    if (platform === 'all') {
      const all = ['doc','twit','fb','ig','yt','tiktok'];
      const results = await Promise.allSettled(all.map(p => this._fetchOne(p, q)));
      let merged = [];
      results.forEach(r => { if (r.status==='fulfilled') merged = merged.concat(r.value); });
      merged.sort((a,b) => {
        const da = a.date_created||a.created_at||'';
        const db = b.date_created||b.created_at||'';
        return db.localeCompare(da);
      });
      return merged;
    }

    return this._fetchOne(platform, q);
  },

  async _fetchOne(platform, q) {
    if (platform === 'ig') {
      const subs = ['postbylike','postbycomment','postbydate',''];
      for (const sub of subs) {
        const url = `/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`;
        try {
          const ctrl = new AbortController(), tid = setTimeout(()=>ctrl.abort(),15000);
          const res  = await fetch(url,{signal:ctrl.signal}); clearTimeout(tid);
          if (!res.ok) continue;
          const data  = await res.json();
          const items = Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
          if (items.length>0) { items.forEach(it => { if(!it._type) it._type = 'ig'; }); return items; }
        } catch(e){ continue; }
      }
      return [];
    }

    const eps = {
      doc:    `/mk/api/news/mentions?${q}`,
      twit:   `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      fb:     `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      yt:     `/mk/api/news/ytb-top-status?${q}`,
      tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };
    const url  = eps[platform]; if(!url) return [];
    const ctrl = new AbortController(), tid = setTimeout(()=>ctrl.abort(),30000);
    const res  = await fetch(url,{signal:ctrl.signal}); clearTimeout(tid);
    if (!res.ok) return [];
    const data = await res.json();
    let items  = Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
    items.forEach(it => { if(!it._type) it._type = platform; });

    if (platform==='doc') {
      items = items.filter(m => {
        const tc=String(m.tcode||'').toLowerCase();
        const mt=String(m.media_type||'').toLowerCase();
        return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';
      });
    }
    return items;
  },

  _normSent(item) {
    const raw = String(item.class_sentiment||item.sentiment||item.sentiment_str||'0').toLowerCase().trim();
    if (raw==='1'||raw==='positive'||raw==='positif') return 'pos';
    if (raw==='-1'||raw==='2'||raw==='negative'||raw==='negatif') return 'neg';
    return 'neu';
  },

  _getFiltered() {
    if (this._curSent === 'all') return this._allItems;
    return this._allItems.filter(item => this._normSent(item) === this._curSent);
  },

  _renderFiltered(list) {
    const items   = this._getFiltered();
    document.getElementById('sntPopCount').textContent = items.length.toLocaleString();

    const badge = document.getElementById('sntPopCount');
    const bColors = { neg:'#ef4444', pos:'#2FC6F6', neu:'#94a3b8', all:'var(--primary-green)' };
    badge.style.background = bColors[this._curSent] || 'var(--primary-green)';

    this._render(list, items);
  },

  _render(list, items) {
    if (!items.length) {
      list.innerHTML = `<div class="sntp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Tidak ada mention untuk filter ini
      </div>`;
      return;
    }

    const SHOW = 60;
    const getPlat = item => {
      if (item._type) return item._type;
      const mt = String(item.media_type||item.type||item.tcode||'').toLowerCase();
      if (mt.includes('doc')||mt.includes('news')||mt.includes('berita')) return 'doc';
      if (mt.includes('twit')||mt.includes('twitter')||mt.includes('x')) return 'twit';
      if (mt.includes('fb')||mt.includes('facebook')) return 'fb';
      if (mt.includes('ig')||mt.includes('instagram')) return 'ig';
      if (mt.includes('yt')||mt.includes('youtube')) return 'yt';
      if (mt.includes('tiktok')) return 'tiktok';
      return this._curPlat || 'doc';
    };

    list.innerHTML = items.slice(0, SHOW).map(item => {
      const plat = getPlat(item);
      const meta = SNTPlatMeta[plat] || { color:'#038047' };
      const color = meta.color;

      const name = (
        item.from_name||item.page_name||item.author_nickname||item.nickname||
        item.channel_title||item.channel_name||
        item.author_name||item.username||item.user_name||
        item.author_scr_name||item.screen_name||
        item.author?.name||item.author?.scr_name||
        item.publisher||item.source_name||item.name||'Tidak diketahui'
      ).trim();

      const isNumericId = /^\d{8,}$/.test(name);
      const displayName = isNumericId ? `User ${name.slice(-4)}` : name;

      const rawHandle = (item.author_scr_name||item.screen_name||item.author?.scr_name||item.username||item.handle||'').trim();
      const handle    = rawHandle && rawHandle.toLowerCase()!==displayName.toLowerCase()
        ? (['twit','ig','tiktok'].includes(plat)
            ? (rawHandle.startsWith('@')?rawHandle:'@'+rawHandle)
            : rawHandle)
        : '';

      const text = (item.content||item.caption||item.description||item.title||item.text||'')
        .replace(/<[^>]*>/g,'').trim().slice(0,155);

      const av = (item.avatar_url||item.profile_image_url||item.author_image||
                  item.author?.image||item.profile_image||item.thumbnail||item.picture||'').trim();

      const words = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini   = words.length>=2
        ? (words[0][0]+words[words.length-1][0]).toUpperCase()
        : (words[0]?.[0]||displayName[0]||'?').toUpperCase();
      const safeIni = ini.replace(/['"]/g,'');

      const avHtml = (av&&(av.startsWith('http://')||av.startsWith('https://')))
        ? `<img src="${sntEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
        : ini;

      const sent    = this._normSent(item);
      const sentLbl = { neg:'Neg', pos:'Pos', neu:'Neu' }[sent] || 'Neu';

      const dt = (item.date_created||item.created_at||item.publish_date||'').split('T')[0];

      const eng = (() => {
        const f = n => parseInt(n)||0>0?parseInt(n).toLocaleString():null;
        const parts=[];
        if (plat==='twit') { const vw=f(item.view_cnt||item.num_views),rt=f(item.rt||item.num_retweeted||item.retweet_count),lk=f(item.num_likes||item.favorite_count); if(vw)parts.push('Views '+vw); if(rt)parts.push('RT '+rt); if(lk)parts.push('Like '+lk); }
        else if(plat==='yt')     { const v=f(item.num_views||item.views),lk=f(item.num_likes||item.likes); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if(plat==='tiktok') { const v=f(item.views||item.play_count),lk=f(item.likes||item.digg_count); if(v)parts.push('Play '+v); if(lk)parts.push('Like '+lk); }
        else if(plat==='ig')     { const lk=f(item.num_likes||item.likes||item.like_count),cm=f(item.num_comments||item.comment_count); if(lk)parts.push('Like '+lk); if(cm)parts.push('Komen '+cm); }
        else if(plat==='fb')     { const lk=f(item.likes||item.num_likes||item.like_count),sh=f(item.shares||item.share_count); if(lk)parts.push('Like '+lk); if(sh)parts.push('Share '+sh); }
        return parts.join(' · ');
      })();

      const platDot = `<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${color};flex-shrink:0;"></span>`;
      const itemData = sntEsc(JSON.stringify(item));

      return `<div class="sntp-item" data-item='${itemData}' data-plat="${plat}" onclick="SNTPopup._onItemClick(this)">
        <div class="sntp-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
        <div class="sntp-item-body">
          <div class="sntp-item-author">${sntEsc(displayName)}</div>
          ${handle?`<div class="sntp-item-handle">${sntEsc(handle)}</div>`:''}
          <div class="sntp-item-text">${sntEsc(text||'(tidak ada konten)')}</div>
          <div class="sntp-item-footer">
            <span class="sntp-sent-badge sntp-sent-badge--${sent}">${sentLbl}</span>
            ${platDot}<span style="font-size:10px;">${meta.label||''}</span>
            ${eng?`<span>${sntEsc(eng)}</span>`:''}
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');

    if (items.length > SHOW) {
      list.insertAdjacentHTML('beforeend',
        `<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-SHOW).toLocaleString()} mentions lainnya</div>`);
    }
  },

  _onItemClick(el) {
    try {
      const raw  = el.getAttribute('data-item');
      const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      const plat = el.dataset.plat || this._curPlat || 'doc';
      SNTDetail.open(item, plat);
    } catch(e) { console.warn('SNT Detail parse error:', e); }
  }
};

/* ══════════════════════════════════════════════════════
   SENTIMENT DETAIL PANEL
══════════════════════════════════════════════════════ */
const SNTDetail = {
  open(item, platform) {
    const panel = document.getElementById('sntDetailPanel');
    const body  = document.getElementById('sntDpBody');
    const title = document.getElementById('sntDpTitle');
    const meta  = SNTPlatMeta[platform] || { label: platform, color:'#038047' };

    const name = (
      item.from_name||item.page_name||item.author_nickname||item.nickname||
      item.channel_title||item.channel_name||
      item.author_name||item.username||item.user_name||
      item.author_scr_name||item.screen_name||
      item.author?.name||item.author?.scr_name||
      item.publisher||item.source_name||item.name||'Tidak diketahui'
    ).trim();
    const isNumericId = /^\d{8,}$/.test(name);
    const displayName = isNumericId ? `User ${name.slice(-4)}` : name;

    const rawHandle = (item.author_scr_name||item.screen_name||item.author?.scr_name||item.username||item.handle||'').trim();
    const handle    = rawHandle && rawHandle.toLowerCase()!==displayName.toLowerCase()
      ? (rawHandle.startsWith('@')?rawHandle:'@'+rawHandle) : '';

    const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av = (item.avatar_url||item.profile_image_url||item.author_image||item.author?.image||item.profile_image||item.thumbnail||item.picture||'').trim();
    let url = item.url||item.link||'';
    // Build Twitter URL from sub_id if not present
    if (!url && platform==='twit') {
      const scr = rawHandle.replace(/^@/,'');
      const subId = item.sub_id||'';
      if (subId) url = `https://twitter.com/${encodeURIComponent(scr)}/status/${encodeURIComponent(subId)}`;
      else if (scr) url = `https://twitter.com/${encodeURIComponent(scr)}`;
    }
    const date = item.date_created||item.created_at||item.publish_date||'';

    const sentRaw = String(item.class_sentiment||item.sentiment||item.sentiment_str||'0').toLowerCase().trim();
    const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
    const sentLbl = {pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];

    title.textContent = displayName;

    const words   = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini     = words.length>=2?(words[0][0]+words[words.length-1][0]).toUpperCase():(words[0]?.[0]||displayName[0]||'?').toUpperCase();
    const safeIni = ini.replace(/['"]/g,'');
    const avHtml  = (av&&(av.startsWith('http://')||av.startsWith('https://')))
      ? `<img src="${sntEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    let dtFmt = '';
    if (date) {
      try { dtFmt = new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
      catch(e) { dtFmt = date.split('T')[0]; }
    }

    let mediaHtml = '';
    if (platform==='yt') {
      const ytId = (url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/)||[])[1];
      if (ytId) mediaHtml = `<div class="sntdp-media-wrap"><iframe style="width:100%;height:210px;border:none;display:block;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;
    } else if (platform==='tiktok') {
      let videoId='';
      if(url){ const m=url.match(/\/video\/(\d+)/); if(m) videoId=m[1]; }
      if(!videoId&&item.id){ const m=String(item.id).match(/(\d{10,})/); if(m) videoId=m[1]; }
      if(videoId) mediaHtml = `<div class="sntdp-media-wrap"><iframe style="width:100%;height:480px;border:none;display:block;" src="https://www.tiktok.com/embed/v2/${videoId}" allow="autoplay" allowfullscreen></iframe></div>`;
      else { const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||''; if(imgUrl) mediaHtml=`<div class="sntdp-media-wrap"><img class="sntdp-media-img" src="${sntEsc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`; }
    } else {
      const imgUrl = item.image_url||item.thumbnail||item.media_url||item.picture||'';
      if (imgUrl) mediaHtml = `<div class="sntdp-media-wrap"><img class="sntdp-media-img" src="${sntEsc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;
    }

    const statsMap = {
      twit:  [['Views',item.view_cnt||item.num_views||0],['Retweet',item.rt||item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0]],
      fb:    [['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],
      ig:    [['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],
      yt:    [['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],
      tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],
      doc:   [['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]],
    };
    const stats     = statsMap[platform] || [];
    const statsHtml = stats.some(s=>parseInt(s[1])>0)
      ? `<div class="sntdp-stats-grid">${stats.map(([l,v])=>`<div class="sntdp-stat-box"><div class="sntdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="sntdp-stat-lbl">${l}</div></div>`).join('')}</div>` : '';

    body.innerHTML = `
      <div class="sntdp-avatar-row">
        <div class="sntdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div>
          <div class="sntdp-author-name">${sntEsc(displayName)}</div>
          ${handle?`<div class="sntdp-author-handle">${sntEsc(handle)}</div>`:''}
          <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
        </div>
      </div>
      ${dtFmt?`<div class="sntdp-meta-row"><span>${dtFmt}</span></div>`:''}
      <span class="sntdp-sent-badge sntdp-sent-badge--${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content?`<div class="sntdp-content-text">${sntEsc(content)}</div>`:''}
      ${statsHtml}
      ${url?`<a href="${sntEsc(url)}" target="_blank" rel="noopener noreferrer" class="sntdp-link-btn">
        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Sumber Asli
      </a>`:''}`;

    panel.classList.add('visible');
  },
  close() { document.getElementById('sntDetailPanel')?.classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   PATCH Chart & Render functions
══════════════════════════════════════════════════════ */

const _origOverviewBar = renderOverviewBar;
window.renderOverviewBar = function() {
  _origOverviewBar();
  const chart = SNTCharts._i['chOverview'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origSovDoughnut = renderSovDoughnut;
window.renderSovDoughnut = function(domId, labels, values, colors, ready=false) {
  _origSovDoughnut(domId, labels, values, colors, ready);
  const chart = SNTCharts._i[domId];
  if (!chart) return;
  chart.on('click', params => {
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    if (domId === 'chSovTotal') {
      SNTPopup.open('all', sent);
    } else if (domId === 'chMassPie') {
      SNTPopup.open('doc', sent);
    } else if (domId === 'chSocialPie') {
      const rect = chart.getDom().getBoundingClientRect();
      SNTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+rect.height/2, sent);
    }
  });
  chart.on('mouseover', () => { chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origMassSocialBars = renderMassSocialBars;
window.renderMassSocialBars = function() {
  _origMassSocialBars();
  ['chMass','chSocial'].forEach(id => {
    const chart = SNTCharts._i[id];
    if (!chart) return;
    const isMass = id === 'chMass';
    chart.on('click', params => {
      if (params.componentType !== 'series') return;
      const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
      const sent = sentMap[params.seriesName] || 'all';
      if (isMass) {
        SNTPopup.open('doc', sent);
      } else {
        const rect = chart.getDom().getBoundingClientRect();
        SNTPopup.showPlatPicker(rect.left+params.event.offsetX, rect.top+params.event.offsetY, sent);
      }
    });
    chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
    chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
  });
};

const _origByTypePct = renderByTypePct;
window.renderByTypePct = function() {
  _origByTypePct();
  const chart = SNTCharts._i['chByType'];
  if (!chart) return;
  const labelToKey = {
    'Mass Media':'doc', 'X / Twitter':'twit', 'Facebook':'fb',
    'Instagram':'ig',   'YouTube':'yt',        'TikTok':'tiktok',
  };
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const bm   = SNTData.byMedia;
    const plat = labelToKey[bm[params.dataIndex]?.label] || 'doc';
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open(plat, sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origByPlatGrouped = renderByPlatGrouped;
window.renderByPlatGrouped = function() {
  _origByPlatGrouped();
  const chart = SNTCharts._i['chByPlat'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    const groups = ['Mass Media','Social Media'];
    const grp = groups[params.dataIndex];
    if (grp === 'Mass Media') {
      SNTPopup.open('doc', sent);
    } else {
      const rect = chart.getDom().getBoundingClientRect();
      SNTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+params.event.offsetY, sent);
    }
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origTrend = renderTrend;
window.renderTrend = function() {
  _origTrend();
  const chart = SNTCharts._i['chTrend'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origTimeChart = renderTimeChart;
window.renderTimeChart = function(domId, skelId, labels, negData, posData, neuData, totals, isHour=false) {
  _origTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour);
  const chart = SNTCharts._i[domId];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

/* ── Patch SNTPage.init & reload ── */
const _origSNTPageInit = SNTPage.init.bind(SNTPage);
SNTPage.init = function() {
  SNTPopup.init();
  _origSNTPageInit();
};

const _origSNTPageReload = SNTPage.reload.bind(SNTPage);
SNTPage.reload = function() {
  SNTPopup._cache = {};
  _origSNTPageReload();
};
</script>
@endsection