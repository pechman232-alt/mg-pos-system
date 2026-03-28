@extends('layouts.homePages')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
    }

    body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif !important; margin: 0; }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.6s ease-out; }

    /* ====== KPI SUMMARY ====== */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 25px; }
    .kpi-card { background: white; border-radius: 16px; padding: 20px 25px; display: flex; align-items: center; gap: 20px; border: 1px solid #edf2f7; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .kpi-icon { width: 55px; height: 55px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .kpi-data h3 { margin: 0; font-weight: 800; font-size: 1.5rem; color: #1e293b; }
    .kpi-data p { margin: 0; color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }

    /* ====== HEADER & LIVE CLOCK ====== */
    .branded-header { background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important; padding: 1.2rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .header-title { color: white !important; font-weight: 800; font-size: 1.2rem; margin: 0; display: flex; align-items: center; gap: 12px; }
    
    #liveClock { font-family: 'JetBrains Mono', monospace; background: rgba(0,0,0,0.2); padding: 5px 15px; border-radius: 8px; font-size: 14px; letter-spacing: 1px; color: #fff; border: 1px solid rgba(255,255,255,0.1); }

    /* ====== TABLE & PAGINATION REFINEMENT ====== */
    .card-main { border: none !important; border-radius: 16px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important; background: #fff; overflow: hidden; width: 100%; }
    .table-custom thead th { background-color: #fcfdfe !important; color: #64748b !important; font-weight: 800; text-transform: uppercase; font-size: 11px; padding: 1.2rem 2rem !important; }
    .table-custom tbody td { padding: 14px 2rem !important; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-weight: 600; }

    /* DataTables Pagination Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--brand-indigo) !important; color: white !important; border: none !important; border-radius: 8px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: var(--brand-purple) !important; color: white !important; border: none !important; border-radius: 8px; }
    .dataTables_info, .dataTables_length { padding: 1.5rem 2rem; font-weight: 600; color: #64748b; font-size: 12px; }
    .dataTables_filter { display: none; } /* We use our custom search bar */

    .action-btn { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: 1.5px solid #e2e8f0; color: #64748b; transition: 0.2s; }
    .action-btn:hover { background-color: var(--brand-indigo); color: white; border-color: var(--brand-indigo); transform: scale(1.1); }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="fluid-wrapper">
    <div class="summary-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(79, 70, 229, 0.1); color: var(--brand-indigo);"><i class="fas fa-car"></i></div>
            <div class="kpi-data"><p>Total Car Models</p><h3>{{ $carModels->count() }}</h3></div>
        </div>
        <div class="kpi-card">
            <div id="liveClock">00:00:00 AM</div>
            <div class="kpi-data"><p>Current System Time</p><h3 id="liveDate">Loading...</h3></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(113, 221, 55, 0.1); color: #519b22;"><i class="fas fa-shield-check"></i></div>
            <div class="kpi-data"><p>Database Status</p><h3>SECURE</h3></div>
        </div>
    </div>

    <div class="card-main">
        <div class="branded-header">
            <h5 class="header-title"><i class="fas fa-layer-group"></i> MODEL REPOSITORY</h5>
            <a href="{{ url('/car-models/create') }}" class="btn" style="background: rgba(255,255,255,0.2); color:white; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-weight:700; font-size: 12px; padding: 10px 22px;">
                <i class="fas fa-plus me-1"></i> ADD MODEL
            </a>
        </div>

        <div class="p-4 bg-white border-bottom d-flex justify-content-end">
            <div style="position: relative; width: 350px;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" id="customSearch" placeholder="Search catalog..." style="width: 100%; padding: 11px 15px 11px 45px; border-radius: 12px; border: 1.5px solid #e2e8f0; background: #f8fafc; font-weight: 600; outline: none;">
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="mainTable" class="table table-custom table-hover m-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">REF</th>
                            <th>Model Nomenclature</th>
                            <th class="text-center">Integration Date</th>
                            <th class="text-center" style="width: 150px;">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($carModels as $index => $carModel)
                            <tr>
                                <td class="text-center text-muted">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                <td><span style="color: var(--brand-indigo); background: rgba(79, 70, 229, 0.08); padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ $carModel->model_name }}</span></td>
                                <td class="text-center text-muted"><i class="far fa-calendar-check me-2"></i>{{ $carModel->created_at->format('d M, Y') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ url('car-models/edit', encrypt($carModel->id)) }}" class="action-btn"><i class="fas fa-edit"></i></a>
                                        <button onclick="confirmDelete('{{ url('car-models/destroy', encrypt($carModel->id)) }}')" class="action-btn" style="border-color: #fecaca; color: #ef4444;"><i class="fas fa-trash"></i></button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 1. LIVE CLOCK ENGINE
    function startTime() {
        const today = new Date();
        let h = today.getHours();
        let m = today.getMinutes();
        let s = today.getSeconds();
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12; h = h ? h : 12; 
        m = checkTime(m); s = checkTime(s);
        document.getElementById('liveClock').innerHTML = h + ":" + m + ":" + s + " " + ampm;
        
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        document.getElementById('liveDate').innerHTML = today.toLocaleDateString('en-GB', options);
        setTimeout(startTime, 1000);
    }
    function checkTime(i) { if (i < 10) {i = "0" + i}; return i; }

    // 2. DELETE CONFIRMATION
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Model?',
            text: "This will permanently remove the nomenclature from system.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Confirm'
        }).then((result) => { if (result.isConfirmed) window.location.href = url; });
    }

    $(document).ready(function() {
        startTime();

        // 3. DATATABLES INITIALIZATION (PAGINATION)
        var table = $('#mainTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50],
            "language": {
                "lengthMenu": "Show _MENU_ units",
                "info": "Displaying _START_ to _END_ of _TOTAL_ models",
                "paginate": { "next": "Next", "previous": "Prev" }
            },
            "dom": "ltip" // Hide default search, show length, table, info, pagination
        });

        // 4. CUSTOM SEARCH LOGIC
        $('#customSearch').keyup(function(){
            table.search($(this).val()).draw();
        });
    });
</script>
@endsection