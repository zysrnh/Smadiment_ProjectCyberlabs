
'use strict';

const NSS_PID = 1;
const NSS_SD  = '1';
const NSS_ED  = '1';

const $      = id => document.getElementById(id);
const numFmt = n  => (parseInt(n)||0).toLocaleString('id-ID');
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
function getPrimary() { return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim()||'#4361EE'; }

/* ── Count-up ── */
function countUp(el, target, dur=900) {
    if(!el) return; el.innerHTML='';
    const s=performance.now(), ease=t=>1-Math.pow(1-t,3);
    (function tick(n){const p=Math.min((n-s)/dur,1);el.textContent=numFmt(Math.round(target*ease(p)));if(p<1)requestAnimationFrame(tick);})(performance.now());
}

/* ── ECharts ── */
const NSS_Charts={_i:{},make(id){if(this._i[id]){try{this._i[id].dispose();}catch(e){}}const d=$(id);if(!d)return null;const c=echarts.init(d,null,{renderer:'canvas'});this._i[id]=c;return c;}};
window.addEventListener('resize',()=>Object.values(NSS_Charts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}}));
const EC_TT={backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[9,13],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'};

/* ── Gauge ── */
let _raf=null;
function renderGauge(nss){
    const val=Math.max(-100,Math.min(100,nss)),targetRot=(val/100)*90;
    const needle=$('nssNeedle'),scoreEl=$('nssScoreNum'),labelEl=$('nssScoreLbl');
    const isPos=val>5,isNeg=val<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const lbl=isPos?'POSITIF':isNeg?'NEGATIF':'NETRAL';
    const finalStr=(val>0?'+':'')+val.toFixed(0)+'%';
    const tf=needle.getAttribute('transform')||'rotate(0,250,260)';
    const cur=parseFloat((tf.match(/rotate\(([-\d.]+)/)||[0,0])[1]);
    if(_raf)cancelAnimationFrame(_raf);
    const dur=1200,t0=performance.now(),ease=t=>t<.5?2*t*t:-1+(4-2*t)*t;
    (function frame(now){
        const p=Math.min((now-t0)/dur,1),rot=cur+(targetRot-cur)*ease(p);
        needle.setAttribute('transform',`rotate(${rot.toFixed(3)},250,260)`);
        const lv=(rot/90)*100;
        if(scoreEl){scoreEl.textContent=(lv>0?'+':'')+lv.toFixed(0)+'%';scoreEl.style.color=color;}
        if(p<1){_raf=requestAnimationFrame(frame);}else{
            if(scoreEl){scoreEl.textContent=finalStr;scoreEl.style.color=color;}
            if(labelEl){labelEl.textContent=lbl;labelEl.style.color=isPos?'#0ea5e9':isNeg?'#dc2626':'#94A3B8';}
            _raf=null;
        }
    })(performance.now());
}

/* ── Distribution chart ── */
function renderDist(pos,neu,neg){
    const dom=$('chNssDist');if(!dom)return;
    const chart=NSS_Charts.make('chNssDist');
    const tot=pos+neu+neg||1;
    const pPct=+(pos/tot*100).toFixed(1),nuPct=+(neu/tot*100).toFixed(1),nePct=+(neg/tot*100).toFixed(1);
    chart.setOption({
        animation:true,animationDuration:900,animationEasing:'cubicInOut',backgroundColor:'transparent',
        tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'none'},formatter:params=>{const p=params.find(x=>x.value>0);return p?`<div style="font-weight:700;margin-bottom:4px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;"></span>${p.seriesName}</div><div style="display:flex;justify-content:space-between;gap:16px;"><span style="opacity:.7;">Share</span><span style="font-weight:700;">${p.value}%</span></div>`:'';}},
        grid:{left:0,right:22,top:4,bottom:0,containLabel:true},
        xAxis:{type:'value',max:100,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:'inherit',fontSize:10,color:'#94A3B8',formatter:v=>v+'%'}},
        yAxis:{type:'category',data:['Negative','Neutral','Positive'],axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#475569',margin:10}},
        series:[
            {name:'Positive',type:'bar',data:[null,null,pPct],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(14,165,233,.1)'},{offset:1,color:'#0ea5e9'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#0ea5e9',formatter:v=>v.value+'%'}},
            {name:'Neutral', type:'bar',data:[null,nuPct,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(148,163,184,.1)'},{offset:1,color:'#94A3B8'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#94A3B8',formatter:v=>v.value+'%'}},
            {name:'Negative',type:'bar',data:[nePct,null,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(239,68,68,.1)'},{offset:1,color:'#EF4444'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#EF4444',formatter:v=>v.value+'%'}},
        ],
    });
    chart.on('click',params=>{if(params.componentType==='series'){const m={Positive:'pos',Neutral:'neu',Negative:'neg'};NSSPanel.open(m[params.seriesName]||'pos');}});
    chart.on('mouseover',p=>{if(p.componentType==='series')chart.getDom().style.cursor='pointer';});
    chart.on('mouseout',()=>{chart.getDom().style.cursor='default';});
}

/* ── Breakdown ── */
function updateBreakdown(pos,neu,neg,nss){
    const tot=pos+neu+neg;
    $('brkPos').textContent=numFmt(pos); $('brkNeu').textContent=numFmt(neu);
    $('brkNeg').textContent=numFmt(neg); $('brkTot').textContent=numFmt(tot);
    const isPos=nss>5,isNeg=nss<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const nssStr=(nss>=0?'+':'')+nss.toFixed(1)+'%';
    const nssEl=$('brkNSS');
    nssEl.textContent=nssStr; nssEl.style.color=color;
    const row=nssEl.closest('.nss-brk-nss-row');
    row.style.background    =isPos?'rgba(14,165,233,.07)':isNeg?'rgba(239,68,68,.07)':'rgba(148,163,184,.07)';
    row.style.borderTopColor=isPos?'rgba(14,165,233,.2)' :isNeg?'rgba(239,68,68,.2)' :'rgba(148,163,184,.2)';
    const keyEl=row.querySelector('.nss-brk-nss-key'); if(keyEl)keyEl.style.color=color;
    const badge=$('nssBadgeMain');
    if(badge){badge.textContent=nssStr;badge.className='do-badge'+(isPos?' do-badge--pos':isNeg?' do-badge--neg':'');}
}

/* ── Data loader ── */
async function loadNSS(){
    if(!NSS_PID){
        ['statPos','statNeu','statNeg'].forEach(id=>{const el=$(id);if(el)el.textContent='—';});
        ['pctPos','pctNeu','pctNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>No Project';});
        const badge=$('nssBadgeMain'); if(badge) badge.textContent='No Project';
        return;
    }
    try{
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';
        const res=await fetch(`/mk/api/sentiment/totals?project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&media=${media}`);
        if(!res.ok)throw new Error(`HTTP ${res.status}`);
        const json=await res.json(); if(json.error)throw new Error(json.error);
        const t=json.totals||{pos:0,neg:0,neu:0};
        const pos=parseInt(t.pos)||0,neg=parseInt(t.neg)||0,neu=parseInt(t.neu)||0;
        const tot=pos+neg+neu,posneg=pos+neg,nss=posneg===0?0:((pos-neg)/posneg*100);
        countUp($('statPos'),pos); countUp($('statNeu'),neu); countUp($('statNeg'),neg);
        $('pctPos').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(pos,tot)}`; $('pctNeu').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(neu,tot)}`; $('pctNeg').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(neg,tot)}`;
        $('legPos').textContent=numFmt(pos)+' Positive'; $('legNeu').textContent=numFmt(neu)+' Neutral'; $('legNeg').textContent=numFmt(neg)+' Negative';
        updateBreakdown(pos,neu,neg,nss); renderGauge(nss); renderDist(pos,neu,neg);
    }catch(err){
        console.error('loadNSS:',err);
        ['statPos','statNeu','statNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">Error</span>';});
        ['pctPos','pctNeu','pctNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>Gagal memuat';});
        const badge=$('nssBadgeMain'); if(badge) badge.textContent='Error';
    }
}

/* ══ Media Dropdown ══ */
const MEDIA_LABELS={all:'All Media',doc:'Mass Media (News)',twitter:'X / Twitter',facebook:'Facebook',instagram:'Instagram',youtube:'YouTube',tiktok:'TikTok'};
const NSSPage={
    toggleMenu(){const o=$('nssMediaMenu').classList.toggle('show');$('nssMediaBtn')?.classList.toggle('open',o);},
    selectMedia(el){
        document.querySelectorAll('.nss-media-menu-item').forEach(i=>i.classList.remove('active'));
        el.classList.add('active');
        const lbl=$('nssMediaLabel'); if(lbl) lbl.textContent=MEDIA_LABELS[el.dataset.m]||'All Media';
        $('nssMediaMenu').classList.remove('show'); $('nssMediaBtn')?.classList.remove('open');
        NSSPanel._cache={}; loadNSS();
    },
};
document.addEventListener('click',e=>{const w=$('nssMediaWrap');if(w&&!w.contains(e.target)){$('nssMediaMenu').classList.remove('show');$('nssMediaBtn')?.classList.remove('open');}});

/* ══ SLIDE PANEL ══ */
const NSSPanel=(()=>{
    let _cache={},_allItems=[],_filtered=[],_curSent='all';
    const SENT_MAP={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
    const SENT_COLORS={pos:'#0ea5e9',neg:'#EF4444',neu:'#94A3B8',all:'#4361EE'};
    const SENT_LABELS={pos:'Positive',neg:'Negative',neu:'Neutral',all:'All Mentions'};
    const PLAT_META={
        doc:      {label:'Online News', color:'#0284c7'},
        twitter:  {label:'X / Twitter', color:'#1d9bf0'},
        facebook: {label:'Facebook',    color:'#1877f2'},
        instagram:{label:'Instagram',   color:'#e1306c'},
        youtube:  {label:'YouTube',     color:'#ff0000'},
        tiktok:   {label:'TikTok',      color:'#111827'},
    };
    function _normSent(item){const r=String(item.class_sentiment||item.sentiment||item.sentiment_str||'0').toLowerCase().trim();return SENT_MAP[r]||'neu';}

    async function open(sentiment){
        _curSent=sentiment||'all';
        const color=SENT_COLORS[_curSent]||getPrimary();
        const label=SENT_LABELS[_curSent]||'Mentions';
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';
        $('nssPanelDot').style.background=color;
        $('nssPanelTitle').textContent=label;
        $('nssPanelMeta').textContent=NSS_SD+' – '+NSS_ED;
        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===_curSent));
        const list=$('nssPanelList');
        list.innerHTML=`<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        overlay.classList.remove('hiding'); panel.classList.remove('hiding');
        overlay.classList.add('show'); panel.classList.add('show');
        try{
            const key=`${NSS_PID}_${media}_${NSS_SD}_${NSS_ED}`;
            if(!_cache[key]) _cache[key]=await _fetchAll(media);
            _allItems=_cache[key];
            _filtered=_filterBySent(_allItems,_curSent);
            _render(list,_filtered);
        }catch(err){
            list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:13px;">Gagal memuat data<br><small>${esc(err.message)}</small></div>`;
        }
    }

    function close(){
        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        panel.classList.add('hiding'); overlay.classList.add('hiding');
        setTimeout(()=>{panel.classList.remove('show','hiding');overlay.classList.remove('show','hiding');},240);
    }
    function closeByOverlay(){close();}
    function filterSent(sent){
        _curSent=sent;
        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===sent));
        _filtered=_filterBySent(_allItems,sent);
        _render($('nssPanelList'),_filtered);
    }
    function _filterBySent(items,sent){return sent==='all'?items:items.filter(i=>_normSent(i)===sent);}

    async function _fetchAll(media){
        const platforms=media==='all'?['doc','twitter','facebook','instagram','youtube','tiktok']:[media];
        const res=await Promise.allSettled(platforms.map(p=>_fetchOne(p)));
        return res.flatMap(r=>r.status==='fulfilled'?r.value:[]);
    }
    async function _fetchOne(platform){
        const q=`project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=200&start=0`;
        /* Online News: use articles API */
        if(platform==='doc'){
          const docQ=`project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=50&start=0&media=doc`;
          try{ const res=await fetch(`/mk/api/news/articles?${docQ}`); if(!res.ok) return []; const d=await res.json(); let items=Array.isArray(d?.data)?d.data:(Array.isArray(d)?d:[]); return items.map(i=>({...i,_platform:'doc'})); }catch(e){ return []; }
        }
        const eps={
            twitter:  `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
            facebook: `/mk/api/news/fb-top-status?${q}&sub=fblike`,
            instagram:`/mk/api/news/ig-top-status?${q}`,
            youtube:  `/mk/api/news/ytb-top-status?${q}`,
            tiktok:   `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
        };
        const url=eps[platform];if(!url)return[];
        try{
            const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),20000);
            const res=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);
            if(!res.ok)return[];
            const data=await res.json();
            let items=Array.isArray(data?.data?.data)?data.data.data:Array.isArray(data?.data)?data.data:(Array.isArray(data)?data:[]);
            items=items.map(i=>({...i,_platform:platform}));
            /* Twitter fallback chain */
            if(platform==='twitter' && items.length===0){
              for(const fbUrl of [`/mk/api/x/most-retweets?${q}`,`/mk/api/x/user-mentions?${q}`]){
                try{ const r2=await fetch(fbUrl); if(r2.ok){ const d2=await r2.json(); const i2=Array.isArray(d2?.data)?d2.data:Array.isArray(d2)?d2:[]; if(i2.length>0){ items=i2.map(i=>({...i,_platform:'twitter'})); break; } } }catch(e){}
              }
              if(items.length===0){
                try{ const r3=await fetch(`/mk/api/news/mentions?${q}`); const d3=await r3.json(); let all=Array.isArray(d3?.data?.data)?d3.data.data:Array.isArray(d3?.data)?d3.data:[];
                  items=all.filter(m=>{const tc=String(m.tcode||'').toLowerCase(),mt=String(m.media_type||'').toLowerCase(),u2=String(m.url||'').toLowerCase(); return tc==='twit'||tc==='rt'||mt==='twit'||mt==='twitter'||mt==='x'||u2.includes('twitter.com')||u2.includes('x.com');}).map(i=>({...i,_platform:'twitter'}));
                }catch(e){}
              }
            }
            return items;
        }catch(e){return[];}
    }

    function _render(list,items, showAll = false){
        if(!items.length){list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;return;}
        const SHOW=80;
        const visibleItems = showAll ? items : items.slice(0,SHOW);
        list.innerHTML=visibleItems.map(item=>{
            const plat=item._platform||'doc';
            const meta=PLAT_META[plat]||{label:plat,color:getPrimary()};
            const sent=_normSent(item);
            const sentText={pos:'Pos',neg:'Neg',neu:'Neu'}[sent]||'Neu';
            const ao=(()=>{if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return {};}})();
            const rawName=(()=>{
                if(plat==='facebook')  return item.from_name||item.page_name||item.author_name||ao?.name||item.author_handle||null;
                if(plat==='instagram') return item.username||item.user_name||null;
                if(plat==='tiktok')    return item.author_nickname||item.nickname||ao?.nickname||null;
                if(plat==='youtube')   return item.channel_title||item.channel_name||null;
                if(plat==='twitter')   return item.name||ao?.name||ao?.scr_name||item.author_name||item.author_scr_name||null;
                return null;
            })();
            let name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'').trim();
            if(!name||/^\d{5,}$/.test(name)||name.toLowerCase()==='unknown'){
              const alt=(item.author_handle||item.author_scr_name||item.screen_name||ao?.scr_name||ao?.username||item.username||item.nickname||'').trim();
              if(alt&&!/^\d{5,}$/.test(alt)) name=alt; else if(!name) name='Unknown';
            }
            const dName=name;
            const rawHandle=(item.author_handle||item.author_scr_name||item.screen_name||ao?.scr_name||item.username||item.handle||'').trim();
            const handle=rawHandle&&rawHandle.toLowerCase()!==dName.toLowerCase()?((['twitter','instagram','tiktok'].includes(plat))?(rawHandle.startsWith('@')?rawHandle:'@'+rawHandle):rawHandle):'';
            const artTitle=(plat==='doc')?(item.title||'').replace(/<[^>]*>/g,'').trim():'';
            const text=(()=>{if(plat==='doc'){const c=(item.content||'').replace(/<[^>]*>/g,'').trim(); return c?c.slice(0,150):(item.title||'').slice(0,150);} return (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);})();
            const av=(item.avatar_url||item.profile_image_url||item.author_image||ao?.image||item.profile_image||'').trim();
            const dt=(item.date_created||item.created_at||item.publish_date||'').split('T')[0];
            const words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||'?')).toUpperCase();
            const safeIni=ini.replace(/['"]/g,'');
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}';">`:(plat==='doc'?`<i class="ph ph-newspaper" style="font-size:14px;color:#fff;"></i>`:ini);
            const docUrl=(plat==='doc')?(item.url||item.link||'').trim():'';
            /* Doc: special rendering with article title */
            if(plat==='doc'&&artTitle){
              return `<div class="do-panel-item">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);"><i class="ph ph-newspaper" style="font-size:14px;color:#fff;"></i></div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author" style="font-size:10px;color:#64748b;">${esc(dName)}</div>
                    <div style="font-size:12px;font-weight:700;color:#1e293b;line-height:1.3;margin:2px 0 3px;">${esc(artTitle.slice(0,100))}</div>
                    <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentText}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${docUrl?`<a href="${esc(docUrl)}" target="_blank" rel="noopener noreferrer" style="margin-left:auto;font-size:10px;font-weight:700;color:${meta.color};display:inline-flex;align-items:center;gap:3px;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'"><i class="ph ph-arrow-square-out" style="font-size:12px;"></i>Buka</a>`:''}
                        ${dt?`<span>${dt}</span>`:''}
                    </div>
                </div>
              </div>`;
            }
            return `<div class="do-panel-item">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(dName)}</div>
                    ${handle?`<div style="font-size:10px;color:#94a3b8;margin-top:-1px;">${esc(handle)}</div>`:''}
                    <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentText}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
                    </div>
                </div>
            </div>`;
        }).join('');
        if(!showAll && items.length>SHOW) {
             const btnWrap = document.createElement('div');
             btnWrap.style.padding = '16px';
             btnWrap.style.textAlign = 'center';
             btnWrap.style.borderTop = '1px dashed rgba(0,0,0,.08)';
             btnWrap.style.background = '#f8fafc';
             btnWrap.innerHTML = `<button onclick="NSSPanel.showAll()" style="background:#038047;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;box-shadow:0 2px 4px rgba(3,128,71,.2);" onmouseover="this.style.background='#026136';this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#038047';this.style.transform='none';">Muat Lebih Banyak</button>`;
             list.appendChild(btnWrap);
        }
    }

    function showAll() { _render($('nssPanelList'), _filtered, true); }

    return{open,close,closeByOverlay,filterSent,showAll,get _cache(){return _cache;},set _cache(v){_cache=v;}};
})();

