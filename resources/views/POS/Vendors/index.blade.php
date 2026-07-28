@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* Clean UI Customizations */
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .modal-header-custom {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e2e8f0;
    }
    .file-drop-area {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background-color: #f8fafc;
        transition: 0.3s;
    }
    .file-drop-area:hover {
        border-color: #0d6efd;
        background-color: #eff6ff;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិន្នន័យដើម /</span> បញ្ជីអ្នកផ្គត់ផ្គង់ (Vendors List)
        </h4>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            <!-- ប៊ូតុង Import -->
            <button type="button" class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-upload me-1"></i> Import Excel
            </button>
            <a href="{{ route('vendors.create') }}" class="btn btn-primary shadow-sm fw-bold">
                <i class="bx bx-plus me-1"></i> បន្ថែមថ្មី
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6 col-12">
                    <label class="form-label fw-bold text-primary">ស្វែងរកអ្នកផ្គត់ផ្គង់</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ឈ្មោះក្រុមហ៊ុន, លេខទូរស័ព្ទ ឬ អ៊ីមែល..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                        <i class="bx bx-filter-alt me-1"></i> ស្វែងរក
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="tooltip" title="Clear Filters">
                        <i class="bx bx-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive text-nowrap" style="min-height: 400px;">
            <table class="table table-hover table-striped table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr class="text-center text-primary text-nowrap" style="font-size: 13px;">
                        <th width="50" class="py-3">No</th>
                        <th class="py-3 text-start">ឈ្មោះអ្នកផ្គត់ផ្គង់ (Vendor Name)</th>
                        <th class="py-3 text-start">លេខទូរស័ព្ទ (Phone)</th>
                        <th class="py-3 text-start">អ៊ីមែល (Email)</th>
                        <th class="py-3 text-start">អាសយដ្ឋាន (Address)</th>
                        <th width="100" class="py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($vendors as $index => $vendor)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">
                            <i class="bx bx-buildings text-primary me-1"></i> {{ $vendor->vendor_name }}
                        </td>
                        <td>{{ $vendor->phone ?? '-' }}</td>
                        <td>{{ $vendor->email ?? '-' }}</td>
                        <td class="text-wrap text-muted" style="min-width: 200px;">{{ $vendor->address ?? '-' }}</td>
                        
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-sm btn-icon btn-outline-warning shadow-sm" data-bs-toggle="tooltip" title="Edit Vendor">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                
                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger shadow-sm btn-delete" data-id="{{ $vendor->id }}" data-name="{{ $vendor->vendor_name }}" data-bs-toggle="tooltip" title="Delete Vendor">
                                    <i class="bx bx-trash"></i>
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
                            <div class="empty-state">
                                <i class="bx bx-buildings text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold text-muted">មិនទាន់មានទិន្នន័យអ្នកផ្គត់ផ្គង់ទេ!</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($vendors, 'hasPages') && $vendors->hasPages())
        <div class="card-footer border-top pb-0 pt-3">
            <div class="d-flex justify-content-end">
                {{ $vendors->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Import Modal (Clean UI) -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bx bx-upload text-success me-2"></i> Import Vendors
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendors.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">សូម Upload ឯកសារ Excel ឬ CSV</span>
                        <!-- ប៊ូតុង Download Template -->
                        <a href="{{ route('vendors.template') }}" class="btn btn-sm btn-outline-primary fw-bold">
                            <i class="bx bx-download me-1"></i> ទាញយកទម្រង់គំរូ (Template)
                        </a>
                    </div>
                    
                    <div class="file-drop-area mb-3">
                        <i class="bx bx-cloud-upload fs-1 text-primary mb-2"></i>
                        <h6 class="fw-bold text-dark">ជ្រើសរើសឯកសារទិន្នន័យរបស់អ្នក</h6>
                        <input type="file" name="file" class="form-control mt-3" accept=".xlsx, .xls, .csv" required>
                    </div>
                    
                    <div class="alert alert-warning mb-0 p-2 small">
                        <i class="bx bx-info-circle me-1"></i> ចំណាំ៖ សូមធានាថាឈ្មោះ Column ក្នុងឯកសាររបស់អ្នកត្រូវគ្នានឹងទម្រង់គំរូ (Vendor Name, Phone, Email, Address)។
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bx bx-check-circle me-1"></i> យល់ព្រម Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    $('.btn-delete').click(function() {
        let vendorId = $(this).data('id');
        let vendorName = $(this).data('name');

        Swal.fire({
            title: 'តើអ្នកប្រាកដឬទេ?',
            html: `អ្នកកំពុងរៀបចំលុបអ្នកផ្គត់ផ្គង់ <b>"${vendorName}"</b><br>ទិន្នន័យនេះមិនអាចទាញយកមកវិញបានទេ។`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> យល់ព្រមលុប',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + vendorId).submit();
            }
        });
    });
});
</script>
@endsection