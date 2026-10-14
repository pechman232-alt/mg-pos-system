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
        
        --success-bg: #D1FAE5;
        --success-text: #059669;
        --danger-bg: #FEE2E2;
        --danger-text: #DC2626;
        --info-bg: #E0F2FE;
        --info-text: #0284C7;

        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
    }

    body { background-color: var(--bg-body); font-family: var(--font-body); color: var(--text-main); }
    
    .page-wrapper { padding: 24px 32px; max-width: 100%; }

    /* --- STATS CARDS --- */
    .stat-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex; align-items: center; justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    
    .stat-info .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-info .stat-value { font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; color: var(--text-main); line-height: 1.2; margin: 0; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

    /* --- MAIN TABLE CARD --- */
    .table-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden; margin-top: 24px;
    }

    /* Card Header */
    .table-header-custom {
        padding: 20px 24px; border-bottom: 1px solid var(--border-color);
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
    }
    .table-header-title { font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; }

    /* Search Bar */
    .search-wrapper { position: relative; flex: 1; max-width: 320px; min-width: 200px; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF; }
    .search-input {
        width: 100%; background: #F9FAFB; border: 1px solid #D1D5DB;
        border-radius: 8px; padding: 8px 16px 8px 36px; font-size: 0.875rem;
        color: var(--text-main); transition: all 0.2s ease;
    }
    .search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); background: #FFF; }

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
        text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 24px;
        border-bottom: 1px solid var(--border-color); border-top: none;
    }
    .table-custom tbody td {
        padding: 16px 24px; vertical-align: middle; border-bottom: 1px solid #F3F4F6;
        font-size: 0.875rem; color: var(--text-main); transition: background 0.15s ease;
    }
    .table-custom tbody tr:hover td { background-color: #F8FAFC; }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    /* User Profile Cell */
    .user-profile { display: flex; align-items: center; gap: 12px; }
    .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color); }
    .user-name { font-weight: 600; color: var(--text-main); display: block; }
    .user-id { font-size: 0.75rem; color: var(--text-muted); font-family: 'Plus Jakarta Sans', monospace; font-weight: 600; }

    /* Badges */
    .badge-clean { padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    .badge-success { background: var(--success-bg); color: var(--success-text); }
    .badge-danger { background: var(--danger-bg); color: var(--danger-text); }
    .badge-role { background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; border-radius: 6px; padding: 2px 8px; font-size: 0.75rem; font-weight: 500; }

    /* Action Buttons (Table) */
    .action-group { display: flex; gap: 8px; justify-content: center; }
    .btn-icon {
        width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;
        background: #FFF; border: 1px solid var(--border-color); color: var(--text-muted); transition: all 0.2s;
    }
    .btn-icon:hover { border-color: var(--primary); color: var(--primary); background: #EEF2FF; }
    .btn-icon.btn-delete:hover { border-color: #EF4444; color: #EF4444; background: #FEF2F2; }

    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_info { padding: 16px 24px !important; font-size: 0.8rem; color: var(--text-muted); }
    .dataTables_wrapper .dataTables_paginate { padding: 12px 24px !important; }
    .paginate_button { border: 1px solid var(--border-color) !important; border-radius: 6px !important; padding: 4px 12px !important; font-size: 0.85rem !important; background: #FFF !important; margin: 0 2px !important; }
    .paginate_button.current { background: var(--primary) !important; color: #FFF !important; border-color: var(--primary) !important; font-weight: 600; }
    .paginate_button:hover:not(.current) { background: #F3F4F6 !important; color: var(--text-main) !important; }
</style>

<div class="page-wrapper">

    <!-- STATS SECTION -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Directory</div>
                    <div class="stat-value">{{ count($users) }} <span style="font-size: 14px; font-weight: 500; color: var(--text-muted);">Staff</span></div>
                </div>
                <div class="stat-icon" style="background: #EEF2FF; color: var(--primary);">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Active Accounts</div>
                    <div class="stat-value">{{ $users->where('status', 1)->count() }} <span style="font-size: 14px; font-weight: 500; color: var(--success-text);">Online</span></div>
                </div>
                <div class="stat-icon" style="background: var(--success-bg); color: var(--success-text);">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Revoked Access</div>
                    <div class="stat-value">{{ $users->where('status', 0)->count() }} <span style="font-size: 14px; font-weight: 500; color: var(--danger-text);">Disabled</span></div>
                </div>
                <div class="stat-icon" style="background: var(--danger-bg); color: var(--danger-text);">
                    <i class="fas fa-user-lock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="table-card">
        <div class="table-header-custom">
            <h5 class="table-header-title">
                <i class="fas fa-shield-alt text-muted"></i> User Access Control
            </h5>
            
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="cleanSearch" class="search-input" placeholder="Search personnel...">
                </div>

                <a href="{{ url('/users/create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus"></i> New User
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="table-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>User Profile</th>
                        <th>Credentials</th>
                        <th>Assignment</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <div class="user-profile">
                                <img src="{{ $user->profile_pic ? asset('imageUser/' . $user->profile_pic) : asset('backend/assets/img/avatars/1.png') }}" class="user-avatar">
                                <div>
                                    <span class="user-name">{{ $user->full_name }}</span>
                                    <span class="user-id">UID: {{ $user->user_id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $user->username }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark"><i class="far fa-building text-muted me-1"></i> {{ $user->branchName ?? 'HQ' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">{{ $user->departName ?? 'Standard' }}</div>
                        </td>
                        <td>
                            <span class="badge-role">{{ $user->user_role_name ?? 'Employee' }}</span>
                        </td>
                        <td>
                            <span class="badge-clean badge-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                {{ $user->status == 1 ? 'Active' : 'Locked' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ url('/users/edit', encrypt($user->id)) }}" class="btn-icon" title="Edit User">
                                    <i class="fas fa-pen" style="font-size: 12px;"></i>
                                </a>
                                <button type="button" class="btn-icon btn-delete" title="Revoke Access"
                                        onclick="confirmDelete('{{ url('/users/destroy', encrypt($user->id)) }}', '{{ addslashes($user->full_name) }}')">
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

<script>
    $(document).ready(function() {
        // Initialize DataTables
        const table = $('#userTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
            "pageLength": 10,
            "ordering": false,
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "paginate": { 
                    "previous": "<i class='fas fa-chevron-left' style='font-size:10px;'></i>", 
                    "next": "<i class='fas fa-chevron-right' style='font-size:10px;'></i>" 
                }
            }
        });

        // Custom Live Search Event
        $('#cleanSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    // Clean SweetAlert2 Delete Confirmation
    function confirmDelete(url, name) {
        Swal.fire({
            title: 'Revoke Access?',
            html: `You are about to remove access for <b style="color:#DC2626">${name}</b>.<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Yes, remove user',
            cancelButtonText: '<span style="color:#4B5563">Cancel</span>',
            fontFamily: 'Inter',
            customClass: {
                popup: 'rounded-4 shadow-sm',
                confirmButton: 'btn fw-bold rounded-3 px-4 border-0 text-white',
                cancelButton: 'btn fw-bold rounded-3 px-4 ms-2 border-0'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating directory securely.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                window.location.href = url;
            }
        });
    }

    // Success Toast Notification
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-3 shadow-sm' }
        });
    @endif
</script>
@endsection