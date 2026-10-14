@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Battambang:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .product-container {
        --brand: #CE181E;
        --brand-dark: #a81116;
        --brand-soft: rgba(206, 24, 30, .08);
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e6e9ef;
        --bg-soft: #f8fafc;
        --success: #16a34a;
        --success-soft: #e9f9ef;
        --warning: #b45309;
        --warning-soft: #fef3e2;
        --radius: 14px;
        font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important;
    }

    /* ---------- header ---------- */
    .pc-eyebrow {
        font-size: .8rem; font-weight: 600; color: var(--muted);
        display: flex; align-items: center; gap: 6px; margin-bottom: 2px;
    }
    .pc-title { color: var(--ink); font-weight: 800; letter-spacing: -.01em; margin: 0; text-transform: none; }

    /* ---------- shared card ---------- */
    .pc-card { border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; }
    .pc-card-header { background: linear-gradient(180deg, #fff, var(--bg-soft)); }
    .pc-icon-chip {
        width: 38px; height: 38px; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px; background: var(--brand-soft); color: var(--brand); font-size: 1.1rem;
    }

    /* ---------- inputs / buttons ---------- */
    .product-container label.form-label { font-size: .82rem; font-weight: 600; color: var(--muted); }
    .product-container .input-group {
        border: 1px solid var(--line); border-radius: 10px; background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .product-container .input-group:focus-within { border-color: var(--brand); box-shadow: 0 0 0 .2rem var(--brand-soft); }
    .product-container .input-group-text { background: transparent; color: var(--muted); border: none; }
    .product-container .form-control { border: none !important; box-shadow: none !important; padding-top: .6rem; padding-bottom: .6rem; }
    .product-container .form-control::placeholder { color: #b7c0cc; }

    .btn-brand { background: var(--brand); color: #fff; border: none; transition: .2s; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; transform: translateY(-1px); }

    .pc-icon-btn {
        width: 44px; border: 1px solid var(--line); border-radius: 10px; background: #fff; color: var(--muted);
        display: inline-flex; align-items: center; justify-content: center; transition: .15s; flex-shrink: 0;
    }
    .pc-icon-btn:hover { border-color: var(--brand); color: var(--brand); }

    .pc-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--bg-soft); border: 1px solid var(--line); color: var(--ink);
        font-size: .8rem; font-weight: 600; padding: .3rem .7rem; border-radius: 999px;
    }
    .pc-chip button { border: none; background: none; color: var(--muted); line-height: 1; padding: 0 0 0 2px; }
    .pc-chip button:hover { color: var(--brand); }

    .pc-count-badge {
        background: var(--brand-soft); color: var(--brand); font-weight: 700;
        font-size: .78rem; padding: .3rem .65rem; border-radius: 999px;
    }

    /* ---------- table ---------- */
    .table-report thead th {
        background-color: var(--bg-soft);
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .03em;
        border-bottom: 1px solid var(--line);
        padding-top: .9rem;
        padding-bottom: .9rem;
    }
    .table-report td { border-color: var(--line) !important; vertical-align: middle; }
    .table-report tbody tr { transition: background-color .12s; }
    .table-report tbody tr:hover { background-color: var(--bg-soft); }

    .pc-item-code {
        font-family: 'Plus Jakarta Sans', monospace;
        font-weight: 700; color: var(--ink);
        background: var(--bg-soft); border: 1px solid var(--line);
        padding: .2rem .55rem; border-radius: 6px; font-size: .82rem;
    }
    .pc-category-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--bg-soft); border: 1px solid var(--line); color: var(--ink);
        font-weight: 600; font-size: .78rem; padding: .35rem .65rem; border-radius: 8px;
    }
    .pc-price { color: var(--brand); font-weight: 800; }
    .pc-cost { color: var(--muted); }

    .pc-stock-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-weight: 700; font-size: .8rem; padding: .4rem .75rem; border-radius: 8px;
    }
    .pc-stock-out    { background: #fdecec; color: var(--brand); }
    .pc-stock-low    { background: var(--warning-soft); color: var(--warning); }
    .pc-stock-ok     { background: var(--success-soft); color: var(--success); }

    .pc-row-actions .btn { border-radius: 8px; }

    /* ---------- empty state ---------- */
    .pc-empty { padding: 3rem 1rem; }
    .pc-empty .pc-icon-chip { width: 64px; height: 64px; font-size: 1.8rem; margin: 0 auto 1rem; }

    /* ---------- footer / pagination ---------- */
    .pc-footer-note { font-size: .82rem; color: var(--muted); }
    .card-footer .pagination { margin-bottom: 0; }
    .card-footer .page-link { border-color: var(--line); color: var(--ink); }
    .card-footer .page-item.active .page-link { background-color: var(--brand); border-color: var(--brand); }
    .card-footer .page-link:hover { color: var(--brand); }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <div class="pc-eyebrow">
                <i class="bx bx-cube"></i>
                <span>ស្តុកទំនិញ</span>
                <i class="bx bx-chevron-right"></i>
                <span>បញ្ជីទំនិញ</span>
            </div>
            <h4 class="pc-title">Products List</h4>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('products.create') }}" class="btn btn-brand shadow-sm fw-bold">
                <i class="bx bx-plus me-1"></i> បន្ថែមទំនិញថ្មី
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 4px solid #198754 !important;" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-4 text-success me-2"></i>
                <span class="fw-semibold text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ========== Filter / Search ========== -->
    <div class="card pc-card mb-4 shadow-sm">
        <div class="card-header pc-card-header border-bottom py-3 d-flex align-items-center">
            <div class="pc-icon-chip me-3"><i class="bx bx-search-alt"></i></div>
            <div>
                <h6 class="m-0 fw-bold" style="color: var(--ink);">ស្វែងរកទំនិញ</h6>
                <div class="text-muted" style="font-size:.8rem;">ស្វែងរកតាមកូដទំនិញ ឈ្មោះ ឬប្រភេទ</div>
            </div>
        </div>
        <div class="card-body p-4">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-8 col-12">
                    <label class="form-label mb-1">ស្វែងរកទំនិញ (Search Product)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search fs-5"></i></span>
                        <input type="text" name="search" class="form-control ps-0" placeholder="បញ្ចូលកូដទំនិញ, ឈ្មោះ ឬ ប្រភេទ..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-brand flex-grow-1 shadow-sm fw-bold">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="pc-icon-btn" data-bs-toggle="tooltip" title="សម្អាតតម្រង (Clear)">
                        <i class="bx bx-refresh fs-5"></i>
                    </a>
                </div>
            </form>

            @if(request('search'))
                <div class="mt-3">
                    <span class="pc-chip">
                        <i class="bx bx-search fs-6"></i>
                        លទ្ធផលសម្រាប់ "{{ request('search') }}"
                        <a href="{{ url()->current() }}"><button type="button">✕</button></a>
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- ========== Products Table ========== -->
    <div class="card pc-card shadow-sm">
        <div class="card-header pc-card-header border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <div class="pc-icon-chip me-3"><i class="bx bx-list-ul"></i></div>
                <h6 class="m-0 fw-bold" style="color: var(--ink);">ព័ត៌មានទំនិញទាំងអស់</h6>
            </div>
            @php
                $totalCount = method_exists($products, 'total') ? $products->total() : (is_countable($products) ? count($products) : 0);
            @endphp
            <span class="pc-count-badge">សរុប {{ number_format($totalCount) }} ទំនិញ</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-report table-bordered mb-0 align-middle">
                <thead>
                    <tr class="text-center text-nowrap">
                        <th width="60" class="py-3">No</th>
                        <th class="py-3 text-start">Item Code</th>
                        <th class="py-3 text-start">Product Name</th>
                        <th class="py-3 text-start">Category</th>
                        <th class="py-3 text-start">Location (សាខា)</th>
                        <th class="py-3 text-start">Description</th> <!-- ✅ បន្ថែម Header Description -->
                        <th class="py-3 text-end">Cost ($)</th>
                        <th class="py-3 text-end">Price ($)</th>
                        <th class="py-3">In Stock</th>
                        <th class="py-3 text-center">Create Date</th>
                        <th class="py-3 text-center">Updated By</th>
                        <th width="120" class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php
                        $startIndex = (method_exists($products, 'perPage') && method_exists($products, 'currentPage'))
                            ? ($products->currentPage() - 1) * $products->perPage()
                            : 0;
                        $lowStockThreshold = 5;
                    @endphp
                    @forelse($products as $index => $product)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $startIndex + $index + 1 }}</td>

                        <td><span class="pc-item-code">{{ $product->item_code }}</span></td>
                        <td class="fw-semibold text-wrap" style="min-width: 200px; color: var(--ink);">{{ $product->item_name }}</td>

                        <td>
                            <span class="pc-category-badge"><i class="bx bx-category-alt"></i>{{ $product->category ?? 'N/A' }}</span>
                        </td>

                        <td>
                            @if($product->location_id && $product->branch)
                                <span class="pc-category-badge" style="background: var(--brand-soft); color: var(--brand); border: 1px solid var(--brand-soft);">
                                    <i class="bx bx-store-alt"></i>{{ $product->branch->branch_name }}
                                </span>
                            @else
                                <span class="pc-category-badge"><i class="bx bx-store-alt"></i>ទូទៅ (All)</span>
                            @endif
                        </td>

                        <!-- ✅ បន្ថែម ទិន្នន័យ Description ដែលមាន Tooltip ការពារកុំឱ្យវែងពេកខូចតារាង -->
                        <td>
                            @if($product->description)
                                <div class="text-truncate text-muted" style="max-width: 150px; cursor: pointer;" data-bs-toggle="tooltip" title="{{ $product->description }}">
                                    {{ $product->description }}
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="text-end pc-cost">${{ number_format($product->cost ?? 0, 2) }}</td>
                        <td class="text-end pc-price">${{ number_format($product->unit_price ?? $product->price ?? $product->selling_price ?? 0, 2) }}</td>

                        <td class="text-center">
                            @if(($product->stock_qty ?? 0) <= 0)
                                <span class="pc-stock-badge pc-stock-out"><i class="bx bx-block"></i> Out of Stock</span>
                            @elseif($product->stock_qty <= $lowStockThreshold)
                                <span class="pc-stock-badge pc-stock-low"><i class="bx bx-error"></i> {{ number_format($product->stock_qty, 0) }} left</span>
                            @else
                                <span class="pc-stock-badge pc-stock-ok"><i class="bx bx-check-circle"></i> {{ number_format($product->stock_qty, 0) }}</span>
                            @endif
                        </td>

                        <td class="text-center text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $product->created_at ? date('d-M-Y H:i', strtotime($product->created_at)) : '-' }}
                        </td>
                        <td class="text-center">
                            <span class="pc-category-badge"><i class="bx bx-user"></i>{{ $product->user->username ?? $product->user->name ?? 'Admin' }}</span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2 pc-row-actions">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-icon btn-outline-warning" data-bs-toggle="tooltip" title="Edit Product">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete" data-id="{{ $product->id }}" data-name="{{ $product->item_name }}" data-bs-toggle="tooltip" title="Delete Product">
                                    <i class="bx bx-trash"></i>
                                </button>

                                <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12"> <!-- ✅ ដូរពី colspan 11 ទៅ 12 ដោយសារយើងថែម Description ១ កូឡុំទៀត -->
                            <div class="pc-empty text-center">
                                <div class="pc-icon-chip"><i class="bx bx-package"></i></div>
                                <h6 class="fw-bold m-0" style="color: var(--ink);">មិនទាន់មានទំនិញនៅឡើយទេ ឬ រកមិនឃើញ!</h6>
                                <p class="text-muted mt-1 mb-3">សូមសាកល្បងពាក្យគន្លឹះផ្សេង ឬបន្ថែមទំនិញថ្មីមួយ</p>
                                <a href="{{ route('products.create') }}" class="btn btn-brand fw-bold px-4">
                                    <i class="bx bx-plus me-1"></i> បន្ថែមទំនិញថ្មី
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <span class="pc-footer-note">
                    កំពុងបង្ហាញ {{ $products->firstItem() }}–{{ $products->lastItem() }} នៃ {{ number_format($products->total()) }}
                </span>
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Initialize Tooltips សម្រាប់ Description និង Buttons
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Delete Confirmation with SweetAlert2
    $('.btn-delete').click(function() {
        let productId = $(this).data('id');
        let productName = $(this).data('name');

        Swal.fire({
            title: '<span style="font-family: \'Plus Jakarta Sans\', \'Battambang\', sans-serif;">តើអ្នកប្រាកដឬទេ?</span>',
            html: `<span style="font-family: \'Plus Jakarta Sans\', \'Battambang\', sans-serif;">អ្នកកំពុងរៀបចំលុបទំនិញ <b>"${productName}"</b><br>ទិន្នន័យនេះមិនអាចទាញយកមកវិញបានទេ។</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> យល់ព្រមលុប',
            cancelButtonText: 'បោះបង់',
            customClass: {
                title: 'fw-bold',
                popup: 'rounded-4 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + productId).submit();
            }
        });
    });
});
</script>
@endsection