@extends('mk.layouts.app')
@section('title', 'News Overview - SMADIMENT')

@section('styles')
<style>
:root{--nv-primary:#4361EE;--nv-green:#10B981;--nv-red:#EF4444;--nv-slate:#94A3B8;--nv-dark:#0F172A;--radius:8px}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
.fade-up{animation:fadeUp .38s ease-out both}
.fade-up-d1{animation-delay:.05s}.fade-up-d2{animation-delay:.1s}.fade-up-d3{animation-delay:.15s}.fade-up-d4{animation-delay:.2s}
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.nv-spinner{width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:var(--nv-primary);border-radius:50%;animation:spin .7s linear infinite}
.nv-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:50px 20px;color:var(--nv-slate);font-size:12px;font-weight:600}
.nv-empty{display:flex;flex-direction:column;align-items:center;gap:8px;padding:50px;color:var(--nv-slate);font-size:12px;text-align:center}
.nv-empty i{font-size:36px;color:#cbd5e1}
.pub-row{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;transition:background .12s;cursor:pointer;text-decoration:none}
.pub-row:last-child{border-bottom:none}
.pub-row:hover{background:#f0f5ff;padding-left:20px}
.pub-rank{font-size:11px;font-weight:800;color:var(--nv-slate);min-width:24px}
.pub-rank.top{color:var(--nv-primary)}
.pub-name{flex:1;font-size:13px;font-weight:600;color:var(--nv-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pub-row:hover .pub-name{color:var(--nv-primary)}
.pub-count{background:rgba(67,97,238,.1);color:var(--nv-primary);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.pub-ext{font-size:12px;color:#cbd5e1;transition:color .12s;margin-left:4px}
.pub-row:hover .pub-ext{color:var(--nv-primary)}
.article-item{display:block;padding:14px 16px;border-bottom:1px solid #f1f5f9;transition:background .12s,padding-left .12s;cursor:pointer;text-decoration:none;color:inherit}
.article-item:hover{background:#f8fafc;padding-left:20px;color:inherit;text-decoration:none}
.article-item:last-child{border-bottom:none}
.article-title{font-size:13px;font-weight:700;color:var(--nv-dark);margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5}
.article-item:hover .article-title{color:var(--nv-primary)}
.article-meta{font-size:11px;color:var(--nv-slate);display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.article-meta i{font-size:13px}
.sent-badge{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px}
.sent-pos{background:#dcfce7;color:#15803d}
.sent-neg{background:#fef2f2;color:#dc2626}
.sent-neu{background:#f1f5f9;color:#64748b}
</style>
@endsection

@section('page-title', 'News Overview')

@section('content')
@include('mk.layouts.partials.filter-datepicker')

<div id="pageExportArea">

{{-- KPI Cards - warna sama dengan Mention page --}}
<div class="row mb-3">
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d1" style="background:#06B6D4">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiPos">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d2" style="background:#F59E0B">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiNeg">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-smiley-sad me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d3" style="background:#4CAF50">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiNeu">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-smiley-meh me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d4" style="background:#038047">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Total News</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiTotal">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12"><i class="ph ph-newspaper me-1"></i>Online News Mentions</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div>
      </div></div>
    </div>
  </div>
</div>

{{-- Trend Chart --}}
<div class="card mb-3 fade-up fade-up-d2">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-line f-18 text-primary"></i></div>
      <div><h6 class="mb-0">Mention Trend</h6><small class="text-muted">Tren mention berita online harian</small></div>
    </div>
    <span class="badge bg-light-primary text-primary rounded-pill" id="trendBadge">Loading…</span>
  </div>
  <div class="card-body p-3">
    <div class="nv-loading" id="trendLoading"><div class="nv-spinner"></div><span>Memuat trend…</span></div>
    <div id="trendChart" style="display:none;min-height:320px"></div>
  </div>
</div>

<div class="row mb-3">
  {{-- Sentiment Donut --}}
  <div class="col-lg-5 fade-up fade-up-d3">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
          <div><h6 class="mb-0">Sentiment Distribution</h6><small class="text-muted">Proporsi sentimen berita</small></div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="nv-loading" id="donutLoading"><div class="nv-spinner"></div><span>Memuat…</span></div>
        <div id="donutChart" style="display:none;min-height:300px"></div>
        <div class="d-flex justify-content-center gap-4 mt-2" id="donutLegend" style="display:none!important">
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#10B981;display:inline-block"></span>Positive</span>
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#EF4444;display:inline-block"></span>Negative</span>
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#94A3B8;display:inline-block"></span>Neutral</span>
        </div>
      </div>
    </div>
  </div>
  {{-- Top Publishers --}}
  <div class="col-lg-7 fade-up fade-up-d4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-buildings f-18 text-primary"></i></div>
          <div><h6 class="mb-0">Top Publishers</h6><small class="text-muted">Klik untuk buka website</small></div>
        </div>
        <span class="badge bg-light-primary text-primary rounded-pill" id="pubBadge">Loading…</span>
      </div>
      <div class="card-body p-0" id="pubList" style="max-height:380px;overflow-y:auto">
        <div class="nv-loading"><div class="nv-spinner"></div><span>Memuat…</span></div>
      </div>
    </div>
  </div>
</div>

{{-- Recent Articles --}}
<div class="card mb-3 fade-up fade-up-d4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-article f-18 text-primary"></i></div>
      <div><h6 class="mb-0">Recent Articles</h6><small class="text-muted">Artikel berita terbaru</small></div>
    </div>
    <span class="badge bg-light-primary text-primary rounded-pill" id="artBadge">Loading…</span>
  </div>
  <div class="card-body p-0" id="artList">
    <div class="nv-loading"><div class="nv-spinner"></div><span>Memuat artikel…</span></div>
  </div>
</div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
'use strict';
const NV = (() => {
  const _p = new URLSearchParams(location.search);
  const pid = _p.get('project_id') || '{{ $projectId }}';
  const sd  = _p.get('start_date') || '{{ $startDate }}';
  const ed  = _p.get('end_date')   || '{{ $endDate }}';
  const $ = id => document.getElementById(id);
  const nF = n => parseInt(n||0).toLocaleString('id-ID');
  const nK = n => { n=parseInt(n||0); if(n>=1e6)return(n/1e6).toFixed(1)+'M'; if(n>=1e3)return(n/1e3).toFixed(1)+'k'; return n.toString(); };
  let _apex = null, _eChart = null, _trendTotal = 0;

  async function init() {
    if (!pid) { $('trendLoading').innerHTML='<div class="nv-empty"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>'; return; }
    await Promise.all([_loadTrend(), _loadArticles(), _loadPublishers()]);
  }

  async function _loadTrend() {
    try {
      const r = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${pid}&start_date=${sd}&end_date=${ed}`);
      const j = await r.json();
      const raw = j.data || [];
      // Hitung total dari trend API
      let total = 0;
      raw.forEach(p => { (p.data||[]).forEach(pt => { total += pt.count || 0; }); });
      _trendTotal = total;
      if ($('kpiTotal') && _trendTotal > 0) $('kpiTotal').textContent = nF(_trendTotal);
      _renderTrend(raw);
    } catch(e) { console.warn('Trend failed', e); $('trendLoading').innerHTML='<div class="nv-empty"><i class="ph ph-warning"></i><span>Gagal memuat trend</span></div>'; }
  }

  async function _loadArticles() {
    try {
      const r = await fetch(`/mk/api/news/articles?project_id=${pid}&start_date=${sd}&end_date=${ed}&media=doc&rows=100`);
      const j = await r.json();
      const arts = j.data || [];
      _updateKPIs(arts);
      _renderDonut(arts);
      _renderArticleList(arts);
    } catch(e) { console.warn('Articles failed', e); }
  }

  async function _loadPublishers() {
    try {
      const r = await fetch(`/mk/api/news/top-publisher?project_id=${pid}&start_date=${sd}&end_date=${ed}`);
      const j = await r.json();
      _renderPubs(j.data || []);
    } catch(e) { $('pubList').innerHTML='<div class="nv-empty"><i class="ph ph-warning"></i><span>Gagal memuat</span></div>'; }
  }

  function _getSent(a) {
    const s = (a.sentiment || a.sentiment_class || '').toLowerCase();
    if (s.includes('positif') || s.includes('positive') || s === 'pos') return 'pos';
    if (s.includes('negatif') || s.includes('negative') || s === 'neg') return 'neg';
    return 'neu';
  }

  function _updateKPIs(arts) {
    const pos = arts.filter(a => _getSent(a)==='pos').length;
    const neg = arts.filter(a => _getSent(a)==='neg').length;
    const neu = arts.length - pos - neg;
    const pct = v => arts.length > 0 ? ((v/arts.length)*100).toFixed(1) : '0.0';
    // Scale dengan trendTotal
    const realTotal = _trendTotal > 0 ? _trendTotal : arts.length;
    const posReal = arts.length > 0 ? Math.round((pos/arts.length)*realTotal) : 0;
    const negReal = arts.length > 0 ? Math.round((neg/arts.length)*realTotal) : 0;
    const neuReal = arts.length > 0 ? Math.round((neu/arts.length)*realTotal) : 0;
    if (_trendTotal <= 0) $('kpiTotal').textContent = nF(arts.length);
    $('kpiPos').textContent = nF(posReal);
    $('kpiNeg').textContent = nF(negReal);
    $('kpiNeu').textContent = nF(neuReal);
    $('kpiPosSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>' + pct(pos) + '% of total';
    $('kpiNegSub').innerHTML = '<i class="ph ph-smiley-sad me-1"></i>' + pct(neg) + '% of total';
    $('kpiNeuSub').innerHTML = '<i class="ph ph-smiley-meh me-1"></i>' + pct(neu) + '% of total';
  }

  function _renderTrend(raw) {
    const curr = new Date(sd), end = new Date(ed), dates = [];
    while (curr <= end) { const y=curr.getFullYear(),m=String(curr.getMonth()+1).padStart(2,'0'),d=String(curr.getDate()).padStart(2,'0'); dates.push(`${y}-${m}-${d}`); curr.setDate(curr.getDate()+1); }
    if (!dates.length) { $('trendLoading').style.display='none'; return; }
    const xLabels = dates.map(ds => { const[,m,d]=ds.split('-'); return parseInt(d)+'/'+parseInt(m); });
    const totVals = new Array(dates.length).fill(0);
    if (raw.length) {
      const docEntry = raw.find(p => ['doc','online_news','news'].includes((p.key||'').toLowerCase()));
      if (docEntry) { (docEntry.data||[]).forEach(pt => { const i=dates.indexOf(pt.date); if(i>=0)totVals[i]=pt.count||0; }); }
      else { raw.forEach(p => { (p.data||[]).forEach(pt => { const i=dates.indexOf(pt.date); if(i>=0)totVals[i]+=pt.count||0; }); }); }
    }
    const el = $('trendChart'); if (!el) return;
    if (_apex) { try{_apex.destroy();}catch(e){} }
    el.style.display = 'block';
    _apex = new ApexCharts(el, {
      chart:{type:'area',height:320,fontFamily:'inherit',background:'transparent',toolbar:{show:false},animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:800}}},
      series:[{name:'Mentions',data:totVals}],
      colors:['#4361EE'], fill:{opacity:0.2}, stroke:{curve:'smooth',width:2.5},
      xaxis:{categories:xLabels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}},
      yaxis:{labels:{formatter:v=>nK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false}},
      markers:{size:4,strokeWidth:2,strokeColors:'#fff',hover:{size:6}},
      dataLabels:{enabled:true,formatter:v=>v>0?nK(v):'',style:{fontSize:'10px',fontFamily:'inherit',fontWeight:'800'},background:{enabled:true,borderRadius:3,borderWidth:0,padding:3,opacity:.9},offsetY:-8},
      grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:10,right:10,left:10,bottom:0}},
      legend:{show:false},
      tooltip:{style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>nF(v)+' mentions'}},
    });
    _apex.render();
    const fmtB=ds=>{const[y,m,d]=ds.split('-');const dt=new Date(parseInt(y),parseInt(m)-1,parseInt(d));return parseInt(d)+' '+dt.toLocaleString('id-ID',{month:'short'});};
    $('trendBadge').textContent=fmtB(dates[0])+' - '+fmtB(dates[dates.length-1]);
    $('trendLoading').style.display='none';
  }

  function _renderDonut(arts) {
    const pos=arts.filter(a=>_getSent(a)==='pos').length, neg=arts.filter(a=>_getSent(a)==='neg').length, neu=arts.length-pos-neg, tot=pos+neg+neu;
    const el=$('donutChart');
    if(!el||!tot){$('donutLoading').innerHTML='<div class="nv-empty"><i class="ph ph-chart-donut"></i><span>Tidak ada data</span></div>';return;}
    el.style.display='block'; $('donutLoading').style.display='none'; $('donutLegend').style.cssText='display:flex!important';
    if(_eChart)_eChart.dispose();
    _eChart=echarts.init(el,null,{renderer:'svg'});
    const displayTotal = _trendTotal > 0 ? _trendTotal : tot;
    _eChart.setOption({
      animation:true,animationDuration:800,backgroundColor:'transparent',
      tooltip:{trigger:'item',backgroundColor:'#fff',borderColor:'#e2e8f0',borderWidth:1,padding:12,textStyle:{color:'#0f172a',fontSize:12,fontFamily:'inherit'},
        formatter:p=>{const pc=tot>0?((p.value/tot)*100).toFixed(1):'0';return `<b>${p.name}</b><br/>Mentions: ${nF(p.value)} (${pc}%)`;}
      },
      legend:{show:false},
      series:[{
        type:'pie',radius:['36%','54%'],center:['50%','45%'],
        avoidLabelOverlap:true,minAngle:8,
        itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
        label:{show:true,alignTo:'edge',edgeDistance:12,lineHeight:20,fontFamily:"'Poppins',sans-serif",fontSize:11,
          formatter:p=>{const pc=tot>0?(p.value/tot*100):0;if(pc<2)return'';return`{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;},
          rich:{name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:20},pct:{fontWeight:'700',fontSize:10,color:'#038047',lineHeight:17,backgroundColor:'#edf7f3',borderRadius:4,padding:[2,6]}}
        },
        labelLine:{show:true,length:16,length2:20,smooth:.3,lineStyle:{color:'#c4cdd8',width:1.2}},
        emphasis:{scale:true,scaleSize:5},
        data:[
          {name:'Positive',value:pos,itemStyle:{color:'#10B981'}},
          {name:'Negative',value:neg,itemStyle:{color:'#EF4444'}},
          {name:'Neutral',value:neu,itemStyle:{color:'#94A3B8'}},
        ].filter(d=>d.value>0)
      }],
      graphic:[
        {type:'text',left:'center',top:'38%',z:100,style:{text:nK(displayTotal),fill:'#0f172a',font:"800 17px 'Poppins',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'48%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 8px 'Poppins',sans-serif",textAlign:'center'}},
      ]
    });
    window.addEventListener('resize',()=>{if(_eChart)try{_eChart.resize();}catch(e){}});
  }

  function _renderPubs(pubs) {
    const el=$('pubList');
    if(!pubs.length){el.innerHTML='<div class="nv-empty"><i class="ph ph-buildings"></i><span>Tidak ada data publisher</span></div>';$('pubBadge').textContent='0';return;}
    const top=pubs.slice(0,10);
    el.innerHTML=top.map((p,i)=>{
      const rank=i+1;
      const domain=p.domain||'unknown';
      const url='https://'+domain.replace(/^www\./,'');
      return `<a href="${url}" target="_blank" rel="noopener" class="pub-row">
        <span class="pub-rank${rank<=3?' top':''}">#${rank}</span>
        <span class="pub-name">${domain}</span>
        <span class="pub-count">${nF(p.count)} articles</span>
        <i class="ph ph-arrow-square-out pub-ext"></i>
      </a>`;
    }).join('');
    $('pubBadge').textContent=pubs.length+' publishers';
  }

  function _renderArticleList(arts) {
    const el=$('artList');
    if(!arts.length){el.innerHTML='<div class="nv-empty"><i class="ph ph-article"></i><span>Tidak ada artikel</span></div>';$('artBadge').textContent='0';return;}
    const top=arts.slice(0,20);
    el.innerHTML=top.map(a=>{
      const sent=_getSent(a);
      const sentLabel=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
      const sentClass=sent==='pos'?'sent-pos':sent==='neg'?'sent-neg':'sent-neu';
      const sentIcon=sent==='pos'?'ph-smiley':sent==='neg'?'ph-smiley-sad':'ph-smiley-meh';
      const date=a.date_created?new Date(a.date_created).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}):'';
      const url=a.url||a.link||'#';
      const publisher=a.publisher||a.hostname||'Unknown';
      return `<a href="${url}" target="_blank" rel="noopener" class="article-item">
        <div class="article-title">${a.title||'Untitled'}</div>
        <div class="article-meta">
          <span><i class="ph ph-globe me-1"></i>${publisher}</span>
          <span><i class="ph ph-calendar-blank me-1"></i>${date}</span>
          <span class="sent-badge ${sentClass}"><i class="ph ${sentIcon}"></i> ${sentLabel}</span>
        </div>
      </a>`;
    }).join('');
    $('artBadge').textContent=arts.length+' articles';
  }

  document.addEventListener('DOMContentLoaded',init);
  return{init};
})();
</script>
@endsection
