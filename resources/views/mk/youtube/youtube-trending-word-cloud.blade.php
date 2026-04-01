@extends('mk.layouts.app')

@section('title', 'YouTube Word Cloud - SMADIMENT')

@section('styles')
<style>
:root{--primary:#038047;--primary-rgb:3,128,71;--primary-lt:rgba(3,128,71,.10);--dark:#273B4A;--white:#FFFFFF;--bg:#F1F5F8;--green:#038047;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;--slate-800:#1E293B;--slate-900:#0F172A;--radius:8px;--radius-sm:5px;--shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12)}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}
@keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
.spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}
.kpi-card-hover{will-change:transform,box-shadow;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important}
.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1}
.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}
.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}
.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}
.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}
.kpi-card-hover:active{transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important}
.kpi-card-hover h3{font-size:1.5rem}
.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}
.chart-empty i{font-size:34px;color:var(--slate-300);display:block}

/* ══ Sentiment Filter Tabs ══ */
.sent-tabs{display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px}
.sent-tab{flex:0 0 auto;display:flex;align-items:center;justify-content:center;gap:5px;padding:6px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap}
.sent-tab:hover{background:#fff;color:var(--slate-800)}
.sent-tab.active{background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.sent-tab.tab-pos.active{color:#16a34a}
.sent-tab.tab-neg.active{color:#dc2626}
.sent-tab.tab-neu.active{color:#b45309}
.sent-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}

.ht-list{display:flex;flex-direction:column}
.ht-item{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--slate-100);cursor:pointer;transition:background .12s}
.ht-item:last-child{border-bottom:none}
.ht-item:hover{background:var(--slate-50)}
.ht-rank{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--slate-400);background:var(--slate-100);border:1px solid var(--slate-200)}
.ht-rank--1{background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700}
.ht-rank--2{background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0}
.ht-rank--3{background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32}
.ht-name{flex:1;min-width:0;font-size:13px;font-weight:700;color:var(--primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ht-bar-wrap{flex:0 0 100px;height:6px;background:var(--slate-100);border-radius:99px;overflow:hidden}
.ht-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),rgba(3,128,71,.5));transition:width .4s cubic-bezier(.4,0,.2,1)}
.ht-count{font-size:11px;font-weight:700;color:var(--slate-500);white-space:nowrap;flex-shrink:0;min-width:36px;text-align:right}
.tme-pagination{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px}
.tme-pag-info{font-size:11px;color:var(--slate-400);font-weight:500}
.tme-pag-controls{display:flex;align-items:center;gap:3px}
.tme-pag-btn{min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none}
.tme-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
.tme-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}
.tme-pag-btn:disabled{opacity:.35;cursor:not-allowed}
.mode-toggle{display:inline-flex;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;gap:2px}
.mode-btn{padding:4px 10px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s}
.mode-btn.active{background:#fff;color:var(--primary);box-shadow:0 1px 3px rgba(0,0,0,.07)}

/* ══ Export ══ */
.page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
.page-export-bar-left i{font-size:15px;color:var(--primary)}
.page-export-bar-right{display:flex;gap:8px}
.page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
.page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
.page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
.page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
.page-export-btn .exp-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.page-export-btn.exporting .exp-spinner{display:inline-block}
.page-export-btn.exporting .exp-icon{display:none}
.card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
.card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
.card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.card-exp-btn-img{color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt)}
.card-exp-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
.card-exp-btn .exp-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.card-exp-btn.exporting .exp-spinner{display:inline-block}
.card-exp-btn.exporting .exp-icon{display:none}
.export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
.export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.export-toast.success{background:#065f46}
.export-toast.error{background:#991b1b}

/* ══ Word Cloud Container ══ */
.wordcloud-container{
    position:relative;
    width:100%;
    height:600px;
    background:#fff;
    border-radius:var(--radius);
    overflow:hidden;
}
.wordcloud-container #wcLoading,
.wordcloud-container #wcEmpty{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;z-index:3}
.wordcloud-container #wcLoading{background:#fff;z-index:3}
.wordcloud-container #wcEmpty{color:var(--slate-400);font-size:12px;font-weight:600}
.wordcloud-container #wcEmpty i{font-size:34px;color:var(--slate-300)}
#wordCloudChart{
    width:100%!important;
    height:100%!important;
    display:block;
    position:absolute;
    top:0;left:0;
    z-index:1;
}
</style>
@endsection

@section('page-title', 'YouTube Word Cloud')

@section('content')
@php
    $projectId    = $projectId    ?? request()->get('project_id');
    $startDate    = $startDate    ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate      = $endDate      ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects     = $projects     ?? [];
    $hashtagsJson = $hashtagsJson ?? '[]';
@endphp
<script>
    const OV_PID        = '{{ $projectId }}';
    const OV_SD         = '{{ $startDate }}';
    const OV_ED         = '{{ $endDate }}';
    const PRELOADED_RAW = {!! $hashtagsJson !!};
</script>

@include('mk.layouts.partials.filter-datepicker')

<div id="pageExportArea">

{{-- KPI --}}
<div class="row mb-3 g-3">
    <div class="col-sm-6 col-xl">
        <div class="card h-100 text-white kpi-card-hover" style="background:#EF4444;animation:fadeUp .38s ease-out .00s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiTopics">-</h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopicsSub"><i class="ph ph-hash me-1"></i>-</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-hash"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card h-100 text-white kpi-card-hover" style="background:#1D9BF0;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Volume</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiVolume">-</h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiVolumeSub"><i class="ph ph-chart-bar me-1"></i>-</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card h-100 text-white kpi-card-hover" style="background:#10B981;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Positif</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiPos">-</h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-smiley me-1"></i>-</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card h-100 text-white kpi-card-hover" style="background:#273B4A;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Negatif</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiNeg">-</h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-smiley-sad me-1"></i>-</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="card h-100 text-white kpi-card-hover" style="background-color:#F59E0B; animation:fadeUp .38s ease-out .20s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Netral</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiNeu">-</h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-smiley-blank me-1"></i>-</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-blank"></i></div></div></div></div>
        </div>
    </div>
</div>

{{-- Page Export Bar --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Word Cloud + Top Topics</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="YTWCExport.run('pdf',this)" title="Export PDF">
            <i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="YTWCExport.run('image',this)" title="Export PNG">
            <i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span>
        </button>
    </div>
</div>

{{-- Content --}}
<div class="row">
    <div class="col-lg-8 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-wc">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-cloud f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Word Cloud</h6>
                            <small class="text-muted" id="wcSubtitle">Klik kata untuk cari di YouTube</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        {{-- Sentiment Filter Tabs --}}
                        <div class="sent-tabs" data-html2canvas-ignore="true">
                            <button class="sent-tab active" data-s="all">Semua</button>
                            <button class="sent-tab tab-pos" data-s="positive"><span class="sent-dot" style="background:#16a34a;"></span>Positif</button>
                            <button class="sent-tab tab-neg" data-s="negative"><span class="sent-dot" style="background:#dc2626;"></span>Negatif</button>
                            <button class="sent-tab tab-neu" data-s="neutral"><span class="sent-dot" style="background:#b45309;"></span>Netral</button>
                        </div>
                        <span class="badge bg-light-primary text-primary" id="badgeWC">Loading…</span>
                        <div data-html2canvas-ignore="true" class="d-flex gap-1">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="YTWCExport.runCard('card-export-wc','wordcloud','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="YTWCExport.runCard('card-export-wc','wordcloud','image',this)" title="Export PNG"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="wordcloud-container">
                        <div id="wcLoading"><div class="spin-ring"></div><span style="color:var(--slate-400);font-size:12px;font-weight:600;">Memuat word cloud…</span></div>
                        <div id="wordCloudChart"></div>
                        <div id="wcEmpty" style="display:none;"><i class="ph ph-cloud-slash"></i><span>Tidak ada data topic</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-topics">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-list-numbers f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Top Topics</h6><small class="text-muted">Ranked by volume</small></div>
                    </div>
                    <div data-html2canvas-ignore="true" class="d-flex gap-1">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="YTWCExport.runCard('card-export-topics','topics','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="YTWCExport.runCard('card-export-topics','topics','image',this)" title="Export PNG"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
                    </div>
                </div>
                <div id="topicLoading" class="spinner-state"><div class="spin-ring"></div><span>Memuat…</span></div>
                <div id="topicContent" style="display:none;"><div id="topicList" class="ht-list"></div><div id="pagArea"></div></div>
                <div id="topicEmpty" style="display:none;" class="chart-empty"><i class="ph ph-hash"></i><span>Tidak ada data</span></div>
            </div>
        </div>
    </div>
</div>

</div>{{-- /pageExportArea --}}

<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

<script>
'use strict';

const CFG  = { pid: OV_PID, sd: OV_SD, ed: OV_ED };
const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

let allTopics = [], filtered = [], curPage = 1, curSent = 'all', curMode = 'hashtag';
const PP = 15;
let wcChart = null;

/* ══ Stopwords ══ */
const SW = new Set(['the','a','an','and','or','but','in','on','at','to','for','of','with','is','are','was','were','be','been','have','has','had','do','does','did','will','would','could','should','may','might','this','that','these','those','i','you','he','she','we','they','it','my','your','his','her','our','their','me','him','us','them','dari','dan','ke','di','yang','dengan','untuk','ini','itu','ada','tidak','bisa','akan','juga','sudah','pada','atau','dalam','oleh','karena','kita','anda','kami','mereka','ya','jadi','tapi','kalau','aja','saja','pun','lebih','seperti','masih','harus','kali','video','youtube','channel','watch','subscribe','like','comment','share','link','click','here','now','new','how','what','why','when','where','who','all','get','let','amp','via','http','https','www','com','co','id','nih','yuk','yg','nya','si','loh','deh','sih','dong','kok','udah','sama','mau','apa','kalo','gimana','banget','guys','ep','eps','full','part','official','ft','feat','cover','live']);

/* ══ Sentiment Keywords ══ */
const NEG_SET = new Set([
    'bad','worst','worse','hate','hated','hating','sad','sadness','fail','failed','failing','failure',
    'lose','lost','losing','loss','loser','angry','anger','furious','rage','raging',
    'terrible','horrible','awful','dreadful','disgusting','poor','dead','death','die','died','dying',
    'kill','killed','killing','killer','murder','murdered','murderer','suicide','suicidal',
    'corrupt','corruption','corrupted','crime','criminal','criminals','fraud','fraudulent','scam','scammer',
    'lie','lies','lied','lying','liar','fake','hoax','misinformation','disinformation','mislead','misleading',
    'manipulate','manipulation','manipulated','abuse','abused','abuser','abusive',
    'terror','terrorist','terrorism','attack','attacked','war','riot','riots','rioting','scandal',
    'boycott','crisis','disaster','disasters','catastrophe','wrong','hurt','hurting','suffer','suffering',
    'violence','violent','victim','victims','bankrupt','bankruptcy',
    'drug','drugs','addict','addiction','accident','accidents',
    'flood','earthquake','landslide','wildfire','tornado','hurricane','tsunami',
    'crash','explosion','explode','exploded','rape','raped','robbery','robbed','theft','steal','stealing','stolen',
    'illegal','unlawful','arrested','arrest','imprisoned','prison','jail','guilty','convicted','sentenced',
    'banned','suspended','fired','dismissed','resign','resigned','resignation',
    'shutdown','collapse','collapsed','broke','broken','damage','damaged','destroy','destroyed',
    'penalty','punish','punished','shame','shameful','disgrace','disgraced','humiliate','humiliated',
    'embarrass','embarrassed','betrayal','betrayed','betray','deceive','deceived','deception',
    'oppression','oppressed','oppress','exploit','exploited','exploitation',
    'discriminate','discrimination','harass','harassment','intimidate','intimidation',
    'depress','depressed','depression','anxiety','anxious','panic','panicked',
    'poverty','starving','starvation','homeless','neglect','neglected',
    'cheat','cheated','cheating','bribe','bribery','toxic','poisoned','poisonous',
    'polluted','pollution','contaminate','contaminated','infected','infection','diseased','pandemic','epidemic',
    'protest','protests','protesting','unrest','conflict','warfare','battle','battles','fighting',
    'vandalism','vandalize','arson','looting','chaos','anarchy','anarchist',
    'threat','threatened','threatening','dangerous','danger','hazard','hazardous',
    'racist','racism','sexist','sexism','bigot','bigotry','xenophobia','xenophobic',
    'bully','bullied','bullying','cyberbully','cyberbullying',
    'crises','emergency','critical','catastrophic','devastating',
    'counterfeit','forgery','forged','plagiarism','plagiarize',
    'buruk','terburuk','benci','membenci','sedih','kesedihan','gagal','kegagalan',
    'kalah','kekalahan','marah','kemarahan','murka','amarah','geram','berang',
    'parah','mengerikan','menakutkan','menjijikkan','mati','kematian','meninggal',
    'tewas','wafat','bunuh','membunuh','pembunuhan','bunuhdiri',
    'korupsi','koruptor','korup','kejahatan','kriminal','penipuan','penipu','curang','kecurangan',
    'bohong','berbohong','pembohong','dusta','fitnah','hoaks','palsu','menyesatkan',
    'manipulasi','memanipulasi','pelecehan','peleceh','kekerasan','brutal','sadis','kejam',
    'teror','teroris','terorisme','serangan','menyerang','perang','huru-hara','rusuh','kerusuhan',
    'skandal','krisis','bencana','malapetaka','musibah','celaka','petaka',
    'salah','rusak','hancur','menghancurkan','kehancuran','kerusakan',
    'sakit','penyakit','derita','menderita','sengsara','nestapa','duka','cemas',
    'narkoba','narkotika','pecandu',
    'kecelakaan','tabrakan','ledakan','meledak','kebakaran','kebanjiran','banjir',
    'gempa','longsor','tsunami',
    'pemerkosaan','memperkosa','perampokan','merampok','pencurian','mencuri','dicuri',
    'pungli','suap','menyuap','gratifikasi',
    'ilegal','ditangkap','penangkapan','dipenjara','penjara',
    'divonis','vonis','hukuman','dihukum','terdakwa','tersangka','pidana',
    'dilarang','dicabut','dibekukan','disita','dirampas','dihapus','ditolak',
    'dipecat','pemecatan',
    'bangkrut','pailit','rugi','kerugian','kebangkrutan',
    'malu','memalukan','aib','tercela','terhina','penghinaan',
    'pengkhianatan','mengkhianati','berkhianat','menipu','tipu',
    'penindasan','menindas','tertindas','eksploitasi','mengeksploitasi','diskriminasi',
    'melecehkan','intimidasi','mengintimidasi',
    'depresi','kecemasan','panik','trauma','stres','tertekan',
    'kemiskinan','miskin','melarat','kelaparan','gelandangan','tunawisma','terlantar',
    'terbuang','dikucilkan','diabaikan','ditelantarkan',
    'beracun','racun','tercemar','pencemaran','polusi','terinfeksi','wabah','pandemi','epidemi',
    'protes','demonstrasi','demo','bentrok','konflik','pertikaian','pertengkaran',
    'ancaman','mengancam','berbahaya','bahaya','darurat','kritis',
    'rasis','rasisme','diskriminatif','perundungan',
    'gelap','suram','kelam','muram','galau','resah','gundah','khawatir','takut',
    'gagalkan','kacau','kekacauan','anarki','perusakan','merusak','vandalisme',
    'pemalsuan','tiruan','plagiat',
    'penjahat','bajingan','brengsek','laknat','terkutuk','terlaknat',
    'dipermalukan','dicerca','dicaci','dimaki','dihujat','hujatan',
    'sampah','limbah','jorok','kotor','busuk','najis',
    'susah','sulit','kesulitan','hambatan','keterpurukan',
]);

const POS_SET = new Set([
    'win','won','winning','winner','best','good','great','love','loved','loving','happy','happiness',
    'success','successful','succeed','succeeded','amazing','excellent','awesome','superb','outstanding',
    'celebrate','celebrated','celebration','proud','pride','champion','champions','championship',
    'victory','victorious','achieve','achieved','achievement','achievements',
    'congratulations','congratulate','congratulated','hope','hopeful','hoping',
    'inspire','inspired','inspiring','inspiration','wonderful','beautiful','brilliant','magnificent',
    'fantastic','legend','legendary','hero','heroes','heroic','progress','progressive','growth','growing',
    'peace','peaceful','prosperous','prosperity','freedom','free','liberate','liberated','liberation',
    'healthy','health','strong','strength','powerful','brave','bravery','courage','courageous',
    'smart','intelligent','genius','innovative','innovation','creative','creativity',
    'talented','talent','skilled','skill','expert','master','professional','professionalism',
    'improve','improved','improvement','advance','advanced','upgrade','upgraded',
    'build','built','building','develop','developed','development','grew','grown',
    'rise','risen','rising','boost','boosted','boosting',
    'help','helped','helping','support','supported','supporting',
    'care','caring','cared','kind','kindness','generous','generosity',
    'grateful','gratitude','thankful','blessed','blessing','bless',
    'joy','joyful','cheerful','cheer','smile','smiling','laugh','laughter','fun',
    'enjoy','enjoyed','enjoying','pleasure','pleased','delight','delighted','delightful',
    'perfect','perfection','phenomenal','incredible','extraordinary','remarkable','unbelievable',
    'award','awarded','reward','rewarded','recognition','recognized','honor','honored','honoured',
    'respect','respected','trust','trusted','trustworthy','loyal','loyalty',
    'unity','united','together','togetherness','community','solidarity','cooperation','collaborative',
    'launch','launched','milestone','record','historic','first','top','trending','viral',
    'popular','famous','iconic','premium','quality','excellence','superior',
    'safe','safety','secure','security','protect','protected','protection',
    'fair','fairness','justice','just','righteous','honest','honesty','integrity',
    'clean','transparent','transparency','accountable','accountability',
    'menang','kemenangan','juara','kejuaraan','terbaik','unggulan',
    'baik','bagus','keren','hebat','luarbiasa','fantastis','menakjubkan',
    'cinta','mencintai','kasih','sayang','menyayangi','peduli','kepedulian',
    'senang','kesenangan','bahagia','kebahagiaan','gembira','ria','ceria','sukacita',
    'sukses','kesuksesan','berhasil','keberhasilan','prestasi','berprestasi','meraih','diraih',
    'bangga','kebanggaan','semangat','antusias','optimis','positif',
    'harapan','berharap','optimisme','impian','mimpi',
    'inspirasi','menginspirasi','terinspirasi','motivasi','memotivasi','termotivasi',
    'indah','cantik','tampan','elok','molek','permai','menawan','memesona',
    'cemerlang','brilian','cerdas','pandai','pintar','jenius','berbakat','bakat',
    'maju','kemajuan','berkembang','perkembangan','pertumbuhan','tumbuh','meningkat','peningkatan',
    'inovatif','inovasi','kreatif','kreativitas','solusi',
    'ahli','pakar','profesional','berpengalaman','kompeten','kompetensi','terampil','keahlian',
    'damai','kedamaian','harmonis','harmoni','rukun','kerukunan',
    'sejahtera','kesejahteraan','makmur','kemakmuran','merdeka','kebebasan',
    'sehat','kesehatan','bugar','kebugaran','kuat','kekuatan','tangguh',
    'berani','keberanian','gagah','perkasa',
    'bersatu','kebersamaan','solidaritas','kerjasama','kompak','bersama',
    'syukur','bersyukur','berkah','karunia','anugerah','rezeki',
    'penghargaan','apresiasi','diapresiasi','dihargai','diakui','pengakuan',
    'terpercaya','amanah','jujur','kejujuran','integritas','transparan','keterbukaan',
    'adil','keadilan','merata','pemerataan',
    'bersih','kebersihan','rapi','kerapian','tertib','ketertiban',
    'aman','keamanan','terlindungi','perlindungan',
    'diresmikan','diluncurkan','terpilih','dipercaya','dianugerahi',
    'rekor','bersejarah','lolos','lulus','diterima',
    'populer','hits','booming','digemari','favorit',
    'spesial','istimewa','berkualitas','terjamin',
    'peringkat','rangking','terdepan',
]);

function getSent(name) {
    const clean  = name.toLowerCase().replace(/^[#@]+/, '');
    const tokens = clean.split(/[\s_\-\.\/\\|&]+/).filter(Boolean);
    let negScore = 0, posScore = 0;
    for (const tok of tokens) {
        if (NEG_SET.has(tok)) negScore++;
        if (POS_SET.has(tok)) posScore++;
    }
    if (tokens.length === 1 && clean.length > 4) {
        for (const kw of NEG_SET) { if (kw.length >= 4 && clean.includes(kw)) negScore += 0.5; }
        for (const kw of POS_SET) { if (kw.length >= 4 && clean.includes(kw)) posScore += 0.5; }
    }
    if (negScore > posScore) return 'negative';
    if (posScore > negScore) return 'positive';
    return 'neutral';
}

/* ══ Extract helpers ══ */
function extractHashtags(posts) {
    const c = {};
    posts.forEach(p => {
        [p.content||'', p.title||'', p.name||''].join(' ')
            .match(/#([a-zA-Z0-9_\u00C0-\u024F\u0400-\u04FF]+)/gu)
            ?.forEach(t => { const k=t.toLowerCase(); if(k.length>3) c[k]=(c[k]||0)+1; });
    });
    return Object.entries(c).sort((a,b)=>b[1]-a[1]).map(([name,size])=>({name,size}));
}

function extractKeywords(posts) {
    const c = {};
    posts.forEach(p => {
        [p.title||'', p.name||'', p.content||''].join(' ')
            .toLowerCase()
            .replace(/https?:\/\/\S+/g,'')
            .replace(/[^a-z0-9\u00C0-\u024F\u0400-\u04FF\s]/gu,' ')
            .split(/\s+/)
            .forEach(w => {
                w = w.trim();
                if (w.length < 3 || /^\d+$/.test(w) || SW.has(w)) return;
                c[w] = (c[w]||0) + 1;
            });
    });
    return Object.entries(c).filter(([,v])=>v>=2).sort((a,b)=>b[1]-a[1]).slice(0,150).map(([name,size])=>({name,size}));
}

/* ══ Load Data — 4-step fallback ══ */
async function loadData() {
    if (!CFG.pid) { showEmpty(); return; }

    if (Array.isArray(PRELOADED_RAW) && PRELOADED_RAW.length >= 3) {
        const data = PRELOADED_RAW
            .map(t => ({ name:String(t.hashtag||t.name||'').trim(), size:+(t.size||t.total_volume||t.appearances||1) }))
            .filter(t => t.name && t.size > 0);
        if (data.length >= 3) {
            const isKw = data.filter(t=>!t.name.startsWith('#')).length > data.length * 0.5;
            buildTopics(data, isKw ? 'keyword' : 'hashtag'); return;
        }
    }

    try {
        const r = await fetch(`/mk/api/youtube/trending-topics?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}`);
        const j = await r.json();
        if (j.success && j.data?.hashtags?.length >= 3) {
            const data = j.data.hashtags.map(t=>({name:(t.hashtag||t.name||'').trim(),size:+(t.size||1)})).filter(t=>t.name&&t.size>0);
            if (data.length >= 3) { const isKw=data.filter(t=>!t.name.startsWith('#')).length>data.length*.5; buildTopics(data,isKw?'keyword':'hashtag'); return; }
        }
    } catch(e) { console.warn('[YTWC] Step 2 failed:', e.message); }

    try {
        const r     = await fetch(`/mk/api/youtube/most-engagement?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}&sub=postbyview&rows=500`);
        const j     = await r.json();
        const posts = (j.success && Array.isArray(j.data)) ? j.data : [];
        if (posts.length) {
            window._rawPosts    = posts;
            const hashtags      = extractHashtags(posts);
            const keywords      = extractKeywords(posts);
            window._hashtagData = hashtags;
            window._keywordData = keywords;
            if (hashtags.length >= 5) { buildTopics(hashtags,'hashtag'); }
            else if (keywords.length >= 3) {
                const merged = [...hashtags.map(t=>({name:t.name,size:t.size*3})),...keywords].sort((a,b)=>b.size-a.size);
                buildTopics(merged, keywords.length>hashtags.length?'keyword':'hashtag');
            } else { showEmpty(); }
            return;
        }
    } catch(e) { console.warn('[YTWC] Step 3 failed:', e.message); }

    showEmpty();
}

function buildTopics(data, mode) {
    curMode = mode;
    document.querySelectorAll('.mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode));
    _$('wcSubtitle').textContent = mode==='keyword'
        ? 'Keyword dari judul video — klik untuk cari di YouTube'
        : 'Klik hashtag untuk cari di YouTube';
    allTopics = data.map(t=>({name:String(t.name||''),size:t.size,sent:getSent(String(t.name||''))})).filter(t=>t.name);
    if (!allTopics.length) { showEmpty(); return; }
    applyFilter();
}

function switchMode(mode) {
    if (mode === curMode) return;
    if (mode === 'hashtag') {
        const ht = window._hashtagData||(window._rawPosts?extractHashtags(window._rawPosts):[]);
        if (ht.length) { window._hashtagData=ht; buildTopics(ht,'hashtag'); }
    } else {
        const kw = window._keywordData||(window._rawPosts?extractKeywords(window._rawPosts):[]);
        if (kw.length) { window._keywordData=kw; buildTopics(kw,'keyword'); }
    }
}

function applyFilter() {
    filtered = curSent==='all' ? [...allTopics] : allTopics.filter(t=>t.sent===curSent);
    curPage  = 1;
    updateKpi(); renderWC(); renderList();
}

function updateKpi() {
    const n=allTopics.length, vol=allTopics.reduce((s,t)=>s+t.size,0);
    const pos=allTopics.filter(t=>t.sent==='positive').length;
    const neg=allTopics.filter(t=>t.sent==='negative').length;
    const neu=allTopics.filter(t=>t.sent==='neutral').length;
    const el=(id,v)=>{const e=_$(id);if(e)e.textContent=numF(v);};
    el('kpiTopics',n);   _$('kpiTopicsSub').innerHTML=`<i class="ph ph-hash me-1"></i>${numF(n)} topics`;
    el('kpiVolume',vol); _$('kpiVolumeSub').innerHTML=`<i class="ph ph-chart-bar me-1"></i>Avg ${numF(n?Math.round(vol/n):0)} / topic`;
    el('kpiPos',pos);    _$('kpiPosSub').innerHTML=`<i class="ph ph-smiley me-1"></i>${n?(pos/n*100).toFixed(1):0}% of topics`;
    el('kpiNeg',neg);    _$('kpiNegSub').innerHTML=`<i class="ph ph-smiley-sad me-1"></i>${n?(neg/n*100).toFixed(1):0}% of topics`;
    el('kpiNeu',neu);    _$('kpiNeuSub').innerHTML=`<i class="ph ph-smiley-blank me-1"></i>${n?(neu/n*100).toFixed(1):0}% of topics`;
    _$('badgeWC').textContent = numF(filtered.length) + ' topics';
}

function showEmpty() {
    ['wcLoading','wcEmpty','topicLoading','topicEmpty'].forEach(id=>{
        const e=_$(id); if(e) e.style.display=id.includes('Loading')?'none':'flex';
    });
    ['kpiTopics','kpiVolume','kpiPos','kpiNeg','kpiNeu'].forEach(id=>{
        const e=_$(id); if(e) e.textContent='0';
    });
}

/* ══ Word Cloud Render ══ */
function renderWC() {
    const ld=_$('wcLoading'), ch=_$('wordCloudChart'), em=_$('wcEmpty');
    if (!filtered.length) {
        if(ld) ld.style.display='none';
        if(em) em.style.display='flex';
        if(wcChart){try{wcChart.dispose();}catch(e){}wcChart=null;}
        return;
    }
    if(em) em.style.display='none';
    const raw  = filtered.slice(0, 100);

    const sizes    = raw.map(t => t.size);
    const maxSize  = Math.max(...sizes);
    const minSize  = Math.min(...sizes);

    const data = raw.map(t => ({
        name  : t.name.replace(/^#/, ''),
        value : Math.pow((t.size - minSize) / (maxSize - minSize || 1), 0.5) * 1200 + 200,
        _orig : t.size,
    }));
    if(wcChart){try{wcChart.dispose();}catch(e){}wcChart=null;}
    requestAnimationFrame(()=>{
        if(ld) ld.style.display='none';
        const rect=ch.getBoundingClientRect();
        if(rect.width===0||rect.height===0){ if(ld)ld.style.display='flex'; setTimeout(()=>renderWC(),120); return; }
        wcChart=echarts.init(ch,null,{renderer:'canvas',width:rect.width,height:rect.height});
        const colorsBySent = {
            positive: ['#10B981','#059669','#34d399','#065f46','#064e3b','#a7f3d0'],
            negative: ['#273B4A','#475569','#64748B','#1E293B','#334155','#94A3B8'],
            neutral:  ['#F59E0B','#d97706','#fbbf24','#92400e','#78350f','#fef3c7'],
            all:      ['#EF4444','#1D9BF0','#10B981','#273B4A','#F59E0B','#8b5cf6','#06b6d4','#ec4899'],
        };
        const colorPool = colorsBySent[curSent] || colorsBySent.all;
        wcChart.setOption({
            backgroundColor:'transparent',
            tooltip: {
                show: true,
                trigger: 'item',
                backgroundColor: '#fff',
                borderColor: '#E2E8F0',
                borderWidth: 1,
                padding: [10, 14],
                textStyle: { color: '#0F172A', fontSize: 12, fontFamily: 'inherit' },
                shadowBlur: 20,
                shadowColor: 'rgba(0,0,0,.10)',
                shadowOffsetY: 4,
                formatter: p => `
                    <div style="font-family:inherit;min-width:130px;text-align:center;">
                        <div style="font-weight:700;font-size:14px;color:#0F172A;margin-bottom:4px;">${p.name}</div>
                        <div style="font-size:11px;color:#64748B;">${numF(p.data._orig || 0)} mentions</div>
                    </div>`,
            },
            series:[{
                type:'wordCloud',
                shape:'circle',
                left:'center',
                top:'center',
                width:'98%',
                height:'98%',
                sizeRange:[16,72],
                rotationRange:[-45,45],
                rotationStep:45,
                gridSize:8,
                drawOutOfBound:false,
                layoutAnimation:true,
                textStyle: {
                    fontFamily: 'Poppins, Inter, sans-serif',
                    fontWeight: 'bold',
                    color: function () {
                        return colorPool[Math.floor(Math.random() * colorPool.length)];
                    }
                },
                emphasis:{
                    focus:'self',
                    textStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,0.35)'}
                },
                data
            }]
        });
        wcChart.on('click',p=>window.open(`https://www.youtube.com/results?search_query=${encodeURIComponent(p.name)}`,'_blank','noopener,noreferrer'));
        if(window._ytwcRO){try{window._ytwcRO.disconnect();}catch(e){}}
        if(typeof ResizeObserver!=='undefined'){
            window._ytwcRO=new ResizeObserver(()=>{
                if(!wcChart)return;
                const r=ch.getBoundingClientRect();
                if(r.width>0&&r.height>0){try{wcChart.resize({width:r.width,height:r.height});}catch(e){}}
            });
            window._ytwcRO.observe(ch.parentElement);
        }
    });
}

/* ══ Top Topics List ══ */
function renderList() {
    const ld=_$('topicLoading'),ct=_$('topicContent'),em=_$('topicEmpty'),list=_$('topicList'),pg=_$('pagArea');
    if(!filtered.length){if(ld)ld.style.display='none';if(em)em.style.display='flex';if(ct)ct.style.display='none';return;}
    if(em)em.style.display='none';
    const total=filtered.length,pages=Math.ceil(total/PP),start=(curPage-1)*PP;
    const items=filtered.slice(start,start+PP),mx=filtered[0]?.size||1;
    list.innerHTML='';
    items.forEach((h,i)=>{
        const rk=start+i+1, rc=rk<=3?` ht-rank--${rk}`:'', pct=Math.round((h.size/mx)*100);
                const sentColor = h.sent === 'positive' ? '#10B981' : h.sent === 'negative' ? '#273B4A' : '#F59E0B';
        const el=document.createElement('div'); el.className='ht-item';
        el.innerHTML=`<div class="ht-rank${rc}">${rk}</div><div class="ht-name" style="color:${sentColor};">${esc(h.name)}</div><div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;background:linear-gradient(90deg,${sentColor},${sentColor}88);"></div></div><div class="ht-count">${numF(h.size)}</div>`;
        el.onclick=()=>window.open(`https://www.youtube.com/results?search_query=${encodeURIComponent(h.name.replace(/^#/,''))}`,'_blank','noopener,noreferrer');
        list.appendChild(el);
    });
    if(pg){
        if(pages<=1){pg.innerHTML='';}
        else{
            const fr=start+1,to=Math.min(start+PP,total);let b='',r=2;
            b+=`<button class="tme-pag-btn" ${curPage<=1?'disabled':''} onclick="goPage(${curPage-1})"><i class="ph ph-caret-left"></i></button>`;
            for(let i=1;i<=pages;i++){
                if(i===1||i===pages||(i>=curPage-r&&i<=curPage+r))b+=`<button class="tme-pag-btn${i===curPage?' is-active':''}" onclick="goPage(${i})">${i}</button>`;
                else if(i===curPage-r-1||i===curPage+r+1)b+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
            }
            b+=`<button class="tme-pag-btn" ${curPage>=pages?'disabled':''} onclick="goPage(${curPage+1})"><i class="ph ph-caret-right"></i></button>`;
            pg.innerHTML=`<div class="tme-pagination"><span class="tme-pag-info">${fr}–${to} dari ${numF(total)}</span><div class="tme-pag-controls">${b}</div></div>`;
        }
    }
    if(ld)ld.style.display='none'; if(ct)ct.style.display='block';
}

function goPage(p){curPage=p;renderList();_$('topicList')?.scrollIntoView({behavior:'smooth',block:'nearest'});}

/* ══════════════════════════════════════════════════════
   EXPORT MODULE — sama persis dengan IG (chrome + safari)
   Hanya label & filename yang berbeda (YouTube)
══════════════════════════════════════════════════════ */
const YTWCExport = (() => {
    'use strict';
    let _timer = null;
    const PID  = OV_PID;

    /* ── Toast ── */
    function _toast(msg, type = 'default', dur = 3200) {
        const t = _$('exportToast'), m = _$('exportToastMsg'), ico = _$('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show' + (type !== 'default' ? ' ' + type : '');
        const icoCls  = { success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' };
        if (ico) ico.className = 'ph ' + (icoCls[type] || icoCls.default);
        clearTimeout(_timer);
        _timer = setTimeout(() => t.classList.remove('show'), dur);
    }

    function _btnState(btn, on) {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle('exporting', on);
    }

    /* ── Chart-swap: ganti ECharts canvas → <img> sebelum html2canvas ── */
    function _swapChartsIn(el) {
        const swaps    = [];
        const container = document.getElementById('wordCloudChart');
        if (!container || !el.contains(container)) return swaps;
        if (container.style.display === 'none') return swaps;

        const echartsCanvas = container.querySelector('canvas');
        if (!echartsCanvas) return swaps;

        let dataUrl = null;

        /* Coba via ECharts API dulu */
        try {
            if (wcChart && !wcChart.isDisposed()) {
                dataUrl = wcChart.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
            }
        } catch(e) { console.warn('[YTWCExport] getDataURL gagal:', e); }

        /* Fallback: salin DOM canvas langsung (Safari-safe) */
        if (!dataUrl || dataUrl === 'data:,' || dataUrl.length < 100) {
            try {
                const off = document.createElement('canvas');
                off.width  = echartsCanvas.width;
                off.height = echartsCanvas.height;
                const ctx  = off.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, off.width, off.height);
                ctx.drawImage(echartsCanvas, 0, 0);
                dataUrl = off.toDataURL('image/png');
            } catch(e2) { console.warn('[YTWCExport] fallback canvas gagal:', e2); }
        }

        if (!dataUrl || dataUrl === 'data:,' || dataUrl.length < 100) return swaps;

        const h   = container.offsetHeight || 520;
        const w   = container.offsetWidth  || echartsCanvas.width || 800;

        const placeholder = document.createElement('div');
        placeholder.dataset.swapFor = 'wordCloudChart';

        const img       = document.createElement('img');
        img.dataset.swapImg = 'wordCloudChart';
        img.src         = dataUrl;
        img.style.cssText = `width:${w}px;height:${h}px;object-fit:contain;display:block;background:#fff;`;

        container.parentNode.insertBefore(placeholder, container);
        container.parentNode.insertBefore(img, placeholder);
        container.style.display = 'none';

        swaps.push({ container, placeholder, img });
        return swaps;
    }

    function _swapChartsOut(swaps) {
        swaps.forEach(({ container, placeholder, img }) => {
            try { img.remove(); }         catch(e) {}
            try { placeholder.remove(); } catch(e) {}
            container.style.display = 'block';
        });
    }

    /* ── onClone: bersihkan animasi & sembunyikan elemen tak perlu ── */
    function _onClone(clonedDoc) {
        clonedDoc.querySelectorAll(
            '#wcLoading,#topicLoading,.spinner-state,.spin-ring,' +
            '.export-toast,.sent-tabs,[data-html2canvas-ignore]'
        ).forEach(el => { el.style.cssText += 'display:none!important;'; });

        clonedDoc.querySelectorAll('*').forEach(el => {
            el.style.animationPlayState = 'paused';
            el.style.animation  = 'none';
            el.style.transition = 'none';
        });

        clonedDoc.querySelectorAll(
            '.card,.card-body,.card-header,.row,[class*="col-"],' +
            '.kpi-card-hover,.ht-item,.ht-list,#topicContent,#pageExportArea'
        ).forEach(el => {
            el.style.opacity    = '1';
            el.style.transform  = 'none';
            el.style.visibility = 'visible';
        });

        const tc = clonedDoc.getElementById('topicContent');
        if (tc) tc.style.display = 'block';
    }

    /* ── Capture utama (Chrome + Safari) ── */
    async function _doCapture(el, isCard) {
        el.querySelectorAll('.kpi-card-hover,.ht-item,.card,[class*="col-"]')
          .forEach(e => { e.style.opacity='1'; e.style.transform='none'; e.style.visibility='visible'; });

        const swaps = _swapChartsIn(el);

        /* Safari butuh jeda sebelum composite */
        await new Promise(r => setTimeout(r, 300));
        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

        let canvas;
        try {
            canvas = await html2canvas(el, {
                scale           : 2,
                useCORS         : true,
                allowTaint      : true,        // wajib untuk Safari cross-origin canvas
                backgroundColor : isCard ? '#ffffff' : '#f1f5f8',
                logging         : false,
                removeContainer : true,
                imageTimeout    : 0,
                x               : 0,
                y               : 0,
                width           : el.offsetWidth,
                height          : el.scrollHeight,
                onclone         : d => _onClone(d),
                ignoreElements  : e => e.hasAttribute('data-html2canvas-ignore'),
            });
        } finally {
            _swapChartsOut(swaps);
        }
        return canvas;
    }

    /* ── PDF helpers ── */
    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3, 128, 71); pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255); pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'YouTube Word Cloud'), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text(`Halaman ${page} / ${total}`, pW / 2, pH - 3, { align: 'center' });
    }

    function _addCanvas(pdf, canvas, margin, pW, pH) {
        const uw = pW - margin*2, uh = pH - 14 - 10;
        const ratio = Math.min(uw / canvas.width, uh / canvas.height);
        const dw = canvas.width * ratio, dh = canvas.height * ratio;
        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', margin + (uw-dw)/2, 14 + (uh-dh)/2, dw, dh);
    }

    function _paginate(pdf, canvas, margin, pW, pH, labelFn) {
        const uw = pW - margin*2, uh = pH - 14 - 10;
        const ratio = uw / canvas.width, sliceH = uh / ratio;
        const total = Math.max(1, Math.ceil((canvas.height * ratio) / uh));
        let srcY = 0, pg = 1;
        while (srcY < canvas.height) {
            if (pg > 1) pdf.addPage();
            _drawHeader(pdf, pW, pH, labelFn(), pg, total);
            const srcSlice = Math.min(sliceH, canvas.height - srcY), dstH = srcSlice * ratio;
            const slice = document.createElement('canvas');
            slice.width = canvas.width; slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, dstH);
            srcY += srcSlice; pg++;
        }
        return total;
    }

    function _stamp() { return new Date().toISOString().slice(0, 10).replace(/-/g, ''); }

    /* ── Export seluruh halaman ── */
    async function run(type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        const bPdf = _$('pageExportPdfBtn'), bImg = _$('pageExportImgBtn');
        _btnState(bPdf, true); _btnState(bImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const area   = _$('pageExportArea');
            if (!area) throw new Error('pageExportArea tidak ditemukan');
            window.scrollTo({ top: 0 });
            await new Promise(r => setTimeout(r, 200));

            const canvas = await _doCapture(area, false);
            const stamp  = _stamp();

            if (type === 'image') {
                const a = document.createElement('a');
                a.download = `youtube_wordcloud_${PID}_${stamp}.png`;
                a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight(), M = 10;
                const uw = pW - M*2, uh = pH - 14 - 10;
                if ((canvas.height * (uw / canvas.width)) <= uh) {
                    _drawHeader(pdf, pW, pH, 'YouTube Word Cloud', 1, 1);
                    _addCanvas(pdf, canvas, M, pW, pH);
                } else {
                    _paginate(pdf, canvas, M, pW, pH, () => 'YouTube Word Cloud');
                }
                pdf.save(`youtube_wordcloud_${PID}_${stamp}.pdf`);
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[YTWCExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(bPdf, false); _btnState(bImg, false);
        }
    }

    /* ── Export per-card ── */
    const _cardLabels = {
        wordcloud : 'YouTube Word Cloud',
        topics    : 'YouTube Top Topics',
    };
    function _cardFilename(k) {
        const map = { wordcloud: 'word-cloud', topics: 'top-topics' };
        return `youtube_${map[k]||k}_${PID}_${_stamp()}`;
    }

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar…', 'default', 99999);
        try {
            const area = document.getElementById(areaId);
            if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');
            const canvas = await _doCapture(area, true);
            const fname  = _cardFilename(cardKey);
            const label  = _cardLabels[cardKey] || cardKey;

            if (type === 'image') {
                const a = document.createElement('a');
                a.download = fname + '.png'; a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation : canvas.width > canvas.height * 1.2 ? 'landscape' : 'portrait',
                    unit: 'mm', format: 'a4'
                });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight(), M = 10;
                const uw = pW - M*2, uh = pH - 14 - 10;
                if ((canvas.height * (uw / canvas.width)) <= uh) {
                    _drawHeader(pdf, pW, pH, label, 1, 1);
                    _addCanvas(pdf, canvas, M, pW, pH);
                } else {
                    _paginate(pdf, canvas, M, pW, pH, () => label);
                }
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[YTWCExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally { _btnState(btn, false); }
    }

    return { run, runCard };
})();

/* ══ Init ══ */
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    window.addEventListener('resize', () => {
        if (!wcChart) return;
        const ch = _$('wordCloudChart'); if (!ch) return;
        const r  = ch.getBoundingClientRect();
        try { if (r.width > 0 && r.height > 0) wcChart.resize({ width: r.width, height: r.height }); else wcChart.resize(); } catch(e) {}
    });
    document.querySelectorAll('.sent-tab').forEach(btn => btn.addEventListener('click', () => {
        document.querySelectorAll('.sent-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        curSent = btn.dataset.s;
        applyFilter();
    }));
    document.querySelectorAll('.mode-btn').forEach(btn => btn.addEventListener('click', () => {
        document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        switchMode(btn.dataset.mode);
    }));
});
</script>
@endsection