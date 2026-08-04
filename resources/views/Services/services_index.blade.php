@extends('layouts.homePages')

@section('content')

    @php
        $user = auth()->user();
        $userRole = $user ? $user->role_id : 0;
        $isManager = in_array($userRole, [1, 6, 11]);

        $userBranchName = 'សាខារបស់អ្នក';
        if ($user && !$isManager && isset($branches)) {
            $userBranchObj = collect($branches)->firstWhere('id', $user->branch_id);
            $userBranchName = $userBranchObj
                ? trim(str_ireplace(['MG ', 'Mg '], '', $userBranchObj->branch_name))
                : 'សាខារបស់អ្នក';
        }

        date_default_timezone_set('Asia/Phnom_Penh');
        $nowPhnomPenhTs = time();
        $activeCat = request('category', 'all');
    @endphp

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --mg-blue: #002663;
            --mg-red: #e31e24;
            --bg-main: #f8fafc;
            --border-light: #cbd5e1;
        }

        body,
        main,
        .main-content {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
            background-color: var(--bg-main) !important;
            color: #334155;
        }

        .container-fluid {
            padding: 2rem !important;
        }

        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--mg-blue);
            letter-spacing: -0.5px;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            cursor: pointer;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .summary-card.active-card {
            border-color: var(--mg-blue) !important;
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -5px rgba(0, 38, 99, 0.2) !important;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .filter-wrapper {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .filter-input {
            border-radius: 8px;
            border: 1px solid var(--border-light);
            padding: 10px 15px 10px 35px;
            font-weight: 500;
            font-size: 0.9rem;
            background-color: #f8fafc;
            color: #475569;
        }

        .filter-input:focus {
            background-color: #ffffff;
            border-color: var(--mg-blue);
            box-shadow: 0 0 0 3px rgba(0, 38, 99, 0.1);
            outline: none;
        }

        .filter-icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .card-main {
            border: 1px solid var(--border-light);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            border-collapse: collapse;
            width: 100%;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid var(--border-light) !important;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 14px 12px;
            text-align: center;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 12px;
            font-size: 0.85rem;
            color: #1e293b;
            font-weight: 500;
            white-space: nowrap;
        }

        .badge-plate {
            color: var(--mg-blue);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            background: #ffffff;
            display: inline-block;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            width: 100%;
        }

        .status-completed { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .status-progress { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .status-pending { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .status-waiting { background-color: #f5f3ff; color: #7c3aed; border: 1px solid #ddd6fe; }
        .status-arrived { background-color: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
        .status-cancel { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-expired, .status-overdue { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .status-archived { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .status-waiting-approved { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .status-under-diagnosis { background-color: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
        .status-under-repair { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .status-waiting-parts { background-color: #fdf2f8; color: #db2777; border: 1px solid #fbcfe8; }
        .status-waiting-delivery { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        .btn-main-action {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            border: none;
        }

        .btn-main-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-action-icon:hover { transform: translateY(-2px); filter: brightness(1.05); }
        .alert-badge-late { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 900; background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }
        .alert-badge-soon { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 900; background-color: #d1fae5; color: #047857; border: 1px solid #6ee7b7; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }

        .btn-act-view { background-color: #eff6ff; color: #2563eb; }
        .btn-act-view:hover { background-color: #dbeafe; }
        .btn-act-note { background-color: #e0e7ff; color: #6366f1; }
        .btn-act-note:hover { background-color: #c7d2fe; }
        .btn-act-receipt { background-color: #ecfdf5; color: #059669; }
        .btn-act-receipt:hover { background-color: #d1fae5; }
        .btn-act-edit { background-color: #fffbeb; color: #d97706; }
        .btn-act-edit:hover { background-color: #fef3c7; }
        .btn-act-cancel { background-color: #fef2f2; color: #dc2626; }
        .btn-act-cancel:hover { background-color: #fee2e2; }
        .btn-act-archive { background-color: #f1f5f9; color: #475569; }
        .btn-act-archive:hover { background-color: #e2e8f0; }
        .btn-act-arrived { background-color: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc; }
        .btn-act-arrived:hover { background-color: #cffafe; }

        /* 🌟 CSS សម្រាប់ធ្វើឱ្យជួរ Actions ជាប់ជានិច្ច និងបិទពណ៌សកុំឱ្យថ្លា 🌟 */
        .sticky-actions {
            position: sticky !important;
            right: -1px;
            background-color: #ffffff !important;
            z-index: 10;
            box-shadow: -5px 0 10px -2px rgba(0, 0, 0, 0.05) !important;
        }
        thead th.sticky-actions {
            background-color: #f1f5f9 !important;
            z-index: 11;
        }
        .table tbody tr:hover td.sticky-actions {
            background-color: #f8fafc !important;
        }
    </style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeInDown">
        <div>
            <h2 class="dashboard-title m-0">Repair & Service Management System</h2>
            <p class="text-muted m-0 mt-1 fw-bold" style="font-size: 0.85rem;">គ្រប់គ្រងការងារជួសជុល និងពេលវេលាសេវាកម្ម</p>
        </div>

        <div class="d-flex gap-3">
            @if(auth()->check() && in_array((int) auth()->user()->role_id, [1, 2, 6, 11]))
                <a href="{{ route('services.performance') }}" 
                   class="btn btn-main-action bg-white text-info border border-info border-opacity-25 shadow-sm"
                   title="មើលរបាយការណ៍សង្ខេបប្រចាំថ្ងៃ">
                    <i class="fas fa-chart-line fs-5"></i> Performance
                </a>
            @endif

            <button onclick="generateMonthlyReport()"
                class="btn btn-main-action bg-white text-primary border border-primary border-opacity-25 shadow-sm"
                title="ទាញរបាយការណ៍ខែនេះស្វ័យប្រវត្តិ">
                <i class="fas fa-calendar-alt fs-5"></i> Monthly Report
            </button>
            <button onclick="promptCustomExport()"
                class="btn btn-main-action bg-white text-success border border-success border-opacity-25 shadow-sm">
                <i class="fas fa-file-excel fs-5"></i> Export Custom
            </button>

            <a href="{{ route('services.create') }}" class="btn text-white btn-main-action shadow-sm"
                style="background: var(--mg-blue);">
                <i class="fas fa-plus-circle fs-5"></i> Customer Walk-in
            </a>
        </div>
    </div>

        <div class="row g-3 mb-4 animate__animated animate__fadeInUp">
            @php
                $summary_config = [
                    ['key' => 'total', 'label' => 'សរុប', 'sub' => 'Total', 'color' => '#2563eb', 'icon' => 'fa-calendar-check', 'bg' => '#eff6ff'],
                    ['key' => 'pending', 'label' => 'រង់ចាំអនុម័ត', 'sub' => 'Pending', 'color' => '#d97706', 'icon' => 'fa-clock', 'bg' => '#fef3c7'],
                    ['key' => 'waiting', 'label' => 'កំពុងរង់ចាំ', 'sub' => 'Waiting', 'color' => '#8b5cf6', 'icon' => 'fa-user-clock', 'bg' => '#ede9fe'],
                    ['key' => 'progress', 'label' => 'កំពុងធ្វើ', 'sub' => 'In Progress', 'color' => '#0ea5e9', 'icon' => 'fa-screwdriver-wrench', 'bg' => '#f0f9ff'],
                    ['key' => 'completed', 'label' => 'បានបញ្ចប់', 'sub' => 'Completed', 'color' => '#10b981', 'icon' => 'fa-check-double', 'bg' => '#ecfdf5'],
                    ['key' => 'overdue', 'label' => 'ហួសម៉ោង', 'sub' => 'Overdue', 'color' => '#dc2626', 'icon' => 'fa-circle-exclamation', 'bg' => '#fee2e2'],
                    ['key' => 'cancel', 'label' => 'បោះបង់', 'sub' => 'Cancelled', 'color' => '#ef4444', 'icon' => 'fa-ban', 'bg' => '#fef2f2'],
                    ['key' => 'archive', 'label' => 'បណ្ណសារ', 'sub' => 'Archive', 'color' => '#475569', 'icon' => 'fa-box-archive', 'bg' => '#f1f5f9'],
                ];
            @endphp
            @foreach ($summary_config as $card)
                <div class="col-xl col-lg-3 col-md-4 col-sm-6">
                    <div class="card summary-card h-100 {{ $activeCat === $card['key'] || ($activeCat === 'all' && $card['key'] === 'total') ? 'active-card' : '' }}"
                        onclick="filterByCategory('{{ $card['key'] }}')">
                        <div class="card-body p-2 d-flex align-items-center">
                            <div class="icon-box"
                                style="background: {{ $card['bg'] }}; color: {{ $card['color'] }}; flex-shrink: 0;">
                                <i class="fas {{ $card['icon'] }}"></i>
                            </div>
                            <div class="ms-2">
                                <p class="mb-0 text-slate-700 fw-bold"
                                    style="font-size: 0.75rem; white-space: nowrap; line-height: 1.2;">
                                    {{ $card['label'] }} <br>
                                    <span style="font-size: 0.6rem; color: {{ $card['color'] }}; font-weight: 700; text-transform: uppercase;">{{ $card['sub'] }}</span>
                                </p>
                                <h3 class="fw-black mb-0 text-slate-800"
                                    style="font-size: 1.35rem; line-height: 1.1; margin-top: 3px;">
                                    {{ number_format($stats[$card['key']] ?? 0) }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="filterForm" method="GET" action="{{ route('services.index') }}" class="filter-wrapper mb-4 animate__animated animate__fadeIn">
            <input type="hidden" name="category" id="catFilter" value="{{ $activeCat }}">

            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="position-relative">
                        <i class="fas fa-search filter-icon"></i>
                        <input type="text" name="search" id="liveSearch" value="{{ request('search') }}"
                            class="form-control filter-input w-100" placeholder="ស្វែងរកឈ្មោះ, លេខឡាន...">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="position-relative">
                        @if ($isManager)
                            <select name="branch" id="branchFilter" class="form-select filter-input w-100"
                                onchange="this.form.submit()">
                                <option value="all">📍 គ្រប់សាខាទាំងអស់</option>
                                @if (isset($branches))
                                    @foreach ($branches as $branch)
                                        @if (stripos($branch->branch_name, 'service') !== false)
                                            <option value="{{ $branch->id }}"
                                                {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                                {{ str_ireplace(['MG ', 'Mg '], '', $branch->branch_name) }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        @else
                            <input type="text" class="form-control filter-input w-100 text-danger"
                                value="{{ $userBranchName }}" readonly>
                            <input type="hidden" name="branch" id="branchFilter" value="all">
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="position-relative">
                        <i class="far fa-calendar-alt filter-icon text-primary"></i>
                        <input type="date" name="from_date" id="fromDate"
                            value="{{ request('from_date', $fromDate ?? \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d')) }}"
                            class="form-control filter-input w-100" onchange="this.form.submit()" title="ចាប់ពីថ្ងៃ">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="position-relative">
                        <i class="far fa-calendar-check filter-icon text-primary"></i>
                        <input type="date" name="to_date" id="toDate"
                            value="{{ request('to_date', $toDate ?? \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d')) }}"
                            class="form-control filter-input w-100" onchange="this.form.submit()" title="ដល់ថ្ងៃ">
                    </div>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('services.index') }}"
                        class="btn btn-light w-100 h-100 fw-semibold border text-danger d-flex align-items-center justify-content-center"
                        style="border-radius: 8px;">
                        <i class="fas fa-undo-alt me-2"></i> Clear all
                    </a>
                </div>
            </div>
        </form>

        <div class="card card-main animate__animated animate__fadeInUp">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="bookingTable">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Customer Name</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Model</th>
                            <th class="text-center">Plate</th>
                            <th class="text-center">Mileage</th>
                            <th class="text-center">Service Type</th>
                            <th class="text-center">Service Advisor</th>
                            <th class="text-center">Technician</th>
                            <th class="text-center">Branch</th>
                            <th class="text-center">Created Date</th>
                            <th class="text-center">Date & Time</th>
                            <!-- 🌟 Column Est. Complete 🌟 -->
                            <th class="text-center">Est. Complete</th>
                            <th class="text-center">Status</th>
                            <!-- 🌟 ពង្រីក Customer Remark 🌟 -->
                            <th class="text-center" style="min-width: 250px;">Customer Remark</th>
                            <th class="text-center">Admin Notes</th>
                            <!-- 🌟 ថែម sticky-actions និង nowrap 🌟 -->
                            <th class="text-center no-export sticky-actions" style="min-width: 260px; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        @forelse($bookings as $index => $booking)
                            @php
                                $bookTs = 0;
                                $durationStr = 'N/A';
                                $startTimeStr = '-';
                                $endTimeStr = '-';
                                if (!empty($booking->start_time)) {
                                    $startTimeStr = date('h:i A', strtotime($booking->start_time));
                                    if (!empty($booking->end_time)) {
                                        $endTimeStr = date('h:i A', strtotime($booking->end_time));
                                        $start = \Carbon\Carbon::parse($booking->start_time);
                                        $end = \Carbon\Carbon::parse($booking->end_time);
                                        $diff = $start->diff($end);
                                        $durationStr = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
                                    }
                                }

                                $saName = $booking->sa_name ?? 'N/A';
                                $techName = $booking->technician_name ?? 'N/A';

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
                                                $soonText = ($m == 0 ? 1 : $m) . ' នាទីទៀត';
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

                                $customerRemark = $booking->remark ?? '';
                                $adminNoteText = $booking->note ?? '';

                                if (in_array($statusKey, ['completed', 'close'])) {
                                    $adminNoteText = 'បានបញ្ចប់ (Close)';
                                } elseif (in_array($statusKey, ['archived', 'archive'])) {
                                    $adminNoteText = 'បានទុកក្នុងបណ្ណសារ (Archived)';
                                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                                    $adminNoteText = 'បានបោះបង់ (Cancelled)';
                                } elseif (empty($adminNoteText) && in_array($statusKey, ['in progress', 'wip', 'under repair'])) {
                                    $adminNoteText = 'កំពុងដំណើរការ (WIP)';
                                }

                                $displayDate = !empty($booking->booking_date) ? date('d-M-Y', strtotime(substr(trim($booking->booking_date), 0, 10))) : 'N/A';
                                $displayTime = !empty($booking->booking_time) ? date('h:i A', strtotime(substr(trim($booking->booking_time), 0, 8))) : 'N/A';
                                $displayCreatedDate = !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d-M-Y') : 'N/A';
                                $displayCreatedTime = !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('h:i A') : 'N/A';
                                $displayMileage = !empty($booking->mileage) ? number_format($booking->mileage) . ' KM' : 'N/A';

                                // 🌟 ចាប់យកប្រវត្តិអ្នកកំណត់ថ្ងៃ និង Input ចាស់ 🌟
                                $firstUpdater = '';
                                $lastUpdater = '';
                                $lastRemark = '';
                                $estForInput = '';
                                $historyData = $booking->estimation_history;

                                if (is_string($historyData)) $historyData = json_decode($historyData, true);
                                if (is_string($historyData)) $historyData = json_decode($historyData, true);

                                if (!empty($historyData) && is_array($historyData)) {
                                    $firstRecord = reset($historyData);
                                    $firstUpdater = $firstRecord['updated_by'] ?? '';

                                    $lastRecord = end($historyData);
                                    $lastUpdater = $lastRecord['updated_by'] ?? '';
                                    $lastRemark = $lastRecord['remark'] ?? '';
                                }

                                if (!empty($booking->estimated_end_datetime)) {
                                    $displayEstComplete = \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('d-M-Y h:i A');
                                    // តម្រូវ Format សម្រាប់ input type="datetime-local" 
                                    $estForInput = \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('Y-m-d\TH:i');
                                } else {
                                    $displayEstComplete = 'N/A';
                                }
                            @endphp

                            <tr class="booking-row" data-book-ts="{{ $bookTs }}"
                                data-status="{{ $statusKey }}"
                                data-customer="{{ addslashes($booking->customer_name ?? 'N/A') }}">
                                <td class="text-center text-slate-500">
                                    {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $index + 1 }}</td>

                                <td class="text-slate-800">
                                    <div class="fw-bold" style="font-size: 0.95rem;">
                                        {{ $booking->customer_name ?? 'N/A' }}</div>

                                    <div class="mt-1 mb-1">
                                        @if (isset($booking->is_walk_in) && $booking->is_walk_in == 1)
                                            <span style="background:#f0fdf4; color:#16a34a; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:700; border:1px solid #bbf7d0; display:inline-block;" title="Walk-in Booked By: {{ $booking->added_by ?? 'Staff' }}">
                                                <i class="fas fa-user-edit me-1"></i> Walk-in: {{ $booking->added_by ?? 'Staff' }}
                                            </span>
                                        @else
                                            <span style="background:#eff6ff; color:#2563eb; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:700; border:1px solid #bfdbfe; display:inline-block;">
                                                <i class="fas fa-globe me-1"></i> Online Booking
                                            </span>
                                        @endif
                                    </div>

                                    @if ($isOverdue && !empty($overdueText))
                                        <div class="mt-1 animate__animated animate__pulse animate__infinite">
                                            <span class="alert-badge-late"><i class="fas fa-clock-rotate-left me-1"></i> ហួស: {{ $overdueText }}</span>
                                        </div>
                                    @elseif ($isSoon && !empty($soonText))
                                        <div class="mt-1 animate__animated animate__pulse animate__infinite">
                                            <span class="alert-badge-soon"><i class="fas fa-hourglass-half me-1"></i> ជិតដល់: {{ $soonText }}</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center text-primary"><i class="fas fa-phone-alt me-1 text-slate-300"></i>{{ $booking->contact ?? 'N/A' }}</td>
                                <td class="text-center text-slate-600">{{ $booking->model ?? 'N/A' }}</td>
                                <td class="text-center"><span class="badge-plate">{{ $booking->plate ?? 'N/A' }}</span></td>
                                <td class="text-center"><span class="badge bg-slate-100 text-slate-600 border border-slate-200">{{ $displayMileage }}</span></td>
                                <td class="text-center text-mg-red">{{ $booking->service_type ?? 'N/A' }}</td>
                                <td class="text-center text-mg-blue">{{ $saName }}</td>
                                <td class="text-center text-emerald-600">{{ $techName }}</td>
                                <td class="text-center"><i class="fas fa-map-marker-alt text-danger me-1"></i> <span class="fw-bold text-slate-700 branch-name-text">{{ $displayLocation }}</span></td>

                                <!-- 🌟 1. Created Date 🌟 -->
                                <td class="text-center bg-slate-50" title="ថ្ងៃបញ្ចូលទិន្នន័យចូលប្រព័ន្ធ">
                                    <div class="fw-bold text-slate-600" style="font-size: 0.85rem;"><i class="fas fa-plus-circle text-success me-1"></i> {{ $displayCreatedDate }}</div>
                                    <div class="mt-1 text-slate-400" style="font-size: 0.75rem; font-weight: 700;">{{ $displayCreatedTime }}</div>
                                </td>

                                <!-- 🌟 2. Date & Time 🌟 -->
                                <td class="text-center text-slate-700">
                                    <div>{{ $displayDate }}</div>
                                    <div style="font-size: 11px; color:#64748b;">{{ $displayTime }}</div>
                                </td>

                                <!-- 🌟 Column Est. Complete 🌟 -->
                                <td class="text-center">
                                    @if (!empty($booking->estimated_end_datetime))
                                        <div class="mb-1" title="ប្រវត្តិអ្នកកំណត់">
                                            <span class="badge bg-info text-dark border border-info border-opacity-25 shadow-sm px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fas fa-flag-checkered text-primary me-1"></i> {{ $displayEstComplete }}
                                            </span>
                                            
                                            <!-- 🌟 បង្ហាញឈ្មោះប្រវត្តិអ្នកធ្វើ (Story) 🌟 -->
                                            @if(!empty($firstUpdater))
                                                <div class="mt-1 fw-bold text-slate-500" style="font-size: 0.65rem;">
                                                    <i class="fas fa-user-edit text-primary opacity-75"></i> 
                                                    @if($firstUpdater === $lastUpdater)
                                                        {{ $firstUpdater }}
                                                    @else
                                                        <span class="text-success" title="អ្នកកំណត់ដំបូងគេ">{{ $firstUpdater }}</span> 
                                                        <i class="fas fa-arrow-right mx-1 text-slate-300" style="font-size: 8px;"></i> 
                                                        <span class="text-danger" title="អ្នក Update ចុងក្រោយ">{{ $lastUpdater }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-300 d-block mb-1" style="font-size: 0.8rem;">-</span>
                                    @endif

                                    @if (!in_array($statusKey, ['completed', 'close', 'cancel', 'cancelled', 'archive', 'archived']))
                                    <button onclick="openEstimationPopup('{{ encrypt($booking->id) }}', '{{ addslashes($booking->plate ?? 'N/A') }}', '{{ $displayCreatedDate }} {{ $displayCreatedTime }}', '{{ $displayDate }} {{ $displayTime }}', '{{ $startTimeStr }}', '{{ $estForInput }}', '{{ base64_encode(is_string($booking->estimation_history) ? $booking->estimation_history : json_encode($booking->estimation_history)) }}')" class="btn btn-sm btn-outline-primary d-block mx-auto mt-1" style="font-size: 0.65rem; padding: 2px 8px; border-radius: 6px;">
                                        <i class="fas fa-plus"></i> Est. Complete
                                    </button>
                                @endif
                                </td>

                                <td class="text-center"><span class="status-badge {{ $class }}">{{ str_ireplace('Waiting Parts Order', 'Parts', $statusDisplay) }}</span></td>
                                
                                <!-- 🌟 Customer Remark ពង្រីកធំ 🌟 -->
                                <td class="text-center">
                                    @if (!empty($customerRemark))
                                        <div style="max-width: 280px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: normal; font-size: 0.8rem; color: #ea580c; margin: 0 auto; text-align: left; line-height: 1.4;" title="{{ $customerRemark }}">
                                            <i class="fas fa-quote-left me-1 opacity-50"></i> {{ $customerRemark }}
                                        </div>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if (!empty($adminNoteText))
                                        <div class="note-container" style="max-width: 130px; margin: 0 auto;">
                                            <div class="note-text" title="{{ $adminNoteText }}"
                                                @if (!in_array($statusKey, ['completed', 'close', 'archived', 'archive', 'cancel', 'cancelled'])) onclick="editNotePopup('{{ encrypt($booking->id ?? 0) }}', '{{ addslashes($adminNoteText) }}', '{{ $booking->status }}')" @else style="cursor: default; color: #059669; font-weight: 600;" @endif>
                                                <i class="fas fa-comment-dots text-indigo-400 me-1"></i> {{ $adminNoteText }}
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center cursor-pointer text-slate-300 hover:text-indigo-400 transition-colors"
                                            onclick="editNotePopup('{{ encrypt($booking->id ?? 0) }}', '', '{{ $booking->status }}')">
                                            <i class="fas fa-plus-circle"></i> Note
                                        </div>
                                    @endif
                                </td>

                                <!-- 🌟 Actions Column ដាក់ sticky-actions និង flex-nowrap 🌟 -->
                                <td class="text-center no-export sticky-actions" style="white-space: nowrap;">
                                    <div class="d-flex justify-content-center flex-nowrap gap-1">
                                        <button
                                            onclick="showQuickView(
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $booking->customer_name ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $booking->contact ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $booking->model ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $booking->plate ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $displayMileage ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $booking->service_type ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $displayDate ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $displayTime ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $displayEstComplete ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $durationStr ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $displayLocation ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $saName ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $techName ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $statusDisplay ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $class ?? '')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $customerRemark ?? 'N/A')) }}', 
                                            '{{ addslashes(str_replace(["\r", "\n"], ' ', $adminNoteText ?? 'N/A')) }}'
                                        )"
                                            class="btn-action-icon btn-act-view" title="Quick View">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if (in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'overdue']))
                                            <button
                                                onclick="markAsArrivedIndex('{{ $booking->id }}', '{{ addslashes($booking->plate ?? '') }}')"
                                                class="btn-action-icon btn-act-arrived"
                                                title="សម្គាល់ថាអតិថិជនមកដល់ (Mark as Arrived)"><i class="fas fa-map-marker-alt"></i></button>
                                        @endif

                                        @if (!in_array($statusKey, ['completed', 'close', 'archived', 'archive', 'cancel', 'cancelled']))
                                            <button
                                                onclick="editNotePopup('{{ encrypt($booking->id ?? 0) }}', '{{ addslashes($adminNoteText) }}', '{{ $booking->status }}')"
                                                class="btn-action-icon btn-act-note" title="Add / Edit Note"><i class="fas fa-sticky-note"></i></button>
                                        @endif

                                        @if (in_array($statusKey, ['completed', 'close']))
                                            <button
                                                onclick="showCompleteJobPopup('{{ $booking->id ?? '' }}', '{{ addslashes($booking->customer_name ?? '') }}', '{{ $booking->contact ?? '' }}', '{{ $booking->model ?? '' }}', '{{ $booking->plate ?? '' }}', '{{ addslashes($displayMileage) }}', '{{ $booking->service_type ?? '' }}', '{{ $displayDate }}', '{{ $startTimeStr }}', '{{ $endTimeStr }}', '{{ $durationStr }}', '{{ addslashes($displayLocation) }}', '{{ addslashes($saName) }}', '{{ addslashes($techName) }}')"
                                                class="btn-action-icon btn-act-receipt" title="Receipt"><i class="fas fa-receipt"></i></button>
                                        @endif

                                        @if (!in_array($statusKey, ['cancel', 'cancelled', 'archived', 'archive']))
                                            <a href="{{ route('services.edit', encrypt($booking->id ?? 0)) }}"
                                                class="btn-action-icon btn-act-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endif

                                        @if (!in_array($statusKey, ['cancel', 'cancelled', 'completed', 'close', 'expired', 'overdue', 'archived', 'archive']))
                                            <button
                                                onclick="confirmCancel('{{ $booking->id }}', '{{ addslashes($booking->customer_name ?? '') }}')"
                                                class="btn-action-icon btn-act-cancel" title="Cancel Booking"><i class="fas fa-ban"></i></button>
                                            <form id="cancel-form-{{ $booking->id }}"
                                                action="{{ url('/services/cancel/' . encrypt($booking->id ?? 0)) }}"
                                                method="POST" class="d-none">@csrf @method('POST')</form>
                                        @endif

                                        @if (!in_array($statusKey, ['archived', 'archive']))
                                            <button
                                                onclick="confirmArchive('{{ $booking->id }}', '{{ addslashes($booking->customer_name ?? '') }}')"
                                                class="btn-action-icon btn-act-archive" title="Archive Data"><i class="fas fa-box-archive"></i></button>
                                            <form id="archive-form-{{ $booking->id }}"
                                                action="{{ url('/services/archive/' . encrypt($booking->id ?? 0)) }}"
                                                method="POST" class="d-none">@csrf @method('POST')</form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-5">
                                    <h5 class="fw-bold text-slate-500">មិនទាន់មានទិន្នន័យនៅឡើយទេ</h5>
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
   // 🌟 កូដ Pop-up ថ្មី មានបែងចែក ប្រវត្តិ (History) និង បញ្ចូលថ្មី (New Record) 🌟
function openEstimationPopup(encId, plate, createdDate, bookingDate, startTime, existingEst, base64History) {
    
    // បំប្លែងប្រវត្តិពី Base64 មកជាអក្សរធម្មតាវិញ
    let historyText = '';
    try {
        let decodedHistory = atob(base64History);
        let historyArr = JSON.parse(decodedHistory);
        if (typeof historyArr === 'string') historyArr = JSON.parse(historyArr); 
        
        if (Array.isArray(historyArr) && historyArr.length > 0) {
            historyArr.forEach(h => {
                let dt = h.updated_at || '';
                let by = h.updated_by || 'Unknown';
                let est = h.estimated_datetime ? h.estimated_datetime.replace('T', ' ') : '-';
                let rmk = h.remark || 'គ្មាន';
                historyText += `[${dt}] ${by}:\n ➔ Est: ${est}\n ➔ Note: ${rmk}\n\n`;
            });
        }
    } catch(e) { console.log(e); }

    let displayHistory = historyText ? historyText : 'មិនទាន់មានប្រវត្តិនៅឡើយទេ...';
    let currentEst = existingEst ? existingEst : '';

    Swal.fire({
        title: '<h5 class="text-primary fw-bold m-0"><i class="fas fa-clock"></i> បន្ថែមមតិយោបល់ (Add Feedback)</h5>',
        html: `
            <div class="text-start mt-3 p-3 bg-light rounded-3 border mb-3" style="font-size: 0.85rem;">
                <div class="row g-2">
                    <div class="col-6"><p class="mb-1 text-slate-700">🚗 ស្លាកលេខ: <strong class="text-danger">${plate}</strong></p></div>
                    <div class="col-6"><p class="mb-1 text-slate-700">📅 ថ្ងៃបង្កើត: <strong class="text-indigo-600">${createdDate}</strong></p></div>
                    <div class="col-6"><p class="mb-1 text-slate-700">🗓️ ថ្ងៃកក់: <strong class="text-primary">${bookingDate}</strong></p></div>
                    <div class="col-6"><p class="mb-0 text-slate-700">⏱️ ចាប់ផ្តើម/មកដល់: <strong class="text-success">${startTime}</strong></p></div>
                </div>
            </div>

            <div class="text-start">
                <label class="form-label fw-bold text-slate-500" style="font-size: 0.8rem;">ប្រវត្តិមតិយោបល់ (History) :</label>
                <textarea class="form-control mb-3 custom-scrollbar" style="border-radius: 8px; font-size: 0.8rem; background-color: #f1f5f9; color: #475569;" rows="4" readonly>${displayHistory}</textarea>
                
                <hr style="border-top: 2px dashed #cbd5e1; margin: 15px 0;">

                <label class="form-label fw-bold text-danger" style="font-size: 0.85rem;">ជ្រើសរើសថ្ងៃ និងម៉ោងថ្មី (New Record) * :</label>
                <input type="datetime-local" id="swal-est-datetime" class="form-control mb-3" style="border-radius: 8px;" value="${currentEst}">
                
                <label class="form-label fw-bold text-danger" style="font-size: 0.85rem;">មូលហេតុ / ចំណាំថ្មី (New Remark) * :</label>
                <input type="text" id="swal-est-remark" class="form-control" placeholder="វាយបញ្ចូលទីនេះ..." style="border-radius: 8px;">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane"></i> បញ្ជូន',
        cancelButtonText: 'បោះបង់',
        confirmButtonColor: '#e31e24', 
        cancelButtonColor: '#64748b',
        customClass: { popup: 'rounded-4 shadow-lg' },
        width: '550px',
        preConfirm: () => {
            const estDt = document.getElementById('swal-est-datetime').value;
            const remark = document.getElementById('swal-est-remark').value;
            if (!estDt) {
                Swal.showValidationMessage('សូមជ្រើសរើសថ្ងៃ និងម៉ោងរួចរាល់!');
                return false;
            }
            if (!remark) {
                Swal.showValidationMessage('សូមបញ្ចូលមូលហេតុ / ចំណាំថ្មី!');
                return false;
            }
            return { datetime: estDt, remark: remark };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'កំពុងបញ្ជូន...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            
            $.ajax({
                url: `/services/estimation/${encId}`,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: {
                    new_estimated_datetime: result.value.datetime,
                    estimation_remark: result.value.remark
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'បញ្ជូនជោគជ័យ!', showConfirmButton: false, timer: 1500 })
                        .then(() => window.location.reload());
                    } else {
                        Swal.fire('បរាជ័យ', res.message || 'មានបញ្ហា!', 'error');
                    }
                },
                error: function(xhr) {
                    let errMsg = 'មានបញ្ហាក្នុងការភ្ជាប់ (Status: ' + xhr.status + ')';
                    if (xhr.status === 403) errMsg = 'គណនីរបស់អ្នកគ្មានសិទ្ធិទេ!';
                    if (xhr.status === 419) errMsg = 'Session ផុតកំណត់ សូម Refresh (F5)!';
                    Swal.fire('បរាជ័យ', errMsg, 'error');
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
            searchTimeout = setTimeout(() => {
                $('#filterForm').submit();
            }, 800);
        });

        function confirmCancel(id, name) {
            Swal.fire({
                title: 'បោះបង់ការកក់?',
                text: "តើអ្នកពិតជាចង់បោះបង់ការកក់របស់អតិថិជន " + name + " មែនទេ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: '<i class="fas fa-ban me-1"></i> បាទ បោះបង់វា!',
                cancelButtonText: 'ថយក្រោយ',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('cancel-form-' + id).submit();
            });
        }

        function confirmArchive(id, name) {
            Swal.fire({
                title: 'ទុកក្នុងបណ្ណសារ?',
                text: "តើអ្នកចង់បញ្ជូនទិន្នន័យរបស់ " + name + " ទៅកាន់បណ្ណសារ (Archive) មិនឱ្យបាត់បង់មែនទេ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#64748b',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: '<i class="fas fa-box-archive me-1"></i> បាទ ទុកក្នុងបណ្ណសារ',
                cancelButtonText: 'ថយក្រោយ',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('archive-form-' + id).submit();
            });
        }

        function markAsArrivedIndex(id, plate) {
            Swal.fire({
                title: 'អតិថិជនមកដល់ហើយ?',
                text: `តើឡានស្លាកលេខ ${plate} ពិតជាបានមកដល់ហើយមែនទេ? (ប្រព័ន្ធនឹងចាប់យកឈ្មោះអ្នកជា SA ដោយស្វ័យប្រវត្តិ)`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0891b2',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: '<i class="fas fa-check-circle me-1"></i> មកដល់ហើយ',
                cancelButtonText: 'បោះបង់',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'កំពុងដំណើរការ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    $.ajax({
                        url: `{{ url('workshop/arrive') }}/${id}`,
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function() {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'ទទួលបានជោគជ័យ!', showConfirmButton: false, timer: 2000 });
                            setTimeout(() => window.location.reload(), 1000);
                        },
                        error: function(xhr) {
                            let errMsg = xhr.responseJSON ? xhr.responseJSON.message : 'មានបញ្ហាក្នុងការភ្ជាប់ទៅប្រព័ន្ធ!';
                            Swal.fire('បរាជ័យ', errMsg, 'error');
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
                'WIP (Work In Progress)': 'កំពុងដំណើរការ (WIP)',
                'Waiting Approved': 'កំពុងរង់ចាំការអនុម័ត (Waiting Approved)',
                'Under Diagnosis': 'កំពុងធ្វើការត្រួតពិនិត្យបញ្ហា (Under Diagnosis)',
                'Waiting Parts Order Local': 'កំពុងរង់ចាំបញ្ជាទិញគ្រឿងបន្លាស់ក្នុងស្រុក (Parts Local)',
                'Waiting Parts Order DHL': 'កំពុងរង់ចាំបញ្ជាទិញគ្រឿងបន្លាស់តាម DHL (Parts DHL)',
                'Waiting Parts Order by Sea': 'កំពុងរង់ចាំបញ្ជាទិញគ្រឿងបន្លាស់តាមផ្លូវសមុទ្រ (Parts by Sea)',
                'Waiting Delivery': 'កំពុងរង់ចាំការដឹកជញ្ជូន (Waiting Delivery)',
                'Waiting Service': 'កំពុងរង់ចាំសេវាកម្ម (Waiting Service)'
            };

            if (safeStatus.includes('COMPLETED') || safeStatus.includes('CLOSE')) {
                defaultVal = 'Close';
                isDisabled = 'disabled';
                selectHtml = `<option value="Close" selected>បានបញ្ចប់ (Close)</option>`;
            } else if (safeStatus.includes('ARCHIVE')) {
                defaultVal = 'Archived';
                isDisabled = 'disabled';
                selectHtml = `<option value="Archived" selected>បណ្ណសារ (Archived)</option>`;
            } else if (safeStatus.includes('CANCEL')) {
                defaultVal = 'Cancel';
                isDisabled = 'disabled';
                selectHtml = `<option value="Cancel" selected>បោះបង់ការកក់ (Cancelled)</option>`;
            } else if (safeStatus.includes('PROGRESS') || safeStatus.includes('WIP') || safeStatus.includes('REPAIR')) {
                if (defaultVal === '') defaultVal = 'WIP (Work In Progress)';
                for (let key in allOpts) {
                    let isSel = (key === defaultVal) ? 'selected' : '';
                    selectHtml += `<option value="${key}" ${isSel}>${allOpts[key]}</option>`;
                }
            } else {
                if (defaultVal === '') selectHtml += `<option value="" disabled selected>--- សូមជ្រើសរើសកំណត់ចំណាំ ---</option>`;
                for (let key in allOpts) {
                    let isSel = (key === defaultVal) ? 'selected' : '';
                    selectHtml += `<option value="${key}" ${isSel}>${allOpts[key]}</option>`;
                }
            }

            Swal.fire({
                title: 'កំណត់ចំណាំផ្ទៃក្នុង (Admin Notes)',
                html: `<div class="mb-3 text-muted text-start" style="font-size: 0.9rem;">Status ឡានបច្ចុប្បន្ន៖ <b class="text-danger">${status || 'គ្មាន'}</b></div><select id="swal-note-select" class="form-select w-100" style="padding:12px; border-radius:8px; border:1px solid #cbd5e1; font-weight: 500; background-color: ${isDisabled ? '#f8fafc' : '#ffffff'};" ${isDisabled}>${selectHtml}</select>`,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save me-1"></i> រក្សាទុក',
                cancelButtonText: 'បិទ',
                confirmButtonColor: '#002663',
                cancelButtonColor: '#64748b',
                customClass: { popup: 'rounded-4' },
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    let val = document.getElementById('swal-note-select').value;
                    if (isDisabled) val = defaultVal;
                    if (!val) {
                        Swal.showValidationMessage('សូមជ្រើសរើសកំណត់ចំណាំណាមួយ!');
                        return false;
                    }
                    return $.ajax({
                        url: `/services/note/${id}`,
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        data: { note: val }
                    }).then(res => {
                        if (!res.success) throw new Error(res.message);
                        return res;
                    }).catch(err => {
                        Swal.showValidationMessage(`បរាជ័យ៖ ${err.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(res => {
                if (res.isConfirmed) {
                    Swal.fire({ title: 'ជោគជ័យ!', text: 'បានរក្សាទុក!', icon: 'success', timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
                }
            });
        }

        $(document).ready(function() {
            let lastId = {{ $bookings->firstItem() ? \App\Models\ServiceBooking::max('id') : 0 }};
            let isShowingAlert = false;
            setInterval(function() {
                if (isShowingAlert) return;
                $.get("{{ route('bookings.checkNew') }}", { last_id: lastId }).done(function(res) {
                    if (res.hasNew) {
                        isShowingAlert = true;
                        new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                        Swal.fire({
                            title: '🔔 ការកក់ថ្មីបានចូលមកដល់!',
                            text: `អតិថិជន: ${res.customer} | ស្លាកលេខ: ${res.plate}`,
                            icon: 'info',
                            confirmButtonColor: '#002663',
                            confirmButtonText: 'បង្ហាញទិន្នន័យថ្មី',
                            allowOutsideClick: false,
                            customClass: { popup: 'rounded-4' }
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
                    let status = $(this).data('status');
                    let customerName = $(this).data('customer');
                    if (ts > 0 && (status === 'pending' || status === 'overdue' || status === 'waiting approved')) {
                        let diffLate = nowTs - ts;
                        let diffSoon = ts - nowTs;
                        if (diffLate >= 900 && diffLate < 960 && !$(this).data('alerted-late')) {
                            $(this).data('alerted-late', true);
                            new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                            Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: `អតិថិជន ${customerName} ហួសម៉ោង 15 នាទីហើយ! (Overdue)`, showConfirmButton: false, timer: 8000, customClass: { popup: 'rounded-4 shadow-lg' } });
                        }
                        if (diffSoon >= 840 && diffSoon <= 900 && !$(this).data('alerted-soon')) {
                            $(this).data('alerted-soon', true);
                            new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e => {});
                            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: `អតិថិជន ${customerName} សល់ 15 នាទីទៀតដល់ម៉ោងជួសជុល!`, showConfirmButton: false, timer: 8000, customClass: { popup: 'rounded-4 shadow-lg' } });
                        }
                    }
                });
            }, 10000);
        });

        // 🌟 EXPORT EXCEL 🌟
        function promptCustomExport() {
            Swal.fire({
                title: '<h4 style="color:#002663; font-weight:800; margin:0;">Export ទិន្នន័យ (Excel)</h4>',
                html: `
                    <div class="text-start mt-3">
                        <label class="form-label fw-bold text-slate-600 mb-1" style="font-size: 0.85rem;">ចាប់ពីថ្ងៃ (From Date):</label>
                        <input type="date" id="exportFromDate" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;" value="${$('#fromDate').val()}">
                    </div>
                    <div class="text-start mt-3">
                        <label class="form-label fw-bold text-slate-600 mb-1" style="font-size: 0.85rem;">ដល់ថ្ងៃ (To Date):</label>
                        <input type="date" id="exportToDate" class="form-control" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;" value="${$('#toDate').val()}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-file-excel me-1"></i> ទាញយក',
                cancelButtonText: 'បោះបង់',
                customClass: { popup: 'rounded-4 shadow-lg' },
                preConfirm: () => {
                    const from = document.getElementById('exportFromDate').value;
                    const to = document.getElementById('exportToDate').value;
                    if (!from || !to) {
                        Swal.showValidationMessage('សូមជ្រើសរើសកាលបរិច្ឆេទទាំងពីរ!');
                        return false;
                    }
                    return { from: from, to: to };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeExportAPI(result.value.from, result.value.to, false);
                }
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
            Swal.fire({ title: 'កំពុងប្រមូលទិន្នន័យ...', text: 'សូមរង់ចាំបន្តិច', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
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
                        Swal.fire('មិនមានទិន្នន័យ', `មិនមានការកក់ក្នុងលក្ខខណ្ឌនេះទេ!`, 'info');
                    } else {
                        Swal.fire('បរាជ័យ', 'មិនអាចទាញទិន្នន័យបានទេ', 'error');
                    }
                },
                error: function() { Swal.fire('បរាជ័យ', 'មានបញ្ហាបច្ចេកទេស', 'error'); }
            });
        }

        async function buildExcelFile(dataList, fromDate, toDate, isMonthly) {
            Swal.fire({ title: 'កំពុងរៀបចំ File Excel...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('MG Report');
            const defaultFont = { name: 'Khmer OS Content', size: 10 };
            const headers = [
                { header: 'Customer Name', key: 'col_0' }, { header: 'Contact', key: 'col_1' }, { header: 'Model', key: 'col_2' }, { header: 'Plate', key: 'col_3' },
                { header: 'Mileage', key: 'col_4' }, { header: 'Service Type', key: 'col_5' }, { header: 'Service Advisor', key: 'col_6' }, { header: 'Technician', key: 'col_7' },
                { header: 'Branch', key: 'col_8' }, { header: 'Date & Time', key: 'col_9' }, { header: 'Est. Complete', key: 'col_10' }, { header: 'Status', key: 'col_11' }, 
                { header: 'Start Time', key: 'col_12' }, { header: 'End Time', key: 'col_13' }, { header: 'Duration', key: 'col_14' }, { header: 'Customer Remark', key: 'col_15' }, { header: 'Admin Notes', key: 'col_16' }
            ];
            const totalCols = headers.length;
            worksheet.mergeCells(1, 1, 1, totalCols);
            const titleCell = worksheet.getCell(1, 1);
            titleCell.value = isMonthly ? 'MG SERVICE CAMBODIA - MONTHLY REPORT' : `MG SERVICE CAMBODIA - EXPORT REPORT (${fromDate} TO ${toDate})`;
            titleCell.font = { name: 'Khmer OS Content', size: 14, bold: true, color: { argb: 'FF002663' } };
            titleCell.alignment = { vertical: 'middle', horizontal: 'center' };
            worksheet.mergeCells(2, 1, 2, totalCols);
            const dateCell = worksheet.getCell(2, 1);
            dateCell.value = 'Export Date: ' + new Date().toLocaleString('en-GB');
            dateCell.font = { name: 'Khmer OS Content', size: 10, italic: true, color: { argb: 'FF64748B' } };
            dateCell.alignment = { horizontal: 'center' };
            worksheet.getRow(4).values = headers.map(h => h.header);
            worksheet.columns = headers.map(h => ({ key: h.key }));
            const headerRow = worksheet.getRow(4);
            headerRow.eachCell((cell) => {
                cell.font = { name: 'Khmer OS Content', size: 10, bold: true, color: { argb: 'FFFFFFFF' } };
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
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Export Excel ជោគជ័យ!', showConfirmButton: false, timer: 3000 });
        }

        function showQuickView(name, phone, model, plate, mileage, service, date, time, est_complete, duration, branch, sa, tech, status,
            statusClass, remark, note) {
            let estHtml = est_complete !== 'N/A' ? `<p style="margin: 5px 0 0; font-weight: 700; color: #0891b2; font-size: 0.85rem;"><i class="fas fa-flag-checkered me-1"></i> Est: ${est_complete}</p>` : '';
            let html = `
            <div style="text-align: left; font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; padding: 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 15px; margin-bottom: 20px;">
                    <div><h3 style="color: #002663; font-weight: 800; margin: 0; font-size: 1.4rem;"><i class="fas fa-clipboard-list text-primary me-2"></i> ព័ត៌មានលម្អិត</h3><p style="margin: 3px 0 0; color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Quick View Details</p></div>
                    <span class="status-badge ${statusClass}" style="width: auto; padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">${status}</span>
                </div>
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                        <p style="margin: 0 0 8px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">អតិថិជន / Customer</p><p style="margin: 0; font-weight: 800; font-size: 1.05rem; color: #1e293b;">${name}</p><p style="margin: 5px 0 0; color: #0ea5e9; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-phone-alt me-1 text-slate-400"></i> ${phone}</p>
                    </div>
                    <div style="flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                        <p style="margin: 0 0 8px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">រថយន្ត / Vehicle</p><p style="margin: 0; font-weight: 800; font-size: 1.05rem; color: #002663;">${model} <span style="background: #e2e8f0; padding: 2px 8px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; border: 1px solid #cbd5e1; color: #0f172a; margin-left: 5px;">${plate}</span></p><p style="margin: 5px 0 0; color: #ea580c; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-tachometer-alt me-1 text-slate-400"></i> ODO: ${mileage}</p>
                    </div>
                </div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.01);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <div style="flex: 1;"><p style="margin: 0 0 5px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">សេវាកម្ម / Service Type</p><p style="margin: 0; font-weight: 800; color: #e31e24; font-size: 1rem;">${service}</p></div>
                        <div style="flex: 1; text-align: right;"><p style="margin: 0 0 5px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">កាលបរិច្ឆេទ / Date & Time</p><p style="margin: 0; font-weight: 700; color: #334155; font-size: 0.95rem;"><i class="far fa-calendar text-primary me-1"></i> ${date} <span style="color:#cbd5e1; margin:0 5px;">|</span> <i class="far fa-clock text-primary me-1"></i> ${time}</p>${estHtml}</div>
                    </div><hr style="border-color: #e2e8f0; margin: 12px 0; border-style: dashed;">
                    <div style="display: flex; justify-content: space-between;">
                        <div style="flex: 1;"><p style="margin: 0 0 5px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">សាខា / Branch</p><p style="margin: 0; font-weight: 700; color: #002663; font-size: 0.9rem;"><i class="fas fa-map-marker-alt text-danger me-1"></i> ${branch}</p></div>
                        <div style="flex: 1; text-align: right;"><p style="margin: 0 0 5px; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">បុគ្គលិក / Staffs</p><p style="margin: 0; font-weight: 700; font-size: 0.9rem;"><span class="text-primary" title="Service Advisor"><i class="fas fa-user-tie"></i> ${sa}</span><span style="color: #cbd5e1; margin: 0 5px;">|</span><span class="text-emerald-600" title="Technician"><i class="fas fa-wrench"></i> ${tech}</span></p></div>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="background: #fff7ed; padding: 12px 15px; border-radius: 10px; border: 1px solid #fed7aa; border-left: 4px solid #f97316;"><p style="margin: 0 0 5px; color: #c2410c; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">សំណូមពរអតិថិជន / Remark</p><p style="margin: 0; font-size: 0.9rem; color: #9a3412; font-style: italic; line-height: 1.5;">${remark ? `<i class="fas fa-quote-left me-1 opacity-50"></i> ${remark}` : '<span style="color: #fdba74;">មិនមានសំណូមពរទេ...</span>'}</p></div>
                    <div style="background: #f8fafc; padding: 12px 15px; border-radius: 10px; border: 1px solid #e2e8f0; border-left: 4px solid #64748b;"><p style="margin: 0 0 5px; color: #475569; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">កំណត់ចំណាំផ្ទៃក្នុង / Admin Note</p><p style="margin: 0; font-size: 0.9rem; color: #334155; font-style: italic; line-height: 1.5;">${note ? `<i class="fas fa-sticky-note text-slate-400 me-1"></i> ${note}` : '<span style="color: #cbd5e1;">មិនមានកំណត់ចំណាំទេ...</span>'}</p></div>
                </div>
            </div>`;
            Swal.fire({ html: html, showCloseButton: true, showConfirmButton: false, width: '650px', customClass: { popup: 'rounded-4 shadow-lg' } });
        }

        function showCompleteJobPopup(id, name, phone, vehicle, plate, mileage, service, date, start, end, duration, branch, sa, tech) {
            let ticketHTML = `
            <div id="printable-ticket" style="font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; padding: 25px; color: #1e293b; text-align: left; background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px dashed #cbd5e1; padding-bottom: 15px; margin-bottom: 20px;">
                    <div><h2 style="color: #002663; font-weight: 900; margin: 0; font-size: 1.8rem; letter-spacing: -1px;">MG <span style="color: #e31e24;">SERVICE</span></h2><p style="margin: 5px 0 0; color: #64748b; font-size: 0.8rem; font-weight: 600;">${branch}</p></div>
                    <div style="text-align: right;"><h3 style="color: #334155; font-weight: 800; margin: 0; font-size: 1.2rem; text-transform: uppercase;">SERVICE RECEIPT</h3><p style="margin: 5px 0 0; color: #64748b; font-size: 0.85rem;">លេខយោង / Receipt No: <strong style="color: #e31e24;">#MG-${id}</strong></p><p style="margin: 2px 0 0; color: #64748b; font-size: 0.85rem;">កាលបរិច្ឆេទ / Date: <strong>${date}</strong></p></div>
                </div>
                <div style="display: flex; justify-content: space-between; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; background: #f8fafc;"><p style="margin: 0 0 8px; color: #94a3b8; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">អតិថិជន / Billed To</p><p style="margin: 0; font-weight: 800; font-size: 1rem; color: #0f172a;">${name}</p><p style="margin: 5px 0 0; color: #475569; font-size: 0.85rem;"><i class="fas fa-phone-alt me-1" style="font-size: 0.75rem; color:#94a3b8;"></i> ${phone}</p></div>
                    <div style="flex: 1; border: 1px solid #e2e8f0; padding: 15px; border-radius: 12px; background: #f8fafc;"><p style="margin: 0 0 8px; color: #94a3b8; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">រថយន្ត / Vehicle Info</p><p style="margin: 0; font-weight: 800; font-size: 1rem; color: #002663;">${vehicle} <span style="background: #e2e8f0; color: #0f172a; padding: 2px 8px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; margin-left: 5px; border: 1px solid #cbd5e1;">${plate}</span></p><p style="margin: 5px 0 0; color: #ea580c; font-size: 0.85rem; font-weight: 600;"><i class="fas fa-tachometer-alt me-1" style="font-size: 0.75rem; color:#94a3b8;"></i> ODO: ${mileage}</p></div>
                </div>
                <div style="border-radius: 12px; overflow: hidden; border: 1px solid #cbd5e1; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse; margin: 0; font-size: 0.85rem;">
                        <thead><tr style="background: #002663; color: white;"><th style="padding: 12px 15px; text-align: left; font-weight: 600;">ការពិពណ៌នាសេវាកម្ម / Description</th><th style="padding: 12px 15px; text-align: right; font-weight: 600;">អ្នកទទួល / Service Advisor</th></tr></thead>
                        <tbody><tr style="background: #ffffff;"><td style="padding: 15px; font-weight: 700; color: #e31e24; font-size: 0.95rem;">${service}</td><td style="padding: 15px; text-align: right; font-weight: 600; color: #334155;">${sa}</td></tr></tbody>
                    </table>
                </div>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 0.85rem; color: #475569; line-height: 1.6;"><div><i class="far fa-clock me-1 text-slate-400"></i> ម៉ោងចាប់ផ្តើម: <strong style="color:#0f172a;">${start}</strong></div><div><i class="fas fa-check-circle me-1 text-emerald-500"></i> ម៉ោងបញ្ចប់: <strong style="color:#0f172a;">${end}</strong></div><div><i class="fas fa-wrench me-1 text-slate-400"></i> ជាងជួសជុល (Tech): <strong style="color:#0ea5e9;">${tech}</strong></div></div>
                    <div style="text-align: right; background: white; padding: 12px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"><p style="margin: 0; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">រយៈពេលសរុប / Duration</p><p style="margin: 2px 0 0; font-size: 1.4rem; font-weight: 900; color: #002663;">${duration}</p></div>
                </div>
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                    <p style="font-size: 0.8rem; color: #64748b; font-weight: 500; margin-bottom: 10px;">សូមអរគុណសម្រាប់ការប្រើប្រាស់សេវាកម្មរបស់យើងខ្ញុំ!</p>
                    <i class="fas fa-barcode" style="font-size: 3.5rem; color: #1e293b; letter-spacing: -3px; transform: scaleY(1.5);"></i><p style="font-size: 0.7rem; color: #94a3b8; margin-top: 10px; font-family: monospace;">*${id}*</p>
                </div>
            </div>`;
            Swal.fire({ html: ticketHTML, width: '580px', showCloseButton: true, showDenyButton: true, showConfirmButton: true, confirmButtonText: '<i class="fas fa-print me-1"></i> បោះពុម្ព (Print)', confirmButtonColor: '#002663', denyButtonText: '<i class="fas fa-file-pdf me-1"></i> Save PDF', denyButtonColor: '#e31e24', background: '#ffffff', customClass: { popup: 'rounded-4 shadow-lg' } }).then((result) => { if (result.isConfirmed) printTicketFromPopup(); else if (result.isDenied) saveTicketAsPDF(plate); });
        }

        function printTicketFromPopup() {
            const printContent = document.getElementById('printable-ticket').innerHTML;
            const windowPrint = window.open('', '', 'width=800,height=900');
            windowPrint.document.write(`<html><head><title>Print Receipt</title><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><style>body { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; padding: 20px; color: #1e293b; } @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }</style></head><body>${printContent}</body></html>`);
            windowPrint.document.close();
            windowPrint.focus();
            setTimeout(() => { windowPrint.print(); windowPrint.close(); }, 800);
        }

        function saveTicketAsPDF(plate) {
            const element = document.getElementById('printable-ticket');
            const opt = { margin: 0.3, filename: 'MG_Service_Receipt_' + plate + '.pdf', image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a5', orientation: 'portrait' } };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endsection