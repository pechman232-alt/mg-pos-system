@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { 
        --brand-indigo: #4f46e5; --brand-emerald: #10b981; --brand-rose: #f43f5e; --brand-amber: #f59e0b;
        --slate-50: #f8fafc; --slate-100: #f1f5f9; --slate-200: #e2e8f0; --slate-600: #475569; --slate-900: #0f172a;
    }

    body { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; background-color: #f8fafc; color: var(--slate-900); overflow-x: hidden; }
    
    /* ✨ Smooth Entry Animations */
    .summary-card, .filter-box, .card-main {
        animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .row > div:nth-child(1) .summary-card { animation-delay: 0.1s; }
    .row > div:nth-child(2) .summary-card { animation-delay: 0.2s; }
    .row > div:nth-child(3) .summary-card { animation-delay: 0.3s; }
    .row > div:nth-child(4) .summary-card { animation-delay: 0.4s; }
    .filter-box { animation-delay: 0.5s; }
    .card-main { animation-delay: 0.6s; }

    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 📊 KPI Cards Styling */
    .summary-card { background: #fff; border-radius: 20px; padding: 24px; border: 1px solid var(--slate-200); transition: all 0.3s ease; height: 100%; position: relative; }
    .summary-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); border-color: var(--brand-indigo); }
    .card-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 15px; box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
    .card-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--slate-600); }
    .card-value { font-size: 1.8rem; font-weight: 800; color: var(--slate-900); margin-top: 5px; }

    /* 🔍 Filter & Search Box */
    .filter-box { background: #fff; border-radius: 20px; padding: 25px; border: 1px solid var(--slate-200); margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .form-control-custom { border-radius: 12px; border: 1.5px solid var(--slate-200); padding: 10px 15px; font-size: 0.9rem; font-weight: 600; transition: 0.3s; }
    .form-control-custom:focus { border-color: var(--brand-indigo); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1); outline: none; transform: translateY(-1px); }

    /* 📋 Table Premium Styling */
    .card-main { border-radius: 24px; border: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04); background: #fff; padding: 25px; }
    .table-premium { border-collapse: separate; border-spacing: 0; width: 100% !important; border-radius: 16px; overflow: hidden; }
    .table-premium thead th { background: #f1f5f9; color: var(--slate-600); font-size: 0.7rem; text-transform: uppercase; padding: 18px; border: none; font-weight: 800; text-align: center; }
    .table-premium tbody tr { transition: all 0.3s ease; }
    .table-premium tbody tr:hover { background-color: #f8fafc !important; transform: scale(1.002); box-shadow: inset 4px 0 0 var(--brand-indigo); }
    .table-premium tbody td { padding: 16px; border-bottom: 1px solid var(--slate-100); vertical-align: middle; font-size: 0.88rem; }
    
    /* 🏷️ Status Badges */
    .badge-status { padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 0.65rem; display: inline-flex; align-items: center; gap: 8px; text-transform: uppercase; }
    .badge-status i { animation: pulseIcon 2s infinite; }
    @keyframes pulseIcon { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    
    .status-active { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .status-won { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-lost { background: #fff1f2; color: #9f1239; border: 1px solid #fecaca; }

    /* 🔘 Actions & Headers */
    .btn-action { width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; border: 1.5px solid var(--slate-200); transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: #fff; cursor: pointer; color: var(--slate-600); text-decoration: none; }
    .btn-action:hover { background: var(--slate-900); color: #fff !important; transform: translateY(-3px) scale(1.1); }
    .btn-header { font-weight: 800; border-radius: 14px; padding: 12px 24px; font-size: 0.85rem; border: none; display: flex; align-items: center; gap: 10px; transition: 0.3s; }
</style>

<div class="container-xxl py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-800 mb-1" style="letter-spacing: -1px;">PIPELINE INTELLIGENCE</h3>
            <p class="text-muted small mb-0"><i class="fas fa-satellite-dish text-success me-2"></i> Master Sales Dashboard</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('leads.export') }}" class="btn-header shadow-sm text-decoration-none text-white" style="background: var(--brand-emerald);">
                <i class="fas fa-file-csv"></i> EXPORT CSV
            </a>
            <a href="{{ route('leads.create') }}" class="btn-header btn-dark shadow-sm text-decoration-none" style="background: var(--slate-900);">
                <i class="fas fa-plus-circle"></i> NEW LEAD
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-icon" style="background: #eef2ff; color: var(--brand-indigo);"><i class="fas fa-database"></i></div>
                <div class="card-label">Total Leads</div>
                <h3 class="card-value">{{ $leads->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-icon" style="background: #fffbeb; color: var(--brand-amber);"><i class="fas fa-spinner"></i></div>
                <div class="card-label">Active Deals</div>
                <h3 class="card-value">{{ $leads->whereNotIn('status', ['Delivery', 'lost', 'won'])->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-icon" style="background: #ecfdf5; color: var(--brand-emerald);"><i class="fas fa-award"></i></div>
                <div class="card-label">Success (Delivery)</div>
                <h3 class="card-value">{{ $leads->whereIn('status', ['Delivery', 'won'])->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="card-icon" style="background: #fff1f2; color: var(--brand-rose);"><i class="fas fa-user-xmark"></i></div>
                <div class="card-label">Lost Rate</div>
                <h3 class="card-value">{{ $leads->where('status', 'lost')->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="filter-box shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="small fw-800 text-muted mb-2"><i class="fas fa-search me-1"></i> SEARCH ANYTHING</label>
                <input type="text" id="customSearch" class="form-control form-control-custom" placeholder="រកឈ្មោះ លេខទូរស័ព្ទ ឬម៉ូដែល...">
            </div>
            <div class="col-md-2">
                <label class="small fw-800 text-muted mb-2">FROM DATE</label>
                <input type="date" id="min" class="form-control form-control-custom">
            </div>
            <div class="col-md-2">
                <label class="small fw-800 text-muted mb-2">TO DATE</label>
                <input type="date" id="max" class="form-control form-control-custom">
            </div>
            <div class="col-md-3">
                <label class="small fw-800 text-muted mb-2">SELECT BRANCH</label>
                <select id="branchFilter" class="form-select form-control-custom">
                    <option value="">All Branches</option>
                    <option value="MG Head Office">MG Head Office</option>
                    <option value="MG 60M Branch">MG 60M Branch</option>
                    <option value="MG Siem Reap">MG Siem Reap</option>
                    <option value="MG Battambang">MG Battambang</option>
                </select>
            </div>
            <div class="col-md-2">
                <button onclick="window.location.reload()" class="btn btn-outline-secondary w-100 rounded-pill fw-800 py-2">RESET</button>
            </div>
        </div>
    </div>

    <div class="card-main shadow-sm">
        <div class="table-responsive">
            <table id="leadTable" class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th class="text-center">ល.រ</th>
                        <th class="text-center">Last Sync</th>
                        <th>ព័ត៌មានអតិថិជន</th>
                        <th>Sale & Branch</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Model</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $index => $lead)
                    @php 
                        $statusLower = strtolower($lead->status);
                        $isLocked = in_array($statusLower, ['delivery', 'lost', 'won']);
                        $lastLog = $lead->activities->sortByDesc('created_at')->first(); 
                        $statusClass = match($statusLower) {
                            'delivery', 'won' => 'status-won',
                            'lost' => 'status-lost',
                            default => 'status-active'
                        };
                    @endphp
                    <tr id="lead-row-{{ $lead->id }}">
                        <td class="text-center fw-bold small text-muted">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <div class="fw-800 text-dark small">{{ $lead->updated_at->format('d-M-Y') }}</div>
                            <div class="text-indigo small fw-700">{{ $lead->updated_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="fw-800 text-dark mb-1">{{ strtoupper($lead->customer_name) }}</div>
                            <div class="small fw-600 text-muted"><i class="fas fa-phone-alt me-1 text-success"></i>{{ $lead->contact_info }}</div>
                        </td>
                        <td>
                            <div class="fw-700 text-dark small">{{ $lead->sales_person }}</div>
                            <div class="badge bg-light text-muted border px-2 py-1 mt-1" style="font-size: 9px;">{{ $lead->branch }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge-status {{ $statusClass }}">
                                <i class="fas {{ $isLocked ? 'fa-lock' : 'fa-clock' }}"></i> {{ strtoupper($lead->status) }}
                            </span>
                        </td>
                        <td class="text-center fw-800 text-indigo small">{{ $lead->model }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('leads.show', Crypt::encryptString($lead->id)) }}" class="btn-action"><i class="fas fa-eye text-success"></i></a>
                                
                                @if(!$isLocked)
                                    <button class="btn-action btn-quick-edit" data-id="{{ $lead->id }}" data-name="{{ $lead->customer_name }}" data-status="{{ $lead->status }}" data-note="{{ $lastLog->comment ?? '' }}">
                                        <i class="fas fa-sync-alt text-primary"></i>
                                    </button>
                                @else
                                    <button class="btn-action" style="background:#f8fafc; cursor:not-allowed;" onclick="Swal.fire('Locked', 'This deal is finalized.', 'info')">
                                        <i class="fas fa-lock text-muted"></i>
                                    </button>
                                @endif
                                
                                <button onclick="deleteLead('{{ $lead->id }}', '{{ $lead->customer_name }}')" class="btn-action text-danger"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#leadTable').DataTable({
            "dom": 'rt<"p-3 d-flex justify-content-between align-items-center"ip>',
            "pageLength": 15,
            "order": [[1, "desc"]]
        });

        // ⚡ Search Anything
        $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });

        // 🏢 Branch Filter
        $('#branchFilter').on('change', function() { table.column(3).search(this.value).draw(); });

        // 📅 Date Filter Logic
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var min = $('#min').val(), max = $('#max').val();
            var date = new Date(data[1]);
            if (min && new Date(min) > date) return false;
            if (max && new Date(max) < date) return false;
            return true;
        });
        $('#min, #max').on('change', function() { table.draw(); });

        // ⚡ Quick Sync AJAX
        $(document).on('click', '.btn-quick-edit', function() {
            const id = $(this).data('id'), name = $(this).data('name'), status = $(this).data('status'), note = $(this).data('note');
            Swal.fire({
                title: `<span style="font-family:'Noto Sans Khmer'; font-size:16px;">Sync Intelligence: ${name}</span>`,
                html: `<div class="text-start mt-3" style="font-family:'Noto Sans Khmer';"><label class="small fw-bold mb-1">Pipeline Phase</label><select id="swal_status" class="form-select border-0 bg-light mb-3" style="border-radius:12px;"><option value="Interest" ${status == 'Interest' ? 'selected' : ''}>Interest</option><option value="Need" ${status == 'Need' ? 'selected' : ''}>Need</option><option value="Survey" ${status == 'Survey' ? 'selected' : ''}>Survey</option><option value="Book" ${status == 'Book' ? 'selected' : ''}>Book</option><option value="Delivery" ${status == 'Delivery' ? 'selected' : ''}>Delivery</option><option value="lost" ${status == 'lost' ? 'selected' : ''}>Lost</option></select><label class="small fw-bold mb-1">Activity Remark</label><textarea id="swal_comment" class="form-control border-0 bg-light" rows="3" style="border-radius:12px;">${note}</textarea></div>`,
                showCancelButton: true, confirmButtonText: 'SYNC NOW', confirmButtonColor: '#4f46e5',
                preConfirm: () => { 
                    const comment = $('#swal_comment').val();
                    if(!comment) { Swal.showValidationMessage('សូមបញ្ចូល Remark ផងបង!'); return false; }
                    return { id: id, status: $('#swal_status').val(), comment: comment, _token: "{{ csrf_token() }}" } 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Syncing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    $.post("{{ url('/leads/quick-update') }}", result.value, function(res) { if(res.success) location.reload(); });
                }
            });
        });
    });

    function deleteLead(id, name) {
        Swal.fire({ title: 'លុបទិន្នន័យ?', text: `លុប ${name} ចេញពីប្រព័ន្ធ?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#f43f5e', confirmButtonText: 'Yes, Delete' }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({ url: `/leads/${id}/delete`, method: "DELETE", data: { _token: "{{ csrf_token() }}" }, success: function() { location.reload(); } });
            }
        });
    }
</script>
@endsection