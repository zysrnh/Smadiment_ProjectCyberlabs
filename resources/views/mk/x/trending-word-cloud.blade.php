@extends('mk.layouts.app')

@section('title', 'X Word Cloud - SMADIMENT')

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
.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}
.chart-empty i{font-size:34px;color:var(--slate-300);display:block}
.sent-tabs{display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px}
.sent-tab{flex:0 0 auto;display:flex;align-items:center;justify-content:center;gap:5px;padding:6px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap}
.sent-tab:hover{background:#fff;color:var(--slate-800)}
.sent-tab.active{background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08)}
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
.kpi-card-hover h3{font-size:1.5rem}

/* ══ Export Styles ══ */
.page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
.page-export-bar-left i{font-size:15px;color:var(--primary)}
.page-export-bar-right{display:flex;gap:8px}
.page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
.page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
.page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
.page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
.page-export-btn .exp-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.page-export-btn.exporting .exp-spinner{display:inline-block}
.page-export-btn.exporting .exp-icon{display:none}
.card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
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
.wordcloud-container {
    position: relative;
    width: 100%;
    height: 520px;
}
.wordcloud-container #wcLoading,
.wordcloud-container #wcEmpty {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}
.wordcloud-container #wcLoading { background: #fff; z-index: 2; }
.wordcloud-container #wcEmpty   { color: var(--slate-400); font-size: 12px; font-weight: 600; }
.wordcloud-container #wcEmpty i { font-size: 34px; color: var(--slate-300); }
#wordCloudChart {
    width:  100% !important;
    height: 100% !important;
    display: block;
}
</style>
@endsection

@section('page-title', 'X Word Cloud')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate   ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects  ?? [];
    $location  = $location  ?? 'Indonesia';
@endphp
<script>
    const OV_PID = {{ $projectId ? (int) $projectId : 'null' }};
    const OV_SD  = '{{ $startDate }}';
    const OV_ED  = '{{ $endDate }}';
    const OV_LOC = '{{ $location }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- KPI --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .00s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiTopics"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopicsSub"><i class="ph ph-hash me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-hash"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-success text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Total Volume</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiVolume"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiVolumeSub"><i class="ph ph-chart-bar me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-warning text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Positive Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiPos"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-smiley me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div></div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-danger text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body"><div class="d-flex align-items-center"><div class="flex-grow-1">
                <p class="mb-1 text-white text-opacity-75 f-12">Negative Topics</p>
                <h3 class="mb-0 text-white f-w-300" id="kpiNeg"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-smiley-sad me-1"></i>Loading…</p>
            </div><div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div></div></div>
        </div>
    </div>
</div>

