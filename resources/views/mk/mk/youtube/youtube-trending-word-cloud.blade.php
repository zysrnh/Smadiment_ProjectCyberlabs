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
.wordcloud-container{position:relative;width:100%;height:520px}
.wordcloud-container #wcLoading,
.wordcloud-container #wcEmpty{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px}
.wordcloud-container #wcLoading{background:#fff;z-index:2}
.wordcloud-container #wcEmpty{color:var(--slate-400);font-size:12px;font-weight:600}
.wordcloud-container #wcEmpty i{font-size:34px;color:var(--slate-300)}
#wordCloudChart{width:100%!important;height:100%!important;display:block}
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
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#4680FF;animation:fadeUp .38s ease-out .00s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiTopics"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopicsSub"><i class="ph ph-hash me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-hash"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#2CA87F;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Volume</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiVolume"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiVolumeSub"><i class="ph ph-chart-bar me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#E58A00;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Positive Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiPos"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-smiley me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#DC2626;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Negative Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiNeg"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-smiley-sad me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div></div></div>
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

/* ══ Stopwords (YouTube-specific, tidak berubah) ══ */
const SW = new Set(['the','a','an','and','or','but','in','on','at','to','for','of','with','is','are','was','were','be','been','have','has','had','do','does','did','will','would','could','should','may','might','this','that','these','those','i','you','he','she','we','they','it','my','your','his','her','our','their','me','him','us','them','dari','dan','ke','di','yang','dengan','untuk','ini','itu','ada','tidak','bisa','akan','juga','sudah','pada','atau','dalam','oleh','karena','kita','anda','kami','mereka','ya','jadi','tapi','kalau','aja','saja','pun','lebih','seperti','masih','harus','kali','video','youtube','channel','watch','subscribe','like','comment','share','link','click','here','now','new','how','what','why','when','where','who','all','get','let','amp','via','http','https','www','com','co','id','nih','yuk','yg','nya','si','loh','deh','sih','dong','kok','udah','sama','mau','apa','kalo','gimana','banget','guys','ep','eps','full','part','official','ft','feat','cover','live']);

/* ══════════════════════════════════════════════════════
   SENTIMENT KEYWORDS — extended dari IG (EN + ID)
   Menggantikan list lama yang terlalu pendek
══════════════════════════════════════════════════════ */
const NEG_KW = [
    /* EN */
    'bad','worst','hate','sad','fail','failed','failing','lose','lost','angry','anger',
    'terrible','horrible','awful','poor','dead','death','die','died','dying','kill','killed',
    'corrupt','corruption','crime','criminal','fraud','scam','lie','lies','lied','lying',
    'abuse','abused','terror','terrorist','attack','attacked','war','riot','scandal',
    'boycott','crisis','disaster','wrong','hurt','suffer','suffering','violence','violent',
    'racist','racism','bully','bullying','threat','threatened','danger','dangerous',
    'victim','victims','bankrupt','drugs','drug','accident','flood','earthquake','landslide',
    'fire','crash','explosion','murder','rape','robbery','theft','steal','stolen',
    'illegal','fake','hoax','mislead','misleading','manipulate','manipulation',
    'protest','chaos','collapse','broken','damage','damaged','destroy','destroyed',
    'loss','losses','penalty','punish','punished','shame','shameful','disgrace','disgraced',
    'embarrass','embarrassed','betrayal','betrayed','betray','deceive','deceived','deception',
    'oppression','oppressed','exploit','exploited','exploitation','discrimination',
    'harass','harassment','intimidate','intimidation','depressed','depression','anxiety',
    'suicide','suicidal','poverty','starving','starvation','homeless','neglect','neglected',
    'cheat','cheated','cheating','bribe','bribery','arrested','imprisoned','guilty',
    'convicted','sentenced','banned','suspended','fired','dismissed','resign','resigned',
    'shutdown','insolvent','toxic','poisoned','polluted','contaminated','infected',
    'diseased','pandemic','hungry','underpaid','underprivileged',
    /* ID */
    'buruk','benci','sedih','gagal','kalah','marah','amarah','parah','miskin','mati',
    'bunuh','tewas','korupsi','kejahatan','penipuan','bohong','curang','kekerasan',
    'serang','perang','rusuh','skandal','krisis','bencana','salah','rusak','sakit',
    'derita','ilegal','ancaman','bahaya','darurat','korban','bangkrut','narkoba',
    'meninggal','wafat','kecelakaan','banjir','gempa','longsor','kebakaran','tabrakan',
    'ledakan','pembunuhan','pemerkosaan','perampokan','pencurian','mencuri','dicuri',
    'palsu','hoaks','menyesatkan','manipulasi','protes','kacau','anarki',
    'runtuh','hancur','kerusakan','menghancurkan','kerugian','hukuman','dihukum',
    'dikecam','malu','memalukan','aib','pengkhianatan','mengkhianati',
    'menipu','penindasan','ditindas','eksploitasi','diskriminasi',
    'pelecehan','intimidasi','depresi','kecemasan','bunuhdiri','kemiskinan',
    'kelaparan','gelandangan','ditelantarkan','terabaikan','dikucilkan','dibuang',
    'dipecat','pemecatan','gelap','suram','nestapa','duka','cemas','takut',
    'khawatir','panik','tertekan','stress','stres','trauma','luka','cedera',
    'terluka','dicederai','disakiti','dizalimi','zalim','aniaya','dianiaya',
    'ditangkap','dipenjara','penjara','divonis','vonis','terdakwa','tersangka',
    'kejam','brutal','sadis','jahat','jahanam','laknat','haram','terlarang',
    'dilarang','dicabut','dibekukan','disita','dirampas','dihapus','ditolak',
    'gagalkan','kegagalan','kekacauan','kehancuran','kebencian',
    'permusuhan','konflik','sengketa','serangan','teror','ancam',
];

