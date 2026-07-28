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
    /* 🌟 Compact & Fit Screen Dashboard Variables 🌟 */
    :root {
        --mg-blue: #002663;
        --mg-red: #e31e24;
        --bg-main: #f1f5f9;
        --border-light: #e2e8f0;
    }

    body, main, .main-content { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-main) !important; 
        color: #334155;
        overflow-x: hidden;
    }

    /* Reduce main padding to fit screen better */
    .container-fluid { padding: 1rem 1.5rem !important; max-width: 1600px; margin: 0 auto; }
    
    /* 🌟 Compact Dashboard Header 🌟 */
    .dashboard-header { 
        background: rgba(255, 255, 255, 0.98); 
        backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--border-light); 
        padding: 0.75rem 1.5rem; 
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; 
        position: sticky; top: 0; z-index: 50; 
    }
    
    /* 🌟 Filter Section 🌟 */
    .filter-group {
        display: flex; align-items: center; gap: 8px; 
        background: #f8fafc; border: 1px solid #cbd5e1; 
        border-radius: 8px; padding: 6px 12px; 
        transition: all 0.2s ease;
    }
    .filter-group:focus-within, .filter-group:hover { 
        border-color: var(--mg-blue); background: #ffffff; 
    }
    .filter-select {
        background: transparent; border: none; outline: none;
        font-size: 0.85rem; font-weight: 700; color: #1e293b; cursor: pointer;
    }
    input[type="date"]::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; }
    input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity: 1; }

    /* 🌟 KPI Cards (Compact) 🌟 */
    .kpi-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--border-light);
        padding: 1rem; transition: all 0.2s ease;
        display: flex; flex-direction: column; justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 12px -2px rgba(0,0,0,0.05); border-color: #cbd5e1; }
    .kpi-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-bottom: 0.5rem; }

    /* 🌟 Clean Data Cards with Scroll 🌟 */
    .clean-card {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--border-light);
        padding: 1rem; height: 100%; display: flex; flex-direction: column;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }
    
    .card-header-title {
        font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 0.75rem;
        display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    }
    .icon-circle { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }

    /* 🌟 Custom Thin Scrollbar for Cards 🌟 */
    .custom-scrollbar {
        overflow-y: auto;
        padding-right: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* 🌟 Modern Soft Tables (Compact) 🌟 */
    .clean-table { width: 100%; text-align: left; border-collapse: separate; border-spacing: 0; }
    .sticky-header th { 
        position: sticky; top: 0; background-color: #ffffff; z-index: 10; 
        box-shadow: 0 1px 0 #e2e8f0; /* Alternative to bottom border for sticky headers */
    }
    .clean-table th { 
        color: #64748b; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; 
        letter-spacing: 0.5px; padding: 6px 8px; 
    }
    .clean-table td { padding: 8px 8px; font-size: 0.85rem; color: #334155; border-bottom: 1px dashed #f1f5f9; font-weight: 600; }
    .clean-table tr:last-child td { border-bottom: none; }
    .clean-table tbody tr:hover td { background-color: #f8fafc; }

    /* 🌟 QTY Badge Soft 🌟 */
    .qty-pill { 
        background: #f1f5f9; color: #475569; padding: 2px 10px; border-radius: 6px; 
        font-weight: 800; font-size: 0.8rem; display: inline-block; min-width: 35px; text-align: center; border: 1px solid #e2e8f0;
    }
    .qty-pill.highlight { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

    /* 🌟 Progress & Leaderboard 🌟 */
    .rank-badge { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 12px; }
    .rank-1 { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
    .rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .rank-other { background: transparent; color: #94a3b8; }

    .progress-bar-container { background: #f1f5f9; border-radius: 99px; height: 5px; overflow: hidden; margin-top: 4px; }
    .progress-bar-fill { height: 100%; border-radius: 99px; transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1); }
    
    /* Utility to limit height */
    .max-h-200 { max-height: 200px; }
    .max-h-250 { max-height: 250px; }
</style>

<div class="w-full flex flex-col min-h-screen">
    
    <!-- 🌟 Compact Header & Filter 🌟 -->
    <div class="dashboard-header animate__animated animate__fadeInDown">
        <div class="mb-2 mb-lg-0 flex items-center gap-3">
            <h1 class="text-xl font-black tracking-tight m-0" style="color: var(--mg-blue);">
                <i class="fas fa-chart-line text-[#e31e24]"></i> Performance Overview
            </h1>
            <div class="h-5 w-px bg-slate-200 hidden md:block"></div>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[1px] m-0 hidden md:block">Daily Insights</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" action="{{ route('services.performance') }}" id="filterForm" class="m-0 d-flex flex-wrap align-items-center gap-2">
                <div class="filter-group m-0 py-1 px-2">
                    @if($isManager)
                        <i class="fas fa-map-marker-alt text-slate-400 text-xs"></i>
                        <select name="branch" class="filter-select text-xs" onchange="document.getElementById('filterForm').submit();" style="width: 130px;">
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
                        <i class="fas fa-map-marker-alt text-[#e31e24] text-xs"></i>
                        <span class="text-xs font-bold text-[#002663] px-1">{{ $currentBranchName }}</span>
                        <input type="hidden" name="branch" value="{{ $user->branch_id }}">
                    @endif
                </div>

                <div class="filter-group m-0 py-1 px-2">
                    <i class="far fa-calendar-alt text-slate-400 text-xs"></i>
                    <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" onchange="document.getElementById('filterForm').submit();" class="filter-select text-xs text-[#002663]">
                </div>

                @if(request()->has('date') || (request()->has('branch') && request('branch') !== 'all'))
                    <a href="{{ route('services.performance') }}" class="text-slate-400 hover:text-[#e31e24] transition-colors text-sm ml-1" title="Clear Filter"><i class="fas fa-undo-alt"></i></a>
                @endif
            </form>

            <a href="{{ route('services.index') }}" class="btn text-white shadow-sm d-flex align-items-center gap-2" style="background: var(--mg-blue); border-radius: 8px; padding: 6px 12px; font-weight: 700; font-size: 0.8rem; margin-left: 8px;">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- 🌟 Main Content Grid 🌟 -->
    <div class="container-fluid flex-grow animate__animated animate__fadeInUp">
        
        <div class="mb-3 d-flex justify-content-between align-items-end">
            <div>
                <h3 class="m-0 font-black text-lg tracking-tight text-slate-800">Daily Overview</h3>
                <p class="m-0 text-slate-500 text-xs mt-1">ទិន្នន័យជាក់ស្តែងសម្រាប់ថ្ងៃទី {{ date('d M, Y', strtotime($selectedDate)) }}</p>
            </div>
        </div>

        <!-- 🌟 Row 1: KPI Top Cards (Fit in 1 row of 5) 🌟 -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
            <!-- Throughput (Gradient Card) -->
            <div class="kpi-card bg-gradient-to-r from-[#002663] to-blue-800 border-none text-white shadow-[0_4px_10px_rgba(0,38,99,0.2)]">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-wide mb-1">Total Throughput</p>
                        <h3 class="text-2xl font-black m-0">{{ number_format($totalThroughput ?? 0) }}</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-blue-200"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
            <!-- Cars In -->
            <div class="kpi-card border-l-4 border-l-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Total Cars In</p>
                        <h3 class="text-2xl font-black text-slate-800 m-0">{{ number_format($carsIn ?? 0) }}</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center"><i class="fas fa-arrow-right"></i></div>
                </div>
            </div>
            <!-- Delivered -->
            <div class="kpi-card border-l-4 border-l-emerald-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Cars Delivery</p>
                        <h3 class="text-2xl font-black text-slate-800 m-0">{{ number_format($carsDelivery ?? 0) }}</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <!-- Today Pending -->
            <div class="kpi-card border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Today Pending</p>
                        <h3 class="text-2xl font-black text-slate-800 m-0">{{ number_format($todayPending ?? 0) }}</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
            <!-- All Pending -->
            <div class="kpi-card border-l-4 border-l-slate-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Total Backlog</p>
                        <h3 class="text-2xl font-black text-slate-800 m-0">{{ number_format($totalPending ?? 0) }}</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center"><i class="fas fa-clock"></i></div>
                </div>
            </div>
        </div>

        <!-- 🌟 Row 2: Booking Breakdown & Parts 🌟 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
            
            <div class="lg:col-span-2 clean-card">
                <div class="card-header-title">
                    <div class="icon-circle bg-indigo-50 text-indigo-600"><i class="fas fa-users"></i></div>
                    <span>SA Booking vs Walk-In Summary</span>
                </div>
                <!-- 🌟 Scrollable Table Area 🌟 -->
                <div class="flex-grow custom-scrollbar max-h-200">
                    <table class="clean-table sticky-header">
                        <thead>
                            <tr>
                                <th>Service Advisor Name</th>
                                <th class="text-center w-24"><i class="fas fa-globe text-slate-400 me-1"></i> Booking</th>
                                <th class="text-center w-24"><i class="fas fa-walking text-slate-400 me-1"></i> Walk-In</th>
                                <th class="text-center w-24">Total</th>
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
                                <tr><td colspan="4" class="text-center py-6 text-slate-400 text-xs">មិនមានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Tfoot outside scroll to always show totals -->
                @if($grandTotal > 0)
                <div class="pt-2 mt-2 border-t border-slate-100">
                    <table class="clean-table m-0">
                        <tr>
                            <td class="text-end font-black text-slate-400 uppercase text-[10px] border-none py-1">Total All</td>
                            <td class="text-center w-24 border-none py-1"><span class="qty-pill highlight">{{ $totBooking ?: 0 }}</span></td>
                            <td class="text-center w-24 border-none py-1"><span class="qty-pill highlight">{{ $totWalkin ?: 0 }}</span></td>
                            <td class="text-center w-24 border-none py-1 font-black text-md text-[#002663]">{{ $grandTotal }}</td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>

            <div class="lg:col-span-1 clean-card">
                <div class="card-header-title">
                    <div class="icon-circle bg-cyan-50 text-cyan-600"><i class="fas fa-box-open"></i></div>
                    <span>Parts Order Summary</span>
                </div>
                <div class="flex-grow custom-scrollbar max-h-200">
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            
            <div class="clean-card">
                <div class="card-header-title">
                    <div class="icon-circle bg-amber-50 text-amber-500"><i class="fas fa-exclamation-circle"></i></div>
                    <span>Pending Description</span>
                </div>
                <div class="flex-grow custom-scrollbar max-h-200">
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
                                <tr><td colspan="2" class="text-center py-6 text-slate-400 text-xs">មិនមានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clean-card">
                <div class="card-header-title">
                    <div class="icon-circle bg-orange-50 text-orange-500"><i class="fas fa-user-clock"></i></div>
                    <span>SA Pending</span>
                </div>
                <div class="flex-grow custom-scrollbar max-h-200">
                    <table class="clean-table sticky-header">
                        <thead><tr><th>SA Name</th><th class="text-center w-16">QTY</th></tr></thead>
                        <tbody>
                            @if(isset($saPendingStats) && count($saPendingStats) > 0)
                                @foreach($saPendingStats as $sa => $qty)
                                    <tr><td>{{ $sa }}</td><td class="text-center"><span class="qty-pill">{{ $qty }}</span></td></tr>
                                @endforeach
                            @else
                                <tr><td colspan="2" class="text-center py-6 text-slate-400 text-xs">មិនមានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="clean-card">
                <div class="card-header-title">
                    <div class="icon-circle bg-purple-50 text-purple-600"><i class="fas fa-store-alt"></i></div>
                    <span>Branch Pending</span>
                </div>
                <div class="flex-grow custom-scrollbar max-h-200">
                    <table class="clean-table sticky-header">
                        <thead><tr><th>Branch Name</th><th class="text-center w-16">QTY</th></tr></thead>
                        <tbody>
                            @if(isset($branchPendingStats) && count($branchPendingStats) > 0)
                                @foreach($branchPendingStats as $branch => $qty)
                                    <tr><td class="font-bold text-slate-700">{{ strtoupper($branch) }} WS</td><td class="text-center"><span class="qty-pill">{{ $qty }}</span></td></tr>
                                @endforeach
                            @else
                                <tr><td colspan="2" class="text-center py-6 text-slate-400 text-xs">មិនមានទិន្នន័យ</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section Title Leaderboard -->
        <div class="d-flex justify-content-between align-items-end mb-3 px-2">
            <div>
                <h3 class="m-0 font-black text-lg tracking-tight text-slate-800">All Time Leaderboards</h3>
                <p class="m-0 text-slate-500 text-xs mt-1">ចំណាត់ថ្នាក់ផ្អែកលើចំនួនរថយន្តជួសជុលរួចសរុប</p>
            </div>
        </div>
        
        <!-- 🌟 Row 4: Leaderboards (2 Columns) 🌟 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-6">
            
            <!-- Top SA -->
            <div class="clean-card">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#002663] flex items-center justify-center text-sm">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h2 class="text-sm font-black text-slate-800 m-0">Top Service Advisors</h2>
                </div>

                <div class="flex-grow custom-scrollbar max-h-250">
                    <div class="space-y-3 pe-1">
                        @forelse($topSA as $index => $sa)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1';
                                elseif($index == 1) $rankClass = 'rank-2';
                                elseif($index == 2) $rankClass = 'rank-3';
                                
                                $maxTotal = $topSA[0]->total ?: 1;
                                $percentage = ($sa->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-bold text-slate-700 text-[0.8rem]">{{ $sa->sa_name }}</span>
                                        <span class="font-black text-[#002663] text-sm">{{ $sa->total }}</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill bg-[#002663]" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400">
                                <p class="font-semibold text-xs m-0">No Data Available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Tech -->
            <div class="clean-card">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#e31e24] flex items-center justify-center text-sm">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h2 class="text-sm font-black text-slate-800 m-0">Top Technicians</h2>
                </div>

                <div class="flex-grow custom-scrollbar max-h-250">
                    <div class="space-y-3 pe-1">
                        @forelse($topTech as $index => $tech)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1';
                                elseif($index == 1) $rankClass = 'rank-2';
                                elseif($index == 2) $rankClass = 'rank-3';
                                
                                $maxTotal = $topTech[0]->total ?: 1;
                                $percentage = ($tech->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-bold text-slate-700 text-[0.8rem]">{{ $tech->technician_name }}</span>
                                        <span class="font-black text-[#e31e24] text-sm">{{ $tech->total }}</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill bg-[#e31e24]" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400">
                                <p class="font-semibold text-xs m-0">No Data Available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Animation for progress bars
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = width; }, 200);
        });
    });
</script>
@endsection