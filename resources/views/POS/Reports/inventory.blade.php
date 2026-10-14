@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --tbl-border: #dbe4eb; 
        --tbl-header-bg: #f4f7f9; 
        --tbl-header-text: #334155; 
        --tbl-text-main: #1e293b;
        --tbl-text-muted: #64748b;
        --saas-primary: #4F46E5;
    }

    .report-container { 
        font-family: 'Inter', 'Battambang', sans-serif !important; 
    }

    .saas-input {
        border: 1px solid var(--tbl-border);
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 12px;
        color: var(--tbl-text-main);
    }
    
    .branch-tabs {
        display: flex; gap: 8px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 15px;
    }
    .branch-tab-item {
        padding: 8px 16px; background-color: white; border: 1px solid var(--tbl-border);
        border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--tbl-text-muted);
        text-decoration: none; white-space: nowrap;
    }
    .branch-tab-item.active { background-color: var(--saas-primary); color: white; border-color: var(--saas-primary); }

    .summary-card { border-left: 4px solid var(--saas-primary); border-radius: 8px; }
    .summary-card.bg-in { border-left-color: #059669; }
    .summary-card.bg-out { border-left-color: #dc2626; }
    .summary-card.bg-balance { border-left-color: #2563eb; }
    .summary-card.bg-value { border-left-color: #8b5cf6; }

    /* Custom Table Header */
    .table-custom-header th {
        background-color: var(--tbl-header-bg) !important;
        color: var(--tbl-header-text) !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        text-transform: uppercase;
        vertical-align: middle;
        white-space: nowrap;
    }

    .val-positive { color: #2563eb; font-weight: 600; }
    .val-negative { color: #dc2626; font-weight: 700; cursor: pointer; text-decoration: underline; text-underline-offset: 3px; }
    .val-negative:hover { color: #a81116; }

    /* ✅ កូដធ្វើឱ្យ Column ចុងក្រោយ (Action ឬ Total Cost) ជាប់ស្អិតនឹងគែមស្តាំពេល Scroll */
    .table-hover tr td:last-child, 
    .table-custom-header th:last-child {
        position: sticky;
        right: 0;
        background-color: #ffffff !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.05);
        z-index: 5;
    }
    .table-hover tr:hover td:last-child {
        background-color: #f8fafc !important;
    }
    .table-custom-header th:last-child {
        background-color: var(--tbl-header-bg) !important;
        z-index: 6;
    }

    /* ============================================================
       ✅ កូដ Ultimate Print Hack
       ============================================================ */
    @media print {
        @page { size: landscape; margin: 10mm; }
        
        body * { visibility: hidden !important; }
        .print-wrapper, .print-wrapper * { visibility: visible !important; }

        .print-wrapper {
            position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important;
            margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; background-color: white !important;
        }

        .no-print, .no-print *, .modal, .modal-backdrop { display: none !important; visibility: hidden !important; height: 0 !important; }

        .table-responsive { overflow: visible !important; border: none !important; margin: 0 !important; }
        .table { width: 100% !important; border-collapse: collapse !important; table-layout: auto; }
        .table th, .table td { border: 1px solid #000 !important; padding: 6px !important; font-size: 10pt !important; color: black !important; word-wrap: break-word; white-space: normal !important;}
        .table th, .val-total, .val-total td { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .val-negative { text-decoration: none !important; } 
        .d-print-flex { display: flex !important; }
    }
</style>

@php
    if(!isset($branches)) { $branches = \App\Models\BranchModel::all(); }
    
    $latestBranches = $branches->sortByDesc('id')->take(4);

    $selectedBranchName = "VIVE MOTORS CO., LTD";
    if(request('branch_id')) {
        $foundBranch = $branches->where('id', request('branch_id'))->first();
        $selectedBranchName = $foundBranch ? $foundBranch->branch_name : "VIVE MOTORS CO., LTD";
    }

    $sumBeginning = 0; $sumReceived = 0; $sumSold = 0;
    foreach($reportData as $r) {
        $sumBeginning += $r->stock_beginning;
        $sumReceived += $r->stock_received;
        $sumSold += abs($r->sold_out);
    }
@endphp

<div class="container-fluid flex-grow-1 container-p-y report-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 no-print">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">របាយការណ៍ /</span> Inventory Balance Report
        </h4>
        <div class="mt-2 mt-md-0">
            <span class="badge p-2 fs-6 shadow-sm" style="background-color: var(--saas-primary); color: white;">
                <i class="bx bx-calendar me-1"></i> 
                {{ date('d M, Y', strtotime($fromDate)) }} - {{ date('d M, Y', strtotime($toDate)) }}
            </span>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-5 g-3 mb-4 no-print">
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 quick-filter-card {{ empty(request('branch_id')) ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                    <div class="card-body p-3 text-center">
                        <i class="bx bx-buildings fs-3 mb-1"></i>
                        <div class="fw-bold" style="font-size: 11px;">គ្រប់សាខាទាំងអស់</div>
                    </div>
                </div>
            </a>
        </div>
        @foreach($latestBranches as $b)
            @php $isActive = request('branch_id') == $b->id; @endphp
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['branch_id' => $b->id]) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 quick-filter-card {{ $isActive ? 'bg-dark text-white' : 'bg-white text-dark' }}">
                        <div class="card-body p-3 text-center">
                            <i class="bx bx-store-alt fs-3 mb-1"></i>
                            <div class="fw-bold text-truncate" style="font-size: 11px;">{{ $b->branch_name }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card mb-4 border-0 shadow-sm no-print filter-section">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="form-label fw-bold text-muted"><i class='bx bx-search-alt-2'></i> ស្វែងរកទំនិញ</label>
                    <div class="input-group input-group-merge border rounded shadow-none">
                        <span class="input-group-text border-0 bg-transparent"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-0" placeholder="កូដ ឬ ឈ្មោះទំនិញ..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-md-6">
                    <label class="form-label fw-bold text-muted"><i class='bx bx-map-pin'></i> សាខា</label>
                    <select name="branch_id" class="form-select border rounded shadow-none">
                        <option value="">-- ទាំងអស់ --</option>
                        @foreach($latestBranches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-4 col-md-6">
                    <label class="form-label fw-bold text-muted"><i class="bx bx-calendar me-1"></i> ចាប់ពីថ្ងៃ - ដល់ថ្ងៃ</label>
                    <div class="input-group input-group-merge border rounded shadow-none">
                        <span class="input-group-text bg-transparent border-0 text-muted">From</span>
                        <input type="date" name="from_date" class="form-control border-0 border-end" value="{{ $fromDate }}">
                        <span class="input-group-text bg-transparent border-0 text-muted">To</span>
                        <input type="date" name="to_date" class="form-control border-0" value="{{ $toDate }}">
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 d-flex justify-content-end align-items-end gap-2">
                    <button type="submit" class="btn btn-dark shadow-sm fw-bold flex-grow-1"><i class='bx bx-filter-alt me-1'></i> ស្វែងរក</button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="tooltip" title="Reset"><i class='bx bx-refresh'></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4 no-print">
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card shadow-sm h-100">
                <div class="card-body p-3">
                    <span class="text-muted d-block mb-1" style="font-size: 12px; font-weight: 700;">ស្តុកដើមគ្រា (BEGINNING)</span>
                    <h3 class="mb-0 text-dark">{{ number_format($sumBeginning) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card bg-in shadow-sm h-100">
                <div class="card-body p-3">
                    <span class="text-muted d-block mb-1" style="font-size: 12px; font-weight: 700;">ស្តុកចូល (IN)</span>
                    <h3 class="mb-0" style="color: #059669;">+{{ number_format($sumReceived) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card summary-card bg-out shadow-sm h-100">
                <div class="card-body p-3">
                    <span class="text-muted d-block mb-1" style="font-size: 12px; font-weight: 700;">ស្តុកចេញ (OUT)</span>
                    <h3 class="mb-0" style="color: #dc2626;">-{{ number_format($sumSold) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card summary-card bg-balance shadow-sm h-100">
                <div class="card-body p-3">
                    <span class="text-muted d-block mb-1" style="font-size: 12px; font-weight: 700;">ស្តុកចុងគ្រា (ENDING BALANCE)</span>
                    <h3 class="mb-0" style="color: #2563eb;">{{ number_format($totalEndingQty) }} <small class="text-muted fs-6">Unit</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card summary-card bg-value shadow-sm h-100">
                <div class="card-body p-3">
                    <span class="text-muted d-block mb-1" style="font-size: 12px; font-weight: 700;">តម្លៃស្តុកសរុប (TOTAL VALUE)</span>
                    <h3 class="mb-0" style="color: #8b5cf6;">${{ number_format($grandTotalCost, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm print-wrapper">
        
        <div class="card-header bg-white border-bottom p-4 no-print">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bx bx-list-ul me-2"></i> ទិន្នន័យសមតុល្យស្តុកលម្អិត
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.inventory-export', request()->query()) }}" class="btn btn-success shadow-sm fw-bold">
                        <i class='bx bx-spreadsheet me-1'></i> Export Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-dark shadow-sm fw-bold">
                        <i class='bx bx-printer me-1'></i> Print Report
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center d-none d-print-block pt-4 mb-3">
            <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: var(--saas-primary); margin: 0; text-transform: uppercase;">
                {{ $selectedBranchName }}
            </h2>
            <h4 style="margin: 5px 0; text-decoration: underline; color: var(--secondary-color);">INVENTORY BALANCE REPORT</h4>
            <p style="margin: 0; font-weight: bold; font-size: 14px; color: #475569;">
                Period: {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}
            </p>
            <div class="text-end px-4" style="font-size: 12px; margin-top: -30px;">
                <div>Print Date: {{ date('d-M-Y H:i') }}</div>
                <div>System: MG POS Official</div>
            </div>
        </div>

        <div class="table-responsive m-3 m-print-0 border-print-0">
            <table class="table table-hover table-bordered mb-0 align-middle" style="min-width: 1200px;">
                <thead class="table-light table-custom-header">
                    <tr class="text-center">
                        <th width="50">No</th>
                        <th>Item Code</th>
                        <th class="text-start">Description</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>U/M</th>
                        <th>Beg. Stock</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                        <th>End. Stock</th>
                        <th class="text-end">Total Cost ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $index => $row)
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold text-dark">{{ $row->item_code }}</td>
                            <td class="text-start text-wrap" style="min-width: 250px;">{{ $row->description }}</td>
                            <td class="text-center text-muted">{{ $row->category }}</td>
                            
                            <td class="text-center fw-bold text-secondary">{{ $row->loc_id }}</td>
                            
                            <td class="text-center">{{ $row->um }}</td>
                            <td class="text-center val-positive">{{ number_format($row->stock_beginning) }}</td>
                            <td class="text-center fw-bold" style="color: #059669;">{{ number_format($row->stock_received) }}</td>
                            
                            <td class="text-center">
                                @if($row->sold_out != 0)
                                    <span class="val-negative out-detail-btn" 
                                          data-itemcode="{{ $row->item_code }}" 
                                          data-itemname="{{ $row->description }}"
                                          data-details="{{ json_encode($row->out_details) }}">
                                        {{ number_format($row->sold_out) }}
                                    </span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            
                            <td class="text-center fw-bold" style="background-color: #fdf2f2; color: #dc2626;">
                                {{ number_format($row->stock_ending) }}
                            </td>
                            <td class="text-end fw-bold text-dark">${{ number_format($row->total_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class='bx bx-package fs-1 d-block mb-2'></i>
                                មិនមានទិន្នន័យស្តុកក្នុងចន្លោះពេលនេះទេ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($reportData) > 0)
                    <tfoot class="table-light">
                        <tr class="val-total fw-bold">
                            <td colspan="6" class="text-end py-3 text-uppercase">Grand Total Balance:</td>
                            <td class="text-center text-primary fs-6">{{ number_format($sumBeginning) }}</td>
                            <td class="text-center text-success fs-6">{{ number_format($sumReceived) }}</td>
                            <td class="text-center text-danger fs-6">{{ number_format($sumSold) }}</td>
                            <td class="text-center fs-5 text-danger">{{ number_format($totalEndingQty) }}</td>
                            <td class="text-end text-success fs-6">${{ number_format($grandTotalCost, 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="d-none d-print-flex justify-content-between mt-5 pt-4 px-4 pb-4 text-center">
            <div style="width: 30%;">
                <p class="fw-bold mb-5">Prepared By</p>
                <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div><small>Date: ___/___/202__</small>
            </div>
            <div style="width: 30%;">
                <p class="fw-bold mb-5">Verified By</p>
                <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div><small>Date: ___/___/202__</small>
            </div>
            <div style="width: 30%;">
                <p class="fw-bold mb-5">Approved By</p>
                <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div><small>Date: ___/___/202__</small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stockOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-3 bg-dark">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bx bx-export me-2"></i> ព័ត៌មានទំនិញចេញ <span id="modalItemCode" class="badge bg-white text-dark ms-2"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <span class="fw-bold text-muted">ទំនិញ៖ </span> <span id="modalItemName" class="text-dark fw-bold"></span>
                </div>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="table-light sticky-top" style="z-index: 1;">
                            <tr class="text-center" style="font-size: 12px;">
                                <th>Date</th>
                                <th>Invoice Nº</th>
                                <th>Customer Name</th>
                                <th>Car Buyer</th>
                                <th>Qty (Out)</th>
                                <th class="text-start">Reason / Remark</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light p-2 justify-content-end">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">បិទផ្ទាំង (Close)</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

        document.querySelectorAll('.out-detail-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let itemCode = this.getAttribute('data-itemcode');
                let itemName = this.getAttribute('data-itemname');
                let rawData = this.getAttribute('data-details');
                
                let details = [];
                if(rawData) { details = JSON.parse(rawData); }

                document.getElementById('modalItemCode').innerText = itemCode;
                document.getElementById('modalItemName').innerText = itemName;
                
                let tbody = document.getElementById('modalTableBody');
                tbody.innerHTML = '';

                if(details && details.length > 0) {
                    details.forEach(d => {
                        let dObj = new Date(d.sale_date);
                        let fDate = dObj.toLocaleDateString('en-GB') + ' ' + dObj.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'});
                        
                        let reasonHtml = d.reason ? `<div class="text-danger mb-1" style="font-size: 11px;"><i class="bx bx-error-circle"></i> <b>Reason:</b> ${d.reason}</div>` : '';
                        let remarkHtml = d.remark ? `<div class="text-info" style="font-size: 11px;"><i class="bx bx-comment-detail"></i> <b>Remark:</b> ${d.remark}</div>` : '';
                        let combinedNotes = (reasonHtml || remarkHtml) ? (reasonHtml + remarkHtml) : '<span class="text-muted">-</span>';

                        let tr = `
                            <tr style="font-size: 12px;">
                                <td class="text-center text-muted">${fDate}</td>
                                <td class="text-center fw-bold" style="color: #CE181E;">${d.invoice_no}</td>
                                <td class="text-center fw-semibold">${d.customer_name || '-'}</td>
                                <td class="text-center">${d.car_buyer_name || '-'}</td>
                                <td class="text-center fw-bold text-danger">-${d.qty}</td>
                                <td class="text-start">${combinedNotes}</td>
                            </tr>
                        `;
                        tbody.innerHTML += tr;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">គ្មានទិន្នន័យលម្អិតទេ!</td></tr>`;
                }

                var myModal = new bootstrap.Modal(document.getElementById('stockOutModal'));
                myModal.show();
            });
        });
    });
</script>
@endsection