const POS_KW = [
    /* EN */
    'win','won','winning','best','good','great','love','loved','loving','happy','happiness',
    'success','successful','succeed','amazing','excellent','awesome','celebrate','celebrated',
    'celebration','proud','pride','champion','champions','championship','victory','victorious',
    'achieve','achieved','achievement','congratulations','congratulate','hope','hopeful',
    'inspire','inspired','inspiring','inspiration','wonderful','beautiful','brilliant',
    'fantastic','legend','legendary','hero','heroes','progress','growth','growing',
    'peace','peaceful','prosperous','prosperity','freedom','free','liberated','liberation',
    'healthy','health','strong','strength','power','powerful','brave','bravery','courage',
    'courageous','smart','intelligent','genius','innovative','innovation','creative',
    'creativity','talented','talent','skilled','skill','expert','master','professional',
    'improve','improved','improvement','advance','advanced','upgrade','upgraded',
    'build','built','building','develop','developed','development','grow','grew','grown',
    'rise','risen','rising','boost','boosted','boosting','help','helped','helping',
    'support','supported','supporting','care','caring','cared','kind','kindness',
    'generous','generosity','grateful','gratitude','thankful','blessed','blessing',
    'joy','joyful','cheerful','cheer','smile','smiling','laugh','laughter','fun',
    'enjoy','enjoyed','enjoying','pleasure','pleased','delight','delighted','delightful',
    'perfect','perfection','outstanding','superb','magnificent','remarkable',
    'extraordinary','incredible','unbelievable','phenomenal','award','awarded',
    'reward','rewarded','recognition','recognized','honor','honored',
    'respect','respected','trust','trusted','trustworthy','loyal','loyalty','unity',
    'united','together','togetherness','community','solidarity','cooperation',
    'launch','launched','official','milestone','record','historic','first',
    'top','trending','viral','popular','famous','iconic','premium','quality',
    /* ID */
    'menang','juara','terbaik','baik','bagus','cinta','senang','sukses','berhasil',
    'hebat','keren','bangga','kemenangan','prestasi','selamat','harapan','inspirasi',
    'indah','cemerlang','fantastis','pahlawan','kemajuan','merdeka','damai','sejahtera',
    'sehat','kuat','berani','pintar','cerdas','inovatif','inovasi','kreatif',
    'kreativitas','berbakat','bakat','ahli','profesional','berkembang','meningkat',
    'peningkatan','maju','canggih','bangun','membangun','tumbuh','naik',
    'membantu','dukungan','mendukung','peduli','dermawan',
    'syukur','bersyukur','berkah','bahagia','gembira','senyum','tertawa',
    'menikmati','menyenangkan','sempurna','luarbiasa','mengagumkan','fenomenal',
    'penghargaan','hadiah','pengakuan','dihormati','kepercayaan','setia','kesetiaan',
    'bersatu','bersama','kebersamaan','komunitas','solidaritas','kerjasama','gotong',
    'royong','kompak','harmonis','harmoni','ceria','riang','giat','rajin','tekun',
    'semangat','antusias','optimis','positif','terpilih','unggul','istimewa',
    'spesial','favorit','populer','viral','trending','hits','booming',
    'resmi','diluncurkan','diresmikan','meraih','mendapatkan',
    'diraih','lolos','lulus','diterima','dipuji',
];

