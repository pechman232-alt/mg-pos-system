@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .history-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; }
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    
    /* Accordion Custom Style */
    .table-hover tbody tr.accordion-toggle:hover { background-color: #f1f5f9; cursor: pointer; }
    .table-sub { background-color: #f8fafc; border-radius: 0 0 8px 8px; }
    .table-sub th { background-color: #e2e8f0 !important; font-size: 0.8rem; text-transform: uppercase; color: #475569; }
    .table-sub td { font-size: 0.85rem; color: #334155; vertical-align: middle; }
    
    .btn-expand { display: inline-block; color: #64748b; transition: transform 0.3s ease; }
    .accordion-toggle.is-open .btn-expand { transform: rotate(180deg); color: #CE181E; }

    /* Summary Card */
    .summary-card { transition: all 0.3s ease; border-radius: 10px; cursor: pointer; border: 1px solid #e2e8f0; }
    .summary-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .summary-card.active { background-color: #CE181E; color: white; border-color: #CE181E; box-shadow: 0 5px 15px rgba(206, 24, 30, 0.3) !important; }
    .summary-card.active .text-muted, .summary-card.active small { color: #ffcccc !important; }
    
    .branch-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
</style>

@php
    // ១. ចាប់យកតែសាខា ៤ ដែលទើបបង្កើតថ្មីបំផុតសម្រាប់បង្ហាញខាងលើ
    $latestBranches = $branches->sortByDesc('id')->take(4);

    // ២. កំណត់កញ្ចប់ពណ៌សម្រាប់សាខា (Soft UI Colors)
    $colorPalette = [
        ['bg' => '#dbeafe', 'text' => '#2563eb', 'border' => '#bfdbfe'], // Blue
        ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#bbf7d0'], // Green
        ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fed7aa'], // Orange
        ['bg' => '#f3e8ff', 'text' => '#9333ea', 'border' => '#e9d5ff'], // Purple
    ];
    
    // ៣. រៀបចំ Color Mapping សម្រាប់ប្រើទូទាំងទំព័រ
    $branchColors = [];
    $allColorIndex = 0;
    // កំណត់ពណ៌ឱ្យគ្រប់សាខាទាំងអស់ដែលមានក្នុង DB ដើម្បីប្រើក្នុង Table Badge
    foreach($branches->sortBy('id') as $b) {
        $branchColors[$b->branch_name] = $colorPalette[$allColorIndex % count($colorPalette)];
        $allColorIndex++;
    }
@endphp

<div class="container-fluid flex-grow-1 container-p-y history-container ">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ផ្នែកលក់ /</span> ប្រវត្តិវិក្កយបត្រ (Sales History)
        </h4>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('checkout.index') }}" class="btn btn-brand shadow-sm fw-bold">
                <i class="bx bx-plus me-1"></i> បង្កើតការលក់ថ្មី (POS)
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
        <div class="col">
            <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm summary-card {{ empty(request('branch_id')) ? 'active' : 'bg-white' }}">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box flex-shrink-0 me-3" style="background-color: {{ empty(request('branch_id')) ? 'rgba(255,255,255,0.2)' : '#fee2e2' }}; color: {{ empty(request('branch_id')) ? 'white' : '#CE181E' }}; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 20px;">
                            <i class="bx bx-buildings"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold {{ empty(request('branch_id')) ? 'text-white' : 'text-dark' }}">គ្រប់សាខាទាំងអស់</h6>
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
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box flex-shrink-0 me-3" style="background-color: {{ $isActive ? 'rgba(255,255,255,0.2)' : $bStyle['bg'] }}; color: {{ $isActive ? 'white' : $bStyle['text'] }}; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 20px;">
                                <i class="bx bx-store-alt"></i>
                            </div>
                            <div class="text-truncate">
                                <h6 class="mb-0 fw-bold text-truncate {{ $isActive ? 'text-white' : 'text-dark' }}" title="{{ $b->branch_name }}">
                                    {{ $b->branch_name }}
                                </h6>
                                <small class="text-muted">New Branch</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-4 col-md-12">
                    <label class="form-label fw-bold text-muted">ស្វែងរកទិន្នន័យ</label>
                    <div class="input-group input-group-merge border rounded shadow-none">
                        <span class="input-group-text border-0 bg-transparent"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-0" placeholder="ឈ្មោះ / លេខ INV / កូដទំនិញ / លេខឡាន..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label fw-bold text-muted"><i class="bx bx-calendar me-1"></i> កាលបរិច្ឆេទ</label>
                    <input type="date" name="date" class="form-control border rounded shadow-none" value="{{ request('date') }}">
                </div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label fw-bold text-muted"><i class="bx bx-store me-1"></i> សាខា (Branch)</label>
                    <select name="branch_id" class="form-select border rounded shadow-none">
                        <option value="">-- គ្រប់សាខាទាំងអស់ --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1 shadow-sm fw-bold">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="tooltip" title="កំណត់ឡើងវិញ">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap" style="min-height: 400px;">
            <table class="table table-hover align-middle">
                <thead class="table-light border-bottom">
                    <tr class="text-primary text-nowrap" style="font-size: 13px;">
                        <th width="50" class="text-center"></th>
                        <th class="py-3 text-start">លេខឯកសារ (INV)</th>
                        <th class="py-3">កាលបរិច្ឆេទ</th>
                        <th class="py-3 text-start">ទីតាំងសាខា</th>
                        <th class="py-3 text-start">អ្នកលក់ (Sales Person)</th>
                        <th class="py-3 text-start">អ្នកទិញឡាន</th>
                        <th class="py-3 text-start">លេខវិក្កយបត្រឡាន</th>
                        <th class="py-3 text-end">សរុបរួម ($)</th>
                        <th class="py-3 text-center">អ្នកលក់</th>
                        <th class="py-3 text-center" style="min-width: 130px;">សកម្មភាព</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="accordion-toggle border-bottom" data-target="#collapse-{{ $sale->id }}" style="font-size: 13px;">
                            <td class="text-center">
                                <span class="btn-expand"><i class="bx bx-chevron-down fs-4"></i></span>
                            </td>
                            <td class="text-start text-primary fw-bold">{{ $sale->document_no }}</td>
                            <td class="text-center text-muted">{{ date('d-M-Y H:i', strtotime($sale->created_at)) }}</td>
                            
                            <td class="text-start">
                                @php 
                                    $sColor = $branchColors[$sale->loc_id] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'];
                                @endphp
                                <span class="branch-badge" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }};">
                                    <i class="bx bx-store-alt"></i> {{ $sale->loc_id ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="text-start">{{ $sale->customer_name }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $sale->car_buyer_name ?? '-' }}</td> 
                            <td class="text-start">
                                @if($sale->reference)
                                    <span class="badge bg-label-dark">{{ $sale->reference }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success fs-6">${{ number_format($sale->grand_total, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-info"><i class="bx bx-user me-1"></i>{{ $sale->by_user ?? 'Admin' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1" onclick="event.stopPropagation();">
                                    <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'invoice']) }}" target="_blank" class="btn btn-sm btn-icon btn-secondary shadow-sm" data-bs-toggle="tooltip" title="Invoice">
                                        <i class="bx bx-receipt"></i>
                                    </a>
                                    <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'tax_invoice']) }}" target="_blank" class="btn btn-sm btn-icon btn-danger shadow-sm" data-bs-toggle="tooltip" title="Tax Invoice">
                                        <i class="bx bx-file"></i>
                                    </a>
                                    <a href="{{ route('print.sales', ['encryptedId' => $sale->encrypted_id, 'type' => 'delivery_note']) }}" target="_blank" class="btn btn-sm btn-icon btn-warning shadow-sm" data-bs-toggle="tooltip" title="Delivery Note">
                                        <i class="bx bx-car text-dark"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="10" class="p-0 border-0">
                                <div class="collapse" id="collapse-{{ $sale->id }}">
                                    <div class="p-4 table-sub shadow-inner">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bx bx-list-ol text-danger fs-4 me-2"></i>
                                            <h6 class="fw-bold m-0 text-dark">បញ្ជីមុខទំនិញលម្អិត (Items)</h6>
                                            @if($sale->reason)
                                                <span class="ms-4 text-danger fw-bold" style="font-size: 0.85rem;">
                                                    <i class="bx bx-comment-error me-1"></i>ចំណាំ: {{ $sale->reason }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <table class="table table-bordered table-sm bg-white mb-0 shadow-sm rounded overflow-hidden">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">#</th>
                                                    <th width="15%">កូដទំនិញ</th>
                                                    <th width="30%">ឈ្មោះទំនិញ</th>
                                                    <th width="15%">វិក្កយបត្រទិញ (PI No)</th>
                                                    <th width="10%" class="text-center">បរិមាណ</th>
                                                    <th width="10%" class="text-end">តម្លៃ ($)</th>
                                                    <th width="15%" class="text-end">សរុប ($)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale->items as $index => $item)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                        <td class="fw-bold text-dark">{{ $item->item_code }}</td>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>
                                                            @if($item->purchase_invoice_no)
                                                                <span class="badge bg-label-success">{{ $item->purchase_invoice_no }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-bold text-info fs-6">{{ number_format($item->qty) }}</td>
                                                        <td class="text-end text-muted">${{ number_format($item->unit_price, 2) }}</td>
                                                        <td class="text-end fw-bold text-danger">${{ number_format($item->total_price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <h5 class="fw-bold text-muted">មិនទាន់មានទិន្នន័យនៅឡើយទេ!</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="card-footer border-top bg-white pb-0 pt-3">
            <div class="d-flex justify-content-end">
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
    // បើក Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // បញ្ជា Accordion (បើក/បិទ)
    $('.accordion-toggle').on('click', function(e) {
        if ($(e.target).closest('td:last-child').length > 0) return;

        let targetId = $(this).data('target');
        let targetRow = $(targetId);
        
        if (targetRow.hasClass('show')) {
            targetRow.collapse('hide');
            $(this).removeClass('is-open');
        } else {
            targetRow.collapse('show');
            $(this).addClass('is-open');
        }
    });
});
</script>
@endsection