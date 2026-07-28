@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .history-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    
    /* Input & Button Styles */
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; }
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    
    /* Summary Card Styles */
    .summary-card { transition: all 0.3s ease; border-radius: 10px; cursor: pointer; border: 1px solid #e2e8f0; }
    .summary-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .summary-card.active { background-color: #CE181E; color: white; border-color: #CE181E; box-shadow: 0 5px 15px rgba(206, 24, 30, 0.3) !important; }
    .summary-card.active .text-muted, .summary-card.active small { color: #ffcccc !important; }

    /* Accordion Custom Styles */
    .table-hover tbody tr.accordion-toggle:hover { background-color: #f8fafc; cursor: pointer; }
    .table-sub { background-color: #f8fafc; border-radius: 0 0 8px 8px; }
    .table-sub th { background-color: #e2e8f0 !important; font-size: 0.8rem; text-transform: uppercase; color: #475569; }
    .table-sub td { font-size: 0.85rem; color: #334155; vertical-align: middle; }
    
    .btn-expand { display: inline-block; color: #64748b; transition: transform 0.3s ease; }
    .accordion-toggle.is-open .btn-expand { transform: rotate(180deg); color: #CE181E; }
    
    /* Badge styling */
    .branch-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-soft-danger { background-color: #fee2e2; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
</style>

@php
    // ១. ចាប់យកតែសាខា ៤ ដែលទើបបង្កើតថ្មីបំផុត
    $latestBranches = $branches->sortByDesc('id')->take(4);

    // ២. កំណត់ពណ៌សម្រាប់សាខានីមួយៗ
    $colorPalette = [
        ['bg' => '#dbeafe', 'text' => '#2563eb', 'border' => '#bfdbfe'], // ខៀវ
        ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#bbf7d0'], // បៃតង
        ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fed7aa'], // ទឹកក្រូច
        ['bg' => '#f3e8ff', 'text' => '#9333ea', 'border' => '#e9d5ff'], // ស្វាយ
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
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិញចូល /</span> ប្រវត្តិវិក្កយបត្រ (Stock Receive History)
        </h4>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('receive-headers.create') }}" class="btn btn-brand shadow-sm fw-bold">
                <i class="bx bx-plus me-1"></i> បង្កើតវិក្កយបត្រថ្មី
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
            <form action="{{ route('receive-headers.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-4">
                    <label class="form-label fw-bold text-muted">សាខា (Branch)</label>
                    <select name="branch_id" class="form-select shadow-none">
                        <option value="">-- គ្រប់សាខាទាំងអស់ --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-3 col-md-4">
                    <label class="form-label fw-bold text-muted">កូដទំនិញ (Item Code)</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text border-end-0"><i class="bx bx-barcode"></i></span>
                        <input type="text" name="item_code" value="{{ request('item_code') }}" class="form-control border-start-0 ps-0" placeholder="ស្វែងរកតាមកូដ...">
                    </div>
                </div>
                <div class="col-xl-4 col-md-4">
                    <label class="form-label fw-bold text-muted">ស្វែងរកទូទៅ</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text border-end-0"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="លេខឯកសារ ឬ លេខវិក្កយបត្រយោង...">
                    </div>
                </div>
                <div class="col-xl-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1 shadow-sm fw-bold">
                        <i class="bx bx-search-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ route('receive-headers.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap" style="min-height: 400px;">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light border-bottom">
                    <tr class="text-primary text-nowrap" style="font-size: 13px;">
                        <th width="50" class="text-center"></th>
                        <th class="py-3">លេខឯកសារ (Doc Nº)</th>
                        <th class="py-3">កាលបរិច្ឆេទ (Date)</th>
                        <th class="py-3">អ្នកផ្គត់ផ្គង់ (Vendor)</th>
                        <th class="py-3">ទីតាំង (Location)</th>
                        <th class="py-3 text-end">សរុបរួម (Grand Total)</th>
                        <th width="200" class="py-3 text-center">សកម្មភាព (Actions)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($headers as $item)
                        <tr class="accordion-toggle border-bottom" data-target="#collapse-{{ $item->id }}">
                            <td class="text-center">
                                <span class="btn-expand"><i class="bx bx-chevron-down fs-4"></i></span>
                            </td>
                            <td>
                                <span class="badge badge-soft-danger px-2 py-1 fw-bold fs-6">{{ $item->document_no }}</span>
                            </td>
                            <td class="text-muted fw-semibold">{{ date('d M, Y', strtotime($item->date)) }}</td>
                            <td class="fw-bold text-dark">{{ $item->vendor->vendor_name ?? 'N/A' }}</td>
                            <td>
                                @php $sColor = $branchColors[$item->branch->branch_name] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1']; @endphp
                                <span class="branch-badge" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border: 1px solid {{ $sColor['border'] }};">
                                    <i class="bx bx-store-alt"></i> {{ $item->branch->branch_name ?? 'N/A' }}
                                </span>
                            </td>
                            
                            @php $grandTotal = $item->details->sum('total_cost'); @endphp
                            <td class="text-end fw-bold text-success fs-6">${{ number_format($grandTotal, 2) }}</td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('receive-items.index', ['header_id' => $item->id]) }}" class="btn btn-sm btn-icon btn-info shadow-sm" data-bs-toggle="tooltip" title="បញ្ចូលទំនិញ">
                                        <i class="bx bx-box"></i>
                                    </a>
                                    <a href="{{ route('receive-headers.edit', encrypt($item->id)) }}" class="btn btn-sm btn-icon btn-warning shadow-sm" data-bs-toggle="tooltip" title="កែប្រែ">
                                        <i class="bx bx-edit"></i>
                                     </a>
                                    <a href="{{ route('receive-headers.print', $item->id) }}" target="_blank" class="btn btn-sm btn-icon btn-secondary shadow-sm" data-bs-toggle="tooltip" title="បោះពុម្ព">
                                        <i class="bx bx-printer"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-delete shadow-sm" data-id="{{ $item->id }}" data-doc="{{ $item->document_no }}" data-bs-toggle="tooltip" title="លុប">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('receive-headers.destroy', $item->id) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="collapse" id="collapse-{{ $item->id }}">
                                    <div class="p-4 table-sub shadow-inner">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bx bx-list-ol text-danger fs-4 me-2"></i>
                                            <h6 class="fw-bold m-0 text-dark">បញ្ជីមុខទំនិញលម្អិត</h6>
                                            @if($item->purchase_invoice_no)
                                                <span class="badge bg-label-success ms-4 px-2 py-1"><i class="bx bx-link me-1"></i>វិក្កយបត្រយោង: {{ $item->purchase_invoice_no }}</span>
                                            @endif
                                        </div>
                                        
                                        <table class="table table-bordered table-sm bg-white mb-0 shadow-sm rounded overflow-hidden">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">#</th>
                                                    <th width="15%">កូដទំនិញ</th>
                                                    <th width="30%">ឈ្មោះទំនិញ</th>
                                                    <th width="15%" class="text-center">ប្រភេទ</th>
                                                    <th width="10%" class="text-center">បរិមាណ</th>
                                                    <th width="10%" class="text-end">តម្លៃដើម/១</th>
                                                    <th width="15%" class="text-end">សរុប</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item->details as $index => $detail)
                                                    <tr>
                                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                                        <td class="fw-bold text-dark">{{ $detail->product->item_code ?? 'N/A' }}</td>
                                                        <td>{{ $detail->product->item_name ?? 'N/A' }}</td>
                                                        <td class="text-center"><span class="badge bg-label-dark">{{ $detail->product->category ?? 'N/A' }}</span></td>
                                                        <td class="text-center fw-bold text-info fs-6">{{ number_format($detail->qty, 2) }}</td>
                                                        <td class="text-end text-muted">${{ number_format($detail->cost ?? $detail->unit_cost, 2) }}</td>
                                                        <td class="text-end fw-bold text-danger">${{ number_format($detail->total_cost, 2) }}</td>
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
                            <td colspan="7" class="text-center py-5">
                                <h5 class="fw-bold text-muted">មិនមានប្រវត្តិវិក្កយបត្រទិញចូលទេ!</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($headers, 'hasPages') && $headers->hasPages())
        <div class="card-footer border-top bg-white pb-0 pt-3 d-flex justify-content-end">
            {{ $headers->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // 1. បើក Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // 2. ✅ កូដបញ្ជា Accordion (បិទ/បើក) រលូន ១០០%
    $('.accordion-toggle').on('click', function(e) {
        // ការពារកុំឱ្យ Click លោត ពេលចុចលើប៊ូតុង Action
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

    // 3. មុខងារលុប
    $('.btn-delete').click(function(e) {
        e.stopPropagation(); 
        let headerId = $(this).data('id');
        let docNo = $(this).data('doc');

        Swal.fire({
            title: 'តើអ្នកប្រាកដឬទេ?',
            html: `អ្នកកំពុងរៀបចំលុបវិក្កយបត្រ <b>"${docNo}"</b><br><span class="text-danger">ទំនិញក្នុងវិក្កយបត្រនេះនឹងត្រូវដកស្តុកចេញវិញ!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            confirmButtonText: 'យល់ព្រមលុប',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + headerId).submit();
            }
        });
    });
});
</script>
@endsection