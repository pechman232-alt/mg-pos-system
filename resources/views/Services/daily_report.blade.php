@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@300;400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<style>
    /* ============================================================
       DESIGN TOKENS — shared with the Workshop Live Board so the
       two screens read as one product, not two different apps.
       navy = brand / neutral info · blue = active work in progress
       amber = awaiting action · teal = completed / positive
       steel = paused / muted · brand red = delayed / warning
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
        --brand-red: #D0262C;
        --brand-red-dark: #B3211F;
        --brand-red-tint: #FBEAEA;
        --bg-main: #F5F7FA;
        --border-light: #E4E9F0;
        --text-dark: #16233B;
        --text-muted: #5B6B84;
        --text-faint: #94A3B8;
    }

    .report-container { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; background: var(--bg-main); color: var(--text-dark); }

    .custom-card {
        border-radius: 18px; border: 1px solid var(--border-light);
        box-shadow: 0 1px 2px rgba(16,24,40,0.03);
        background: #ffffff;
    }
    .branch-card { cursor: pointer; transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease; }
    .branch-card:hover { transform: translateY(-4px); box-shadow: 0 16px 28px -10px rgba(11,42,82,0.16); border-color: #d3dae4; }

    .chip-count { background: var(--active-blue-tint); color: var(--active-blue); font-weight: 700; font-size: 0.72rem; padding: 4px 11px; border-radius: 999px; }

    .stat-box { background: #f8fafc; border: 1px solid var(--border-light); border-left: 3px solid var(--steel); border-radius: 12px; padding: 10px; }
    .stat-box.stat-wip { border-left-color: var(--active-blue); }
    .delayed-box { background-color: var(--brand-red-tint) !important; border-color: #fca5a5 !important; border-left-color: var(--brand-red) !important; }
    .delayed-box .title { color: var(--brand-red) !important; }

    /* ---- eyebrow labels: one consistent typographic voice for every micro-label ---- */
    .eyebrow-label { font-size: 9.5px; font-weight: 700; letter-spacing: 0.6px; color: var(--text-faint); text-transform: uppercase; display: block; margin-bottom: 6px; }

    .form-select, .form-control { border-radius: 10px !important; border-color: var(--border-light) !important; font-weight: 600; font-size: 0.85rem; }
    .form-select:focus, .form-control:focus { border-color: var(--active-blue) !important; box-shadow: 0 0 0 3px rgba(47,111,222,0.15) !important; }

    /* ---- shared action-button component — same classes as the Workshop Live Board ---- */
    .btn-action {
        display: inline-flex; align-items: center; justify-content: center; gap: 7px;
        border-radius: 10px; font-weight: 600; font-size: 0.875rem; border: none; cursor: pointer;
        padding: 10px 18px; text-decoration: none; line-height: 1;
        box-shadow: 0 4px 10px -4px rgba(16,24,40,0.28);
        transition: background-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
    }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 8px 16px -6px rgba(16,24,40,0.32); text-decoration: none; }
    .btn-action:active { transform: translateY(0); }
    .btn-navy { background: var(--ink-navy); color: #fff; }
    .btn-navy:hover { background: var(--navy-light); color: #fff; }
    .btn-teal { background: var(--teal); color: #fff; }
    .btn-teal:hover { background: var(--teal-dark); color: #fff; }
    .btn-outline { background: #fff; color: var(--text-muted); border: 2px solid var(--border-light); box-shadow: none; }
    .btn-outline:hover { border-color: #cbd5e1; background: #f8fafc; color: var(--text-muted); box-shadow: none; }

    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ---- table: navy header reads as an official report, zebra rows carry density ---- */
    .custom-table { border-collapse: collapse; width: 100%; }
    .custom-table thead th {
        background-color: var(--ink-navy) !important; color: #fff !important; font-weight: 700; font-size: 0.7rem;
        border: 1px solid var(--navy-light); vertical-align: middle; text-transform: uppercase; letter-spacing: 0.5px;
        white-space: nowrap; padding: 12px 10px;
    }
    .custom-table thead th i { opacity: 0.85; }
    .custom-table td {
        vertical-align: middle; font-size: 0.85rem; border: 1px solid var(--border-light);
        white-space: nowrap; padding: 11px 10px;
    }
    .custom-table tbody tr:nth-child(even) { background: #FAFBFD; }
    .custom-table tbody tr:hover { background: var(--active-blue-tint) !important; }

    /* ---- semantic badges: one meaning per color, reused everywhere ---- */
    .badge-soft { padding: 5px 11px; border-radius: 7px; font-weight: 700; font-size: 0.72rem; letter-spacing: 0.2px; }
    .badge-wip { background: var(--active-blue-tint); color: var(--active-blue); }
    .badge-waiting { background: var(--amber-tint); color: var(--amber); }
    .badge-completed { background: var(--teal-tint); color: var(--teal); }
    .badge-missing { background: var(--brand-red-tint); color: var(--brand-red); }
    .badge-plate {
        font-family: 'Space Grotesk', monospace; font-weight: 700; font-size: 0.78rem;
        background: #F5F7FA; border: 1px solid var(--border-light); color: var(--ink-navy);
        padding: 4px 10px; border-radius: 7px;
    }

    .btn-vin {
        border: 1.5px solid var(--active-blue); color: var(--active-blue); background: #fff;
        border-radius: 8px; padding: 3px 7px; font-size: 0.72rem; line-height: 1;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .btn-vin:hover { background: var(--active-blue); color: #fff; }

    .remark-box {
        background: var(--amber-tint); border-left: 3px solid var(--amber); padding: 8px 10px; border-radius: 8px;
        font-size: 0.75rem; color: var(--amber-ink); width: max-content; max-width: 100%;
        white-space: normal; line-height: 1.5; max-height: 80px; overflow-y: auto; text-align: left;
    }
    .remark-empty { color: var(--text-faint); font-style: italic; font-size: 0.75rem; }
    .history-wrapper { display: flex; flex-direction: column; gap: 6px; max-height: 80px; overflow-y: auto; padding-right: 4px; min-width: 250px; }
    .history-item { background: var(--steel-tint); border: 1px solid var(--border-light); border-radius: 8px; padding: 6px 8px; text-align: left; white-space: normal; }
    .history-date { font-size: 0.7rem; color: var(--active-blue); margin-bottom: 2px; }
    .history-remark { font-size: 0.75rem; color: var(--text-dark); line-height: 1.3; }
</style>

<div class="container-fluid py-4 report-container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-extrabold m-0" style="color: var(--ink-navy);"><i class="fas fa-chart-pie me-2" style="color: var(--brand-red);"></i> Pending & WIP YTD</h2>
            <p class="m-0 mt-1" style="font-size: 0.85rem; color: var(--text-muted);">របាយការណ៍ឡានកំពុងបន្តធ្វើ និងរង់ចាំ (Management View)</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="promptCustomExport()" class="btn-action btn-teal">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <a href="{{ route('services.index') }}" class="btn-action btn-outline">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>
<!-- 🔍 Filter Form -->
<form method="GET" action="{{ route('services.dailyReport') }}" class="custom-card p-3 mb-4">
    <div class="row g-2 align-items-end">
        <!-- 🌟 ប្រអប់រើសសាខា (បាន Update ខ្លីជាងមុន ព្រោះយើងច្រោះពី Controller រួចហើយ) 🌟 -->
        <div class="col-md-2">
            <label class="form-label eyebrow-label">សាខា (Branch)</label>
            <select name="branch" class="form-select fw-bold" onchange="this.form.submit()">
                <option value="all">🏢 គ្រប់សាខាទាំងអស់</option>
                @foreach($allBranches as $branchOp)
                    <option value="{{ $branchOp }}" {{ $selectedBranch == $branchOp ? 'selected' : '' }}>
                        {{ $branchOp }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2">
            <label class="form-label eyebrow-label">ស្ថានភាព (Status)</label>
            <select name="status" class="form-select fw-bold" style="color: var(--active-blue);" onchange="this.form.submit()">
                <option value="pending_wip" {{ $selectedStatus == 'pending_wip' ? 'selected' : '' }}>🔥 កំពុងចាំ & ធ្វើ (Active)</option>
                <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>⏳ ឡានកំពុងចាំ (Waiting)</option>
                <option value="wip" {{ $selectedStatus == 'wip' ? 'selected' : '' }}>🔧 កំពុងធ្វើ (In Progress)</option>
                <option value="Completed" {{ $selectedStatus == 'Completed' ? 'selected' : '' }}>✅ បានបញ្ចប់ (Completed)</option>
                <option value="all" {{ $selectedStatus == 'all' ? 'selected' : '' }}>🌟 គ្រប់ Status ទាំងអស់</option>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label eyebrow-label">ចាប់ពីថ្ងៃ (From Date)</label>
            <input type="date" id="fromDate" name="from_date" value="{{ $fromDate }}" class="form-control" onchange="this.form.submit()">
        </div>
        
        <div class="col-md-3">
            <label class="form-label eyebrow-label">ដល់ថ្ងៃ (To Date)</label>
            <input type="date" id="toDate" name="to_date" value="{{ $toDate }}" class="form-control" onchange="this.form.submit()">
        </div>
        
        <div class="col-md-2">
            <button type="submit" class="btn-action btn-navy w-100">
                <i class="fas fa-search"></i> ស្វែងរក
            </button>
        </div>
    </div>
</form>

    <!-- 📊 Branch Summary Cards -->
    <div class="row g-3 mb-4">
        @foreach($branchSummaries as $branchName => $summary)
            @php
                // 🌟 មុខងារបំប្លែងលេខទៅជាឈ្មោះសាខា 🌟
                $displayBranch = $branchName;
                if (is_numeric($displayBranch)) {
                    $bMap = [
                        '10' => 'Service Btb (battambang)',
                        '11' => 'Service 50m',
                        '12' => 'Service Sr (siem Reap)',
                        '13' => 'Service Hq (head Office)'
                    ];
                    if (isset($branches)) {
                        $matched = collect($branches)->firstWhere('id', $displayBranch);
                        if ($matched) {
                            $displayBranch = $matched->branch_name;
                        } else {
                            $displayBranch = $bMap[$displayBranch] ?? $displayBranch;
                        }
                    } else {
                        $displayBranch = $bMap[$displayBranch] ?? $displayBranch;
                    }
                }
                $displayBranch = str_ireplace(['MG ', 'Mg '], '', $displayBranch);
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="custom-card branch-card h-100" onclick="showBranchDetails('{{ addslashes($displayBranch) }}', {{ json_encode($summary['cars']) }})">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-extrabold m-0" style="font-size: 0.95rem; color: var(--ink-navy);"><i class="fas fa-map-marker-alt me-2" style="color: var(--brand-red);"></i> {{ $displayBranch }}</h6>
                            <span class="chip-count">{{ $summary['total'] }} គ្រឿង</span>
                        </div>

                        <!-- 🌟 បន្ថែមប្រអប់រាប់ឡានយឺតយ៉ាវ (Delayed) 🌟 -->
                        <div class="d-flex justify-content-between text-center mt-3">
                            <div class="stat-box flex-fill me-1 p-2">
                                <div class="fw-bold fs-6" style="color: var(--steel);">{{ $summary['waiting'] }}</div>
                                <div class="mt-1" style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted);">រង់ចាំ</div>
                            </div>
                            <div class="stat-box stat-wip flex-fill mx-1 p-2">
                                <div class="fw-bold fs-6" style="color: var(--active-blue);">{{ $summary['wip'] }}</div>
                                <div class="mt-1" style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted);">បន្តធ្វើ</div>
                            </div>
                            <div class="stat-box delayed-box flex-fill ms-1 p-2">
                                <div class="fw-bold fs-6" style="color: var(--brand-red);">{{ $summary['delayed'] }}</div>
                                <div class="title mt-1" style="font-size: 0.65rem; font-weight: 700;">យឺត/គ្មានម៉ោង</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 📋 Main Data Table -->
    <div class="custom-card overflow-hidden">
        <div class="table-responsive custom-scrollbar" style="max-height: 650px;">
            <table class="table table-hover custom-table mb-0">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center">Branch</th>
                        <th>Customer Name</th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">Model</th>
                        <th class="text-center">Plate</th>
                        <th class="text-center"><i class="fas fa-car"></i> VIN (តួឡាន)</th>
                        <th class="text-center">Mileage</th>
                        <th class="text-center">Service Type</th>
                        <th class="text-center">Service Advisor</th>
                        <th class="text-center">Technician</th>
                        <th class="text-center"><i class="fas fa-sign-in-alt"></i> Created Date</th>
                        <th class="text-center"><i class="far fa-calendar-check"></i> Booking Date</th>

                        <th class="text-center">Arrived Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center"><i class="fas fa-clock"></i> Est. Complete</th>
                        <th style="min-width: 320px;">Remark / Note</th>
                        <th style="min-width: 300px;">Estimation History</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $row)
                        @php
                            // 🌟 មុខងារបំប្លែងលេខទៅជាឈ្មោះសាខាក្នុង Table 🌟
                            $rowLoc = $row->location ?? 'N/A';
                            if (is_numeric($rowLoc)) {
                                $bMapTable = [
                                    '10' => 'Service Btb (battambang)',
                                    '11' => 'Service 50m',
                                    '12' => 'Service Sr (siem Reap)',
                                    '13' => 'Service Hq (head Office)'
                                ];
                                if (isset($branches)) {
                                    $matchedRow = collect($branches)->firstWhere('id', $rowLoc);
                                    if ($matchedRow) {
                                        $rowLoc = $matchedRow->branch_name;
                                    } else {
                                        $rowLoc = $bMapTable[$rowLoc] ?? $rowLoc;
                                    }
                                } else {
                                    $rowLoc = $bMapTable[$rowLoc] ?? $rowLoc;
                                }
                            }
                            $rowLoc = str_ireplace(['MG ', 'Mg '], '', $rowLoc);
                        @endphp
                        <tr>
                            <td class="text-center" style="color: var(--text-faint);">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold" style="color: var(--text-muted);">{{ $rowLoc }}</td>
                            <td class="fw-bold" style="color: var(--text-dark);">{{ $row->customer_name ?? 'N/A' }}</td>
                            <td class="text-center" style="color: var(--active-blue);">{{ $row->contact ?? 'N/A' }}</td>
                            <td class="text-center">{{ $row->model ?? 'N/A' }}</td>
                            <td class="text-center"><span class="badge-plate">{{ $row->plate ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <span id="vin-text-{{ $row->id }}" class="me-2" style="font-family: monospace; font-weight: 600; color: var(--text-muted);">
                                        {{ $row->vin ?? 'N/A' }}
                                    </span>
                                    <button type="button" onclick="promptUpdateVin({{ $row->id }}, '{{ addslashes($row->vin ?? '') }}')" class="btn-vin" title="បញ្ចូលលេខតួឡាន (VIN)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="text-center">{{ $row->mileage ? number_format($row->mileage) . ' km' : 'N/A' }}</td>
                            <td class="text-center fw-semibold" style="color: var(--brand-red);">{{ $row->service_type ?? 'N/A' }}</td>
                            <td class="text-center" style="color: var(--active-blue);">{{ $row->sa_name ?? 'N/A' }}</td>
                            <td class="text-center" style="color: var(--teal);">{{ $row->technician_name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if(!empty($row->created_at))
                                    <span class="d-block fw-bold" style="color: var(--text-dark);">{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</span>
                                    <span class="d-block" style="font-size: 0.75rem; margin-top: 2px; color: var(--text-faint);"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($row->created_at)->format('h:i A') }}</span>
                                @else
                                    <span style="color: var(--text-faint);">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($row->booking_date))
                                    <span class="d-block fw-bold" style="color: var(--text-dark);">{{ \Carbon\Carbon::parse($row->booking_date)->format('d-M-Y') }}</span>
                                    @if(!empty($row->booking_time))
                                        <span class="d-block" style="font-size: 0.75rem; margin-top: 2px; color: var(--active-blue);"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($row->booking_time)->format('h:i A') }}</span>
                                    @endif
                                @else
                                    <span style="color: var(--text-faint);">N/A</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if(!empty($row->arrived_date))
                                    <span class="d-block fw-bold" style="color: var(--text-dark);">{{ \Carbon\Carbon::parse($row->arrived_date)->format('d-M-Y') }}</span>
                                @else
                                    <span style="color: var(--text-faint);">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(in_array(strtolower($row->status), ['in progress', 'wip', 'under repair', 'under diagnosis']))
                                    <span class="badge-soft badge-wip">{{ $row->status }}</span>
                                @elseif(strtolower($row->status) === 'completed')
                                    <span class="badge-soft badge-completed">{{ $row->status }}</span>
                                @else
                                    <span class="badge-soft badge-waiting">{{ $row->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($row->estimated_end_datetime))
                                    @php $isOverdue = \Carbon\Carbon::parse($row->estimated_end_datetime)->isPast(); @endphp
                                    <span class="fw-bold" style="color: {{ $isOverdue ? 'var(--brand-red)' : 'var(--teal)' }};">{{ \Carbon\Carbon::parse($row->estimated_end_datetime)->format('d-M-Y h:i A') }}</span>
                                @else
                                    <span class="badge-soft badge-missing">Missing Est.</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $remarkText = trim($row->remark ?? $row->note ?? ''); @endphp
                                @if(!empty($remarkText) && $remarkText !== 'N/A' && $remarkText !== 'null')
                                    <div class="remark-box custom-scrollbar text-start mx-auto"><i class="fas fa-comment-dots me-1" style="color: var(--amber);"></i> {{ $remarkText }}</div>
                                @else
                                    <span class="remark-empty"><i class="fas fa-minus"></i> គ្មានកំណត់ចំណាំ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($row->estimation_history) && $row->estimation_history !== 'N/A' && $row->estimation_history !== 'null')
                                    @php $histories = json_decode($row->estimation_history, true); @endphp
                                    @if(is_array($histories) && count($histories) > 0)
                                        <div class="history-wrapper custom-scrollbar mx-auto">
                                            @foreach($histories as $hist)
                                                <div class="history-item">
                                                    <div class="history-date fw-bold"><i class="far fa-clock me-1"></i> {{ isset($hist['estimated_datetime']) ? \Carbon\Carbon::parse($hist['estimated_datetime'])->format('d-M-y h:i A') : 'N/A' }}</div>
                                                    <div class="history-remark"><i class="fas fa-angle-right me-1" style="color: var(--text-faint); font-size: 0.6rem;"></i> {{ $hist['remark'] ?? 'គ្មានការកត់សម្គាល់' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="font-size:0.75rem; color: var(--text-faint);">{{ $row->estimation_history }}</span>
                                    @endif
                                @else
                                    <span style="color: var(--text-faint);"><i class="fas fa-minus"></i> N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="18" class="text-center py-5" style="color: var(--text-muted);">
                                <i class="fas fa-folder-open fs-1 mb-3 d-block" style="color: var(--border-light);"></i>
                                <span>មិនមានទិន្នន័យសម្រាប់បង្ហាញទេ...</span>
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
            Swal.fire({ icon: 'info', title: 'គ្មានទិន្នន័យ', text: 'មិនមានឡានកំពុងជាប់ក្នុងសាខានេះទេ។' });
            return;
        }

        let tableRows = '';
        cars.forEach((car, i) => {
            let statusColor = car.is_wip ? 'var(--active-blue)' : 'var(--amber)';
            // លោតពណ៌ក្រហម បើឡាននោះត្រូវបានចាត់ទុកថាយឺត (is_delayed)
            let estColor = car.is_delayed ? 'var(--brand-red)' : 'var(--teal)';

            tableRows += `
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: center;">${i + 1}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; font-weight: bold; font-size: 0.85rem;">
                        ${car.customer} <br><span style="font-size:0.75rem; color:#64748b; font-family:monospace;">${car.plate}</span>
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: ${statusColor}; font-weight: 700; font-size: 0.75rem;">${car.status}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #e2e8f0; color: ${estColor}; font-weight: bold; font-size: 0.8rem;">${car.est_complete}</td>
                </tr>
            `;
        });

        Swal.fire({
            title: `<div class="text-start"><h4 class="fw-extrabold m-0" style="color: var(--ink-navy);"><i class="fas fa-map-marker-alt me-2" style="color: var(--brand-red);"></i>សាខា ${branchName}</h4></div>`,
            html: `
                <div class="custom-scrollbar" style="max-height: 400px; overflow-y: auto; text-align: left;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead style="position: sticky; top: 0; background: #EEF2F7; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                            <tr>
                                <th style="padding: 10px; text-align: center; border: 1px solid #e2e8f0;">No.</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0;">អតិថិជន & ស្លាកលេខ</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0;">Status</th>
                                <th style="padding: 10px; border: 1px solid #e2e8f0;"><i class="fas fa-clock" style="color: var(--active-blue);"></i> Est. Complete</th>
                            </tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                </div>
            `,
            width: '700px',
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    function promptCustomExport() {
        const from = document.getElementById('fromDate').value;
        const to = document.getElementById('toDate').value;
        const status = document.querySelector('select[name="status"]').value;

        // ចាប់យកឈ្មោះសាខាពេញ ដើម្បីយកទៅបង្ហាញលើ Header Excel
        const branchSelect = document.querySelector('select[name="branch"]');
        const branchText = branchSelect.options[branchSelect.selectedIndex].text;
        const branchVal = branchSelect.value;

        Swal.fire({ title: 'កំពុងប្រមូលទិន្នន័យ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        $.ajax({
            url: window.location.href.split('?')[0],
            type: 'GET',
            data: { fetch_all_for_export: 1, from_date: from, to_date: to, status: status, branch: branchVal },
            success: async function(res) {
                if (res.success && res.data.length > 0) {
                    // បោះទិន្នន័យទៅឱ្យ Function Build Excel ព្រមទាំង ថ្ងៃខែ និង សាខា
                    await buildExcelFile(res.data, from, to, branchText);
                } else {
                    Swal.fire('មិនមានទិន្នន័យ', 'មិនមានទិន្នន័យក្នុងសាខា ឬចន្លោះពេលនេះទេ!', 'info');
                }
            }
        });
    }

    async function buildExcelFile(dataList, fromDate, toDate, branchText) {
        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet('Pending Report');

        const totalCols = 18; // ចំនួន Column សរុប (A ដល់ R)

        // ==========================================
        // 1. រៀបចំ Header (លាតសន្ធឹងពេញតារាង និងចំកណ្តាល)
        // ==========================================
        // 🌟 ជួរទី១៖ ចំណងជើងធំ 🌟
        worksheet.mergeCells(1, 1, 1, totalCols);
        const titleRow = worksheet.getCell(1, 1);
        titleRow.value = 'PENDING & WIP REPORT (MANAGEMENT VIEW)';
        titleRow.font = { size: 16, bold: true, color: { argb: 'FF0052CC' } }; // ពណ៌ខៀវ
        titleRow.alignment = { vertical: 'middle', horizontal: 'center' };

        // ដាក់បន្ទាត់ (Border) ជុំវិញចំណងជើងធំឱ្យដូចគំរូ
        for(let i=1; i<=totalCols; i++) {
            worksheet.getCell(1, i).border = {
                top: {style:'medium', color: {argb:'FF0052CC'}},
                bottom: {style:'medium', color: {argb:'FF0052CC'}},
                left: i === 1 ? {style:'medium', color: {argb:'FF0052CC'}} : undefined,
                right: i === totalCols ? {style:'medium', color: {argb:'FF0052CC'}} : undefined
            };
        }

        // 🌟 ជួរទី២៖ ព័ត៌មានសាខា ថ្ងៃខែ និងម៉ោង Export (ដាក់ចំកណ្តាល) 🌟
        let dateRangeText = (fromDate || toDate) ? `${fromDate || '...'} ដល់ ${toDate || '...'}` : 'All Time';
        worksheet.mergeCells(2, 1, 2, totalCols);
        const subTitleRow = worksheet.getCell(2, 1);
        subTitleRow.value = `Branch: ${branchText}   |   Date Range: ${dateRangeText}   |   Exported Date: ${new Date().toLocaleString('en-GB')}`;
        subTitleRow.font = { size: 10, italic: true, color: { argb: 'FF475569' } };
        subTitleRow.alignment = { vertical: 'middle', horizontal: 'center' };

        // ជួរទី៣ ទុកចោលឱ្យស្រឡះ

        // ==========================================
        // 2. រៀបចំ Column Headers (ចាប់ផ្តើមនៅជួរទី ៤)
        // ==========================================
        const headers = [
            { header: 'No.', key: 'no', width: 8 }, { header: 'Branch', key: 'location', width: 15 },
            { header: 'Customer Name', key: 'customer_name', width: 25 }, { header: 'Contact', key: 'contact', width: 15 },
            { header: 'Car Model', key: 'model', width: 15 }, { header: 'Plate', key: 'plate', width: 15 },
            { header: 'VIN', key: 'vin', width: 22 }, { header: 'Mileage', key: 'mileage', width: 12 },
            { header: 'Service Type', key: 'service_type', width: 20 }, { header: 'Service Advisor', key: 'sa_name', width: 20 },
            { header: 'Technician', key: 'technician_name', width: 20 },
            { header: 'Booking Date', key: 'booking_date', width: 20 }, { header: 'Created Date', key: 'created_at', width: 20 },
            { header: 'Arrived Date', key: 'arrived_date', width: 18 }, { header: 'Status', key: 'status', width: 20 },
            { header: 'Est. Complete', key: 'estimated_end_datetime', width: 22 }, { header: 'Remark', key: 'remark', width: 35 },
            { header: 'History', key: 'estimation_history', width: 60 }
        ];

        worksheet.columns = headers.map(h => ({ key: h.key, width: h.width }));

        const headerRow = worksheet.getRow(4);
        headerRow.values = headers.map(h => h.header);

        headerRow.eachCell((cell, colNumber) => {
            cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF003399' } }; // ពណ៌ខៀវចាស់
            cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
            // ដាក់បន្ទាត់ក្រឡាឱ្យក្បាលតារាង
            cell.border = { top: {style:'thin'}, bottom: {style:'thin'}, left: {style:'thin'}, right: {style:'thin'} };
        });

        worksheet.autoFilter = { from: { row: 4, column: 1 }, to: { row: 4, column: totalCols } };

        // ==========================================
        // 3. បញ្ចូលទិន្នន័យ (Data)
        // ==========================================
        dataList.forEach((rowData, index) => {
            let historyFormatted = 'N/A';
            if (rowData.estimation_history && rowData.estimation_history !== 'N/A' && rowData.estimation_history !== 'null') {
                try {
                    let parsedHistory = JSON.parse(rowData.estimation_history);
                    if (Array.isArray(parsedHistory) && parsedHistory.length > 0) {
                        historyFormatted = parsedHistory.map(h => {
                            let dt = h.estimated_datetime ? h.estimated_datetime.replace('T', ' ') : 'N/A';
                            let user = h.updated_by ? `(By: ${h.updated_by})` : '';
                            let rem = h.remark ? h.remark : 'គ្មានការកត់សម្គាល់';
                            return `• ${dt} ${user}\n  └ ${rem}`;
                        }).join('\n\n');
                    } else {
                        historyFormatted = rowData.estimation_history;
                    }
                } catch (e) {
                    historyFormatted = rowData.estimation_history;
                }
            }

            // 🌟 មុខងារបំប្លែងលេខទៅជាឈ្មោះសាខាក្នុងការ Export 🌟
            let exportLoc = rowData.location || 'N/A';
            let locMapExport = {
                '10': 'Service Btb (battambang)',
                '11': 'Service 50m',
                '12': 'Service Sr (siem Reap)',
                '13': 'Service Hq (head Office)'
            };
            if (locMapExport[exportLoc]) {
                exportLoc = locMapExport[exportLoc];
            }
            exportLoc = exportLoc.replace(/MG |Mg /gi, '');

            let row = worksheet.addRow([
                index + 1, exportLoc,
                rowData.customer_name || 'N/A', rowData.contact || 'N/A', rowData.model || 'N/A',
                rowData.plate || 'N/A', rowData.vin || 'N/A', rowData.mileage ? rowData.mileage + ' km' : 'N/A',
                rowData.service_type || 'N/A', rowData.sa_name || 'N/A', rowData.technician_name || 'N/A',
                rowData.booking_date ? rowData.booking_date.substring(0, 10) + (rowData.booking_time ? ' ' + rowData.booking_time : '') : 'N/A',
                rowData.created_at ? rowData.created_at.replace('T', ' ').substring(0, 19) : 'N/A',
                rowData.arrived_date || 'N/A', rowData.status || 'N/A',
                rowData.estimated_end_datetime ? rowData.estimated_end_datetime.replace('T', ' ') : 'Missing Est.',
                rowData.remark || rowData.note || 'N/A',
                historyFormatted
            ]);

            // 🌟 កំណត់ការតម្រឹម និងគូសបន្ទាត់ក្រឡា (Borders) 🌟
            row.eachCell((cell, colNumber) => {
                // គូសបន្ទាត់ក្រឡាពណ៌ប្រផេះស្តើងៗ ជុំវិញប្រអប់ទិន្នន័យនីមួយៗ
                cell.border = {
                    top: {style:'thin', color:{argb:'FFCBD5E1'}}, bottom: {style:'thin', color:{argb:'FFCBD5E1'}},
                    left: {style:'thin', color:{argb:'FFCBD5E1'}}, right: {style:'thin', color:{argb:'FFCBD5E1'}}
                };

                if (colNumber === 17 || colNumber === 18) {
                    cell.alignment = { vertical: 'top', horizontal: 'left', wrapText: true };
                } else {
                    cell.alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
                }
            });
        });

        // 🌟 Save File 🌟
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

        let fileName = "Pending_Report_";
        if(branchText !== '🏢 គ្រប់សាខាទាំងអស់') fileName += branchText.replace(/ /g, '_') + "_";
        fileName += new Date().toISOString().slice(0, 10) + ".xlsx";

        saveAs(blob, fileName);
        Swal.close();
    }

    function promptUpdateVin(bookingId, currentVin) {
        Swal.fire({
            title: 'បញ្ចូលលេខតួឡាន (VIN)',
            input: 'text',
            inputValue: currentVin !== 'N/A' ? currentVin : '',
            inputPlaceholder: 'សូមវាយបញ្ចូលលេខតួ...',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-1"></i> រក្សាទុក',
            cancelButtonText: 'បោះបង់',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#94a3b8',
            inputValidator: (value) => { if (!value) return 'សូមវាយបញ្ចូលលេខតួឡានជាមុនសិន!'; }
        }).then((result) => {
            if (result.isConfirmed) {
                let newVin = result.value;
                $.ajax({
                    url: '{{ route("services.updateVin") }}', type: 'POST',
                    data: { _token: '{{ csrf_token() }}', id: bookingId, vin: newVin },
                    success: function(res) {
                        if (res.success) {
                            $('#vin-text-' + bookingId).text(newVin);
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'រក្សាទុកលេខតួជោគជ័យ!', showConfirmButton: false, timer: 3000 });
                        } else { Swal.fire('បរាជ័យ', res.message || 'មានបញ្ហាបច្ចេកទេស', 'error'); }
                    },
                    error: function() { Swal.fire('បរាជ័យ', 'មិនអាចភ្ជាប់ទៅកាន់ Server បានទេ។', 'error'); }
                });
            }
        });
    }
</script>
@endsection