/*
 * getSent — scoring (pakai .includes), konsisten dengan IG & YouTube Overview
 * Analisis dari nama topic/hashtag/keyword.
 * Default tie → 'neutral' (YouTube konten lebih banyak netral: tutorial, review, dll)
 */
function getSent(name) {
    const low = (name || '').toLowerCase().replace(/^#/, '').replace(/[_\-]/g, ' ').trim();
    if (!low) return 'neutral';
    let negScore = 0, posScore = 0;
    for (const k of NEG_KW) { if (low.includes(k)) negScore++; }
    for (const k of POS_KW) { if (low.includes(k)) posScore++; }
    if (negScore > posScore) return 'negative';
    if (posScore > negScore) return 'positive';
    return 'neutral'; /* tie → neutral untuk YouTube */
}

/* ══ Ekstraksi hashtag & keyword dari raw posts (tidak berubah) ══ */
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
    return Object.entries(c)
        .filter(([,v]) => v >= 2)
        .sort((a,b)=>b[1]-a[1])
        .slice(0, 150)
        .map(([name,size]) => ({name,size}));
}

/* ══════════════════════════════════════════════════════
   LOAD DATA — 4-step fallback chain (tidak berubah)
══════════════════════════════════════════════════════ */
async function loadData() {
    if (!CFG.pid) { showEmpty(); return; }

    /* Step 1 — PRELOADED_RAW */
    if (Array.isArray(PRELOADED_RAW) && PRELOADED_RAW.length >= 3) {
        const data = PRELOADED_RAW
            .map(t => ({ name:String(t.hashtag||t.name||'').trim(), size:+(t.size||t.total_volume||t.appearances||1) }))
            .filter(t => t.name && t.size > 0);
        if (data.length >= 3) {
            const isKw = data.filter(t=>!t.name.startsWith('#')).length > data.length * 0.5;
            buildTopics(data, isKw ? 'keyword' : 'hashtag');
            return;
        }
    }

    /* Step 2 — trending-topics API */
    try {
        const r = await fetch(`/mk/api/youtube/trending-topics?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}`);
        const j = await r.json();
        if (j.success && j.data?.hashtags?.length >= 3) {
            const data = j.data.hashtags
                .map(t => ({ name:(t.hashtag||t.name||'').trim(), size:+(t.size||1) }))
                .filter(t => t.name && t.size > 0);
            if (data.length >= 3) {
                const isKw = data.filter(t=>!t.name.startsWith('#')).length > data.length * 0.5;
                buildTopics(data, isKw ? 'keyword' : 'hashtag');
                return;
            }
        }
    } catch(e) { console.warn('[YTWC] Step 2 failed:', e.message); }

    /* Step 3 — fetch raw posts → extract client-side */
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

            if (hashtags.length >= 5) {
                buildTopics(hashtags, 'hashtag');
            } else if (keywords.length >= 3) {
                const merged = [
                    ...hashtags.map(t => ({name:t.name, size:t.size*3})),
                    ...keywords,
                ].sort((a,b)=>b.size-a.size);
                buildTopics(merged, keywords.length > hashtags.length ? 'keyword' : 'hashtag');
            } else {
                showEmpty();
            }
            return;
        }
    } catch(e) { console.warn('[YTWC] Step 3 failed:', e.message); }

    /* Step 4 — empty */
    showEmpty();
}

