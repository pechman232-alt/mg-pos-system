@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables Library -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    /* =========================================================
       CLEAN SaaS DESIGN SYSTEM (NO TAILWIND CDN NEEDED)
       ========================================================= */
    :root {
        --primary: #4F46E5;      /* Indigo 600 */
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      /* Slate 50 */
        --text-main: #111827;    /* Gray 900 */
        --text-muted: #6B7280;   /* Gray 500 */
        --border-color: #E5E7EB; /* Gray 200 */
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
    }

    body, .container-xxl { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); -webkit-font-smoothing: antialiased; }
    .fluid-page-wrapper { width: 100% !important; padding: 24px 32px !important; }

    /* --- MAIN TABLE CARD --- */
    .table-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden; 
    }

    /* Card Header */
    .table-header-custom {
        padding: 20px 24px; border-bottom: 1px solid var(--border-color);
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
        background: #FFFFFF;
    }
    .header-title { 
        font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; 
        color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; text-transform: uppercase;
    }

    /* Action Button */
    .btn-primary-custom {
        background: var(--primary); color: #FFF; border: none;
        padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-primary-custom:hover { background: var(--primary-hover); color: #FFF; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    /* --- TABLE STYLING --- */
    .table-custom { margin: 0 !important; width: 100%; border-collapse: collapse; }
    .table-custom thead th {
        background: #F9FAFB; color: var(--text-muted); font-size: 0.75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.05em; padding: 14px 24px;
        border-bottom: 1px solid var(--border-color); border-top: none;
    }
    .table-custom tbody td {
        padding: 16px 24px; vertical-align: middle; border-bottom: 1px solid #F3F4F6;
        font-size: 0.875rem; color: var(--text-main); transition: background 0.15s ease; font-weight: 500;
    }
    .table-custom tbody tr:hover td { background-color: #F8FAFC; }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    /* Badges */
    .badge-model { 
        background: #EEF2FF; color: var(--primary); border: 1px solid #C7D2FE; 
        border-radius: 6px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; 
        text-transform: uppercase; display: inline-block;
    }
    .badge-color {
        background: #F0FDF4; color: #059669; border: 1px solid #A7F3D0;
        border-radius: 6px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; display: inline-block;
    }

    /* Image Preview with Smooth Zoom */
    .car-preview-img {
        border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        cursor: zoom-in;
    }
    .car-preview-img:hover {
        transform: scale(2); z-index: 10; position: relative;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-color: #FFF;
    }

    /* Action Buttons (Table) */
    .action-group { display: flex; gap: 8px; justify-content: center; }
    .btn-icon {
        width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;
        background: #FFF; border: 1px solid var(--border-color); color: var(--text-muted); transition: all 0.2s;
    }
    .btn-icon:hover { border-color: var(--primary); color: var(--primary); background: #EEF2FF; }
    .btn-icon.btn-delete:hover { border-color: #EF4444; color: #EF4444; background: #FEF2F2; }

    /* DataTables Overrides (Clean SaaS Look) */
    .dataTables_wrapper { padding-top: 10px; }
    .dataTables_wrapper .dataTables_filter { padding: 10px 24px 20px 24px; float: left !important; }
    .dataTables_filter label { font-size: 0; /* លាក់ពាក្យ Search: */ }
    .dataTables_filter input {
        width: 320px !important; background: #F9FAFB !important; border: 1px solid #D1D5DB !important;
        border-radius: 8px !important; padding: 10px 16px !important; font-size: 0.875rem !important;
        color: var(--text-main) !important; margin-left: 0 !important; outline: none; transition: all 0.2s ease;
    }
    .dataTables_filter input:focus { background: #FFF !important; border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important; }
    
    .dataTables_wrapper .dataTables_info { padding: 20px 24px !important; font-size: 0.8rem; color: var(--text-muted); font-weight: 500; }
    .dataTables_wrapper .dataTables_paginate { padding: 16px 24px !important; }
    .paginate_button { border: 1px solid var(--border-color) !important; border-radius: 6px !important; padding: 4px 12px !important; font-size: 0.85rem !important; background: #FFF !important; margin: 0 2px !important; }
    .paginate_button.current { background: var(--primary) !important; color: #FFF !important; border-color: var(--primary) !important; font-weight: 600; }
    .paginate_button:hover:not(.current) { background: #F3F4F6 !important; color: var(--text-main) !important; }

    /* Animation */
    .anim-fade-up { animation: fadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(15px); }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="fluid-page-wrapper">
    <div class="table-card anim-fade-up">
        
        <!-- Header -->
        <div class="table-header-custom">
            <h5 class="header-title">
                <i class="fas fa-images text-muted"></i> FLEET ASSET REPOSITORY
            </h5>
            <a href="{{ url('/car-images/create') }}" class="btn-primary-custom">
                <i class="fas fa-plus"></i> ADD NEW ASSET
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatable" class="table-custom">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th class="text-center" style="width: 140px;">Visual Preview</th>
                            <th>Vehicle Model Mapping</th>
                            <th>Color Specification</th>
                            <th>Database Entry</th>
                            <th class="text-center" style="width: 120px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carImages as $index => $carImage)
                            <tr>
                                <td class="text-center text-muted fw-bold">#{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <img src="{{ $carImage->image_name == '' ? url('backend/assets/img/avatars/1.png') : url('/imageCar/' . $carImage->image_name) }}" 
                                         alt="car" class="car-preview-img" width="80" height="50">
                                </td>
                                <td>
                                    <span class="badge-model">{{ $carImage->modelName }}</span>
                                </td>
                                <td>
                                    <span class="badge-color">{{ $carImage->colorName }}</span>
                                </td>
                                <td class="text-muted" style="font-size: 0.8rem;">
                                    <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($carImage->created_at)->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{ url('car-images/edit', encrypt($carImage->id)) }}" class="btn-icon" title="Edit Record">
                                            <i class="fas fa-pen" style="font-size: 12px;"></i>
                                        </a>
                                        <button type="button" onclick="confirmDelete('{{ url('car-images/destroy', encrypt($carImage->id)) }}')" class="btn-icon btn-delete" title="Delete Permanent">
                                            <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
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

<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Asset?',
            html: `This visual record will be permanently removed from the server.<br><span style="color:#6B7280; font-size:13px;">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: '<span style="color:#4B5563">Cancel</span>',
            fontFamily: 'Inter',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-sm border-0',
                actions: 'd-flex flex-wrap justify-content-center gap-2 w-100 mt-4',
                confirmButton: 'btn fw-bold rounded-3 px-4 py-2 border-0 shadow-sm text-white',
                cancelButton: 'btn fw-bold rounded-3 px-4 py-2 border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the system.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); },
                    customClass: { popup: 'rounded-4 shadow-sm border-0' }
                });
                window.location.href = url;
            }
        });
    }

    $(document).ready(function() {
        // Initialize DataTables with Clean Layout
        $('#datatable').DataTable({
            "iDisplayLength": 10,
            "ordering": true,
            "dom": '<"top"f>rt<"d-flex justify-content-between align-items-center flex-wrap"ip><"clear">',
            "language": {
                "search": "",
                "searchPlaceholder": "Search by model or color...",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "next": '<i class="fas fa-chevron-right" style="font-size:10px;"></i>',
                    "previous": '<i class="fas fa-chevron-left" style="font-size:10px;"></i>'
                }
            },
            "drawCallback": function(settings) {
                $('#datatable tbody').hide().fadeIn(200);
            }
        });

        // SweetAlert2 Toast Notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-3 shadow-sm border-0' }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif
    });
</script>

@endsection