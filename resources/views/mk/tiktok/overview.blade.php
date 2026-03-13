@extends('mk.layouts.app')

@section('title', 'TikTok Overview - SMADIMENT')

@section('styles')
<style>
/* ══ Design Tokens ══ */
:root {
    --primary        : #038047;
    --primary-rgb    : 3, 128, 71;
    --primary-lt     : rgba(3,128,71,.10);
    --slate-50       : #F8FAFC;
    --slate-100      : #F1F5F9;
    --slate-200      : #E2E8F0;
    --slate-400      : #94A3B8;
    --slate-500      : #64748B;
    --slate-800      : #1E293B;
    --radius         : 8px;
    --radius-sm      : 5px;
}

/* ══ Animations ══ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer { 0%{left:-100%} 100%{left:150%} }

/* ══ KPI Icon bg ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

/* ══ Skeleton ══ */
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}
.loading-skeleton {
    background:linear-gradient(90deg,var(--slate-50) 25%,#e2e8f0 50%,var(--slate-50) 75%);
    background-size:200% 100%; animation:shimmer 1.5s ease-in-out infinite; border-radius:8px;
}

/* ══ KPI Card Hover ══ */
.kpi-card-hover {
    will-change:transform,box-shadow; cursor:default; position:relative!important; overflow:hidden!important;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important, box-shadow .25s ease!important, filter .25s ease!important;
}
.kpi-card-hover::before {
    content:''; position:absolute; top:0;bottom:0; left:-100%; width:60%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
    pointer-events:none; z-index:1;
}
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }

/* ══ Chart container ══ */
.chart-container { position:relative; }

/* ══ All Users Modal ══ */
.all-users-modal {
    position:fixed; inset:0; z-index:9999; display:none;
    align-items:center; justify-content:center;
    background:rgba(0,0,0,.6); backdrop-filter:blur(8px);
    opacity:0; transition:opacity .3s ease;
}
.all-users-modal.show { display:flex; opacity:1; }
.all-users-modal .modal-overlay { position:absolute; inset:0; }
.all-users-modal .modal-content {
    position:relative; background:#fff; border-radius:16px;
    box-shadow:0 20px 60px rgba(0,0,0,.3); width:95%; max-width:1000px;
    max-height:90vh; display:flex; flex-direction:column; z-index:10000;
}
.all-users-modal .modal-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:16px 20px; border-bottom:1px solid var(--slate-200);
}
.all-users-modal .modal-header h3 { font-size:16px; font-weight:700; margin:0; }
.all-users-modal .modal-body { padding:0; overflow-y:auto; }
.modal-close {
    width:32px; height:32px; border-radius:8px; background:var(--slate-50); border:none;
    cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s;
}
.modal-close:hover { background:#ef4444; color:#fff; }
</style>
@endsection

@section('page-title', 'TikTok Overview')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp

<script>
    const OV_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const OV_SD  = '{{ $startDate }}';
    const OV_ED  = '{{ $endDate }}';
</script>

{{-- Filter --}}
@include('mk.layouts.partials.filter-datepicker')

{{-- ══ KPI Cards ══ --}}
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Volume Total</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiVolume">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-chart-bar me-1"></i>Total mentions
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-success text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPositive">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-smiley me-1"></i>Sentimen positif
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-warning text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiNeutral">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-smiley-meh me-1"></i>Sentimen netral
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-danger text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiNegative">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-smiley-sad me-1"></i>Sentimen negatif
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Top Hashtags Card ══ --}}
<div class="row">
    <div class="col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-hash f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Top Hashtags</h6>
                        <small class="text-muted">Hashtag paling sering digunakan</small>
                    </div>
                </div>
                <span class="badge bg-light-primary text-primary" id="badgeHashtag">Loading…</span>
            </div>
            <div class="card-body">
                <div id="hashtagLoading" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-muted f-12">Memuat hashtag…</span>
                </div>
                <div id="hashtagContent" style="display:none;">
                    <div id="hashtagList" class="d-flex flex-wrap gap-2"></div>
                </div>
                <div id="hashtagEmpty" style="display:none;" class="text-center py-4">
                    <i class="ph ph-hash f-32 text-muted d-block mb-2"></i>
                    <span class="text-muted f-12">Tidak ada data hashtag</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Charts Row ══ --}}
<div class="row">
    {{-- Volume Trend --}}
    <div class="col-xl-7 col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .22s both;">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded">
                    <i class="ph ph-chart-line f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Volume Trend</h6>
                    <small class="text-muted">Daily posting volume over time</small>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:320px;">
                    <div id="volumeTrendLoading" class="loading-skeleton" style="height:100%;"></div>
                    <canvas id="volumeTrendChart" style="display:none;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Sentiment Distribution --}}
    <div class="col-xl-5 col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .26s both;">
            <div class="card-header d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded">
                    <i class="ph ph-chart-donut f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Sentiment Distribution</h6>
                    <small class="text-muted">Positive, neutral, and negative breakdown</small>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:320px;">
                    <div id="sentimentLoading" class="loading-skeleton" style="height:100%;"></div>
                    <canvas id="sentimentChart" style="display:none;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Most Active Users ══ --}}
<div class="row">
    <div class="col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .30s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-users f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Most Active Users</h6>
                        <small class="text-muted">Top users with highest posting frequency</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="table-search" style="width:220px;">
                        <div style="position:relative;">
                            <i class="ph ph-magnifying-glass" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--do-slate-400);font-size:14px;"></i>
                            <input type="text" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()" style="width:100%;padding:6px 10px 6px 30px;border:1px solid var(--do-slate-200);border-radius:var(--do-radius-sm);font-size:12px;outline:none;">
                        </div>
                    </div>
                    <span class="badge bg-light-primary text-primary" id="badgeUsers">Loading…</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="activeUsersLoading" class="text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-muted f-12">Memuat data users…</span>
                </div>
                <div id="activeUsersTable" style="display:none;overflow-x:auto;"></div>
            </div>
            <div id="viewAllContainer" class="card-footer text-center" style="display:none;">
                <button class="btn btn-outline-primary btn-sm" onclick="showAllUsersModal()">
                    <i class="ph ph-users me-1"></i> View All Users (<span id="remainingCount">0</span> more)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- All Users Modal --}}
