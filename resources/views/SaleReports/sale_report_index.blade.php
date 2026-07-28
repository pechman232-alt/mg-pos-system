@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* ================= PROFESSIONAL UI SYSTEM ================= */
    :root { --brand-red: #CE181E; --text-heading: #0f172a; --text-body: #475569; --bg-main: #f8fafc; --border-color: #cbd5e1; }
    body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: var(--bg-main); color: var(--text-body); margin: 0; padding: 0; }

    .content-fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* NAVBAR & TILES */
    .nav-command-bar { background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 12px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .nav-title { font-size: 1.15rem; font-weight: 800; color: var(--text-heading); display: flex; align-items: center; gap: 10px; }
    .nav-title i { color: var(--brand-red); font-size: 1.3rem; }

    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 20px; }
    .stat-card { background: #ffffff; border-radius: 14px; padding: 1rem 1.25rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1rem; transition: 0.3s; }
    .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; }
    .icon-total { background: linear-gradient(135deg, #1e293b, #334155); }
    .icon-active { background: linear-gradient(135deg, #10b981, #059669); }
    .icon-inactive { background: linear-gradient(135deg, var(--brand-red), #991b1b); }
    .icon-today { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .stat-content h3 { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin: 0 0 4px 0; }
    .stat-content .number { font-size: 1.3rem; font-weight: 800; color: var(--text-heading); line-height: 1; }

    /* FILTERS */
    .filter-cockpit { background: #ffffff; border-radius: 14px; padding: 15px; margin-bottom: 20px; border: 1px solid var(--border-color); }
    .form-control-modern { border-radius: 8px !important; border: 1.5px solid var(--border-color) !important; padding: 8px 12px !important; font-size: 0.85rem; font-weight: 600; }
    .form-control-modern:focus { border-color: var(--brand-red) !important; box-shadow: 0 0 0 3px rgba(206, 24, 30, 0.1) !important; }

    /* TABLES */
    .table-container { background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
    #posts { border-collapse: collapse; width: 100%; table-layout: auto; }
    #posts thead th { background: #f1f5f9; color: #334155; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px; border: 1px solid #cbd5e1; white-space: nowrap; text-align: left; }
    #posts tbody td { padding: 8px 12px; vertical-align: middle; font-size: 0.8rem; font-weight: 600; color: var(--text-body); border: 1px solid #e2e8f0; white-space: nowrap; }
    #posts tbody tr:hover { background-color: #f8fafc; }
    td.text-wrap-col { white-space: normal !important; min-width: 200px; max-width: 280px; word-wrap: break-word; }

    .badge-modern { padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 0.65rem; text-transform: uppercase; }
    .bg-active { background: #ecfdf5; color: #059669; border: 1px solid #10b98133; }
    .bg-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #ef444433; }
    .invoice-tag { font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--brand-red); background: #fff5f5; padding: 3px 6px; border-radius: 4px; border: 1px solid #fecaca; }
    .mono-font { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #0f172a; }

    /* BUTTONS */
    .btn-action { width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); background: #fff; color: #64748b; font-size: 0.8rem; }
    .btn-action:hover { background: var(--brand-red); color: #fff; border-color: var(--brand-red); }
    .btn-primary-solid { background: var(--brand-red); color: white; border: none; border-radius: 8px; font-weight: 700; padding: 8px 16px; font-size: 0.85rem; text-decoration: none; }
    .btn-primary-solid:hover { background: #b91c1c; color: white; }
    .btn-success-solid { background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 700; padding: 8px 20px; font-size: 0.85rem; }
    
    /* 🌟 Dashboard Button Style */
    .btn-dashboard { background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 700; padding: 8px 16px; font-size: 0.85rem; text-decoration: none; transition: 0.3s; }
    .btn-dashboard:hover { background: #1e293b; color: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .btn-dashboard i { color: #fbbf24; }

    /* MODAL */
    .modal-content-clean { border-radius: 16px; border: none; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2); }
    .modal-header-clean { border-bottom: 1px solid #f1f5f9; padding: 15px 20px; }
    .modal-body-clean { padding: 20px; }
    .modal-footer-clean { border-top: 1px solid #f1f5f9; padding: 15px 20px; background: #f8fafc; border-radius: 0 0 16px 16px; }
    .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 9999; display: none; align-items: center; justify-content: center; flex-direction: column; }
    .spinner-border { width: 3rem; height: 3rem; border-width: 0.25em; color: var(--brand-red); }
</style>

<div class="loading-overlay" id="globalLoading">
    <div class="spinner-border" role="status"></div>
    <h5 class="mt-3 fw-800 text-dark">កំពុងដំណើរការទិន្នន័យ...</h5>
</div>

<div class="content-fluid-wrapper">
    <div class="nav-command-bar">
        <div class="nav-title"><i class="fa-solid fa-layer-group"></i> Sale Reports Directory</div>
        <div class="d-flex gap-2 align-items-center">
            
            <a href="{{ url('sale-reports/dashboard') }}" class="btn-dashboard d-flex align-items-center gap-2">
                <i class="fa-solid fa-chart-pie"></i> Analytics Dashboard
            </a>
            
            <div class="vr mx-1" style="opacity: 0.2; width: 2px; background-color: #cbd5e1;"></div>

            <button type="button" class="btn btn-outline-dark fw-bold rounded-2 px-3 py-2 d-flex align-items-center gap-2" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa-solid fa-file-csv"></i> Import CSV
            </button>
            <a href="{{ url('sale-reports/create') }}" class="btn-primary-solid d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-plus"></i> New Report
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="stat-card"><div class="stat-icon icon-total"><i class="fa-solid fa-file-invoice"></i></div><div class="stat-content"><h3>Total Reports</h3><div class="number">{{ $countSaleReports }}</div></div></div>
        <div class="stat-card"><div class="stat-icon icon-active"><i class="fa-solid fa-check-double"></i></div><div class="stat-content"><h3>Active</h3><div class="number">{{ $saleReports->where('status', 1)->count() }}</div></div></div>
        <div class="stat-card"><div class="stat-icon icon-inactive"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="stat-content"><h3>Inactive</h3><div class="number">{{ $saleReports->where('status', 0)->count() }}</div></div></div>
        <div class="stat-card"><div class="stat-icon icon-today"><i class="fa-solid fa-calendar-day"></i></div><div class="stat-content"><h3>Today</h3><div class="number">{{ $saleReports->where('created_at', '>=', now()->startOfDay())->count() }}</div></div></div>
    </div>

    <div class="filter-cockpit shadow-sm">
        <div class="row align-items-center g-3">
            <div class="col-xl-6">
                <form id="filterForm" method="GET" action="{{ url('sale-reports') }}" class="d-flex align-items-center gap-2">
                    <label class="fw-800 text-dark text-nowrap mb-0" style="font-size: 0.85rem;">Date Node:</label>
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-modern" type="date" id="date_from" name="date_from" value="{{ $date_from ?? '' }}">
                        <span class="input-group-text bg-transparent border-0 fw-bold">to</span>
                        <input class="form-control form-control-modern" type="date" id="date_to" name="date_to" value="{{ $date_to ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-dark fw-bold rounded-2 px-3" style="font-size: 0.85rem;">Filter</button>
                    <button type="button" id="exportBtn" class="btn btn-outline-danger fw-bold rounded-2 px-3" style="font-size: 0.85rem;">Excel</button>
                </form>
            </div>
            <div class="col-xl-6">
                <form action="{{ route('search.sale-reports') ?? url('sale-reports') }}" method="GET">
                    @csrf
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control form-control-modern border-start-0" placeholder="Search by customer, phone, or invoice..." name="keyword" value="{{ request()->input('keyword') }}">
                        @if (request()->input('keyword'))
                            <a class="btn btn-light border-top border-bottom px-2 d-flex align-items-center" href="{{ url('sale-reports') }}"><i class="fa-solid fa-xmark"></i></a>
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
                        <th class="text-center" style="width: 30px;">#</th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                        <th>Source</th>
                        <th>Brand</th>
                        <th>Branch</th>
                        <th>Sales Person</th>
                        <th>Contract No</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Delivery Date</th>
                        <th>Feedback Comment</th>
                        <th>SA Remark</th>
                        <th class="text-center" style="width: 70px;">Status</th>
                        <th class="text-center" style="width: 140px; position: sticky; right: 0; background: #f1f5f9; z-index: 2;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saleReports as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-800">{{ $saleReports->firstItem() + $index }}</td>
                            <td class="fw-800 text-dark">{{ $item->customer_name }}</td>
                            <td class="text-primary fw-700">{{ $item->phone_number }}</td>
                            <td>{{ $item->source_id ?? 'N/A' }}</td>
                            <td class="text-danger fw-800">{{ $item->brand_name ?? 'MG' }}</td>
                            <td class="text-muted fw-700">{{ $item->branch->branch_name ?? $item->branch_name ?? 'N/A' }}</td>
                            <td>{{ $item->employee->full_name ?? $item->employee_name ?? 'N/A' }}</td>
                            <td class="mono-font">{{ $item->contract_no ?? 'N/A' }}</td>
                            <td><span class="invoice-tag">#{{ $item->invoice_no }}</span></td>
                            
                            <td>{{ $item->invoice_date ? \Carbon\Carbon::parse($item->invoice_date)->format('d-M-Y') : 'N/A' }}</td>
                            <td class="text-success fw-700">{{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->format('d-M-Y') : 'N/A' }}</td>

                            <td class="text-wrap-col">
                                @if($item->feedback_command && $item->feedback_command !== 'N/A')
                                    <div class="small fw-700 text-dark" style="white-space: pre-line;">{{ $item->feedback_command }}</div>
                                    <div class="small text-muted fw-800 mt-1" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-user-pen me-1 text-warning"></i> Update by: <span class="text-dark">{{ $item->created_by ?? 'System Admin' }}</span>
                                    </div>
                                @else
                                    <span class="small text-muted fst-italic opacity-50">No feedback</span>
                                @endif
                            </td>

                            <td class="text-wrap-col">
                                @if($item->remark_sa && $item->remark_sa !== 'N/A')
                                    <div class="small fw-700 text-dark" style="white-space: pre-line;">{{ $item->remark_sa }}</div>
                                    <div class="small text-muted fw-800 mt-1" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-user-pen me-1 text-primary"></i> Update by: <span class="text-dark">{{ $item->created_by ?? 'System Admin' }}</span>
                                    </div>
                                @else
                                    <span class="small text-muted fst-italic opacity-50">No remark</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($item->status == 1) <span class="badge-modern bg-active">Active</span>
                                @else <span class="badge-modern bg-inactive">Inactive</span> @endif
                            </td>

                            <td class="text-center" style="position: sticky; right: 0; background: #ffffff; border-left: 2px solid #e2e8f0; z-index: 1;">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn-action edit-feedback-btn" 
                                            style="color: #d97706; border-color: #fde68a; background: #fffbeb;"
                                            data-id="{{ encrypt($item->id) }}" data-feedback="{{ $item->feedback_command }}" title="Add Feedback">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </button>
                                    <button type="button" class="btn-action edit-remark-btn" 
                                            style="color: #0ea5e9; border-color: #bae6fd; background: #f0f9ff;"
                                            data-id="{{ encrypt($item->id) }}" data-remark="{{ $item->remark_sa }}" title="Add Remark">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                    <a href="{{ url('sale-reports/view', encrypt($item->id)) }}" class="btn-action" title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ url('sale-reports/edit', encrypt($item->id)) }}" class="btn-action" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between align-items-center px-2">
        <span class="text-muted small fw-800 text-uppercase" style="letter-spacing: 0.5px;">Entry {{ $saleReports->firstItem() }} - {{ $saleReports->lastItem() }} of {{ $countSaleReports }}</span>
        <div>{{ $saleReports->appends(request()->input())->links('paginitaion_backend') }}</div>
    </div>
</div>

<div class="modal fade" id="quickFeedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-clean">
            <div class="modal-header modal-header-clean bg-light border-bottom-0 pb-3">
                <h6 class="modal-title fw-800 text-dark d-flex align-items-center gap-2">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;"><i class="fa-solid fa-bullhorn fs-6"></i></div>
                    បន្ថែមមតិយោបល់ (Add Feedback)
                </h6>
                <button type="button" class="btn-close shadow-sm" data-bs-dismiss="modal" aria-label="Close" style="background-color: white;"></button>
            </div>
            <form id="quickFeedbackForm">
                @csrf
                <input type="hidden" id="feedback_report_id" name="report_id">
                <div class="modal-body modal-body-clean pt-2">
                    <div id="existing_feedback_container" class="mb-3 d-none">
                        <label class="fw-800 text-muted small mb-1">ប្រវត្តិមតិយោបល់ (History) :</label>
                        <div id="existing_feedback" class="p-3 bg-light rounded-2 border" style="font-size: 0.85rem; white-space: pre-line; max-height: 150px; overflow-y: auto; color: #475569;"></div>
                    </div>
                    <div class="form-group">
                        <label class="fw-800 text-dark small mb-1">បន្ថែមមតិយោបល់ថ្មី (New Record) <span class="text-danger">*</span> :</label>
                        <textarea id="feedback_content" name="feedback_command" class="form-control form-control-modern" rows="3" placeholder="វាយបញ្ចូលទីនេះ..." required style="font-size: 0.85rem; resize: none; background: #ffffff; border-color: #94a3b8;"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-clean justify-content-end gap-2 pt-0 border-top-0">
                    <button type="button" class="btn btn-light fw-bold px-3 py-1" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.85rem;">បោះបង់</button>
                    <button type="submit" class="btn-primary-solid px-3 py-1" id="btnSaveFeedback"><i class="fa-solid fa-paper-plane me-1"></i> បញ្ជូន</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="quickRemarkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-clean">
            <div class="modal-header modal-header-clean bg-light border-bottom-0 pb-3">
                <h6 class="modal-title fw-800 text-dark d-flex align-items-center gap-2">
                    <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;"><i class="fa-regular fa-comment-dots fs-6"></i></div>
                    បន្ថែមការកត់សម្គាល់ (Add Remark)
                </h6>
                <button type="button" class="btn-close shadow-sm" data-bs-dismiss="modal" aria-label="Close" style="background-color: white;"></button>
            </div>
            <form id="quickRemarkForm">
                @csrf
                <input type="hidden" id="remark_report_id" name="report_id">
                <div class="modal-body modal-body-clean pt-2">
                    <div id="existing_remarks_container" class="mb-3 d-none">
                        <label class="fw-800 text-muted small mb-1">ប្រវត្តិការកត់សម្គាល់ (History) :</label>
                        <div id="existing_remarks" class="p-3 bg-light rounded-2 border" style="font-size: 0.85rem; white-space: pre-line; max-height: 150px; overflow-y: auto; color: #475569;"></div>
                    </div>
                    <div class="form-group">
                        <label class="fw-800 text-dark small mb-1">បន្ថែមការកត់សម្គាល់ថ្មី (New Record) <span class="text-danger">*</span> :</label>
                        <textarea id="remark_content" name="remark_sa" class="form-control form-control-modern" rows="3" placeholder="វាយបញ្ចូលទីនេះ..." required style="font-size: 0.85rem; resize: none; background: #ffffff; border-color: #94a3b8;"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-clean justify-content-end gap-2 pt-0 border-top-0">
                    <button type="button" class="btn btn-light fw-bold px-3 py-1" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.85rem;">បោះបង់</button>
                    <button type="submit" class="btn-primary-solid px-3 py-1" id="btnSaveRemark"><i class="fa-solid fa-paper-plane me-1"></i> បញ្ជូន</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-clean">
            <div class="modal-header modal-header-clean">
                <h6 class="modal-title fw-800 text-dark"><i class="fa-solid fa-file-csv text-success me-2 fs-5"></i> Import Sale Reports</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importFormAjax" action="{{ url('sale-reports/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body modal-body-clean">
                    <div id="importErrors" class="mb-3 d-none"><div class="error-list-box text-danger small fw-600" id="errorContent"></div></div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="fw-700 text-dark mb-0" style="font-size: 0.85rem;">Upload CSV/Excel <span class="text-danger">*</span></label>
                        <a href="{{ url('sale-reports/download-template') }}" class="btn btn-sm text-primary fw-bold" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 4px 10px; font-size: 0.8rem; text-decoration: none;"><i class="fa-solid fa-download me-1"></i> Template</a>
                    </div>
                    <div class="file-upload-box position-relative" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px 15px; text-align: center; background: #f8fafc;">
                        <i class="fa-solid fa-cloud-arrow-up text-muted mb-2" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-800 text-dark mb-1" style="font-size: 0.85rem;">Click or drag file here</h6>
                        <p class="text-muted fw-600 mb-0" style="font-size: 0.75rem;">Support .csv, .xls, .xlsx</p>
                        <input class="form-control position-absolute top-0 start-0 w-100 h-100" type="file" name="import_file" id="import_file" accept=".xlsx, .xls, .csv" required style="opacity: 0; cursor: pointer;">
                    </div>
                </div>
                <div class="modal-footer modal-footer-clean justify-content-end gap-2">
                    <button type="button" class="btn btn-light fw-bold px-3 py-1" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 0.85rem;">Cancel</button>
                    <button type="submit" class="btn-success-solid px-3 py-1" id="submitImport"><i class="fa-solid fa-check-circle me-2"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        document.getElementById('exportBtn').addEventListener('click', function() {
            const form = document.getElementById('filterForm');
            const oldAction = form.action;
            form.action = "{{ url('sale-reports/export') }}";
            form.submit();
            form.action = oldAction;
        });

        // QUICK FEEDBACK LOGIC
        $(document).on('click', '.edit-feedback-btn', function() {
            let id = $(this).data('id');
            let feedback = $(this).data('feedback');
            $('#feedback_report_id').val(id);
            if(feedback && feedback !== 'N/A') {
                $('#existing_feedback_container').removeClass('d-none');
                $('#existing_feedback').text(feedback);
            } else {
                $('#existing_feedback_container').addClass('d-none');
                $('#existing_feedback').text('');
            }
            $('#feedback_content').val(''); 
            let modal = new bootstrap.Modal(document.getElementById('quickFeedbackModal'));
            modal.show();
        });

        $('#quickFeedbackForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#feedback_report_id').val();
            let feedback = $('#feedback_content').val();
            let btn = $('#btnSaveFeedback');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> កំពុងបញ្ជូន...');
            
            $.ajax({
                url: "{{ route('sale-reports.quick-feedback') }}", 
                type: "POST",
                data: { _token: "{{ csrf_token() }}", report_id: id, feedback_command: feedback },
                success: function(res) {
                    $('#quickFeedbackModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'ជោគជ័យ!', text: 'មតិយោបល់ត្រូវបានបញ្ចូលរួចរាល់!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                },
                error: function(err) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> បញ្ជូន');
                    let errorMsg = err.responseJSON && err.responseJSON.error ? err.responseJSON.error : 'មានបញ្ហាក្នុងការរក្សាទុក!';
                    Swal.fire('បរាជ័យ', errorMsg, 'error');
                }
            });
        });

        // QUICK REMARK LOGIC
        $(document).on('click', '.edit-remark-btn', function() {
            let id = $(this).data('id');
            let remark = $(this).data('remark');
            $('#remark_report_id').val(id);
            if(remark && remark !== 'N/A') {
                $('#existing_remarks_container').removeClass('d-none');
                $('#existing_remarks').text(remark);
            } else {
                $('#existing_remarks_container').addClass('d-none');
                $('#existing_remarks').text('');
            }
            $('#remark_content').val(''); 
            let modal = new bootstrap.Modal(document.getElementById('quickRemarkModal'));
            modal.show();
        });

        $('#quickRemarkForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#remark_report_id').val();
            let remark = $('#remark_content').val();
            let btn = $('#btnSaveRemark');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> កំពុងបញ្ជូន...');
            
            $.ajax({
                url: "{{ route('sale-reports.quick-remark') }}", 
                type: "POST",
                data: { _token: "{{ csrf_token() }}", report_id: id, remark_sa: remark },
                success: function(res) {
                    $('#quickRemarkModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'ជោគជ័យ!', text: 'ការកត់សម្គាល់ត្រូវបានបញ្ចូលរួចរាល់!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                },
                error: function(err) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-paper-plane me-1"></i> បញ្ជូន');
                    let errorMsg = err.responseJSON && err.responseJSON.error ? err.responseJSON.error : 'មានបញ្ហាក្នុងការរក្សាទុក!';
                    Swal.fire('បរាជ័យ', errorMsg, 'error');
                }
            });
        });

        // IMPORT LOGIC
        $('#importFormAjax').on('submit', function(e) {
            e.preventDefault(); 
            let formData = new FormData(this);
            $('#globalLoading').css('display', 'flex');
            $('#importErrors').addClass('d-none');
            $('#submitImport').prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#globalLoading').hide();
                    Swal.fire({ icon: 'success', title: 'ជោគជ័យ!', text: 'ទិន្នន័យត្រូវបាន Import រួចរាល់', timer: 2000, showConfirmButton: false }).then(() => location.reload());
                },
                error: function(xhr) {
                    $('#globalLoading').hide();
                    $('#submitImport').prop('disabled', false);
                    let errorHtml = '<h6 class="fw-800 mb-2"><i class="fa-solid fa-triangle-exclamation"></i> រកឃើញកំហុសក្នុង File៖</h6><ul class="mb-0 ps-3">';
                    
                    if (xhr.status === 422 && xhr.responseJSON.errors) { 
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) { errorHtml += `<li>${val[0]}</li>`; });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorHtml += `<li>${xhr.responseJSON.message}</li>`;
                    } else {
                        errorHtml += `<li>មិនអាចភ្ជាប់ទៅ Server ទេ!</li>`;
                    }
                    errorHtml += '</ul>';

                    $('#errorContent').html(errorHtml);
                    $('#importErrors').removeClass('d-none');
                    Swal.fire({ icon: 'error', title: 'បរាជ័យ!', text: 'សូមពិនិត្យមើលសារកំហុស!' });
                }
            });
        });
    });
</script>
@endsection