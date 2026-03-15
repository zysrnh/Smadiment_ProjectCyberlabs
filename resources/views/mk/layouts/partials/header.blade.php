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
                    {{-- Search dihapus --}}
                </ul>
            </div>

            {{-- [Right Side Actions] --}}
            <div class="ms-auto">
                <ul class="list-unstyled">

                    {{-- Date Range Picker Trigger --}}
                    @php
                        $globalStartDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
                        $globalEndDate   = request()->get('end_date', now()->format('Y-m-d'));
                    @endphp
                    <li class="pc-h-item d-none d-md-inline-flex">
                        <button type="button" class="pc-head-link border-0 bg-transparent" id="gdpTrigger"
                                style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;">
                            <i class="ph ph-calendar-blank"></i>
                            <span id="gdpTriggerLabel">{{ $globalStartDate }} – {{ $globalEndDate }}</span>
                            <i class="ph ph-caret-down" style="font-size:11px;opacity:0.6;"></i>
                        </button>
                    </li>


                    
                    {{-- 
                    <li class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" aria-expanded="false">
                            <i class="ph ph-sun-dim"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                                <i class="ph ph-moon"></i>
                                <span>Dark</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                                <i class="ph ph-sun"></i>
                                <span>Light</span>
                            </a>
                            <a href="#!" class="dropdown-item" onclick="layout_change_default()">
                                <i class="ph ph-cpu"></i>
                                <span>Default</span>
                            </a>
                        </div>
                    </li>
  --}}
                     
                    {{-- Notifications dihapus --}}

                    {{-- User Profile --}}
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown" href="#" role="button"
                        aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                            <i class="ph ph-user-circle"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <h6 class="mb-0">{{ auth()->user()->name ?? 'Admin' }}</h6>
                                <small class="text-muted">{{ auth()->user()->email ?? 'admin@smadiment.com' }}</small>
                            </div>
                            <a href="#!" class="dropdown-item">
                                <i class="ph ph-user-circle"></i>
                                <span>Profile</span>
                            </a>
                            {{-- Settings dihapus --}}
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-danger"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ph ph-sign-out"></i>
                                <span>Sign Out</span>
                            </a>
                            <form id="logout-form" action="/logout" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </header>

    {{-- ── Date Picker Modal ── --}}
    <div class="gdp-modal" id="gdpModal" style="position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div style="position:absolute;inset:0;cursor:pointer;" id="gdpOverlay"></div>
        <div class="gdp-container" style="position:relative;background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);display:flex;max-width:860px;width:90%;max-height:90vh;z-index:10000;">
            <div class="gdp-sidebar" style="width:165px;background:#f8fafc;border-right:1px solid #e2e8f0;padding:16px 10px;border-radius:16px 0 0 16px;display:flex;flex-direction:column;gap:3px;flex-shrink:0;">
                <button type="button" class="gdp-preset" data-preset="today">Today</button>
                <button type="button" class="gdp-preset" data-preset="yesterday">Yesterday</button>
                <button type="button" class="gdp-preset" data-preset="last7">Last 7 Days</button>
                <button type="button" class="gdp-preset" data-preset="last30">Last 30 Days</button>
                <button type="button" class="gdp-preset active" data-preset="thismonth">This Month</button>
                <button type="button" class="gdp-preset" data-preset="lastmonth">Last Month</button>
                <button type="button" class="gdp-preset" data-preset="custom">Custom Range</button>
            </div>
            <div class="gdp-content" style="flex:1;padding:22px 24px;display:flex;flex-direction:column;gap:16px;overflow:hidden;">
                <div style="display:flex;align-items:flex-start;gap:16px;">
                    <button type="button" id="gdpPrev" style="width:34px;height:34px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                        <i class="ph ph-caret-left"></i>
                    </button>
                    <div style="display:flex;gap:20px;flex:1;">
                        <div id="gdpCal1" style="flex:1;min-width:0;"></div>
                        <div id="gdpCal2" style="flex:1;min-width:0;"></div>
                    </div>
                    <button type="button" id="gdpNext" style="width:34px;height:34px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
                        <i class="ph ph-caret-right"></i>
                    </button>
                </div>
                <div id="gdpDisplay" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:11px 16px;text-align:center;font-size:14px;font-weight:600;">
                    {{ $globalStartDate }} – {{ $globalEndDate }}
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" id="gdpCancel" class="btn btn-light">Cancel</button>
                    <button type="button" id="gdpApply" class="btn btn-primary">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .gdp-preset {
        padding: 9px 14px; background: transparent; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 500; color: var(--bs-body-color);
        text-align: left; cursor: pointer; transition: all 0.18s; width: 100%;
    }
    .gdp-preset:hover { background: #fff; color: var(--bs-primary); }
    .gdp-preset.active { background: var(--bs-primary); color: #fff; }
    .gdp-cal-month { font-size: 15px; font-weight: 700; text-align: center; margin-bottom: 12px; }
    .gdp-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; margin-bottom: 6px; }
    .gdp-weekday { text-align: center; font-size: 11px; font-weight: 700; color: #64748b; padding: 6px 0; }
    .gdp-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }
    .gdp-day {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500; border-radius: 7px; cursor: pointer;
        transition: all 0.15s; border: none; background: transparent;
    }
    .gdp-day:hover:not([disabled]):not(.gdp-other) { background: #f1f5f9; }
    .gdp-day.gdp-other { color: #cbd5e1; cursor: default; pointer-events: none; }
    .gdp-day[disabled] { color: #e2e8f0; cursor: not-allowed; }
    .gdp-day.gdp-today { border: 2px solid var(--bs-primary); }
    .gdp-day.gdp-sel { background: var(--bs-primary); color: #fff; }
    .gdp-day.gdp-range { background: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); }

    /* Perkecil ukuran judul halaman */
    .page-header h1,
    .page-header .page-title,
    h1.page-title,
    .page-title {
        font-size: 1.4rem !important;
        font-weight: 600 !important;
    }
    </style>

    <script>
    (function() {
        const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        let startDate = null, endDate = null, viewMonth1, viewMonth2, picking = 'start';
        const params = new URLSearchParams(window.location.search);
        const today  = new Date(); today.setHours(0,0,0,0);

        function parseOrDefault(str, fallback) {
            const d = new Date(str); return isNaN(d) ? fallback : d;
        }

        const initStart = params.get('start_date');
        const initEnd   = params.get('end_date');
        if (initStart && initEnd) {
            startDate = parseOrDefault(initStart, new Date(today.getFullYear(), today.getMonth(), 1));
            endDate   = parseOrDefault(initEnd, today);
        } else {
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate   = new Date(today);
        }
        viewMonth1 = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
        viewMonth2 = new Date(viewMonth1.getFullYear(), viewMonth1.getMonth() + 1, 1);

        function fmt(d) {
            if (!d) return '';
            return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
        }
        function sameDay(a, b) {
            return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
        }

        function renderAll() {
            renderCal('gdpCal1', viewMonth1);
            renderCal('gdpCal2', viewMonth2);
            const label = fmt(startDate) + ' – ' + fmt(endDate);
            document.getElementById('gdpDisplay').textContent = label;
            const triggerLabel = document.getElementById('gdpTriggerLabel');
            if (triggerLabel) triggerLabel.textContent = label;
        }

        function renderCal(id, month) {
            const el = document.getElementById(id);
            if (!el) return;
            const y = month.getFullYear(), m = month.getMonth();
            const first = new Date(y, m, 1).getDay();
            const lastD = new Date(y, m+1, 0).getDate();
            const prevLast = new Date(y, m, 0).getDate();

            let html = `<div class="gdp-cal-month">${MONTHS[m]} ${y}</div>`;
            html += `<div class="gdp-weekdays">${DAYS.map(d=>`<div class="gdp-weekday">${d}</div>`).join('')}</div>`;
            html += `<div class="gdp-days">`;
            for (let i=0; i<first; i++) html += `<button class="gdp-day gdp-other" disabled>${prevLast - first + 1 + i}</button>`;
            for (let d=1; d<=lastD; d++) {
                const date = new Date(y, m, d);
                let cls = '';
                if (sameDay(date, today)) cls += ' gdp-today';
                if (sameDay(date, startDate) || sameDay(date, endDate)) cls += ' gdp-sel';
                else if (startDate && endDate && date > startDate && date < endDate) cls += ' gdp-range';
                html += `<button class="gdp-day${cls}" data-date="${fmt(date)}" ${date > today ? 'disabled' : ''}>${d}</button>`;
            }
            const lastDow = new Date(y, m+1, 0).getDay();
            for (let i=1; i < (lastDow===6 ? 0 : 7-lastDow); i++) html += `<button class="gdp-day gdp-other" disabled>${i}</button>`;
            html += `</div>`;
            el.innerHTML = html;

            el.querySelectorAll('.gdp-day:not([disabled]):not(.gdp-other)').forEach(btn => {
                btn.addEventListener('click', function() {
                    const clicked = new Date(this.dataset.date); clicked.setHours(0,0,0,0);
                    document.querySelectorAll('.gdp-preset').forEach(b => b.classList.remove('active'));
                    document.querySelector('[data-preset="custom"]').classList.add('active');
                    if (picking === 'start' || clicked < startDate) {
                        startDate = clicked; endDate = new Date(clicked); picking = 'end';
                    } else { endDate = clicked; picking = 'start'; }
                    renderAll();
                });
            });
        }

        function applyPreset(preset) {
            const t = new Date(today);
            switch(preset) {
                case 'today':     startDate = new Date(t); endDate = new Date(t); break;
                case 'yesterday': startDate = new Date(t); startDate.setDate(t.getDate()-1); endDate = new Date(startDate); break;
                case 'last7':     endDate = new Date(t); startDate = new Date(t); startDate.setDate(t.getDate()-6); break;
                case 'last30':    endDate = new Date(t); startDate = new Date(t); startDate.setDate(t.getDate()-29); break;
                case 'thismonth': startDate = new Date(t.getFullYear(), t.getMonth(), 1); endDate = new Date(t); break;
                case 'lastmonth': startDate = new Date(t.getFullYear(), t.getMonth()-1, 1); endDate = new Date(t.getFullYear(), t.getMonth(), 0); break;
            }
            if (preset !== 'custom') {
                viewMonth1 = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
                viewMonth2 = new Date(viewMonth1.getFullYear(), viewMonth1.getMonth()+1, 1);
                renderAll();
            }
        }

        function openModal()  { const m = document.getElementById('gdpModal'); m.classList.add('show'); renderAll(); }
        function closeModal() { document.getElementById('gdpModal').classList.remove('show'); }

        function applyAndReload() {
            const url = new URL(window.location.href);
            url.searchParams.set('start_date', fmt(startDate));
            url.searchParams.set('end_date', fmt(endDate));
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const trigger = document.getElementById('gdpTrigger');
            if (trigger) trigger.addEventListener('click', openModal);
            document.getElementById('gdpOverlay').addEventListener('click', closeModal);
            document.getElementById('gdpCancel').addEventListener('click', closeModal);
            document.getElementById('gdpApply').addEventListener('click', applyAndReload);
            document.getElementById('gdpPrev').addEventListener('click', function() {
                viewMonth1 = new Date(viewMonth1.getFullYear(), viewMonth1.getMonth()-1, 1);
                viewMonth2 = new Date(viewMonth2.getFullYear(), viewMonth2.getMonth()-1, 1);
                renderAll();
            });
            document.getElementById('gdpNext').addEventListener('click', function() {
                viewMonth1 = new Date(viewMonth1.getFullYear(), viewMonth1.getMonth()+1, 1);
                viewMonth2 = new Date(viewMonth2.getFullYear(), viewMonth2.getMonth()+1, 1);
                renderAll();
            });
            document.querySelectorAll('.gdp-preset').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.gdp-preset').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    applyPreset(this.dataset.preset);
                });
            });
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

            const trigLabel = document.getElementById('gdpTriggerLabel');
            if (trigLabel) trigLabel.textContent = fmt(startDate) + ' – ' + fmt(endDate);
        });
    })();
    </script>