<div class="all-users-modal" id="allUsersModal">
    <div class="modal-overlay" onclick="document.getElementById('allUsersModal').classList.remove('show')"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>All Active Users</h3>
            <button class="modal-close" onclick="document.getElementById('allUsersModal').classList.remove('show')">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="allUsersTableContent" style="overflow-x:auto;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  'use strict';

  const projectId = OV_PID;
  const startDate = OV_SD;
  const endDate   = OV_ED;

  if (!projectId || !startDate || !endDate) return;

  let allUsers = [];
  let displayedCount = 10;

  function fmtNum(n){ return new Intl.NumberFormat('en-US').format(n); }

  document.addEventListener('DOMContentLoaded', function(){
    loadVolumeTotal();
    loadSentimentTotal();
    loadTopHashtags();
    loadMostActiveUsers();
  });

  // ── Volume Total ──
  async function loadVolumeTotal(){
    try {
      const r = await fetch(`/mk/api/tiktok/volume-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const j = await r.json();
      if(j.success && j.data){
        document.getElementById('kpiVolume').textContent = fmtNum(j.data.total||0);
        renderVolumeTrendChart(j.data.chart||[]);
      }
    } catch(e){ console.error('Volume error:',e); document.getElementById('kpiVolume').textContent = '—'; }
  }

  // ── Sentiment Total ──
  async function loadSentimentTotal(){
    try {
      const r = await fetch(`/mk/api/tiktok/sentiment-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const j = await r.json();
      if(j.success && j.data){
        const pos = j.data.positive||0, neu = j.data.neutral||0, neg = j.data.negative||0;
        document.getElementById('kpiPositive').textContent = fmtNum(pos);
        document.getElementById('kpiNeutral').textContent  = fmtNum(neu);
        document.getElementById('kpiNegative').textContent = fmtNum(neg);
        renderSentimentChart({positive:pos, neutral:neu, negative:neg});
      }
    } catch(e){ console.error('Sentiment error:',e); }
  }

  // ── Top Hashtags ──
  async function loadTopHashtags(){
    const loading = document.getElementById('hashtagLoading');
    const content = document.getElementById('hashtagContent');
    const empty   = document.getElementById('hashtagEmpty');
    const badge   = document.getElementById('badgeHashtag');
    try {
      const r = await fetch(`/mk/api/tiktok/trending-topics?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const j = await r.json();
      if(j.success && j.data && j.data.hashtags && j.data.hashtags.length > 0){
        const hashtags = j.data.hashtags.slice(0, 30);
        badge.textContent = j.data.total_hashtags + ' hashtags';
        const list = document.getElementById('hashtagList');
        list.innerHTML = '';
        const maxSize = hashtags[0].size;
        hashtags.forEach((h, i)=>{
          const ratio = h.size / maxSize;
          let bg, color;
          if(i === 0){ bg = 'var(--bs-primary, #038047)'; color = '#fff'; }
          else if(ratio > 0.6){ bg = 'rgba(3,128,71,.15)'; color = '#038047'; }
          else if(ratio > 0.3){ bg = 'rgba(245,158,11,.12)'; color = '#92400e'; }
          else { bg = '#f1f5f9'; color = '#475569'; }
          const el = document.createElement('span');
          el.className = 'badge';
          el.style.cssText = `padding:6px 14px;border-radius:20px;font-size:${Math.max(11, 11 + ratio * 5)}px;font-weight:600;background:${bg};color:${color};cursor:default;transition:transform .12s;`;
          el.textContent = '#' + h.name + ' (' + h.size + ')';
          el.onmouseenter = function(){ this.style.transform='translateY(-2px)'; };
          el.onmouseleave = function(){ this.style.transform=''; };
          list.appendChild(el);
        });
        loading.style.display = 'none';
        content.style.display = 'block';
      } else {
        loading.style.display = 'none';
        empty.style.display = 'block';
        badge.textContent = '0';
      }
    } catch(e){
      console.error('Hashtag error:',e);
      loading.style.display = 'none';
      empty.style.display = 'block';
      badge.textContent = 'Error';
    }
  }

  // ── Most Active Users ──
  async function loadMostActiveUsers(){
    const badge = document.getElementById('badgeUsers');
    try {
      const r = await fetch(`/mk/api/tiktok/most-active-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const j = await r.json();
      if(j.success && j.data && j.data.data){
        allUsers = j.data.data;
        badge.textContent = allUsers.length + ' users';
        displayUsersTable(10);
        if(allUsers.length > 10){
          document.getElementById('viewAllContainer').style.display = '';
          document.getElementById('remainingCount').textContent = allUsers.length - 10;
        }
        document.getElementById('activeUsersLoading').style.display = 'none';
        document.getElementById('activeUsersTable').style.display = 'block';
      } else { badge.textContent = '0'; }
    } catch(e){ console.error('Users error:',e); badge.textContent = 'Error'; }
  }

  function buildUsersTableHTML(users){
    let h = `<table class="table table-hover mb-0" style="font-size:12px;">
      <thead><tr>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;letter-spacing:.3px;">NO.</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">AVATAR</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">USERNAME</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">DISPLAY NAME</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">ENGAGEMENT</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">POSTS</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">LIKES</th>
        <th style="font-size:10px;font-weight:700;color:var(--do-slate-500);text-transform:uppercase;">COMMENTS</th>
      </tr></thead><tbody>`;
    users.forEach((item,i)=>{
      const username   = item.username||item.author||item.name||'Unknown';
      const profileUrl = item.profile_url||item.profile_image_url||'';
      const dispName   = item.contentJson?.nickname||item.display_name||item.name||username;
      const likes      = item.likes||item.contentJson?.heart_count||0;
      const comments   = item.comments||0;
      const posts      = item.posts||item.y||0;
      const engagement = posts + likes + comments;
      h += `<tr>
        <td><strong>${i+1}</strong></td>
        <td>${profileUrl
          ? `<img src="${profileUrl}" alt="${username}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;" onerror="this.outerHTML='<div style=\\'width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#038047,#026738);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;\\'>${username.charAt(0).toUpperCase()}</div>'">`
          : `<div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#038047,#026738);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:12px;">${username.charAt(0).toUpperCase()}</div>`
        }</td>
        <td><a href="https://www.tiktok.com/@${encodeURIComponent(username)}" target="_blank" rel="noopener noreferrer" style="color:#ea580c;font-weight:500;text-decoration:none;">@${username}</a></td>
        <td style="font-weight:600;">${dispName}</td>
        <td><strong>${fmtNum(engagement)}</strong></td>
        <td>${fmtNum(posts)}</td>
        <td>${fmtNum(likes)}</td>
        <td>${fmtNum(comments)}</td>
      </tr>`;
    });
    h += '</tbody></table>';
    return h;
  }

  function displayUsersTable(count){
    const c = document.getElementById('activeUsersTable');
    c.innerHTML = buildUsersTableHTML(allUsers.slice(0, count));
    displayedCount = count;
  }

  window.showAllUsersModal = function(){
    const modal = document.getElementById('allUsersModal');
    document.getElementById('allUsersTableContent').innerHTML = buildUsersTableHTML(allUsers);
    modal.classList.add('show');
  };

  window.filterUsers = function(){
    const s = document.getElementById('userSearchInput').value.toLowerCase();
    if(!s){ displayUsersTable(displayedCount); return; }
    const f = allUsers.filter(u => {
      const un = (u.username||u.author||u.name||'').toLowerCase();
      const dn = (u.contentJson?.nickname||u.display_name||u.name||'').toLowerCase();
      return un.includes(s)||dn.includes(s);
    });
    const c = document.getElementById('activeUsersTable');
    c.innerHTML = f.length ? buildUsersTableHTML(f) : '<div class="text-center py-4 text-muted f-12">No users found</div>';
  };

  // ── Charts ──
  function renderVolumeTrendChart(data){
    const canvas = document.getElementById('volumeTrendChart');
    const loading = document.getElementById('volumeTrendLoading');
    if(!data||!data.length){ loading.innerHTML='<div class="text-center py-4 text-muted f-12">No data available</div>'; return; }
    new Chart(canvas.getContext('2d'),{
      type:'line',
      data:{ labels:data.map(d=>d.date), datasets:[{
        label:'Volume', data:data.map(d=>d.count||d.value||0),
        borderColor:'#038047', backgroundColor:'rgba(3,128,71,.1)',
        borderWidth:3, tension:.4, fill:true, pointRadius:4, pointHoverRadius:6,
        pointBackgroundColor:'#038047', pointBorderColor:'#fff', pointBorderWidth:2
      }]},
      options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'#1a202c', padding:12, titleColor:'#fff', bodyColor:'#fff', cornerRadius:8, displayColors:false }},
        scales:{ y:{ beginAtZero:true, grid:{color:'#f1f5f9',drawBorder:false}, ticks:{color:'#64748b',font:{family:'Poppins',size:11},padding:8}},
                 x:{ grid:{display:false,drawBorder:false}, ticks:{color:'#64748b',font:{family:'Poppins',size:11},padding:8}}}
      }
    });
    loading.style.display='none'; canvas.style.display='block';
  }

  function renderSentimentChart(s){
    const canvas = document.getElementById('sentimentChart');
    const loading = document.getElementById('sentimentLoading');
    new Chart(canvas.getContext('2d'),{
      type:'doughnut',
      data:{ labels:['Positive','Neutral','Negative'], datasets:[{
        data:[s.positive,s.neutral,s.negative],
        backgroundColor:['#10b981','#64748b','#ef4444'], borderWidth:0, hoverOffset:15
      }]},
      options:{ responsive:true, maintainAspectRatio:false, cutout:'70%',
        plugins:{ legend:{ position:'bottom', labels:{ color:'#1a202c', font:{family:'Poppins',size:12,weight:'600'}, padding:16, usePointStyle:true, pointStyle:'circle' }},
          tooltip:{ backgroundColor:'#1a202c', padding:12, titleColor:'#fff', bodyColor:'#fff', cornerRadius:8, displayColors:false,
            callbacks:{ label:function(ctx){ const t=ctx.dataset.data.reduce((a,b)=>a+b,0); return ctx.label+': '+fmtNum(ctx.parsed)+' ('+(t?(ctx.parsed*100/t).toFixed(1):0)+'%)'; }}
          }
        }
      }
    });
    loading.style.display='none'; canvas.style.display='block';
  }

})();
</script>
@endsection