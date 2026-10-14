@extends('layouts.homePages')
@section('content')

    @php
        $user = auth()->user();
        $userRole = $user ? $user->role_id : 0;
        $isManager = in_array($userRole, [1, 6, 11]);

        // SA Role IDs definition
        $saRoles = [12]; 
        $isSA = in_array($userRole, $saRoles);

        $userBranchName = 'Your Branch';
        if ($user && !$isManager && isset($branches)) {
            $userBranchObj = collect($branches)->firstWhere('id', $user->branch_id);
            $userBranchName = $userBranchObj
                ? trim(str_ireplace(['MG ', 'Mg '], '', $userBranchObj->branch_name))
                : 'Your Branch';
        }

        date_default_timezone_set('Asia/Phnom_Penh');
        $nowPhnomPenhTs = time();
        $activeCat = request('category', 'all');
    @endphp

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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

        body, main, .main-content { font-family: var(--font-body) !important; background-color: var(--bg-body) !important; color: var(--text-main); -webkit-font-smoothing: antialiased; }
        .container-fluid { padding: 24px 32px !important; }

        /* --- HEADER & TITLES --- */
        .dashboard-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; margin: 0; }
        .dashboard-subtitle { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-top: 4px; }

        /* --- BUTTONS --- */
        .btn-primary-custom {
            background: var(--primary); color: #FFF; border: none; padding: 10px 18px; border-radius: 8px;
            font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; text-decoration: none;
        }
        .btn-primary-custom:hover { background: var(--primary-hover); color: #FFF; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

        .btn-outline-custom {
            background: #FFF; color: var(--text-main); border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px;
            font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; text-decoration: none;
        }
        .btn-outline-custom:hover { background: #F3F4F6; border-color: #D1D5DB; transform: translateY(-1px); }

        .btn-danger-custom { color: #DC2626; border-color: #FCA5A5; background: #FEF2F2; }
        .btn-danger-custom:hover { background: #FEE2E2; color: #B91C1C; }

        /* --- KPI SUMMARY CARDS --- */
        .summary-card {
            background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px;
            padding: 16px; display: flex; align-items: center; gap: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition: all 0.2s ease; cursor: pointer;
        }
        .summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-color: #D1D5DB; }
        .summary-card.active-card { border-color: var(--primary) !important; box-shadow: 0 0 0 1px var(--primary); background: #EEF2FF; }

        .kpi-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .kpi-title { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }
        .kpi-value { font-family: var(--font-display); font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin: 2px 0 0 0; line-height: 1; }

        /* --- FILTER BAR --- */
        .filter-wrapper { background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color); padding: 16px; margin-bottom: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        .filter-input {
            width: 100%; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px;
            padding: 10px 16px 10px 36px; font-size: 0.875rem; color: var(--text-main); transition: all 0.2s; font-weight: 500;
        }
        .filter-input:focus { background: #FFF; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
        .filter-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 0.9rem; z-index: 5; }

        /* --- MAIN TABLE WITH BORDERS RESTORED --- */
        .table-card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .table-responsive { width: 100%; margin: 0; overflow-x: auto; }
        .table-custom { width: 100%; border-collapse: collapse; margin: 0; white-space: nowrap; }
        
        /* Restoring the Table Borders */
        .table-custom th, .table-custom td {
            border: 1px solid var(--border-color);
        }
        
        .table-custom thead th {
            background-color: #F9FAFB; color: var(--text-muted); font-weight: 600; text-transform: uppercase;
            font-size: 0.7rem; letter-spacing: 0.05em; padding: 14px 16px; text-align: center;
        }
        .table-custom tbody td { 
            padding: 12px 16px; vertical-align: middle; font-size: 0.85rem; color: var(--text-main); font-weight: 500; transition: background 0.15s ease; 
        }
        .table-custom tbody tr:hover td { background-color: #F8FAFC; }
        
        /* Sticky Actions Column */
        .sticky-actions { position: sticky !important; right: 0; background-color: #FFFFFF; z-index: 10; border-left: 2px solid var(--border-color) !important; box-shadow: -4px 0 6px -2px rgba(0,0,0,0.02); }
        .table-custom thead th.sticky-actions { background-color: #F9FAFB; z-index: 11; }
        .table-custom tbody tr:hover td.sticky-actions { background-color: #F8FAFC; }

        /* --- BADGES & STATUS --- */
        .badge-plate { font-family: var(--font-mono); background: #F1F5F9; color: var(--text-main); padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; border: 1px solid #E2E8F0; }
        
        .status-badge { padding: 4px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; display: inline-block; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; }
        .status-completed { background-color: #D1FAE5; color: #059669; }
        .status-under-repair { background-color: #DBEAFE; color: #2563EB; }
        .status-waiting-approved { background-color: #FEF3C7; color: #D97706; }
        .status-waiting { background-color: #EDE9FE; color: #7C3AED; }
        .status-under-diagnosis { background-color: #CFFAFE; color: #0891B2; }
        .status-waiting-parts { background-color: #FCE7F3; color: #DB2777; }
        .status-waiting-delivery { background-color: #DCFCE7; color: #16A34A; }
        .status-cancel { background-color: #FEE2E2; color: #DC2626; }
        .status-overdue { background-color: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }
        .status-archived { background-color: #F1F5F9; color: #475569; }
        .status-pending { background-color: #F3F4F6; color: #6B7280; }

        /* Notification Pulses */
        .alert-badge-late { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background-color: #FEE2E2; color: #B91C1C; border: 1px solid #FECACA; }
        .alert-badge-soon { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background-color: #D1FAE5; color: #047857; border: 1px solid #6EE7B7; }

        /* --- TABLE ACTION ICONS --- */
        .btn-action-icon {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 6px; transition: all 0.2s; border: 1px solid transparent; cursor: pointer; font-size: 0.85rem; background: #FFF; color: var(--text-muted);
        }
        .btn-action-icon:hover { transform: translateY(-2px); }
        .btn-act-view { border-color: #BFDBFE; color: #2563EB; background: #EFF6FF; }
        .btn-act-view:hover { background: #DBEAFE; }
        .btn-act-note { border-color: #DDD6FE; color: #7C3AED; background: #F5F3FF; }
        .btn-act-note:hover { background: #EDE9FE; }
        .btn-act-receipt { border-color: #A7F3D0; color: #059669; background: #ECFDF5; }
        .btn-act-receipt:hover { background: #D1FAE5; }
        .btn-act-edit { border-color: #FDE68A; color: #D97706; background: #FFFBEB; }
        .btn-act-edit:hover { background: #FEF3C7; }
        .btn-act-cancel { border-color: #FECACA; color: #DC2626; background: #FEF2F2; }
        .btn-act-cancel:hover { background: #FEE2E2; }
        .btn-act-archive { border-color: #CBD5E1; color: #475569; background: #F1F5F9; }
        .btn-act-archive:hover { background: #E2E8F0; }
        .btn-act-arrived { border-color: #A5F3FC; color: #0891B2; background: #ECFEFF; }
        .btn-act-arrived:hover { background: #CFFAFE; }
    </style>

    <div class="container-fluid">
        <!-- Header Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
            <div>
                <h2 class="dashboard-title">Service & Repair Dashboard</h2>
                <p class="dashboard-subtitle">Manage repair jobs, schedules, and service capacities</p>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                @if(auth()->check() && in_array((int) auth()->user()->role_id, [1, 2, 6, 11]))
                    <a href="{{ route('capacity.daily_tracking') }}" class="btn-outline-custom" style="color: #D97706;">
                        <i class="fas fa-tasks"></i> Capacity Board
                    </a>
                    <a href="{{ route('services.performance') }}" class="btn-outline-custom" style="color: #0284C7;">
                        <i class="fas fa-chart-line"></i> Performance
                    </a>
                @endif

                <button onclick="generateMonthlyReport()" class="btn-outline-custom">
                    <i class="fas fa-calendar-alt text-primary"></i> Monthly Report
                </button>
                
                <button onclick="promptCustomExport()" class="btn-outline-custom" style="color: #059669;">
                    <i class="fas fa-file-excel"></i> Export Custom
                </button>

                <a href="{{ route('services.create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus-circle"></i> New Walk-in
                </a>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            @php
                $summary_config = [
                    ['key' => 'total', 'sub' => 'Total Bookings', 'color' => '#2563EB', 'icon' => 'fa-list-check', 'bg' => '#EFF6FF'],
                    ['key' => 'pending', 'sub' => 'Pending Approval', 'color' => '#D97706', 'icon' => 'fa-clock', 'bg' => '#FFFBEB'],
                    ['key' => 'waiting', 'sub' => 'Waiting', 'color' => '#7C3AED', 'icon' => 'fa-user-clock', 'bg' => '#F5F3FF'],
                    ['key' => 'progress', 'sub' => 'In Progress', 'color' => '#0891B2', 'icon' => 'fa-screwdriver-wrench', 'bg' => '#ECFEFF'],
                    ['key' => 'completed', 'sub' => 'Completed', 'color' => '#059669', 'icon' => 'fa-check-double', 'bg' => '#ECFDF5'],
                    ['key' => 'overdue', 'sub' => 'Overdue', 'color' => '#DC2626', 'icon' => 'fa-triangle-exclamation', 'bg' => '#FEF2F2'],
                    ['key' => 'cancel', 'sub' => 'Cancelled', 'color' => '#EF4444', 'icon' => 'fa-ban', 'bg' => '#FEF2F2'],
                    ['key' => 'archive', 'sub' => 'Archived', 'color' => '#475569', 'icon' => 'fa-box-archive', 'bg' => '#F1F5F9'],
                ];
            @endphp
            
            @foreach ($summary_config as $card)
                <div class="col-xl col-lg-3 col-md-4 col-sm-6">
                    <div class="summary-card {{ $activeCat === $card['key'] || ($activeCat === 'all' && $card['key'] === 'total') ? 'active-card' : '' }}"
                         onclick="filterByCategory('{{ $card['key'] }}')">
                        <div class="kpi-icon" style="background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="kpi-title">{{ $card['sub'] }}</p>
                            <h3 class="kpi-value">{{ number_format($stats[$card['key']] ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filter Bar -->
        <form id="filterForm" method="GET" action="{{ route('services.index') }}" class="filter-wrapper animate__animated animate__fadeIn">
            <input type="hidden" name="category" id="catFilter" value="{{ $activeCat }}">
            <div class="row g-3 align-items-center">
                
                <div class="col-md-3">
                    <div class="position-relative">
                        <i class="fas fa-search filter-icon"></i>
                        <input type="text" name="search" id="liveSearch" value="{{ request('search') }}"
                               class="filter-input" placeholder="Search name, plate...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="position-relative">
                        @if ($isManager)
                            <select name="branch" id="branchFilter" class="filter-input" onchange="this.form.submit()" style="padding-left: 16px;">
                                <option value="all">All Branches</option>
                                @if (isset($branches))
                                    @foreach ($branches as $branch)
                                        @if (stripos($branch->branch_name, 'service') !== false)
                                            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                                {{ str_ireplace(['MG ', 'Mg '], '', $branch->branch_name) }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        @else
                            <input type="text" class="filter-input text-danger" value="{{ $userBranchName }}" readonly style="padding-left: 16px;">
                            <input type="hidden" name="branch" id="branchFilter" value="all">
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="position-relative">
                        <i class="far fa-calendar-alt filter-icon text-primary"></i>
                        <input type="date" name="from_date" id="fromDate"
                               value="{{ request('from_date', $fromDate ?? \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d')) }}"
                               class="filter-input" onchange="this.form.submit()" title="From Date">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="position-relative">
                        <i class="far fa-calendar-check filter-icon text-primary"></i>
                        <input type="date" name="to_date" id="toDate"
                               value="{{ request('to_date', $toDate ?? \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d')) }}"
                               class="filter-input" onchange="this.form.submit()" title="To Date">
                    </div>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('services.index') }}" class="btn-outline-custom btn-danger-custom w-100 justify-content-center">
                        <i class="fas fa-undo-alt"></i> Reset Filters
                    </a>
                </div>
            </div>
        </form>

        <!-- Main Data Table -->
        <div class="table-card animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table class="table-custom" id="bookingTable">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-left">Customer Name</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Vehicle Model</th>
                            <th class="text-center">License Plate</th>
                            <th class="text-center">Branch</th>
                            <th class="text-center">Mileage</th>
                            <th class="text-center">Service Type</th>
                            <th class="text-center">Source</th>
                            <th class="text-center">Advisor</th>
                            <th class="text-center">Technician</th>
                            <th class="text-center">Created Date</th>
                            <th class="text-center">Appointment</th>
                            <th class="text-center">Est. Completion</th>
                            <th class="text-center">Status</th>
                            <th class="text-left" style="min-width: 200px;">Customer Remarks</th>
                            <th class="text-left">Admin Notes</th>
                            <th class="text-center sticky-actions" style="min-width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        @forelse($bookings as $index => $booking)
                            @php
                                // XSS Sanitization
                                $safeCustomerName = strip_tags($booking->customer_name ?? 'N/A');
                                $safeContact = strip_tags($booking->contact ?? 'N/A');
                                $safePlate = strip_tags($booking->plate ?? 'N/A');
                                $safeModel = strip_tags($booking->model ?? 'N/A');
                                $safeRemark = strip_tags($booking->remark ?? '');
                                $safeNote = strip_tags($booking->note ?? '');

                                $bookTs = 0;
                                $durationStr = 'N/A';
                                
                                $startTimeStr = !empty($booking->start_time) ? date('h:i A', strtotime($booking->start_time)) : '-';
                                $endTimeStr = !empty($booking->end_time) ? date('h:i A', strtotime($booking->end_time)) : '-';

                                // Inline Carbon Logic for Break Deduction
                                if (!empty($booking->start_time) && !empty($booking->end_time)) {
                                    try {
                                        $dateStr = !empty($booking->booking_date) ? date('Y-m-d', strtotime($booking->booking_date)) : date('Y-m-d');
                                        $startStr = $dateStr . ' ' . trim($booking->start_time);
                                        $endStr = $dateStr . ' ' . trim($booking->end_time);
                                        
                                        $startObj = \Carbon\Carbon::parse($startStr);
                                        $endObj = \Carbon\Carbon::parse($endStr);
                                        
                                        if ($endObj->lessThan($startObj)) { $endObj->addDay(); }
                                        
                                        $rawSeconds = $startObj->diffInSeconds($endObj);
                                        $totalSeconds = $rawSeconds;
                                        
                                        $branchStr = strtolower(trim((string) $booking->location));
                                        $isTuekThla = str_contains($branchStr, 'ទឹកថ្លា') || str_contains($branchStr, 'tuek thla') || str_contains($branchStr, 'hq') || str_contains($branchStr, 'head') || $booking->location == 13 || $booking->branch_id == 13;

                                        if (!$isTuekThla) {
                                            $lunchStart = $startObj->copy()->setTime(12, 0, 0);
                                            $lunchEnd = $startObj->copy()->setTime(13, 0, 0);
                                            if ($startObj < $lunchEnd && $endObj > $lunchStart) {
                                                $overlapStart = max($startObj->timestamp, $lunchStart->timestamp);
                                                $overlapEnd = min($endObj->timestamp, $lunchEnd->timestamp);
                                                $overlapSeconds = max(0, $overlapEnd - $overlapStart);
                                                $totalSeconds -= $overlapSeconds;
                                            }
                                        } else {
                                            $shiftStart = $startObj->copy()->setTime(11, 30, 0);
                                            $shiftEnd = $startObj->copy()->setTime(13, 30, 0);
                                            if ($startObj < $shiftEnd && $endObj > $shiftStart && $totalSeconds >= 7200) {
                                                $totalSeconds -= 3600;
                                            }
                                        }
                                        
                                        $netSeconds = max(0, $totalSeconds);
                                        $hours = floor($netSeconds / 3600);
                                        $minutes = floor(($netSeconds % 3600) / 60);
                                        $durationStr = ($hours > 0 ? $hours.'h ' : '').$minutes.'m';
                                        
                                        if ($rawSeconds > $netSeconds) { $durationStr .= ' (-1h)'; }
                                    } catch (\Exception $e) {
                                        \Log::error("Blade Duration Error: " . $e->getMessage());
                                    }
                                }

                                $saName = strip_tags($booking->sa_name ?? 'N/A');
                                $techName = strip_tags($booking->technician_name ?? 'N/A');

                                $rawLoc = $booking->location;
                                $locObj = isset($branches) ? collect($branches)->firstWhere('id', $rawLoc) : null;
                                $displayLocation = $locObj
                                    ? str_ireplace(['MG ', 'Mg '], '', $locObj->branch_name)
                                    : str_ireplace(['MG ', 'Mg '], '', $rawLoc);
                                $displayLocation = ucwords(trim($displayLocation) ?: 'N/A');

                                $statusDisplay = trim((string) ($booking->status ?? 'Pending'));
                                $statusKey = strtolower($statusDisplay);

                                $isOverdue = false;
                                $overdueText = '';
                                $isSoon = false;
                                $soonText = '';

                                if (in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'waiting service'])) {
                                    $dateMatch = [];
                                    preg_match('/(\d{4}-\d{2}-\d{2})|(\d{2}-\d{2}-\d{4})/', (string) $booking->booking_date, $dateMatch);
                                    $cleanDate = $dateMatch[0] ?? null;
                                    $timeMatch = [];
                                    preg_match('/(\d{2}:\d{2}:\d{2})|(\d{2}:\d{2})/', (string) $booking->booking_time, $timeMatch);
                                    $cleanTime = $timeMatch[0] ?? '00:00:00';

                                    if ($cleanDate && $cleanDate !== '0000-00-00') {
                                        $bookTs = strtotime($cleanDate . ' ' . $cleanTime);
                                        if ($bookTs !== false && $bookTs > 0) {
                                            if ($bookTs < $nowPhnomPenhTs) {
                                                $isOverdue = true;
                                                $statusDisplay = 'Overdue';
                                                $statusKey = 'overdue';
                                                $diffSeconds = $nowPhnomPenhTs - $bookTs;
                                                $d = floor($diffSeconds / 86400);
                                                $h = floor(($diffSeconds % 86400) / 3600);
                                                $m = floor(($diffSeconds % 3600) / 60);
                                                $overdueText = trim(($d > 0 ? $d . 'd ' : '') . ($h > 0 ? $h . 'h ' : '') . ($m > 0 ? $m . 'm' : ''));
                                                if (empty($overdueText)) $overdueText = '1m';
                                            } elseif ($bookTs >= $nowPhnomPenhTs && $bookTs - $nowPhnomPenhTs <= 900) {
                                                $isSoon = true;
                                                $m = floor(($bookTs - $nowPhnomPenhTs) / 60);
                                                $soonText = ($m == 0 ? 1 : $m) . ' min left';
                                            }
                                        }
                                    }
                                }

                                $class = match ($statusKey) {
                                    'completed', 'close' => 'status-completed',
                                    'in progress', 'wip', 'under repair' => 'status-under-repair',
                                    'waiting approved', 'pending' => 'status-waiting-approved',
                                    'waiting service', 'waiting' => 'status-waiting',
                                    'under diagnosis' => 'status-under-diagnosis',
                                    'waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea' => 'status-waiting-parts',
                                    'waiting delivery', 'arrived' => 'status-waiting-delivery',
                                    'cancel', 'cancelled' => 'status-cancel',
                                    'expired', 'overdue' => 'status-overdue',
                                    'archived', 'archive' => 'status-archived',
                                    default => 'status-pending',
                                };

                                $customerRemark = $safeRemark;
                                $adminNoteText = $safeNote;

                                if (in_array($statusKey, ['completed', 'close'])) {
                                    $adminNoteText = 'Completed (Closed)';
                                } elseif (in_array($statusKey, ['archived', 'archive'])) {
                                    $adminNoteText = 'Archived';
                                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                                    $adminNoteText = 'Cancelled';
                                } elseif (empty($adminNoteText) && in_array($statusKey, ['in progress', 'wip', 'under repair'])) {
                                    $adminNoteText = 'Work In Progress (WIP)';
                                }

                                $displayDate = !empty($booking->booking_date) ? date('d-M-Y', strtotime(substr(trim($booking->booking_date), 0, 10))) : 'N/A';
                                $displayTime = !empty($booking->booking_time) ? date('h:i A', strtotime(substr(trim($booking->booking_time), 0, 8))) : 'N/A';
                                $displayCreatedDate = !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d-M-Y') : 'N/A';
                                $displayCreatedTime = !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('h:i A') : 'N/A';
                                $displayMileage = !empty($booking->mileage) ? number_format($booking->mileage) . ' KM' : 'N/A';

                                $firstUpdater = '';
                                $lastUpdater = '';
                                $historyData = $booking->estimation_history;

                                if (is_string($historyData)) $historyData = json_decode($historyData, true);
                                if (is_string($historyData)) $historyData = json_decode($historyData, true);

                                if (!empty($historyData) && is_array($historyData)) {
                                    $firstRecord = reset($historyData);
                                    $firstUpdater = strip_tags($firstRecord['updated_by'] ?? '');
                                    $lastRecord = end($historyData);
                                    $lastUpdater = strip_tags($lastRecord['updated_by'] ?? '');
                                }

                                $estForInput = '';
                                if (!empty($booking->estimated_end_datetime)) {
                                    $displayEstComplete = \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('d-M-Y h:i A');
                                    $estForInput = \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('Y-m-d\TH:i');
                                } else {
                                    $displayEstComplete = 'N/A';
                                }

                                // Base64 Payloads for Modals
                                $qvData = [
                                    'name' => $booking->customer_name ?? 'N/A',
                                    'phone' => $booking->contact ?? 'N/A',
                                    'model' => $booking->model ?? 'N/A',
                                    'plate' => $booking->plate ?? 'N/A',
                                    'mileage' => $displayMileage,
                                    'service' => $booking->service_type ?? 'N/A',
                                    'date' => $displayDate,
                                    'time' => $displayTime,
                                    'est' => $displayEstComplete,
                                    'duration' => $durationStr,
                                    'branch' => $displayLocation,
                                    'sa' => $booking->sa_name ?? 'N/A',
                                    'tech' => $booking->technician_name ?? 'N/A',
                                    'status' => $statusDisplay,
                                    'statusClass' => $class,
                                    'remark' => $booking->remark ?? '',
                                    'note' => $adminNoteText
                                ];

                                $estData = [
                                    'encId' => encrypt($booking->id),
                                    'plate' => $booking->plate ?? 'N/A',
                                    'created' => "$displayCreatedDate $displayCreatedTime",
                                    'booking' => "$displayDate $displayTime",
                                    'start' => $startTimeStr,
                                    'existingEst' => $estForInput,
                                    'history' => is_string($booking->estimation_history) ? $booking->estimation_history : json_encode($booking->estimation_history)
                                ];

                                $receiptData = [
                                    'id' => $booking->id,
                                    'name' => $booking->customer_name ?? 'N/A',
                                    'phone' => $booking->contact ?? 'N/A',
                                    'vehicle' => $booking->model ?? 'N/A',
                                    'plate' => $booking->plate ?? 'N/A',
                                    'mileage' => $displayMileage,
                                    'service' => $booking->service_type ?? 'N/A',
                                    'date' => $displayDate,
                                    'start' => $startTimeStr,
                                    'end' => $endTimeStr,
                                    'duration' => $durationStr,
                                    'branch' => $displayLocation,
                                    'sa' => $booking->sa_name ?? 'N/A',
                                    'tech' => $booking->technician_name ?? 'N/A'
                                ];

                                $qvBase64 = base64_encode(json_encode($qvData));
                                $estBase64 = base64_encode(json_encode($estData));
                                $receiptBase64 = base64_encode(json_encode($receiptData));
                            @endphp

                            <tr class="booking-row" data-book-ts="{{ $bookTs }}" data-status="{{ $statusKey }}">
                                <td class="text-center text-muted">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}
                                </td>

                                <td class="text-left">
                                    <div class="fw-bold" style="font-size: 0.95rem; color: var(--text-main);">
                                        {{ $safeCustomerName }}
                                    </div>
                                    <div class="mt-1 mb-1">
                                        @if (isset($booking->is_walk_in) && $booking->is_walk_in == 1)
                                            <span style="background:#F0FDF4; color:#16A34A; padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600; border:1px solid #BBF7D0;">
                                                <i class="fas fa-walking me-1"></i> Walk-in: {{ strip_tags($booking->added_by ?? 'Staff') }}
                                            </span>
                                        @else
                                            <span style="background:#EFF6FF; color:#2563EB; padding:3px 8px; border-radius:6px; font-size:10px; font-weight:600; border:1px solid #BFDBFE;">
                                                <i class="fas fa-globe me-1"></i> Online Booking
                                            </span>
                                        @endif
                                    </div>

                                    @if ($isOverdue && !empty($overdueText))
                                        <div class="mt-1 animate__animated animate__pulse animate__infinite">
                                            <span class="alert-badge-late"><i class="fas fa-clock me-1"></i> Late: {{ $overdueText }}</span>
                                        </div>
                                    @elseif ($isSoon && !empty($soonText))
                                        <div class="mt-1 animate__animated animate__pulse animate__infinite">
                                            <span class="alert-badge-soon"><i class="fas fa-hourglass-half me-1"></i> {{ $soonText }}</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center" style="color: var(--primary);"><i class="fas fa-phone-alt me-1 text-muted"></i>{{ $safeContact }}</td>
                                <td class="text-center text-muted">{{ $safeModel }}</td>
                                <td class="text-center"><span class="badge-plate">{{ $safePlate }}</span></td>

                                <td class="text-center"><i class="fas fa-map-marker-alt text-danger me-1"></i> <span class="fw-bold">{{ $displayLocation }}</span></td>
                                <td class="text-center text-muted">{{ $displayMileage }}</td>
                                
                                <td class="text-center fw-bold">{{ strip_tags($booking->service_type ?? 'N/A') }}</td>
                                
                                <td class="text-center">
                                    @if($booking->booking_source == 'Hotline')
                                        <span class="status-badge" style="background:#FEE2E2; color:#DC2626;"><i class="fas fa-phone-alt"></i> Hotline</span>
                                    @elseif($booking->booking_source == 'Telegram')
                                        <span class="status-badge" style="background:#E0F2FE; color:#0284C7;"><i class="fab fa-telegram"></i> Telegram</span>
                                    @elseif($booking->booking_source == 'Facebook')
                                        <span class="status-badge" style="background:#DBEAFE; color:#1D4ED8;"><i class="fab fa-facebook"></i> Facebook</span>
                                    @elseif($booking->booking_source == 'Walk-in')
                                        <span class="status-badge" style="background:#D1FAE5; color:#059669;"><i class="fas fa-walking"></i> Walk-in</span>
                                    @elseif($booking->booking_source == 'SA Direct')
                                        <span class="status-badge" style="background:#F3F4F6; color:#4B5563;"><i class="fas fa-user-tie"></i> SA Direct</span>
                                    @else
                                        <span class="text-muted fw-bold">N/A</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">{{ $saName }}</td>
                                <td class="text-center" style="color: #059669;">{{ $techName }}</td>

                                <td class="text-center" title="System Entry Date">
                                    <div class="fw-bold" style="font-size: 0.85rem;"><i class="fas fa-plus-circle text-success me-1"></i> {{ $displayCreatedDate }}</div>
                                    <div class="mt-1 text-muted" style="font-size: 0.75rem;">{{ $displayCreatedTime }}</div>
                                </td>

                                <td class="text-center">
                                    <div class="fw-bold">{{ $displayDate }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $displayTime }}</div>
                                </td>

                                <td class="text-center">
                                    @if (!empty($booking->estimated_end_datetime))
                                        <div class="mb-1" title="Estimation History">
                                            <span style="background: #E0F2FE; color: #0284C7; border: 1px solid #BAE6FD; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                                <i class="fas fa-flag-checkered me-1"></i> {{ $displayEstComplete }}
                                            </span>
                                            
                                            @if(!empty($firstUpdater))
                                                <div class="mt-1 text-muted" style="font-size: 0.65rem;">
                                                    <i class="fas fa-user-edit me-1"></i> 
                                                    @if($firstUpdater === $lastUpdater)
                                                        {{ $firstUpdater }}
                                                    @else
                                                        <span style="color: #059669;" title="Initial Estimator">{{ $firstUpdater }}</span> 
                                                        <i class="fas fa-arrow-right mx-1" style="font-size: 8px;"></i> 
                                                        <span style="color: #DC2626;" title="Last Updater">{{ $lastUpdater }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted d-block mb-1" style="font-size: 0.8rem;">-</span>
                                    @endif

                                    @if (!in_array($statusKey, ['completed', 'close', 'cancel', 'cancelled', 'archive', 'archived']))
                                        <button onclick="handleEstPopup(this)" data-payload="{{ $estBase64 }}" class="btn btn-sm btn-outline-custom mt-1" style="font-size: 0.65rem; padding: 4px 8px;">
                                            <i class="fas fa-plus"></i> Est. Time
                                        </button>
                                    @endif
                                </td>

                                <td class="text-center"><span class="status-badge {{ $class }}">{{ str_ireplace('Waiting Parts Order', 'Parts', $statusDisplay) }}</span></td>
                                
                                <td class="text-left">
                                    @if (!empty($customerRemark))
                                        <div style="max-width: 250px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: normal; font-size: 0.8rem; color: #D97706; line-height: 1.4;" title="{{ $customerRemark }}">
                                            <i class="fas fa-quote-left me-1 opacity-50"></i> {{ $customerRemark }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td class="text-left">
                                    @if (!empty($adminNoteText))
                                        <div style="max-width: 150px;">
                                            <div title="{{ $adminNoteText }}" style="cursor: pointer; font-size: 0.8rem; color: var(--primary); font-weight: 500;"
                                                 @if (!in_array($statusKey, ['completed', 'close', 'archived', 'archive', 'cancel', 'cancelled'])) onclick="handleEditNote(this)" data-id="{{ encrypt($booking->id ?? 0) }}" data-note="{{ htmlspecialchars($adminNoteText, ENT_QUOTES, 'UTF-8') }}" data-status="{{ htmlspecialchars($statusDisplay, ENT_QUOTES, 'UTF-8') }}" @else style="cursor: default; color: #059669;" @endif>
                                                <i class="fas fa-comment-dots me-1"></i> {{ $adminNoteText }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="cursor-pointer text-muted" style="font-size: 0.8rem;" onclick="handleEditNote(this)" data-id="{{ encrypt($booking->id ?? 0) }}" data-note="" data-status="{{ htmlspecialchars($statusDisplay, ENT_QUOTES, 'UTF-8') }}">
                                            <i class="fas fa-plus-circle"></i> Add Note
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center no-export sticky-actions">
                                    <div class="action-group">
                                        <button onclick="handleQuickView(this)" data-payload="{{ $qvBase64 }}" class="btn-action-icon btn-act-view" title="Quick View">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if (in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'overdue']))
                                            @if ($isSA)
                                                <button onclick="handleArrived(this)" data-id="{{ $booking->id }}" data-plate="{{ htmlspecialchars($safePlate, ENT_QUOTES, 'UTF-8') }}" class="btn-action-icon btn-act-arrived" title="Mark as Arrived">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>
                                            @else
                                                <button disabled class="btn-action-icon" style="background-color: #F8FAFC; color: #CBD5E1; cursor: not-allowed;" title="Only SA can mark arrival">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>
                                            @endif
                                        @endif

                                        @if (!in_array($statusKey, ['completed', 'close', 'archived', 'archive', 'cancel', 'cancelled']))
                                            <button onclick="handleEditNote(this)" data-id="{{ encrypt($booking->id ?? 0) }}" data-note="{{ htmlspecialchars($adminNoteText, ENT_QUOTES, 'UTF-8') }}" data-status="{{ htmlspecialchars($statusDisplay, ENT_QUOTES, 'UTF-8') }}" class="btn-action-icon btn-act-note" title="Admin Notes">
                                                <i class="fas fa-sticky-note"></i>
                                            </button>
                                        @endif

                                        @if (in_array($statusKey, ['completed', 'close']))
                                            <button onclick="handleReceipt(this)" data-payload="{{ $receiptBase64 }}" class="btn-action-icon btn-act-receipt" title="Generate Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </button>
                                        @endif

                                        @if (!in_array($statusKey, ['cancel', 'cancelled', 'archived', 'archive']))
                                            <a href="{{ route('services.edit', encrypt($booking->id ?? 0)) }}" class="btn-action-icon btn-act-edit" title="Edit Booking">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if (!in_array($statusKey, ['cancel', 'cancelled', 'completed', 'close', 'expired', 'overdue', 'archived', 'archive']))
                                            <button onclick="handleCancel(this)" data-id="{{ $booking->id }}" data-name="{{ htmlspecialchars($safeCustomerName, ENT_QUOTES, 'UTF-8') }}" class="btn-action-icon btn-act-cancel" title="Cancel Booking">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <form id="cancel-form-{{ $booking->id }}" action="{{ url('/services/cancel/' . encrypt($booking->id ?? 0)) }}" method="POST" class="d-none">@csrf @method('POST')</form>
                                        @endif

                                        @if (!in_array($statusKey, ['archived', 'archive']))
                                            <button onclick="handleArchive(this)" data-id="{{ $booking->id }}" data-name="{{ htmlspecialchars($safeCustomerName, ENT_QUOTES, 'UTF-8') }}" class="btn-action-icon btn-act-archive" title="Archive Record">
                                                <i class="fas fa-box-archive"></i>
                                            </button>
                                            <form id="archive-form-{{ $booking->id }}" action="{{ url('/services/archive/' . encrypt($booking->id ?? 0)) }}" method="POST" class="d-none">@csrf @method('POST')</form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="text-center py-5">
                                    <i class="fas fa-folder-open text-muted" style="font-size: 2rem; opacity: 0.3; margin-bottom: 10px;"></i>
                                    <h6 class="fw-bold text-muted">No data available for the selected filters.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-white border-top d-flex justify-content-center">
                {{ $bookings->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <script>
    // Global Ajax & Session Handling
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        error: function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.status === 419 || jqXHR.status === 401) {
                Swal.fire({
                    icon: 'warning', title: 'Session Expired!', text: 'The system will auto-refresh to reconnect...',
                    showConfirmButton: false, timer: 2000, customClass: { popup: 'rounded-4' }
                });
                setTimeout(() => window.location.reload(), 2000);
            }
        }
    });

    // XSS Protection Helper
    function escapeHtml(unsafe) {
        return (unsafe || '').toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Decode Base64 Payload safely
    function decodePayload(b64) {
        let decoded = atob(b64);
        try {
            return JSON.parse(decoded);
        } catch(e) {
            return JSON.parse(decodeURIComponent(escape(decoded)));
        }
    }

    // Button Action Handlers
    function handleQuickView(el) {
        try {
            let data = decodePayload(el.getAttribute('data-payload'));
            showQuickView(escapeHtml(data.name), escapeHtml(data.phone), escapeHtml(data.model), escapeHtml(data.plate), escapeHtml(data.mileage), escapeHtml(data.service), escapeHtml(data.date), escapeHtml(data.time), escapeHtml(data.est), escapeHtml(data.duration), escapeHtml(data.branch), escapeHtml(data.sa), escapeHtml(data.tech), escapeHtml(data.status), escapeHtml(data.statusClass), escapeHtml(data.remark), escapeHtml(data.note));
        } catch(e) { console.error('Error payload', e); }
    }

    function handleEstPopup(el) {
        try {
            let data = decodePayload(el.getAttribute('data-payload'));
            openEstimationPopup(data.encId, escapeHtml(data.plate), escapeHtml(data.created), escapeHtml(data.booking), escapeHtml(data.start), escapeHtml(data.existingEst), data.history);
        } catch(e) { console.error('Error payload', e); }
    }

    function handleReceipt(el) {
        try {
            let data = decodePayload(el.getAttribute('data-payload'));
            showCompleteJobPopup(data.id, escapeHtml(data.name), escapeHtml(data.phone), escapeHtml(data.vehicle), escapeHtml(data.plate), escapeHtml(data.mileage), escapeHtml(data.service), escapeHtml(data.date), escapeHtml(data.start), escapeHtml(data.end), escapeHtml(data.duration), escapeHtml(data.branch), escapeHtml(data.sa), escapeHtml(data.tech));
        } catch(e) { console.error('Error payload', e); }
    }

    function handleArrived(el) { markAsArrivedIndex(el.getAttribute('data-id'), escapeHtml(el.getAttribute('data-plate'))); }
    function handleEditNote(el) { editNotePopup(el.getAttribute('data-id'), escapeHtml(el.getAttribute('data-note')), escapeHtml(el.getAttribute('data-status'))); }
    function handleCancel(el) { confirmCancel(el.getAttribute('data-id'), escapeHtml(el.getAttribute('data-name'))); }
    function handleArchive(el) { confirmArchive(el.getAttribute('data-id'), escapeHtml(el.getAttribute('data-name'))); }

    // Estimation Popup
    function openEstimationPopup(encId, plate, createdDate, bookingDate, startTime, existingEst, historyData) {
        let historyText = '';
        try {
            let historyArr = typeof historyData === 'string' ? JSON.parse(historyData) : historyData;
            if (Array.isArray(historyArr) && historyArr.length > 0) {
                historyArr.forEach(h => {
                    let dt = escapeHtml(h.updated_at || '');
                    let by = escapeHtml(h.updated_by || 'Unknown');
                    let est = escapeHtml(h.estimated_datetime ? h.estimated_datetime.replace('T', ' ') : '-');
                    let rmk = escapeHtml(h.remark || 'None');
                    historyText += `[${dt}] ${by}:\n ➔ Est: ${est}\n ➔ Note: ${rmk}\n\n`;
                });
            }
        } catch(e) { console.log(e); }

        let displayHistory = historyText ? historyText : 'No history available yet...';
        let currentEst = existingEst ? existingEst : '';

        Swal.fire({
            title: '<h5 class="text-primary fw-bold m-0"><i class="fas fa-clock"></i> Add Feedback / Estimation</h5>',
            html: `
                <div class="text-start mt-3 p-3 bg-light rounded-3 border mb-3" style="font-size: 0.85rem;">
                    <div class="row g-2">
                        <div class="col-6"><p class="mb-1 text-muted">Vehicle Plate: <strong class="text-dark">${plate}</strong></p></div>
                        <div class="col-6"><p class="mb-1 text-muted">Created: <strong class="text-dark">${createdDate}</strong></p></div>
                        <div class="col-6"><p class="mb-1 text-muted">Booking Date: <strong class="text-dark">${bookingDate}</strong></p></div>
                        <div class="col-6"><p class="mb-0 text-muted">Arrived/Started: <strong class="text-success">${startTime}</strong></p></div>
                    </div>
                </div>
                <div class="text-start">
                    <label class="form-label fw-bold text-muted" style="font-size: 0.8rem;">History Log:</label>
                    <textarea class="form-control mb-3" style="border-radius: 8px; font-size: 0.8rem; background-color: #f8fafc;" rows="4" readonly>${displayHistory}</textarea>
                    
                    <hr style="border-top: 1px dashed #cbd5e1; margin: 15px 0;">

                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">New Record Date & Time * :</label>
                    <input type="datetime-local" id="swal-est-datetime" class="form-control mb-3" style="border-radius: 8px;" value="${currentEst}">
                    
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">New Remark / Reason * :</label>
                    <input type="text" id="swal-est-remark" class="form-control" placeholder="Type here..." style="border-radius: 8px;">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#4F46E5', 
            cancelButtonColor: '#6B7280',
            customClass: { popup: 'rounded-4 shadow-sm border-0' },
            width: '550px',
            preConfirm: () => {
                const estDt = document.getElementById('swal-est-datetime').value;
                const remark = document.getElementById('swal-est-remark').value;
                if (!estDt) { Swal.showValidationMessage('Please select a Date & Time!'); return false; }
                if (!remark) { Swal.showValidationMessage('Please provide a remark!'); return false; }
                return { datetime: estDt, remark: remark };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({
                    url: `/services/estimation/${encId}`,
                    type: 'POST',
                    data: { new_estimated_datetime: result.value.datetime, estimation_remark: result.value.remark },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Submitted successfully!', showConfirmButton: false, timer: 1500 }).then(() => window.location.reload());
                        } else {
                            Swal.fire('Failed', res.message || 'Error occurred!', 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 419 || xhr.status === 401) return;
                        let errMsg = 'Connection Error (Status: ' + xhr.status + ')';
                        if (xhr.status === 403) errMsg = 'Permission denied!';
                        Swal.fire('Failed', errMsg, 'error');
                    }
                });
            }
        });
    }

    function filterByCategory(category) {
        document.getElementById('catFilter').value = category === 'total' ? 'all' : category;
        document.getElementById('filterForm').submit();
    }

    let searchTimeout;
    $('#liveSearch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => { $('#filterForm').submit(); }, 800);
    });

    function confirmCancel(id, name) {
        Swal.fire({
            title: 'Cancel Booking?',
            text: `Are you sure you want to cancel the booking for ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Cancel it!',
            cancelButtonText: 'Go Back',
            customClass: { popup: 'rounded-4 border-0 shadow-sm' }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('cancel-form-' + id).submit();
        });
    }

    function confirmArchive(id, name) {
        Swal.fire({
            title: 'Archive Record?',
            text: `Do you want to archive ${name}'s record for future reference?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Go Back',
            customClass: { popup: 'rounded-4 border-0 shadow-sm' }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('archive-form-' + id).submit();
        });
    }

    function markAsArrivedIndex(id, plate) {
        Swal.fire({
            title: 'Customer Arrived?',
            text: `Has the vehicle with plate ${plate} arrived? (The system will record you as the SA)`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Arrived',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'rounded-4 border-0 shadow-sm' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({
                    url: `{{ url('workshop/arrive') }}/${id}`,
                    type: 'POST',
                    success: function() {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Success!', showConfirmButton: false, timer: 2000 });
                        setTimeout(() => window.location.reload(), 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 419 || xhr.status === 401) return;
                        let errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Connection Error!';
                        Swal.fire('Failed', errMsg, 'error');
                    }
                });
            }
        });
    }

    function editNotePopup(id, currentNote, status) {
        let safeStatus = String(status || '').toUpperCase().trim();
        let defaultVal = String(currentNote || '').trim();
        let selectHtml = '';
        let isDisabled = '';
        let allOpts = {
            'Work In Progress (WIP)': 'Work In Progress (WIP)',
            'Waiting Approved': 'Waiting Approved',
            'Under Diagnosis': 'Under Diagnosis',
            'Waiting Parts Order Local': 'Waiting Parts Order Local',
            'Waiting Parts Order DHL': 'Waiting Parts Order DHL',
            'Waiting Parts Order by Sea': 'Waiting Parts Order by Sea',
            'Waiting Delivery': 'Waiting Delivery',
            'Waiting Service': 'Waiting Service'
        };

        if (safeStatus.includes('COMPLETED') || safeStatus.includes('CLOSE')) {
            defaultVal = 'Completed (Closed)'; isDisabled = 'disabled'; selectHtml = `<option value="Completed (Closed)" selected>Completed (Closed)</option>`;
        } else if (safeStatus.includes('ARCHIVE')) {
            defaultVal = 'Archived'; isDisabled = 'disabled'; selectHtml = `<option value="Archived" selected>Archived</option>`;
        } else if (safeStatus.includes('CANCEL')) {
            defaultVal = 'Cancelled'; isDisabled = 'disabled'; selectHtml = `<option value="Cancelled" selected>Cancelled</option>`;
        } else if (safeStatus.includes('PROGRESS') || safeStatus.includes('WIP') || safeStatus.includes('REPAIR')) {
            if (defaultVal === '') defaultVal = 'Work In Progress (WIP)';
            for (let key in allOpts) { let isSel = (key === defaultVal) ? 'selected' : ''; selectHtml += `<option value="${key}" ${isSel}>${allOpts[key]}</option>`; }
        } else {
            if (defaultVal === '') selectHtml += `<option value="" disabled selected>--- Select Admin Note ---</option>`;
            for (let key in allOpts) { let isSel = (key === defaultVal) ? 'selected' : ''; selectHtml += `<option value="${key}" ${isSel}>${allOpts[key]}</option>`; }
        }

        Swal.fire({
            title: 'Admin Notes',
            html: `<div class="mb-3 text-muted text-start" style="font-size: 0.9rem;">Current Status: <b class="text-dark">${status || 'None'}</b></div>
                   <select id="swal-note-select" class="form-select w-100" style="padding:12px; border-radius:8px; border:1px solid #cbd5e1; font-weight: 500; background-color: ${isDisabled ? '#f8fafc' : '#ffffff'};" ${isDisabled}>${selectHtml}</select>`,
            showCancelButton: true,
            confirmButtonText: 'Save Note',
            cancelButtonText: 'Close',
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#6B7280',
            customClass: { popup: 'rounded-4 shadow-sm border-0' },
            showLoaderOnConfirm: true,
            preConfirm: () => {
                let val = document.getElementById('swal-note-select').value;
                if (isDisabled) val = defaultVal;
                if (!val) { Swal.showValidationMessage('Please select a note!'); return false; }
                return $.ajax({ url: `/services/note/${id}`, type: 'POST', data: { note: val } }).then(res => {
                    if (!res.success) throw new Error(res.message); return res;
                }).catch(err => { Swal.showValidationMessage(`Failed: ${err.message}`); });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(res => {
            if (res.isConfirmed) {
                Swal.fire({ title: 'Success!', text: 'Note Saved!', icon: 'success', timer: 1500, showConfirmButton: false }).then(() => window.location.reload());
            }
        });
    }

    $(document).ready(function() {
        // Keep-Alive Ping
        setInterval(function() { $.get('/sanctum/csrf-cookie'); }, 900000);

        let lastId = {{ $bookings->firstItem() ? \App\Models\ServiceBooking::max('id') : 0 }};
        let isShowingAlert = false;
        
        setInterval(function() {
            if (isShowingAlert) return;
            $.get("{{ route('bookings.checkNew') }}", { last_id: lastId }).done(function(res) {
                if (res.hasNew) {
                    isShowingAlert = true;
                    new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                    Swal.fire({
                        title: '🔔 New Booking Arrived!',
                        text: `Customer: ${res.customer} | Plate: ${res.plate}`,
                        icon: 'info',
                        confirmButtonColor: '#4F46E5',
                        confirmButtonText: 'Refresh View',
                        allowOutsideClick: false,
                        customClass: { popup: 'rounded-4 shadow-sm border-0' }
                    }).then((result) => {
                        if (result.isConfirmed) window.location.reload();
                        isShowingAlert = false;
                    });
                    lastId = res.new_id;
                }
            });

            let nowTs = Math.floor(Date.now() / 1000);
            $('tr.booking-row').each(function() {
                let ts = parseInt($(this).data('book-ts'));
                let status = escapeHtml($(this).data('status'));
                let customerName = escapeHtml($(this).data('customer'));
                if (ts > 0 && (status === 'pending' || status === 'overdue' || status === 'waiting approved')) {
                    let diffLate = nowTs - ts;
                    let diffSoon = ts - nowTs;
                    if (diffLate >= 900 && diffLate < 960 && !$(this).data('alerted-late')) {
                        $(this).data('alerted-late', true);
                        new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                        Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `${customerName} is Overdue by 15 mins!`, showConfirmButton: false, timer: 8000, customClass: { popup: 'rounded-3 shadow-sm border-0' } });
                    }
                    if (diffSoon >= 840 && diffSoon <= 900 && !$(this).data('alerted-soon')) {
                        $(this).data('alerted-soon', true);
                        new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: `${customerName} arrives in 15 mins!`, showConfirmButton: false, timer: 8000, customClass: { popup: 'rounded-3 shadow-sm border-0' } });
                    }
                }
            });
        }, 10000);
    });

    // EXPORT EXCEL
    function promptCustomExport() {
        Swal.fire({
            title: 'Export Data (Excel)',
            html: `
                <div class="text-start mt-3">
                    <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.85rem;">From Date:</label>
                    <input type="date" id="exportFromDate" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;" value="${$('#fromDate').val()}">
                </div>
                <div class="text-start mt-3">
                    <label class="form-label fw-bold text-muted mb-1" style="font-size: 0.85rem;">To Date:</label>
                    <input type="date" id="exportToDate" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;" value="${$('#toDate').val()}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Export Now',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'rounded-4 shadow-sm border-0' },
            preConfirm: () => {
                const from = document.getElementById('exportFromDate').value;
                const to = document.getElementById('exportToDate').value;
                if (!from || !to) { Swal.showValidationMessage('Please select both dates!'); return false; }
                return { from: from, to: to };
            }
        }).then((result) => {
            if (result.isConfirmed) { executeExportAPI(result.value.from, result.value.to, false); }
        });
    }

    function generateMonthlyReport() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const firstDay = `${y}-${m}-01`;
        const lastDay = new Date(y, now.getMonth() + 1, 0).toISOString().split('T')[0];
        executeExportAPI(firstDay, lastDay, true);
    }

    function executeExportAPI(fromDate, toDate, isMonthly) {
        Swal.fire({ title: 'Compiling Data...', text: 'Please wait', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        let branch = $('#branchFilter').length ? $('#branchFilter').val() : 'all';
        let search = $('#liveSearch').val();
        let category = $('#catFilter').val();

        $.ajax({
            url: window.location.href.split('?')[0],
            type: 'GET',
            data: { fetch_all_for_export: 1, from_date: fromDate, to_date: toDate, branch: branch, search: search, category: category },
            success: async function(res) {
                if (res.success && res.data.length > 0) {
                    await buildExcelFile(res.data, fromDate, toDate, isMonthly);
                } else if (res.success && res.data.length === 0) {
                    Swal.fire('No Data', 'No records found for the selected filters!', 'info');
                } else {
                    Swal.fire('Error', 'Unable to fetch data.', 'error');
                }
            },
            error: function() { Swal.fire('Error', 'Technical issue occurred.', 'error'); }
        });
    }

    async function buildExcelFile(dataList, fromDate, toDate, isMonthly) {
        Swal.fire({ title: 'Generating Excel...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('MG Report');
        const defaultFont = { name: 'Arial', size: 10 };
    
        const headers = [
            { header: 'Customer Name', key: 'col_0' }, { header: 'Contact', key: 'col_1' }, { header: 'Model', key: 'col_2' }, { header: 'Plate', key: 'col_3' },
            { header: 'Mileage', key: 'col_4' }, { header: 'Service Type', key: 'col_5' }, { header: 'Service Advisor', key: 'col_6' }, { header: 'Technician', key: 'col_7' },
            { header: 'Branch', key: 'col_8' }, { header: 'Date & Time', key: 'col_9' }, { header: 'Est. Complete', key: 'col_10' }, { header: 'Status', key: 'col_11' }, 
            { header: 'Start Time', key: 'col_12' }, { header: 'End Time', key: 'col_13' }, 
            { header: 'Total Duration', key: 'col_14' }, 
            { header: 'Lunch Break', key: 'col_15' },   
            { header: 'Net Duration', key: 'col_16' }, 
            { header: 'Customer Remark', key: 'col_17' }, { header: 'Admin Notes', key: 'col_18' }
        ];
        const totalCols = headers.length;
        worksheet.mergeCells(1, 1, 1, totalCols);
        const titleCell = worksheet.getCell(1, 1);
        titleCell.value = isMonthly ? 'MG SERVICE CAMBODIA - MONTHLY REPORT' : `MG SERVICE CAMBODIA - EXPORT REPORT (${fromDate} TO ${toDate})`;
        titleCell.font = { name: 'Arial', size: 14, bold: true, color: { argb: 'FF002663' } };
        titleCell.alignment = { vertical: 'middle', horizontal: 'center' };
        worksheet.mergeCells(2, 1, 2, totalCols);
        const dateCell = worksheet.getCell(2, 1);
        dateCell.value = 'Export Date: ' + new Date().toLocaleString('en-GB');
        dateCell.font = { name: 'Arial', size: 10, italic: true, color: { argb: 'FF64748B' } };
        dateCell.alignment = { horizontal: 'center' };
        worksheet.getRow(4).values = headers.map(h => h.header);
        worksheet.columns = headers.map(h => ({ key: h.key }));
        const headerRow = worksheet.getRow(4);
        headerRow.eachCell((cell) => {
            cell.font = { name: 'Arial', size: 10, bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF002663' } };
            cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
            cell.border = { top: { style: 'medium', color: { argb: 'FF002663' } }, left: { style: 'thin', color: { argb: 'FFFFFFFF' } }, bottom: { style: 'medium', color: { argb: 'FF002663' } }, right: { style: 'thin', color: { argb: 'FFFFFFFF' } } };
        });
        headerRow.height = 25;
        const getColLetter = (c) => { let t, l = ''; while (c > 0) { t = (c - 1) % 26; l = String.fromCharCode(t + 65) + l; c = (c - t - 1) / 26; } return l; };
        worksheet.autoFilter = `A4:${getColLetter(totalCols)}4`;
        dataList.forEach(rowData => {
            const newRow = worksheet.addRow(rowData);
            newRow.eachCell((cell) => {
                cell.font = defaultFont;
                cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                cell.border = { top: { style: 'thin', color: { argb: 'FFCBD5E1' } }, left: { style: 'thin', color: { argb: 'FFCBD5E1' } }, bottom: { style: 'thin', color: { argb: 'FFCBD5E1' } }, right: { style: 'thin', color: { argb: 'FFCBD5E1' } } };
            });
        });
        worksheet.columns.forEach((column) => {
            let maxLen = 0;
            column.eachCell({ includeEmpty: true }, (cell, rowNumber) => {
                if (rowNumber >= 4) { const val = cell.value ? cell.value.toString() : ''; if (val.length > maxLen) { maxLen = val.length; } }
            });
            column.width = Math.min(Math.max(maxLen + 2, 12), 45);
        });
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const fileNamePrefix = isMonthly ? "MG_Monthly_Report_" : "MG_Custom_Report_";
        saveAs(blob, fileNamePrefix + new Date().toISOString().slice(0, 10) + ".xlsx");
        Swal.close();
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Export Successful!', showConfirmButton: false, timer: 3000 });
    }

    function showQuickView(name, phone, model, plate, mileage, service, date, time, est_complete, duration, branch, sa, tech, status, statusClass, remark, note) {
        let estHtml = est_complete !== 'N/A' ? `<p style="margin: 5px 0 0; font-weight: 600; color: #0284C7; font-size: 0.85rem;"><i class="fas fa-flag-checkered me-1"></i> Est: ${est_complete}</p>` : '';
        let html = `
        <div style="text-align: left; font-family: 'Inter', sans-serif; padding: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; padding-bottom: 15px; margin-bottom: 20px;">
                <div><h3 style="color: #111827; font-weight: 700; margin: 0; font-size: 1.2rem;">Quick View Details</h3></div>
                <span class="status-badge ${statusClass}" style="width: auto; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem;">${status}</span>
            </div>
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div style="flex: 1; background: #F9FAFB; border: 1px solid #E5E7EB; padding: 15px; border-radius: 8px;">
                    <p style="margin: 0 0 8px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Customer</p>
                    <p style="margin: 0; font-weight: 700; font-size: 1rem; color: #111827;">${name}</p>
                    <p style="margin: 5px 0 0; color: #4B5563; font-weight: 500; font-size: 0.85rem;"><i class="fas fa-phone-alt me-1 text-muted"></i> ${phone}</p>
                </div>
                <div style="flex: 1; background: #F9FAFB; border: 1px solid #E5E7EB; padding: 15px; border-radius: 8px;">
                    <p style="margin: 0 0 8px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Vehicle</p>
                    <p style="margin: 0; font-weight: 700; font-size: 1rem; color: #111827;">${model} <span style="background: #E5E7EB; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.8rem; margin-left: 5px;">${plate}</span></p>
                    <p style="margin: 5px 0 0; color: #4B5563; font-weight: 500; font-size: 0.85rem;"><i class="fas fa-tachometer-alt me-1 text-muted"></i> Mileage: ${mileage}</p>
                </div>
            </div>
            <div style="border: 1px solid #E5E7EB; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div style="flex: 1;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Service Type</p><p style="margin: 0; font-weight: 700; color: #111827; font-size: 0.95rem;">${service}</p></div>
                    <div style="flex: 1; text-align: right;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Date & Time</p><p style="margin: 0; font-weight: 600; color: #374151; font-size: 0.9rem;">${date} | ${time}</p>${estHtml}</div>
                </div><hr style="border-color: #E5E7EB; margin: 12px 0;">
                <div style="display: flex; justify-content: space-between;">
                    <div style="flex: 1;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Branch</p><p style="margin: 0; font-weight: 600; color: #111827; font-size: 0.85rem;">${branch}</p></div>
                    <div style="flex: 1; text-align: right;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Staff</p><p style="margin: 0; font-weight: 600; font-size: 0.85rem;"><span class="text-indigo-600" title="Advisor"><i class="fas fa-user-tie"></i> ${sa}</span> <span class="mx-1 text-muted">|</span> <span class="text-emerald-600" title="Technician"><i class="fas fa-wrench"></i> ${tech}</span></p></div>
                </div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="background: #FFFBEB; padding: 12px 15px; border-radius: 8px; border: 1px solid #FEF3C7;"><p style="margin: 0 0 5px; color: #B45309; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Customer Remarks</p><p style="margin: 0; font-size: 0.85rem; color: #78350F; font-style: italic;">${remark ? `<i class="fas fa-quote-left me-1 opacity-50"></i> ${remark}` : 'None'}</p></div>
                <div style="background: #F9FAFB; padding: 12px 15px; border-radius: 8px; border: 1px solid #E5E7EB;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Admin Notes</p><p style="margin: 0; font-size: 0.85rem; color: #374151; font-style: italic;">${note ? `<i class="fas fa-sticky-note text-muted me-1"></i> ${note}` : 'None'}</p></div>
            </div>
        </div>`;
        Swal.fire({ html: html, showCloseButton: true, showConfirmButton: false, width: '600px', customClass: { popup: 'rounded-4 shadow-sm border-0' } });
    }

    function showCompleteJobPopup(id, name, phone, vehicle, plate, mileage, service, date, start, end, duration, branch, sa, tech) {
        let ticketHTML = `
        <div id="printable-ticket" style="font-family: 'Inter', sans-serif; padding: 20px; color: #111827; text-align: left; background: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px dashed #E5E7EB; padding-bottom: 15px; margin-bottom: 20px;">
                <div><h2 style="font-weight: 800; margin: 0; font-size: 1.5rem;">MG SERVICE</h2><p style="margin: 5px 0 0; color: #6B7280; font-size: 0.8rem;">${branch}</p></div>
                <div style="text-align: right;"><h3 style="font-weight: 700; margin: 0; font-size: 1.1rem; text-transform: uppercase;">SERVICE RECEIPT</h3><p style="margin: 5px 0 0; color: #4B5563; font-size: 0.8rem;">Receipt No: <strong style="color: #111827;">#MG-${id}</strong></p><p style="margin: 2px 0 0; color: #4B5563; font-size: 0.8rem;">Date: <strong>${date}</strong></p></div>
            </div>
            <div style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 20px;">
                <div style="flex: 1; border: 1px solid #E5E7EB; padding: 12px; border-radius: 8px; background: #F9FAFB;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Billed To</p><p style="margin: 0; font-weight: 700; font-size: 0.95rem;">${name}</p><p style="margin: 3px 0 0; color: #4B5563; font-size: 0.8rem;">${phone}</p></div>
                <div style="flex: 1; border: 1px solid #E5E7EB; padding: 12px; border-radius: 8px; background: #F9FAFB;"><p style="margin: 0 0 5px; color: #6B7280; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Vehicle Info</p><p style="margin: 0; font-weight: 700; font-size: 0.95rem;">${vehicle} <span style="font-family: monospace; font-size: 0.8rem; border: 1px solid #D1D5DB; border-radius: 4px; padding: 1px 4px; background: #FFF;">${plate}</span></p><p style="margin: 3px 0 0; color: #4B5563; font-size: 0.8rem;">Mileage: ${mileage}</p></div>
            </div>
            <div style="border-radius: 8px; overflow: hidden; border: 1px solid #E5E7EB; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse; margin: 0; font-size: 0.85rem;">
                    <thead><tr style="background: #F3F4F6; color: #374151;"><th style="padding: 10px 12px; text-align: left; font-weight: 600; border-bottom: 1px solid #E5E7EB;">Service Description</th><th style="padding: 10px 12px; text-align: right; font-weight: 600; border-bottom: 1px solid #E5E7EB;">Service Advisor</th></tr></thead>
                    <tbody><tr><td style="padding: 12px; font-weight: 600; color: #111827;">${service}</td><td style="padding: 12px; text-align: right; font-weight: 500; color: #4B5563;">${sa}</td></tr></tbody>
                </table>
            </div>
            <div style="background: #F9FAFB; border: 1px solid #E5E7EB; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 0.8rem; color: #4B5563; line-height: 1.6;"><div>Start Time: <strong style="color:#111827;">${start}</strong></div><div>End Time: <strong style="color:#111827;">${end}</strong></div><div>Technician: <strong style="color:#111827;">${tech}</strong></div></div>
                <div style="text-align: right; background: white; padding: 10px 15px; border-radius: 6px; border: 1px solid #E5E7EB;"><p style="margin: 0; color: #6B7280; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">Duration</p><p style="margin: 2px 0 0; font-size: 1.2rem; font-weight: 700; color: #111827;">${duration}</p></div>
            </div>
            <div style="text-align: center; margin-top: 25px; padding-top: 15px; border-top: 1px dashed #E5E7EB;">
                <p style="font-size: 0.75rem; color: #6B7280; margin-bottom: 10px;">Thank you for your business!</p>
                <i class="fas fa-barcode" style="font-size: 3rem; color: #111827; letter-spacing: -2px; transform: scaleY(1.2);"></i><p style="font-size: 0.65rem; color: #9CA3AF; margin-top: 8px; font-family: monospace;">*${id}*</p>
            </div>
        </div>`;

        Swal.fire({ 
            html: ticketHTML, width: '550px', showCloseButton: true, showDenyButton: true, showConfirmButton: true, 
            confirmButtonText: '<i class="fas fa-print me-1"></i> Print', confirmButtonColor: '#4F46E5', 
            denyButtonText: '<i class="fas fa-file-pdf me-1"></i> Save PDF', denyButtonColor: '#6B7280', 
            background: '#ffffff', customClass: { popup: 'rounded-4 shadow-sm border-0' } 
        }).then((result) => { 
            if (result.isConfirmed) { printTicketFromPopup(ticketHTML); } 
            else if (result.isDenied) { saveTicketAsPDF(ticketHTML, plate); }
        });
    }

    function printTicketFromPopup(htmlContent) {
        const windowPrint = window.open('', '', 'width=800,height=900');
        windowPrint.document.write(`
            <html>
                <head>
                    <title>Print Receipt</title>
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>body { font-family: 'Inter', sans-serif; padding: 20px; color: #111827; } @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style>
                </head>
                <body>${htmlContent}</body>
            </html>
        `);
        windowPrint.document.close();
        windowPrint.focus();
        setTimeout(() => { windowPrint.print(); windowPrint.close(); }, 800);
    }

    function saveTicketAsPDF(htmlContent, plate) {
        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = htmlContent;
        tempContainer.style.position = 'absolute'; tempContainer.style.left = '-9999px'; tempContainer.style.background = '#ffffff';
        document.body.appendChild(tempContainer);
        const opt = { margin: 0.3, filename: 'Service_Receipt_' + plate + '.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a5', orientation: 'portrait' } };
        html2pdf().set(opt).from(tempContainer).save().then(() => { document.body.removeChild(tempContainer); });
    }
    </script>
@endsection