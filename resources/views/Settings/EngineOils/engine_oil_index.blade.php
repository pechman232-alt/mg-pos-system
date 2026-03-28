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
    .btn-add-oil {
        background: rgba(255,255,255,0.2);
        color: white; border: 1px solid rgba(255,255,255,0.4);
        padding: 8px 18px; border-radius: 10px; font-weight: 700;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .btn-add-oil:hover { background: #fff; color: var(--brand-purple); transform: translateY(-2px); }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f1f5f9; color: #475569; font-weight: 700; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;
        padding: 15px 20px; border: none; text-align: center;
    }
    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .oil-name-text { font-weight: 800; color: #0f172a; font-size: 14px; }
    
    /* Status Badges */
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

    .avatar-oil {
        width: 42px; height: 42px; background: #fff7ed; color: #f97316;
        display: flex; align-items: center; justify-content: center; border-radius: 12px;
        font-size: 1.2rem;
    }

    /* DataTables Custom Search */
    .dataTables_filter input {
        border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 6px 12px; outline: none; margin-bottom: 10px;
    }
    .dataTables_filter input:focus { border-color: var(--brand-indigo); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
</style>

<div class="container-xxl">

    @if(session('warning') || session('success') || session('info'))
        <script>
            Swal.fire({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                icon: "{{ session('warning') ? 'warning' : (session('info') ? 'info' : 'success') }}",
                title: "{{ session('warning') ?? session('success') ?? session('info') }}"
            });
        </script>
    @endif

    <div class="card-main">
        <div class="card-header-main">
            <h5 class="header-title">
                <i class='fas fa-oil-can me-2'></i>FLUID & ENGINE OIL INVENTORY
            </h5>
            <a href="{{ url('/engineOils/create') }}" class="btn btn-add-oil shadow-sm">
                <i class='fas fa-plus-circle me-1'></i> Add New Lubricant
            </a>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Rank</th>
                            <th style="text-align: left;">Lubricant Specification</th>
                            <th>Global Status</th>
                            <th>Entry Created</th>
                            <th style="width: 150px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($engineOils as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td style="text-align: left;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-oil">
                                        <i class="fas fa-droplet"></i>
                                    </div>
                                    <div>
                                        <span class="d-block oil-name-text">{{ $item->name }}</span>
                                        <small class="text-muted fw-bold">SKU: #OIL-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge">
                                    <i class="fas fa-check-circle" style="font-size: 6px;"></i> Active
                                </span>
                            </td>
                            <td class="text-center text-muted fw-600">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/engineOils/edit', encrypt($item->id)) }}" 
                                       class="action-btn" title="Modify Details">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    
                                    <form id="delete-form-{{ $item->id }}" action="{{ url('/engineOils/destroy', encrypt($item->id)) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    
                                    <button type="button" class="action-btn btn-delete" onclick="handleDelete('{{ $item->id }}', '{{ $item->name }}')">
                                        <i class="fas fa-trash-can" id="icon-{{ $item->id }}"></i>
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
    $(document).ready(function() {
        $('#datatable').DataTable({
            "pageLength": 15,
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            "language": {
                "search": "",
                "searchPlaceholder": "Search lubricants..."
            }
        });
    });

    function handleDelete(id, name) {
        Swal.fire({
            title: 'Confirm Deletion?',
            html: `You are about to remove <b>${name}</b> from the active inventory specifications.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete Record',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading on the icon
                const icon = document.getElementById('icon-' + id);
                icon.className = 'fas fa-spinner fa-spin';
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating inventory database',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>

@endsection