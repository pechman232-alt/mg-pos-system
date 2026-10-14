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

    /* ស្ទីលសម្រាប់ប្រអប់ស្វែងរក */
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

    /* ✅ ផ្ទាំងរុំតារាង (Scrollable) */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid var(--tbl-border);
        border-radius: 6px;
        background-color: #fff;
    }

    /* ✅ លក្ខណៈតារាង ១ជួរ (Flat Table) */
    .data-table {
        width: 100%;
        min-width: 2000px; /* ពង្រីកឱ្យធំបន្តិច ព្រោះមាន ១៥ Columns */
        border-collapse: collapse;
        table-layout: fixed; 
    }

    /* ✅ រក្សា ១ ជួរដាច់ខាត មិនឱ្យធ្លាក់បន្ទាត់ */
    .data-table th, .data-table td {
        padding: 10px 14px;
        vertical-align: middle;
        white-space: nowrap !important; /* ហាមធ្លាក់ជួរ */
        overflow: hidden !important; /* លាក់អក្សរវែង */
        text-overflow: ellipsis !important; /* លោតសញ្ញា ... */
        border: 1px solid var(--tbl-border);
    }

    /* ក្បាលតារាង ពណ៌ប្រផេះខៀវ ដូចក្នុងរូប */
    .data-table thead th {
        background-color: var(--tbl-header-bg);
        color: var(--tbl-header-text);
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .data-table tbody td {
        font-size: 12px;
        color: var(--tbl-text-main);
        font-weight: 500;
        background-color: #ffffff;
    }

    .data-table tbody tr:hover td { background-color: #f8fafc; cursor: pointer; }

    /* ✅ កំណត់ទំហំ Column ទាំង ១៥ */
    .data-table th:nth-child(1) { width: 3%; text-align: center; } /* # */
    .data-table th:nth-child(2) { width: 8%; } /* INVOICE Nº */
    .data-table th:nth-child(3) { width: 6%; text-align: center; } /* DATE */
    .data-table th:nth-child(4) { width: 7%; text-align: center; } /* BRANCH */
    .data-table th:nth-child(5) { width: 8%; } /* SALEMAN */
    .data-table th:nth-child(6) { width: 8%; } /* CAR BUYER */
    .data-table th:nth-child(7) { width: 7%; text-align: center; } /* CAR INVOICE */
    .data-table th:nth-child(8) { width: 8%; } /* REASON */
    .data-table th:nth-child(9) { width: 8%; } /* REMARK */
    .data-table th:nth-child(10) { width: 8%; } /* ITEM CODE */
    .data-table th:nth-child(11) { width: 12%; } /* DESCRIPTION */
    .data-table th:nth-child(12) { width: 4%; text-align: center; } /* QTY */
    .data-table th:nth-child(13) { width: 6%; text-align: right; } /* PRICE */
    .data-table th:nth-child(14) { width: 6%; text-align: right; } /* TOTAL */
    .data-table th:nth-child(15) { width: 6%; text-align: center; } /* SELLER */

    .badge-branch { background-color: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 4px; font-size: 11px; }
</style>

@php
    if(!isset($branches)) $branches = \App\Models\BranchModel::all();
    $globalIndex = method_exists($sales, 'currentPage') ? ($sales->currentPage() - 1) * $sales->perPage() + 1 : 1; 
@endphp

<div class="container-fluid flex-grow-1 report-container py-4">
    
    <!-- ចំណងជើងទំព័រ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0 text-uppercase text-secondary">
            <span>របាយការណ៍ /</span> <span style="color: #1e293b;">STOCK OUT REPORT</span>
        </h5>
    </div>

    <!-- Branch Tabs -->
    <div class="branch-tabs">
        <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}" class="branch-tab-item {{ empty(request('branch_id')) ? 'active' : '' }}">
            គ្រប់សាខាទាំងអស់
        </a>
        @foreach($branches as $b)
            <a href="{{ request()->fullUrlWithQuery(['branch_id' => $b->id]) }}" class="branch-tab-item {{ request('branch_id') == $b->id ? 'active' : '' }}">
                {{ $b->branch_name }}
            </a>
        @endforeach
    </div>
<!-- ផ្ទាំងស្វែងរក និង Export -->
<div class="d-flex flex-wrap gap-3 align-items-center mb-4 bg-white p-3 rounded shadow-sm border" style="border-color: var(--tbl-border) !important;">
    <form action="{{ route('reports.daily-sales') }}" method="GET" class="d-flex flex-wrap w-100 gap-3 align-items-center m-0">
        
        {{-- ✅ រក្សាទុកសាខា (Branch) ដែលកំពុងជ្រើសរើស ពេលចុចស្វែងរក --}}
        @if(request('branch_id'))
            <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
        @endif

        <div class="d-flex align-items-center gap-3 flex-grow-1 flex-wrap">
            
            {{-- ✅ ប្រអប់ស្វែងរកទូទៅ (Search All) --}}
            <div class="position-relative" style="min-width: 280px;">
                <i class="bx bx-search position-absolute text-muted" style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 16px;"></i>
                <input type="text" name="search" class="saas-input w-100" style="padding-left: 32px;" 
                       placeholder="ស្វែងរក វិក្កយបត្រ, អតិថិជន, កូដទំនិញ..." 
                       value="{{ request('search') }}">
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">កាលបរិច្ឆេទ:</span>
                <input type="date" name="from_date" class="saas-input" value="{{ request('from_date', $fromDate ?? date('Y-m-d')) }}">
                <span class="text-muted small">ដល់</span>
                <input type="date" name="to_date" class="saas-input" value="{{ request('to_date', $toDate ?? date('Y-m-d')) }}">
            </div>
        </div>
        
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <button type="submit" class="btn btn-dark btn-sm px-3 shadow-sm">
                <i class="bx bx-search me-1"></i> ស្វែងរក
            </button>
            
            {{-- ប៊ូតុង Clear សម្រាប់លុបទិន្នន័យស្វែងរកចេញ --}}
            @if(request('search'))
                <a href="{{ route('reports.daily-sales', ['branch_id' => request('branch_id'), 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="btn btn-outline-secondary btn-sm px-2 shadow-sm" title="លុបការស្វែងរក">
                    <i class="bx bx-x fs-5"></i>
                </a>
            @endif

            {{-- ✅ ប៊ូតុង Analytics Dashboard --}}
            <a href="{{ route('expense.dashboard') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold" style="background-color: #4F46E5; border: none;">
                <i class="bx bx-pie-chart-alt-2 me-1"></i> Analytics
            </a>

            <a href="{{ route('reports.daily-sales-export', request()->query()) }}" class="btn btn-success btn-sm px-3 shadow-sm fw-bold">
                <i class="bx bx-spreadsheet me-1"></i> Export Excel
            </a>
        </div>
    </form>
</div>

    <!-- ✅ តារាងទិន្នន័យ (រត់តែ ១ ជួរដេក មាន ១៥ Columns) -->
    <div class="table-wrapper shadow-sm">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>INVOICE Nº</th>
                    <th>DATE</th>
                    <th>BRANCH</th>
                    <th>SALEMAN</th>
                    <th>CAR BUYER</th>
                    <th>CAR INVOICE</th>
                    <th>REASON</th>
                    <th>REMARK</th>
                    <th>ITEM CODE</th>
                    <th>DESCRIPTION</th>
                    <th>QTY</th>
                    <th>PRICE ($)</th>
                    <th>TOTAL ($)</th>
                    <th>SELLER</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $item)
                    @if(isset($item->items) && count($item->items) > 0)
                        @foreach($item->items as $sub)
                            <tr>
                                <td class="text-center text-muted">{{ $globalIndex++ }}</td>
                                <td class="text-danger fw-bold" title="#{{ $item->document_no }}">#{{ $item->document_no }}</td>
                                <td class="text-center text-muted" title="{{ date('d/M/y', strtotime($item->date)) }}">{{ date('d/M/y', strtotime($item->date)) }}</td>
                                <td class="text-center" title="{{ $item->loc_id ?? 'Vive Warehouse' }}">
                                    <span class="badge-branch">{{ $item->loc_id ?? 'Vive Warehouse' }}</span>
                                </td>
                                <td title="{{ $item->customer_name }}">{{ strtoupper($item->customer_name) }}</td>
                                <td title="{{ $item->car_buyer_name }}">{{ strtoupper($item->car_buyer_name ?? '-') }}</td>
                                
                                {{-- ទិន្នន័យ CAR INVOICE, REASON, REMARK --}}
                                <td class="text-center" title="{{ $item->reference }}">{{ $item->reference ?? '-' }}</td>
                                <td class="text-danger" title="{{ $item->reason }}">{{ $item->reason ?? '-' }}</td>
                                <td class="text-info" title="{{ $item->remark }}">{{ $item->remark ?? '-' }}</td>
                                
                                <td class="fw-bold text-dark" title="{{ $sub->item_code }}">{{ $sub->item_code }}</td>
                                <td title="{{ $sub->item_name }}">{{ $sub->item_name }}</td>
                                <td class="text-center fw-bold text-primary">{{ number_format($sub->qty) }}</td>
                                <td class="text-end text-muted">${{ number_format($sub->unit_price, 2) }}</td>
                                <td class="text-end fw-bold text-danger">${{ number_format($sub->total_price, 2) }}</td>
                                
                                <td class="text-center text-primary" title="{{ $item->by_user }}">{{ strtoupper($item->by_user ?? 'ADMIN') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center text-muted">{{ $globalIndex++ }}</td>
                            <td class="text-danger fw-bold" title="#{{ $item->document_no }}">#{{ $item->document_no }}</td>
                            <td class="text-center text-muted" title="{{ date('d/M/y', strtotime($item->date)) }}">{{ date('d/M/y', strtotime($item->date)) }}</td>
                            <td class="text-center" title="{{ $item->loc_id ?? 'Vive Warehouse' }}">
                                <span class="badge-branch">{{ $item->loc_id ?? 'Vive Warehouse' }}</span>
                            </td>
                            <td title="{{ $item->customer_name }}">{{ strtoupper($item->customer_name) }}</td>
                            <td title="{{ $item->car_buyer_name }}">{{ strtoupper($item->car_buyer_name ?? '-') }}</td>
                            
                            {{-- ទិន្នន័យ CAR INVOICE, REASON, REMARK --}}
                            <td class="text-center" title="{{ $item->reference }}">{{ $item->reference ?? '-' }}</td>
                            <td class="text-danger" title="{{ $item->reason }}">{{ $item->reason ?? '-' }}</td>
                            <td class="text-info" title="{{ $item->remark }}">{{ $item->remark ?? '-' }}</td>
                            
                            <td colspan="5" class="text-center text-muted fst-italic">មិនមានទំនិញ</td>
                            
                            <td class="text-center text-primary" title="{{ $item->by_user }}">{{ strtoupper($item->by_user ?? 'ADMIN') }}</td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="15" class="text-center py-5 text-muted fw-bold">មិនមានទិន្នន័យទេ!</td></tr>
                @endforelse
            </tbody>
            
            @if($sales->count() > 0)
            <tfoot>
                <tr style="background-color: #f8fafc;">
                    {{-- Colspan 13 (ពី # រហូតដល់ PRICE) ដើម្បីឱ្យត្រូវជាមួយ TOTAL --}}
                    <td colspan="13" class="text-end text-uppercase" style="font-size: 12px; font-weight: 600; color: #64748b;">Grand Total Summary (All Invoices):</td>
                    <td class="text-end text-success" style="font-size: 14px; font-weight: 700;">${{ number_format($totalAmount ?? 0, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    
    <!-- ផ្នែកទំព័រ (Pagination) -->
    @if(method_exists($sales, 'hasPages') && $sales->hasPages())
    <div class="d-flex justify-content-end mt-3">
        {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection