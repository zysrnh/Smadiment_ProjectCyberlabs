@php
    $globalStartDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $globalEndDate   = request()->get('end_date', now()->format('Y-m-d'));
@endphp<style>
.notif-btn {
    width: 56px; height: 56px; border-radius: 50%; border: 3px solid #e2e8f0; background: #f1f5f9; display: flex; align-items: center; justify-content: center; position: relative;
    transition: all 0.2s ease-in-out;
    color: #475569;
}
.notif-btn:hover {
    background: #e2e8f0 !important;
    border-color: #cbd5e1 !important;
    color: #038047;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
</style>

<header class="pc-header">
    <div class="header-wrapper">

        {{-- [Mobile / Collapse Controls] --}}
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ph ph-list"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ph ph-list"></i>
                    </a>
                </li>
            </ul>
        </div>

        {{-- [Right Side Actions] --}}
        <div class="ms-auto">
            <ul class="list-unstyled">

                {{-- Date Range Picker Trigger – circle icon, same size as avatar --}}
                <li class="pc-h-item gdp-trigger-item d-none d-md-inline-flex" style="position:relative;z-index:50;">
                    <a href="javascript:void(0);"
                       id="gdpTrigger"
                       onclick="GDPicker.open(); return false;"
                       class="pc-head-link gdp-icon-btn"
                       title="{{ $globalStartDate }} – {{ $globalEndDate }}"
                       aria-label="Open date range picker">
                        <i class="ph ph-calendar-blank"></i>
                    </a>
                </li>

                {{-- Subscription Notifications --}}
                @php
                    $user = auth()->user();
                    $trialEnds = $user->trial_ends_at;
                    $noticeAt = $user->subscription_notice_at;
                    $remaining = $user->trialRemainingDays();
                    
                    $isExpiring = false;
                    $noticeMessage = '';
                    $noticeType = 'warning'; // warning or danger
                    
                    if ($trialEnds) {
                        // Determine if we should start showing warnings
                        $shouldShow = false;
                        if ($noticeAt) {
                            $shouldShow = now()->startOfDay()->greaterThanOrEqualTo(\Carbon\Carbon::parse($noticeAt)->startOfDay());
                        } else {
                            // Default behavior: start warning 7 days before
                            $shouldShow = $remaining <= 7;
                        }

                        // Set message and type based on actual remaining days
                        if ($shouldShow) {
                            $isExpiring = true;
                            if ($remaining <= 1) {
                                $noticeMessage = "Your trial expires <strong>tomorrow</strong>! Please renew to avoid service interruption.";
                                $noticeType = 'danger';
                            } elseif ($remaining <= 3) {
                                $noticeMessage = "Only <strong>$remaining days</strong> left in your trial. Renew now to stay active.";
                                $noticeType = 'danger';
                            } elseif ($remaining <= 7) {
                                $noticeMessage = "Your trial ends in <strong>1 week</strong>. Consider renewing your subscription.";
                                $noticeType = 'warning';
                            } else {
                                $noticeMessage = "Your trial ends in <strong>$remaining days</strong>. Plan your subscription renewal.";
                                $noticeType = 'warning';
                            }
                        }
                    }
                    
                    $hasNotification = $isExpiring;
                @endphp
                <li class="dropdown pc-h-item header-notification">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0 notif-btn" 
                       data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ph ph-bell fs-4"></i>
                        @if($hasNotification)
                            <span class="position-absolute top-0 start-100 translate-middle p-2 {{ $noticeType == 'danger' ? 'bg-danger' : 'bg-warning' }} border border-light rounded-circle" style="margin-top: 15px; margin-left: -15px;">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown p-0" style="width: 320px;">
                        <div class="dropdown-header d-flex align-items-center justify-content-between py-3 px-4 border-bottom">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            @if($hasNotification)
                                <span class="badge {{ $noticeType == 'danger' ? 'bg-light-danger text-danger' : 'bg-light-warning text-warning' }}">
                                    {{ $noticeType == 'danger' ? 'Urgent' : 'Reminder' }}
                                </span>
                            @endif
                        </div>
                        <div class="notification-list py-2">
                            @if($isExpiring)
                                <div class="dropdown-item d-flex align-items-start gap-3 py-3 px-4">
                                    <div class="{{ $noticeType == 'danger' ? 'bg-light-danger text-danger' : 'bg-light-warning text-warning' }} p-2 rounded-3">
                                        <i class="ph {{ $noticeType == 'danger' ? 'ph-warning-circle' : 'ph-bell-ringing' }} fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold" style="font-size: 14px;">Subscription Alert</h6>
                                        <p class="text-muted mb-0" style="font-size: 12px;">{!! $noticeMessage !!}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!$hasNotification)
                                <div class="text-center py-5">
                                    <i class="ph ph-bell-slash text-muted mb-2" style="font-size: 40px;"></i>
                                    <p class="text-muted small">No new notifications</p>
                                </div>
                            @endif
                        </div>
                        @if($hasNotification)
                            <div class="dropdown-footer border-top text-center py-2 bg-light bg-opacity-50">
                                <a href="{{ route('mk.profile') }}" class="text-primary fw-bold" style="font-size: 12px;">View Details</a>
                            </div>
                        @endif
                    </div>
                </li>

                {{-- User Profile --}}
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0"
                       data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        @if(auth()->check() && auth()->user()->avatar)
                            <img src="{{ asset(ltrim(auth()->user()->avatar, '/')) }}"
                                 alt="User Avatar"
                                 class="profile-avatar-img"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                            <i class="ph ph-user-circle fallback-icon" style="display:none;"></i>
                        @else
                            <i class="ph ph-user-circle fallback-icon"></i>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center gap-3">
                            @if(auth()->check() && auth()->user()->avatar)
                                <img src="{{ asset(ltrim(auth()->user()->avatar, '/')) }}"
                                     alt="Avatar"
                                     class="dropdown-avatar-img"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                                <i class="ph ph-user-circle" style="display:none; font-size:64px; color:#64748b;"></i>
                            @else
                                <i class="ph ph-user-circle" style="font-size:64px; color:#64748b;"></i>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                <small class="text-muted">{{ auth()->user()->email ?? 'admin@smadiment.com' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('mk.profile') }}" class="dropdown-item">
                            <i class="ph ph-user-circle"></i>
                            <span>Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item text-danger"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ph ph-sign-out"></i>
                            <span>Sign Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</header>

