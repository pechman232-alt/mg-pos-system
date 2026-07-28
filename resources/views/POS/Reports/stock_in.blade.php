@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root { 
        --brand-primary: #CE181E; 
        --text-main: #0f172a; 
        --bg-body: #f8fafc; 
        --border-color: #cbd5e1;
    }

    .report-container { 
        font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; 
        background-color: var(--bg-body); 
    }

    .quick-filter-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        cursor: pointer;
        border: 1px solid var(--border-color);
    }
    .quick-filter-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .report-card { 
        background-color: #ffffff; 
        border: 1px solid var(--border-color); 
        border-radius: 12px; 
        padding: 2.5rem; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
    }

    .report-header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 3px double var(--text-main);
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .company-title { font-size: 1.8rem; font-weight: 800; color: var(--brand-primary); text-transform: uppercase; margin: 0; }
    .report-title { font-size: 1.3rem; font-weight: 700; color: var(--text-main); margin-top: 5px; }

    .branch-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
    }

    .report-table th { 
        background-color: #f1f5f9; 
        color: var(--text-main); 
        font-weight: 700; 
        text-transform: uppercase; 
        border: 1px solid var(--border-color);
        font-size: 11px;
        text-align: center;
    }
    
    .report-table td { border: 1px solid var(--border-color); padding: 8px; font-size: 12px; }

    /* សម្រាប់ធ្វើឱ្យ Input ស្អាតเวลាចុច */
    .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15); }
    .input-group-text { background-color: transparent; }

    /* ============================================================
       ✅ កូដ Print ថ្មី: Ultimate Print Hack (ដាច់ Navbar ១០០%)
       ============================================================ */
    @media print {
        @page { 
            size: landscape; 
            margin: 10mm; 
        }

        body * { visibility: hidden !important; }
        .report-card, .report-card * { visibility: visible !important; }

        .report-card {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background-color: white !important;
        }

        .report-table { 
            width: 100% !important; 
            border-collapse: collapse !important;
        }

        .report-table th, .report-table td { 
            border: 1px solid #000 !important; 
            padding: 6px !important; 
            font-size: 9pt !important; 
            color: black !important;
        }

        .report-table th { 
            background-color: #f2f2f2 !important; 
            -webkit-print-color-adjust: exact; 
        }
    }
</style>

@php
    if(!isset($branches)) { $branches = \App\Models\BranchModel::all(); }
    $latestBranches = $branches->sortByDesc('id')->take(4);

    // រកឈ្មោះសាខាឌីណាមិក
    $selectedBranchName = "VIVE MOTORS CO., LTD";
    if(request('branch_id')) {
        $foundBranch = $branches->where('id', request('branch_id'))->first();
        $selectedBranchName = $foundBranch ? $foundBranch->branch_name : "VIVE MOTORS CO., LTD";
    }

    $colorPalette = [
        ['bg' => '#dbeafe', 'text' => '#2563eb', 'border' => '#bfdbfe'],
        ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#bbf7d0'],
        ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fed7aa'],
        ['bg' => '#f3e8ff', 'text' => '#9333ea', 'border' => '#e9d5ff'],
    ];
    
    $branchColors = []; $i = 0;
    foreach($branches as $b) {
        $branchColors[$b->branch_name] = $colorPalette[$i % count($colorPalette)];
        $i++;
    }
@endphp

<div class="container-fluid flex-grow-1 container-p-y report-container">
    
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="fw-bold mb-0 text-uppercase"><span class="text-muted fw-light">របាយការណ៍ /</span> Stock In Official Report</h4>
        <button class="btn btn-dark shadow-sm fw-bold" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Print Report
        </button>
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
                
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted"><i class='bx bx-search-alt-2'></i> ស្វែងរក (Search)</label>
                    <div class="input-group input-group-merge border rounded shadow-none">
                        <span class="input-group-text border-0"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-0" placeholder="លេខឯកសារ ឬ កូដទំនិញ..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate) }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate) }}">
                </div>
                
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-filter-alt me-1"></i> ស្វែងរក</button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Clear Filters"><i class="bx bx-refresh"></i> Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="report-card">
        <div class="report-header-wrapper">
            <div>
                <h1 class="company-title">{{ $selectedBranchName }}</h1>
                <h2 class="report-title text-muted">Stock In Official List (GRN)</h2>
                <div class="mt-2 fw-bold text-dark" style="font-size: 14px;">
                    Period: {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}
                </div>
            </div>
            <div class="text-end" style="font-size: 12px;">
                <div class="fw-bold text-uppercase text-danger">Goods Receive Note</div>
                <div>Print Date: {{ date('d-M-Y H:i') }}</div>
                <div>System: MG POS Official</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table report-table">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Document Nº</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Item Code</th>
                        <th class="text-start">Description</th>
                        <th>Qty</th>
                        <th>UM</th>
                        <th class="text-end">Cost ($)</th>
                        <th class="text-end">Total ($)</th>
                        <th>វិក្កយបត្រយោង</th>
                        <th class="text-start">Vendor</th>
                    </tr>
                </thead>
                <tbody>
                    @php $gTotal = 0; $gQty = 0; @endphp
                    @forelse($receiveDetails as $index => $row)
                        @php 
                            $gTotal += $row->total_cost; 
                            $gQty += $row->qty;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold">{{ $row->header->document_no ?? 'N/A' }}</td>
                            <td class="text-center text-nowrap">{{ $row->header ? date('d-M-Y', strtotime($row->header->date)) : 'N/A' }}</td>
                            <td class="text-center">
                                @php 
                                    $bName = $row->header->branch->branch_name ?? 'N/A';
                                    $sColor = $branchColors[$bName] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'];
                                @endphp
                                <span class="branch-badge" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }};">
                                    {{ $bName }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ $row->product->item_code ?? 'N/A' }}</td>
                            <td class="text-start">{{ $row->product->item_name ?? 'N/A' }}</td>
                            <td class="text-center fw-bold">{{ number_format($row->qty, 2) }}</td>
                            <td class="text-center text-muted">{{ $row->product->unit ?? 'UNIT' }}</td>
                            <td class="text-end">${{ number_format($row->cost, 2) }}</td>
                            <td class="text-end fw-bold text-danger">${{ number_format($row->total_cost, 2) }}</td>
                            <td class="text-center fw-bold text-primary small">
                                {{ $row->header->purchase_invoice_no ?? '-' }}
                            </td>
                            <td class="text-start small">{{ $row->header->vendor->vendor_name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center py-5 text-muted">No stock in records found.</td></tr>
                    @endforelse
                </tbody>
                @if(count($receiveDetails) > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end py-3">GRAND TOTAL:</td>
                            <td class="text-center text-primary py-3">{{ number_format($gQty, 2) }}</td>
                            <td colspan="2"></td>
                            <td class="text-end text-danger py-3" style="font-size: 14px;">${{ number_format($gTotal, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection