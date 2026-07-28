@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* ================= PROFESSIONAL UI SYSTEM ================= */
    :root {
        --brand-red: #CE181E;
        --brand-blue: #002663;
        --brand-emerald: #10b981;
        --text-heading: #0f172a;
        --text-body: #475569;
        --text-muted: #64748b;
        --bg-main: #f8fafc;
        --border-color: #cbd5e1;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: var(--bg-main); color: var(--text-body); margin: 0; padding: 0; }

    .content-fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* NAVBAR & TILES */
    .nav-command-bar { background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .nav-title { font-size: 1.15rem; font-weight: 800; color: var(--text-heading); display: flex; align-items: center; gap: 10px; }
    .nav-title i { color: var(--brand-blue); font-size: 1.3rem; }

    /* STAT CARDS */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 20px; }
    .stat-card { background: #ffffff; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1rem; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--brand-blue); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; }
    .icon-total { background: linear-gradient(135deg, #1e293b, #334155); }
    .icon-active { background: linear-gradient(135deg, #10b981, #059669); }
    .icon-inactive { background: linear-gradient(135deg, var(--brand-red), #991b1b); }
    .stat-content h3 { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin: 0 0 4px 0; }
    .stat-content .number { font-size: 1.5rem; font-weight: 800; color: var(--text-heading); line-height: 1; }

    /* FILTERS */
    .filter-cockpit { background: #ffffff; border-radius: 14px; padding: 15px; margin-bottom: 20px; border: 1px solid var(--border-color); }
    .form-control-modern { border-radius: 8px !important; border: 1.5px solid var(--border-color) !important; padding: 8px 12px !important; font-size: 0.85rem; font-weight: 600; }
    .form-control-modern:focus { border-color: var(--brand-blue) !important; box-shadow: 0 0 0 3px rgba(0, 38, 99, 0.1) !important; }

    /* COMPACT DATA TABLE */
    .table-container { background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
    #posts { border-collapse: collapse; width: 100%; table-layout: auto; }
    #posts thead th { background: #f1f5f9; color: #334155; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px; border: 1px solid #cbd5e1; white-space: nowrap; text-align: left; }
    #posts tbody td { padding: 8px 12px; vertical-align: middle; font-size: 0.8rem; font-weight: 600; color: var(--text-body); border: 1px solid #e2e8f0; white-space: nowrap; }
    #posts tbody tr:hover { background-color: #f8fafc; }
    
    td.text-wrap-col { white-space: normal !important; min-width: 150px; max-width: 250px; word-wrap: break-word; }

    .badge-modern { padding: 4px 8px; border-radius: 6px; font-weight: 800; font-size: 0.65rem; text-transform: uppercase; }
    .bg-available { background: #ecfdf5; color: #059669; border: 1px solid #10b98133; }
    .bg-sold { background: #fef2f2; color: #dc2626; border: 1px solid #ef444433; }
    .vin-tag { font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--brand-blue); background: #eff6ff; padding: 3px 6px; border-radius: 4px; border: 1px solid #bfdbfe; }

    /* BUTTONS */
    .btn-action { width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); background: #fff; color: #64748b; font-size: 0.8rem; transition: 0.2s;}
    .btn-action:hover { background: var(--brand-blue); color: #fff; border-color: var(--brand-blue); }
    .btn-action.btn-delete:hover { background: var(--brand-red); border-color: var(--brand-red); color: white;}
    
    .btn-primary-solid { background: var(--brand-blue); color: white; border: none; border-radius: 8px; font-weight: 700; padding: 8px 16px; font-size: 0.85rem; text-decoration: none; transition: 0.3s;}
    .btn-primary-solid:hover { background: #0f4492; color: white; }

    /* MODAL */
    .modal-content-clean { border-radius: 16px; border: none; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2); }
    .modal-header-clean { border-bottom: 1px solid #f1f5f9; padding: 15px 20px; }
    .modal-body-clean { padding: 20px; }
    .modal-footer-clean { border-top: 1px solid #f1f5f9; padding: 15px 20px; background: #f8fafc; border-radius: 0 0 16px 16px; }

    /* 🌟 បន្ថែម CSS សម្រាប់ Loading Spinner */
    .import-loader { display: none; }
    .spinner-active .import-loader { display: inline-block; }
    .spinner-active .btn-text { display: none; }
</style>

<div class="content-fluid-wrapper">
    
    <div class="nav-command-bar">
        <div class="nav-title"><i class="fa-solid fa-truck-ramp-box"></i> Logistics Hub</div>
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-outline-dark fw-bold rounded-2 px-3 py-2 d-flex align-items-center gap-2" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa-solid fa-file-csv"></i> Bulk Import
            </button>
            <a href="{{ url('logistics/create') }}" class="btn-primary-solid d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Entry
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="stat-card">
            <div class="stat-icon icon-total"><i class="fa-solid fa-layer-group"></i></div>
            <div class="stat-content"><h3>Total Fleet</h3><div class="number text-dark">{{ $countLogistic ?? $logistics->total() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-active"><i class="fa-solid fa-car-side"></i></div>
            <div class="stat-content"><h3>Available Units</h3><div class="number text-success">{{ $countInStock ?? $logistics->where('is_sale', '1')->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-inactive"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="stat-content"><h3>Sold Pipeline</h3><div class="number text-danger">{{ $countSold ?? $logistics->where('is_sale', '0')->count() }}</div></div>
        </div>
    </div>

    <div class="filter-cockpit shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-xl-7">
                <form id="filterForm" method="GET" action="{{ url('logistics') }}" class="d-flex align-items-center gap-2">
                    <label class="fw-800 text-dark text-nowrap mb-0" style="font-size: 0.85rem;">Status:</label>
                    <select name="isSale" class="form-control form-control-modern" style="width: 140px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('isSale') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ request('isSale') == '0' ? 'selected' : '' }}>Sold</option>
                    </select>

                    <label class="fw-800 text-dark text-nowrap mb-0 ms-2" style="font-size: 0.85rem;">Date:</label>
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-modern" type="date" name="date_from" value="{{ $date_from ?? '' }}">
                        <span class="input-group-text bg-transparent border-0 fw-bold">to</span>
                        <input class="form-control form-control-modern" type="date" name="date_to" value="{{ $date_to ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-dark fw-bold rounded-2 px-3" style="font-size: 0.85rem;">Filter</button>
                    <button type="button" id="exportBtn" class="btn btn-outline-danger fw-bold rounded-2 px-3" style="font-size: 0.85rem;">Export</button>
                </form>
            </div>
            <div class="col-xl-5">
                <form action="{{ route('search.logistics') }}" method="GET">
                    @csrf
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control form-control-modern border-start-0" placeholder="Search VIN, Engine, or Model..." name="keyword" value="{{ request('keyword') }}">
                        @if (request('keyword'))
                            <a class="btn btn-light border-top border-bottom px-2 d-flex align-items-center" href="{{ url('logistics') }}"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                        <button type="submit" class="btn-primary-solid rounded-start-0 m-0" style="border-radius: 0 8px 8px 0;">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table id="posts" class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Model Name</th>
                        <th>Color Specs</th>
                        <th class="text-center">Year</th>
                        <th>Identification (VIN/Engine)</th>
                        <th class="text-center">Status</th>
                        <th>Remarks</th>
                        <th class="text-center" style="width: 90px; position: sticky; right: 0; background: #f1f5f9; z-index: 2;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logistics as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-800">{{ $logistics->firstItem() + $index }}</td>
                            
                            <td>
                                {{ $item->carModel ? $item->carModel->model_name : 'N/A' }}
                            </td>

                            <td>
                                <span class="d-block fw-700 text-dark"><i class="fa-solid fa-fill-drip text-muted me-1"></i> Ext: {{ $item->exterior_color }}</span>
                                <span class="d-block small text-muted fw-600 mt-1">Int: {{ $item->interior_color }}</span>
                            </td>

                            <td class="text-center fw-800 text-primary">{{ $item->model_year }}</td>

                            <td>
                                <span class="vin-tag d-inline-block mb-1">{{ $item->vin_number }}</span>
                                <div class="small fw-700 text-muted"><i class="fa-solid fa-gear me-1"></i> {{ $item->engine_number }}</div>
                            </td>

                            <td class="text-center">
                                @if ($item->is_sale == '1')
                                    <span class="badge-modern bg-available">Available</span>
                                @else
                                    <span class="badge-modern bg-sold">Sold</span>
                                @endif
                            </td>

                            <td class="text-wrap-col">
                                @if($item->remark)
                                    <div class="small fw-600 text-muted fst-italic">{{ $item->remark }}</div>
                                @else
                                    <span class="small text-muted fst-italic opacity-50">---</span>
                                @endif
                            </td>

                            <td class="text-center" style="position: sticky; right: 0; background: #ffffff; border-left: 2px solid #e2e8f0; z-index: 1;">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ url('/logistics/edit', encrypt($item->id)) }}" class="btn-action" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <button type="button" class="btn-action btn-delete" title="Delete" onclick="confirmDelete('{{ url('/logistics/destroy', encrypt($item->id)) }}')">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 fw-bold text-muted">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between align-items-center px-2">
        <span class="text-muted small fw-800 text-uppercase" style="letter-spacing: 0.5px;">Entry {{ $logistics->firstItem() }} - {{ $logistics->lastItem() }} of {{ $logistics->total() }}</span>
        <div>{{ $logistics->appends(request()->input())->links('paginitaion_backend') }}</div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-clean">
            <div class="modal-header modal-header-clean">
                <h6 class="modal-title fw-800 text-dark"><i class="fa-solid fa-file-csv text-success me-2 fs-5"></i> Bulk Import Logistics</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="importForm" action="{{ url('/logistics/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body modal-body-clean">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="fw-700 text-dark mb-0" style="font-size: 0.85rem;">Upload Excel/CSV <span class="text-danger">*</span></label>
                        <a href="{{ url('/download-template-logistic') }}" class="btn btn-sm text-primary fw-bold" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 4px 10px; font-size: 0.8rem; text-decoration: none;">
                            <i class="fa-solid fa-download me-1"></i> Template
                        </a>
                    </div>
                    <div class="file-upload-box position-relative" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px 15px; text-align: center; background: #f8fafc;">
                        <i class="fa-solid fa-cloud-arrow-up text-muted mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.85rem;">Click or drag file here</h6>
                        <p class="text-muted fw-600 mb-0" style="font-size: 0.75rem;">Support .csv, .xls, .xlsx</p>
                        <input class="form-control position-absolute top-0 start-0 w-100 h-100" type="file" name="file" accept=".xlsx, .xls, .csv" required style="opacity: 0; cursor: pointer;">
                    </div>
                </div>
                <div class="modal-footer modal-footer-clean justify-content-end gap-2">
                    <button type="button" class="btn btn-light fw-bold px-3 py-1" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.85rem;">Cancel</button>
                    
                    <button type="submit" id="btnStartImport" class="btn-primary-solid px-3 py-1 d-flex align-items-center gap-2" style="background: var(--brand-emerald);">
                        <span class="import-loader spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="btn-text"><i class="fa-solid fa-check-circle me-1"></i> Start Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
    @endif
    
    // បង្ហាញ Error ធម្មតា (បើមានបញ្ហាផ្សេងៗ)
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ session('error') }}" });
    @endif

    // បង្ហាញ Validation Errors (បើខុសកូឡុំ ឬទិន្នន័យចាំបាច់)
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Import Error',
            html: `
                <ul class="text-start mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `
        });
    @endif

    function confirmDelete(url) {
        Swal.fire({
            title: 'Confirm Deletion',
            text: "Are you sure you want to delete this logistics record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            customClass: { confirmButton: 'rounded-2', cancelButton: 'rounded-2' }
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

    // 🌟 មុខងារ Loading ពេលចុច Import
    document.getElementById('importForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnStartImport');
        btn.classList.add('spinner-active'); // បង្ហាញ Spinner និងលាក់ Text
        btn.setAttribute('disabled', 'disabled'); // Disable ប៊ូតុងកុំឱ្យចុចស្ទួន
    });
</script>

@endsection