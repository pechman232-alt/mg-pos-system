@extends('layouts.homePages')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* =========================================================
       CLEAN SaaS DESIGN SYSTEM 
       ========================================================= */
    :root {
        --primary: #4F46E5;      /* Indigo 600 */
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      /* Slate 50 */
        --text-main: #111827;    /* Gray 900 */
        --text-muted: #6B7280;   /* Gray 500 */
        --border-color: #E5E7EB; /* Gray 200 */
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
    }

    body { background-color: var(--bg-body); font-family: var(--font-body) !important; color: var(--text-main); margin: 0; -webkit-font-smoothing: antialiased; }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 24px 32px !important; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    /* ====== KPI SUMMARY ====== */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px; }
    .kpi-card { 
        background: var(--surface); border-radius: 12px; padding: 20px 24px; 
        display: flex; align-items: center; gap: 20px; border: 1px solid var(--border-color); 
        box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    
    .kpi-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .kpi-data h3 { margin: 0; font-family: var(--font-display); font-weight: 700; font-size: 1.5rem; color: var(--text-main); line-height: 1.2; }
    .kpi-data p { margin: 0 0 4px 0; color: var(--text-muted); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Live Clock Styling */
    #liveClock { 
        font-family: 'JetBrains Mono', monospace; background: #F3F4F6; 
        padding: 4px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; 
        letter-spacing: 0.5px; color: var(--text-main); border: 1px solid var(--border-color); 
    }

    /* ====== TABLE CARD ====== */
    .card-main { 
        background: var(--surface); border: 1px solid var(--border-color); 
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; width: 100%; 
    }
    
    /* Clean Header */
    .card-header-clean { 
        padding: 20px 24px; border-bottom: 1px solid var(--border-color); 
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
        background: #FFFFFF;
    }
    .header-title { 
        font-family: var(--font-display); color: var(--text-main); font-weight: 700; font-size: 1.1rem; 
        margin: 0; display: flex; align-items: center; gap: 10px; 
    }
    
    /* Buttons */
    .btn-primary-custom {
        background: var(--primary); color: #FFF; border: none;
        padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-primary-custom:hover { background: var(--primary-hover); color: #FFF; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    /* Search Bar */
    .search-wrapper { position: relative; width: 320px; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF; }
    .search-input {
        width: 100%; background: #F9FAFB; border: 1px solid #D1D5DB;
        border-radius: 8px; padding: 8px 16px 8px 36px; font-size: 0.875rem;
        color: var(--text-main); transition: all 0.2s ease; outline: none;
    }
    .search-input:focus { background: #FFF; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }

    /* ====== TABLE STYLING ====== */
    .table-custom { margin: 0 !important; width: 100%; border-collapse: collapse; }
    .table-custom thead th { 
        background-color: #F9FAFB !important; color: var(--text-muted) !important; font-weight: 600; 
        text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; 
        padding: 12px 24px !important; border-bottom: 1px solid var(--border-color) !important; border-top: none;
    }
    .table-custom tbody td { 
        padding: 16px 24px !important; vertical-align: middle; border-bottom: 1px solid #F3F4F6; 
        font-size: 0.875rem; color: var(--text-main); font-weight: 500; transition: background 0.15s ease;
    }
    .table-custom tbody tr:hover td { background-color: #F8FAFC; }
    .table-custom tbody tr:last-child td { border-bottom: none; }

    /* Badges */
    .badge-model {
        background: #EEF2FF; color: var(--primary); padding: 4px 10px; border-radius: 6px; 
        font-size: 0.8rem; font-weight: 600; border: 1px solid #C7D2FE; display: inline-block;
    }

    /* Action Buttons (Table) */
    .action-group { display: flex; gap: 8px; justify-content: center; }
    .action-btn { 
        width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; 
        border-radius: 6px; border: 1px solid var(--border-color); color: var(--text-muted); 
        transition: 0.2s; background: #FFF; text-decoration: none !important;
    }
    .action-btn:hover { background-color: #EEF2FF; color: var(--primary); border-color: var(--primary); }
    .btn-delete:hover { background-color: #FEF2F2; color: #EF4444; border-color: #EF4444; }

    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_length { padding: 16px 24px !important; font-weight: 500; color: var(--text-muted); font-size: 0.8rem; }
    .dataTables_wrapper .dataTables_paginate { padding: 12px 24px !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border: 1px solid var(--border-color) !important; border-radius: 6px !important; padding: 4px 12px !important; font-size: 0.85rem !important; background: #FFF !important; margin: 0 2px !important; color: var(--text-muted) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--primary) !important; color: white !important; border: 1px solid var(--primary) !important; font-weight: 600; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) { background: #F3F4F6 !important; color: var(--text-main) !important; border-color: var(--border-color) !important; }
    .dataTables_filter { display: none; } /* Hide default search, use custom */

</style>

<div class="fluid-wrapper">
    
    <!-- KPI SUMMARY -->
    <div class="summary-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #EEF2FF; color: var(--primary);"><i class="fas fa-car-side"></i></div>
            <div class="kpi-data"><p>Total Car Models</p><h3>{{ $carModels->count() }}</h3></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-data"><p>Current System Time</p><h3 id="liveDate">Loading...</h3></div>
            <div id="liveClock" class="ms-auto">00:00:00 AM</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: #D1FAE5; color: #059669;"><i class="fas fa-shield-check"></i></div>
            <div class="kpi-data"><p>Database Status</p><h3 style="color: #059669;">SECURE</h3></div>
        </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="card-main">
        
        <div class="card-header-clean">
            <h5 class="header-title"><i class="fas fa-layer-group text-muted"></i> MODEL REPOSITORY</h5>
            
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-wrapper d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" id="customSearch" class="search-input" placeholder="Search catalog...">
                </div>

                <a href="{{ url('/car-models/create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus"></i> ADD MODEL
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="mainTable" class="table-custom">
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
                                <td class="text-center text-muted fw-bold">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                                <td><span class="badge-model">{{ $carModel->model_name }}</span></td>
                                <td class="text-center text-muted" style="font-size: 0.8rem;">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $carModel->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <a href="{{ url('car-models/edit', encrypt($carModel->id)) }}" class="action-btn" title="Edit Model">
                                            <i class="fas fa-pen" style="font-size: 12px;"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ url('car-models/destroy', encrypt($carModel->id)) }}')" class="action-btn btn-delete" title="Delete Model">
                                            <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
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

    // 2. DELETE CONFIRMATION (Clean Theme)
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Model?',
            html: `This will permanently remove the nomenclature from system.<br><span style="color:#6B7280; font-size:13px;">This action cannot be undone.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: '<span style="color:#4B5563">Cancel</span>',
            fontFamily: 'Inter',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-sm border-0',
                actions: 'd-flex flex-wrap justify-content-center gap-2 w-100 mt-4',
                confirmButton: 'btn fw-bold rounded-3 px-4 py-2 border-0 shadow-sm text-white',
                cancelButton: 'btn fw-bold rounded-3 px-4 py-2 border-0'
            }
        }).then((result) => { 
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the system.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); },
                    customClass: { popup: 'rounded-4 shadow-sm border-0' }
                });
                window.location.href = url;
            }
        });
    }

    $(document).ready(function() {
        startTime();

        // 3. DATATABLES INITIALIZATION
        var table = $('#mainTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ models",
                "paginate": { 
                    "next": '<i class="fas fa-chevron-right" style="font-size:10px;"></i>', 
                    "previous": '<i class="fas fa-chevron-left" style="font-size:10px;"></i>' 
                }
            },
            "dom": '<"d-flex justify-content-between align-items-center border-bottom px-4"l>rt<"d-flex justify-content-between align-items-center flex-wrap"ip><"clear">',
            "drawCallback": function(settings) {
                $('#mainTable tbody').hide().fadeIn(200);
            }
        });

        // 4. CUSTOM SEARCH LOGIC
        $('#customSearch').keyup(function(){
            table.search($(this).val()).draw();
        });
    });
</script>
@endsection