@extends('layouts.homePages')

@section('content')

@php
    $authUser = auth('users')->user() ?? auth()->user();
    $roleId = (int)($authUser->role_id ?? 0);
    $isGlobalManager = in_array($roleId, [1, 6, 11]);
    $userBranchId = $authUser->branch_id ?? null;

    // Force Branch ID based on Role
    if ($roleId === 16) $userBranchId = 13; // HQ
    elseif ($roleId === 10) $userBranchId = 11; // 50M
    elseif ($roleId === 14) $userBranchId = 12; // SR
    elseif ($roleId === 15) $userBranchId = 10; // BTB
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<style>
    /* =========================================================
       CLEAN SaaS DESIGN SYSTEM 
       ========================================================= */
    :root {
        --primary: #4F46E5;      /* Indigo 600 */
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      /* Slate 50 */
        --text-main: #111827;    /* Gray 900 */
        --text-muted: #6B7280;   /* Gray 500 */
        --border-color: #E5E7EB; /* Gray 200 */
        --input-bg: #F9FAFB;
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
    }

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); margin: 0; -webkit-font-smoothing: antialiased; }
    .fluid-page-wrapper { width: 100% !important; max-width: 100% !important; padding: 24px 32px !important; }

    /* --- PAGE HEADER --- */
    .page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; margin: 0; }
    .page-subtitle { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-top: 4px; }

    /* --- BUTTONS --- */
    .btn-primary-custom {
        background: var(--primary); color: #FFF; border: none; padding: 10px 18px; border-radius: 8px;
        font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; text-decoration: none; cursor: pointer;
    }
    .btn-primary-custom:hover { background: var(--primary-hover); color: #FFF; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    .btn-outline-custom {
        background: #FFF; color: var(--text-main); border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px;
        font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; text-decoration: none;
    }
    .btn-outline-custom:hover { background: #F3F4F6; border-color: #D1D5DB; transform: translateY(-1px); }

    .btn-vin { border: 1px solid var(--border-color); color: var(--text-muted); background: #FFF; border-radius: 6px; padding: 4px 8px; font-size: 0.75rem; transition: all 0.2s ease; cursor: pointer; }
    .btn-vin:hover { background: #EEF2FF; color: var(--primary); border-color: var(--primary); }

    /* --- KPI SCOREBOARD --- */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .summary-card {
        background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px;
        padding: 16px 20px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 16px;
    }
    .kpi-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .kpi-data h3 { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0; line-height: 1; }
    .kpi-data p { margin: 0; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-data small { font-size: 0.7rem; color: #9CA3AF; font-weight: 500; display: block; margin-top: 4px; }

    /* --- FILTER FORM --- */
    .filter-wrapper { background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color); padding: 16px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .filter-label { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 6px; }
    .filter-input {
        width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px;
        padding: 10px 14px; font-size: 0.875rem; color: var(--text-main); font-weight: 500; outline: none; transition: 0.2s;
    }
    .filter-input:focus { background: #FFF; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
    .select-locked { background: #F3F4F6 !important; pointer-events: none; color: #9CA3AF !important; }

    /* --- BRANCH BAY CARDS --- */
    .branch-card { 
        background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px;
        padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); cursor: pointer; transition: all 0.2s ease;
        border-left-width: 4px !important; border-left-style: solid !important;
    }
    .branch-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .accent-critical { border-left-color: #EF4444 !important; }
    .accent-active { border-left-color: #F59E0B !important; }
    .accent-clear { border-left-color: #10B981 !important; }
    
    .branch-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .branch-name { font-family: var(--font-display); font-weight: 700; font-size: 1rem; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px; }
    .branch-icon { color: var(--primary); background: #EEF2FF; padding: 6px 8px; border-radius: 6px; font-size: 0.9rem; }
    .chip-count { background: #F3F4F6; color: var(--text-main); font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--border-color); }

    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
    .stat-box { background: #F9FAFB; border: 1px solid var(--border-color); border-radius: 8px; padding: 8px; text-align: center; }
    .stat-num { font-family: var(--font-mono); font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px; line-height: 1; }
    .stat-label { font-size: 0.65rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }
    
    .stat-wip { background: #EFF6FF; border-color: #BFDBFE; } .stat-wip .stat-num { color: #2563EB; }
    .delayed-box { background: #FEF2F2; border-color: #FECACA; } .delayed-box .stat-num { color: #DC2626; }
    .done-box { background: #ECFDF5; border-color: #A7F3D0; } .done-box .stat-num { color: #059669; }

    /* --- TABLE STYLING WITH FULL BORDERS --- */
    .table-container { background: var(--surface); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid var(--border-color); }
    .table-responsive { width: 100%; overflow-x: auto; max-height: 650px; }
    
    .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F3F4F6; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

    .custom-table { width: 100%; border-collapse: collapse; white-space: nowrap; margin: 0; }
    .custom-table th, .custom-table td { border: 1px solid var(--border-color); } /* Explicit Grid Borders */
    
    .custom-table thead th { 
        background: #F9FAFB; color: var(--text-muted); font-weight: 600; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; text-align: center;
        position: sticky; top: 0; z-index: 10; box-shadow: 0 1px 0 var(--border-color);
    }
    .custom-table tbody td { padding: 12px 16px; font-size: 0.85rem; vertical-align: middle; color: var(--text-main); font-weight: 500; }
    .custom-table tbody tr:hover td { background-color: #F8FAFC; }
    
    .mono-cell { font-family: var(--font-mono); font-weight: 600; }

    /* --- BADGES & GAUGES --- */
    .badge-plate { font-family: var(--font-mono); font-weight: 700; font-size: 0.8rem; background: #F3F4F6; border: 1px solid var(--border-color); color: var(--text-main); padding: 4px 8px; border-radius: 6px; }
    .badge-soft { padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; gap: 4px; }
    .badge-completed { background: #D1FAE5; color: #059669; }

    .aging-gauge { display: inline-flex; align-items: center; gap: 8px; }
    .aging-gauge-track { width: 50px; height: 6px; background: #E5E7EB; border-radius: 999px; overflow: hidden; display: inline-block; }
    .aging-gauge-fill { display: block; height: 100%; border-radius: 999px; background: #3B82F6; }
    .aging-gauge--warn .aging-gauge-fill { background: #F59E0B; }
    .aging-gauge--critical .aging-gauge-fill { background: #EF4444; }
    
    .aging-gauge-num { font-family: var(--font-mono); font-weight: 700; font-size: 0.8rem; color: var(--text-main); }
    .aging-gauge--warn .aging-gauge-num { color: #D97706; }
    .aging-gauge--critical .aging-gauge-num { color: #DC2626; }
    .aging-gauge-num small { font-weight: 500; color: var(--text-muted); font-size: 0.7rem; }

    .remark-box { background: #FEF3C7; border-left: 3px solid #F59E0B; padding: 8px 12px; border-radius: 6px; font-size: 0.75rem; color: #92400E; width: max-content; max-width: 250px; white-space: normal; line-height: 1.4; max-height: 80px; overflow-y: auto; }
    .history-wrapper { display: flex; flex-direction: column; gap: 6px; max-height: 80px; overflow-y: auto; min-width: 280px; }
    .history-item { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 6px; padding: 6px 10px; text-align: left; white-space: normal; font-size: 0.75rem; line-height: 1.4; }
</style>

<div class="fluid-page-wrapper">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="page-title">
                <i class="fas fa-chart-pie text-muted me-2"></i> Pending &amp; WIP Dashboard
            </h2>
            <p class="page-subtitle">Active repair jobs and waiting list (Management View)</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="promptCustomExport()" class="btn-primary-custom" style="background: #059669;">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <a href="{{ route('services.index') }}" class="btn-outline-custom">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>

    <!-- 1. Global Summary Scoreboard -->
    @if(isset($globalSummary))
    <div class="summary-grid">
        <!-- Active Cases -->
        <div class="summary-card">
            <div class="kpi-icon" style="background: #EFF6FF; color: #2563EB;"><i class="fas fa-tools"></i></div>
            <div class="kpi-data">
                <p>Active Jobs</p>
                <h3>{{ $globalSummary['total_pending'] + $globalSummary['total_wip'] }}</h3>
                <small>⏳ {{ $globalSummary['total_pending'] }} Waiting | 🔧 {{ $globalSummary['total_wip'] }} In Progress</small>
            </div>
        </div>

        <!-- Critical Delayed -->
        <div class="summary-card" style="border-left: 4px solid #EF4444;">
            <div class="kpi-icon" style="background: #FEF2F2; color: #DC2626;"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="kpi-data">
                <p>Critical &gt; 7 Days</p>
                <h3>{{ $globalSummary['critical_delayed'] }}</h3>
                <small class="text-danger">🚨 Repair Overdue > 7 Days</small>
            </div>
        </div>

        <!-- Waiting Parts & Approval -->
        <div class="summary-card">
            <div class="kpi-icon" style="background: #FEF3C7; color: #D97706;"><i class="fas fa-cogs"></i></div>
            <div class="kpi-data">
                <p>Parts &amp; Approval</p>
                <h3>{{ $globalSummary['waiting_parts'] + $globalSummary['waiting_approval'] }}</h3>
                <small>📦 {{ $globalSummary['waiting_parts'] }} Parts | 👤 {{ $globalSummary['waiting_approval'] }} Cust. Approval</small>
            </div>
        </div>

        <!-- Completed -->
        <div class="summary-card">
            <div class="kpi-icon" style="background: #ECFDF5; color: #059669;"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-data">
                <p>Completed</p>
                <h3>{{ $globalSummary['total_completed'] }}</h3>
                <small>✅ Ready for delivery (100%)</small>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. Filter Form -->
    <form method="GET" action="{{ route('services.dailyReport') }}" class="filter-wrapper">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">Branch</label>
                @if($isGlobalManager)
                    <select name="branch" class="filter-input" onchange="this.form.submit()">
                        <option value="all">🏢 All Branches</option>
                        @foreach($allBranches as $branchOp)
                            <option value="{{ $branchOp }}" {{ $selectedBranch == $branchOp ? 'selected' : '' }}>
                                {{ $branchOp }}
                            </option>
                        @endforeach
                    </select>
                @else
                    @php
                        $lockedBranchName = 'My Branch';
                        if ($userBranchId == 13) $lockedBranchName = 'Service HQ';
                        elseif ($userBranchId == 11) $lockedBranchName = 'Service 50M';
                        elseif ($userBranchId == 12) $lockedBranchName = 'Service SR';
                        elseif ($userBranchId == 10) $lockedBranchName = 'Service BTB';
                    @endphp
                    <select name="branch" class="filter-input select-locked">
                        <option value="{{ $lockedBranchName }}" selected>{{ $lockedBranchName }}</option>
                    </select>
                @endif
            </div>

            <div class="col-md-3">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-input" onchange="this.form.submit()">
                    <option value="pending_wip" {{ $selectedStatus == 'pending_wip' ? 'selected' : '' }}>🔥 Active (Pending & WIP)</option>
                    <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>⏳ Waiting</option>
                    <option value="wip" {{ $selectedStatus == 'wip' ? 'selected' : '' }}>🔧 In Progress</option>
                    <option value="Completed" {{ $selectedStatus == 'Completed' ? 'selected' : '' }}>✅ Completed</option>
                    <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>🌟 All Statuses</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="filter-label">From Date</label>
                <input type="date" id="fromDate" name="from_date" value="{{ $fromDate }}" class="filter-input" onchange="this.form.submit()">
            </div>

            <div class="col-md-2">
                <label class="filter-label">To Date</label>
                <input type="date" id="toDate" name="to_date" value="{{ $toDate }}" class="filter-input" onchange="this.form.submit()">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn-primary-custom w-100 justify-content-center" style="height: 40px;">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </form>

    <!-- 3. Branch Bay Cards -->
    <div class="row g-3 mb-4">
        @foreach($branchSummaries as $branchName => $summary)
            @php
                $currentBranchId = $branchName;
                if (!is_numeric($currentBranchId)) {
                    $lowerName = strtolower($currentBranchId);
                    if (str_contains($lowerName, 'btb') || str_contains($lowerName, 'battambang')) $currentBranchId = 10;
                    elseif (str_contains($lowerName, '50m')) $currentBranchId = 11;
                    elseif (str_contains($lowerName, 'sr') || str_contains($lowerName, 'siem reap')) $currentBranchId = 12;
                    elseif (str_contains($lowerName, 'hq') || str_contains($lowerName, 'head office')) $currentBranchId = 13;
                }

                if (!$isGlobalManager && (int)$currentBranchId !== (int)$userBranchId) { continue; }

                $branchAccent = 'accent-clear';
                if (($summary['critical'] ?? 0) > 0) {
                    $branchAccent = 'accent-critical';
                } elseif ((($summary['waiting'] ?? 0) + ($summary['wip'] ?? 0)) > 0) {
                    $branchAccent = 'accent-active';
                }
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="branch-card {{ $branchAccent }} h-100" onclick="showBranchDetails('{{ addslashes($branchName) }}', {{ json_encode($summary['cars']) }})">
                    <div class="branch-header">
                        <h6 class="branch-name">
                            <span class="branch-icon"><i class="fas fa-map-marker-alt"></i></span>
                            {{ $branchName }}
                        </h6>
                        <span class="chip-count">{{ $summary['total'] }} Units</span>
                    </div>

                    <div class="stat-grid">
                        <div class="stat-box">
                            <div class="stat-num">{{ $summary['waiting'] }}</div>
                            <div class="stat-label">Waiting</div>
                        </div>
                        <div class="stat-box stat-wip">
                            <div class="stat-num">{{ $summary['wip'] }}</div>
                            <div class="stat-label">WIP</div>
                        </div>
                        <div class="stat-box delayed-box">
                            <div class="stat-num">{{ $summary['critical'] }}</div>
                            <div class="stat-label">Critical</div>
                        </div>
                        <div class="stat-box done-box">
                            <div class="stat-num">{{ $summary['completed'] }}</div>
                            <div class="stat-label">Done</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 4. Main Data Table -->
    <div class="table-container">
        <div class="table-responsive custom-scrollbar">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>Branch</th>
                        <th class="text-start">Customer Name</th>
                        <th>Contact</th>
                        <th>Model</th>
                        <th>Plate</th>
                        <th><i class="fas fa-car"></i> VIN</th>
                        <th>Mileage</th>
                        <th>Service Type</th>
                        <th>Advisor</th>
                        <th>Technician</th>
                        <th><i class="fas fa-hourglass-half"></i> Aging</th>
                        <th><i class="far fa-calendar-check"></i> Booking Date</th>
                        <th>Arrived Date</th>
                        <th>Status</th>
                        <th><i class="fas fa-clock"></i> Est. Complete</th>
                        <th style="min-width: 250px;">Remark / Note</th>
                        <th style="min-width: 280px;">Estimation History</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $row)
                        @php
                            $rowLoc = ucwords(strtolower(str_ireplace(['MG ', 'Mg '], '', $row->location ?? 'N/A')));
                            $now = \Carbon\Carbon::now();
                            $bDate = $row->booking_date ? \Carbon\Carbon::parse($row->booking_date) : \Carbon\Carbon::parse($row->created_at);
                            $agingDays = (int) $bDate->diffInDays($now);
                            $agingMax = 10;
                            $agingPct = max(0, min(100, ($agingDays / $agingMax) * 100));
                            $agingLevel = 'ok';
                            if ($agingDays >= 3 && $agingDays <= 7) $agingLevel = 'warn';
                            elseif ($agingDays > 7) $agingLevel = 'critical';
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold text-muted">{{ $rowLoc }}</td>
                            <td class="text-start fw-bold" style="color: var(--text-main);">{{ $row->customer_name ?? 'N/A' }}</td>
                            <td class="text-center" style="color: var(--primary);">{{ $row->contact ?? 'N/A' }}</td>
                            <td class="text-center">{{ $row->model ?? 'N/A' }}</td>
                            <td class="text-center"><span class="badge-plate">{{ $row->plate ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <span id="vin-text-{{ $row->id }}" class="mono-cell text-muted">
                                        {{ $row->vin ?? 'N/A' }}
                                    </span>
                                    <button type="button" onclick="promptUpdateVin({{ $row->id }}, '{{ addslashes($row->vin ?? '') }}')" class="btn-vin" title="Edit VIN">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-center mono-cell text-muted">{{ $row->mileage ? number_format($row->mileage) . ' km' : 'N/A' }}</td>
                            <td class="text-center fw-semibold text-danger">{{ $row->service_type ?? 'N/A' }}</td>
                            <td class="text-center" style="color: var(--primary);">{{ $row->sa_name ?? 'N/A' }}</td>
                            <td class="text-center text-success">{{ $row->technician_name ?? 'N/A' }}</td>

                            <!-- Aging Column -->
                            <td class="text-center">
                                @if(in_array(strtolower(trim($row->status)), ['completed', 'close', 'done']))
                                    <span class="badge-soft badge-completed"><i class="fas fa-check"></i> Done</span>
                                @else
                                    <div class="aging-gauge aging-gauge--{{ $agingLevel }}">
                                        <span class="aging-gauge-track"><span class="aging-gauge-fill" style="width: {{ $agingPct }}%;"></span></span>
                                        <span class="aging-gauge-num">{{ $agingDays }}<small>d</small></span>
                                    </div>
                                @endif
                            </td>

                            <!-- Booking Date -->
                            <td class="text-center">
                                @if(!empty($row->booking_date))
                                    <span class="d-block fw-bold" style="color: var(--text-main);">{{ \Carbon\Carbon::parse($row->booking_date)->format('d-M-Y') }}</span>
                                    @if(!empty($row->booking_time))
                                        <span class="d-block" style="font-size: 0.75rem; margin-top: 2px; color: var(--primary);"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($row->booking_time)->format('h:i A') }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <!-- Arrived Date -->
                            <td class="text-center">
                                @php
                                    $finalArrivedDate = $row->arrived_date;
                                    if (empty($finalArrivedDate)) {
                                        if ($row->is_walk_in == 1 && !empty($row->created_at)) {
                                            $finalArrivedDate = $row->created_at;
                                        } elseif (!empty($row->start_time)) {
                                            $bDate = !empty($row->booking_date) ? \Carbon\Carbon::parse($row->booking_date)->format('Y-m-d') : date('Y-m-d');
                                            $finalArrivedDate = $bDate . ' ' . $row->start_time;
                                        }
                                    }
                                @endphp
                                @if(!empty($finalArrivedDate))
                                    <span class="d-block fw-bold" style="color: var(--text-main);">{{ \Carbon\Carbon::parse($finalArrivedDate)->format('d-M-Y') }}</span>
                                    <span class="d-block" style="font-size: 0.75rem; margin-top: 2px; color: var(--primary);"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($finalArrivedDate)->format('h:i A') }}</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-minus"></i> N/A</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                @if(in_array(strtolower(trim($row->status)), ['in progress', 'wip', 'under repair', 'under diagnosis']))
                                    <span class="badge-soft" style="background:#DBEAFE; color:#2563EB;">{{ $row->status }}</span>
                                @elseif(in_array(strtolower(trim($row->status)), ['completed', 'close', 'done']))
                                    <span class="badge-soft badge-completed">{{ $row->status }}</span>
                                @else
                                    <span class="badge-soft" style="background:#FEF3C7; color:#D97706;">{{ $row->status }}</span>
                                @endif
                            </td>

                            <!-- Est. Complete -->
                            <td class="text-center">
                                @if(!empty($row->estimated_end_datetime))
                                    @php $isEstOverdue = \Carbon\Carbon::parse($row->estimated_end_datetime)->isPast(); @endphp
                                    <span class="fw-bold" style="color: {{ $isEstOverdue ? '#DC2626' : '#059669' }};">{{ \Carbon\Carbon::parse($row->estimated_end_datetime)->format('d-M-Y h:i A') }}</span>
                                @else
                                    <span class="badge-soft" style="background:#FEE2E2; color:#DC2626;">Missing Est.</span>
                                @endif
                            </td>

                            <!-- Remark -->
                            <td class="text-center">
                                @php $remarkText = trim($row->remark ?? $row->note ?? ''); @endphp
                                @if(!empty($remarkText) && $remarkText !== 'N/A' && $remarkText !== 'null')
                                    <div class="remark-box custom-scrollbar text-start mx-auto"><i class="fas fa-comment-dots me-1 text-warning"></i> {{ $remarkText }}</div>
                                @else
                                    <span class="text-muted" style="font-style: italic; font-size: 0.75rem;"><i class="fas fa-minus"></i> No notes</span>
                                @endif
                            </td>

                            <!-- History -->
                            <td class="text-left">
                                @if(!empty($row->estimation_history) && $row->estimation_history !== 'N/A' && $row->estimation_history !== 'null')
                                    @php $histories = json_decode($row->estimation_history, true); @endphp
                                    @if(is_array($histories) && count($histories) > 0)
                                        <div class="history-wrapper custom-scrollbar">
                                            @foreach($histories as $hist)
                                                <div class="history-item">
                                                    <div style="font-size: 0.7rem; color: var(--primary); margin-bottom: 4px; font-weight: 600;"><i class="far fa-clock me-1"></i> {{ isset($hist['estimated_datetime']) ? \Carbon\Carbon::parse($hist['estimated_datetime'])->format('d-M-y h:i A') : 'N/A' }}</div>
                                                    <div style="font-size: 0.75rem; color: var(--text-main); line-height: 1.4;"><i class="fas fa-angle-right me-1 text-muted" style="font-size: 0.6rem;"></i> {{ $hist['remark'] ?? 'No remark' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size:0.75rem;">{{ $row->estimation_history }}</span>
                                    @endif
                                @else
                                    <span class="text-muted"><i class="fas fa-minus"></i> N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="18" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fs-1 mb-3 d-block opacity-50"></i>
                                <span class="fw-semibold">No records found...</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showBranchDetails(branchName, cars) {
        if (!cars || cars.length === 0) {
            Swal.fire({ icon: 'info', title: 'No Data', text: 'There are no vehicles in this branch.', confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-4 border-0 shadow-sm' } });
            return;
        }

        let tableRows = '';
        cars.forEach((car, i) => {
            let statusColor = car.is_completed ? '#059669' : (car.is_wip ? '#2563EB' : '#D97706');
            let estColor = car.is_delayed ? '#DC2626' : '#059669';

            let agingBadge = '';
            let rowBg = 'transparent';
            let gaugeColor = '#3B82F6';
            const agingPct = Math.max(0, Math.min(100, ((car.aging_days || 0) / 10) * 100));

            if (car.severity === 'critical') {
                gaugeColor = '#EF4444';
                agingBadge = `<div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
                    <span style="width:44px; height:6px; border-radius:99px; background:#E5E7EB; display:inline-block; overflow:hidden;"><span style="display:block; height:100%; width:${agingPct}%; background:${gaugeColor}; border-radius:99px;"></span></span>
                    <span style="background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; padding:3px 8px; border-radius:6px; font-size:0.65rem; font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Overdue &gt; 7 Days</span>
                </div>`;
                rowBg = '#FEF2F2';
            } else if (car.severity === 'warning') {
                gaugeColor = '#F59E0B';
                agingBadge = `<div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
                    <span style="width:44px; height:6px; border-radius:99px; background:#E5E7EB; display:inline-block; overflow:hidden;"><span style="display:block; height:100%; width:${agingPct}%; background:${gaugeColor}; border-radius:99px;"></span></span>
                    <span style="background:#FFFBEB; color:#D97706; border:1px solid #FDE68A; padding:3px 8px; border-radius:6px; font-size:0.65rem; font-weight:600;">⏳ ${car.aging_days} Days</span>
                </div>`;
            } else if (car.severity === 'normal') {
                gaugeColor = '#10B981';
                agingBadge = `<div style="margin-top:6px; display:flex; align-items:center; gap:8px;">
                    <span style="width:44px; height:6px; border-radius:99px; background:#E5E7EB; display:inline-block; overflow:hidden;"><span style="display:block; height:100%; width:${agingPct}%; background:${gaugeColor}; border-radius:99px;"></span></span>
                    <span style="background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; padding:3px 8px; border-radius:6px; font-size:0.65rem; font-weight:600;">✅ ${car.aging_days} Days</span>
                </div>`;
            } else if (car.is_completed) {
                agingBadge = `<div style="margin-top:6px;"><span style="background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; padding:4px 8px; border-radius:6px; font-size:0.65rem; font-weight:700; display:inline-block;">🎉 Done</span></div>`;
            }

            tableRows += `
                <tr style="border-bottom: 1px solid #E5E7EB; background: ${rowBg}; transition: background 0.18s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='${rowBg}'">
                    <td style="padding: 14px 10px; text-align: center; color: #6B7280; font-weight: 600;">${i + 1}</td>
                    <td style="padding: 14px 10px; font-weight: 700; font-size: 0.85rem; color: #111827;">
                        ${car.customer} <br><span style="font-size:0.75rem; color:#6B7280; font-family:'JetBrains Mono',monospace; font-weight: 600;">${car.plate}</span>
                    </td>
                    <td style="padding: 14px 10px; color: ${statusColor}; font-weight: 700; font-size: 0.75rem;">
                        ${car.status}
                        ${agingBadge}
                    </td>
                    <td style="padding: 14px 10px; color: ${estColor}; font-weight: 600; font-size: 0.8rem;">${car.est_complete}</td>
                </tr>
            `;
        });

        Swal.fire({
            title: `<div class="text-start d-flex align-items-center gap-2"><div class="kpi-icon" style="background: #EEF2FF; color: var(--primary);"><i class="fas fa-map-marker-alt"></i></div><h5 class="fw-bold m-0" style="color: #111827; font-family: 'Plus Jakarta Sans', sans-serif;">${branchName}</h5></div>`,
            html: `
                <div class="custom-scrollbar mt-3" style="max-height: 450px; overflow-y: auto; text-align: left; border: 1px solid #E5E7EB; border-radius: 12px; background: #fff;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead style="position: sticky; top: 0; background: #F9FAFB; box-shadow: 0 1px 2px rgba(0,0,0,0.05); z-index: 10;">
                            <tr>
                                <th style="padding: 12px 10px; text-align: center; color: #6B7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid #E5E7EB;">No.</th>
                                <th style="padding: 12px 10px; color: #6B7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid #E5E7EB;">Customer & Plate</th>
                                <th style="padding: 12px 10px; color: #6B7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid #E5E7EB;">Status & Aging</th>
                                <th style="padding: 12px 10px; color: #6B7280; font-weight: 600; text-transform: uppercase; font-size: 0.7rem; border-bottom: 1px solid #E5E7EB;"><i class="fas fa-clock" style="color: #2563EB;"></i> Est. Complete</th>
                            </tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                </div>
            `,
            width: '780px', showCloseButton: true, showConfirmButton: false, customClass: { popup: 'rounded-4 border-0 shadow-sm' }
        });
    }

    function promptCustomExport() {
        const from = document.getElementById('fromDate').value;
        const to = document.getElementById('toDate').value;
        const status = document.querySelector('select[name="status"]').value;
        const branchSelect = document.querySelector('select[name="branch"]');
        const branchText = branchSelect.options[branchSelect.selectedIndex].text;
        const branchVal = branchSelect.value;

        Swal.fire({ title: 'Compiling Data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: window.location.href.split('?')[0],
            type: 'GET',
            data: { fetch_all_for_export: 1, from_date: from, to_date: to, status: status, branch: branchVal },
            success: async function(res) {
                if (res.success && res.data.length > 0) {
                    await buildExcelFile(res.data, from, to, branchText);
                } else {
                    Swal.fire({icon: 'info', title: 'No Data', text: 'No records found for the selected filters!', confirmButtonColor: '#4F46E5', customClass: { popup: 'rounded-4 border-0 shadow-sm' }});
                }
            }
        });
    }

    async function buildExcelFile(dataList, fromDate, toDate, branchText) {
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Pending Report');
        const totalCols = 18;

        worksheet.mergeCells(1, 1, 1, totalCols);
        const titleRow = worksheet.getCell(1, 1);
        titleRow.value = 'PENDING & WIP REPORT (MANAGEMENT VIEW)';
        titleRow.font = { size: 16, bold: true, color: { argb: 'FF111827' } };
        titleRow.alignment = { vertical: 'middle', horizontal: 'center' };

        for(let i=1; i<=totalCols; i++) {
            worksheet.getCell(1, i).border = { top: {style:'medium', color: {argb:'FF111827'}}, bottom: {style:'medium', color: {argb:'FF4F46E5'}}, left: i === 1 ? {style:'medium', color: {argb:'FF111827'}} : undefined, right: i === totalCols ? {style:'medium', color: {argb:'FF111827'}} : undefined };
        }

        let dateRangeText = (fromDate || toDate) ? `${fromDate || '...'} to ${toDate || '...'}` : 'All Time';
        worksheet.mergeCells(2, 1, 2, totalCols);
        const subTitleRow = worksheet.getCell(2, 1);
        subTitleRow.value = `Branch: ${branchText}    |    Date Range: ${dateRangeText}    |    Exported Date: ${new Date().toLocaleString('en-GB')}`;
        subTitleRow.font = { size: 10, italic: true, color: { argb: 'FF6B7280' } };
        subTitleRow.alignment = { vertical: 'middle', horizontal: 'center' };

        const headers = [
            { header: 'No.', key: 'no', width: 8 }, { header: 'Branch', key: 'location', width: 18 },
            { header: 'Customer Name', key: 'customer_name', width: 25 }, { header: 'Contact', key: 'contact', width: 15 },
            { header: 'Car Model', key: 'model', width: 15 }, { header: 'Plate', key: 'plate', width: 15 },
            { header: 'VIN', key: 'vin', width: 22 }, { header: 'Mileage', key: 'mileage', width: 12 },
            { header: 'Service Type', key: 'service_type', width: 20 }, { header: 'Service Advisor', key: 'sa_name', width: 20 },
            { header: 'Technician', key: 'technician_name', width: 20 }, { header: 'Created Date', key: 'created_at', width: 22 },
            { header: 'Booking Date', key: 'booking_date', width: 22 }, { header: 'Arrived Date', key: 'arrived_date', width: 22 },
            { header: 'Status', key: 'status', width: 20 }, { header: 'Est. Complete', key: 'estimated_end_datetime', width: 22 },
            { header: 'Remark', key: 'remark', width: 35 }, { header: 'History', key: 'estimation_history', width: 60 }
        ];

        worksheet.columns = headers.map(h => ({ key: h.key, width: h.width }));
        const headerRow = worksheet.getRow(4);
        headerRow.values = headers.map(h => h.header);

        headerRow.eachCell((cell) => {
            cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF111827' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
            cell.border = { top: {style:'thin'}, bottom: {style:'thin', color:{argb:'FF4F46E5'}}, left: {style:'thin'}, right: {style:'thin'} };
        });

        dataList.forEach((rowData, index) => {
            let historyFormatted = 'N/A';
            if (rowData.estimation_history && rowData.estimation_history !== 'N/A' && rowData.estimation_history !== 'null') {
                try {
                    let parsedHistory = JSON.parse(rowData.estimation_history);
                    if (Array.isArray(parsedHistory) && parsedHistory.length > 0) {
                        historyFormatted = parsedHistory.map(h => {
                            let dt = h.estimated_datetime ? h.estimated_datetime.replace('T', ' ') : 'N/A';
                            let user = h.updated_by ? `(By: ${h.updated_by})` : '';
                            let rem = h.remark ? h.remark : 'No notes';
                            return `• ${dt} ${user}\n  └ ${rem}`;
                        }).join('\n\n');
                    } else { historyFormatted = rowData.estimation_history; }
                } catch (e) { historyFormatted = rowData.estimation_history; }
            }

            let exportLoc = rowData.location || 'N/A';
            exportLoc = exportLoc.replace(/MG |Mg /gi, '');

            let createdStr = rowData.created_at ? rowData.created_at.replace('T', ' ').substring(0, 19) : 'N/A';
            let bookingStr = rowData.booking_date ? rowData.booking_date.substring(0, 10) + (rowData.booking_time ? ' ' + rowData.booking_time : '') : 'N/A';

            let arrivedStr = 'N/A';
            if (rowData.arrived_date) { arrivedStr = rowData.arrived_date.replace('T', ' ').substring(0, 19); }
            else if (rowData.is_walk_in == 1 && rowData.created_at) { arrivedStr = createdStr; }
            else if (rowData.start_time) {
                let bDate = rowData.booking_date ? rowData.booking_date.substring(0, 10) : new Date().toISOString().slice(0, 10);
                arrivedStr = bDate + ' ' + rowData.start_time;
            }

            let row = worksheet.addRow([
                index + 1, exportLoc, rowData.customer_name || 'N/A', rowData.contact || 'N/A', rowData.model || 'N/A',
                rowData.plate || 'N/A', rowData.vin || 'N/A', rowData.mileage ? rowData.mileage + ' km' : 'N/A',
                rowData.service_type || 'N/A', rowData.sa_name || 'N/A', rowData.technician_name || 'N/A',
                createdStr, bookingStr, arrivedStr, rowData.status || 'N/A',
                rowData.estimated_end_datetime ? rowData.estimated_end_datetime.replace('T', ' ').substring(0, 19) : 'Missing Est.',
                rowData.remark || rowData.note || 'N/A', historyFormatted
            ]);

            row.eachCell((cell, colNumber) => {
                cell.border = { top: {style:'thin', color:{argb:'FFE5E7EB'}}, bottom: {style:'thin', color:{argb:'FFE5E7EB'}}, left: {style:'thin', color:{argb:'FFE5E7EB'}}, right: {style:'thin', color:{argb:'FFE5E7EB'}} };
                if (colNumber === 17 || colNumber === 18) { cell.alignment = { vertical: 'top', horizontal: 'left', wrapText: true }; }
                else { cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true }; }
            });
        });

        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

        let fileName = "Pending_Report_";
        if(branchText !== '🏢 All Branches') fileName += branchText.replace(/ /g, '_') + "_";
        fileName += new Date().toISOString().slice(0, 10) + ".xlsx";

        saveAs(blob, fileName);
        Swal.close();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Export Successful!', showConfirmButton: false, timer: 3000 });
    }

    function promptUpdateVin(bookingId, currentVin) {
        Swal.fire({
            title: 'Enter Vehicle Identification Number (VIN)',
            input: 'text',
            inputValue: currentVin !== 'N/A' ? currentVin : '',
            inputPlaceholder: 'Type VIN here...',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-1"></i> Save',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#6B7280',
            customClass: { popup: 'rounded-4 border-0 shadow-sm', input: 'rounded-3' },
            inputValidator: (value) => { if (!value) return 'Please enter the VIN first!'; }
        }).then((result) => {
            if (result.isConfirmed) {
                let newVin = result.value;
                $.ajax({
                    url: '{{ route("services.updateVin") }}', type: 'POST',
                    data: { _token: '{{ csrf_token() }}', id: bookingId, vin: newVin },
                    success: function(res) {
                        if (res.success) {
                            $('#vin-text-' + bookingId).text(newVin);
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'VIN updated successfully!', showConfirmButton: false, timer: 3000 });
                        } else { 
                            Swal.fire({icon:'error', title:'Failed', text: res.message || 'Technical error occurred', confirmButtonColor: '#4F46E5'}); 
                        }
                    }
                });
            }
        });
    }
</script>
@endsection