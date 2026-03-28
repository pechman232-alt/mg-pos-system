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

    /* Notification Bell */
    .nav-item-notifications { position: relative; cursor: pointer; transition: 0.3s; }
    .nav-item-notifications:hover { transform: scale(1.1); }
    .notification-badge {
        position: absolute; top: -8px; right: -8px;
        background-color: #fff; color: var(--brand-red);
        border-radius: 50%; width: 18px; height: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 800; border: 2px solid var(--brand-red);
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 62, 29, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 62, 29, 0); }
    }

    /* Action Button (Add New) */
    .btn-add-warranty {
        background: rgba(255,255,255,0.2);
        color: white; border: 1px solid rgba(255,255,255,0.4);
        padding: 8px 18px; border-radius: 10px; font-weight: 700;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .btn-add-warranty:hover { background: #fff; color: var(--brand-purple); transform: translateY(-2px); }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f1f5f9; color: #475569; font-weight: 700; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;
        padding: 15px 20px; border: none; text-align: center;
    }
    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    /* Modern Status Badges */
    .badge-status {
        padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 10px;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.3s ease; text-transform: uppercase; cursor: pointer;
    }
    .status-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .status-inused { background: #f0f9ff; color: #0284c7; border: 1px solid #e0f2fe; }
    .status-expired { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    
    .badge-status:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

    /* Days Countdown Badges */
    .days-badge {
        padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 11px;
    }
    .days-safe { background: #ecfdf5; color: #059669; }
    .days-warning { background: #fffbeb; color: #d97706; }
    .days-danger { background: #fef2f2; color: #dc2626; }

    /* Operations Buttons */
    .action-btn {
        width: 36px; height: 36px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: #64748b; border: 1px solid #e2e8f0; transition: 0.2s;
    }
    .action-btn:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); transform: scale(1.1); }
</style>

@php
    $urgentCount = 0;
    foreach($warantys as $w) {
        $expiry = \Carbon\Carbon::parse($w->expiry_date);
        $diff = \Carbon\Carbon::now()->diffInDays($expiry, false);
        if($diff <= 30 && $diff > 0) { $urgentCount++; }
    }
@endphp

<div class="container-xxl">

    <div class="card-main">
        <div class="card-header-main">
            <div class="d-flex align-items-center">
                <h5 class="header-title me-4">
                    <i class='fas fa-shield-halved me-2'></i>WARRANTY ASSURANCE SYSTEM
                </h5>
                <div class="nav-item-notifications">
                    <i class='fas fa-bell text-white fs-5'></i>
                    @if($urgentCount > 0)
                        <span class="notification-badge">{{ $urgentCount }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ url('/warantys/create') }}" class="btn btn-add-warranty shadow-sm">
                <i class='fas fa-plus-circle me-1'></i> New Entry
            </a>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Rank</th>
                            <th style="text-align: left;">Assurance Designation</th>
                            <th>Status</th>
                            <th>Time Remaining</th>
                            <th>Maturity Date</th>
                            <th style="width: 150px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warantys as $index => $waranty)
                            @php
                                $expiry = \Carbon\Carbon::parse($waranty->expiry_date);
                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                                <td style="text-align: left;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; background: #f0f4ff; color: var(--brand-indigo); display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.1rem;">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                        <div>
                                            <span class="d-block fw-800 text-dark">{{ $waranty->name }}</span>
                                            <small class="text-muted fw-bold">ID: #WAR-{{ str_pad($waranty->id, 4, '0', STR_PAD_LEFT) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span onclick="toggleStatus('{{ encrypt($waranty->id) }}', '{{ $waranty->status }}')" 
                                          class="badge-status {{ $waranty->status == 'active' ? 'status-active' : ($waranty->status == 'inused' ? 'status-inused' : 'status-expired') }}">
                                        <i class="fas {{ $waranty->status == 'inused' ? 'fa-tools' : ($waranty->status == 'active' ? 'fa-shield-check' : 'fa-calendar-xmark') }}"></i>
                                        {{ $waranty->status == 'inused' ? 'In Service' : ucfirst($waranty->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($daysLeft > 30)
                                        <span class="days-badge days-safe"><i class="fas fa-circle-check me-1"></i> {{ $daysLeft }} Days</span>
                                    @elseif($daysLeft <= 30 && $daysLeft > 0)
                                        <span class="days-badge days-warning"><i class="fas fa-clock me-1"></i> {{ $daysLeft }} Days Left</span>
                                    @else
                                        <span class="days-badge days-danger"><i class="fas fa-triangle-exclamation me-1"></i> Terminated</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted fw-600">
                                    {{ $expiry->format('d M, Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ url('/warantys/edit', encrypt($waranty->id)) }}" class="action-btn" title="Edit">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ encrypt($waranty->id) }}')" class="action-btn text-danger" title="Delete">
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

<script>
    function toggleStatus(id, currentStatus) {
        // Rotates logic: Active -> Inused -> Expired
        let nextStatus = currentStatus === 'active' ? 'inused' : (currentStatus === 'inused' ? 'expired' : 'active');

        Swal.fire({
            title: 'Transition Status?',
            html: `Change assurance lifecycle to <b>${nextStatus.toUpperCase()}</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel',
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/warantys/toggle') }}/" + id + "?status=" + nextStatus;
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Warranty?',
            text: "This action will permanently erase this assurance record.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Erase Record',
            reverseButtons: true,
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/warantys/destroy') }}/" + id;
            }
        });
    }

    $(document).ready(function() {
        $('#datatable').DataTable({
            pageLength: 15,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Filter assurance records..."
            }
        });

        @if(session('success'))
            Swal.fire({
                toast: true, position: 'top-end', icon: 'success',
                title: 'Operation Successful', text: "{{ session('success') }}",
                showConfirmButton: false, timer: 3000, timerProgressBar: true
            });
        @endif
    });
</script>

@endsection