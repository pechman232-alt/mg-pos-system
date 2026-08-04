@extends('layouts.homePages')

@section('content')

@php
    $authUser = auth('users')->user() ?? auth()->user();
    $roleId = (int)($authUser->role_id ?? 0);
    // លេខ 1, 6, 11 នេះគឺតំណាងអោយ Admin, Super Admin, Group Manager 
    $isManager = in_array($roleId, [1, 6, 11]);
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ============================================================
       DESIGN TOKENS — shared brand system
       navy = brand / neutral info · blue = active work in progress
       amber = awaiting action · teal = completed / positive
       steel = paused / muted · brand red = accents & warnings

       Every color below has exactly one meaning across the board.
       Buttons, badges, stepper dots and card accents all pull from
       this same table instead of one-off hex values, so status is
       always readable at a glance regardless of where it appears.
       ============================================================ */
    :root {
        --ink-navy: #0B2A52;
        --navy-light: #123B6E;
        --active-blue: #2F6FDE;
        --active-blue-tint: #EAF1FD;
        --steel: #45566E;
        --steel-tint: #EEF2F7;
        --teal: #0E9C79;
        --teal-dark: #0B8567;
        --teal-tint: #E3F6F0;
        --amber: #B9750F;
        --amber-dark: #9C630B;
        --amber-ink: #7A5209;
        --amber-tint: #FBF1E1;
        --rust: #C24A3B;
        --rust-tint: #FBEAE8;
        --brand-red: #D0262C;
        --brand-red-tint: #FBEAEA;
        --bg-main: #F5F7FA;
        --border-light: #E4E9F0;
        --text-dark: #16233B;
        --text-muted: #5B6B84;
        --text-faint: #94A3B8;

        /* legacy aliases so any lingering references keep working */
        --mg-blue: #0B2A52;
        --mg-red: #D0262C;
    }

    body, main, .main-content { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; background-color: var(--bg-main) !important; color: var(--text-dark); }
    #sidebar-wrapper, .main-sidebar, .main-header, .navbar, footer { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 0 !important; width: 100% !important; background: transparent !important; }
    .dashboard-header { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-light); position: sticky; top: 0; z-index: 40; box-shadow: 0 1px 2px rgba(16,24,40,0.04); }

    .pro-job-card { background: #ffffff; border-radius: 18px; border: 1px solid var(--border-light); display: flex; flex-direction: column; height: 100%; min-height: 480px; overflow: hidden; transition: all 0.25s ease; position: relative; box-shadow: 0 1px 2px rgba(16,24,40,0.03); }
    .pro-job-card:hover { transform: translateY(-3px); box-shadow: 0 16px 32px -8px rgba(16,24,40,0.10); border-color: #d3dae4; }

    /* ---- license plate: double-border embossed frame, not a text box ---- */
    .kh-plate {
        border: 2px solid var(--ink-navy);
        border-radius: 8px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        display: inline-flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 6px 18px 7px;
        position: relative;
        box-shadow: 0 1px 2px rgba(16,24,40,0.06);
    }
    .kh-plate::before {
        content: "";
        position: absolute;
        top: 3px; left: 3px; right: 3px; bottom: 3px;
        border: 1px solid rgba(11, 42, 82, 0.15);
        border-radius: 5px;
        pointer-events: none;
    }
    .plate-city {
        font-family: 'Noto Sans Khmer', sans-serif;
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 1.5px;
        color: var(--text-faint);
        text-transform: uppercase;
        line-height: 1;
        margin-bottom: 3px;
    }
    .plate-num {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 17px;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--ink-navy);
        line-height: 1;
    }

    /* ---- eyebrow labels: one consistent typographic voice for all micro-labels ---- */
    .eyebrow-label {
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.6px;
        color: var(--text-faint);
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    /* ---- status stepper: real workflow position, not decoration ---- */
    .status-stepper { display: flex; align-items: center; margin-bottom: 18px; width: 100%; }
    .status-step { display: flex; flex-direction: column; align-items: center; gap: 6px; flex-shrink: 0; }
    .status-step .step-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #CBD5E1; border: 2px solid #fff;
        box-shadow: 0 0 0 2px #CBD5E1;
        transition: all 0.2s ease;
    }
    .status-step .step-label {
        font-size: 8.5px; font-weight: 700; letter-spacing: 0.4px;
        color: var(--text-faint); text-transform: uppercase; white-space: nowrap;
    }
    .status-step.done .step-dot { background: var(--teal); box-shadow: 0 0 0 2px var(--teal); }
    .status-step.done .step-label { color: var(--teal); }
    .status-step.active .step-dot { background: var(--active-blue); box-shadow: 0 0 0 3px rgba(47,111,222,0.22); width: 12px; height: 12px; }
    .status-step.active .step-label { color: var(--ink-navy); }
    .status-step.paused .step-dot { background: var(--steel); box-shadow: 0 0 0 3px rgba(69,86,110,0.18); width: 12px; height: 12px; }
    .status-step.paused .step-label { color: var(--steel); }
    .step-connector { flex: 1; height: 2px; background: #CBD5E1; margin: 0 4px; margin-bottom: 14px; border-radius: 2px; transition: background 0.2s ease; }
    .step-connector.done { background: var(--teal); }

    /* ---- LED timer (unchanged, already reads as an instrument) ---- */
    .led-screen { background: #0B1526; border-radius: 10px; padding: 10px; border: 2px solid #1c2b45; box-shadow: inset 0 2px 10px rgba(0,0,0,0.45); }
    .digital-timer { font-family: 'JetBrains Mono', monospace; font-size: 2.5rem; font-weight: 700; color: #EF4B49; letter-spacing: 2px; line-height: 1; text-align: center; text-shadow: 0 0 10px rgba(239, 75, 73, 0.45); }
    .timer-frozen { color: #64748b !important; text-shadow: none !important; }

    /* ---- action buttons: one shared component, hover states in CSS not inline JS ---- */
    .btn-action {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        border-radius: 10px; font-weight: 600; border: none; color: #fff;
        padding: 12px 20px; cursor: pointer;
        box-shadow: 0 4px 10px -4px rgba(16,24,40,0.28);
        transition: background-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 8px 16px -6px rgba(16,24,40,0.32); }
    .btn-action:active { transform: translateY(0); }
    .btn-navy { background: var(--ink-navy); }
    .btn-navy:hover { background: var(--navy-light); }
    .btn-teal { background: var(--teal); }
    .btn-teal:hover { background: var(--teal-dark); }
    .btn-amber { background: var(--amber); }
    .btn-amber:hover { background: var(--amber-dark); }
    .btn-outline { background: #fff; color: var(--text-muted); border: 2px solid var(--border-light); box-shadow: none; }
    .btn-outline:hover { border-color: #cbd5e1; background: #f8fafc; box-shadow: none; }

    .grid-btn.grid-btn-active { background: #ffffff; color: var(--ink-navy); box-shadow: 0 1px 2px rgba(16,24,40,0.08); }
</style>

<div class="w-full flex flex-col min-h-screen">
    <div class="dashboard-header px-6 py-4 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight m-0" style="color: var(--ink-navy);">
                <i class="fas fa-tv me-2" style="color: var(--brand-red);"></i> Workshop Live Board
            </h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                <span id="liveClock" class="text-sm font-medium text-slate-500 font-['JetBrains_Mono'] uppercase tracking-widest">00:00:00 PM</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm">
                <div class="px-4 py-1.5 text-xs font-medium text-slate-500 border-r border-slate-100">
                    Total: <span class="text-slate-800 text-sm font-bold ms-1" id="totalCount">{{ count($jobs) }}</span>
                </div>
                <div class="px-4 py-1.5 text-xs font-medium" style="color: var(--ink-navy);">
                    Active: <span class="text-lg font-bold ms-1">{{ collect($jobs)->where('status', 'In Progress')->count() }}</span>
                </div>
            </div>
            
            <!-- 🌟 មុខងារកំណត់ Card (Grid View Selector) 🌟 -->
            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl p-1 shadow-sm hidden md:flex" title="កំណត់ចំនួន Card ក្នុងមួយជួរ">
                <button onclick="setGrid(1)" class="grid-btn px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" data-col="1">1</button>
                <button onclick="setGrid(2)" class="grid-btn px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" data-col="2">2</button>
                <button onclick="setGrid(3)" class="grid-btn px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" data-col="3">3</button>
                <button onclick="setGrid(4)" class="grid-btn px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" data-col="4">4</button>
                <button onclick="setGrid('auto')" class="grid-btn px-2.5 py-1 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-200 transition-colors" data-col="auto">Auto</button>
            </div>
        
            <form action="{{ url()->current() }}" method="GET" id="filterForm" class="flex flex-wrap items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 shadow-sm">
                <i class="fas fa-search text-slate-400 text-sm"></i>
                <input type="text" id="plateSearch" class="bg-transparent text-sm font-medium text-slate-700 outline-none w-32 placeholder:text-slate-400" placeholder="Search plate..." onkeyup="applyFilters()">
                <div class="w-[1px] h-4 bg-slate-300 mx-1"></div>
        
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar-alt text-xs" style="color: var(--ink-navy);"></i>
                    <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" class="bg-transparent text-sm font-bold text-slate-700 outline-none cursor-pointer border-none p-0 focus:ring-0" onchange="this.form.submit()" title="ជ្រើសរើសកាលបរិច្ឆេទ">
                </div>
                <div class="w-[1px] h-4 bg-slate-300 mx-1 hidden sm:block"></div>

                @if($isManager)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-xs" style="color: var(--brand-red);"></i>
                        <select name="branch" class="bg-transparent text-sm font-bold text-slate-700 outline-none cursor-pointer border-none p-0 focus:ring-0" onchange="this.form.submit()">
                            <option value="all">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                    {{ str_ireplace(['MG ', 'Mg ', ' Service', ' Showroom'], '', $branch->branch_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <span class="text-sm font-bold text-slate-600">
                        <i class="fas fa-map-marker-alt me-1" style="color: var(--brand-red);"></i> 
                        {{ str_ireplace(['MG ', 'Mg '], '', \App\Models\BranchModel::where('id', $authUser->branch_id)->value('branch_name') ?? 'My Branch') }}
                    </span>
                @endif
        
                <div class="w-[1px] h-4 bg-slate-300 mx-1 hidden sm:block"></div>
                
                <div class="flex items-center gap-2">
                    <i class="fas fa-id-badge text-xs" style="color: var(--steel);"></i>
                    <select name="employee" class="bg-transparent text-sm font-bold outline-none cursor-pointer border-none p-0 focus:ring-0" style="color: var(--ink-navy);" onchange="this.form.submit()">
                        <option value="all">All Staffs</option>
                        @if(isset($branchEmployees) && count($branchEmployees) > 0)
                            @foreach($branchEmployees as $emp)
                                <option value="{{ $emp }}" {{ request('employee') == $emp ? 'selected' : '' }}>{{ $emp }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <button type="submit" class="hidden">Filter</button>
            </form>
        </div>
    </div> 
    
    <div class="p-6 flex-grow w-full">
        <div id="busyTechsData" class="hidden">{!! json_encode($busyTechnicians ?? []) !!}</div>

        <!-- 🌟 បន្ថែម ID 'jobsGrid' ដែលនឹងប្តូរ Class លេខ Column អូតូតាម JS 🌟 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 w-full" id="jobsGrid">
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
                
                $displayLocation = $job->location;
                $jobBranchId = $job->branch_id ?? $job->location;
                if (is_numeric($displayLocation) && isset($branches)) {
                    $matchedBranch = collect($branches)->firstWhere('id', $displayLocation);
                    if ($matchedBranch) $displayLocation = str_ireplace(['MG ', 'Mg ', ' Service', ' Showroom'], '', $matchedBranch->branch_name);
                }
                
                $sColor = match(strtolower($job->status)) { 'in progress' => '#2F6FDE', 'arrived' => '#B9750F', 'hold' => '#5B6B84', 'completed' => '#0E9C79', default => '#CBD5E1' };
                $sTextClass = match(strtolower($job->status)) { 'in progress' => 'text-[#2F6FDE] bg-[#EAF1FD] border-[#C9DBF7]', 'arrived' => 'text-[#B9750F] bg-[#FBF1E1] border-[#F0DDB8]', 'hold' => 'text-[#5B6B84] bg-[#EEF2F7] border-[#DDE4EC]', 'completed' => 'text-[#0E9C79] bg-[#E3F6F0] border-[#BEEADD]', default => 'text-[#5B6B84] bg-[#F5F7FA] border-[#E4E9F0]' };

                // 🌟 Stepper position: where this job actually sits in the real workflow 🌟
                $stepOrder = ['arrived' => 1, 'in progress' => 2, 'hold' => 2, 'completed' => 3];
                $stepIndex = $stepOrder[strtolower($job->status)] ?? 0;
                $isPaused = strtolower($job->status) === 'hold';
            @endphp
            
            <div class="job-item w-full animate__animated animate__fadeInUp" style="animation-duration: 0.5s;" data-branch-id="{{ strtolower($jobBranchId) }}" data-plate="{{ $job->plate }}" data-date="{{ $onlyDate }}">
                <div class="pro-job-card">
                    <div class="w-full h-2" style="background-color: {{ $sColor }}"></div>
                    <div class="p-5 flex-grow flex flex-col w-full text-left">
                        <div class="flex justify-between items-start mb-5 w-full">
                            <div class="kh-plate">
                                <span class="plate-city">ភ្នំពេញ</span>
                                <span class="plate-num">{{ $job->plate }}</span>
                            </div>
                            
                            <!-- 🌟 Update ថ្មី: Booked & Arrived Layout 🌟 -->
                            <div class="text-right">
                                <!-- Status Badge -->
                                <div class="inline-block border px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider mb-3 shadow-sm {{ $sTextClass }}">
                                    {{ $job->status }}
                                </div>
                                
                                <div class="flex flex-col items-end gap-1.5">
                                    <!-- ប្រអប់ Booking Date -->
                                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-100 px-2 py-1.5 rounded-md shadow-sm">
                                        <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-widest border-r border-slate-200 pr-2">Booked</span>
                                        <div class="text-[10px] font-bold text-slate-700 flex items-center gap-1.5">
                                            <span><i class="far fa-calendar-alt text-blue-500"></i> {{ date('d-M-Y', strtotime($job->booking_date)) }}</span>
                                            <span class="text-slate-300">|</span>
                                            <span><i class="far fa-clock text-slate-400"></i> {{ date('h:i A', strtotime($job->booking_time)) }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- ប្រអប់ Arrived Date (បង្ហាញលុះត្រាតែអតិថិជនមកដល់) -->
                                    @if(in_array(strtolower($job->status), ['arrived', 'in progress', 'completed', 'hold']))
                                    <div class="flex items-center gap-2 bg-amber-50/50 border border-amber-100/50 px-2 py-1.5 rounded-md shadow-sm">
                                        <span class="text-[8.5px] font-bold text-amber-600/70 uppercase tracking-widest border-r border-amber-200/50 pr-2">Arrived</span>
                                        <div class="text-[10px] font-bold text-slate-700 flex items-center gap-1.5">
                                            <span><i class="fas fa-sign-in-alt text-amber-500"></i> {{ date('d-M-Y', strtotime($job->updated_at ?? $job->booking_date)) }}</span>
                                            <span class="text-slate-300">|</span>
                                            <span><i class="far fa-clock text-slate-400"></i> {{ date('h:i A', strtotime($job->updated_at ?? $job->booking_time)) }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 w-full">
                            <h3 class="text-lg font-bold text-slate-800 m-0 truncate">{{ $job->customer_name }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="text-xs font-medium text-slate-500"><i class="fas fa-phone-alt text-slate-400 me-1"></i> {{ $job->contact }}</span>
                                
                                @if(isset($job->is_walk_in) && $job->is_walk_in == 1)
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-md border" style="color: var(--teal); background: var(--teal-tint); border-color: #BEEADD;" title="អ្នកបញ្ចូល / Added By">
                                        <i class="fas fa-user-edit me-1"></i> {{ $job->added_by ?? 'Staff' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mb-5 w-full">
                            <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-md text-xs font-medium text-slate-700 shadow-sm"><i class="fas fa-car text-slate-400 me-1.5"></i> {{ $job->model }}</span>
                            <span class="px-3 py-1.5 rounded-md text-xs font-medium shadow-sm" style="background: var(--brand-red-tint); border: 1px solid #F3CDCF; color: var(--brand-red);"><i class="fas fa-wrench me-1.5" style="color: var(--brand-red); opacity: 0.7;"></i> {{ $job->service_type }}</span>
                        </div>

                        <!-- 🌟 Status Stepper — replaces empty space with the job's real position in the workflow 🌟 -->
                        <div class="status-stepper">
                            <div class="status-step {{ $stepIndex > 0 ? 'done' : 'active' }}">
                                <div class="step-dot"></div>
                                <div class="step-label">Booked</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 1 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex > 1 ? 'done' : ($stepIndex == 1 ? 'active' : '') }}">
                                <div class="step-dot"></div>
                                <div class="step-label">Arrived</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 2 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex > 2 ? 'done' : ($stepIndex == 2 ? ($isPaused ? 'paused' : 'active') : '') }}">
                                <div class="step-dot"></div>
                                <div class="step-label">{{ $isPaused && $stepIndex == 2 ? 'Paused' : 'In Progress' }}</div>
                            </div>
                            <div class="step-connector {{ $stepIndex >= 3 ? 'done' : '' }}"></div>

                            <div class="status-step {{ $stepIndex >= 3 ? 'done' : '' }}">
                                <div class="step-dot"></div>
                                <div class="step-label">Completed</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 w-full mt-auto mb-3">
                            <div class="rounded-xl p-3 border text-left" style="background: var(--steel-tint); border-color: #DDE4EC;">
                                <div class="eyebrow-label">Service Advisor</div>
                                <div class="text-xs font-semibold text-slate-700 truncate"><i class="fas fa-user-tie me-1.5" style="color: var(--active-blue);"></i> {{ $job->sa_name ?? 'Pending' }}</div>
                            </div>
                            <div class="rounded-xl p-3 border text-left" style="background: var(--teal-tint); border-color: #BEEADD;">
                                <div class="eyebrow-label">Technician</div>
                                <div class="text-xs font-semibold truncate" style="color: var(--teal);"><i class="fas fa-user-cog me-1.5" style="color: var(--teal);"></i> {{ $job->technician_name ?? 'Pending' }}</div>
                            </div>
                        </div>

                        <div class="mb-3 flex flex-col gap-2 w-full text-left">
                            @if(!empty($job->remark))
                            <div class="p-2 rounded-lg" style="background-color: var(--amber-tint); border: 1px solid #F0DDB8; border-left: 4px solid var(--amber); font-size: 0.75rem;">
                                <div style="color: var(--amber); font-weight: 700; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.4px; font-size: 0.68rem;">
                                    <i class="fas fa-quote-left me-1 opacity-75"></i> Remark
                                </div>
                                <div style="color: var(--amber-ink); font-style: italic; line-height: 1.4;">
                                    {{ $job->remark }}
                                </div>
                            </div>
                            @endif

                            @if(!empty($job->note))
                            <div class="p-2 rounded-lg" style="background-color: var(--steel-tint); border: 1px solid #DDE4EC; border-left: 4px solid var(--steel); font-size: 0.75rem;">
                                <div style="color: var(--steel); font-weight: 700; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.4px; font-size: 0.68rem;">
                                    <i class="fas fa-sticky-note me-1 opacity-75"></i> Note
                                </div>
                                <div style="color: var(--text-dark); line-height: 1.4;">
                                    {{ $job->note }}
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>

                    <div class="bg-white border-t border-slate-100 p-5 text-center w-full mt-auto">
                        @if($job->status == 'In Progress')
                            <div class="mb-4 led-screen"><span class="text-[9px] font-medium text-slate-400 uppercase tracking-widest block mb-1">Repair Duration</span><div class="digital-timer" data-seconds="{{ $elapsedSeconds }}">00:00:00</div></div>
                            <div class="flex justify-center items-center gap-3">
                                <button type="button" class="btn-action btn-outline" onclick="confirmAction('hold', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-pause"></i></button>
                                <button type="button" class="btn-action btn-teal flex-grow" onclick="confirmAction('finish', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-check-circle"></i> Finish Job</button>
                            </div>
                        @elseif($job->status == 'Hold')
                            <div class="mb-4 led-screen"><span class="text-[9px] font-medium text-slate-400 uppercase tracking-widest block mb-1">Paused Duration</span><div class="digital-timer timer-frozen">{{ $job->paused_duration ?? '00:00:00' }}</div></div>
                            <button type="button" class="btn-action btn-navy w-full" onclick="confirmAction('resume', '{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-play"></i> Resume Work</button>
                        @elseif($job->status == 'Arrived')
                            <!-- 🌟 បញ្ជូន Booking Date ទៅក្នុង Function Start Job 🌟 -->
                            <button type="button" class="btn-action btn-navy w-full mt-2" onclick="startJob('{{ $job->id }}', '{{ $job->plate }}', '{{ $jobBranchId }}', '{{ addslashes($job->sa_name ?? '') }}', '{{ addslashes($job->technician_name ?? '') }}', '{{ $onlyDate }}')">
                                <i class="fas fa-rocket"></i> ចាប់ផ្ដើមការងារ (Start)
                            </button>
                        @else
                            <button type="button" class="btn-action btn-amber w-full mt-2" onclick="markAsArrived('{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-map-marker-alt"></i> អតិថិជនមកដល់ (Arrived)</button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-24 w-full">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-5 shadow-inner" style="background: var(--steel-tint);"><i class="fas fa-box-open fa-3x" style="color: #aab6c6;"></i></div>
                    <h4 class="text-slate-600 font-bold text-xl">មិនមានការងារនៅឡើយទេ</h4>
                </div>
            @endforelse
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
    
    const dbErrorMsg = {!! json_encode($dbErrorMsg ?? '') !!};
    const fallbackStaffs = {!! json_encode(array_values($allStaffsFlat ?? [])) !!};
    const saByBranch = {!! json_encode($saByBranch ?? (object)[]) !!};
    const techByBranch = {!! json_encode($techByBranch ?? (object)[]) !!};
    
    const isManager = {{ $isManager ? 'true' : 'false' }};
    
    let busyTechs = {!! json_encode($busyTechnicians ?? []) !!};

    // 🌟 កូដសម្រាប់ផ្លាស់ប្តូរ Layout Card (Grid Configurator) 🌟
    function setGrid(cols) {
        const grid = document.getElementById('jobsGrid');
        const buttons = document.querySelectorAll('.grid-btn');

        // កែពណ៌ប៊ូតុង
        buttons.forEach(btn => {
            btn.classList.remove('grid-btn-active');
            btn.classList.add('text-slate-500', 'hover:bg-slate-200');
            if(btn.dataset.col == cols) {
                btn.classList.add('grid-btn-active');
                btn.classList.remove('text-slate-500', 'hover:bg-slate-200');
            }
        });

        // ផ្លាស់ប្តូរ Class របស់ Grid
        let classMap = {
            '1': 'grid-cols-1',
            '2': 'grid-cols-2',
            '3': 'grid-cols-3',
            '4': 'grid-cols-4',
            'auto': 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
        };
        
        grid.className = `grid gap-6 w-full ${classMap[cols]}`;
        
        // រក្សាទុកជម្រើសរបស់អ្នកប្រើប្រាស់ក្នុង LocalStorage
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
        // 🌟 អានជម្រើស Grid ពី LocalStorage ពេល Load Page 🌟
        let pref = localStorage.getItem('workshopGridPref') || 'auto';
        setGrid(pref);

        updateRepairTimers(); setInterval(updateRepairTimers, 1000);
        setInterval(() => { $('#liveClock').text(new Date().toLocaleTimeString('en-US', {hour12: true})); }, 1000);
        applyFilters(); 
    });

    function applyFilters() {
        let searchValue = $('#plateSearch').val().toUpperCase(); 
        let branchSelect = $('select[name="branch"]');
        let branchValue = branchSelect.length ? branchSelect.val().toString().toLowerCase() : 'all'; 

        $('.job-item').each(function() {
            let rowPlate = $(this).data('plate').toString().toUpperCase(); 
            let rowBranchId = String($(this).data('branch-id')).toLowerCase(); 
            let matchSearch = rowPlate.includes(searchValue); 
            let matchBranch = (branchValue === 'all' || branchValue === '' || branchValue === undefined || rowBranchId === branchValue || rowBranchId.includes(branchValue) || branchValue.includes(rowBranchId));
            $(this).toggle(matchSearch && matchBranch);
        });
    }

    function refreshDashboard() {
        $.get(window.location.href, function(data) {
            let newGrid = $(data).find('#jobsGrid').html(); 
            let newCount = $(data).find('#totalCount').text();
            
            $('#jobsGrid').html(newGrid); 
            $('#totalCount').text(newCount);
            
            let newBusyData = $(data).find('#busyTechsData').text();
            if (newBusyData) { busyTechs = JSON.parse(newBusyData); }
            updateRepairTimers(); applyFilters(); 
        });
    }

    function confirmAction(type, id, plate) {
        let configs = { hold: { t: 'ផ្អាកការងារសិន?', i: 'warning', b: 'យល់ព្រមផ្អាក', c: '#45566E' }, resume: { t: 'បន្តការងារនេះ?', i: 'info', b: 'បន្តការងារ', c: '#0B2A52' }, finish: { t: 'ការងាររួចរាល់ហើយ?', i: 'success', b: 'បញ្ជាក់រួចរាល់', c: '#0E9C79' } };
        Swal.fire({
            title: configs[type].t, text: "ស្លាកលេខ: " + plate, icon: configs[type].i, showCancelButton: true, confirmButtonColor: configs[type].c, cancelButtonColor: '#cbd5e1', confirmButtonText: configs[type].b, cancelButtonText: 'បោះបង់', customClass: { popup: 'rounded-2xl' }
        }).then(res => {
            if(res.isConfirmed) {
                Swal.fire({ title: 'កំពុងដំណើរការ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({ url: "{{ url('workshop') }}/" + type + "/" + id, type: 'POST', success: function(response) { Swal.close(); refreshDashboard(); }, error: function() { Swal.close(); refreshDashboard(); } });
            }
        });
    }

    function startJob(id, plate, jobBranchId, currentSA, currentTech, bookingDate) {
        if (dbErrorMsg) {
            Swal.fire({ icon: 'error', title: 'បញ្ហា Database!', text: dbErrorMsg });
            return;
        }

        jobBranchId = String(jobBranchId || '').trim().toLowerCase();
        if (isNaN(jobBranchId)) {
            if (jobBranchId.includes('hq') || jobBranchId.includes('head')) jobBranchId = '13';
            else if (jobBranchId.includes('50m') || jobBranchId.includes('50')) jobBranchId = '11';
            else if (jobBranchId.includes('sr') || jobBranchId.includes('siem')) jobBranchId = '12';
            else if (jobBranchId.includes('btb') || jobBranchId.includes('battambang')) jobBranchId = '10';
            else jobBranchId = '13'; 
        }

        let availableSAs = [];
        let availableTechs = [];

        if (isManager) {
            Object.values(saByBranch).forEach(arr => availableSAs.push(...arr));
            Object.values(techByBranch).forEach(arr => availableTechs.push(...arr));
        } else {
            availableSAs = saByBranch[jobBranchId] || [];
            availableTechs = techByBranch[jobBranchId] || [];
        }

        availableSAs = [...new Set(availableSAs)].filter(Boolean).sort((a, b) => String(a).localeCompare(String(b)));
        availableTechs = [...new Set(availableTechs)].filter(Boolean).sort((a, b) => String(a).localeCompare(String(b)));

        let isSALocked = currentSA ? true : false;
        let saLockAttr = isSALocked ? 'disabled' : '';
        let saLockStyles = isSALocked ? 'background-color: #f1f5f9; cursor: not-allowed; color: #002663; font-weight: 700;' : '';

        let saOpts = '';
        if (isSALocked) {
            saOpts = `<option value="${currentSA}" selected>${currentSA} (ជម្រើសស្វ័យប្រវត្តិ)</option>`;
        } else {
            saOpts = `<option value="" disabled selected>-- សូមជ្រើសរើស SA --</option>`;
            if(availableSAs.length > 0) {
                availableSAs.forEach(emp => { saOpts += `<option value="${emp}">${emp}</option>`; });
            } else { saOpts += `<option value="" disabled>-- គ្មាន SA ទេ --</option>`; }
        }

        let techOpts = `<option value="" disabled ${!currentTech ? 'selected' : ''}>-- សូមជ្រើសរើសជាងជួសជុល --</option>`;
        if(availableTechs.length > 0) {
            availableTechs.forEach(emp => {
                let isBusy = (typeof busyTechs !== 'undefined' && busyTechs.includes(emp));
                let busyMark = isBusy ? ' 🔴 (ជាប់រវល់)' : ' 🟢 (ទំនេរ)';
                let isSelected = (emp === currentTech) ? 'selected' : ''; 
                techOpts += `<option value="${emp}" data-busy="${isBusy}" ${isSelected}>${emp}${busyMark}</option>`;
            });
        } else { techOpts += `<option value="" disabled>-- គ្មានមេជាងទេ --</option>`; }

        Swal.fire({
            title: 'ចាប់ផ្ដើមការងារ: ' + plate,
            html: `
                <div class="text-left mb-1 mt-3"><label class="text-xs font-medium text-slate-500 uppercase">កាលបរិច្ឆេទកក់ (Booking Date)</label></div>
                <input type="date" id="booking_date" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 mb-3 text-sm outline-none font-bold text-slate-700 focus:border-blue-500 transition-colors" value="${bookingDate}">

                <div class="text-left mb-1 mt-1"><label class="text-xs font-medium text-slate-500 uppercase">Service Advisor</label></div>
                <select id="sa_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 mb-3 text-sm outline-none font-medium text-slate-700 focus:border-blue-500 transition-colors" ${saLockAttr} style="${saLockStyles}">${saOpts}</select>
                
                <div class="text-left mb-1 mt-2"><label class="text-xs font-medium text-slate-500 uppercase">Technician</label></div>
                <select id="technician_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm outline-none font-medium text-slate-700 focus:border-blue-500 transition-colors">${techOpts}</select>
            `,
            showCancelButton: true, confirmButtonColor: '#002663', cancelButtonColor: '#94a3b8', confirmButtonText: 'ចាប់ផ្ដើម', cancelButtonText: 'បោះបង់', customClass: { popup: 'rounded-2xl' },
            preConfirm: () => {
                const bDate = document.getElementById('booking_date').value;
                const saName = document.getElementById('sa_name').value;
                const techSelect = document.getElementById('technician_name');
                const techName = techSelect.value;
                
                if (!bDate) { Swal.showValidationMessage('សូមជ្រើសរើសកាលបរិច្ឆេទសិន!'); return false; }
                if (!saName || !techName) { Swal.showValidationMessage('សូមជ្រើសរើសមេជាងជួសជុលសិន!'); return false; }
                
                const isTechBusy = techSelect.options[techSelect.selectedIndex].getAttribute('data-busy') === 'true';
                return { booking_date: bDate, sa_name: saName, technician_name: techName, is_busy: isTechBusy };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let data = result.value;
                const runStartJobAjax = () => {
                    Swal.fire({ title: 'កំពុងចាប់ផ្ដើម...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    $.ajax({
                        url: `{{ url('workshop/start') }}/${id}`, type: 'POST', 
                        // 🌟 បញ្ជូន Booking Date ទៅកាន់ Controller 🌟
                        data: { booking_date: data.booking_date, sa_name: data.sa_name, technician_name: data.technician_name },
                        success: function() { Swal.close(); refreshDashboard(); }, 
                        error: function(xhr) { Swal.fire('Error', xhr.responseJSON ? xhr.responseJSON.message : 'មានបញ្ហាក្នុងការរក្សាទុក!', 'error'); refreshDashboard(); }
                    });
                };
                if (data.is_busy) {
                    Swal.fire({ title: 'ជាងកំពុងជាប់រវល់!', text: `តើចង់ដាក់ការងារនេះអោយគាត់មែនទេ?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#e31e24', confirmButtonText: 'បាទ/ចាស បន្ត', customClass: { popup: 'rounded-2xl' }
                    }).then((r) => { if (r.isConfirmed) runStartJobAjax(); });
                } else { runStartJobAjax(); }
            }
        });
    }

    function markAsArrived(id, plate) {
        Swal.fire({
            title: 'បញ្ជាក់ការមកដល់', text: `តើអតិថិជនពិតជាបានមកដល់ហើយមែនទេ? (${plate}) (ប្រព័ន្ធនឹងចាប់យកឈ្មោះអ្នកជា SA អូតូ)`, icon: 'question', showCancelButton: true, confirmButtonColor: '#B9750F', cancelButtonColor: '#cbd5e1', confirmButtonText: 'មកដល់ហើយ', cancelButtonText: 'បោះបង់', customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'កំពុងដំណើរការ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({ 
                    url: `{{ url('workshop/arrive') }}/${id}`, type: 'POST', data: { _token: '{{ csrf_token() }}' },
                    success: function() { Swal.close(); refreshDashboard(); }, 
                    error: function(xhr) { Swal.fire('បរាជ័យ', xhr.responseJSON ? xhr.responseJSON.message : 'មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ប្រព័ន្ធ!', 'error'); refreshDashboard(); } 
                });
            }
        });
    }
</script>
@endsection