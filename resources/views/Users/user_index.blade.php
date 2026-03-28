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

    /* --- DASHBOARD STATS --- */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border-left: 5px solid var(--brand-indigo);
        transition: transform 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card.active { border-left-color: #10b981; }
    .stat-card.inactive { border-left-color: var(--brand-red); }

    /* --- MAIN TABLE CARD --- */
    .card-main {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        background: #ffffff;
        overflow: hidden;
        margin-top: 25px;
    }

    /* Professional 4-Color Gradient Header */
    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%) !important;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title { color: #fff; font-weight: 800; font-size: 1.1rem; margin: 0; letter-spacing: -0.5px; }

    /* Search Bar */
    .header-search-wrapper { position: relative; flex: 0 1 400px; }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); }
    .header-search-input {
        width: 100%; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px; padding: 10px 15px 10px 45px; color: #fff; transition: 0.3s;
    }
    .header-search-input::placeholder { color: rgba(255,255,255,0.6); }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f1f5f9; color: #475569; font-weight: 700; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;
        padding: 15px 20px; border: none;
    }
    .user-name-text { font-weight: 800; color: #0f172a; font-size: 14px; }
    .avatar { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; border: 2px solid #f1f5f9; }
    
    .badge-soft { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; }
    .badge-soft-success { background: #ecfdf5; color: #059669; }
    .badge-soft-danger { background: #fef2f2; color: #dc2626; }

    .btn-action {
        width: 35px; height: 35px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: #64748b; border: 1px solid #e2e8f0; transition: 0.2s;
    }
    .btn-action:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); transform: scale(1.1); }
    .btn-delete:hover { border-color: var(--brand-red); color: var(--brand-red); background: #fef2f2; }

    .btn-add-user {
        background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 8px 18px; border-radius: 8px; font-weight: 700; text-decoration: none; transition: 0.3s;
    }
    .btn-add-user:hover { background: #fff; color: var(--brand-purple); }

    .dataTables_info, .dataTables_paginate { padding: 20px !important; font-weight: 600; }
</style>

<div class="container-xxl">

    <div class="row g-4 mb-2">
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Directory</h6>
                <h3 class="fw-black m-0">{{ count($users) }} <small style="font-size: 12px; font-weight: 400;">Staff Members</small></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card active">
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Active Accounts</h6>
                <h3 class="fw-black m-0 text-success">{{ $users->where('status', 1)->count() }} <small style="font-size: 12px; font-weight: 400;">Online</small></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card inactive">
                <h6 class="text-muted small fw-bold text-uppercase mb-1">Revoked Access</h6>
                <h3 class="fw-black m-0 text-danger">{{ $users->where('status', 0)->count() }} <small style="font-size: 12px; font-weight: 400;">Disabled</small></h3>
            </div>
        </div>
    </div>

    <div class="card-main">
        <div class="card-header-gradient">
            <h5 class="header-title">
                <i class='fas fa-user-shield me-2'></i>USER ACCESS CONTROL
            </h5>
            
            <div class="header-search-wrapper d-none d-md-block">
                <i class='fas fa-search search-icon'></i>
                <input type="text" id="cleanSearch" class="header-search-input" placeholder="Quick search personnel...">
            </div>

            <a href="{{ url('/users/create') }}" class="btn-add-user">
                <i class='fas fa-plus-circle me-1'></i> NEW USER
            </a>
        </div>

        <div class="table-responsive">
            <table id="userTable" class="table align-middle m-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
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
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_pic ? asset('imageUser/' . $user->profile_pic) : asset('backend/assets/img/avatars/1.png') }}" class="avatar me-3">
                                <div>
                                    <span class="d-block user-name-text">{{ $user->full_name }}</span>
                                    <small class="text-muted fw-bold uppercase" style="font-size: 10px;">UID: {{ $user->user_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="fw-bold" style="color: var(--brand-indigo); background: #f0f4ff; padding: 3px 8px; border-radius: 5px;">{{ $user->username }}</code>
                            <div class="text-muted small mt-1">{{ $user->email }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-building me-1 text-muted"></i> {{ $user->branchName ?? 'HQ' }}</div>
                            <small class="text-muted">{{ $user->departName ?? 'Standard' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border fw-bold uppercase" style="font-size: 10px;">
                                <i class="fas fa-key me-1"></i> {{ $user->user_role_name ?? 'Employee' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                <i class="fas fa-circle me-1" style="font-size: 7px;"></i>
                                {{ $user->status == 1 ? 'ACTIVE' : 'LOCKED' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ url('/users/edit', encrypt($user->id)) }}" class="btn-action" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button type="button" class="btn-action btn-delete" 
                                        onclick="confirmDelete('{{ url('/users/destroy', encrypt($user->id)) }}', '{{ $user->full_name }}')">
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

<script>
    $(document).ready(function() {
        const table = $('#userTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center"ip>',
            "pageLength": 10,
            "language": {
                "paginate": { 
                    "previous": "<i class='fas fa-angle-left'></i>", 
                    "next": "<i class='fas fa-angle-right'></i>" 
                }
            }
        });

        $('#cleanSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function confirmDelete(url, name) {
        Swal.fire({
            title: 'Terminate Access?',
            text: `Revoking access for ${name}. This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Account',
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
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
            timerProgressBar: true
        });
    @endif
</script>

@endsection