{{-- =====================================================================
     GLOBAL DATE PICKER MODAL
     ===================================================================== --}}
<div id="gdpModal" role="dialog" aria-modal="true" aria-label="Date Range Picker">
    <div id="gdpOverlay" style="position:absolute;inset:0;cursor:pointer;"></div>
    <div class="gdp-wrap">
        <div class="gdp-sidebar">
            <button type="button" class="gdp-preset" data-p="today">Today</button>
            <button type="button" class="gdp-preset" data-p="yesterday">Yesterday</button>
            <button type="button" class="gdp-preset" data-p="last7">Last 7 Days</button>
            <button type="button" class="gdp-preset" data-p="last30">Last 30 Days</button>
            <button type="button" class="gdp-preset" data-p="thismonth">This Month</button>
            <button type="button" class="gdp-preset" data-p="lastmonth">Last Month</button>
            <button type="button" class="gdp-preset" data-p="custom">Custom Range</button>
        </div>
        <div class="gdp-body">
            <div class="gdp-nav">
                <button type="button" class="gdp-nav-btn" id="gdpPrev" title="Previous month">
                    <i class="ph ph-caret-left"></i>
                </button>
                <div class="gdp-cals">
                    <div class="gdp-cal" id="gdpCal1"></div>
                    <div class="gdp-cal" id="gdpCal2"></div>
                </div>
                <button type="button" class="gdp-nav-btn" id="gdpNext" title="Next month">
                    <i class="ph ph-caret-right"></i>
                </button>
            </div>
            <div class="gdp-display" id="gdpDisplay">
                {{ $globalStartDate }} – {{ $globalEndDate }}
            </div>
            <div class="gdp-footer">
                <button type="button" class="btn btn-light btn-sm px-4" id="gdpCancel">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="gdpApply">Apply</button>
            </div>
        </div>
    </div>
</div>

