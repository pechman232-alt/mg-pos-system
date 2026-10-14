@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Battambang:wght@400;700&display=swap" rel="stylesheet">

<style>
    .history-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; border-color: #e2e8f0; }
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(206, 24, 30, 0.2); }
    
    .table-custom th { 
        background-color: #f8fafc !important; 
        color: #475569; 
        font-weight: 600; 
        font-size: 13px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-custom td {
        font-size: 13px;
        color: #334155;
        vertical-align: middle;
    }
    
    .th-sub {
        display: block;
        font-size: 10px;
        font-weight: 500;
        text-transform: capitalize;
        color: #64748b;
        margin-top: 2px;
    }
    .bg-white .th-sub { color: #94a3b8; } 

    .summary-card { transition: all 0.3s ease; border-radius: 12px; cursor: pointer; border: 1px solid #e2e8f0; overflow: hidden; }
    .summary-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important; border-color: #cbd5e1; }
    .summary-card.active { background-color: #CE181E; color: white; border-color: #CE181E; box-shadow: 0 8px 20px rgba(206, 24, 30, 0.25) !important; }
    .summary-card.active .text-muted, .summary-card.active small { color: #fecaca !important; }
    
    .branch-badge {
        padding: 5px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* ✅ កូដ Sticky Column សម្រាប់ Column សកម្មភាព (Action) នៅចុងគេបង្អស់ ឱ្យដាច់ពីគេច្បាស់ល្អ */
    .sticky-action {
        position: sticky !important;
        right: 0 !important;
        background-color: #ffffff !important;
        box-shadow: -6px 0 12px rgba(0, 0, 0, 0.1) !important;
        z-index: 10 !important;
        min-width: 150px !important;
        text-align: center;
    }
    thead th.sticky-action {
        background-color: #f8fafc !important;
        z-index: 11 !important;
    }
    .table-hover tbody tr:hover td.sticky-action {
        background-color: #f8fafc !important;
    }
</style>

@php
    $latestBranches = $branches->sortByDesc('id')->take(4);
    $colorPalette = [
        ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'border' => '#bfdbfe'], 
        ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#bbf7d0'], 
        ['bg' => '#ffedd5', 'text' => '#c2410c', 'border' => '#fed7aa'], 
        ['bg' => '#f3e8ff', 'text' => '#7e22ce', 'border' => '#e9d5ff'], 
    ];
    $branchColors = [];
    $allColorIndex = 0;
    foreach($branches->sortBy('id') as $b) {
        $branchColors[$b->branch_name] = $colorPalette[$allColorIndex % count($colorPalette)];
        $allColorIndex++;
    }
@endphp

<div class="container-fluid flex-grow-1 container-p-y history-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase text-dark">
            <span class="text-muted fw-light"><i class="bx bx-history me-1"></i> ផ្នែកលក់ /</span> ប្រវត្តិវិក្កយបត្រ (Sales History)
        </h4>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('checkout.index') }}" class="btn btn-brand shadow-sm fw-bold px-4">
                <i class="bx bx-plus-circle me-2"></i> បង្កើតការលក់ថ្មី (POS)
            </a>
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm summary-card {{ empty(request('branch_id')) ? 'active' : 'bg-white' }}">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="icon-box flex-shrink-0 me-3" style="background-color: {{ empty(request('branch_id')) ? 'rgba(255,255,255,0.2)' : '#fef2f2' }}; color: {{ empty(request('branch_id')) ? 'white' : '#CE181E' }}; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 24px;">
                            <i class="bx bx-buildings"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold {{ empty(request('branch_id')) ? 'text-white' : 'text-dark' }}">គ្រប់សាខាទាំងអស់</h6>
                            <small class="text-muted">All Branches</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        @foreach($latestBranches as $b)
            @php 
                $bStyle = $branchColors[$b->branch_name] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1']; 
                $isActive = request('branch_id') == $b->id;
            @endphp
            <div class="col">
                <a href="{{ request()->fullUrlWithQuery(['branch_id' => $b->id]) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm summary-card {{ $isActive ? 'active' : 'bg-white' }}">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="icon-box flex-shrink-0 me-3" style="background-color: {{ $isActive ? 'rgba(255,255,255,0.2)' : $bStyle['bg'] }}; color: {{ $isActive ? 'white' : $bStyle['text'] }}; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 22px;">
                                <i class="bx bx-store-alt"></i>
                            </div>
                            <div class="text-truncate">
                                <h6 class="mb-1 fw-bold text-truncate {{ $isActive ? 'text-white' : 'text-dark' }}" title="{{ $b->branch_name }}">
                                    {{ $b->branch_name }}
                                </h6>
                                <small class="text-muted">Branch Location</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Search Form -->
    <div class="card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-12">
                    <label class="form-label fw-bold text-dark"><i class="bx bx-search-alt text-muted me-1"></i> ស្វែងរកទិន្នន័យ (Search)</label>
                    <div class="input-group input-group-merge border rounded">
                        <span class="input-group-text bg-transparent border-0"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-0" placeholder="ឈ្មោះ/INV/កូដ..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-xl-4 col-md-6">
                    <label class="form-label fw-bold text-dark"><i class="bx bx-calendar text-muted me-1"></i> ចន្លោះកាលបរិច្ឆេទ (Date Range)</label>
                    <div class="input-group input-group-merge border rounded">
                        <input type="date" name="from_date" class="form-control border-0 shadow-none" value="{{ request('from_date') }}">
                        <span class="input-group-text bg-light border-0 border-start border-end" style="font-size: 13px;">ដល់ (To)</span>
                        <input type="date" name="to_date" class="form-control border-0 shadow-none" value="{{ request('to_date') }}">
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label class="form-label fw-bold text-dark"><i class="bx bx-store text-muted me-1"></i> ជ្រើសរើសសាខា (Branch)</label>
                    <select name="branch_id" class="form-select border rounded">
                        <option value="">-- គ្រប់សាខាទាំងអស់ --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1 fw-bold">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="កំណត់ឡើងវិញ (Reset)">
                        <i class="bx bx-refresh fs-5"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive" style="min-height: 400px;">
            <table class="table table-hover table-bordered table-custom align-middle mb-0" style="min-width: 1900px;">
                <thead class="table-light">
                    <tr style="white-space: nowrap;">
                        <th class="py-2 align-middle">លេខឯកសារ (INV)<span class="th-sub">Document No</span></th>
                        <th class="py-2 text-center align-middle">កាលបរិច្ឆេទ<span class="th-sub">Date</span></th>
                        <th class="py-2 align-middle">ទីតាំងសាខា<span class="th-sub">Branch</span></th>
                        <th class="py-2 align-middle">អ្នកលក់<span class="th-sub">Sale Person</span></th>
                        <th class="py-2 align-middle">អ្នកទិញឡាន<span class="th-sub">Buyer</span></th>
                        <th class="py-2 align-middle">វិក្កយបត្រឡាន<span class="th-sub">Car Invoice</span></th>
                        <th class="py-2 align-middle">មូលហេតុ (ស្ទួន)<span class="th-sub">Reason</span></th>
                        <th class="py-2 align-middle">ចំណាំ<span class="th-sub">Remark</span></th>
                        
                        <!-- ផ្នែកទំនិញ -->
                        <th class="py-2 bg-white text-primary align-middle">កូដទំនិញ<span class="th-sub">Item Code</span></th>
                        <th class="py-2 bg-white text-primary align-middle">ឈ្មោះទំនិញ<span class="th-sub">Item Name</span></th>
                        <th class="py-2 bg-white text-primary align-middle">វិក្កយបត្រទិញ<span class="th-sub">PI No</span></th>
                        <th class="py-2 bg-white text-primary text-center align-middle">បរិមាណ<span class="th-sub">Qty</span></th>
                        <th class="py-2 bg-white text-primary text-end align-middle">តម្លៃ ($)<span class="th-sub">Price</span></th>
                        <th class="py-2 bg-white text-primary text-end align-middle">សរុប ($)<span class="th-sub">Amount</span></th>
                        
                        <!-- សរុបរួម និង Action (ដាក់ Action ជាប់ស្តាំបង្អស់) -->
                        <th class="py-2 text-end align-middle" style="min-width: 130px;">សរុបរួម ($)<span class="th-sub">Grand Total</span></th>
                        <th class="py-2 text-center align-middle sticky-action">សកម្មភាព<span class="th-sub">Action</span></th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php 
                            $itemCount = $sale->items->count(); 
                            $rowSpan = $itemCount > 0 ? $itemCount : 1; 
                            $sColor = $branchColors[$sale->loc_id] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'];
                        @endphp

                        @if($itemCount > 0)
                            @foreach($sale->items as $index => $item)
                                <tr>
                                    @if($index == 0)
                                        <td rowspan="{{ $rowSpan }}" class="text-primary fw-bold text-nowrap">{{ $sale->document_no }}</td>
                                        <td rowspan="{{ $rowSpan }}" class="text-center text-muted text-nowrap"><i class="bx bx-time-five me-1"></i>{{ date('d-M-Y H:i', strtotime($sale->created_at)) }}</td>
                                        <td rowspan="{{ $rowSpan }}" class="text-nowrap">
                                            <span class="branch-badge" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }};">
                                                <i class="bx bxs-store"></i> {{ $sale->loc_id ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td rowspan="{{ $rowSpan }}" class="fw-semibold text-dark text-nowrap">{{ $sale->customer_name }}</td>
                                        <td rowspan="{{ $rowSpan }}" class="text-muted text-nowrap">{{ $sale->car_buyer_name ?? '-' }}</td>
                                        
                                        <td rowspan="{{ $rowSpan }}" class="text-nowrap">
                                            @if($sale->reference)
                                                <span class="badge bg-label-dark px-2 py-1"><i class="bx bx-hash me-1"></i>{{ $sale->reference }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td rowspan="{{ $rowSpan }}" class="text-nowrap" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                            @if($sale->reason)
                                                <span class="text-danger" style="font-size: 12px;"><i class="bx bx-error-circle me-1"></i>{{ $sale->reason }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td rowspan="{{ $rowSpan }}" class="text-nowrap" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                            @if($sale->remark)
                                                <span class="text-info" style="font-size: 12px;"><i class="bx bx-comment-detail me-1"></i>{{ $sale->remark }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                    
                                    <td class="fw-bold text-dark text-nowrap">{{ $item->item_code }}</td>
                                    <td class="text-nowrap" style="min-width: 150px;">{{ $item->item_name }}</td>
                                    <td class="text-nowrap">
                                        @if($item->purchase_invoice_no)
                                            <span class="badge bg-label-success px-2 py-1">{{ $item->purchase_invoice_no }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-info">{{ number_format($item->qty) }}</td>
                                    <td class="text-end text-muted">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">${{ number_format($item->total_price, 2) }}</td>
                                    
                                    @if($index == 0)
                                        <td rowspan="{{ $rowSpan }}" class="text-end fw-bold text-success fs-6 text-nowrap">${{ number_format($sale->grand_total, 2) }}</td>
                                        <td rowspan="{{ $rowSpan }}" class="text-center text-nowrap sticky-action">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'invoice']) }}" target="_blank" class="btn btn-sm btn-icon btn-secondary shadow-sm" data-bs-toggle="tooltip" title="បោះពុម្ព Invoice">
                                                    <i class="bx bx-receipt"></i>
                                                </a>
                                                <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'tax_invoice']) }}" target="_blank" class="btn btn-sm btn-icon btn-danger shadow-sm" data-bs-toggle="tooltip" title="បោះពុម្ព Tax Invoice">
                                                    <i class="bx bxs-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'delivery_note']) }}" target="_blank" class="btn btn-sm btn-icon btn-warning shadow-sm" data-bs-toggle="tooltip" title="បោះពុម្ព Delivery Note">
                                                    <i class="bx bxs-truck text-dark"></i>
                                                </a>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-primary fw-bold text-nowrap">{{ $sale->document_no }}</td>
                                <td class="text-center text-muted text-nowrap"><i class="bx bx-time-five me-1"></i>{{ date('d-M-Y H:i', strtotime($sale->created_at)) }}</td>
                                <td class="text-nowrap">
                                    <span class="branch-badge" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }};">
                                        <i class="bx bxs-store"></i> {{ $sale->loc_id ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark text-nowrap">{{ $sale->customer_name }}</td>
                                <td class="text-muted text-nowrap">{{ $sale->car_buyer_name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    @if($sale->reference)
                                        <span class="badge bg-label-dark px-2 py-1"><i class="bx bx-hash me-1"></i>{{ $sale->reference }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($sale->reason)
                                        <span class="text-danger" style="font-size: 12px;"><i class="bx bx-error-circle me-1"></i>{{ $sale->reason }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if($sale->remark)
                                        <span class="text-info" style="font-size: 12px;"><i class="bx bx-comment-detail me-1"></i>{{ $sale->remark }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td colspan="6" class="text-center text-muted">មិនមានបញ្ជីទំនិញទេ</td>
                                <td class="text-end fw-bold text-success fs-6 text-nowrap">${{ number_format($sale->grand_total, 2) }}</td>
                                <td class="text-center text-nowrap sticky-action">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'invoice']) }}" target="_blank" class="btn btn-sm btn-icon btn-secondary shadow-sm" data-bs-toggle="tooltip" title="បោះពុម្ព Invoice">
                                            <i class="bx bx-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        
                    @empty
                    <tr>
                        <td colspan="16" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" alt="No Data" style="width: 80px; opacity: 0.5;" class="mb-3">
                            <h5 class="fw-bold text-muted">មិនទាន់មានទិន្នន័យនៅឡើយទេ! (No Data Found)</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="card-footer border-top bg-white p-3">
            <div class="d-flex justify-content-end m-0">
                {{ $sales->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection