    @extends('layouts.homePages')
    @section('content')

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ================= PROFESSIONAL UI SYSTEM ================= */
        :root {
            --brand-red: #CE181E;
            --brand-red-hover: #b91c1c;
            --text-heading: #0f172a;
            --text-body: #475569;
            --bg-main: #f8fafc;
            --border-color: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.9);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: var(--bg-main);
            color: var(--text-body);
            margin: 0;
            padding: 0;
        }

        /* 100% FIT SCREEN WRAPPER */
        .content-fluid-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.5rem !important;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

        /* MODERN NAVBAR */
        .nav-command-bar {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 15px 25px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .nav-title { font-size: 1.25rem; font-weight: 800; color: var(--text-heading); display: flex; align-items: center; gap: 12px; }
        .nav-title i { color: var(--brand-red); font-size: 1.5rem; }

        /* COMMAND SUMMARY TILES */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover { transform: translateY(-5px); border-color: var(--brand-red); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }

        .stat-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
        }
        .icon-total { background: linear-gradient(135deg, #1e293b, #334155); }
        .icon-active { background: linear-gradient(135deg, #10b981, #059669); }
        .icon-inactive { background: linear-gradient(135deg, var(--brand-red), #991b1b); }
        .icon-today { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }

        .stat-content h3 { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin: 0; }
        .stat-content .number { font-size: 1.75rem; font-weight: 800; color: var(--text-heading); line-height: 1.2; }

        /* PROFESSIONAL FILTERS */
        .filter-cockpit {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .form-control-modern {
            border-radius: 10px !important;
            border: 1.5px solid var(--border-color) !important;
            padding: 10px 16px !important;
            font-weight: 600;
            transition: 0.2s;
        }
        .form-control-modern:focus { border-color: var(--brand-red) !important; box-shadow: 0 0 0 4px rgba(206, 24, 30, 0.1) !important; }

        /* ENTERPRISE DATA TABLE */
        .table-container {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        #posts thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 18px 16px;
            border-bottom: 2px solid var(--border-color);
        }

        #posts tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-body);
        }
        #posts tbody tr:hover { background-color: #fbfcfe; }

        /* STATUS & ACTION PILLS */
        .badge-modern { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }
        .bg-active { background: #ecfdf5; color: #059669; border: 1px solid #10b98133; }
        .bg-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #ef444433; }

        .btn-action {
            width: 34px; height: 34px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: 0.2s; border: 1px solid var(--border-color);
            background: #fff; color: #64748b;
        }
        .btn-action:hover { background: var(--brand-red); color: #fff; border-color: var(--brand-red); transform: translateY(-2px); }

        .btn-primary-solid { background: var(--brand-red); color: white; border: none; border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.3s; }
        .btn-primary-solid:hover { background: var(--brand-red-hover); box-shadow: 0 10px 15px -3px rgba(206, 24, 30, 0.3); }

        .invoice-tag { font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--brand-red); background: #fff5f5; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; }
    </style>

    <div class="content-fluid-wrapper">
        
        <div class="nav-command-bar">
            <div class="nav-title">
                <i class="fa-solid fa-layer-group"></i> Sale Reports Directory
            </div>
            <a href="{{ url('sale-reports/create') }}" class="btn-primary-solid">
                <i class="fa-solid fa-circle-plus me-2"></i> New Report
            </a>
        </div>

        <div class="summary-grid">
            <div class="stat-card">
                <div class="stat-icon icon-total"><i class="fa-solid fa-file-invoice"></i></div>
                <div class="stat-content">
                    <h3>Total Reports</h3>
                    <div class="number">{{ $countSaleReports }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-active"><i class="fa-solid fa-check-double"></i></div>
                <div class="stat-content">
                    <h3>Active</h3>
                    <div class="number">{{ $saleReports->where('status', 1)->count() }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-inactive"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="stat-content">
                    <h3>Inactive</h3>
                    <div class="number">{{ $saleReports->where('status', 0)->count() }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-today"><i class="fa-solid fa-calendar-day"></i></div>
                <div class="stat-content">
                    <h3>Today</h3>
                    <div class="number">{{ $saleReports->where('created_at', '>=', now()->startOfDay())->count() }}</div>
                </div>
            </div>
        </div>

        <div class="filter-cockpit shadow-sm">
            <div class="row align-items-center g-3">
                <div class="col-xl-6">
                    <form id="filterForm" method="GET" action="{{ url('sale-reports') }}" class="d-flex align-items-center gap-2">
                        <label class="fw-800 text-dark text-nowrap mb-0">Date Node:</label>
                        <div class="input-group">
                            <input class="form-control form-control-modern" type="date" id="date_from" name="date_from" value="{{ $date_from }}">
                            <span class="input-group-text bg-transparent border-0 fw-bold">to</span>
                            <input class="form-control form-control-modern" type="date" id="date_to" name="date_to" value="{{ $date_to }}">
                        </div>
                        <button type="submit" class="btn btn-dark fw-bold rounded-3 px-4">Filter</button>
                        <button type="button" id="exportBtn" class="btn btn-outline-danger fw-bold rounded-3 px-4">Excel</button>
                    </form>
                </div>
                <div class="col-xl-6">
                    <form action="{{ route('search.sale-reports') }}" method="GET">
                        @csrf
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" class="form-control form-control-modern border-start-0" placeholder="Search by customer, phone, or invoice..." name="keyword" value="{{ request()->input('keyword') }}">
                            @if (request()->input('keyword'))
                                <a class="btn btn-light border-top border-bottom" href="{{ url('sale-reports') }}"><i class="fa-solid fa-xmark"></i></a>
                            @endif
                            <button type="submit" class="btn btn-primary-solid rounded-start-0">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-container shadow-sm">
            <div class="table-responsive text-nowrap">
                <table id="posts" class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Customer / Operator</th>
                            <th>Contact No</th>
                            <th>Gender</th>
                            <th>Invoice No</th>
                            <th>Sync Date</th>
                            <th>Brand Node</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saleReports as $index => $item)
                            <tr>
                                <td class="text-center text-muted fw-800">{{ $saleReports->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-800" style="width:34px; height:34px; font-size: 11px; border: 1px solid #eef2ff;">
                                            {{ strtoupper(substr($item->customer_name, 0, 1)) }}
                                        </div>
                                        <span class="fw-800 text-heading">{{ $item->customer_name }}</span>
                                    </div>
                                </td>
                                <td class="fw-700 text-primary">{{ $item->phone_number }}</td>
                                <td><span class="badge rounded-pill bg-light text-dark border">{{ $item->gender }}</span></td>
                                <td><span class="invoice-tag">#{{ $item->invoice_no }}</span></td>
                                <td class="text-muted small fw-700">{{ \Carbon\Carbon::parse($item->invoice_date)->format('d M, Y') }}</td>
                                <td><span class="badge bg-light text-info fw-800 px-3">{{ $item->brand_name }}</span></td>
                                <td class="text-center">
                                    @if ($item->status == 1)
                                        <span class="badge-modern bg-active"><i class="fa-solid fa-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge-modern bg-inactive"><i class="fa-solid fa-circle-xmark me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ url('sale-reports/view', encrypt($item->id)) }}" class="btn-action" title="View"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ url('sale-reports/edit', encrypt($item->id)) }}" class="btn-action" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <button type="button" class="btn-action text-danger delete-btn" data-customer-name="{{ $item->customer_name }}" data-delete-url="{{ url('sale-reports/destroy', encrypt($item->id)) }}" title="Delete"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-between align-items-center px-2">
            <span class="text-muted small fw-800 uppercase tracking-widest">Entry {{ $saleReports->firstItem() }} - {{ $saleReports->lastItem() }} of {{ $countSaleReports }}</span>
            <div>{{ $saleReports->appends(request()->input())->links('paginitaion_backend') }}</div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body text-center p-5">
                    <div class="mb-4 text-danger"><i class="fa-solid fa-circle-exclamation" style="font-size: 4.5rem;"></i></div>
                    <h3 class="fw-800 text-dark">Confirm Deletion</h3>
                    <p class="text-muted">You are about to permanently remove the report for:</p>
                    <div class="p-3 bg-light rounded-3 fw-800 text-danger border-start border-danger border-4 mb-4" id="deleteCustomerName"></div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                        <a href="#" id="confirmDeleteBtn" class="btn-primary-solid px-4">Delete Permanently</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DELETE MODAL LOGIC
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const deleteCustomerName = document.getElementById('deleteCustomerName');
            
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    deleteCustomerName.textContent = this.getAttribute('data-customer-name');
                    confirmDeleteBtn.href = this.getAttribute('data-delete-url');
                    deleteModal.show();
                });
            });

            // EXCEL EXPORT LOGIC
            document.getElementById('exportBtn').addEventListener('click', function() {
                const form = document.getElementById('filterForm');
                const oldAction = form.action;
                form.action = "{{ url('sale-reports/export') }}";
                form.submit();
                form.action = oldAction;
            });
        });
    </script>
    @endsection