<style>
/* ── GDP Trigger – circle icon (matches avatar) ── */
#gdpTrigger.gdp-icon-btn {
    width: 56px !important;
    height: 56px !important;
    border-radius: 50% !important;
    padding: 0 !important;
    border: 3px solid #e2e8f0 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,.12) !important;
    background: #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    overflow: hidden !important;
    opacity: 1 !important;
    cursor: pointer !important;
    will-change: transform, box-shadow;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1),
                border-color .2s,
                box-shadow .25s,
                filter .25s !important;
    text-decoration: none !important;
    color: inherit !important;
    pointer-events: auto !important;
}
#gdpTrigger.gdp-icon-btn i {
    font-size: 22px;
    color: #475569;
    pointer-events: none;
    transition: color .2s, transform .25s cubic-bezier(.34,1.56,.64,1);
}

/* Shimmer pseudo-element (sama seperti avatar) */
#gdpTrigger.gdp-icon-btn::before {
    content: '';
    position: absolute;
    top: 0; bottom: 0; left: -100%;
    width: 60%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.6), transparent);
    pointer-events: none;
    z-index: 10;
    transition: none;
}

/* Hover state */
.gdp-trigger-item:hover #gdpTrigger.gdp-icon-btn,
#gdpTrigger.gdp-icon-btn:hover {
    transform: translateY(-6px) scale(1.025) !important;
    border-color: var(--bs-primary, #0d6efd) !important;
    box-shadow: 0 16px 32px rgba(13,110,253,.3) !important;
    filter: brightness(1.05) !important;
}
.gdp-trigger-item:hover #gdpTrigger.gdp-icon-btn i,
#gdpTrigger.gdp-icon-btn:hover i {
    color: var(--bs-primary, #0d6efd);
    animation: gdpBounce .5s cubic-bezier(.34,1.56,.64,1) both;
}
.gdp-trigger-item:hover #gdpTrigger.gdp-icon-btn::before {
    animation: gdpShimmer .6s ease forwards;
}

/* Active press */
#gdpTrigger.gdp-icon-btn:active {
    transform: translateY(-2px) scale(1.01) !important;
    transition-duration: .08s !important;
}

