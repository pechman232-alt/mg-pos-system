@extends('layouts.homePages')

@section('content')

@php
    $authUser = auth('users')->user() ?? auth()->user();
    $roleId = (int)($authUser->role_id ?? 0);
    $isManager = in_array($roleId, [1, 6, 11]);
    
    // Roles with permission to Start Job
    $wsManagerRoles = [10, 14, 15, 16]; 
    $canStartWork = $isManager || in_array($roleId, $wsManagerRoles);

    // Roles with permission to Mark Arrived
    $saRoles = [1, 4, 5, 10, 14, 15, 16]; 
    $isSA = in_array($roleId, $saRoles);

    $userBranchName = 'Your Branch';
    if ($authUser && !$isManager && isset($branches)) {
        $userBranchObj = collect($branches)->firstWhere('id', $authUser->branch_id);
        $userBranchName = $userBranchObj
            ? trim(str_ireplace(['MG ', 'Mg '], '', $userBranchObj->branch_name))
            : 'Your Branch';
    }

    date_default_timezone_set('Asia/Phnom_Penh');
    $nowPhnomPenhTs = time();
    $activeCat = request('category', 'all');
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ============================================================
       SAAS DESIGN SYSTEM & TAILWIND REPLACEMENTS
       ============================================================ */
    :root {
        --primary: #4F46E5;      
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      
        --text-main: #111827;    
        --text-muted: #6B7280;   
        --border-color: #E5E7EB; 
        
        --teal: #0D9488;
        --teal-light: #CCFBF1;
        --amber: #D97706;
        --amber-light: #FEF3C7;
        --blue: #2563EB;
        --blue-light: #DBEAFE;
        --danger: #DC2626;

        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
    }

    body, main, .main-content { font-family: var(--font-body) !important; background-color: var(--bg-body) !important; color: var(--text-main); }
    #sidebar-wrapper, .main-sidebar, .main-header, .navbar, footer { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; width: 100% !important; background: transparent !important; }
    
    /* Layout Helpers */
    .d-flex { display: flex; }
    .flex-col { flex-direction: column; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .justify-center { justify-content: center; }
    .gap-2 { gap: 8px; } .gap-3 { gap: 12px; } .gap-4 { gap: 16px; } .gap-6 { gap: 24px; }
    .w-full { width: 100%; } .h-full { height: 100%; }
    .flex-grow { flex-grow: 1; }
    .m-0 { margin: 0; } .mt-1 { margin-top: 4px; } .mt-2 { margin-top: 8px; } .mb-2 { margin-bottom: 8px; } .mb-3 { margin-bottom: 12px; } .mb-4 { margin-bottom: 16px; } .me-2 { margin-right: 8px; }
    .p-0 { padding: 0; } .p-3 { padding: 12px; } .p-4 { padding: 16px; } .p-6 { padding: 24px; }
    .px-4 { padding-left: 16px; padding-right: 16px; } .py-2 { padding-top: 8px; padding-bottom: 8px; }
    .text-center { text-align: center; } .text-right { text-align: right; } .text-left { text-align: left; }
    .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hidden { display: none !important; }

    /* Header */
    .dashboard-header { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 40; box-shadow: 0 1px 2px rgba(0,0,0,0.02); padding: 16px 24px; }
    .page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; }
    .pulse-dot { width: 10px; height: 10px; border-radius: 50%; background-color: #10B981; box-shadow: 0 0 8px rgba(16,185,129,0.8); animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.2); } 100% { opacity: 1; transform: scale(1); } }
    .live-clock { font-family: var(--font-mono); font-size: 0.85rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

    /* Top Stats & Filters */
    .stat-badge { background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; align-items: center; }
    .stat-badge .total { padding: 4px 12px; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); border-right: 1px solid var(--border-color); }
    .stat-badge .active { padding: 4px 12px; font-size: 0.75rem; font-weight: 600; color: var(--text-main); }
    
    .filter-bar { background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 6px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .filter-bar input, .filter-bar select { border: none; background: transparent; outline: none; font-family: var(--font-body); font-size: 0.85rem; font-weight: 600; color: var(--text-main); padding: 4px; cursor: pointer; }
    .filter-bar .divider { width: 1px; height: 16px; background: var(--border-color); margin: 0 4px; }

    .grid-btn-group { display: flex; background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; padding: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .grid-btn { padding: 4px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; border: none; background: transparent; color: var(--text-muted); cursor: pointer; transition: 0.2s; }
    .grid-btn:hover { background: #F3F4F6; }
    .grid-btn.active { background: var(--text-main); color: #FFF; }

    /* Job Card Grid Layout */
    .jobs-grid { display: grid; gap: 24px; padding: 24px; width: 100%; }
    .grid-cols-1 { grid-template-columns: 1fr; }
    .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
    .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
    @media (max-width: 1200px) { .jobs-grid { grid-template-columns: repeat(3, 1fr) !important; } }
    @media (max-width: 900px) { .jobs-grid { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (max-width: 600px) { .jobs-grid { grid-template-columns: 1fr !important; } }

    /* Card Styling */
    .pro-job-card { background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color); display: flex; flex-direction: column; height: 100%; min-height: 420px; overflow: hidden; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .pro-job-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #D1D5DB; }

    .kh-plate { border: 2px solid var(--text-main); border-radius: 6px; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); display: inline-flex; flex-direction: column; align-items: center; justify-content: center; padding: 4px 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .plate-city { font-family: 'Noto Sans Khmer', sans-serif; font-size: 8px; font-weight: 600; letter-spacing: 1px; color: var(--text-muted); text-transform: uppercase; line-height: 1; margin-bottom: 2px; }
    .plate-num { font-family: var(--font-mono); font-size: 1.1rem; font-weight: 800; letter-spacing: 0.5px; color: var(--text-main); line-height: 1; }

    .eyebrow-label { font-size: 9px; font-weight: 700; letter-spacing: 0.5px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; line-height: 1; }

    /* Stepper */
    .status-stepper { display: flex; align-items: flex-start; margin-bottom: 16px; width: 100%; }
    .status-step { display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0; width: 54px; }
    .status-step .step-dot { width: 10px; height: 10px; border-radius: 50%; background: #D1D5DB; border: 2px solid #fff; box-shadow: 0 0 0 2px #D1D5DB; transition: all 0.2s ease; }
    .status-step .step-label { font-size: 9px; font-weight: 700; letter-spacing: 0.2px; color: var(--text-muted); text-transform: uppercase; text-align: center; }
    
    .status-step.done .step-dot { background: var(--teal); box-shadow: 0 0 0 2px var(--teal); }
    .status-step.done .step-label { color: var(--teal); }
    .status-step.active .step-dot { background: var(--blue); box-shadow: 0 0 0 3px rgba(37,99,235,0.2); width: 12px; height: 12px; }
    .status-step.active .step-label { color: var(--text-main); }
    .status-step.paused .step-dot { background: var(--text-muted); box-shadow: 0 0 0 3px rgba(107,114,128,0.2); width: 12px; height: 12px; }
    .status-step.paused .step-label { color: var(--text-muted); }
    
    .step-connector { flex: 1; height: 2px; background: #D1D5DB; margin: 0 2px; margin-top: 4px; border-radius: 2px; transition: background 0.2s ease; }
    .step-connector.done { background: var(--teal); }

    /* LED Timer */
    .led-screen { background: #0F172A; border-radius: 8px; padding: 8px; border: 2px solid #1E293B; box-shadow: inset 0 2px 10px rgba(0,0,0,0.5); text-align: center; margin-bottom: 12px; }
    .digital-timer { font-family: var(--font-mono); font-size: 1.75rem; font-weight: 700; color: #EF4444; letter-spacing: 1px; line-height: 1; text-shadow: 0 0 8px rgba(239, 68, 68, 0.5); }
    .timer-frozen { color: #64748B !important; text-shadow: none !important; }

    /* Action Buttons */
    .btn-action { display: flex; align-items: center; justify-content: center; gap: 8px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; border: none; color: #fff; padding: 10px 16px; cursor: pointer; transition: 0.2s; width: 100%; }
    .btn-action:hover:not([disabled]) { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .btn-primary { background: var(--text-main); } .btn-primary:hover { background: #374151; }
    .btn-teal { background: var(--teal); } .btn-teal:hover { background: #0F766E; }
    .btn-amber { background: var(--amber); } .btn-amber:hover { background: #B45309; }
    .btn-outline { background: #FFF; color: var(--text-main); border: 1px solid var(--border-color); width: auto; }
    .btn-outline:hover { background: #F3F4F6; }
    .btn-disabled { background: #F3F4F6; color: #9CA3AF; border: 1px solid var(--border-color); cursor: not-allowed; }

    .tag { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; border: 1px solid transparent; }
    .tag-blue { background: var(--blue-light); color: var(--blue); border-color: #BFDBFE; }
    .tag-amber { background: var(--amber-light); color: var(--amber); border-color: #FDE68A; }
    .tag-teal { background: var(--teal-light); color: var(--teal); border-color: #99F6E4; }
    .tag-gray { background: #F3F4F6; color: var(--text-muted); border-color: var(--border-color); }
</style>

<div class="d-flex flex-col h-full w-full">
    <!-- Header -->
    <div class="dashboard-header d-flex items-center justify-between">
        <div>
            <h1 class="page-title m-0"><i class="fas fa-tv me-2" style="color: var(--primary);"></i> Workshop Live Board</h1>
            <div class="d-flex items-center gap-2 mt-1">
                <span class="pulse-dot"></span>
                <span id="liveClock" class="live-clock">00:00:00 PM</span>
            </div>
        </div>

        <div class="d-flex items-center gap-3">
            <div class="stat-badge">
                <div class="total">Total: <span style="font-weight: 800; color: var(--text-main);">{{ count($jobs) }}</span></div>
                <div class="active">Active: <span style="font-weight: 800; font-size: 0.9rem;">{{ collect($jobs)->where('status', 'In Progress')->count() }}</span></div>
            </div>
            
            <div class="grid-btn-group" title="Set Grid Columns">
                <button onclick="setGrid(1)" class="grid-btn" data-col="1">1</button>
                <button onclick="setGrid(2)" class="grid-btn" data-col="2">2</button>
                <button onclick="setGrid(3)" class="grid-btn" data-col="3">3</button>
                <button onclick="setGrid(4)" class="grid-btn" data-col="4">4</button>
                <button onclick="setGrid('auto')" class="grid-btn active" data-col="auto">Auto</button>
            </div>
        
            <form action="{{ url()->current() }}" method="GET" id="filterForm" class="filter-bar">
                <i class="fas fa-search text-muted"></i>
                <input type="text" id="plateSearch" placeholder="Search plate..." onkeyup="applyFilters()" style="width: 120px;">
                <div class="divider"></div>
        
                <i class="far fa-calendar-alt text-primary"></i>
                <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" onchange="this.form.submit()" title="Select Date">
                
                <div class="divider"></div>

                @if($isManager)
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    <select name="branch" onchange="this.form.submit()">
                        <option value="all">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                {{ str_ireplace(['MG ', 'Mg ', ' Service', ' Showroom'], '', $branch->branch_name) }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $userBranchName }}
                    </span>
                @endif
        
                <div class="divider"></div>
                
                <i class="fas fa-id-badge text-muted"></i>
                <select name="employee" onchange="this.form.submit()">
                    <option value="all">All Staffs</option>
                    @if(isset($branchEmployees) && count($branchEmployees) > 0)
                        @foreach($branchEmployees as $emp)
                            <option value="{{ $emp }}" {{ request('employee') == $emp ? 'selected' : '' }}>{{ $emp }}</option>
                        @endforeach
                    @endif
                </select>
                <button type="submit" class="hidden">Filter</button>
            </form>
        </div>
    </div> 
    
    <div style="flex-grow: 1; width: 100%;">
        <div id="busyTechsData" class="hidden">{!! json_encode($busyTechnicians ?? []) !!}</div>

        <!-- Grid Layout -->
        <div class="jobs-grid grid-cols-4" id="jobsGrid">
            @forelse($jobs as $job)
            @php 
                $onlyDate = date('Y-m-d', strtotime($job->booking_date));
                $elapsedSeconds = 0;
                if ($job->status == 'In Progress' && $job->start_time) {
                    try {
                        $startObj = \Carbon\Carbon::parse($onlyDate . ' ' . $job->start_time, 'Asia/Phnom_Penh');
                        $nowObj = \Carbon\Carbon::now('Asia/Phnom_Penh');
                        $diff = abs($nowObj->getTimestamp() - $startObj->getTimestamp());
                        $elapsedSeconds = $diff % 43200;
                        if ($elapsedSeconds > 21600) { $elapsedSeconds = 43200 - $elapsedSeconds; }
                    } catch (\Exception $e) { $elapsedSeconds = 0; }
                }
                
                $jobBranchId = $job->branch_id ?? $job->location;
                $sColor = match(strtolower($job->status)) { 'in progress' => 'var(--blue)', 'arrived' => 'var(--amber)', 'hold' => 'var(--text-muted)', 'completed' => 'var(--teal)', default => 'var(--border-color)' };
                $sTagClass = match(strtolower($job->status)) { 'in progress' => 'tag-blue', 'arrived' => 'tag-amber', 'hold' => 'tag-gray', 'completed' => 'tag-teal', default => 'tag-gray' };

                $stepOrder = ['arrived' => 1, 'in progress' => 2, 'hold' => 2, 'completed' => 3];
                $stepIndex = $stepOrder[strtolower($job->status)] ?? 0;
                $isPaused = strtolower($job->status) === 'hold';
            @endphp
            
            <div class="job-item animate__animated animate__fadeInUp" style="animation-duration: 0.4s;" data-branch-id="{{ strtolower($jobBranchId) }}" data-plate="{{ $job->plate }}" data-date="{{ $onlyDate }}">
                <div class="pro-job-card">
                    <div style="height: 6px; width: 100%; background-color: {{ $sColor }};"></div>
                    
                    <div class="p-4 d-flex flex-col flex-grow w-full">
                        <div class="d-flex justify-between items-center mb-3">
                            <div class="kh-plate">
                                <span class="plate-city">ភ្នំពេញ</span>
                                <span class="plate-num">{{ $job->plate }}</span>
                            </div>
                            <div class="text-right">
                                <div class="tag {{ $sTagClass }} mb-2 uppercase">{{ $job->status }}</div>
                                <div style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted);"><i class="far fa-clock"></i> {{ date('h:i A', strtotime($job->booking_time)) }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin: 0;" class="truncate">{{ $job->customer_name }}</h3>
                            <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-top: 4px;"><i class="fas fa-phone-alt me-2"></i>{{ $job->contact }}</div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <span class="tag tag-gray"><i class="fas fa-car me-2 text-muted"></i>{{ $job->model }}</span>
                            <span class="tag" style="background: #FEE2E2; color: #DC2626; border-color: #FECACA;"><i class="fas fa-wrench me-2 opacity-75"></i>{{ $job->service_type }}</span>
                        </div>

                        <!-- Stepper -->
                        <div class="status-stepper">
                            <div class="status-step {{ $stepIndex > 0 ? 'done' : 'active' }}">
                                <div class="step-dot"></div><div class="step-label">Booked</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 1 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex > 1 ? 'done' : ($stepIndex == 1 ? 'active' : '') }}">
                                <div class="step-dot"></div><div class="step-label">Arrived</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 2 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex > 2 ? 'done' : ($stepIndex == 2 ? ($isPaused ? 'paused' : 'active') : '') }}">
                                <div class="step-dot"></div><div class="step-label">{{ $isPaused && $stepIndex == 2 ? 'Paused' : 'Working' }}</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 3 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex >= 3 ? 'done' : '' }}">
                                <div class="step-dot"></div><div class="step-label">Done</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 w-full mt-auto mb-3">
                            <div class="flex-grow p-2" style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 8px;">
                                <div class="eyebrow-label">Advisor</div>
                                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-main);" class="truncate"><i class="fas fa-user-tie me-2 text-primary"></i>{{ $job->sa_name ?? 'Pending' }}</div>
                            </div>
                            <div class="flex-grow p-2" style="background: var(--teal-light); border: 1px solid #99F6E4; border-radius: 8px;">
                                <div class="eyebrow-label" style="color: var(--teal);">Technician</div>
                                <div style="font-size: 0.75rem; font-weight: 700; color: var(--teal);" class="truncate"><i class="fas fa-wrench me-2 opacity-75"></i>{{ $job->technician_name ?? 'Pending' }}</div>
                            </div>
                        </div>

                        @if(!empty($job->remark))
                        <div class="p-2 mb-2" style="background: #FFFBEB; border-left: 3px solid var(--amber); border-radius: 6px; font-size: 0.75rem; font-weight: 500; color: #92400E;">
                            <i class="fas fa-quote-left me-1 opacity-50"></i> {{ $job->remark }}
                        </div>
                        @endif
                    </div>

                    <div style="background: var(--surface); border-top: 1px solid var(--border-color); padding: 16px; text-align: center;">
                        @if($job->status == 'In Progress')
                            <div class="led-screen"><span style="font-size: 8px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Repair Duration</span><div class="digital-timer" data-seconds="{{ $elapsedSeconds }}">00:00:00</div></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn-action btn-outline" onclick="confirmAction('hold', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-pause"></i></button>
                                <button type="button" class="btn-action btn-teal flex-grow" onclick="confirmAction('finish', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-check-circle"></i> Finish Job</button>
                            </div>
                        @elseif($job->status == 'Hold')
                            <div class="led-screen"><span style="font-size: 8px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Paused Duration</span><div class="digital-timer timer-frozen">{{ $job->paused_duration ?? '00:00:00' }}</div></div>
                            <button type="button" class="btn-action btn-primary" onclick="confirmAction('resume', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-play"></i> Resume Work</button>
                        @elseif($job->status == 'Arrived')
                            @if ($canStartWork)
                                <button type="button" class="btn-action btn-primary" onclick="startJob('{{ $job->id }}', '{{ $job->plate }}', '{{ $jobBranchId }}', '{{ addslashes($job->sa_name ?? '') }}', '{{ addslashes($job->technician_name ?? '') }}', '{{ $onlyDate }}')"><i class="fas fa-rocket"></i> Start Job</button>
                            @else
                                <button type="button" disabled class="btn-action btn-disabled" title="Only Managers can start jobs"><i class="fas fa-rocket"></i> Start Job</button>
                            @endif
                        @else
                            @if ($isSA)
                                <button type="button" class="btn-action btn-amber" onclick="markAsArrived('{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-map-marker-alt"></i> Mark Arrived</button>
                            @else
                                <button type="button" disabled class="btn-action btn-disabled" title="Only SA can mark as arrived"><i class="fas fa-map-marker-alt"></i> Mark Arrived</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 100px 0;">
                    <i class="fas fa-box-open" style="font-size: 3rem; color: var(--border-color); margin-bottom: 16px;"></i>
                    <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--text-muted);">No Active Jobs Today</h4>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    // 🌟 Error Handler 🌟
    $.ajaxSetup({ 
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
        error: function(jqXHR) {
            if (jqXHR.status === 419 || jqXHR.status === 401) {
                Swal.fire({ icon: 'warning', title: 'Session Expired!', text: 'Refreshing page...', showConfirmButton: false, timer: 2000 });
                setTimeout(() => window.location.reload(), 2000);
            }
        }
    });
    
    const dbErrorMsg = {!! json_encode($dbErrorMsg ?? '') !!};
    const saByBranch = {!! json_encode($saByBranch ?? (object)[]) !!};
    const techByBranch = {!! json_encode($techByBranch ?? (object)[]) !!};
    const isManager = {{ $isManager ? 'true' : 'false' }};
    let busyTechs = {!! json_encode($busyTechnicians ?? []) !!};

    // 🌟 Grid Configurator 🌟
    function setGrid(cols) {
        const grid = document.getElementById('jobsGrid');
        const buttons = document.querySelectorAll('.grid-btn');

        buttons.forEach(btn => {
            btn.classList.remove('active');
            if(btn.dataset.col == cols) btn.classList.add('active');
        });

        let classMap = { '1': 'grid-cols-1', '2': 'grid-cols-2', '3': 'grid-cols-3', '4': 'grid-cols-4', 'auto': 'grid-cols-4' };
        grid.className = `jobs-grid ${classMap[cols]}`;
        localStorage.setItem('workshopGridPref', cols);
    }

    function updateRepairTimers() {
        document.querySelectorAll('.digital-timer').forEach(t => {
            if(t.classList.contains('timer-frozen')) return;
            let sTotal = parseInt(t.dataset.seconds);
            if (isNaN(sTotal)) return;
            sTotal = Math.abs(sTotal); 
            let h = Math.floor(sTotal / 3600).toString().padStart(2, '0');
            let m = Math.floor((sTotal % 3600) / 60).toString().padStart(2, '0');
            let s = (sTotal % 60).toString().padStart(2, '0');
            t.innerText = h + ":" + m + ":" + s; t.dataset.seconds = sTotal + 1;
        });
    }

    $(document).ready(function() {
        let pref = localStorage.getItem('workshopGridPref') || 'auto';
        setGrid(pref);

        updateRepairTimers(); setInterval(updateRepairTimers, 1000);
        setInterval(() => { $('#liveClock').text(new Date().toLocaleTimeString('en-US', {hour12: true})); }, 1000);
        applyFilters(); 

        setInterval(() => { $.get('/sanctum/csrf-cookie'); }, 900000);
    });

    function applyFilters() {
        let searchValue = $('#plateSearch').val().toUpperCase(); 
        let branchSelect = $('select[name="branch"]');
        let branchValue = branchSelect.length ? branchSelect.val().toString().toLowerCase() : 'all'; 

        $('.job-item').each(function() {
            let rowPlate = String($(this).data('plate')).toUpperCase(); 
            let rowBranchId = String($(this).data('branch-id')).toLowerCase(); 
            let matchSearch = rowPlate.includes(searchValue); 
            let matchBranch = (branchValue === 'all' || branchValue === '' || rowBranchId === branchValue);
            $(this).toggle(matchSearch && matchBranch);
        });
    }

    function refreshDashboard() {
        $.get(window.location.href, function(data) {
            $('#jobsGrid').html($(data).find('#jobsGrid').html()); 
            let newBusyData = $(data).find('#busyTechsData').text();
            if (newBusyData) busyTechs = JSON.parse(newBusyData);
            updateRepairTimers(); applyFilters(); 
        });
    }

    function confirmAction(type, id, plate) {
        let configs = { 
            hold: { t: 'Pause Work?', i: 'warning', b: 'Yes, Pause', c: '#4B5563', msg: 'Job Paused!' }, 
            resume: { t: 'Resume Work?', i: 'info', b: 'Yes, Resume', c: '#4F46E5', msg: 'Job Resumed!' }, 
            finish: { t: 'Finish Job?', i: 'success', b: 'Yes, Finish', c: '#0D9488', msg: 'Job Completed!' } 
        };

        Swal.fire({
            title: configs[type].t, text: "Plate: " + plate, icon: configs[type].i, 
            showCancelButton: true, confirmButtonColor: configs[type].c, confirmButtonText: configs[type].b,
            customClass: { popup: 'rounded-4 shadow-sm border-0' }
        }).then(res => {
            if(res.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({ 
                    url: "{{ url('workshop') }}/" + type + "/" + id, type: 'POST', 
                    success: function() { 
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: configs[type].msg, showConfirmButton: false, timer: 2000 });
                        setTimeout(() => window.location.reload(), 1500);
                    }, 
                    error: function(xhr) { 
                        if (xhr.status !== 419 && xhr.status !== 401) Swal.fire('Error', 'Failed to save data!', 'error'); 
                    } 
                });
            }
        });
    }

    function startJob(id, plate, jobBranchId, currentSA, currentTech, bookingDate) {
        if (dbErrorMsg) { Swal.fire('Error', dbErrorMsg, 'error'); return; }

        jobBranchId = String(jobBranchId || '').trim().toLowerCase();
        let availableSAs = isManager ? [].concat(...Object.values(saByBranch)) : (saByBranch[jobBranchId] || []);
        let availableTechs = isManager ? [].concat(...Object.values(techByBranch)) : (techByBranch[jobBranchId] || []);

        availableSAs = [...new Set(availableSAs)].filter(Boolean).sort();
        availableTechs = [...new Set(availableTechs)].filter(Boolean).sort();

        let saOpts = `<option value="" disabled ${!currentSA ? 'selected' : ''}>-- Select SA --</option>`;
        availableSAs.forEach(emp => { saOpts += `<option value="${emp}" ${emp === currentSA ? 'selected' : ''}>${emp}</option>`; });
        if (currentSA && !availableSAs.includes(currentSA)) saOpts += `<option value="${currentSA}" selected>${currentSA}</option>`;

        let techOpts = `<option value="" disabled ${!currentTech ? 'selected' : ''}>-- Select Tech --</option>`;
        availableTechs.forEach(emp => { 
            let isBusy = busyTechs.includes(emp);
            techOpts += `<option value="${emp}" data-busy="${isBusy}" ${emp === currentTech ? 'selected' : ''}>${emp} ${isBusy ? '🔴 (Busy)' : '🟢 (Free)'}</option>`; 
        });
        if (currentTech && !availableTechs.includes(currentTech)) techOpts += `<option value="${currentTech}" selected>${currentTech}</option>`;

        Swal.fire({
            title: 'Start Job: ' + plate,
            html: `
                <div class="text-left mt-3"><label style="font-size:12px; font-weight:700; color:#6B7280;">Booking Date</label></div>
                <input type="date" id="booking_date" value="${bookingDate}" style="width:100%; padding:10px; border:1px solid #E5E7EB; border-radius:8px; margin-bottom:12px;">
                <div class="text-left"><label style="font-size:12px; font-weight:700; color:#6B7280;">Service Advisor</label></div>
                <select id="sa_name" style="width:100%; padding:10px; border:1px solid #E5E7EB; border-radius:8px; margin-bottom:12px;">${saOpts}</select>
                <div class="text-left"><label style="font-size:12px; font-weight:700; color:#6B7280;">Technician</label></div>
                <select id="technician_name" style="width:100%; padding:10px; border:1px solid #E5E7EB; border-radius:8px;">${techOpts}</select>
            `,
            showCancelButton: true, confirmButtonColor: '#4F46E5', confirmButtonText: 'Start Job',
            customClass: { popup: 'rounded-4 shadow-sm border-0' },
            preConfirm: () => {
                const bDate = document.getElementById('booking_date').value;
                const saName = document.getElementById('sa_name').value;
                const techSelect = document.getElementById('technician_name');
                const techName = techSelect.value;
                if (!bDate || !saName || !techName) { Swal.showValidationMessage('Please fill all fields!'); return false; }
                return { booking_date: bDate, sa_name: saName, technician_name: techName, is_busy: techSelect.options[techSelect.selectedIndex].getAttribute('data-busy') === 'true' };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let data = result.value;
                const runStartJobAjax = () => {
                    Swal.fire({ title: 'Starting...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    $.ajax({
                        url: `{{ url('workshop/start') }}/${id}`, type: 'POST', 
                        data: { booking_date: data.booking_date, sa_name: data.sa_name, technician_name: data.technician_name },
                        success: function() { Swal.close(); refreshDashboard(); }, 
                        error: function(xhr) { if (xhr.status !== 419 && xhr.status !== 401) { Swal.fire('Error', 'Save failed!', 'error'); refreshDashboard(); } }
                    });
                };
                if (data.is_busy) {
                    Swal.fire({ title: 'Technician is Busy!', text: `Assign anyway?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#DC2626', confirmButtonText: 'Yes, Assign'
                    }).then((r) => { if (r.isConfirmed) runStartJobAjax(); });
                } else { runStartJobAjax(); }
            }
        });
    }

    function markAsArrived(id, plate, force = 0) {
        let url = `{{ url('workshop/arrive') }}/${id}` + (force ? '?force_update=1' : '');
        Swal.fire({
            title: 'Confirm Arrival', text: `Mark ${plate} as arrived?`, icon: 'question', showCancelButton: true, confirmButtonColor: '#D97706', confirmButtonText: 'Yes, Arrived'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({ 
                    url: url, type: 'POST', data: { _token: '{{ csrf_token() }}' },
                    success: function() { Swal.close(); refreshDashboard(); }, 
                    error: function(xhr) {
                        if (xhr.status === 419 || xhr.status === 401) return;
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.is_early) {
                            Swal.fire({ title: 'Early Arrival!', text: xhr.responseJSON.message, icon: 'question', showCancelButton: true, confirmButtonText: 'Change Date & Continue'
                            }).then((ans) => { if (ans.isConfirmed) markAsArrived(id, plate, 1); });
                        } else { Swal.fire('Error', 'Operation failed', 'error'); }
                    } 
                });
            }
        });
    }
</script>
@endsection