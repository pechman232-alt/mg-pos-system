@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .audit-container {
        --brand: #CE181E;
        --brand-dark: #a81116;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
        --bg-soft: #f8fafc;
        --radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }
    
    .vc-card {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        background: #ffffff;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    
    .vc-card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--line);
        padding: 1rem 1.25rem;
    }

    /* Badges */
    .badge-action {
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .badge-create { background: #dcfce7; color: #166534; }
    .badge-update { background: #fef08a; color: #854d0e; }
    .badge-delete { background: #fee2e2; color: #991b1b; }
    .badge-default{ background: var(--bg-soft); color: var(--muted); }

    /* Input & Table */
    .audit-container .input-group {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
    }
    .audit-container .input-group:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(206, 24, 30, 0.1);
    }
    .audit-container .form-control {
        border: none !important;
        box-shadow: none !important;
    }
    .table-breeze th {
        background-color: var(--bg-soft);
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1rem;
    }
    .table-breeze td {
        vertical-align: middle;
        padding: 1rem;
        font-size: 0.875rem;
        color: var(--ink);
    }
    .log-desc {
        max-width: 350px;
        white-space: normal;
        word-wrap: break-word;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y audit-container">
    
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--ink);">System Audit Logs</h4>
            <span class="text-muted small">Track user activities, creations, and deletions across the POS system.</span>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-danger fw-bold shadow-sm" id="btn-clear-logs">
                <i class="bx bx-trash me-1"></i> Clear All Logs
            </button>
            <form id="clear-logs-form" action="{{ route('audit-logs.clear') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 4px solid #16a34a !important;" role="alert">
            <i class="bx bx-check-circle text-success me-2 fs-5"></i>
            <span class="fw-semibold text-dark">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Box -->
    <div class="vc-card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('audit-logs.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="bx bx-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by module, action, or description..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary fw-semibold px-4" style="background-color: #0f172a; border-color: #0f172a;">Search</button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-light border px-3"><i class="bx bx-refresh"></i></a>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="vc-card">
        <div class="vc-card-header d-flex align-items-center gap-2">
            <i class="bx bx-history fs-5 text-muted"></i>
            <span class="fw-bold" style="color: var(--ink);">Activity History</span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-breeze table-borderless border-top mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="160">Date & Time</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody class="border-top">
                    @forelse($logs as $log)
                    <tr class="border-bottom">
                        <td class="text-muted fw-semibold">
                            {{ $log->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="fw-bold text-dark">
                            <i class="bx bx-user-circle text-muted me-1"></i> 
                            {{ $log->user ? $log->user->username : 'System / Deleted User' }}
                        </td>
                        <td class="text-muted fw-semibold">{{ $log->module }}</td>
                        <td>
                            @php
                                $badgeClass = 'badge-default';
                                if(strtolower($log->action) == 'create') $badgeClass = 'badge-create';
                                if(strtolower($log->action) == 'update') $badgeClass = 'badge-update';
                                if(strtolower($log->action) == 'delete') $badgeClass = 'badge-delete';
                            @endphp
                            <span class="badge-action {{ $badgeClass }}">{{ $log->action }}</span>
                        </td>
                        <td class="log-desc text-muted">{{ $log->description }}</td>
                        <td>
                            <span class="badge bg-light text-muted border font-monospace">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="rounded-circle p-3 mb-2" style="background: var(--bg-soft); color: var(--muted);">
                                    <i class="bx bx-history fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No audit logs found.</h6>
                                <p class="text-muted small">System activities will be recorded here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#btn-clear-logs').click(function() {
        Swal.fire({
            title: 'Clear All Audit Logs?',
            text: "This action cannot be undone. All activity history will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bx bx-trash me-1"></i> Yes, clear them all',
            customClass: {
                title: 'fw-bold',
                popup: 'rounded-4 shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#clear-logs-form').submit();
            }
        });
    });
</script>
@endsection