@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- 🌟 បន្ថែម DataTables Library នៅទីនេះ 🌟 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    /* --- GLOBAL BRANDING & OPTIMIZATION --- */
    :root {
        --brand-indigo: #4f46e5;
        --brand-indigo-hover: #4338ca;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --surface-white: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #cbd5e1;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: var(--text-main);
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 20px 30px !important;
    }

    /* --- ANIMATIONS (GPU Accelerated) --- */
    @keyframes fadeUp {
        0% { opacity: 0; transform: translate3d(0, 20px, 0); }
        100% { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    
    .anim-fade-up {
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        will-change: opacity, transform;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }

    /* --- DASHBOARD STATS --- */
    .stat-card {
        background: var(--surface-white);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border-left: 6px solid var(--brand-indigo);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }
    .stat-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 25px rgba(0,0,0,0.06); 
    }
    .stat-card.active { border-left-color: #10b981; }
    .stat-card.inactive { border-left-color: var(--brand-red); }

    /* --- MAIN TABLE CARD --- */
    .card-main {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: var(--surface-white);
        overflow: hidden;
        margin-top: 25px;
    }

    /* Professional Gradient Header */
    .card-header-gradient {
        background: linear-gradient(135deg, var(--brand-indigo) 0%, var(--brand-purple) 100%) !important;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .header-title { color: #fff; font-weight: 800; font-size: 1.15rem; margin: 0; letter-spacing: -0.3px; }

    /* Search Bar */
    .header-search-wrapper { position: relative; flex: 1; max-width: 400px; min-width: 250px; }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); }
    .header-search-input {
        width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px; padding: 10px 15px 10px 40px; color: #fff; transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }
    .header-search-input:focus {
        outline: none; background: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 4px rgba(255,255,255,0.1);
    }
    .header-search-input::placeholder { color: rgba(255,255,255,0.6); }

    /* Button New User */
    .btn-add-user {
        background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 9px 20px; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all 0.3s ease;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-add-user:hover { background: #fff; color: var(--brand-indigo); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

    /* --- TABLE STYLING WITH BORDERS --- */
    .table-responsive { padding: 15px; }
    .table { border-collapse: collapse; width: 100%; border: 1px solid var(--border-color); }
    
    .table thead th { 
        background: #f8fafc; color: var(--text-muted); font-weight: 800; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.8px;
        padding: 16px 20px; border: 1px solid var(--border-color); border-bottom: 2px solid #94a3b8;
    }
    
    .table tbody td { 
        padding: 15px 20px; vertical-align: middle; 
        border: 1px solid var(--border-color); transition: background 0.2s ease; 
    }
    .table tbody tr { transition: transform 0.2s ease, background 0.2s ease; }
    .table tbody tr:hover td { background-color: #f1f5f9; }
    
    .user-name-text { font-weight: 800; color: var(--text-main); font-size: 14px; }
    .avatar { width: 42px; height: 42px; border-radius: 12px; object-fit: cover; border: 2px solid #e2e8f0; }
    
    /* Badges */
    .badge-soft { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; display: inline-flex; align-items: center; gap: 4px; }
    .badge-soft-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-soft-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-role { background: #f8fafc; color: var(--brand-indigo); border: 1px solid var(--border-color); padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 10px; text-transform: uppercase; }

    /* Action Buttons */
    .btn-action {
        width: 34px; height: 34px; border-radius: 8px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: var(--text-muted); border: 1px solid var(--border-color); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); background: #f5f3ff; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-delete:hover { border-color: var(--brand-red); color: var(--brand-red); background: #fef2f2; }

    /* DataTables Pagination & Info */
    .dataTables_info { padding: 15px 0 !important; font-weight: 600; color: var(--text-muted); font-size: 0.8rem; }
    .dataTables_paginate { padding: 15px 0 !important; }
    .paginate_button { border: 1px solid var(--border-color) !important; border-radius: 8px !important; font-weight: 600 !important; margin: 0 2px !important; transition: 0.3s !important; background: #fff !important; color: var(--text-muted) !important; }
    .paginate_button.current { background: var(--brand-indigo) !important; color: white !important; border-color: var(--brand-indigo) !important; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3) !important; }
    .paginate_button:hover:not(.current) { background: #f1f5f9 !important; color: var(--brand-indigo) !important; border-color: var(--border-color) !important; }
</style>

<div class="container-xxl">

    <!-- STATS SECTION -->
    <div class="row g-4 mb-3">
        <div class="col-md-4 anim-fade-up">
            <div class="stat-card">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Total Directory</h6>
                <h3 class="fw-black m-0" style="font-size: 1.8rem; color: #0f172a;">{{ count($users) }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Staff Members</span></h3>
            </div>
        </div>
        <div class="col-md-4 anim-fade-up delay-100">
            <div class="stat-card active">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Active Accounts</h6>
                <h3 class="fw-black m-0 text-success" style="font-size: 1.8rem;">{{ $users->where('status', 1)->count() }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Online</span></h3>
            </div>
        </div>
        <div class="col-md-4 anim-fade-up delay-200">
            <div class="stat-card inactive">
                <h6 class="text-muted small fw-bold text-uppercase mb-2">Revoked Access</h6>
                <h3 class="fw-black m-0 text-danger" style="font-size: 1.8rem;">{{ $users->where('status', 0)->count() }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Disabled</span></h3>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="card-main anim-fade-up delay-300">
        <div class="card-header-gradient">
            <h5 class="header-title">
                <i class='fas fa-user-shield me-2'></i>USER ACCESS CONTROL
            </h5>
            
            <div class="header-search-wrapper d-none d-md-block">
                <i class='fas fa-search search-icon'></i>
                <input type="text" id="cleanSearch" class="header-search-input" placeholder="Quick search personnel...">
            </div>

            <a href="{{ url('/users/create') }}" class="btn-add-user">
                <i class='fas fa-plus-circle'></i> NEW USER
            </a>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="table align-middle m-0 w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Full Identification</th>
                        <th>Account Credentials</th>
                        <th>Assigned Branch</th>
                        <th>Access Role</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                    <tr>
                        <td class="text-center text-muted fw-bold" style="background-color: #f8fafc;">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_pic ? asset('imageUser/' . $user->profile_pic) : asset('backend/assets/img/avatars/1.png') }}" class="avatar me-3">
                                <div>
                                    <span class="d-block user-name-text">{{ $user->full_name }}</span>
                                    <span class="text-muted fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">UID: {{ $user->user_id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="fw-bold border" style="color: var(--brand-indigo); background: #f0f4ff; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">{{ $user->username }}</code>
                            <div class="text-muted fw-medium mt-1" style="font-size: 0.8rem;">{{ $user->email }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;"><i class="fas fa-building me-1 text-muted"></i> {{ $user->branchName ?? 'HQ' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 500; margin-top: 2px;">{{ $user->departName ?? 'Standard' }}</div>
                        </td>
                        <td>
                            <span class="badge-role">
                                <i class="fas fa-key me-1"></i> {{ $user->user_role_name ?? 'Employee' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                <i class="fas fa-circle" style="font-size: 6px;"></i>
                                {{ $user->status == 1 ? 'ACTIVE' : 'LOCKED' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ url('/users/edit', encrypt($user->id)) }}" class="btn-action shadow-sm" title="Edit User">
                                    <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
                                </a>
                                <button type="button" class="btn-action btn-delete shadow-sm" title="Revoke Access"
                                        onclick="confirmDelete('{{ url('/users/destroy', encrypt($user->id)) }}', '{{ addslashes($user->full_name) }}')">
                                    <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
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
        // 🌟 Initialize DataTables 🌟
        const table = $('#userTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
            "pageLength": 10,
            "ordering": false,
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ personnel",
                "paginate": { 
                    "previous": "<i class='fas fa-angle-left'></i>", 
                    "next": "<i class='fas fa-angle-right'></i>" 
                }
            },
            "drawCallback": function(settings) {
                $('#userTable tbody').hide().fadeIn(300);
            }
        });

        // 🌟 Custom Live Search Event 🌟
        $('#cleanSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function confirmDelete(url, name) {
        Swal.fire({
            title: 'Terminate Access?',
            html: `You are about to revoke access for <b style="color:#ef4444">${name}</b>.<br>This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete Account',
            cancelButtonText: 'Cancel',
            fontFamily: 'Plus Jakarta Sans',
            customClass: {
                confirmButton: 'btn btn-danger fw-bold rounded-3 px-4 border-0 shadow-sm',
                cancelButton: 'btn btn-secondary fw-bold rounded-3 px-4 ms-2 border-0 shadow-sm'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we secure the directory.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                window.location.href = url;
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Directory Updated',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-4 shadow-sm' }
        });
    @endif
</script>

@endsection