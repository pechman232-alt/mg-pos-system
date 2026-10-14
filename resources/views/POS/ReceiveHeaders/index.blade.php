@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Battambang:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .history-container { 
        font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; 
        --brand: #CE181E;
        --brand-tint: #FCEBEC;
        --border: #E4E7EC;
        background-color: #F8FAFC;
        font-size: 14px;
    }

    .history-container h4 { font-size: clamp(1.05rem, 1.6vw + .6rem, 1.4rem); }

    /* Button Styles */
    .btn-brand { background-color: var(--brand); color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    
    /* POS Field Style សម្រាប់ទម្រង់ស្វែងរក (Filter) */
    .pos-field label { font-size: 12px; font-weight: 600; color: #4B5565; margin-bottom: 6px; display: block; }
    .pos-input-wrap {
        position: relative; border: 1.5px solid var(--border); border-radius: 8px;
        background: #FFFFFF; display: flex; align-items: center;
        transition: border-color .14s ease, box-shadow .14s ease;
    }
    .pos-input-wrap:focus-within { border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-tint); }
    .pos-input-wrap .ic { width: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #8A93A3; font-size: 15px; }
    .pos-input-wrap input, .pos-input-wrap select {
        width: 100%; padding: 9px 12px 9px 0; font-family: inherit; font-size: 13px;
        font-weight: 500; color: #12141A; border: 0 !important; outline: 0 !important;
        box-shadow: none !important; background: transparent !important;
    }
    
    /* Summary Card Styles */
    .summary-card { transition: all 0.3s ease; border-radius: 10px; cursor: pointer; border: 1px solid var(--border); }
    .summary-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .summary-card.active { background-color: var(--brand); color: white; border-color: var(--brand); box-shadow: 0 5px 15px rgba(206, 24, 30, 0.3) !important; }
    .summary-card.active .text-muted, .summary-card.active small { color: #ffcccc !important; }
    .summary-card .card-body { padding: .9rem 1rem; }
    .summary-card h6 { font-size: .88rem; }
    .summary-card small { font-size: .74rem; }

    /* ==========================================
       ROWSPAN TABLE STYLES (RESPONSIVE)
       ========================================== */
    .table-wrapper {
        background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-custom { width: 100%; min-width: 1400px; border-collapse: separate; border-spacing: 0; }
    .table-custom th {
        background-color: #F8FAFC; padding: 11px 14px; border-bottom: 1px solid #E2E8F0; border-right: 1px solid #F1F5F9;
        font-weight: normal; vertical-align: top; white-space: nowrap;
    }
    .table-custom th:last-child { border-right: none; }
    
    .th-kh { font-size: 12px; font-weight: 700; color: #6366f1; margin-bottom: 2px; }
    .th-en { font-size: 10px; color: #94A3B8; }

    .table-custom td {
        padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #F1F5F9;
        border-right: 1px solid #F8FAFC; font-size: 12.5px; color: #475569;
    }
    .table-custom td:last-child { border-right: none; }
    .table-custom tbody tr:hover td { background-color: #F8FAFC; }
    
    /* Fix borders for rowspan */
    .table-custom td[rowspan] { border-bottom: 1px solid #E2E8F0; background-color: #fff; }
    .table-custom tr:last-child td { border-bottom: 1px solid #E2E8F0; }

    /* Frozen first column so the document stays visible while scrolling wide table */
    .table-custom .sticky-col {
        position: sticky; left: 0; z-index: 1; background-color: #fff;
        box-shadow: 1px 0 0 #F1F5F9;
    }
    .table-custom thead .sticky-col { z-index: 2; background-color: #F8FAFC; }
    .table-custom tbody tr:hover .sticky-col { background-color: #F8FAFC; }

    /* Mobile swipe hint under the table */
    .scroll-hint {
        display: none; align-items: center; gap: 6px; justify-content: center;
        color: #94A3B8; font-size: 11.5px; font-weight: 600; padding: 8px 0 2px;
    }
    .scroll-hint i { animation: pc-hint-x 1.2s ease-in-out infinite; }
    @keyframes pc-hint-x { 0%,100% { transform: translateX(0); } 50% { transform: translateX(4px); } }

    /* Badges & Text */
    .inv-text { color: #4F46E5; font-weight: 700; font-size: 13px; text-decoration: none; }
    .inv-text:hover { text-decoration: underline; }
    .date-text { color: #94A3B8; display: flex; align-items: center; gap: 4px; font-size: 11.5px; }
    .badge-branch { background: #FFEDD5; color: #EA580C; border: 1px solid #FED7AA; padding: 5px 9px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; font-size: 11px; }
    .badge-pi { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; padding: 5px 9px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; font-size: 11px; }
    .badge-cat { background: #DCFCE7; color: #16A34A; border: 1px solid #BBF7D0; padding: 3px 7px; border-radius: 4px; font-weight: 600; font-size: 10.5px; }
    
    .fw-600 { font-weight: 600; }
    .fw-700 { font-weight: 700; }
    .text-dark-blue { color: #1E293B; }
    .text-qty { color: #3B82F6; font-weight: 700; }
    .text-amount { color: #EF4444; font-weight: 700; }
    .text-grand { color: #10B981; font-weight: 700; font-size: 13px; }

    /* Action Buttons */
    .action-btns { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .btn-sq { 
        width: 30px; height: 30px; border-radius: 6px; border: none; 
        display: flex; align-items: center; justify-content: center; 
        color: #fff; font-size: 15px; cursor: pointer; transition: 0.2s; text-decoration: none;
    }
    .btn-sq-info { background-color: #3B82F6; } .btn-sq-info:hover { background-color: #2563EB; color: #fff;}
    .btn-sq-yellow { background-color: #F59E0B; } .btn-sq-yellow:hover { background-color: #D97706; color: #fff;}
    .btn-sq-gray { background-color: #64748B; } .btn-sq-gray:hover { background-color: #475569; color: #fff;}
    .btn-sq-red { background-color: #EF4444; } .btn-sq-red:hover { background-color: #DC2626; color: #fff;}

    /* ---------- Responsive tweaks ---------- */
    @media (max-width: 991.98px) {
        .table-custom { min-width: 1250px; }
        .scroll-hint { display: flex; }
    }
    @media (max-width: 767.98px) {
        .table-custom th, .table-custom td { padding: 10px 12px; }
        .pos-field label { font-size: 11.5px; }
        .btn-sq { width: 28px; height: 28px; font-size: 14px; }
    }
    @media (max-width: 575.98px) {
        .history-container { font-size: 13px; }
        .summary-card .card-body { padding: .75rem .85rem; }
    }
</style>

@php
    $latestBranches = $branches->sortByDesc('id')->take(4);
    $colorPalette = [
        ['bg' => '#dbeafe', 'text' => '#2563eb', 'border' => '#bfdbfe'],
        ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#bbf7d0'],
        ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fed7aa'],
        ['bg' => '#f3e8ff', 'text' => '#9333ea', 'border' => '#e9d5ff'],
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

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
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
                                <small class="text-muted">សាខា</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('receive-headers.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-4">
                    <div class="pos-field">
                        <label>សាខា (Branch)</label>
                        <div class="pos-input-wrap">
                            <span class="ic"><i class="bx bx-store"></i></span>
                            <select name="branch_id">
                                <option value="">-- គ្រប់សាខាទាំងអស់ --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-4">
                    <div class="pos-field">
                        <label>កូដទំនិញ (Item Code)</label>
                        <div class="pos-input-wrap">
                            <span class="ic"><i class="bx bx-barcode"></i></span>
                            <input type="text" name="item_code" value="{{ request('item_code') }}" placeholder="ស្វែងរកតាមកូដ...">
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-md-4">
                    <div class="pos-field">
                        <label>ស្វែងរកទូទៅ (Search)</label>
                        <div class="pos-input-wrap">
                            <span class="ic"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="លេខឯកសារ ឬ លេខវិក្កយបត្រយោង...">
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-2 col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1 shadow-sm fw-bold" style="height: 42px; border-radius: 8px;">
                        <i class="bx bx-search-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ route('receive-headers.index') }}" class="btn btn-outline-secondary shadow-sm" style="height: 42px; width: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-refresh fs-4"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. TABLE DATA (ROWSPAN) -->
    <div class="table-wrapper">
        <table class="table-custom">
            <thead>
                <tr>
                    <th class="sticky-col">
                        <div class="th-kh">លេខឯកសារ (Doc Nº)</div>
                        <div class="th-en">Document No</div>
                    </th>
                    <th>
                        <div class="th-kh">កាលបរិច្ឆេទ</div>
                        <div class="th-en">Date</div>
                    </th>
                    <th>
                        <div class="th-kh">ទីតាំងសាខា</div>
                        <div class="th-en">Branch</div>
                    </th>
                    <th>
                        <div class="th-kh">អ្នកផ្គត់ផ្គង់</div>
                        <div class="th-en">Vendor</div>
                    </th>
                    <th>
                        <div class="th-kh">វិក្កយបត្រយោង</div>
                        <div class="th-en">Ref Invoice</div>
                    </th>
                    <th>
                        <div class="th-kh">កូដទំនិញ</div>
                        <div class="th-en">Item Code</div>
                    </th>
                    <th>
                        <div class="th-kh">ឈ្មោះទំនិញ</div>
                        <div class="th-en">Item Name</div>
                    </th>
                    <th class="text-center">
                        <div class="th-kh">បរិមាណ</div>
                        <div class="th-en">Qty</div>
                    </th>
                    <th class="text-end">
                        <div class="th-kh">តម្លៃដើម/១</div>
                        <div class="th-en">Cost ($)</div>
                    </th>
                    <th class="text-end">
                        <div class="th-kh">សរុប</div>
                        <div class="th-en">Amount ($)</div>
                    </th>
                    <th class="text-end">
                        <div class="th-kh">សរុបរួម</div>
                        <div class="th-en">Grand Total</div>
                    </th>
                    <th class="text-center" style="min-width: 140px;">
                        <div class="th-kh">សកម្មភាព</div>
                        <div class="th-en">Action</div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($headers as $item)
                    @php
                        $detailCount = $item->details->count();
                        $rowspanCount = $detailCount > 0 ? $detailCount : 1;
                        $grandTotal = $item->details->sum('total_cost');
                    @endphp

                    @if($detailCount > 0)
                        @foreach($item->details as $index => $detail)
                            <tr>
                                @if($index == 0)
                                    <!-- Header Data -->
                                    <td rowspan="{{ $rowspanCount }}" class="sticky-col">
                                        <a href="{{ route('receive-items.index', ['header_id' => $item->id]) }}" class="inv-text">{{ $item->document_no }}</a>
                                    </td>
                                    <td rowspan="{{ $rowspanCount }}">
                                        <span class="date-text"><i class="bx bx-time-five"></i> {{ date('d-M-Y', strtotime($item->date)) }}</span>
                                    </td>
                                    <td rowspan="{{ $rowspanCount }}">
                                        @php $sColor = $branchColors[$item->branch->branch_name] ?? ['bg' => '#FFEDD5', 'text' => '#EA580C', 'border' => '#FED7AA']; @endphp
                                        <span class="badge-branch" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border-color: {{ $sColor['border'] }};">
                                            <i class="bx bx-store-alt"></i> {{ $item->branch->branch_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td rowspan="{{ $rowspanCount }}" class="fw-700 text-dark-blue">{{ $item->vendor->vendor_name ?? 'N/A' }}</td>
                                    <td rowspan="{{ $rowspanCount }}">
                                        @if($item->purchase_invoice_no)
                                            <span class="badge-pi"><i class="bx bx-hash"></i> {{ $item->purchase_invoice_no }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif

                                <!-- Details Data -->
                                <td class="fw-600 text-dark-blue">{{ $detail->product->item_code ?? 'N/A' }}</td>
                                <td>{{ $detail->product->item_name ?? 'N/A' }} <span class="badge-cat ms-1">{{ $detail->product->category ?? '' }}</span></td>
                                <td class="text-center text-qty">{{ number_format($detail->qty, 2) }}</td>
                                <td class="text-end fw-600">${{ number_format($detail->cost ?? $detail->unit_cost, 2) }}</td>
                                <td class="text-end text-amount">${{ number_format($detail->total_cost, 2) }}</td>

                                @if($index == 0)
                                    <!-- Grand Total & Action Buttons -->
                                    <td rowspan="{{ $rowspanCount }}" class="text-end text-grand">${{ number_format($grandTotal, 2) }}</td>
                                    <td rowspan="{{ $rowspanCount }}">
                                        <div class="action-btns justify-content-center">
                                            <a href="{{ route('receive-items.index', ['header_id' => $item->id]) }}" class="btn-sq btn-sq-info" title="បញ្ចូលទំនិញ"><i class="bx bx-box"></i></a>
                                            <a href="{{ route('receive-headers.edit', encrypt($item->id)) }}" class="btn-sq btn-sq-yellow" title="កែប្រែ"><i class="bx bx-edit"></i></a>
                                            <a href="{{ route('receive-headers.print', $item->id) }}" target="_blank" class="btn-sq btn-sq-gray" title="បោះពុម្ព"><i class="bx bx-printer"></i></a>
                                            <button type="button" class="btn-sq btn-sq-red btn-delete" data-id="{{ $item->id }}" data-doc="{{ $item->document_no }}" title="លុប"><i class="bx bx-trash"></i></button>
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('receive-headers.destroy', $item->id) }}" method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <!-- Fallback សម្រាប់ក្បាលវិក្កយបត្រដែលមិនទាន់មានបញ្ចូលទំនិញ -->
                        <tr>
                            <td rowspan="1" class="sticky-col"><a href="{{ route('receive-items.index', ['header_id' => $item->id]) }}" class="inv-text">{{ $item->document_no }}</a></td>
                            <td rowspan="1"><span class="date-text"><i class="bx bx-time-five"></i> {{ date('d-M-Y', strtotime($item->date)) }}</span></td>
                            <td rowspan="1">
                                @php $sColor = $branchColors[$item->branch->branch_name] ?? ['bg' => '#FFEDD5', 'text' => '#EA580C', 'border' => '#FED7AA']; @endphp
                                <span class="badge-branch" style="background-color: {{ $sColor['bg'] }}; color: {{ $sColor['text'] }}; border-color: {{ $sColor['border'] }};">
                                    <i class="bx bx-store-alt"></i> {{ $item->branch->branch_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td rowspan="1" class="fw-700 text-dark-blue">{{ $item->vendor->vendor_name ?? 'N/A' }}</td>
                            <td rowspan="1">
                                @if($item->purchase_invoice_no)
                                    <span class="badge-pi"><i class="bx bx-hash"></i> {{ $item->purchase_invoice_no }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td colspan="5" class="text-center text-muted">មិនទាន់មានទំនិញ...</td>
                            
                            <td rowspan="1" class="text-end text-grand">$0.00</td>
                            <td rowspan="1">
                                <div class="action-btns justify-content-center">
                                    <a href="{{ route('receive-items.index', ['header_id' => $item->id]) }}" class="btn-sq btn-sq-info" title="បញ្ចូលទំនិញ"><i class="bx bx-box"></i></a>
                                    <a href="{{ route('receive-headers.edit', encrypt($item->id)) }}" class="btn-sq btn-sq-yellow" title="កែប្រែ"><i class="bx bx-edit"></i></a>
                                    <button type="button" class="btn-sq btn-sq-red btn-delete" data-id="{{ $item->id }}" data-doc="{{ $item->document_no }}" title="លុប"><i class="bx bx-trash"></i></button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('receive-headers.destroy', $item->id) }}" method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <h5 class="fw-bold text-muted mb-0"><i class="bx bx-file-blank"></i> មិនមានប្រវត្តិវិក្កយបត្រទិញចូលទេ!</h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="scroll-hint">
        <i class="bx bx-left-arrow-alt"></i> អូសតារាងទៅឆ្វេង/ស្តាំ ដើម្បីមើលបន្ថែម <i class="bx bx-right-arrow-alt"></i>
    </div>

    @if(method_exists($headers, 'hasPages') && $headers->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $headers->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Tooltip Initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // 2. Delete Confirmation (SweetAlert)
    $('.btn-delete').click(function(e) {
        e.preventDefault(); 
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