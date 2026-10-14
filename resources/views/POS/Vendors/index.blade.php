@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Battambang:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .vendor-container {
        --brand: #CE181E;
        --brand-dark: #a81116;
        --brand-soft: rgba(206, 24, 30, .08);
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
        --bg-soft: #f8fafc;
        --success: #16a34a;
        --success-soft: #e9f9ef;
        --radius: 12px;
        font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important;
    }

    /* Breeze Header Typography */
    .vc-eyebrow {
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }
    .vc-title {
        color: var(--ink);
        font-weight: 800;
        letter-spacing: -.02em;
        margin: 0;
    }

    /* Card Scaffolding */
    .vc-card {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        background: #ffffff;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    .vc-card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--line);
        padding: 1rem 1.25rem;
    }

    /* Breeze Pill Badges & Chips */
    .vc-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-soft);
        border: 1px solid var(--line);
        color: var(--ink);
        font-size: .8rem;
        font-weight: 600;
        padding: .35rem .75rem;
        border-radius: 9999px;
    }
    .vc-count-badge {
        background: var(--brand-soft);
        color: var(--brand);
        font-weight: 700;
        font-size: .78rem;
        padding: .3rem .65rem;
        border-radius: 9999px;
    }

    /* Input & Search */
    .vendor-container .input-group {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        transition: all .15s ease-in-out;
    }
    .vendor-container .input-group:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-soft);
    }
    .vendor-container .input-group-text {
        background: transparent;
        color: var(--muted);
        border: none;
        padding-left: .85rem;
    }
    .vendor-container .form-control {
        border: none !important;
        box-shadow: none !important;
        padding-top: .6rem;
        padding-bottom: .6rem;
        font-size: .875rem;
    }

    /* Buttons */
    .btn-brand {
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 8px;
        transition: .15s;
    }
    .btn-brand:hover {
        background: var(--brand-dark);
        color: #fff;
        transform: translateY(-1px);
    }
    .btn-white {
        background: #fff;
        border: 1px solid var(--line);
        color: var(--ink);
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-white:hover {
        background: var(--bg-soft);
        color: var(--brand);
        border-color: var(--brand);
    }

    /* Table Minimalist Breeze Style */
    .table-breeze thead th {
        background-color: var(--bg-soft);
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .05em;
        border-bottom: 1px solid var(--line);
        padding: .85rem 1rem;
    }
    .table-breeze td {
        border-color: var(--line) !important;
        vertical-align: middle;
        padding: .85rem 1rem;
        font-size: .875rem;
    }
    .table-breeze tbody tr {
        transition: background-color .12s ease;
    }
    .table-breeze tbody tr:hover {
        background-color: #f1f5f9;
    }

    /* Drag & Drop Area */
    .file-drop-area {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius);
        padding: 35px 20px;
        text-align: center;
        background-color: var(--bg-soft);
        transition: all .2s ease;
        cursor: pointer;
    }
    .file-drop-area:hover {
        border-color: var(--brand);
        background-color: var(--brand-soft);
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y vendor-container">
    
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <div class="vc-eyebrow">
                <i class="bx bx-store-alt"></i>
                <span>ទិន្នន័យដើម</span>
                <i class="bx bx-chevron-right"></i>
                <span>បញ្ជីអ្នកផ្គត់ផ្គង់</span>
            </div>
            <h4 class="vc-title">Vendors Management</h4>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-white shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-upload me-1 text-success"></i> Import Excel
            </button>
            <a href="{{ route('vendors.create') }}" class="btn btn-brand shadow-sm fw-bold px-3">
                <i class="bx bx-plus me-1"></i> បន្ថែមអ្នកផ្គត់ផ្គង់ថ្មី
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 4px solid #16a34a !important;" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-4 text-success me-2"></i>
                <span class="fw-semibold text-dark">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 4px solid #dc2626 !important;" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle fs-4 text-danger me-2"></i>
                <span class="fw-semibold text-dark">{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="vc-card mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9 col-12">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search fs-5"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ស្វែងរកតាមឈ្មោះក្រុមហ៊ុន, លេខទូរស័ព្ទ ឬ អ៊ីមែល..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-brand flex-grow-1 shadow-sm fw-semibold">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-white px-3" data-bs-toggle="tooltip" title="សម្អាតតម្រង">
                        <i class="bx bx-refresh fs-5"></i>
                    </a>
                </div>
            </form>

            @if(request('search'))
                <div class="mt-3">
                    <span class="vc-chip">
                        <i class="bx bx-search text-muted"></i>
                        លទ្ធផលសម្រាប់: "{{ request('search') }}"
                        <a href="{{ url()->current() }}" class="text-muted ms-1 text-decoration-none">&times;</a>
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Vendors Table Card -->
    <div class="vc-card">
        <div class="vc-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-list-ul fs-5 text-muted"></i>
                <span class="fw-bold" style="color: var(--ink);">តារាងអ្នកផ្គត់ផ្គង់</span>
            </div>
            @php
                $totalCount = method_exists($vendors, 'total') ? $vendors->total() : (is_countable($vendors) ? count($vendors) : 0);
            @endphp
            <span class="vc-count-badge">សរុប {{ number_format($totalCount) }} ក្រុមហ៊ុន</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-breeze table-bordered mb-0 align-middle">
                <thead>
                    <tr class="text-nowrap">
                        <th width="60" class="text-center">No</th>
                        <th class="text-start">ឈ្មោះអ្នកផ្គត់ផ្គង់ (Vendor Name)</th>
                        <th class="text-start">លេខទូរស័ព្ទ (Phone)</th>
                        <th class="text-start">អ៊ីមែល (Email)</th>
                        <th class="text-start">អាសយដ្ឋាន (Address)</th>
                        <th width="110" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $startIndex = (method_exists($vendors, 'perPage') && method_exists($vendors, 'currentPage'))
                            ? ($vendors->currentPage() - 1) * $vendors->perPage()
                            : 0;
                    @endphp
                    @forelse($vendors as $index => $vendor)
                    <tr>
                        <td class="text-center fw-semibold text-muted">{{ $startIndex + $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded p-1 d-inline-flex" style="background: var(--brand-soft); color: var(--brand);">
                                    <i class="bx bx-buildings fs-5"></i>
                                </div>
                                <span class="fw-bold" style="color: var(--ink);">{{ $vendor->vendor_name }}</span>
                            </div>
                        </td>
                        <td class="text-muted fw-semibold">
                            {{ $vendor->phone ? $vendor->phone : '-' }}
                        </td>
                        <td class="text-muted">
                            {{ $vendor->email ? $vendor->email : '-' }}
                        </td>
                        <td class="text-wrap text-muted" style="min-width: 220px; max-width: 350px;">
                            {{ $vendor->address ? $vendor->address : '-' }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-sm btn-icon btn-white" data-bs-toggle="tooltip" title="Edit Vendor">
                                    <i class="bx bx-edit-alt text-warning"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-icon btn-white btn-delete" data-id="{{ $vendor->id }}" data-name="{{ $vendor->vendor_name }}" data-bs-toggle="tooltip" title="Delete Vendor">
                                    <i class="bx bx-trash text-danger"></i>
                                </button>
                                <form id="delete-form-{{ $vendor->id }}" action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="rounded-circle p-3 mb-2" style="background: var(--bg-soft); color: var(--muted);">
                                    <i class="bx bx-buildings fs-1"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="color: var(--ink);">មិនទាន់មានទិន្នន័យអ្នកផ្គត់ផ្គង់ទេ!</h6>
                                <p class="text-muted small mb-3">សូមចុចប៊ូតុងខាងក្រោមដើម្បីបង្កើតអ្នកផ្គត់ផ្គង់ដំបូង</p>
                                <a href="{{ route('vendors.create') }}" class="btn btn-brand btn-sm px-3 fw-bold">
                                    <i class="bx bx-plus me-1"></i> បន្ថែមថ្មី
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($vendors, 'hasPages') && $vendors->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <span class="text-muted small">
                    កំពុងបង្ហាញ {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} នៃ {{ number_format($vendors->total()) }} អ្នកផ្គត់ផ្គង់
                </span>
                {{ $vendors->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Import Modal (Breeze Styled) -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius); overflow: hidden;">
            <div class="modal-header bg-white border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded p-1" style="background: var(--brand-soft); color: var(--brand);">
                        <i class="bx bx-upload fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold m-0" style="color: var(--ink); font-size: 1.05rem;">Import Vendors Excel</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('vendors.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">ទ្រង់ទ្រាយឯកសារ: .xlsx, .xls, .csv</span>
                        <a href="{{ route('vendors.template') }}" class="btn btn-sm btn-white text-primary">
                            <i class="bx bx-download me-1"></i> Template
                        </a>
                    </div>
                    
                    <div class="file-drop-area mb-3">
                        <i class="bx bx-cloud-upload text-muted mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-bold mb-1" style="color: var(--ink);">ជ្រើសរើសឯកសារសម្រាប់ Upload</h6>
                        <span class="text-muted small">ចុចត្រង់នេះដើម្បីស្វែងរក File ក្នុងម៉ាស៊ីន</span>
                        <input type="file" name="file" class="form-control mt-3" accept=".xlsx, .xls, .csv" required>
                    </div>
                    
                    <div class="rounded p-2" style="background-color: #fffbeb; border: 1px solid #fef3c7;">
                        <div class="d-flex gap-2">
                            <i class="bx bx-info-circle text-warning fs-5"></i>
                            <span class="small text-muted" style="line-height: 1.4;">
                                សូមប្រាកដថា Header ក្នុង Excel ត្រូវតាមលំដាប់: <b>vendor_name, phone, email, address</b>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top px-4 py-3">
                    <button type="button" class="btn btn-white fw-semibold" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-brand fw-bold px-4">
                        <i class="bx bx-check me-1"></i> ចាប់ផ្តើម Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    $('.btn-delete').click(function() {
        let vendorId = $(this).data('id');
        let vendorName = $(this).data('name');

        Swal.fire({
            title: '<span style="font-family: \'Plus Jakarta Sans\', \'Battambang\', sans-serif;">តើអ្នកប្រាកដឬទេ?</span>',
            html: `<span style="font-family: \'Plus Jakarta Sans\', \'Battambang\', sans-serif;">អ្នកកំពុងរៀបចំលុបអ្នកផ្គត់ផ្គង់ <b>"${vendorName}"</b><br>ទិន្នន័យនេះមិនអាចទាញយកមកវិញបានទេ។</span>`,
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
                $('#delete-form-' + vendorId).submit();
            }
        });
    });
});
</script>
@endsection