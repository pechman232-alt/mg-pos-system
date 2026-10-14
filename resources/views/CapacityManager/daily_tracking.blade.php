@extends('layouts.homePages')

@section('content')

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    /* 🌟 ប្តូរពណ៌ទៅជាទម្រង់ Clean & Light (Breeze Style) 🌟 */
    .capacity-board {
        --primary: #4F46E5; /* Indigo 600 */
        --primary-light: #EEF2FF;
        --success: #10B981; /* Emerald 500 */
        --success-light: #D1FAE5;
        --danger: #EF4444; /* Red 500 */
        --danger-light: #FEE2E2;
        --warning: #F59E0B; /* Amber 500 */
        --warning-light: #FEF3C7;
        
        --ink: #111827; /* Gray 900 */
        --steel: #6B7280; /* Gray 500 */
        --steel-soft: #9CA3AF; /* Gray 400 */
        
        --paper: #F9FAFB; /* Gray 50 */
        --card: #FFFFFF;
        --line: #E5E7EB; /* Gray 200 */
        --line-strong: #D1D5DB; /* Gray 300 */
        
        --font-display: 'Inter', 'Battambang', sans-serif;
        --font-body: 'Inter', 'Battambang', sans-serif;
        --font-mono: 'IBM Plex Mono', 'Battambang', monospace;
        
        font-family: var(--font-body);
        color: var(--ink);
    }

    .capacity-board h3, .capacity-board h4 { font-family: var(--font-display); }

    /* Header */
    .cb-eyebrow {
        font-family: var(--font-display); font-size: 11px; font-weight: 600; text-transform: uppercase;
        color: var(--steel); margin-bottom: 3px; display: flex; align-items: center; gap: 8px;
    }
    .capacity-board .btn-back {
        border: 1px solid var(--line-strong); border-radius: 6px; background: var(--card);
        color: var(--ink); font-family: var(--font-body); font-size: 13px; font-weight: 600;
        padding: 8px 14px; transition: all .15s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .capacity-board .btn-back:hover { border-color: var(--steel-soft); background: var(--paper); }
    .capacity-board .filter-select, .capacity-board .filter-date {
        border: 1px solid var(--line-strong); border-radius: 6px; font-family: var(--font-body);
        font-size: 13px; font-weight: 500; color: var(--ink); box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .capacity-board .filter-select:focus, .capacity-board .filter-date:focus {
        border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.10); outline: none;
    }

    /* Gauge cluster (summary strip) - Clean White Version */
    .gauge-cluster {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex; flex-wrap: wrap;
    }
    .gauge-cell {
        flex: 1 1 0; min-width: 150px;
        padding: 20px;
        text-align: center;
    }
    .gauge-divider { width: 1px; background: var(--line); margin: 20px 0; flex: 0 0 1px; }
    .gauge-label {
        font-family: var(--font-display); font-size: 11px; font-weight: 600; text-transform: uppercase;
        color: var(--steel); margin-bottom: 12px;
    }
    .gauge-value { display: flex; align-items: baseline; justify-content: center; gap: 8px; }
    .gauge-num { font-family: var(--font-body); font-weight: 700; font-size: 24px; color: var(--ink); line-height: 1; }
    .gauge-num small { font-size: 13px; color: var(--steel-soft); font-weight: 500; margin-left: 2px; }
    
    .gauge-chip {
        font-family: var(--font-body); font-size: 12px; font-weight: 600;
        background: var(--paper); border: 1px solid var(--line);
        color: var(--steel); padding: 4px 10px; border-radius: 6px;
    }
    .gauge-sub { margin-top: 10px; }

    /* Utilization dial - Clean colors */
    .gauge-cell--hero { flex: 1.5 1 0; min-width: 190px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
    .dial-wrap { position: relative; width: 152px; max-width: 100%; margin-top: 2px; }
    .dial-svg { width: 100%; height: auto; display: block; overflow: visible; }
    .dial-track { fill: none; stroke: var(--paper); stroke-width: 11; stroke-linecap: round; }
    .dial-progress { fill: none; stroke-width: 11; stroke-linecap: round; transition: stroke-dashoffset .7s cubic-bezier(.4,0,.2,1); }
    .dial-progress.ok { stroke: var(--success); }
    .dial-progress.warn { stroke: var(--warning); }
    .dial-progress.hot { stroke: var(--danger); }
    .dial-tick { fill: var(--font-body); font-family: var(--font-body); font-weight: 600; font-size: 8px; fill: var(--steel-soft); }
    .dial-readout { position: absolute; left: 0; right: 0; bottom: 4px; text-align: center; }
    .dial-pct { font-family: var(--font-body); font-weight: 700; font-size: 26px; color: var(--ink); line-height: 1; }
    .dial-pct small { font-size: 14px; color: var(--steel); font-weight: 500; margin-left: 2px; }
    .dial-sub { font-family: var(--font-body); font-size: 11px; font-weight: 500; color: var(--steel); margin-top: 4px; }

    @media (max-width: 991px) {
        .gauge-cluster { flex-direction: column; }
        .gauge-divider { display: none; }
        .gauge-cell { border-bottom: 1px solid var(--line); }
        .gauge-cell:last-child { border-bottom: none; }
    }

    /* Slot cards */
    .capacity-board .slot-card { border: 1px solid var(--line); border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.03); background: var(--card); }
    .capacity-board .slot-card.is-closed { background: var(--paper); border-color: var(--line); opacity: 0.9; }
    .slot-card-header {
        background: #FAFAFA; border-bottom: 1px solid var(--line); padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;
    }
    .slot-time { font-family: var(--font-body); font-weight: 700; font-size: 1.1rem; }
    .slot-time.is-open { color: var(--ink); }
    .slot-time.is-closed { color: var(--steel-soft); }

    /* Badges */
    .status-pill { font-family: var(--font-body); font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .capacity-board .badge.status-pill.bg-secondary { background-color: var(--line) !important; color: var(--steel) !important; }
    .capacity-board .badge.status-pill.bg-danger { background-color: var(--danger-light) !important; color: var(--danger) !important; }
    .capacity-board .badge.status-pill.bg-success { background-color: var(--success-light) !important; color: var(--success) !important; }

    .cb-gauge-bar-wrap { font-size: 12px; }
    .cb-gauge-bar-wrap .label-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-family: var(--font-body); font-size: 11px; font-weight: 600; }
    .cb-gauge-bar-wrap .label-row .lbl { color: var(--steel); text-transform: uppercase; }
    .cb-gauge-bar-wrap .label-row .val { font-weight: 700; color: var(--ink); }
    .capacity-board .progress { background: var(--line); border-radius: 20px; box-shadow: none; }
    .capacity-board .progress-bar.gauge-bar.bg-primary { background-color: var(--primary) !important; }
    .capacity-board .progress-bar.gauge-bar.bg-danger { background-color: var(--danger) !important; }
    .capacity-board .progress-bar.gauge-bar.bg-success { background-color: var(--success) !important; }

    .stat-box { padding: 8px; border-radius: 6px; text-align: center; }
    .stat-box .stat-label { font-family: var(--font-body); font-size: 11px; font-weight: 600; margin-bottom: 2px; }
    .stat-box .stat-value { font-family: var(--font-body); font-weight: 700; font-size: 18px; }
    .stat-box.stat-online { background: #F0F9FF; border: 1px solid #BAE6FD; }
    .stat-box.stat-online .stat-label, .stat-box.stat-online .stat-value { color: #0284C7; }
    .stat-box.stat-walkin { background: var(--success-light); border: 1px solid #A7F3D0; }
    .stat-box.stat-walkin .stat-label, .stat-box.stat-walkin .stat-value { color: var(--success); }

    .cb-vehicle-title { font-family: var(--font-body); font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--steel); border-bottom: 1px solid var(--line); padding-bottom: 8px; }
    .booking-row { cursor: pointer; padding: 10px 8px; border-radius: 6px; transition: background .15s ease; border-bottom: 1px solid var(--paper); }
    .booking-row:hover { background: var(--paper); }
    .booking-row:last-child { border-bottom: none; }
    .plate-mono { font-family: var(--font-body); font-weight: 700; font-size: 13px; color: var(--ink); }
    
    .capacity-board .badge.mini-badge { padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 10px; }
    .capacity-board .badge.mini-badge.bg-success { background-color: var(--success-light) !important; color: var(--success) !important; }
    .capacity-board .badge.mini-badge.bg-info { background-color: #E0F2FE !important; color: #0284C7 !important; }

    .cb-empty { text-align: center; color: var(--steel-soft); font-size: 13px; font-weight: 500; padding: 20px 0; }
</style>

<div class="capacity-board container-fluid py-4">

    {{-- Back button and title --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('services.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
            <div>
                <h3 class="m-0 fw-bold" style="font-size: 1.25rem;"><i class="fas fa-tasks text-primary me-2"></i> Capacity Board</h3>
                <p class="text-muted m-0" style="font-size: 13px; font-weight: 500;">គ្រប់គ្រងកូតា Walk-in & Online Booking (Sync ផ្ទាល់)</p>
            </div>
        </div>

        <form method="GET" action="{{ route('capacity.daily_tracking') }}" class="d-flex gap-2">
            @if($isGlobalManager)
            <select name="branch" class="form-select filter-select" onchange="this.form.submit()">
                @foreach($allBranches as $b)
                    <option value="{{ $b->id }}" {{ $targetBranchId == $b->id ? 'selected' : '' }}>
                        {{ str_ireplace('MG ', '', $b->branch_name) }}
                    </option>
                @endforeach
            </select>
            @endif
            <input type="date" name="date" class="form-control filter-date" value="{{ $selectedDate }}" onchange="this.form.submit()">
        </form>
    </div>

    {{-- Instrument cluster: daily summary (CLEAN THEME) --}}
    @php
        $utilTarget = $maxCapacityHours;
        $utilPct = $utilTarget > 0 ? round(($dailyTotals['total_booked_hours'] / $utilTarget) * 100) : 0;
        $utilPctClamped = max(0, min(100, $utilPct));
        $utilState = $utilPct >= 100 ? 'hot' : ($utilPct >= 75 ? 'warn' : 'ok');
        $dialPathLength = 100;
        $dialOffset = $dialPathLength - (($dialPathLength / 100) * $utilPctClamped);
    @endphp
    <div class="gauge-cluster mb-5">
        <div class="gauge-cell gauge-cell--hero">
            <div class="gauge-label">Hours Utilized</div>
            <div class="dial-wrap">
                <svg viewBox="0 0 140 82" class="dial-svg">
                    <path class="dial-track" pathLength="100" d="M15,72 A55,55 0 0 1 125,72"></path>
                    <path class="dial-progress {{ $utilState }}" pathLength="100" stroke-dasharray="100" style="stroke-dashoffset: {{ $dialOffset }};" d="M15,72 A55,55 0 0 1 125,72"></path>
                    <text x="15" y="80" class="dial-tick" text-anchor="middle">0</text>
                    <text x="125" y="80" class="dial-tick" text-anchor="middle">{{ $maxCapacityHours }}h</text>
                </svg>
                <div class="dial-readout">
                    <div class="dial-pct">{{ $utilPct }}<small>%</small></div>
                    <div class="dial-sub">{{ $dailyTotals['total_booked_hours'] }}h / {{ $maxCapacityHours }}h</div>
                </div>
            </div>
        </div>

        <div class="gauge-divider"></div>

        <div class="gauge-cell">
            <div class="gauge-label">Technicians / Hours</div>
            <div class="gauge-value">
                <span class="gauge-chip"><i class="fas fa-users text-muted me-1"></i> {{ $techQty }}</span>
                <span class="gauge-num">{{ $maxCapacityHours }}<small>h</small></span>
            </div>
        </div>

        <div class="gauge-divider"></div>

        <div class="gauge-cell">
            <div class="gauge-label">Booked Hours</div>
            <div class="gauge-num" style="color: var(--primary);">{{ $dailyTotals['total_booked_hours'] }}<small>h</small></div>
            @php $remHrs = max(0, $maxCapacityHours - $dailyTotals['total_booked_hours']); @endphp
            <div class="gauge-sub"><span class="gauge-chip border-0 text-muted" style="font-size: 11px;">Remaining {{ $remHrs }}h</span></div>
        </div>

        <div class="gauge-divider"></div>

        <div class="gauge-cell">
            <div class="gauge-label">Total Bay Used</div>
            <div class="gauge-num">{{ $dailyTotals['total_booked'] }}<small>/ {{ $dailyTotals['total_bay_quota'] }}</small></div>
        </div>

        <div class="gauge-divider"></div>

        <div class="gauge-cell">
            <div class="gauge-label">Walk-In / Online</div>
            <div class="d-flex justify-content-center gap-2 mt-2">
                <span class="badge text-success bg-success-light border">W: {{ $dailyTotals['walkin_booked'] }}</span>
                <span class="badge text-primary bg-primary-light border">O: {{ $dailyTotals['online_booked'] }}</span>
            </div>
        </div>
    </div>

    {{-- Time slot cards (Using Bootstrap Grid) --}}
    <div class="row g-4">
        @foreach($slotData as $time => $slot)
        <div class="col-md-6 col-xl-3">
            <div class="card slot-card h-100 border-0 {{ $slot['is_closed'] ? 'is-closed' : '' }}">

                <div class="slot-card-header">
                    <h4 class="m-0 slot-time {{ $slot['is_closed'] ? 'is-closed' : 'is-open' }}">
                        <i class="far fa-clock"></i> {{ $slot['display_time'] }}
                    </h4>
                    @if($slot['is_closed'])
                        <span class="badge status-pill bg-secondary border">បិទសម្រាក</span>
                    @elseif($slot['booked_total'] >= $slot['quota_total'])
                        <span class="badge status-pill bg-danger">Full Bay</span>
                    @else
                        <span class="badge status-pill bg-success">Available</span>
                    @endif
                </div>

                <div class="card-body">
                    @if($slot['is_closed'])
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-ban fs-2 mb-3 opacity-25"></i>
                            <p class="m-0 fw-bold" style="font-size: 13px;">មិនមានទទួលសេវាកម្មទេ</p>
                        </div>
                    @else
                        {{-- Total bay gauge --}}
                        <div class="cb-gauge-bar-wrap mb-3">
                            <div class="label-row"><span class="lbl">Total Bay Used</span><span class="val">{{ $slot['booked_total'] }} / {{ $slot['quota_total'] }}</span></div>
                            <div class="progress" style="height: 6px;">
                                @php $pct = ($slot['quota_total'] > 0) ? ($slot['booked_total'] / $slot['quota_total']) * 100 : 0; @endphp
                                <div class="progress-bar gauge-bar bg-{{ $pct >= 100 ? 'danger' : 'primary' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Walk-in gauge --}}
                        <div class="cb-gauge-bar-wrap mb-4">
                            <div class="label-row"><span class="lbl">Walk-In Quota</span><span class="val" style="color: var(--success);">{{ $slot['booked_walkin'] }} / {{ $slot['quota_walkin'] }}</span></div>
                            <div class="progress" style="height: 6px;">
                                @php $wPct = ($slot['quota_walkin'] > 0) ? ($slot['booked_walkin'] / $slot['quota_walkin']) * 100 : 0; @endphp
                                <div class="progress-bar gauge-bar bg-success" style="width: {{ $wPct }}%"></div>
                            </div>
                        </div>

                        {{-- Online / Walk-in counts --}}
                        <div class="row text-center mb-4 g-2">
                            <div class="col-6">
                                <div class="stat-box stat-online">
                                    <div class="stat-label">Online</div>
                                    <div class="stat-value">{{ $slot['booked_online'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box stat-walkin">
                                    <div class="stat-label">Walk-In</div>
                                    <div class="stat-value">{{ $slot['booked_walkin'] }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Vehicle list --}}
                        <div>
                            <h6 class="cb-vehicle-title mb-0">Vehicle List</h6>
                            @if(count($slot['bookings']) > 0)
                                <ul class="list-unstyled m-0 mt-2">
                                    @foreach($slot['bookings'] as $b)
                                        <li class="d-flex justify-content-between align-items-center booking-row"
                                            onclick="showTicketDetails({
                                                id: '{{ $b->id }}',
                                                name: '{{ htmlspecialchars($b->customer_name ?? '') }}',
                                                phone: '{{ htmlspecialchars($b->contact ?? '') }}',
                                                plate: '{{ $b->plate }}',
                                                model: '{{ htmlspecialchars($b->model ?? '') }}',
                                                service: '{{ htmlspecialchars($b->service_type ?? '') }}',
                                                duration: '{{ $b->est_duration }}',
                                                date: '{{ \Carbon\Carbon::parse($b->booking_date)->format('d-M-Y') }}',
                                                time: '{{ \Carbon\Carbon::parse($b->booking_time)->format('h:i A') }}',
                                                branch: '{{ htmlspecialchars($b->location ?? '') }}',
                                                remark: '{{ preg_replace('/\r|\n/', ' ', htmlspecialchars($b->remark ?? '')) }}',
                                                type: '{{ $b->is_walk_in ? 'Walk-In' : 'Online Booking' }}'
                                            })">

                                            <div style="line-height: 1.3;">
                                                <span class="plate-mono">{{ $b->plate }}</span>
                                                <span class="text-muted d-block" style="font-size: 11px; font-weight: 500;">
                                                    <i class="far fa-clock"></i> {{ $b->est_duration }}h · {{ \Illuminate\Support\Str::limit($b->model, 15) }}
                                                </span>
                                            </div>
                                            <span class="badge mini-badge {{ $b->is_walk_in ? 'bg-success' : 'bg-info' }}">
                                                {{ $b->is_walk_in ? 'Walk-In' : 'Online' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="cb-empty">No vehicles booked yet.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Ticket pop-up (Clean Theme) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showTicketDetails(data) {
        const typeBg = data.type === 'Walk-In' ? '#D1FAE5' : '#E0F2FE';
        const typeColor = data.type === 'Walk-In' ? '#059669' : '#0284C7';
        
        const ticketHtml = `
            <div style="text-align: left; font-family: 'Inter', 'Battambang', sans-serif; border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                
                <div style="background: #F9FAFB; padding: 20px; text-align: center; border-bottom: 1px solid #E5E7EB;">
                    <div style="width:48px;height:48px;background:#EEF2FF;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-ticket-alt" style="color:#4F46E5;font-size:1.25rem;"></i>
                    </div>
                    <div style="font-size:18px;font-weight:700;color:#111827;">Ticket #MG-${String(data.id).padStart(4, '0')}</div>
                    <span style="display:inline-block; margin-top:8px; font-size:11px; font-weight:600; text-transform:uppercase; padding:4px 10px; border-radius:20px; background:${typeBg}; color:${typeColor}; border:1px solid ${typeBg};">${data.type}</span>
                </div>

                <div style="padding: 24px; background: #fff; font-size: 14px;">
                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">អតិថិជន:</span> <span style="font-weight:600; color:#111827;">${data.name}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">ទូរស័ព្ទ:</span> <span style="font-weight:600; color:#111827;">${data.phone}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">ម៉ូដែល:</span> <span style="font-weight:600; color:#111827;">${data.model}</span></div>
                    <div class="d-flex justify-content-between mb-3"><span style="color:#6B7280; font-weight:500;">ស្លាកលេខ:</span> <span style="font-weight:700; color:#111827; background:#F3F4F6; padding: 2px 8px; border-radius: 4px; border:1px solid #D1D5DB;">${data.plate}</span></div>

                    <div style="border-top: 1px dashed #D1D5DB; margin: 16px 0;"></div>

                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">សេវាកម្ម:</span> <span style="font-weight:600; color:#DC2626; text-align:right; max-width:60%;">${data.service}</span></div>
                    <div class="d-flex justify-content-between mb-3"><span style="color:#6B7280; font-weight:500;">រយៈពេល:</span> <span style="font-weight:600; color:#059669;"><i class="far fa-clock"></i> ${data.duration} ម៉ោង</span></div>

                    <div style="border-top: 1px dashed #D1D5DB; margin: 16px 0;"></div>

                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">កាលបរិច្ឆេទ:</span> <span style="font-weight:600; color:#111827;"><i class="far fa-calendar-alt"></i> ${data.date}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span style="color:#6B7280; font-weight:500;">ម៉ោងណាត់:</span> <span style="font-weight:600; color:#4F46E5;"><i class="far fa-clock"></i> ${data.time}</span></div>
                    <div class="d-flex justify-content-between"><span style="color:#6B7280; font-weight:500;">សាខា:</span> <span style="font-weight:600; color:#111827;">${data.branch}</span></div>

                    ${data.remark ? `
                    <div style="border-top: 1px solid #E5E7EB; margin: 20px 0 12px;"></div>
                    <div style="color:#4B5563; font-weight:600; font-size:12px; margin-bottom:8px;"><i class="far fa-comment-dots"></i> ចំណាំ (Remarks):</div>
                    <div style="background:#FEF3C7; border: 1px solid #FDE68A; border-radius: 6px; padding: 12px; font-size: 13px; color: #92400E; line-height: 1.5;">${data.remark}</div>
                    ` : ''}
                </div>
            </div>
        `;

        Swal.fire({
            html: ticketHtml,
            showCloseButton: true,
            showConfirmButton: false,
            width: '420px',
            background: 'transparent',
            padding: 0,
            customClass: {
                popup: 'p-0',
                closeButton: 'text-dark bg-white rounded-circle shadow-sm m-2 border border-light'
            }
        });
    }
</script>
@endsection