/* ── Modal ── */
#gdpModal {
    position: fixed; inset: 0; z-index: 99999;
    display: none; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
}
#gdpModal.gdp-open { display: flex; }
.gdp-wrap {
    position: relative; background: #fff; border-radius: 16px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.25);
    display: flex; width: 90%; max-width: 840px; max-height: 90vh; overflow: hidden;
    animation: gdpIn .2s cubic-bezier(.34,1.2,.64,1) both;
}
@keyframes gdpIn {
    from { opacity:0; transform:scale(.95) translateY(8px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
@keyframes gdpShimmer { 100% { left: 150%; } }
@keyframes gdpBounce  { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

/* Sidebar */
.gdp-sidebar {
    width: 160px; flex-shrink: 0; background: #f8fafc;
    border-right: 1px solid #e2e8f0; padding: 14px 8px;
    display: flex; flex-direction: column; gap: 2px;
}
.gdp-preset {
    width: 100%; padding: 9px 12px; background: transparent; border: none;
    border-radius: 8px; font-size: 13px; font-weight: 500; color: #475569;
    text-align: left; cursor: pointer; transition: background .15s, color .15s; white-space: nowrap;
}
.gdp-preset:hover { background: #fff; color: var(--bs-primary, #0d6efd); }
.gdp-preset.active { background: var(--bs-primary, #0d6efd); color: #fff; }

/* Body */
.gdp-body {
    flex: 1; padding: 20px 22px; display: flex;
    flex-direction: column; gap: 14px; overflow: auto;
}
.gdp-nav { display: flex; align-items: flex-start; gap: 12px; }
.gdp-nav-btn {
    width: 34px; height: 34px; flex-shrink: 0; border-radius: 8px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 14px; color: #475569; transition: background .15s;
}
.gdp-nav-btn:hover { background: #e2e8f0; }
.gdp-cals { flex: 1; display: flex; gap: 18px; }
.gdp-cal  { flex: 1; min-width: 0; }

/* Calendar grid */
.gdp-cal-title { font-size:14px; font-weight:700; text-align:center; margin-bottom:10px; color:#1e293b; }
.gdp-weekdays  { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; margin-bottom:4px; }
.gdp-weekday   { text-align:center; font-size:11px; font-weight:700; color:#94a3b8; padding:4px 0; }
.gdp-days      { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
.gdp-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:12px; font-weight:500; border-radius:6px; cursor:pointer;
    transition:all .12s; border:none; background:transparent; color:#374151; padding:0;
}
.gdp-day:hover:not([disabled]):not(.gdp-other) { background:#f1f5f9; }
.gdp-day.gdp-other  { color:#d1d5db; cursor:default; pointer-events:none; }
.gdp-day[disabled]  { color:#e5e7eb; cursor:not-allowed; }
.gdp-day.gdp-today  { border:1.5px solid var(--bs-primary,#0d6efd); }
.gdp-day.gdp-sel    { background:var(--bs-primary,#0d6efd) !important; color:#fff !important; font-weight:600; }
.gdp-day.gdp-range  { background:rgba(13,110,253,.1); color:var(--bs-primary,#0d6efd); }

/* Display + footer */
.gdp-display {
    background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;
    padding:10px 16px; text-align:center; font-size:14px; font-weight:600; color:#1e293b;
}
.gdp-footer { display:flex; gap:10px; justify-content:flex-end; }

/* ── Avatar KPI ── */
.header-user-profile .pc-head-link,
.header-user-profile .pc-head-link.show,
.header-user-profile .pc-head-link[aria-expanded="true"] {
    width:56px !important; height:56px !important; border-radius:50% !important;
    padding:0 !important; border:3px solid #e2e8f0 !important;
    box-shadow:0 2px 10px rgba(0,0,0,.12) !important; background:#f1f5f9 !important;
    display:flex !important; align-items:center !important; justify-content:center !important;
    position:relative !important; overflow:hidden !important; opacity:1 !important;
    will-change:transform,box-shadow;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1),border-color .2s,box-shadow .25s,filter .25s !important;
}
.header-user-profile .pc-head-link::before {
    content:''; position:absolute; top:0; bottom:0; left:-100%;
    width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.6),transparent);
    pointer-events:none; z-index:10; transition:none;
}
.header-user-profile:hover .pc-head-link,
.header-user-profile.show .pc-head-link {
    transform:translateY(-6px) scale(1.025) !important;
    border-color:var(--bs-primary,#0d6efd) !important;
    box-shadow:0 16px 32px rgba(13,110,253,.3) !important;
    filter:brightness(1.05) !important;
}
.header-user-profile:hover .pc-head-link::before { animation:gdpShimmer .6s ease forwards; }
.header-user-profile:hover .profile-avatar-img,
.header-user-profile:hover .fallback-icon { animation:gdpBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; }
.header-user-profile:active .pc-head-link { transform:translateY(-2px) scale(1.01) !important; transition-duration:.08s !important; }
.header-user-profile .profile-avatar-img { width:100%; height:100%; object-fit:cover; display:block; border-radius:50%; }
.header-user-profile .fallback-icon { font-size:38px; color:#64748b; display:block; }
.header-user-profile .pc-head-link:hover .profile-avatar-img,
.header-user-profile .pc-head-link.show .profile-avatar-img,
.header-user-profile .pc-head-link[aria-expanded="true"] .profile-avatar-img { opacity:1 !important; visibility:visible !important; filter:none !important; }
.header-user-profile .pc-head-link:hover .fallback-icon,
.header-user-profile .pc-head-link.show .fallback-icon,
.header-user-profile .pc-head-link[aria-expanded="true"] .fallback-icon { color:#64748b !important; opacity:1 !important; visibility:visible !important; }

/* ── Avatar dropdown ── */
.dropdown-avatar-img {
    width:64px; height:64px; border-radius:50%; object-fit:cover;
    border:3px solid #e2e8f0; flex-shrink:0; background:#f1f5f9; box-shadow:0 2px 10px rgba(0,0,0,.10);
}
.pc-h-dropdown .dropdown-header { padding:16px 16px 14px; border-bottom:1px solid #f1f5f9; margin-bottom:4px; }
.pc-h-dropdown .dropdown-header h6 { font-size:14px; font-weight:600; line-height:1.3; margin-bottom:2px; }
.pc-h-dropdown .dropdown-header small { font-size:12px; color:#94a3b8; }
@media (min-width:992px) {
    .header-user-profile:hover .dropdown-menu { display:block !important; animation:gdpDropFade .22s ease forwards; }
}
@keyframes gdpDropFade { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

/* ── Page title ── */
.page-header h1,.page-header .page-title,h1.page-title,.page-title {
    font-size:1.4rem !important; font-weight:600 !important;
}

/* ── Mobile ── */
@media (max-width:640px) {
    .gdp-wrap { flex-direction:column; max-height:95vh; }
    .gdp-sidebar { width:100%; flex-direction:row; flex-wrap:wrap; border-right:none; border-bottom:1px solid #e2e8f0; padding:10px; }
    .gdp-preset { width:auto; }
    .gdp-cals { flex-direction:column; }
}
.bg-light-danger { background-color: rgba(220, 38, 38, 0.1) !important; color: #DC2626 !important; }
.bg-light-warning { background-color: rgba(217, 119, 6, 0.1) !important; color: #D97706 !important; }
.header-notification .pc-head-link:hover {
    transform: translateY(-6px) scale(1.025) !important;
    border-color: var(--bs-primary, #0d6efd) !important;
    box-shadow: 0 16px 32px rgba(13,110,253,.3) !important;
}
</style>

<script>
var GDPicker = (function () {
    'use strict';

    var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    var LS_S   = 'smadiment_g_start';
    var LS_E   = 'smadiment_g_end';

    var startDate, endDate, vm1, vm2;
    var picking = 'start';
    var isOpen  = false;

    var today = new Date();
    today.setHours(0,0,0,0);

    /* ── utils ── */
    function fmt(d) {
        if (!d || isNaN(d.getTime())) return '';
        return d.getFullYear() + '-'
             + String(d.getMonth()+1).padStart(2,'0') + '-'
             + String(d.getDate()).padStart(2,'0');
    }

    function parseLocal(s) {
        if (!s) return null;
        var p = s.split('-');
        if (p.length !== 3) return null;
        var d = new Date(+p[0], +p[1]-1, +p[2]);
        return isNaN(d.getTime()) ? null : d;
    }

    function sameDay(a, b) {
        return a && b
            && a.getFullYear() === b.getFullYear()
            && a.getMonth()    === b.getMonth()
            && a.getDate()     === b.getDate();
    }

    function clamp(d) {
        return (d && d > today) ? new Date(today) : d;
    }

    /* ── init ── */
    function initDates() {
        if (!sessionStorage.getItem('smad_sess_v3')) {
            sessionStorage.setItem('smad_sess_v3','1');
            localStorage.removeItem(LS_S);
            localStorage.removeItem(LS_E);
        }
        var params = new URLSearchParams(window.location.search);
        var us = params.get('start_date'), ue = params.get('end_date');
        if (us && ue) {
            startDate = parseLocal(us) || new Date(today.getFullYear(), today.getMonth(), 1);
            endDate   = parseLocal(ue) || new Date(today);
        } else {
            startDate = parseLocal(localStorage.getItem(LS_S)) || new Date(today.getFullYear(), today.getMonth(), 1);
            endDate   = parseLocal(localStorage.getItem(LS_E)) || new Date(today);
        }
        startDate = clamp(startDate);
        endDate   = clamp(endDate);
        if (startDate > endDate) endDate = new Date(startDate);
        localStorage.setItem(LS_S, fmt(startDate));
        localStorage.setItem(LS_E, fmt(endDate));
        vm1 = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
        vm2 = new Date(vm1.getFullYear(), vm1.getMonth()+1, 1);
    }

    /* ── move modal to <body> ── */
    function moveToBody() {
        var el = document.getElementById('gdpModal');
        if (el && el.parentNode !== document.body) {
            document.body.appendChild(el);
        }
    }

    /* ── render calendar ── */
    function renderCal(id, month) {
        var el = document.getElementById(id);
        if (!el) return;
        var y = month.getFullYear(), m = month.getMonth();
        var fd = new Date(y,m,1).getDay();
        var ld = new Date(y,m+1,0).getDate();
        var pl = new Date(y,m,0).getDate();
        var lw = new Date(y,m+1,0).getDay();
        var h  = '<div class="gdp-cal-title">' + MONTHS[m] + ' ' + y + '</div>';
        h += '<div class="gdp-weekdays">' + DAYS.map(function(d){ return '<div class="gdp-weekday">'+d+'</div>'; }).join('') + '</div>';
        h += '<div class="gdp-days">';
        for (var i=0;i<fd;i++) h += '<button class="gdp-day gdp-other" disabled>'+(pl-fd+1+i)+'</button>';
        for (var d=1;d<=ld;d++) {
            var date = new Date(y,m,d);
            var fut  = date > today;
            var cls  = '';
            if (sameDay(date,today)) cls += ' gdp-today';
            if (sameDay(date,startDate)||sameDay(date,endDate)) cls += ' gdp-sel';
            else if (startDate&&endDate&&date>startDate&&date<endDate) cls += ' gdp-range';
            h += '<button class="gdp-day'+cls+'" data-date="'+fmt(date)+'"'+(fut?' disabled':'')+'>'+d+'</button>';
        }
        var fill = lw===6 ? 0 : 6-lw;
        for (var j=1;j<=fill;j++) h += '<button class="gdp-day gdp-other" disabled>'+j+'</button>';
        h += '</div>';
        el.innerHTML = h;
        el.querySelectorAll('.gdp-day:not([disabled]):not(.gdp-other)').forEach(function(btn){
            btn.addEventListener('click', function(){
                var clicked = parseLocal(this.getAttribute('data-date'));
                if (!clicked) return;
                setActive('custom');
                if (picking==='start'||clicked<startDate) {
                    startDate=clicked; endDate=new Date(clicked); picking='end';
                } else { endDate=clicked; picking='start'; }
                renderAll();
            });
        });
    }

    function renderAll() {
        renderCal('gdpCal1', vm1);
        renderCal('gdpCal2', vm2);
        var lbl = fmt(startDate) + ' \u2013 ' + fmt(endDate);
        var d   = document.getElementById('gdpDisplay');
        /* Update trigger title attribute as tooltip */
        var t   = document.getElementById('gdpTrigger');
        if (d) d.textContent = lbl;
        if (t) t.setAttribute('title', lbl);
    }

    /* ── presets ── */
    function setActive(p) {
        document.querySelectorAll('.gdp-preset').forEach(function(b){
            b.classList.toggle('active', b.getAttribute('data-p')===p);
        });
    }

    function detectPreset() {
        var s=fmt(startDate),e=fmt(endDate),t=today;
        var yd=new Date(t); yd.setDate(t.getDate()-1);
        var l7=new Date(t); l7.setDate(t.getDate()-6);
        var l30=new Date(t); l30.setDate(t.getDate()-29);
        var lms=new Date(t.getFullYear(),t.getMonth()-1,1);
        var lme=new Date(t.getFullYear(),t.getMonth(),0);
        var tms=new Date(t.getFullYear(),t.getMonth(),1);
        if(s===fmt(t)  &&e===fmt(t))  return 'today';
        if(s===fmt(yd) &&e===fmt(yd)) return 'yesterday';
        if(s===fmt(l7) &&e===fmt(t))  return 'last7';
        if(s===fmt(l30)&&e===fmt(t))  return 'last30';
        if(s===fmt(tms)&&e===fmt(t))  return 'thismonth';
        if(s===fmt(lms)&&e===fmt(lme))return 'lastmonth';
        return 'custom';
    }

    function applyPreset(p) {
        var t=today;
        switch(p){
            case 'today':     startDate=new Date(t);endDate=new Date(t);break;
            case 'yesterday': startDate=new Date(t);startDate.setDate(t.getDate()-1);endDate=new Date(startDate);break;
            case 'last7':     endDate=new Date(t);startDate=new Date(t);startDate.setDate(t.getDate()-6);break;
            case 'last30':    endDate=new Date(t);startDate=new Date(t);startDate.setDate(t.getDate()-29);break;
            case 'thismonth': startDate=new Date(t.getFullYear(),t.getMonth(),1);endDate=new Date(t);break;
            case 'lastmonth': startDate=new Date(t.getFullYear(),t.getMonth()-1,1);endDate=new Date(t.getFullYear(),t.getMonth(),0);break;
            default: return;
        }
        vm1=new Date(startDate.getFullYear(),startDate.getMonth(),1);
        vm2=new Date(vm1.getFullYear(),vm1.getMonth()+1,1);
        renderAll();
    }

    /* ── open / close ── */
    function open() {
        if (isOpen) return;
        moveToBody();
        var m = document.getElementById('gdpModal');
        if (!m) return;
        isOpen  = true;
        picking = 'start';
        m.classList.add('gdp-open');
        renderAll();
        setActive(detectPreset());
    }

    function close() {
        var m = document.getElementById('gdpModal');
        if (m) m.classList.remove('gdp-open');
        isOpen  = false;
        picking = 'start';
    }

    function applyAndReload() {
        localStorage.setItem(LS_S, fmt(startDate));
        localStorage.setItem(LS_E, fmt(endDate));
        var url = new URL(window.location.href);
        url.searchParams.set('start_date', fmt(startDate));
        url.searchParams.set('end_date',   fmt(endDate));
        window.location.href = url.toString();
    }

    function updateTrigger() {
        var el = document.getElementById('gdpTrigger');
        if (el) el.setAttribute('title', fmt(startDate) + ' \u2013 ' + fmt(endDate));
    }

    /* ── link interceptor ── */
    function interceptLinks() {
        document.addEventListener('click', function(e){
            var link = e.target.closest('a[href]');
            if (!link) return;
            try {
                var url = new URL(link.href, window.location.origin);
                if (url.origin!==window.location.origin) return;
                var h = link.getAttribute('href')||'';
                if (/^javascript:/i.test(h)||h.startsWith('#')) return;
                if (/\.(png|jpg|jpeg|gif|svg|pdf|zip|csv|xlsx)$/i.test(url.pathname)) return;
                if (url.pathname.includes('/logout')||url.pathname.includes('/api/')) return;
                var gs=localStorage.getItem(LS_S), ge=localStorage.getItem(LS_E);
                if (gs&&ge){ url.searchParams.set('start_date',gs); url.searchParams.set('end_date',ge); link.href=url.toString(); }
            } catch(err){}
        }, true);
    }

    /* ── bind all modal events ── */
    function bindEvents() {
        document.addEventListener('click', function(e){
            if (e.target.closest('#gdpTrigger')) { e.preventDefault(); e.stopPropagation(); open(); }
        }, true);

        document.addEventListener('click', function(e){
            if (e.target.id==='gdpOverlay')            close();
            else if (e.target.id==='gdpCancel')        close();
            else if (e.target.id==='gdpApply')         applyAndReload();
            else if (e.target.closest('#gdpPrev')) {
                vm1=new Date(vm1.getFullYear(),vm1.getMonth()-1,1);
                vm2=new Date(vm2.getFullYear(),vm2.getMonth()-1,1);
                renderAll();
            } else if (e.target.closest('#gdpNext')) {
                vm1=new Date(vm1.getFullYear(),vm1.getMonth()+1,1);
                vm2=new Date(vm2.getFullYear(),vm2.getMonth()+1,1);
                renderAll();
            } else {
                var pb = e.target.closest('.gdp-preset');
                if (pb) { setActive(pb.getAttribute('data-p')); applyPreset(pb.getAttribute('data-p')); }
            }
        });

        document.addEventListener('keydown', function(e){
            if (e.key==='Escape'&&isOpen) close();
        });
    }

    function syncPage() {
        var urlHasDates = new URLSearchParams(window.location.search).has('start_date');
        if (!urlHasDates) {
            var sd=fmt(startDate), ed=fmt(endDate);
            var hsd=document.getElementById('hiddenStartDate');
            var hed=document.getElementById('hiddenEndDate');
            var dd =document.getElementById('doDateDisplay');
            var rt =document.getElementById('doDpRangeText');
            if (hsd) hsd.value=sd;
            if (hed) hed.value=ed;
            if (dd)  dd.textContent=sd+' \u2013 '+ed;
            if (rt)  rt.textContent=sd+' \u2013 '+ed;
        }
    }

    /* ── boot ── */
    initDates();
    updateTrigger();
    moveToBody();
    bindEvents();
    interceptLinks();

    function onReady() {
        setActive(detectPreset());
        updateTrigger();
        syncPage();
    }
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', onReady);
    else onReady();

    return { open: open, close: close };
})();
</script>