function buildTopics(data, mode) {
    curMode = mode;
    document.querySelectorAll('.mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));
    _$('wcSubtitle').textContent = mode === 'keyword'
        ? 'Keyword dari judul video — klik untuk cari di YouTube'
        : 'Klik hashtag untuk cari di YouTube';
    /* ★ getSent sekarang pakai scoring, bukan token exact-match ★ */
    allTopics = data
        .map(t => ({ name: String(t.name||''), size: t.size, sent: getSent(String(t.name||'')) }))
        .filter(t => t.name);
    if (!allTopics.length) { showEmpty(); return; }
    applyFilter();
}

function switchMode(mode) {
    if (mode === curMode) return;
    if (mode === 'hashtag') {
        const ht = window._hashtagData || (window._rawPosts ? extractHashtags(window._rawPosts) : []);
        if (ht.length) { window._hashtagData=ht; buildTopics(ht,'hashtag'); }
    } else {
        const kw = window._keywordData || (window._rawPosts ? extractKeywords(window._rawPosts) : []);
        if (kw.length) { window._keywordData=kw; buildTopics(kw,'keyword'); }
    }
}

function applyFilter() {
    filtered = curSent === 'all' ? [...allTopics] : allTopics.filter(t => t.sent === curSent);
    curPage  = 1;
    updateKpi(); renderWC(); renderList();
}

function updateKpi() {
    const n=allTopics.length, vol=allTopics.reduce((s,t)=>s+t.size,0);
    const pos=allTopics.filter(t=>t.sent==='positive').length;
    const neg=allTopics.filter(t=>t.sent==='negative').length;
    const el=(id,v)=>{const e=_$(id);if(e)e.textContent=numF(v);};
    el('kpiTopics',n);   _$('kpiTopicsSub').innerHTML=`<i class="ph ph-hash me-1"></i>${numF(n)} trending topics`;
    el('kpiVolume',vol); _$('kpiVolumeSub').innerHTML=`<i class="ph ph-chart-bar me-1"></i>Avg ${numF(n?Math.round(vol/n):0)} / topic`;
    el('kpiPos',pos);    _$('kpiPosSub').innerHTML=`<i class="ph ph-smiley me-1"></i>${n?(pos/n*100).toFixed(1):0}% of topics`;
    el('kpiNeg',neg);    _$('kpiNegSub').innerHTML=`<i class="ph ph-smiley-sad me-1"></i>${n?(neg/n*100).toFixed(1):0}% of topics`;
    _$('badgeWC').textContent = numF(filtered.length) + ' topics';
}

function showEmpty() {
    ['wcLoading','wcEmpty','topicLoading','topicEmpty'].forEach(id=>{
        const e=_$(id); if(e) e.style.display=id.includes('Loading')?'none':'flex';
    });
    ['kpiTopics','kpiVolume','kpiPos','kpiNeg'].forEach(id=>{
        const e=_$(id); if(e) e.textContent='0';
    });
}

/* ══════════════════════════════════════════════════════
   WORD CLOUD RENDER
   ★ Tambahan: warna per sentimen aktif (konsisten dengan IG & X)
══════════════════════════════════════════════════════ */
function renderWC() {
    const ld=_$('wcLoading'), ch=_$('wordCloudChart'), em=_$('wcEmpty');

    if (!filtered.length) {
        if(ld) ld.style.display='none';
        if(em) em.style.display='flex';
        if(wcChart){try{wcChart.dispose();}catch(e){}wcChart=null;}
        return;
    }
    if(em) em.style.display='none';

    /* Normalize 30–100 */
    const raw=filtered.slice(0,150);
    const maxV=Math.max(...raw.map(t=>t.size),1), minV=Math.min(...raw.map(t=>t.size),1), span=maxV-minV||1;
    const data=raw.map(t=>({ name:t.name.replace(/^#/,''), value:Math.round(30+((t.size-minV)/span)*70) }));

    if(wcChart){try{wcChart.dispose();}catch(e){}wcChart=null;}

    requestAnimationFrame(()=>{
        if(ld) ld.style.display='none';
        const rect=ch.getBoundingClientRect();
        if(rect.width===0||rect.height===0){
            if(ld) ld.style.display='flex';
            setTimeout(()=>renderWC(),120);
            return;
        }

        wcChart=echarts.init(ch,null,{renderer:'canvas',width:rect.width,height:rect.height});

        /* ★ Warna berdasarkan tab sentimen aktif — konsisten dengan IG & X ★ */
        const colorsBySent = {
            positive: ['#16a34a','#22c55e','#4ade80','#15803d','#166534','#bbf7d0'],
            negative: ['#dc2626','#ef4444','#f87171','#b91c1c','#991b1b','#fecaca'],
            all:      ['#16a34a','#2563eb','#f59e0b','#ef4444','#9333ea','#14b8a6','#0ea5e9','#d97706'],
        };
        const colorPool = colorsBySent[curSent] || colorsBySent.all;

        wcChart.setOption({
            backgroundColor:'transparent',
            series:[{
                type:'wordCloud',
                shape:'circle',
                left:'center', top:'center',
                width:'100%',  height:'100%',
                sizeRange:[22,90],
                rotationRange:[-60,60],
                rotationStep:15,
                gridSize:6,
                drawOutOfBound:false,
                layoutAnimation:true,
                textStyle:{
                    fontFamily:'inherit',
                    fontWeight:'600',
                    color: () => colorPool[Math.floor(Math.random() * colorPool.length)],
                },
                emphasis:{focus:'self',textStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.20)'}},
                data,
            }]
        });

        wcChart.on('click', p =>
            window.open(`https://www.youtube.com/results?search_query=${encodeURIComponent(p.name)}`, '_blank', 'noopener,noreferrer')
        );

        /* ResizeObserver */
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

/* ══ Top Topics list — warna ht-name per sentimen ══ */
function renderList() {
    const ld=_$('topicLoading'),ct=_$('topicContent'),em=_$('topicEmpty'),list=_$('topicList'),pg=_$('pagArea');
    if(!filtered.length){if(ld)ld.style.display='none';if(em)em.style.display='flex';if(ct)ct.style.display='none';return;}
    if(em)em.style.display='none';
    const total=filtered.length,pages=Math.ceil(total/PP),start=(curPage-1)*PP;
    const items=filtered.slice(start,start+PP),mx=filtered[0]?.size||1;
    list.innerHTML='';
    items.forEach((h,i)=>{
        const rk=start+i+1, rc=rk<=3?` ht-rank--${rk}`:'', pct=Math.round((h.size/mx)*100);
        /* ★ Warna nama berdasarkan sentimen — konsisten dengan IG ★ */
        const sentColor = h.sent==='positive' ? '#16a34a' : h.sent==='negative' ? '#dc2626' : 'var(--primary)';
        const el=document.createElement('div'); el.className='ht-item';
        el.innerHTML=`
            <div class="ht-rank${rc}">${rk}</div>
            <div class="ht-name" style="color:${sentColor};">${esc(h.name)}</div>
            <div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;background:linear-gradient(90deg,${sentColor},${sentColor}88);"></div></div>
            <div class="ht-count">${numF(h.size)}</div>`;
        el.onclick=()=>window.open(
            `https://www.youtube.com/results?search_query=${encodeURIComponent(h.name.replace(/^#/,''))}`,
            '_blank','noopener,noreferrer'
        );
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

/* ══ EXPORT (tidak berubah) ══ */
const YTWCExport=(()=>{
    'use strict';let _timer=null;const PID=OV_PID;
    function _toast(msg,type='default',dur=3200){const t=_$('exportToast'),m=_$('exportToastMsg'),ico=_$('exportToastIcon');if(!t||!m)return;m.textContent=msg;t.className='export-toast show'+(type!=='default'?' '+type:'');if(ico)ico.className='ph '+({success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'}[type]||'ph-spinner');clearTimeout(_timer);_timer=setTimeout(()=>t.classList.remove('show'),dur);}
    function _btnState(btn,on){if(!btn)return;btn.disabled=on;btn.classList.toggle('exporting',on);}
    async function _capPage(){const a=_$('pageExportArea');if(!a)throw new Error('pageExportArea tidak ditemukan');window.scrollTo({top:0});await new Promise(r=>setTimeout(r,400));if(wcChart){try{wcChart.resize();}catch(e){}}await new Promise(r=>setTimeout(r,200));return html2canvas(a,{scale:2,useCORS:true,allowTaint:false,backgroundColor:'#f1f5f8',logging:false,removeContainer:true,windowWidth:document.documentElement.scrollWidth,windowHeight:a.scrollHeight,height:a.scrollHeight,ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')||el.id==='pageExportPdfBtn'||el.id==='pageExportImgBtn'});}
    async function _capCard(id){const a=document.getElementById(id);if(!a)throw new Error('Area #'+id+' tidak ditemukan');if(wcChart&&id==='card-export-wc'){try{wcChart.resize();}catch(e){}}await new Promise(r=>setTimeout(r,280));return html2canvas(a,{scale:2,useCORS:true,allowTaint:false,backgroundColor:'#ffffff',logging:false,removeContainer:true,ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')});}
    function _hdr(pdf,title){const pW=pdf.internal.pageSize.getWidth();pdf.setFillColor(3,128,71);pdf.rect(0,0,pW,11,'F');pdf.setTextColor(255,255,255);pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.text('SMADIMENT — '+title,10,7.5);const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});pdf.setFontSize(7);pdf.setFont('helvetica','normal');pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});}
    async function _pag(pdf,cv){const pW=pdf.internal.pageSize.getWidth(),pH=pdf.internal.pageSize.getHeight(),m=10,uW=pW-m*2,uH=pH-m*2-14,ratio=uW/cv.width,sH=uH/ratio;let sy=0,pg=0;while(sy<cv.height){if(pg>0){pdf.addPage();_hdr(pdf,'YouTube Word Cloud');}const ss=Math.min(sH,cv.height-sy),dH=ss*ratio;const sl=document.createElement('canvas');sl.width=cv.width;sl.height=Math.ceil(ss);sl.getContext('2d').drawImage(cv,0,sy,cv.width,ss,0,0,cv.width,ss);pdf.addImage(sl.toDataURL('image/png'),'PNG',m,14,uW,dH);pdf.setFontSize(7);pdf.setTextColor(148,163,184);pdf.text(`Halaman ${pg+1}`,pW/2,pH-3,{align:'center'});sy+=ss;pg++;}}
    const _stamp=()=>new Date().toISOString().slice(0,10).replace(/-/g,'');
    async function run(type,btn){
        if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
        if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
        const bP=_$('pageExportPdfBtn'),bI=_$('pageExportImgBtn');
        _btnState(bP,true);_btnState(bI,true);
        _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        try{const cv=await _capPage(),st=_stamp();if(type==='image'){const a=document.createElement('a');a.download=`youtube_wordcloud_${PID}_${st}.png`;a.href=cv.toDataURL('image/png');a.click();_toast('Gambar berhasil diunduh!','success');}else{const{jsPDF}=window.jspdf,pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});_hdr(pdf,'YouTube Word Cloud');await _pag(pdf,cv);pdf.save(`youtube_wordcloud_${PID}_${st}.pdf`);_toast('PDF berhasil diunduh!','success');}}
        catch(e){console.error('[YTWCExport]',e);_toast('Export gagal: '+e.message,'error');}
        finally{_btnState(bP,false);_btnState(bI,false);}
    }
    async function runCard(areaId,cardKey,type,btn){
        if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
        if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
        _btnState(btn,true);_toast(type==='pdf'?'Menyiapkan PDF card…':'Mengambil gambar…','default',99999);
        try{const cv=await _capCard(areaId),st=_stamp();const labels={wordcloud:'word-cloud',topics:'top-topics'},titles={wordcloud:'YouTube Word Cloud',topics:'YouTube Top Topics'},fname=`youtube_${labels[cardKey]||cardKey}_${PID}_${st}`;if(type==='image'){const a=document.createElement('a');a.download=fname+'.png';a.href=cv.toDataURL('image/png');a.click();_toast('Gambar berhasil diunduh!','success');}else{const{jsPDF}=window.jspdf,landscape=cv.width>cv.height,pdf=new jsPDF({orientation:landscape?'landscape':'portrait',unit:'mm',format:'a4'});_hdr(pdf,titles[cardKey]||'YouTube Word Cloud');await _pag(pdf,cv);pdf.save(fname+'.pdf');_toast('PDF berhasil diunduh!','success');}}
        catch(e){console.error('[YTWCExport.runCard]',e);_toast('Export gagal: '+e.message,'error');}
        finally{_btnState(btn,false);}
    }
    return{run,runCard};
})();

/* ══ Init ══ */
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    window.addEventListener('resize', () => {
        if(!wcChart) return;
        const ch=_$('wordCloudChart'); if(!ch) return;
        const r=ch.getBoundingClientRect();
        try{ if(r.width>0&&r.height>0) wcChart.resize({width:r.width,height:r.height}); else wcChart.resize(); }catch(e){}
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