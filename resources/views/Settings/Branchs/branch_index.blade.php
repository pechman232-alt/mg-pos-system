@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
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
        --font-main: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-main);
        background-color: var(--bg-body);
        font-size: 0.82rem;
        color: #1e293b;
        -webkit-font-smoothing: antialiased;
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* --- DASHBOARD ELEMENTS --- */
    .card-main {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        background: #ffffff;
    }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    /* Professional Multi-Color Header Gradient */
    .card-header-main {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        padding: 20px 25px;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title { color: #fff; font-weight: 800; font-size: 1.05rem; margin: 0; letter-spacing: 0.5px; }

    /* Buttons */
    .btn-add-branch {
        background: rgba(255,255,255,0.15);
        color: white; border: 1px solid rgba(255,255,255,0.3);
        padding: 8px 20px; border-radius: 10px; font-weight: 700;
        transition: all 0.3s ease; text-decoration: none; font-size: 0.7rem;
    }
    .btn-add-branch:hover { background: #fff; color: var(--brand-indigo); transform: translateY(-2px); }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f8fafc; color: #64748b; font-weight: 800; 
        text-transform: uppercase; font-size: 0.65rem; letter-spacing: 1px;
        padding: 16px 20px; border-bottom: 1.5px solid #f1f5f9;
    }
    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .branch-name-text { font-weight: 800; color: #0f172a; font-size: 0.9rem; }
    .badge-soft { padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.65rem; background: #eef2ff; color: var(--brand-indigo); border: 1px solid rgba(79, 70, 229, 0.1); }

    .action-btn {
        width: 34px; height: 34px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: #64748b; border: 1px solid #e2e8f0; transition: 0.2s; text-decoration: none;
    }
    .action-btn:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); transform: translateY(-2px); }

    .avatar-branch {
        width: 42px; height: 42px; background: #eef2ff; color: var(--brand-indigo);
        display: flex; align-items: center; justify-content: center; border-radius: 12px;
        font-size: 1.1rem;
    }

    /* Custom Search Styling */
    .dataTables_filter input {
        border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 6px 12px; margin-left: 10px; outline: none; transition: 0.3s; font-size: 0.8rem;
    }
    .dataTables_filter input:focus { border-color: var(--brand-indigo); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.08); }
</style>

<div class="container-xxl">
    <div class="card-main animate-in">
        <div class="card-header-main">
            <h5 class="header-title">
                <i class='fas fa-map-location-dot me-2'></i>GLOBAL BRANCH NETWORK <span style="font-family: 'Noto Sans Khmer'; font-weight: 400; font-size: 0.75rem; opacity: 0.8; margin-left: 10px;">គ្រប់គ្រងបណ្តាញសាខា</span>
            </h5>
            <a href="{{ url('/branch/create') }}" class="btn-add-branch">
                <i class='fas fa-plus-circle me-1'></i> ADD NEW BRANCH
            </a>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">#</th>
                            <th style="text-align: left;">Branch Information</th>
                            <th class="text-center">Registration Date</th>
                            <th class="text-center" style="width: 150px;">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branchs as $index => $branch)
                        <tr>
                            <td class="text-center fw-bold text-muted" data-label="No">{{ $index + 1 }}</td>
                            <td style="text-align: left;" data-label="Branch Info">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-branch shadow-sm">
                                        <i class="fas fa-building-circle-check"></i>
                                    </div>
                                    <div>
                                        <span class="d-block branch-name-text">{{ $branch->branch_name }}</span>
                                        <small class="text-muted fw-800" style="font-size: 10px; letter-spacing: 0.5px;">UUID: #{{ str_pad($branch->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" data-label="Created Date">
                                <span class="badge-soft">
                                    <i class="far fa-calendar-alt me-1 opacity-50"></i>
                                    {{ \Carbon\Carbon::parse($branch->created_at)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="text-center" data-label="Actions">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/branch/edit', encrypt($branch->id)) }}" 
                                       class="action-btn" title="Modify Details">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-btn" 
                                            style="color: #ef4444; border-color: #fee2e2;"
                                            onclick="confirmBranchDeletion(event, '{{ $branch->id }}', '{{ $branch->branch_name }}')">
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

<form id="delete-branch-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
$(document).ready(function() {
    // 1. DataTable Initialization
    $('#datatable').DataTable({
        pageLength: 10,
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search infrastructure..."
        }
    });

    // 2. Success Notification (Toast)
    @if(session('success'))
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Action Successful', text: "{{ session('success') }}",
            showConfirmButton: false, timer: 3000, timerProgressBar: true
        });
    @endif
});

// 3. SECURE DELETE HANDLER
function confirmBranchDeletion(event, id, name) {
    // Prevent event bubbling and layout conflicts
    event.preventDefault();
    event.stopImmediatePropagation();

    Swal.fire({
        title: 'Authorize Deletion?',
        html: `You are about to remove <b>${name}</b> from the network. <br><span class="text-danger small fw-bold">Warning: This action will impact linked logistics.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, Decommission',
        cancelButtonText: 'Abort',
        reverseButtons: true,
        fontFamily: 'Plus Jakarta Sans',
        borderRadius: '16px'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating Directory...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            const form = document.getElementById('delete-branch-form');
            // Update the form action to your DELETE route
            form.action = "{{ url('/branch/destroy') }}/" + id;
            form.submit();
        }
    });
}
</script>

@endsection