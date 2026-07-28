@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #CE181E;
        --secondary-color: #0f172a;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }

    .product-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    
    .input-group-text { background-color: var(--bg-light); color: #64748b; }
    
    .btn-brand { background-color: var(--primary-color); color: white; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    
    .table-report th { 
        background-color: #f1f5f9; 
        color: var(--secondary-color); 
        font-weight: 700; 
        text-transform: uppercase; 
        font-size: 13px;
    }
    .table-report tbody tr:hover { background-color: #f8fafc; }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទំនិញស្តុក /</span> បញ្ជីទំនិញ (Products List)
        </h4>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('products.create') }}" class="btn btn-brand shadow-sm fw-bold">
                <i class="bx bx-plus me-1"></i> បន្ថែមទំនិញថ្មី (Add Product)
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

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-8 col-12">
                    <label class="form-label fw-semibold text-muted">ស្វែងរកទំនិញ (Search Product)</label>
                    <div class="input-group input-group-merge shadow-none border rounded">
                        <span class="input-group-text border-0"><i class="bx bx-search fs-5"></i></span>
                        <input type="text" name="search" class="form-control border-0 ps-0" placeholder="បញ្ចូលកូដទំនិញ, ឈ្មោះ ឬ ប្រភេទ..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-dark flex-grow-1 shadow-sm fw-bold">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="tooltip" title="Clear Filters">
                        <i class="bx bx-refresh fs-5"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
            <i class="bx bx-list-ul fs-4 me-2" style="color: var(--primary-color);"></i>
            <h6 class="m-0 fw-bold text-dark">ព័ត៌មានទំនិញទាំងអស់</h6>
        </div>

        <div class="table-responsive text-nowrap" style="min-height: 400px;">
            <table class="table table-report table-bordered mb-0 align-middle">
                <thead>
                    <tr class="text-center text-nowrap">
                        <th width="60" class="py-3">No</th>
                        <th class="py-3 text-start">Item Code</th>
                        <th class="py-3 text-start">Product Name</th>
                        <th class="py-3 text-start">Category</th>
                        <th class="py-3 text-end">Cost ($)</th>
                        <th class="py-3 text-end">Price ($)</th>
                        <th class="py-3">In Stock</th>
                        <th class="py-3 text-center">Create Date</th>
                        <th class="py-3 text-center">Updated By</th>
                        <th width="120" class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($products as $index => $product)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                        
                        <td class="fw-bold text-dark">{{ $product->item_code }}</td>
                        <td class="fw-semibold text-wrap" style="min-width: 250px;">{{ $product->item_name }}</td>
                        
                        <td>
                            <span class="badge bg-light text-dark border"><i class="bx bx-category-alt me-1 text-muted"></i>{{ $product->category ?? 'N/A' }}</span>
                        </td>
                        
                        <td class="text-end text-muted">${{ number_format($product->cost ?? 0, 2) }}</td>
                        <td class="text-end fw-bold text-danger">${{ number_format($product->unit_price ?? $product->price ?? $product->selling_price ?? 0, 2) }}</td>
                        
                        <td class="text-center">
                            @if($product->stock_qty > 0)
                                <span class="badge bg-label-info fw-bold px-3 py-2 fs-6">{{ number_format($product->stock_qty, 0) }}</span>
                            @else
                                <span class="badge bg-label-danger fw-bold px-3 py-2 fs-6">Out of Stock</span>
                            @endif
                        </td>

                        <td class="text-center text-muted small">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $product->created_at ? date('d-M-Y H:i', strtotime($product->created_at)) : '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-secondary">
                                <i class="bx bx-user me-1"></i> {{ $product->user->username ?? $product->user->name ?? 'Admin' }}
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-icon btn-outline-warning shadow-sm" data-bs-toggle="tooltip" title="Edit Product">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger shadow-sm btn-delete" data-id="{{ $product->id }}" data-name="{{ $product->item_name }}" data-bs-toggle="tooltip" title="Delete Product">
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
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bx bx-package text-muted mb-3" style="font-size: 3.5rem; opacity: 0.5;"></i>
                                <h6 class="fw-bold text-muted m-0">មិនទាន់មានទំនិញនៅឡើយទេ ឬ រកមិនឃើញ!</h6>
                                <p class="text-muted fs-6 mt-1">សូមសាកល្បងពាក្យគន្លឹះផ្សេង ឬចុចប៊ូតុង "បន្ថែមទំនិញថ្មី"។</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="card-footer bg-white border-top pb-2 pt-3">
            <div class="d-flex justify-content-end">
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
    // Initialize Tooltips
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