@extends('layouts.homePages')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ====== BRAND IDENTITY & COLORS ====== */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --brand-success: #71dd37;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body {
        background-color: var(--bg-body);
        font-family: var(--font-family);
        font-size: 13px;
        color: #1e293b;
        margin: 0;
    }

    /* ====== 100% FIT SCREEN LAYOUT ====== */
    .fluid-page-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: fadeInPage 0.5s ease-out;
    }

    .card-main {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        background: #fff;
        overflow: hidden;
        width: 100%;
    }

    /* ====== BRANDED GRADIENT HEADER ====== */
    .branded-header {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important;
        padding: 1.2rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        color: white !important;
        font-weight: 800;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .btn-add-car {
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(8px);
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        border-radius: 10px !important;
        font-weight: 700;
        font-size: 12px !important;
        padding: 10px 22px !important;
        transition: 0.3s;
    }

    .btn-add-car:hover {
        background: white !important;
        color: var(--brand-indigo) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* ====== TABLE & FULL WIDTH SCALING ====== */
    .table-responsive {
        padding: 0;
        margin: 0;
        width: 100%;
    }

    #datatable {
        width: 100% !important;
        margin: 0 !important;
    }

    #datatable thead th {
        background-color: #fcfdfe !important;
        color: #64748b !important;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        padding: 1.5rem !important;
        border-bottom: 2px solid #f1f5f9 !important;
    }

    #datatable tbody td {
        padding: 15px 1.5rem !important;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
    }

    .highlight-badge {
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-block;
        font-weight: 700;
    }
    .bg-indigo-light { background: rgba(79, 70, 229, 0.08); color: var(--brand-indigo); }
    .bg-success-light { background: rgba(113, 221, 55, 0.1); color: #519b22; }

    /* ====== IMAGE GALLERY STYLING ====== */
    .car-preview-img {
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        cursor: zoom-in;
    }

    .car-preview-img:hover {
        transform: scale(2.2);
        z-index: 100;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    /* ====== ACTION BUTTONS ====== */
    .action-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        transition: 0.2s ease;
        text-decoration: none !important;
    }
    .action-btn:hover { background: var(--brand-indigo); color: white; border-color: var(--brand-indigo); transform: scale(1.1); }
    .btn-delete:hover { background: var(--brand-red); border-color: var(--brand-red); }

    /* Custom Search UI */
    .dataTables_wrapper .dataTables_filter {
        padding: 20px 24px;
        float: left !important;
        width: 100%;
    }
    .dataTables_filter input {
        width: 300px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 10px 20px !important;
        background-color: #f8fafc !important;
        margin-left: 0 !important;
        outline: none;
    }
    .dataTables_filter input:focus { border-color: var(--brand-indigo) !important; background-color: #fff !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    @keyframes fadeInPage { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="fluid-page-wrapper">
    <div class="card-main">
        <div class="branded-header">
            <h5 class="header-title">
                <i class="fas fa-images"></i> FLEET ASSET REPOSITORY
            </h5>
            <a href="{{ url('/car-images/create') }}" class="btn btn-add-car">
                <i class="fas fa-plus-circle me-1"></i> ADD NEW ASSET
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th class="text-center" style="width: 150px;">Visual Preview</th>
                            <th>Vehicle Model Mapping</th>
                            <th>Color Specification</th>
                            <th class="text-center">Database Entry</th>
                            <th class="text-center" style="width: 120px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carImages as $index => $carImage)
                            <tr>
                                <td class="text-center text-muted fw-bold">#{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <img src="{{ $carImage->image_name == '' ? url('backend/assets/img/avatars/1.png') : url('/imageCar/' . $carImage->image_name) }}" 
                                         alt="car" class="car-preview-img" width="100" height="65">
                                </td>
                                <td>
                                    <span class="highlight-badge bg-indigo-light text-uppercase">{{ $carImage->modelName }}</span>
                                </td>
                                <td>
                                    <span class="highlight-badge bg-success-light fw-800">{{ $carImage->colorName }}</span>
                                </td>
                                <td class="text-center text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($carImage->created_at)->format('d M, Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ url('car-images/edit', encrypt($carImage->id)) }}" 
                                           class="action-btn" title="Edit Record">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button type="button" 
                                           onclick="confirmDelete('{{ url('car-images/destroy', encrypt($carImage->id)) }}')" 
                                           class="action-btn btn-delete" title="Delete Permanent">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Asset?',
            text: "This visual record will be permanently removed from the server.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Confirm Delete',
            customClass: {
                confirmButton: 'btn btn-primary px-4 me-3',
                cancelButton: 'btn btn-label-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    $(document).ready(function() {
        $('#datatable').DataTable({
            "iDisplayLength": 10,
            "ordering": true,
            "dom": '<"top"f>rt<"bottom"lip><"clear">',
            "language": {
                "search": "",
                "searchPlaceholder": "Search by model or color...",
                "paginate": {
                    "next": '<i class="fas fa-chevron-right"></i>',
                    "previous": '<i class="fas fa-chevron-left"></i>'
                }
            }
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif
    });
</script>

@endsection