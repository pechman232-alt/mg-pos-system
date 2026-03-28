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
        --brand-indigo: #4f46e5;    /* Primary */
        --brand-emerald: #10b981;   /* Success */
        --brand-amber: #f59e0b;     /* Warning */
        --brand-rose: #ef4444;      /* Danger/Sold */
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: #1e293b;
    }

    /* --- ULTRA-WIDE CONTAINER --- */
    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 15px 30px !important;
    }

    /* --- DASHBOARD CARDS --- */
    .dash-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        height: 110px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0 25px;
    }
    .dash-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); }
    
    .border-indigo { border-left: 5px solid var(--brand-indigo); }
    .border-emerald { border-left: 5px solid var(--brand-emerald); }
    .border-rose { border-left: 5px solid var(--brand-rose); }

    .dash-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }
    .dash-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }
    .dash-icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        opacity: 0.08;
        font-size: 4rem;
        transform: rotate(-15deg);
    }

    /* --- MAIN TABLE CARD --- */
    .main-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        background: #ffffff;
    }

    .card-header-main {
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 33%, #ec4899 66%, #ef4444 100%);
        padding: 20px 25px;
        border: none;
    }

    .table thead th {
        background-color: #f1f5f9;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px;
        border: none;
    }

    .table tbody td {
        padding: 16px 15px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Status Badges */
    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .bg-available { background-color: #ecfdf5; color: #059669; }
    .bg-sold { background-color: #fef2f2; color: #dc2626; }

    /* Action Buttons */
    .action-btn {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .action-btn:hover { transform: scale(1.1); }
    .btn-edit:hover { color: var(--brand-indigo); border-color: var(--brand-indigo); background: #f0f7ff; }
    .btn-delete:hover { color: var(--brand-rose); border-color: var(--brand-rose); background: #fef2f2; }

    .animate-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Search/Filter Inputs */
    .form-control, .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 15px;
        font-weight: 500;
        transition: all 0.2s;
        height: 42px;
    }
    .form-control:focus {
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
</style>

<div class="container-xxl">
    
    <div class="row mb-5 animate-up">
        <div class="col-md-4">
            <div class="card dash-card border-indigo">
                <div class="dash-title">Total Fleet</div>
                <div class="dash-value" style="color: var(--brand-indigo);">{{ $countLogistic ?? $logistics->total() }}</div>
                <i class='fa-solid fa-layer-group dash-icon-bg' style="color: var(--brand-indigo);"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dash-card border-emerald">
                <div class="dash-title">Available Units</div>
                <div class="dash-value" style="color: var(--brand-emerald);">{{ $countInStock ?? $logistics->where('is_sale', '1')->count() }}</div>
                <i class='fa-solid fa-car-side dash-icon-bg' style="color: var(--brand-emerald);"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card dash-card border-rose">
                <div class="dash-title">Sold Pipeline</div>
                <div class="dash-value" style="color: var(--brand-rose);">{{ $countSold ?? $logistics->where('is_sale', '0')->count() }}</div>
                <i class='fa-solid fa-cart-shopping dash-icon-bg' style="color: var(--brand-rose);"></i>
            </div>
        </div>
    </div>

    <div class="card main-card animate-up">
        <div class="card-header-main d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-white p-2 rounded-3 me-3">
                    <i class='fa-solid fa-truck-ramp-box' style="color: var(--brand-indigo); font-size: 1.5rem;"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-800 text-white" style="letter-spacing: -0.5px;">Logistics Hub</h5>
                    <small class="text-white-50">Enterprise Resource Management</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-white bg-white text-dark fw-bold px-3 border-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class='fa-solid fa-file-import me-1'></i> Import
                </button>
                <a href="{{ url('logistics/create') }}" class="btn btn-sm btn-dark fw-bold px-3 shadow-sm" style="background: #1e293b;">
                    <i class='fa-solid fa-plus me-1'></i> New Entry
                </a>
            </div>
        </div>

        <div class="bg-white p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-4">
            <form id="filterForm" method="GET" action="{{ url('logistics') }}" class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center">
                    <span class="me-2 fw-bold text-muted small">STATUS:</span>
                    <select name="isSale" class="form-select" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="0" {{ request('isSale') == '0' ? 'selected' : '' }}>Sold</option>
                        <option value="1" {{ request('isSale') == '1' ? 'selected' : '' }}>Available</option>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input class="form-control" type="date" name="date_from" value="{{ $date_from ?? '' }}">
                    <span class="text-muted small">to</span>
                    <input class="form-control" type="date" name="date_to" value="{{ $date_to ?? '' }}">
                </div>
                <button type="submit" class="btn btn-primary px-4" style="background: var(--brand-indigo); border:none;">Filter</button>
                <button type="button" id="exportBtn" class="btn btn-outline-danger px-4">Export</button>
            </form>

            <form action="{{ route('search.logistics') }}" method="GET">
                <div class="input-group" style="width: 400px;">
                    <span class="input-group-text bg-light border-end-0"><i class='fa-solid fa-magnifying-glass text-muted'></i></span>
                    <input type="text" class="form-control bg-light border-start-0" placeholder="Search VIN, Engine, or Model..." name="keyword" value="{{ request('keyword') }}">
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">Rank</th>
                        <th>Vehicle Description</th>
                        <th>Color Specs</th>
                        <th>Year</th>
                        <th>Identification (VIN/Engine)</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logistics as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $logistics->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-800 text-dark">{{ $item->model_name }}</div>
                                <div class="text-muted small">Class: Commercial</div>
                            </td>
                            <td>
                                <div class="fw-600">Ext: {{ $item->exterior_color }}</div>
                                <div class="text-muted small">Int: {{ $item->interior_color }}</div>
                            </td>
                            <td class="fw-bold">{{ $item->model_year }}</td>
                            <td class="text-nowrap">
                                <span class="badge bg-light text-primary border mb-1" style="font-family: monospace; font-size: 0.75rem;">{{ $item->vin_number }}</span>
                                <div class="text-muted small">{{ $item->engine_number }}</div>
                            </td>
                            <td>
                                <span class="badge-status {{ $item->is_sale == '0' ? 'bg-sold' : 'bg-available' }}">
                                    <i class='fa-solid fa-circle' style="font-size: 6px;"></i>
                                    {{ $item->is_sale == '0' ? 'SOLD' : 'AVAILABLE' }}
                                </span>
                            </td>
                            <td class="text-muted small" style="max-width: 200px;">{{ $item->remark ?: '---' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/logistics/edit', encrypt($item->id)) }}" class="action-btn btn-edit text-muted">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="action-btn btn-delete text-muted" onclick="confirmDelete('{{ url('/logistics/destroy', encrypt($item->id)) }}')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light border-top d-flex justify-content-between align-items-center p-4">
            <div class="text-muted fw-bold">
                Showing {{ $logistics->firstItem() }} - {{ $logistics->lastItem() }} of {{ $logistics->total() }} Entries
            </div>
            <div>
                {{ $logistics->appends(request()->input())->links('paginitaion_backend') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <form action="{{ url('/logistics/import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header p-4">
                    <h5 class="modal-title fw-800"><i class='fa-solid fa-file-excel me-2' style="color: #27ae60;"></i>Bulk Import</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <a href="{{ url('/download-template-logistic')}}" class="btn btn-outline-primary w-100 mb-4 fw-bold">
                        <i class='fa-solid fa-download me-1'></i> Download Excel Template
                    </a>
                    <label class="fw-bold text-muted small mb-2 uppercase">Select File (.xlsx, .csv)</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-sm btn-secondary fw-bold border-0" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-sm btn-primary px-4 fw-bold shadow-sm" style="background: var(--brand-indigo); border:none;">Start Import</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
    @endif

    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete this record?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete it'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    document.getElementById('exportBtn').addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        const oldAction = form.action;
        form.action = "{{ url('logistics/export') }}";
        form.submit();
        form.action = oldAction;
    });
</script>

@endsection