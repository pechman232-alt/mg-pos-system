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

    /* --- CARD & ANIMATIONS --- */
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
    .btn-add-company {
        background: rgba(255,255,255,0.2);
        color: white; border: 1px solid rgba(255,255,255,0.4);
        padding: 8px 18px; border-radius: 10px; font-weight: 700;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .btn-add-company:hover { background: #fff; color: var(--brand-purple); transform: translateY(-2px); }

    /* --- TABLE STYLING --- */
    .table thead th { 
        background: #f1f5f9; color: #475569; font-weight: 700; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px;
        padding: 15px 20px; border: none; text-align: center;
    }
    .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .company-name-text { font-weight: 800; color: #0f172a; font-size: 14px; }
    
    /* Status Badges */
    .badge-status {
        cursor: pointer;
        padding: 6px 14px; border-radius: 50px; font-weight: 800; font-size: 10px;
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }
    .status-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .status-active:hover { background: #059669; color: #fff; transform: translateY(-2px); }
    .status-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
    .status-inactive:hover { background: #dc2626; color: #fff; transform: translateY(-2px); }

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
</style>

<div class="container-xxl">

    @if(session('success'))
    <script>
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Action Successful', text: "{{ session('success') }}",
            showConfirmButton: false, timer: 3000, timerProgressBar: true
        });
    </script>
    @endif

    <div class="card-main">
        <div class="card-header-main">
            <h5 class="header-title">
                <i class='fas fa-buildings me-2'></i>ENTERPRISE ENTITY DIRECTORY
            </h5>
            <a href="{{ url('/companys/create') }}" class="btn btn-add-company">
                <i class='fas fa-plus-circle me-1'></i> Add New Company
            </a>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Rank</th>
                            <th style="text-align: left;">Company Legal Name</th>
                            <th>Operation Status</th>
                            <th>Entry Created</th>
                            <th style="width: 150px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companys as $index => $company)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            <td style="text-align: left;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 40px; height: 40px; background: #f0f4ff; color: var(--brand-indigo); display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.1rem;">
                                        <i class="fas fa-city"></i>
                                    </div>
                                    <div>
                                        <span class="d-block company-name-text">{{ $company->company_name }}</span>
                                        <small class="text-muted fw-bold">UUID: #{{ str_pad($company->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span onclick="toggleStatus('{{ encrypt($company->id) }}', '{{ $company->status }}')" 
                                      class="badge-status {{ $company->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                    <i class="fas {{ $company->status == 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $company->status }}
                                </span>
                            </td>
                            <td class="text-center text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $company->created_at->format('d M, Y') }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/companys/edit', encrypt($company->id)) }}" 
                                       class="action-btn" title="Edit Company">
                                        <i class="fas fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="action-btn btn-delete" 
                                            onclick="confirmDelete('{{ encrypt($company->id) }}', '{{ $company->company_name }}')">
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
    $(document).ready(function() {
        $('#datatable').DataTable({
            "pageLength": 15,
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            "language": {
                "search": "",
                "searchPlaceholder": "Search companies..."
            }
        });
    });

    function toggleStatus(id, currentStatus) {
        const nextStatus = currentStatus === 'active' ? 'inactive' : 'active';
        Swal.fire({
            title: 'Transition to ' + nextStatus.toUpperCase() + '?',
            text: "This changes the visibility and access rights of this company.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Confirm Update',
            cancelButtonText: 'Abort',
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/companys/toggle') }}/" + id;
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Delete ' + name + '?',
            html: 'Are you sure? This will permanently remove the record from the <b>Enterprise Entity Directory</b>.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Delete Record',
            cancelButtonText: 'Keep it',
            reverseButtons: true,
            fontFamily: 'Plus Jakarta Sans'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/companys/destroy') }}/" + id;
            }
        });
    }
</script>

@endsection