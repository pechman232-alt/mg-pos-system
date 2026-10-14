@extends('layouts.homePages')

@section('content')

@php
    $user = auth()->user();
    $roleId = (int)($user->role_id ?? 0);
    $isManager = in_array($roleId, [1, 6, 11]);

    $currentBranchName = 'សាខារបស់អ្នក';
    if (!$isManager && isset($branches)) {
        $myBranch = collect($branches)->firstWhere('id', $user->branch_id);
        $currentBranchName = $myBranch ? trim(str_ireplace(['MG ', 'Mg '], '', $myBranch->branch_name)) : 'សាខារបស់អ្នក';
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    /* ============================================================
       CLEAN & PROFESSIONAL DESIGN TOKENS (SaaS STYLE)
       ============================================================ */
    :root {
        --text-main: #1e293b;
        --text-muted: #64748b;
        --text-light: #94a3b8;
        
        --primary: #0ea5e9; /* Professional Blue */
        --primary-light: #e0f2fe;
        
        --success: #10b981;
        --success-light: #d1fae5;
        
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        
        --danger: #ef4444;
        --danger-light: #fee2e2;

        --bg-body: #f8fafc;
        --border-color: #e2e8f0;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }

    body {
        background-color: var(--bg-body) !important;
        font-family: 'Inter', 'Noto Sans Khmer', sans-serif !important;
        color: var(--text-main);
        overflow: hidden !important;
        margin: 0; padding: 0;
    }

    .content-fluid-wrapper { height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

    /* 🌟 Header Section 🌟 */
    .dashboard-header { 
        background: #ffffff; 
        border-bottom: 1px solid var(--border-color); 
        padding: 1rem 2rem; 
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; 
        z-index: 50; flex-shrink: 0;
    }

    .main-dashboard-content { flex-grow: 1; overflow-y: auto; padding: 2rem; }

    /* Custom Scrollbar - Refined */
    .main-dashboard-content::-webkit-scrollbar, .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .main-dashboard-content::-webkit-scrollbar-track, .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .main-dashboard-content::-webkit-scrollbar-thumb, .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .main-dashboard-content::-webkit-scrollbar-thumb:hover, .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .custom-scrollbar { overflow-y: auto; padding-right: 8px; min-height: 0; }

    /* 🌟 Filters 🌟 */
    .filter-wrapper {
        background: #f1f5f9; border-radius: 8px; padding: 4px;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .filter-group {
        display: flex; align-items: center; gap: 8px; 
        background: #ffffff; border: 1px solid transparent; 
        border-radius: 6px; padding: 6px 12px; 
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }
    .filter-group:hover, .filter-group:focus-within { border-color: #cbd5e1; }
    .filter-select { background: transparent; border: none; outline: none; font-size: 0.85rem; font-weight: 500; color: var(--text-main); cursor: pointer; }

    /* 🌟 Clean Cards 🌟 */
    .card-clean {
        background: #ffffff; border-radius: 12px; border: 1px solid var(--border-color);
        box-shadow: var(--shadow-md); transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden; display: flex; flex-direction: column;
    }
    .card-clean:hover { transform: translateY(-2px); box-shadow: var(--shadow-card); }
    
    .card-padding { padding: 1.5rem; }

    /* KPI Top Cards */
    .kpi-title { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; }
    .kpi-value { font-size: 2rem; font-weight: 700; color: var(--text-main); line-height: 1; margin: 0; }
    .kpi-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

    /* Card Headers */
    .section-title { font-size: 1rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem; }
    .section-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }

    /* 🌟 Modern Tables 🌟 */
    .table-clean { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-clean th { 
        position: sticky; top: 0; background-color: #f8fafc; z-index: 10;
        color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; 
        padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: left;
    }
    .table-clean td { 
        padding: 16px; font-size: 0.875rem; color: var(--text-main); font-weight: 500;
        border-bottom: 1px solid #f1f5f9; 
    }
    .table-clean tbody tr:hover td { background-color: #f8fafc; }

    /* Badges */
    .badge-pill { padding: 4px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; min-width: 32px; }
    .badge-gray { background: #f1f5f9; color: #475569; }
    .badge-primary { background: var(--primary-light); color: #0284c7; }
    
    /* Leaderboard */
    .rank-badge { width: 26px; height: 26px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem; }
    .rank-1 { background: #fef08a; color: #b45309; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #fed7aa; color: #9a3412; }
    .rank-other { background: #f1f5f9; color: #94a3b8; }

    .progress-track { background: #f1f5f9; border-radius: 99px; height: 6px; overflow: hidden; margin-top: 8px; }
    .progress-fill { height: 100%; border-radius: 99px; transition: width 1s ease-out; }

    /* Tech KPI Card */
    .tech-card { padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    .tech-avatar { width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 1rem; border: 1px solid #e2e8f0; }

    .btn-back {
        background: #ffffff; border: 1px solid var(--border-color); color: var(--text-main);
        padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
    }
    .btn-back:hover { background: #f8fafc; border-color: #cbd5e1; }
</style>

<div class="content-fluid-wrapper">
    
    <!-- 🌟 HEADER & FILTERS 🌟 -->
    <div class="dashboard-header animate__animated animate__fadeInDown">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background: var(--primary);">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold m-0" style="color: var(--text-main);">Performance Overview</h1>
                <p class="text-xs font-medium m-0 mt-0.5 text-slate-500">Analytics & KPI Metrics</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 mt-3 md:mt-0">
            <form method="GET" action="{{ route('services.performance') }}" id="filterForm" class="m-0 filter-wrapper">
                
                <!-- Branch Filter -->
                <div class="filter-group">
                    <i class="fas fa-map-marker-alt text-slate-400 text-xs"></i>
                    @if($isManager)
                        <select name="branch" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                            <option value="all">All Branches</option>
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
                        <span class="font-semibold text-sm" style="color: var(--primary);">{{ $currentBranchName }}</span>
                        <input type="hidden" name="branch" value="{{ $user->branch_id }}">
                    @endif
                </div>

                <!-- Date Range -->
                <div class="filter-group">
                    <i class="far fa-calendar-alt text-slate-400 text-xs"></i>
                    <input type="date" name="from_date" value="{{ $fromDate ?? date('Y-m-01') }}" onchange="document.getElementById('filterForm').submit();" class="filter-select">
                    <span class="text-slate-300 mx-1">→</span>
                    <input type="date" name="to_date" value="{{ $toDate ?? date('Y-m-d') }}" onchange="document.getElementById('filterForm').submit();" class="filter-select">
                </div>

                @if(request()->has('from_date') || request()->has('to_date') || (request()->has('branch') && request('branch') !== 'all'))
                    <a href="{{ route('services.performance') }}" class="w-7 h-7 rounded bg-white text-slate-400 hover:text-red-500 flex items-center justify-center transition-colors shadow-sm" title="Clear Filters">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </form>

            <a href="{{ route('services.index') }}" class="btn-back shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>
    </div>

    <!-- 🌟 MAIN DASHBOARD CONTENT 🌟 -->
    <div class="main-dashboard-content">
        
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-slate-800 m-0">Daily Snapshot <span class="text-slate-400 font-medium ms-2 text-sm">{{ date('d M, Y', strtotime($selectedDate ?? date('Y-m-d'))) }}</span></h2>
        </div>

        <!-- 🌟 ROW 1: TOP KPI CARDS 🌟 -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <!-- Throughput (Hero Card) -->
            <div class="card-clean card-padding" style="background: var(--primary); color: white; border: none;">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="kpi-title" style="color: rgba(255,255,255,0.8);">Total Throughput</p>
                        <h3 class="kpi-value text-white">{{ number_format($totalThroughput ?? 0) }}</h3>
                    </div>
                    <div class="kpi-icon" style="background: rgba(255,255,255,0.2);"><i class="fas fa-layer-group"></i></div>
                </div>
            </div>

            <!-- Cars In -->
            <div class="card-clean card-padding">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="kpi-title">Total Cars In</p>
                        <h3 class="kpi-value">{{ number_format($carsIn ?? 0) }}</h3>
                    </div>
                    <div class="kpi-icon" style="background: var(--primary-light); color: var(--primary);"><i class="fas fa-sign-in-alt"></i></div>
                </div>
            </div>

            <!-- Delivery -->
            <div class="card-clean card-padding">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="kpi-title">Cars Delivery</p>
                        <h3 class="kpi-value">{{ number_format($carsDelivery ?? 0) }}</h3>
                    </div>
                    <div class="kpi-icon" style="background: var(--success-light); color: var(--success);"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>

            <!-- Pending Today -->
            <div class="card-clean card-padding">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="kpi-title">Today Pending</p>
                        <h3 class="kpi-value">{{ number_format($todayPending ?? 0) }}</h3>
                    </div>
                    <div class="kpi-icon" style="background: var(--warning-light); color: var(--warning);"><i class="fas fa-hourglass-half"></i></div>
                </div>
            </div>

            <!-- Total Backlog -->
            <div class="card-clean card-padding">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="kpi-title">Total Backlog</p>
                        <h3 class="kpi-value">{{ number_format($totalPending ?? 0) }}</h3>
                    </div>
                    <div class="kpi-icon" style="background: var(--danger-light); color: var(--danger);"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        <!-- 🌟 ROW 2: BOOKING vs WALK-IN & PARTS 🌟 -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <!-- SA Booking vs Walk-In -->
            <div class="xl:col-span-2 card-clean" style="height: 320px;">
                <div class="card-padding border-b border-slate-100 bg-slate-50">
                    <div class="section-title m-0">
                        <div class="section-icon bg-white border border-slate-200 text-slate-600"><i class="fas fa-users-cog"></i></div>
                        SA Booking vs Walk-In
                    </div>
                </div>
                <div class="flex-grow custom-scrollbar">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Service Advisor</th>
                                <th class="text-center w-24">Booking</th>
                                <th class="text-center w-24">Walk-In</th>
                                <th class="text-center w-24">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totBooking = 0; $totWalkin = 0; $grandTotal = 0; @endphp
                            @forelse($saDailyStats ?? [] as $sa => $stats)
                                @php 
                                    $totBooking += $stats['booking']; $totWalkin += $stats['walkin']; 
                                    $subTotal = $stats['booking'] + $stats['walkin']; $grandTotal += $subTotal;
                                @endphp
                                <tr>
                                    <td class="font-semibold">{{ $sa }}</td>
                                    <td class="text-center"><span class="badge-pill badge-gray">{{ $stats['booking'] ?: '-' }}</span></td>
                                    <td class="text-center"><span class="badge-pill badge-gray">{{ $stats['walkin'] ?: '-' }}</span></td>
                                    <td class="text-center font-bold text-slate-800">{{ $subTotal }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-8 text-slate-400 text-sm">No data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($grandTotal > 0)
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    <div class="flex justify-end items-center gap-8 pe-6">
                        <span class="text-xs font-bold text-slate-400 uppercase">Grand Total</span>
                        <span class="badge-pill badge-primary w-12">{{ $totBooking }}</span>
                        <span class="badge-pill" style="background: var(--success-light); color: var(--success); width: 48px;">{{ $totWalkin }}</span>
                        <span class="font-bold text-lg text-slate-800 w-12 text-center">{{ $grandTotal }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Parts Summary -->
            <div class="card-clean" style="height: 320px;">
                <div class="card-padding border-b border-slate-100 bg-slate-50">
                    <div class="section-title m-0">
                        <div class="section-icon bg-white border border-slate-200 text-slate-600"><i class="fas fa-boxes"></i></div>
                        Parts Order Status
                    </div>
                </div>
                <div class="p-5 flex flex-col justify-center h-full gap-4">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-600 text-sm">Order Part (Local)</span>
                        <span class="font-bold text-lg text-slate-800">{{ $totalOrderPartLocal ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-slate-600 text-sm">Order Part (Sea)</span>
                        <span class="font-bold text-lg text-slate-800">{{ $totalOrderPartSea ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <span class="font-medium text-emerald-600 text-sm">Warranty Order</span>
                        <span class="font-bold text-lg text-emerald-600">{{ $totalPartWarranty ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-blue-600 text-sm">Retail Order</span>
                        <span class="font-bold text-lg text-blue-600">{{ $totalPartRetail ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🌟 ROW 3: LEADERBOARDS 🌟 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Top SA -->
            <div class="card-clean" style="height: 380px;">
                <div class="card-padding border-b border-slate-100">
                    <div class="section-title m-0">
                        <div class="section-icon" style="background: var(--warning-light); color: var(--warning);"><i class="fas fa-trophy"></i></div>
                        Top Service Advisors
                    </div>
                </div>
                <div class="flex-grow custom-scrollbar p-5">
                    <div class="space-y-5">
                        @forelse($topSA ?? [] as $index => $sa)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1'; elseif($index == 1) $rankClass = 'rank-2'; elseif($index == 2) $rankClass = 'rank-3';
                                $maxTotal = $topSA[0]->total ?: 1; $percentage = ($sa->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-4">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-semibold text-slate-800 text-sm">{{ $sa->sa_name }}</span>
                                        <span class="font-bold text-sm" style="color: var(--primary);">{{ $sa->total }} <span class="text-[10px] text-slate-400 font-medium ml-0.5">CARS</span></span>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill" style="width: {{ $percentage }}%; background: var(--primary);"></div></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-sm">No data yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Tech -->
            <div class="card-clean" style="height: 380px;">
                <div class="card-padding border-b border-slate-100">
                    <div class="section-title m-0">
                        <div class="section-icon" style="background: var(--danger-light); color: var(--danger);"><i class="fas fa-medal"></i></div>
                        Top Technicians
                    </div>
                </div>
                <div class="flex-grow custom-scrollbar p-5">
                    <div class="space-y-5">
                        @forelse($topTech ?? [] as $index => $tech)
                            @php
                                $rankClass = 'rank-other';
                                if($index == 0) $rankClass = 'rank-1'; elseif($index == 1) $rankClass = 'rank-2'; elseif($index == 2) $rankClass = 'rank-3';
                                $maxTotal = $topTech[0]->total ?: 1; $percentage = ($tech->total / $maxTotal) * 100;
                            @endphp
                            <div class="flex items-center gap-4">
                                <div class="rank-badge {{ $rankClass }}">#{{ $index + 1 }}</div>
                                <div class="flex-grow">
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-semibold text-slate-800 text-sm">{{ $tech->technician_name }}</span>
                                        <span class="font-bold text-sm" style="color: var(--danger);">{{ $tech->total }} <span class="text-[10px] text-slate-400 font-medium ml-0.5">JOBS</span></span>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill" style="width: {{ $percentage }}%; background: var(--danger);"></div></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-sm">No data yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 🌟 ROW 4: TECH KPI TRACKER 🌟 -->
        <div class="mb-4 mt-8 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 m-0">Technician Time Tracking</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 pb-8">
            @forelse($techPerformances ?? [] as $tech)
                <div class="card-clean tech-card relative">
                    <!-- Overtime Indicator -->
                    @if($tech->daily_ot_hours > 0 || $tech->daily_ot_minutes > 0)
                        <div class="absolute top-3 right-3 text-[10px] font-bold px-2 py-1 rounded bg-red-50 text-red-600 border border-red-100">
                            OT: {{ $tech->daily_ot_hours }}h {{ $tech->daily_ot_minutes }}m
                        </div>
                    @endif

                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-5">
                        <div class="tech-avatar"><i class="fas fa-user-wrench text-sm"></i></div>
                        <div class="flex-grow truncate pe-12">
                            <h3 class="text-sm font-bold text-slate-800 m-0 truncate" title="{{ $tech->name }}">{{ $tech->name }}</h3>
                            <span class="text-[10px] font-medium text-slate-500 uppercase">Technician</span>
                        </div>
                    </div>

                    <!-- Daily Progress -->
                    <div class="mb-5">
                        <div class="flex justify-between text-xs font-semibold text-slate-600 mb-2">
                            <span>Today (8h)</span>
                            <span>{{ $tech->daily_actual_hours }}h {{ $tech->daily_actual_minutes }}m</span>
                        </div>
                        <div class="progress-track mb-1.5"><div class="progress-fill" style="width: {{ $tech->daily_bar_percent }}%; background: var(--primary);"></div></div>
                        <div class="text-right text-[10px] font-bold" style="color: {{ $tech->daily_percent >= 100 ? 'var(--success)' : 'var(--text-muted)' }};">
                            {{ number_format($tech->daily_percent, 1) }}%
                        </div>
                    </div>

                    <!-- Monthly Stats -->
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                        <div>
                            <span class="block text-[10px] font-medium text-slate-400 uppercase mb-0.5">Month KPI</span>
                            <span class="text-sm font-bold" style="color: {{ $tech->monthly_percent >= 100 ? 'var(--success)' : 'var(--text-main)' }};">
                                {{ number_format($tech->monthly_percent, 1) }}%
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-slate-800">
                                {{ $tech->monthly_actual_hours }}<span class="text-[10px] text-slate-400 mx-0.5">h</span>{{ $tech->monthly_actual_minutes }}<span class="text-[10px] text-slate-400">m</span>
                            </span>
                            <span class="text-[10px] font-medium text-slate-400 block mt-0.5">Actual Time</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full card-clean flex flex-col items-center justify-center py-12 border-dashed border-2 border-slate-200 shadow-none">
                    <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3"><i class="fas fa-folder-open text-xl text-slate-300"></i></div>
                    <p class="text-slate-500 font-medium text-sm m-0">មិនមានទិន្នន័យសម្រាប់បង្ហាញទេ</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Smooth load progress bars
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => { bar.style.width = width; }, 50);
            });
        }, 100);
    });
</script>
@endsection