@extends('layouts.homePages')

@section('content')

@php
    $user = auth()->user();
    $roleId = (int)($user->role_id ?? 0);
    
    // កំណត់សិទ្ធិ: 1=Admin, 6=IT, 11=Group Manager
    $isManager = in_array($roleId, [1, 6, 11]);

    // ទាញឈ្មោះសាខាសម្រាប់អ្នកប្រើប្រាស់ធម្មតា
    $currentBranchName = 'សាខារបស់អ្នក';
    if (!$isManager && isset($branches)) {
        $myBranch = collect($branches)->firstWhere('id', $user->branch_id);
        $currentBranchName = $myBranch ? trim(str_ireplace(['MG ', 'Mg '], '', $myBranch->branch_name)) : 'សាខារបស់អ្នក';
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    /* ============================================================
       DESIGN TOKENS
       A restrained, meaningful color system:
       navy = informational · teal = on-target/positive
       amber = needs attention · rust = backlog/concern
       brand red = reserved for true alerts (overtime)
       ============================================================ */
    :root {
        --ink-navy: #0B2A52;
        --navy-light: #123B6E;
        --steel: #45566E;
        --steel-tint: #EEF2F7;
        --teal: #0E9C79;
        --teal-tint: #E3F6F0;
        --amber: #B9750F;
        --amber-tint: #FBF1E1;
        --rust: #C24A3B;
        --rust-tint: #FBEAE8;
        --brand-red: #D0262C;
        --brand-red-tint: #FBEAEA;
        --bg-main: #F5F7FA;
        --border-light: #E4E9F0;
        --text-dark: #16233B;
        --text-muted: #5B6B84;

        /* legacy aliases kept so nothing referencing brand colors breaks */
        --mg-blue: #0B2A52;
        --mg-red: #D0262C;
    }

    /* 🌟 FIT SCREEN APP LAYOUT 🌟 */
    body {
        margin: 0;
        padding: 0;
        overflow: hidden !important;
        background-color: var(--bg-main) !important;
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
        color: var(--text-dark);
    }

    .content-fluid-wrapper {
        height: 100vh;
        max-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* 🌟 Header Section 🌟 */
    .dashboard-header { 
        background: #ffffff; 
        border-bottom: 1px solid var(--border-light); 
        padding: 0.85rem 1.5rem; 
        display: flex; 
        flex-wrap: wrap; 
        justify-content: space-between; 
        align-items: center; 
        flex-shrink: 0;
        z-index: 50;
        box-shadow: 0 1px 2px rgba(16,24,40,0.04);
    }

    /* 🌟 Main Scrollable Area 🌟 */
    .main-dashboard-content {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1.25rem 1.5rem;
        padding-bottom: 2rem;
    }

    /* 🌟 Custom Scrollbar for all areas 🌟 */
    .main-dashboard-content::-webkit-scrollbar, 
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .main-dashboard-content::-webkit-scrollbar-track, 
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .main-dashboard-content::-webkit-scrollbar-thumb, 
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .main-dashboard-content::-webkit-scrollbar-thumb:hover, 
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* 🌟 Filter Section 🌟 */
    .filter-group {
        display: flex; align-items: center; gap: 8px; 
        background: #f8fafc; border: 1px solid #dde3ec; 
        border-radius: 8px; padding: 4px 10px; 
        transition: all 0.2s ease;
    }
    .filter-group:focus-within, .filter-group:hover { border-color: var(--ink-navy); background: #ffffff; }
    .filter-select { background: transparent; border: none; outline: none; font-size: 0.8rem; font-weight: 700; color: var(--text-dark); cursor: pointer; }

    /* 🌟 KPI Cards (Compact for Fit Screen) 🌟 */
    .kpi-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--border-light);
        padding: 0.9rem 1.05rem; transition: all 0.2s ease;
        display: flex; flex-direction: column; justify-content: center;
        box-shadow: 0 1px 2px rgba(16,24,40,0.03);
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(16,24,40,0.06); }

    /* 🌟 Clean Data Cards 🌟 */
    .clean-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--border-light);
        padding: 1.05rem; display: flex; flex-direction: column;
        box-shadow: 0 1px 2px rgba(16,24,40,0.03);
        height: 100%;
        transition: all 0.2s ease;
    }
    .clean-card:hover { border-color: #d3dae4; box-shadow: 0 8px 20px rgba(16,24,40,0.05); }
    
    .card-header-title {
        font-size: 0.9rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.85rem;
        display: flex; align-items: center; gap: 9px; flex-shrink: 0;
        letter-spacing: -0.01em;
    }
    .icon-circle { width: 27px; height: 27px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }

    /* 🌟 Modern Soft Tables 🌟 */
    .clean-table { width: 100%; text-align: left; border-collapse: separate; border-spacing: 0; }
    .sticky-header th { position: sticky; top: 0; background-color: #ffffff; z-index: 10; box-shadow: 0 1px 0 var(--border-light); }
    .clean-table th { color: var(--text-muted); font-size: 0.63rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; padding: 6px 8px; }
    .clean-table td { padding: 7px 8px; font-size: 0.8rem; color: #334155; border-bottom: 1px dashed #eef1f5; font-weight: 600; font-variant-numeric: tabular-nums; }
    .clean-table tbody tr:hover td { background-color: #f8fafc; }

    /* 🌟 QTY Badge Soft 🌟 */
    .qty-pill { 
        background: var(--steel-tint); color: var(--steel); padding: 2px 9px; border-radius: 6px; 
        font-weight: 800; font-size: 0.75rem; display: inline-block; min-width: 32px; text-align: center; 
        border: 1px solid #e2e8f0; font-variant-numeric: tabular-nums;
    }
    .qty-pill.highlight { background: #EAF0F9; color: var(--ink-navy); border-color: #cbd9ec; }

    /* 🌟 Progress & Leaderboard 🌟 */
    .rank-badge { width: 24px; height: 24px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 11px; }
    .rank-1 { background: #FBF1D2; color: #92660B; border: 1px solid #F1DD9C; }
    .rank-2 { background: #EEF1F5; color: var(--steel); border: 1px solid #dbe1e9; }
    .rank-3 { background: #F6E4DA; color: #9A4E28; border: 1px solid #edcdb8; }
    .rank-other { background: transparent; color: #a1acbb; }
    
    .progress-bar-container { background: #eef1f5; border-radius: 99px; height: 4px; overflow: hidden; margin-top: 4px; }
    .progress-bar-fill { height: 100%; border-radius: 99px; transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1); }

    .custom-scrollbar { overflow-y: auto; padding-right: 4px; }
</style>

<!-- 🌟 MAIN CONTENT FLUID WRAPPER 🌟 -->
<div class="content-fluid-wrapper">
    
    <!-- 🌟 Compact Header & Filter (Fixed Top) 🌟 -->
    <div class="dashboard-header animate__animated animate__fadeInDown">
        <div class="flex items-center gap-3">
            <h1 class="text-[1.1rem] font-black tracking-tight m-0" style="color: var(--ink-navy);">
                <i class="fas fa-chart-line mr-1" style="color: var(--brand-red);"></i> Performance Overview
            </h1>
            <div class="h-4 w-px bg-slate-200 hidden md:block"></div>
            <p class="text-slate-400 text-[9px] font-bold uppercase tracking-[1.5px] m-0 hidden md:block">Daily Insights</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('services.performance') }}" id="filterForm" class="m-0 d-flex flex-wrap align-items-center gap-2">
                <div class="filter-group m-0 py-1 px-2">
                    @if($isManager)
                        <i class="fas fa-map-marker-alt text-slate-400 text-[10px]"></i>
                        <select name="branch" class="filter-select text-[11px]" onchange="document.getElementById('filterForm').submit();" style="width: 120px;">
                            <option value="all">គ្រប់សាខាទាំងអស់</option>
                            @if(isset($branches))
                                @foreach($branches as $b)
                                    @if(stripos($b->branch_name, 'service') !== false)
                                        <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>
                                            {{ trim(str_ireplace(['MG ', 'Mg ', ' Service', ' Showroom'], '', $b->branch_name)) }}
                                        </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    @else
                        <i class="fas fa-map-marker-alt text-[10px]" style="color: var(--brand-red);"></i>
                        <span class="text-[11px] font-bold px-1" style="color: var(--ink-navy);">{{ $currentBranchName }}</span>
                        <input type="hidden" name="branch" value="{{ $user->branch_id }}">
                    @endif
                </div>

                <!-- 🌟 From Date Filter 🌟 -->
                <div class="filter-group m-0 py-1 px-2">
                    <span class="text-[10px] font-bold text-slate-400">FROM:</span>
                    <input type="date" name="from_date" value="{{ $fromDate ?? date('Y-m-01') }}" onchange="document.getElementById('filterForm').submit();" class="filter-select text-[11px]" style="color: var(--ink-navy);">
                </div>

                <!-- 🌟 To Date Filter 🌟 -->
                <div class="filter-group m-0 py-1 px-2">
                    <span class="text-[10px] font-bold text-slate-400">TO:</span>
                    <input type="date" name="to_date" value="{{ $toDate ?? date('Y-m-d') }}" onchange="document.getElementById('filterForm').submit();" class="filter-select text-[11px]" style="color: var(--ink-navy);">
                </div>

                @if(request()->has('from_date') || request()->has('to_date') || (request()->has('branch') && request('branch') !== 'all'))
                    <a href="{{ route('services.performance') }}" class="text-slate-400 hover:text-[color:var(--brand-red)] transition-colors text-sm ml-1" title="Clear Filter"><i class="fas fa-undo-alt"></i></a>
                @endif
            </form>

            <a href="{{ route('services.index') }}" class="btn text-white shadow-sm d-flex align-items-center gap-2" style="background: var(--ink-navy); border-radius: 8px; padding: 5px 12px; font-weight: 700; font-size: 0.75rem; margin-left: 4px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- 🌟 Main Dashboard Area (Scrollable internally) 🌟 -->
    <div class="main-dashboard-content animate__animated animate__fadeInUp">
        
        <div class="mb-3">
            <h3 class="m-0 font-black text-[1rem] tracking-tight text-slate-800">Daily Statistics <span class="text-slate-400 text-xs font-normal ms-2 border-l border-slate-300 pl-2">{{ date('d M, Y', strtotime($selectedDate)) }}</span></h3>
        </div>

        <!-- 🌟 Row 1: Top KPI Cards 🌟 -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 mb-4">
            <div class="kpi-card border-none text-white" style="background: linear-gradient(120deg, var(--ink-navy), var(--navy-light)); box-shadow: 0 4px 14px rgba(11,42,82,0.25);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-wide mb-1" style="color:#B9C7DC;">Total Throughput</p>
                        <h3 class="text-xl font-black m-0 tabular-nums">{{ number_format($totalThroughput ?? 0) }}</h3>
                    </div>
                    <div class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-xs" style="color:#B9C7DC;"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
            <div class="kpi-card" style="border-left: 3px solid var(--steel);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">Total Cars In</p>
                        <h3 class="text-xl font-black text-slate-800 m-0 tabular-nums">{{ number_format($carsIn ?? 0) }}</h3>
                    </div>
                    <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs" style="background: var(--steel-tint); color: var(--steel);"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
            <div class="kpi-card" style="border-left: 3px solid var(--teal);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">Cars Delivery</p>
                        <h3 class="text-xl font-black text-slate-800 m-0 tabular-nums">{{ number_format($carsDelivery ?? 0) }}</h3>
                    </div>
                    <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs" style="background: var(--teal-tint); color: var(--teal);"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="kpi-card" style="border-left: 3px solid var(--amber);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">Today Pending</p>
                        <h3 class="text-xl font-black text-slate-800 m-0 tabular-nums">{{ number_format($todayPending ?? 0) }}</h3>
                    </div>
                    <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs" style="background: var(--amber-tint); color: var(--amber);"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
            <div class="kpi-card" style="border-left: 3px solid var(--rust);">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mb-1">Total Backlog</p>
                        <h3 class="text-xl font-black text-slate-800 m-0 tabular-nums">{{ number_format($totalPending ?? 0) }}</h3>
                    </div>
                    <div class="w-7 h-7 rounded-md flex items-center justify-center text-xs" style="background: var(--rust-tint); color: var(--rust);"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        <!-- 🌟 Row 2: Booking Breakdown & Parts 🌟 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-3">
            <div class="lg:col-span-2 clean-card" style="height: 220px;">
                <div class="card-header-title">
                    <div class="icon-circle" style="background: var(--steel-tint); color: var(--steel);"><i class="fas fa-users"></i></div>
                    <span>SA Booking vs Walk-In Summary</span>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="clean-table sticky-header">
                        <thead>
                            <tr>
                                <th>Service Advisor Name</th>
                                <th class="text-center w-20"><i class="fas fa-globe text-slate-400"></i> Booking</th>
                                <th class="text-center w-20"><i class="fas fa-walking text-slate-400"></i> Walk-In</th>
                                <th class="text-center w-20">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totBooking = 0; $totWalkin = 0; $grandTotal = 0; @endphp
                            @if(isset($saDailyStats) && count($saDailyStats) > 0)
                                @foreach($saDailyStats as $sa => $stats)
                                    @php 
                                        $totBooking += $stats['booking']; 
                                        $totWalkin += $stats['walkin']; 
                                        $grandTotal += ($stats['booking'] + $stats['walkin']);
                                    @endphp
                                    <tr>
                                        <td>{{ $sa }}</td>
                                        <td class="text-center"><span class="qty-pill">{{ $stats['booking'] ?: '-' }}</span></td>
                                        <td class="text-center"><span class="qty-pill">{{ $stats['walkin'] ?: '-' }}</span></td>
                                        <td class="text-center font-black text-slate-800">{{ ($stats['booking'] + $stats['walkin']) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4" class="text-center py-4 text-slate-400 text-xs">មិនមានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if($grandTotal > 0)
                <div class="pt-2 mt-2 border-t border-slate-100 flex-shrink-0">
                    <table class="clean-table m-0">
                        <tr>
                            <td class="text-end font-black text-slate-400 uppercase text-[9px] border-none py-0">Total</td>
                            <td class="text-center w-20 border-none py-0"><span class="qty-pill highlight">{{ $totBooking ?: 0 }}</span></td>
                            <td class="text-center w-20 border-none py-0"><span class="qty-pill highlight">{{ $totWalkin ?: 0 }}</span></td>
                            <td class="text-center w-20 border-none py-0 font-black text-[13px]" style="color: var(--ink-navy);">{{ $grandTotal }}</td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1 clean-card" style="height: 220px;">
                <div class="card-header-title">
                    <div class="icon-circle" style="background: var(--steel-tint); color: var(--steel);"><i class="fas fa-box-open"></i></div>
                    <span>Parts Order Summary</span>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="clean-table">
                        <tbody>
                            <tr><td>Order Part (Local)</td><td class="text-end w-16"><span class="qty-pill">{{ $totalOrderPartLocal ?? 0 }}</span></td></tr>
                            <tr><td>Order Part (Sea)</td><td class="text-end"><span class="qty-pill">{{ $totalOrderPartSea ?? 0 }}</span></td></tr>
                            <tr><td>Warranty Order</td><td class="text-end"><span class="qty-pill">{{ $totalPartWarranty ?? 0 }}</span></td></tr>
                            <tr><td>Retail Order</td><td class="text-end"><span class="qty-pill">{{ $totalPartRetail ?? 0 }}</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 🌟 Row 3: Pending Details (3 Columns) 🌟 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
            <div class="clean-card" style="height: 200px;">
                <div class="card-header-title">
                    <div class="icon-circle" style="background: var(--amber-tint); color: var(--amber);"><i class="fas fa-exclamation-circle"></i></div>
                    <span>Pending Description</span>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="clean-table sticky-header">
                        <thead><tr><th>Description</th><th class="text-center w-16">QTY</th></tr></thead>
                        <tbody>
                            @if(isset($pendingDescStats) && count(array_filter($pendingDescStats)) > 0)
                                @foreach($pendingDescStats as $desc => $qty)
                                    @if($qty > 0)
                                    <tr><td>{{ $desc }}</td><td class="text-center"><span class="qty-pill">{{ $qty }}</span></td></tr>
                                    @endif
                                @endforeach
                            @else
                                <tr><td colspan="2" class="text-center py-4 text-slate-400 text-xs">គ្មានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clean-card" style="height: 200px;">
                <div class="card-header-title">
                    <div class="icon-circle" style="background: var(--amber-tint); color: var(--amber);"><i class="fas fa-user-clock"></i></div>
                    <span>SA Pending</span>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="clean-table sticky-header">
                        <thead><tr><th>SA Name</th><th class="text-center w-16">QTY</th></tr></thead>
                        <tbody>
                            @if(isset($saPendingStats) && count($saPendingStats) > 0)
                                @foreach($saPendingStats as $sa => $qty)
                                    <tr><td>{{ $sa }}</td><td class="text-center"><span class="qty-pill">{{ $qty }}</span></td></tr>
                                @endforeach
                            @else
                                <tr><td colspan="2" class="text-center py-4 text-slate-400 text-xs">គ្មានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clean-card" style="height: 200px;">
                <div class="card-header-title">
                    <div class="icon-circle" style="background: var(--amber-tint); color: var(--amber);"><i class="fas fa-store-alt"></i></div>
                    <span>Branch Pending</span>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="clean-table sticky-header">
                        <thead><tr><th>Branch Name</th><th class="text-center w-16">QTY</th></tr></thead>
                        <tbody>
                            @if(isset($branchPendingStats) && count($branchPendingStats) > 0)
                                @foreach($branchPendingStats as $branch => $qty)
                                    <tr><td class="font-bold text-slate-700">{{ strtoupper($branch) }} WS</td><td class="text-center"><span class="qty-pill">{{ $qty }}</span></td></tr>
                                @endforeach
                            @else
                                <tr><td colspan="2" class="text-center py-4 text-slate-400 text-xs">គ្មានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 🌟 Row 4: Leaderboards 🌟 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
            <!-- Top SA -->
            <div class="clean-card" style="height: 210px;">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded flex items-center justify-center text-xs" style="background: var(--steel-tint); color: var(--ink-navy);"><i class="fas fa-trophy"></i></div>
                    <h2 class="text-[0.85rem] font-black text-slate-800 m-0">Top Service Advisors</h2>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <div class="space-y-2 pe-1">
                        @forelse($topSA as $index => $sa)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1';
                                elseif($index == 1) $rankClass = 'rank-2';
                                elseif($index == 2) $rankClass = 'rank-3';
                                
                                $maxTotal = $topSA[0]->total ?: 1;
                                $percentage = ($sa->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-bold text-slate-700 text-[0.75rem]">{{ $sa->sa_name }}</span>
                                        <span class="font-black text-xs tabular-nums" style="color: var(--ink-navy);">{{ $sa->total }}</span>
                                    </div>
                                    <div class="progress-bar-container"><div class="progress-bar-fill" style="width: {{ $percentage }}%; background: var(--ink-navy);"></div></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400"><p class="font-semibold text-[10px] m-0">No Data</p></div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Tech -->
            <div class="clean-card" style="height: 210px;">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded flex items-center justify-center text-xs" style="background: var(--brand-red-tint); color: var(--brand-red);"><i class="fas fa-medal"></i></div>
                    <h2 class="text-[0.85rem] font-black text-slate-800 m-0">Top Technicians</h2>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <div class="space-y-2 pe-1">
                        @forelse($topTech as $index => $tech)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1';
                                elseif($index == 1) $rankClass = 'rank-2';
                                elseif($index == 2) $rankClass = 'rank-3';
                                
                                $maxTotal = $topTech[0]->total ?: 1;
                                $percentage = ($tech->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-bold text-slate-700 text-[0.75rem]">{{ $tech->technician_name }}</span>
                                        <span class="font-black text-xs tabular-nums" style="color: var(--brand-red);">{{ $tech->total }}</span>
                                    </div>
                                    <div class="progress-bar-container"><div class="progress-bar-fill" style="width: {{ $percentage }}%; background: var(--brand-red);"></div></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400"><p class="font-semibold text-[10px] m-0">No Data</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    <!-- 🌟 Row 5: Technician Working Hours (KPI & OT Tracker) 🌟 -->
    <div class="mb-2 mt-4">
        <h3 class="m-0 font-black text-[1rem] tracking-tight text-slate-800">Technician KPI Tracker <span class="text-slate-400 text-xs font-normal ms-2 border-l border-slate-300 pl-2">Daily & Period Overtime</span></h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($techPerformances ?? [] as $tech)
            <div class="clean-card justify-between relative overflow-hidden" style="min-height: 160px;">
                <!-- Badge សម្រាប់អ្នកធ្វើការលើសម៉ោង (OT) ប្រចាំថ្ងៃ -->
                @if($tech->daily_ot_hours > 0 || $tech->daily_ot_minutes > 0)
                    <div class="absolute top-0 right-0 text-white text-[9px] font-black uppercase px-2 py-1 rounded-bl-lg shadow-sm" style="background: var(--brand-red);">
                        <i class="fas fa-fire-alt mr-1"></i> Daily OT
                    </div>
                @endif

                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shadow-inner" style="background: var(--steel-tint); color: var(--ink-navy); border: 1px solid #dbe4f0;">
                        <i class="fas fa-user-wrench"></i>
                    </div>
                    <div class="flex-grow truncate pe-6">
                        <h3 class="text-[13px] font-bold text-slate-800 m-0 truncate" title="{{ $tech->name }}">{{ $tech->name }}</h3>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Technician</span>
                    </div>
                </div>

                <!-- របាយការណ៍ថ្ងៃនេះ (Daily) -->
                <div class="mb-3">
                    <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                        <span>Today Target: 8h</span>
                        <span class="tabular-nums" style="color: var(--ink-navy);">{{ $tech->daily_actual_hours }}h {{ $tech->daily_actual_minutes }}m</span>
                    </div>
                    
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1 relative">
                        <div class="h-1.5 rounded-full progress-bar-fill"
                             style="width: {{ $tech->daily_bar_percent }}%; background: linear-gradient(90deg, var(--navy-light), var(--ink-navy));">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[10px] font-black tabular-nums" style="color: {{ $tech->daily_percent >= 100 ? 'var(--teal)' : '#94a3b8' }};">
                            Daily: {{ number_format($tech->daily_percent, 1) }}%
                        </span>
                        
                        @if($tech->daily_ot_hours > 0 || $tech->daily_ot_minutes > 0)
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded tabular-nums" style="color: var(--brand-red); background: var(--brand-red-tint); border: 1px solid #f3cdcf;">
                                + {{ $tech->daily_ot_hours }}h {{ $tech->daily_ot_minutes }}m
                            </span>
                        @endif
                    </div>
                </div>

                <!-- របាយការណ៍សរុបតាម Period / ១ ខែ (Total Time ជាក់ស្តែង) -->
                <div class="pt-2 border-t border-slate-100 flex justify-between items-end mt-auto">
                    <div>
                        <span class="block text-[9px] font-bold text-slate-400 uppercase mb-0.5">Period KPI (Total)</span>
                        <span class="text-xs font-black tabular-nums" style="color: {{ $tech->monthly_percent >= 100 ? 'var(--teal)' : 'var(--ink-navy)' }};">
                            {{ number_format($tech->monthly_percent, 1) }}%
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-black text-slate-700 leading-none block tabular-nums">
                            {{ $tech->monthly_actual_hours }}<span class="text-[10px] text-slate-400 font-bold mx-0.5">h</span>
                            {{ $tech->monthly_actual_minutes }}<span class="text-[10px] text-slate-400 font-bold">m</span>
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 block mt-0.5">Actual Time</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full clean-card flex flex-col items-center justify-center py-8">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3" style="background: var(--steel-tint);">
                    <i class="fas fa-tools text-2xl" style="color: #aab6c6;"></i>
                </div>
                <p class="text-slate-500 font-bold text-sm m-0">មិនទាន់មានទិន្នន័យការងារក្នុងចន្លោះកាលបរិច្ឆេទនេះទេ</p>
            </div>
        @endforelse
    </div>
        
    </div> <!-- End Main Dashboard Content -->
</div> <!-- End Content Fluid Wrapper -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Animation for all progress bars (Leaderboards & Tech Performance)
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = width; }, 200);
        });
    });
</script>
@endsection