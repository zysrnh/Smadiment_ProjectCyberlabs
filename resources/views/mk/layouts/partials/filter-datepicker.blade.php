{{--
    Partial: Filter Bar (Project + Datepicker) & Date Picker Modal
    Variables: $projects, $projectId, $startDate, $endDate
--}}

{{-- ══ CSS ══ --}}
<style>
/* ══ Filter-Datepicker Variables (fallbacks) ═════════ */
:root {
    --do-primary        : var(--bs-primary, #4361EE);
    --do-primary-rgb    : var(--bs-primary-rgb, 67,97,238);
    --do-primary-lt     : rgba(var(--do-primary-rgb,67,97,238),.10);
    --do-slate-50       : #F8FAFC;
    --do-slate-100      : #F1F5F9;
    --do-slate-200      : #E2E8F0;
    --do-slate-300      : #CBD5E1;
    --do-slate-400      : #94A3B8;
    --do-slate-500      : #64748B;
    --do-slate-600      : #475569;
    --do-slate-700      : #334155;
    --do-slate-800      : #1E293B;
    --do-slate-900      : #0F172A;
    --do-radius         : 8px;
    --do-radius-sm      : 5px;
    --do-shadow-sm      : 0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
    --do-shadow-md      : 0 4px 14px rgba(15,23,42,.08);
    --do-shadow-lg      : 0 10px 30px rgba(15,23,42,.12);
}

/* ══ Animations (safe re-declare) ════════════════════ */
@keyframes fadeUp    { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes overlayIn { from{opacity:0} to{opacity:1} }

/* ══ Filter Bar ══════════════════════════════════════ */
.do-filter-card {
    background:#fff; border-radius:var(--do-radius);
    border:1px solid var(--do-slate-200); box-shadow:var(--do-shadow-sm);
    padding:14px 18px; margin-bottom:20px;
}
.do-filter-row { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
.do-filter-group { display:flex; flex-direction:column; gap:5px; }
.do-filter-label {
    font-size:10px; font-weight:700; color:var(--do-slate-500);
    text-transform:uppercase; letter-spacing:.5px;
}
.do-filter-select {
    padding:7px 12px; border:1px solid var(--do-slate-200);
    border-radius:var(--do-radius-sm);
    font-size:13px; font-weight:500; color:var(--do-slate-800);
    background:var(--do-slate-50); outline:none;
    transition:border-color .14s, box-shadow .14s;
    min-width:180px; cursor:pointer;
}
.do-filter-select:focus {
    border-color:var(--do-primary);
    box-shadow:0 0 0 3px var(--do-primary-lt);
    background:#fff;
}
.do-date-trigger {
    display:flex; align-items:center; gap:8px; padding:7px 14px;
    background:var(--do-slate-50); border:1px solid var(--do-slate-200);
    border-radius:var(--do-radius-sm); font-size:13px; font-weight:500;
    color:var(--do-slate-800); cursor:pointer; min-width:260px;
    transition:border-color .14s, box-shadow .14s;
}
.do-date-trigger:hover {
    border-color:var(--do-primary);
    box-shadow:0 0 0 3px var(--do-primary-lt);
    background:#fff;
}
.do-date-trigger i { font-size:14px; color:var(--do-slate-400); }
.do-date-trigger span { flex:1; }
.do-filter-submit {
    display:inline-flex; align-items:center; gap:6px; padding:7px 18px;
    background:var(--do-primary); color:#fff; border:none;
    border-radius:var(--do-radius-sm); font-size:13px; font-weight:700;
    cursor:pointer; transition:filter .14s, box-shadow .14s;
    white-space:nowrap; margin-left:auto;
}
.do-filter-submit:hover { filter:brightness(1.1); box-shadow:0 4px 12px var(--do-primary-lt); }
.do-filter-submit i { font-size:14px; }

/* ══ Date Picker Modal ═══════════════════════════════ */
.do-dp-modal {
    position:fixed; inset:0; z-index:10000;
    display:none; align-items:center; justify-content:center;
    background:rgba(15,23,42,.55); backdrop-filter:blur(6px);
}
.do-dp-modal.show { display:flex; animation:overlayIn .2s ease-out; }
.do-dp-overlay { position:absolute; inset:0; cursor:pointer; }
.do-dp-container {
    position:relative; background:#fff;
    border-radius:var(--do-radius); box-shadow:var(--do-shadow-lg);
    display:flex; max-width:860px; width:90%; max-height:90vh;
    z-index:10001; animation:fadeUp .25s ease-out;
}
.do-dp-sidebar {
    width:170px; background:var(--do-slate-50);
    border-right:1px solid var(--do-slate-200);
    padding:14px 10px; border-radius:var(--do-radius) 0 0 var(--do-radius);
    display:flex; flex-direction:column; gap:2px; flex-shrink:0;
}
.do-dp-preset {
    padding:8px 14px; background:transparent; border:none;
    border-radius:var(--do-radius-sm); font-size:12px; font-weight:600;
    color:var(--do-slate-700); text-align:left; cursor:pointer; transition:all .13s;
}
.do-dp-preset:hover { background:#fff; color:var(--do-primary); }
.do-dp-preset.active { background:var(--do-primary); color:#fff; }
.do-dp-content { flex:1; padding:20px; display:flex; flex-direction:column; overflow:hidden; }
.do-dp-header { display:flex; align-items:flex-start; gap:16px; margin-bottom:16px; }
.do-dp-nav {
    width:32px; height:32px; border-radius:var(--do-radius-sm);
    background:var(--do-slate-50); border:1px solid var(--do-slate-200);
    display:flex; align-items:center; justify-content:center; cursor:pointer;
    transition:all .13s; flex-shrink:0; font-size:15px; color:var(--do-slate-600);
}
.do-dp-nav:hover { background:var(--do-primary); border-color:var(--do-primary); color:#fff; }
.do-dp-cals { display:flex; gap:20px; flex:1; }
.do-cal { flex:1; }
.do-cal-month { font-size:13px; font-weight:700; color:var(--do-slate-900); text-align:center; margin-bottom:12px; }
.do-cal-wdays { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; margin-bottom:6px; }
.do-cal-wd { text-align:center; font-size:10px; font-weight:700; color:var(--do-slate-400); padding:6px 0; }
.do-cal-days { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; }
.do-cal-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:500; border-radius:var(--do-radius-sm);
    cursor:pointer; background:transparent; border:none; padding:0;
    font-family:inherit; color:var(--do-slate-800); transition:all .12s;
}
.do-cal-day:hover:not(.dim):not(:disabled) { background:var(--do-slate-100); }
.do-cal-day.dim       { color:var(--do-slate-300); cursor:default; }
.do-cal-day:disabled  { color:var(--do-slate-300); cursor:not-allowed; }
.do-cal-day.today     { border:1.5px solid var(--do-primary); }
.do-cal-day.selected  { background:var(--do-primary); color:#fff; }
.do-cal-day.in-range  { background:var(--do-primary-lt); color:var(--do-primary); }
.do-dp-display {
    padding:10px 16px; background:var(--do-slate-50);
    border:1px solid var(--do-slate-200); border-radius:var(--do-radius-sm);
    text-align:center; margin-bottom:16px;
}
.do-dp-display span { font-size:13px; font-weight:600; color:var(--do-slate-800); }
.do-dp-footer { display:flex; gap:8px; justify-content:flex-end; }
.do-dp-cancel {
    padding:7px 16px; border-radius:var(--do-radius-sm); font-size:12px; font-weight:600;
    cursor:pointer; transition:background .12s; border:1px solid var(--do-slate-200);
    background:var(--do-slate-50); color:var(--do-slate-700);
}
.do-dp-cancel:hover { background:var(--do-slate-100); }
.do-dp-apply {
    padding:7px 18px; border-radius:var(--do-radius-sm); font-size:12px; font-weight:700;
    cursor:pointer; transition:filter .12s; border:none;
    background:var(--do-primary); color:#fff;
}
.do-dp-apply:hover { filter:brightness(1.1); }

/* ══ Responsive ══ */
@media(max-width:768px) {
    .do-filter-row { flex-direction:column; align-items:stretch; }
    .do-filter-submit { margin-left:0; justify-content:center; }
    .do-date-trigger { min-width:auto; }
}
</style>

{{-- ══ Filter Card HTML ══ --}}
<div class="do-filter-card fade-up fade-up-d1">
    <form id="doFilterForm" method="GET">
        <input type="hidden" name="project_id" id="hiddenProjectId" value="{{ $projectId }}">
        <input type="hidden" name="start_date"  id="hiddenStartDate" value="{{ $startDate }}">
        <input type="hidden" name="end_date"     id="hiddenEndDate"   value="{{ $endDate }}">
        <div class="do-filter-row">
            <div class="do-filter-group">
                <label class="do-filter-label">Project</label>
                <select class="do-filter-select" id="doProject">
                    @foreach($projects as $p)
                    <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
                        {{ $p['name'] ?? $p['title'] ?? 'Project #'.$p['id'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="do-filter-group">
                <label class="do-filter-label">Tanggal</label>
                <button type="button" class="do-date-trigger" id="doDateTrigger">
                    <i class="ph ph-calendar-blank"></i>
                    <span id="doDateDisplay">{{ $startDate }} – {{ $endDate }}</span>
                    <i class="ph ph-caret-down"></i>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ══ Date Picker Modal ══ --}}
<div class="do-dp-modal" id="doDpModal">
    <div class="do-dp-overlay" onclick="DPicker.close()"></div>
    <div class="do-dp-container">
        <div class="do-dp-sidebar">
            <button type="button" class="do-dp-preset" data-p="today">Today</button>
            <button type="button" class="do-dp-preset" data-p="yesterday">Yesterday</button>
            <button type="button" class="do-dp-preset" data-p="last7">Last 7 Days</button>
            <button type="button" class="do-dp-preset" data-p="last30">Last 30 Days</button>
            <button type="button" class="do-dp-preset" data-p="thismonth">This Month</button>
            <button type="button" class="do-dp-preset" data-p="lastmonth">Last Month</button>
            <button type="button" class="do-dp-preset active" data-p="custom">Custom Range</button>
        </div>
        <div class="do-dp-content">
            <div class="do-dp-header">
                <button type="button" class="do-dp-nav" onclick="DPicker.nav(-1)"><i class="ph ph-caret-left"></i></button>
                <div class="do-dp-cals">
                    <div class="do-cal" id="doCal1"></div>
                    <div class="do-cal" id="doCal2"></div>
                </div>
                <button type="button" class="do-dp-nav" onclick="DPicker.nav(1)"><i class="ph ph-caret-right"></i></button>
            </div>
            <div class="do-dp-display">
                <span id="doDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
            </div>
            <div class="do-dp-footer">
                <button type="button" class="do-dp-cancel" onclick="DPicker.close()">Batal</button>
                <button type="button" class="do-dp-apply"  onclick="DPicker.apply()">Terapkan</button>
            </div>
        </div>
    </div>
</div>

{{-- ══ JavaScript ══ --}}
<script>
(function(){
const _dpEl = id => document.getElementById(id);
const DPicker = (()=>{
    let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
    const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
    function parseLocal(s){
        if(!s)return null;
        const p=String(s).split('-');
        if(p.length===3) return new Date(+p[0],+p[1]-1,+p[2]);
        return new Date(s);
    }
    function init(){
        const si=_dpEl('hiddenStartDate'),ei=_dpEl('hiddenEndDate');
        ds=si?.value?parseLocal(si.value):(()=>{const d=new Date();d.setHours(0,0,0,0);d.setDate(d.getDate()-6);return d;})();
        de=ei?.value?parseLocal(ei.value):(()=>{const d=new Date();d.setHours(0,0,0,0);return d;})();
        m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
        render();
        _dpEl('doDateTrigger')?.addEventListener('click',open);
        document.querySelectorAll('.do-dp-preset').forEach(b=>b.addEventListener('click',onPreset));
        document.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
        _dpEl('doProject')?.addEventListener('change',function(){
            _dpEl('hiddenProjectId').value=this.value;
            _dpEl('doFilterForm').submit();
        });
    }
    function open(){_dpEl('doDpModal').classList.add('show');render();}
    function close(){_dpEl('doDpModal').classList.remove('show');}
    function apply(){
        _dpEl('hiddenStartDate').value=fmt(ds);
        _dpEl('hiddenEndDate').value=fmt(de);
        _dpEl('doDateDisplay').textContent=fmt(ds)+' – '+fmt(de);
        close();
        _dpEl('doFilterForm').submit();
    }
    function nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
    function onPreset(e){
        document.querySelectorAll('.do-dp-preset').forEach(b=>b.classList.remove('active'));
        e.target.classList.add('active');
        const today=new Date();today.setHours(0,0,0,0);
        switch(e.target.dataset.p){
            case 'today':    ds=new Date(today);de=new Date(today);break;
            case 'yesterday':ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
            case 'last7':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
            case 'last30':   de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
            case 'thismonth':ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
            case 'lastmonth':ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
        }
        if(e.target.dataset.p!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);}
        updDisp();render();
    }
    function render(){renderCal('doCal1',m1);renderCal('doCal2',m2);updDisp();}
    function renderCal(id,month){
        const el=_dpEl(id);if(!el)return;
        const y=month.getFullYear(),mn=month.getMonth();
        const first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
        const today=new Date();today.setHours(0,0,0,0);
        let h=`<div class="do-cal-month">${MN[mn]} ${y}</div>
               <div class="do-cal-wdays">${WD.map(d=>`<div class="do-cal-wd">${d}</div>`).join('')}</div>
               <div class="do-cal-days">`;
        for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="do-cal-day dim" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
        for(let d=1;d<=last.getDate();d++){
            const date=new Date(y,mn,d);date.setHours(0,0,0,0);
            let cls='do-cal-day';
            if(sD(date,today))cls+=' today';
            if(date>today)cls+=' dim';
            if(ds&&de){if(sD(date,ds)||sD(date,de))cls+=' selected';else if(date>ds&&date<de)cls+=' in-range';}
            h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
        }
        const rem=last.getDay()===6?0:6-last.getDay();
        for(let i=1;i<=rem;i++) h+=`<button type="button" class="do-cal-day dim" disabled>${i}</button>`;
        h+='</div>';el.innerHTML=h;
        el.querySelectorAll('.do-cal-day:not(.dim):not(:disabled)').forEach(btn=>{
            btn.addEventListener('click',function(){
                const d=parseLocal(this.dataset.date);
                document.querySelectorAll('.do-dp-preset').forEach(b=>b.classList.remove('active'));
                document.querySelector('[data-p="custom"]').classList.add('active');
                if(pickStart||d<ds){ds=d;de=d;pickStart=false;}
                else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
                updDisp();render();
            });
        });
    }
    function updDisp(){const el=_dpEl('doDpRangeText');if(el&&ds&&de)el.textContent=fmt(ds)+' – '+fmt(de);}
    function fmt(d){if(!d)return '';return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
    function sD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
    return{init,open,close,apply,nav};
})();

window.DPicker = DPicker;

_dpEl('doFilterForm')?.addEventListener('submit',function(e){e.preventDefault();_dpEl('hiddenProjectId').value=_dpEl('doProject').value;this.submit();});

document.addEventListener('DOMContentLoaded',()=>DPicker.init());
})();
</script>
