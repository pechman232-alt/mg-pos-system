@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- GLOBAL BRANDING --- */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: #1e293b;
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 15px 30px !important;
    }

    /* --- DASHBOARD ELEMENTS --- */
    .card-main {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        background: #ffffff;
        animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Professional Multi-Color Header Gradient */
    .card-header-main {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        padding: 20px 25px;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title { color: #fff; font-weight: 800; font-size: 1.1rem; margin: 0; letter-spacing: -0.5px; }

    /* Action Button (Add New) */
    .btn-add-new {
        background: rgba(255,255,255,0.2);
        color: white; border: 1px solid rgba(255,255,255,0.4);
        padding: 8px 18px; border-radius: 10px; font-weight: 700;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .btn-add-new:hover { background: #fff; color: var(--brand-purple); transform: translateY(-2px); }

    /* --- BULK ACTION BAR --- */
    #bulk-action-bar {
        display: none; background: #1e293b; border-radius: 12px;
        padding: 12px 25px; margin-bottom: 1.5rem; color: white;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f1f5f9; color: #475569; font-weight: 700; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;
        padding: 15px 20px; border: none; text-align: center;
    }
    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .province-name-text { font-weight: 800; color: #0f172a; font-size: 14px; }
    
    .status-badge {
        padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 11px;
        display: inline-flex; align-items: center; gap: 5px;
        background: #ecfdf5; color: #059669;
    }

    /* Operations Buttons */
    .action-btn {
        width: 36px; height: 36px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: #64748b; border: 1px solid #e2e8f0; transition: 0.2s;
    }
    .action-btn:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); transform: scale(1.1); }
    .btn-delete:hover { border-color: var(--brand-red); color: var(--brand-red); background: #fef2f2; }

    /* DataTables Custom Search */
    .dataTables_filter input {
        border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 6px 12px; outline: none; margin-bottom: 10px;
    }
    .dataTables_filter input:focus { border-color: var(--brand-indigo); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
</style>

<div class="container-xxl">

    <div id="bulk-action-bar" class="d-flex align-items-center justify-content-between shadow-lg">
        <div class="d-flex align-items-center">
            <div class="bg-primary p-2 rounded-circle me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check text-white small"></i>
            </div>
            <span class="fw-bold"><span id="selected-count">0</span> Provinces Selected</span>
        </div>
        <button type="button" onclick="bulkDelete()" class="btn btn-danger btn-sm fw-bold px-4 rounded-pill shadow-sm">
            <i class="fas fa-trash-alt me-2"></i> Delete Selected
        </button>
    </div>

    <div class="card-main">
        <div class="card-header-main">
            <h5 class="header-title">
                <i class='fas fa-map-location-dot me-2'></i>REGIONAL PROVINCE DIRECTORY
            </h5>
            <a href="{{ url('/provinces/create') }}" class="btn btn-add-new shadow-sm">
                <i class='fas fa-plus-circle me-1'></i> Add Province
            </a>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                            <th style="width: 60px;">#</th>
                            <th style="text-align: left;">Province Identity</th>
                            <th>Global Status</th>
                            <th style="width: 150px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($provinces as $index => $province)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $province->id }}" data-name="{{ $province->name }}">
                            </td>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td style="text-align: left;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: #f5f3ff; color: var(--brand-purple); display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.1rem;">
                                        <i class="fas fa-map-pin"></i>
                                    </div>
                                    <span class="d-block province-name-text">{{ $province->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge">
                                    <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/provinces/edit', encrypt($province->id)) }}" 
                                       class="action-btn" title="Modify">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-btn btn-delete" 
                                            onclick="deleteConfirm('{{ url('/provinces/destroy', encrypt($province->id)) }}', '{{ $province->name }}')">
                                        <i class="fas fa-trash-can"></i>
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

<form id="delete-form" method="GET" style="display:none;"></form>

<script>
    $(document).ready(function() {
        const table = $('#datatable').DataTable({
            "pageLength": 10,
            "dom": '<"row px-4 py-3"<"col-md-6"l><"col-md-6"f>>t<"row px-4 py-3"<"col-md-6"i><"col-md-6"p>>',
            "columnDefs": [{ "targets": 0, "orderable": false }],
            "language": {
                "search": "",
                "searchPlaceholder": "Search regions...",
                "paginate": { "previous": "<i class='fas fa-angle-left'></i>", "next": "<i class='fas fa-angle-right'></i>" }
            }
        });

        // --- BULK SELECTION LOGIC ---
        $('#checkAll').on('click', function() {
            $('.row-checkbox').prop('checked', this.checked);
            toggleBulkBar();
        });

        $(document).on('change', '.row-checkbox', function() {
            $('#checkAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
            toggleBulkBar();
        });

        function toggleBulkBar() {
            const count = $('.row-checkbox:checked').length;
            if (count > 0) {
                $('#bulk-action-bar').css('display', 'flex');
                $('#selected-count').text(count);
            } else {
                $('#bulk-action-bar').hide();
            }
        }
    });

    // Individual Delete Confirmation
    function deleteConfirm(url, name) {
        Swal.fire({
            title: 'Delete Province?',
            html: `Are you sure you want to remove <b>${name}</b> from the regional directory?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete Record',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', didOpen: () => { Swal.showLoading(); } });
                let form = document.getElementById('delete-form');
                form.action = url;
                form.submit();
            }
        });
    }

    // Bulk Delete Action
    function bulkDelete() {
        const selectedCount = $('.row-checkbox:checked').length;
        Swal.fire({
            title: 'Bulk Deletion',
            text: `Permanently remove ${selectedCount} selected provinces?`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Delete Selected',
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit logic here
                Swal.fire('Deleted!', 'Data has been synchronized.', 'success');
            }
        });
    }
</script>

@endsection