/* ══════════════════════════════════════════════════════
   NSSExport — FIXED v6
   Fix tambahan: adjust posisi .nss-score-overlay
   saat capture agar teks +27% tidak tertutup needle
══════════════════════════════════════════════════════ */
const NSSExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t   = document.getElementById('nssExportToast');
        const m   = document.getElementById('nssExportToastMsg');
        const ico = document.getElementById('nssExportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        const icons   = { success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' };
        ico.className = 'ph ' + (icons[type] || icons.default);
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
    }

    function _btnState(btns, loading) {
        [].concat(btns).forEach(b => {
            if (!b) return;
            b.disabled = loading;
            b.classList.toggle('exporting', loading);
        });
    }

    let _freezeStyle = null;

    function _freezeAnimations() {
        /* Cancel gauge needle animation */
        if (typeof _raf !== 'undefined' && _raf) {
            cancelAnimationFrame(_raf);
        }

        /* Freeze ECharts */
        Object.values(NSS_Charts._i).forEach(c => {
            try {
                if (!c.isDisposed()) {
                    c.setOption({ animation: false }, false);
                    if (c.getZr && typeof c.getZr === 'function') {
                        c.getZr().flush(true);
                    }
                    c.resize();
                }
            } catch (e) {}
        });

        /* Hanya freeze shimmer skeleton — JANGAN reset transform/opacity
           karena akan menghapus posisi final elemen .fade-up */
        _freezeStyle = document.createElement('style');
        _freezeStyle.id = '__nss_freeze__';
        _freezeStyle.textContent = `
            #nssExportArea .kpi-card-hover::before {
                display: none !important;
            }
        `;
        document.head.appendChild(_freezeStyle);
    }

    function _restoreAnimations() {
        if (_freezeStyle) {
            _freezeStyle.remove();
            _freezeStyle = null;
        }
        Object.values(NSS_Charts._i).forEach(c => {
            try {
                if (!c.isDisposed()) c.setOption({ animation: true }, false);
            } catch (e) {}
        });
    }

    /* ════════════════════════════════════════════════════
       Capture full page
       Kunci: paksa semua .fade-up ke posisi final sebelum
       capture dengan menambahkan class helper, lalu restore
    ════════════════════════════════════════════════════ */
    async function _capture() {
        const area = document.getElementById('nssExportArea');
        if (!area) throw new Error('nssExportArea tidak ditemukan');

        window.scrollTo({ top: 0, behavior: 'instant' });

        /* Paksa semua animasi fade-up ke state final */
        const animStyle = document.createElement('style');
        animStyle.id = '__nss_anim_fix__';
        animStyle.textContent = `
            #nssExportArea .fade-up,
            #nssExportArea .fade-up-d1,
            #nssExportArea .fade-up-d2,
            #nssExportArea .fade-up-d3 {
                animation: none !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            #nssExportArea [style*="animation"] {
                animation-fill-mode: both !important;
                animation-delay: 0s !important;
                animation-duration: 0.01s !important;
            }
        `;
        document.head.appendChild(animStyle);

        await new Promise(r => setTimeout(r, 200));

        _freezeAnimations();
        await new Promise(r => setTimeout(r, 300));

        const areaH = area.scrollHeight;

        const canvas = await html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#f1f5f9',
            logging:         false,
            removeContainer: true,
            x:               0,
            y:               0,
            scrollX:         0,
            scrollY:         0,
            windowWidth:     document.documentElement.scrollWidth,
            windowHeight:    areaH,
            width:           area.offsetWidth,
            height:          areaH,
            ignoreElements:  el =>
                el.hasAttribute('data-html2canvas-ignore') ||
                el.id === 'nssExportPdfBtn'  ||
                el.id === 'nssExportImgBtn'  ||
                el.id === 'nssExportToast'   ||
                el.id === 'nssSntPanel'      ||
                el.id === 'nssPanelOverlay',
        });

        animStyle.remove();
        _restoreAnimations();
        return canvas;
    }

    /* ════════════════════════════════════════════════════
       Capture single card
    ════════════════════════════════════════════════════ */
    async function _captureCard(areaId) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

        const animStyle = document.createElement('style');
        animStyle.id = '__nss_anim_fix__';
        animStyle.textContent = `
            #${areaId} .fade-up,
            #${areaId} .fade-up-d1,
            #${areaId} .fade-up-d2,
            #${areaId} .fade-up-d3 {
                animation: none !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        `;
        document.head.appendChild(animStyle);

        _freezeAnimations();
        await new Promise(r => setTimeout(r, 300));

        const canvas = await html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#ffffff',
            logging:         false,
            removeContainer: true,
            ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
        });

        animStyle.remove();
        _restoreAnimations();
        return canvas;
    }

    /* ════════════════════════════════════════════════════
       PDF Helpers
    ════════════════════════════════════════════════════ */
    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'Net Sentiment Score'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
    }

    function _fitCanvas(pdf, canvas, margin, pW, pH, label) {
        _drawPdfHeader(pdf, pW, margin, label);
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - 18;
        const ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        const dstW    = canvas.width  * ratio;
        const dstH    = canvas.height * ratio;
        pdf.addImage(
            canvas.toDataURL('image/png', 1), 'PNG',
            margin + (usableW - dstW) / 2,
            14    + (usableH - dstH) / 2,
            dstW, dstH
        );
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text('Halaman 1 / 1', pW / 2, pH - 3, { align: 'center' });
    }

    function _canvasToPdf(pdf, canvas, margin, pW, pH, label) {
        const usableW  = pW - margin * 2;
        const usableH  = pH - margin * 2 - 14;
        const ratio    = usableW / canvas.width;
        const slicePx  = Math.floor(usableH / ratio);
        const numPages = Math.ceil(canvas.height / slicePx);

        for (let page = 0; page < numPages; page++) {
            if (page > 0) pdf.addPage();
            _drawPdfHeader(pdf, pW, margin, label);
            const srcY     = page * slicePx;
            const srcSlice = Math.min(slicePx, canvas.height - srcY);
            if (srcSlice <= 0) break;
            const dstH  = srcSlice * ratio;
            const slice = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = Math.ceil(srcSlice);
            const ctx    = slice.getContext('2d');
            ctx.fillStyle = '#f1f5f9';
            ctx.fillRect(0, 0, slice.width, slice.height);
            ctx.drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png', 1), 'PNG', margin, 14, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${page + 1} / ${numPages}`, pW / 2, pH - 3, { align: 'center' });
        }
        return numPages;
    }

    const _cardLabels = {
        gauge:     'Net Sentiment Score Gauge',
        dist:      'Sentiment Distribution',
        breakdown: 'Score Breakdown',
    };

    /* ════════════════════════════════════════════════════
       PUBLIC: Export per card
    ════════════════════════════════════════════════════ */
    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);
        try {
            const canvas = await _captureCard(areaId);
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const fname  = `nss_${cardKey}_${NSS_PID}_${stamp}`;
            const label  = _cardLabels[cardKey] || cardKey;
            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = fname + '.png';
                a.href     = canvas.toDataURL('image/png', 1);
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const orient = canvas.width > canvas.height ? 'landscape' : 'portrait';
                const pdf    = new jsPDF({ orientation: orient, unit: 'mm', format: 'a4' });
                _fitCanvas(pdf, canvas, 10, pdf.internal.pageSize.getWidth(), pdf.internal.pageSize.getHeight(), label);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch (err) {
            console.error('[NSSExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    /* ════════════════════════════════════════════════════
       PUBLIC: Export full page
    ════════════════════════════════════════════════════ */
    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        const btnPdf = document.getElementById('nssExportPdfBtn');
        const btnImg = document.getElementById('nssExportImgBtn');
        _btnState([btnPdf, btnImg], true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);
        try {
            const canvas = await _capture();
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = `net_sentiment_score_${NSS_PID}_${stamp}.png`;
                a.href     = canvas.toDataURL('image/png', 1);
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf  = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW   = pdf.internal.pageSize.getWidth();
                const pH   = pdf.internal.pageSize.getHeight();
                const pages = _canvasToPdf(pdf, canvas, 10, pW, pH, 'Net Sentiment Score');
                pdf.save(`net_sentiment_score_${NSS_PID}_${stamp}.pdf`);
                _toast(`PDF ${pages} halaman berhasil diunduh!`, 'success');
            }
        } catch (err) {
            console.error('[NSSExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState([btnPdf, btnImg], false);
        }
    }

    return { run, runCard };
})();

/* ══ Boot ══ */
document.addEventListener('DOMContentLoaded',()=>{
    const needle=$('nssNeedle');
    if(needle) needle.setAttribute('transform','rotate(0,250,260)');
    loadNSS();
});