{{-- ── Page Export Toolbar ── --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Word Cloud + Top Topics</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf"
                id="pageExportPdfBtn"
                onclick="XWCExport.run('pdf', this)"
                title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf exp-icon"></i>
            <span class="exp-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img"
                id="pageExportImgBtn"
                onclick="XWCExport.run('image', this)"
                title="Export halaman sebagai PNG">
            <i class="ph ph-image exp-icon"></i>
            <span class="exp-spinner"></span>
        </button>
    </div>
</div>

{{-- Sentiment Filter --}}
<div class="sent-tabs mb-3" id="sentTabs">
    <button class="sent-tab active" data-s="all"><span class="sent-dot" style="background:var(--primary);"></span> All</button>
    <button class="sent-tab" data-s="positive"><span class="sent-dot" style="background:#10b981;"></span> Positive</button>
    <button class="sent-tab" data-s="negative"><span class="sent-dot" style="background:#ef4444;"></span> Negative</button>
    <button class="sent-tab" data-s="neutral"><span class="sent-dot" style="background:#f59e0b;"></span> Neutral</button>
</div>

{{-- Content --}}
<div class="row">
    {{-- Word Cloud Card --}}
    <div class="col-lg-8 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-wc">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-cloud f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Word Cloud</h6>
                            <small class="text-muted">Klik kata untuk cari di X</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary" id="badgeWC">Loading…</span>
                        <div data-html2canvas-ignore="true" class="d-flex gap-1">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="XWCExport.runCard('card-export-wc','wordcloud','pdf',this)"
                                    title="Export Word Cloud PDF">
                                <i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="XWCExport.runCard('card-export-wc','wordcloud','image',this)"
                                    title="Export Word Cloud PNG">
                                <i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="wordcloud-container">
                        <div id="wcLoading">
                            <div class="spin-ring"></div>
                            <span style="color:var(--slate-400);font-size:12px;font-weight:600;">Memuat word cloud…</span>
                        </div>
                        <div id="wordCloudChart"></div>
                        <div id="wcEmpty" style="display:none;">
                            <i class="ph ph-cloud-slash"></i>
                            <span>Tidak ada data topic</span>
                        </div>
                    </div>
                </div>
            </div>{{-- /card-export-wc --}}
        </div>
    </div>

    {{-- Top Topics Card --}}
    <div class="col-lg-4 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-topics">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-list-numbers f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Top Topics</h6>
                            <small class="text-muted">Ranked by volume</small>
                        </div>
                    </div>
                    <div data-html2canvas-ignore="true" class="d-flex gap-1">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="XWCExport.runCard('card-export-topics','topics','pdf',this)"
                                title="Export Topics PDF">
                            <i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="XWCExport.runCard('card-export-topics','topics','image',this)"
                                title="Export Topics PNG">
                            <i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span>
                        </button>
                    </div>
                </div>
                <div id="topicLoading" class="spinner-state"><div class="spin-ring"></div><span>Memuat…</span></div>
                <div id="topicContent" style="display:none;">
                    <div id="topicList" class="ht-list"></div>
                    <div id="pagArea"></div>
                </div>
                <div id="topicEmpty" style="display:none;" class="chart-empty">
                    <i class="ph ph-hash"></i><span>Tidak ada data</span>
                </div>
            </div>{{-- /card-export-topics --}}
        </div>
    </div>
</div>

</div>{{-- /pageExportArea --}}

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

@endsection

@section('scripts')
{{-- Export deps --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

<script>
'use strict';

const CFG = { sd: OV_SD, ed: OV_ED, loc: OV_LOC };
const _$  = id => document.getElementById(id);
const numF = n => parseInt(n||0).toLocaleString('id-ID');
const esc  = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

let allTopics = [], filtered = [], curPage = 1, curSent = 'all';
const PP = 15;
let wcChart = null;

/* ── Sentiment keywords ── */
const NEG_KW = ['bad','worst','hate','sad','fail','lose','lost','angry','terrible','poor','dead','death','die','kill','corrupt','corruption','crime','fraud','scam','lie','lies','abuse','terror','attack','war','riot','scandal','boycott','crisis','disaster','wrong','hurt','suffer','violence','racist','bully','threat','danger','victim','buruk','benci','sedih','gagal','kalah','marah','parah','miskin','mati','bunuh','tewas','korupsi','kejahatan','penipuan','bohong','curang','kekerasan','serang','perang','rusuh','skandal','krisis','bencana','salah','rusak','sakit','derita','ilegal','ancaman','bahaya','darurat','korban','bangkrut','narkoba','meninggal','wafat','kecelakaan','banjir','gempa','longsor'];
const POS_KW = ['win','won','best','good','great','love','happy','success','amazing','excellent','awesome','celebrate','proud','champion','victory','achieve','congratulations','hope','inspire','wonderful','beautiful','brilliant','fantastic','legend','hero','progress','growth','menang','juara','terbaik','baik','bagus','cinta','senang','sukses','berhasil','hebat','keren','bangga','kemenangan','prestasi','selamat','harapan','inspirasi','indah','cemerlang','fantastis','pahlawan','kemajuan','merdeka','damai','sejahtera'];

function getSent(name) {
    const low = name.toLowerCase().replace(/^#/,'').trim();
    const tk  = low.split(/[^a-z0-9]+/).filter(t => t.length > 0);
    for (const k of NEG_KW) if (tk.includes(k)) return 'negative';
    for (const k of POS_KW) if (tk.includes(k)) return 'positive';
    return 'neutral';
}

/* ── Load ── */
async function loadData() {
    try {
        const r = await fetch(`/mk/api/x/trending-topics?start_date=${CFG.sd}&end_date=${CFG.ed}&location=${CFG.loc}`);
        const j = await r.json();
        if (!j.success || !j.data?.top_topics?.length) { showEmpty(); return; }
        allTopics = j.data.top_topics.map(t => ({
            name: t.name,
            size: t.total_volume || t.appearances || 100,
            sent: getSent(t.name)
        }));
        applyFilter();
    } catch(e) { console.error(e); showEmpty(); }
}

function applyFilter() {
    filtered = curSent === 'all' ? [...allTopics] : allTopics.filter(t => t.sent === curSent);
    curPage  = 1;
    updateKpi();
    renderWC();
    renderList();
}

function updateKpi() {
    const n   = allTopics.length;
    const vol = allTopics.reduce((s,t) => s + t.size, 0);
    const pos = allTopics.filter(t => t.sent === 'positive').length;
    const neg = allTopics.filter(t => t.sent === 'negative').length;
    const el  = (id, v) => { const e = _$(id); if (e) e.textContent = numF(v); };
    el('kpiTopics', n);
    _$('kpiTopicsSub').innerHTML = `<i class="ph ph-hash me-1"></i>${n} trending topics`;
    el('kpiVolume', vol);
    _$('kpiVolumeSub').innerHTML = `<i class="ph ph-chart-bar me-1"></i>Avg ${numF(n ? Math.round(vol/n) : 0)} / topic`;
    el('kpiPos', pos);
    _$('kpiPosSub').innerHTML = `<i class="ph ph-smiley me-1"></i>${n ? (pos/n*100).toFixed(1) : 0}% of topics`;
    el('kpiNeg', neg);
    _$('kpiNegSub').innerHTML = `<i class="ph ph-smiley-sad me-1"></i>${n ? (neg/n*100).toFixed(1) : 0}% of topics`;
    _$('badgeWC').textContent = filtered.length + ' topics';
}

function showEmpty() {
    _$('wcLoading').style.display    = 'none';
    _$('wcEmpty').style.display      = 'flex';
    _$('topicLoading').style.display = 'none';
    _$('topicEmpty').style.display   = 'flex';
    ['kpiTopics','kpiVolume','kpiPos','kpiNeg'].forEach(id => { const e = _$(id); if (e) e.textContent = '0'; });
}

/* ── Word Cloud ── */
function renderWC() {
    const ld = _$('wcLoading');
    const ch = _$('wordCloudChart');
    const em = _$('wcEmpty');

    if (!filtered.length) {
        if (ld) ld.style.display = 'none';
        if (em) em.style.display = 'flex';
        if (wcChart) { try { wcChart.dispose(); } catch(e) {} wcChart = null; }
        return;
    }

    if (em) em.style.display = 'none';

    /* Normalisasi nilai ke rentang 30–100 agar semua kata proporsional */
    const raw  = filtered.slice(0, 150);
    const maxV = Math.max(...raw.map(t => t.size), 1);
    const minV = Math.min(...raw.map(t => t.size), 1);
    const span = maxV - minV || 1;

    const data = raw.map(t => ({
        name:  t.name.replace(/^#/, ''),
        value: Math.round(30 + ((t.size - minV) / span) * 70),
    }));

    /* Destroy existing instance */
    if (wcChart) { try { wcChart.dispose(); } catch(e) {} wcChart = null; }

    /* Tunggu satu frame agar container sudah dirender browser */
    requestAnimationFrame(() => {
        if (ld) ld.style.display = 'none';

        const rect = ch.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            setTimeout(() => renderWC(), 100);
            return;
        }

        wcChart = echarts.init(ch, null, {
            renderer: 'canvas',
            width:    rect.width,
            height:   rect.height,
        });

        wcChart.setOption({
            backgroundColor: 'transparent',
            series: [{
                type:            'wordCloud',
                shape:           'circle',
                left:            'center',
                top:             'center',
                width:           '100%',
                height:          '100%',
                sizeRange:       [22, 90],
                rotationRange:   [-60, 60],
                rotationStep:    15,
                gridSize:        6,
                drawOutOfBound:  false,
                layoutAnimation: true,
                textStyle: {
                    fontFamily: 'inherit',
                    fontWeight: '600',
                    color: function () {
                        const colors = ['#16a34a','#2563eb','#f59e0b','#ef4444','#9333ea','#14b8a6'];
                        return colors[Math.floor(Math.random() * colors.length)];
                    }
                },
                emphasis: {
                    focus:     'self',
                    textStyle: { shadowBlur: 10, shadowColor: 'rgba(0,0,0,.20)' }
                },
                data
            }]
        });

        wcChart.on('click', p => {
            const q = encodeURIComponent(p.name);
            window.open(`https://www.x.com/search?q=${q}`, '_blank', 'noopener,noreferrer');
        });

        /* ResizeObserver → ikut resize saat container berubah */
        if (window._xwcResizeObserver) window._xwcResizeObserver.disconnect();
        if (typeof ResizeObserver !== 'undefined') {
            window._xwcResizeObserver = new ResizeObserver(() => {
                if (wcChart) {
                    const r = ch.getBoundingClientRect();
                    if (r.width > 0 && r.height > 0) {
                        try { wcChart.resize({ width: r.width, height: r.height }); } catch(e) {}
                    }
                }
            });
            window._xwcResizeObserver.observe(ch.parentElement);
        }
    });
}

/* ── Top Topics list ── */
function renderList() {
    const ld   = _$('topicLoading'), ct = _$('topicContent'), em = _$('topicEmpty');
    const list = _$('topicList'),    pg = _$('pagArea');
    if (!filtered.length) {
        if (ld) ld.style.display = 'none';
        if (em) em.style.display = 'flex';
        if (ct) ct.style.display = 'none';
        return;
    }
    if (em) em.style.display = 'none';
    const total = filtered.length, pages = Math.ceil(total / PP), start = (curPage - 1) * PP;
    const items = filtered.slice(start, start + PP), mx = filtered[0]?.size || 1;
    list.innerHTML = '';
    items.forEach((h, i) => {
        const rk  = start + i + 1;
        const rc  = rk <= 3 ? ` ht-rank--${rk}` : '';
        const pct = Math.round((h.size / mx) * 100);
        const el  = document.createElement('div');
        el.className = 'ht-item';
        el.innerHTML = `<div class="ht-rank${rc}">${rk}</div><div class="ht-name">${esc(h.name)}</div><div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;"></div></div><div class="ht-count">${numF(h.size)}</div>`;
        el.onclick = () => { const q = encodeURIComponent(h.name.replace(/^#/,'')); window.open(`https://www.x.com/search?q=${q}`,'_blank','noopener,noreferrer'); };
        list.appendChild(el);
    });
    if (pg) {
        if (pages <= 1) { pg.innerHTML = ''; }
        else {
            const fr = start + 1, to = Math.min(start + PP, total);
            let b = '', r = 2;
            b += `<button class="tme-pag-btn" ${curPage<=1?'disabled':''} onclick="goPage(${curPage-1})"><i class="ph ph-caret-left"></i></button>`;
            for (let i = 1; i <= pages; i++) {
                if (i===1||i===pages||(i>=curPage-r&&i<=curPage+r)) b += `<button class="tme-pag-btn${i===curPage?' is-active':''}" onclick="goPage(${i})">${i}</button>`;
                else if (i===curPage-r-1||i===curPage+r+1) b += `<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
            }
            b += `<button class="tme-pag-btn" ${curPage>=pages?'disabled':''} onclick="goPage(${curPage+1})"><i class="ph ph-caret-right"></i></button>`;
            pg.innerHTML = `<div class="tme-pagination"><span class="tme-pag-info">${fr}–${to} dari ${total}</span><div class="tme-pag-controls">${b}</div></div>`;
        }
    }
    if (ld) ld.style.display = 'none';
    if (ct) ct.style.display = 'block';
}

function goPage(p) {
    curPage = p;
    renderList();
    _$('topicList')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ═══════════════════════════════════════════════════
   X WORD CLOUD EXPORT MODULE
═══════════════════════════════════════════════════ */
const XWCExport = (() => {
    'use strict';
    let _timer = null;
    const PID  = OV_PID;
    const SD   = OV_SD;
    const ED   = OV_ED;

    function _toast(msg, type = 'default', dur = 3200) {
        const t   = _$('exportToast');
        const m   = _$('exportToastMsg');
        const ico = _$('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show' + (type !== 'default' ? ' ' + type : '');
        const icoCls  = { success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' };
        if (ico) ico.className = 'ph ' + (icoCls[type] || icoCls.default);
        clearTimeout(_timer);
        _timer = setTimeout(() => t.classList.remove('show'), dur);
    }

    function _btnState(btn, on) {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle('exporting', on);
    }

    async function _capturePage() {
        const area = _$('pageExportArea');
        if (!area) throw new Error('pageExportArea tidak ditemukan');
        window.scrollTo({ top: 0 });
        await new Promise(r => setTimeout(r, 400));
        if (wcChart) { try { wcChart.resize(); } catch(e) {} }
        await new Promise(r => setTimeout(r, 200));
        return html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#f1f5f8',
            logging:         false,
            removeContainer: true,
            windowWidth:     document.documentElement.scrollWidth,
            windowHeight:    area.scrollHeight,
            height:          area.scrollHeight,
            ignoreElements:  el =>
                el.hasAttribute('data-html2canvas-ignore') ||
                el.id === 'pageExportPdfBtn' ||
                el.id === 'pageExportImgBtn',
        });
    }

    async function _captureCard(areaId) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');
        if (wcChart && areaId === 'card-export-wc') { try { wcChart.resize(); } catch(e) {} }
        await new Promise(r => setTimeout(r, 280));
        return html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#ffffff',
            logging:         false,
            removeContainer: true,
            ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
        });
    }

    function _pdfHeader(pdf, title) {
        const pW = pdf.internal.pageSize.getWidth();
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + title, 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
    }

    async function _paginatePdf(pdf, canvas) {
        const pW      = pdf.internal.pageSize.getWidth();
        const pH      = pdf.internal.pageSize.getHeight();
        const margin  = 10;
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - 14;
        const ratio   = usableW / canvas.width;
        const sliceH  = usableH / ratio;
        let srcY = 0, pg = 0;
        while (srcY < canvas.height) {
            if (pg > 0) { pdf.addPage(); _pdfHeader(pdf, 'X Word Cloud'); }
            const srcSlice = Math.min(sliceH, canvas.height - srcY);
            const dstH     = srcSlice * ratio;
            const slice    = document.createElement('canvas');
            slice.width    = canvas.width;
            slice.height   = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${pg + 1}`, pW / 2, pH - 3, { align: 'center' });
            srcY += srcSlice; pg++;
        }
    }

    function _stamp() { return new Date().toISOString().slice(0, 10).replace(/-/g, ''); }

    async function run(type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        const bPdf = _$('pageExportPdfBtn');
        const bImg = _$('pageExportImgBtn');
        _btnState(bPdf, true); _btnState(bImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const canvas = await _capturePage();
            const stamp  = _stamp();
            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = `x_wordcloud_${PID}_${stamp}.png`;
                a.href     = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                _pdfHeader(pdf, 'X Word Cloud');
                await _paginatePdf(pdf, canvas);
                pdf.save(`x_wordcloud_${PID}_${stamp}.pdf`);
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XWCExport]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(bPdf, false); _btnState(bImg, false);
        }
    }

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const canvas  = await _captureCard(areaId);
            const labels  = { wordcloud: 'word-cloud', topics: 'top-topics' };
            const titles  = { wordcloud: 'X Word Cloud', topics: 'X Top Topics' };
            const fname   = `x_${labels[cardKey] || cardKey}_${PID}_${_stamp()}`;

            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = fname + '.png';
                a.href     = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF }  = window.jspdf;
                const landscape  = canvas.width > canvas.height;
                const pdf        = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
                _pdfHeader(pdf, titles[cardKey] || 'X Word Cloud');
                await _paginatePdf(pdf, canvas);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XWCExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    return { run, runCard };
})();

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    loadData();

    /* Global resize fallback */
    window.addEventListener('resize', () => {
        if (wcChart) {
            const ch   = _$('wordCloudChart');
            const rect = ch ? ch.getBoundingClientRect() : null;
            try {
                if (rect && rect.width > 0) wcChart.resize({ width: rect.width, height: rect.height });
                else wcChart.resize();
            } catch(e) {}
        }
    });

    document.querySelectorAll('.sent-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sent-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            curSent = btn.dataset.s;
            applyFilter();
        });
    });
});
</script>
@endsection