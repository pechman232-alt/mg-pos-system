@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,600;0,800;1,800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>

<style>
    :root {
        --mg-red: #e31837;
        --mg-navy: #0a1a2f;
        --soft-bg: #f8fafc;
        --border-color: #edf2f7;
    }

    /* ==================== 100% FIT SCREEN WRAPPER ==================== */
    .report-fluid-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        background: var(--soft-bg);
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* SHAKE ANIMATION */
    @keyframes shake-modal {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
    .shake-trigger { animation: shake-modal 0.8s cubic-bezier(.36,.07,.19,.97) both; }

    /* DATA CARDS */
    .data-card { 
        background: white; 
        border-radius: 20px; 
        padding: 25px; 
        margin-bottom: 20px; 
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .card-header-clean { 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        margin-bottom: 1.5rem; 
        border-bottom: 2px solid var(--soft-bg); 
        padding-bottom: 12px; 
    }
    .card-header-clean h6 { margin: 0; font-weight: 800; color: var(--mg-navy); text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; }

    /* FORM ELEMENTS */
    .field-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block; }
    .form-control:disabled { 
        background-color: #fcfdfe !important; 
        border: 1.5px solid #e2e8f0; 
        font-weight: 700; 
        color: #1e293b; 
        font-size: 14px; 
        padding: 12px; 
        border-radius: 10px; 
    }
    
    .highlight-val { border-left: 5px solid var(--mg-red) !important; color: var(--mg-red) !important; }

    /* KPI BOXES */
    .bar-fill-blue { background: linear-gradient(135deg, #1e40af, #3b82f6); border-radius: 20px; padding: 25px; color: white; box-shadow: 0 10px 20px rgba(30, 64, 175, 0.15); }
    .bar-fill-purple { background: linear-gradient(135deg, #0f172a, #334155); border-radius: 20px; padding: 30px; color: white; }

    .btn-call-neon { background: var(--mg-red); color: white; border-radius: 14px; padding: 18px; font-weight: 800; border: none; width: 100%; transition: 0.3s; }
    .btn-call-neon:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(227, 24, 55, 0.3); }

    .sticky-hud { position: sticky; top: 20px; }

    @media print { .no-print { display: none; } .report-fluid-container { padding: 0; } }
</style>

<div class="report-fluid-container">
    <audio id="notifSound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="fw-800 text-navy mb-0" style="font-size: 2rem;">SALES REPORT ARCHIVE</h1>
            <div id="liveTime" class="fw-bold text-danger small"></div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.history.back()" class="btn btn-light border fw-bold rounded-pill px-4">BACK</button>
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow">PRINT REPORT</button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div class="bar-fill-blue mb-4">
                <div class="d-flex justify-content-between fw-black small mb-2"><span>TRANSACTION VALUE PROGRESS</span><span id="goalPercent">0%</span></div>
                <div class="progress bg-white bg-opacity-20" style="height: 12px; border-radius: 12px;">
                    <div id="goalBar" class="progress-bar bg-white shadow-sm" style="width: 0%"></div>
                </div>
            </div>

            <div class="data-card">
                <div class="card-header-clean"><i class="fas fa-user-circle text-primary fs-4"></i><h6>Core Information</h6></div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="field-label">Brand Model</label><input class="form-control" value="{{ $saleReports->model_name }}" disabled></div>
                    <div class="col-md-4"><label class="field-label">Branch</label><input class="form-control" value="{{ $saleReports->branchs }}" disabled></div>
                    <div class="col-md-4"><label class="field-label">Brand Name</label><input class="form-control" value="{{ $saleReports->brand_name }}" disabled></div>
                    <div class="col-md-6"><label class="field-label">Customer Name</label><input class="form-control" value="{{ $saleReports->customer_name }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Gender</label><input class="form-control" value="{{ $saleReports->gender }}" disabled></div>
                    <div class="col-md-3">
                        <label class="field-label">Date of Birth</label>
                        <input class="form-control" value="{{ $saleReports->dob ? \Carbon\Carbon::parse(preg_replace('/^00/', '20', $saleReports->dob))->format('d-m-Y') : 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-4"><label class="field-label">Phone Number</label><input class="form-control highlight-val" value="{{ $saleReports->phone_number }}" disabled></div>
                    <div class="col-md-8"><label class="field-label">Address</label><input class="form-control" value="{{ $saleReports->address }}" disabled></div>
                </div>
            </div>

            <div class="data-card">
                <div class="card-header-clean"><i class="fas fa-file-contract text-warning fs-4"></i><h6>Contractual & Source Data</h6></div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="field-label">Contract No</label><input class="form-control" value="{{ $saleReports->contract_no }}" disabled></div>
                    <div class="col-md-4">
                        <label class="field-label">Contract Date</label>
                        <input class="form-control" value="{{ $saleReports->contract_date ? \Carbon\Carbon::parse(preg_replace('/^00/', '20', $saleReports->contract_date))->format('d-m-Y') : 'N/A' }}" disabled>
                    </div>
                    <div class="col-md-4"><label class="field-label">Sale Person</label><input class="form-control" value="{{ $saleReports->sale_person }}" disabled></div>
                    <div class="col-md-6"><label class="field-label">Source Customer</label><input class="form-control" value="{{ $saleReports->source_id }}" disabled></div>
                    <div class="col-md-6"><label class="field-label">Member Card</label><input class="form-control" value="{{ $saleReports->memberCard }}" disabled></div>
                </div>
            </div>

            <div class="data-card">
                <div class="card-header-clean"><i class="fas fa-car-side text-danger fs-4"></i><h6>Vehicle Specifications</h6></div>
                <div class="row g-3">
                    <div class="col-md-4"><label class="field-label">VIN / Chassis</label><input class="form-control" value="{{ $saleReports->vinNumber }}" disabled></div>
                    <div class="col-md-4"><label class="field-label">Engine Number</label><input class="form-control" value="{{ $saleReports->engineNumber }}" disabled></div>
                    <div class="col-md-4"><label class="field-label">Plate Number</label><input class="form-control highlight-val" value="{{ $saleReports->plate_number }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Battery IR</label><input class="form-control" value="{{ $saleReports->vihicle_battery }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Warranty MG</label><input class="form-control" value="{{ $saleReports->warranty }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Warranty LeapMotor</label><input class="form-control" value="{{ $saleReports->warranty_leap_motor }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">VPC VDR Status</label><input class="form-control" value="{{ $saleReports->vpc_vdr }}" disabled></div>
                </div>
            </div>

            <div class="data-card">
                <div class="card-header-clean"><i class="fas fa-box-open text-success fs-4"></i><h6>Promotions & Options</h6></div>
                <div class="row g-2">
                    <div class="col-md-2"><label class="field-label">Floor Mat</label><input class="form-control" value="{{ $saleReports->offer_floor_mat }}" disabled></div>
                    <div class="col-md-2"><label class="field-label">UV Film</label><input class="form-control" value="{{ $saleReports->offer_uv_film }}" disabled></div>
                    <div class="col-md-2"><label class="field-label">Side Step</label><input class="form-control" value="{{ $saleReports->side_step }}" disabled></div>
                    <div class="col-md-2"><label class="field-label">Tailgate</label><input class="form-control" value="{{ $saleReports->auto_tailgate }}" disabled></div>
                    <div class="col-md-2"><label class="field-label">Spoiler</label><input class="form-control" value="{{ $saleReports->rear_spoiler }}" disabled></div>
                </div>
            </div>

            <div class="data-card">
                <div class="card-header-clean"><i class="fas fa-file-invoice-dollar text-info fs-4"></i><h6>Accounting & Remarks</h6></div>
                <div class="row g-3">
                    <div class="col-md-3"><label class="field-label">Invoice No</label><input class="form-control" value="{{ $saleReports->invoice_no }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Invoice Date</label><input class="form-control" value="{{ $saleReports->invoice_date }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Delivery Date</label><input class="form-control highlight-val" value="{{ $saleReports->delivery_date }}" disabled></div>
                    <div class="col-md-3"><label class="field-label">Price</label><input class="form-control highlight-val" value="${{ number_format((float)$saleReports->price, 2) }}" disabled></div>
                    <div class="col-md-12"><label class="field-label">SA Remarks</label><input class="form-control" value="{{ $saleReports->remark_sa }}" disabled></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="sticky-hud no-print">
                <div class="data-card p-2 text-center">
                    <div style="border-radius: 15px; overflow: hidden; background: #f1f5f9; border: 4px solid #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                        <img src="{{ url($saleReports->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $saleReports->image_name) }}" class="img-fluid" style="width: 100%;">
                    </div>
                    <div class="mt-3"><span class="badge bg-danger fw-bold p-2 px-3">{{ $saleReports->brand_name }} Node</span></div>
                </div>

                <div class="bar-fill-purple text-center">
                    <div id="countdownNum" style="font-size: 3.5rem; font-weight: 800; line-height: 1;">--</div>
                    <div id="countdownLabel" class="fw-800 small uppercase mt-2">Days in System</div>
                </div>

                <div class="data-card mt-3 text-center">
                    <div id="qrcode" class="mb-3 d-inline-block p-2 bg-white rounded-3 shadow-sm"></div>
                    <div class="d-grid gap-2">
                        <a href="tel:{{ str_replace(' ', '', $saleReports->phone_number) }}" class="btn-call-neon text-decoration-none"><i class="fas fa-phone-alt me-2"></i> CALL CLIENT</a>
                        <button class="btn btn-outline-dark fw-800 py-3 rounded-3" data-bs-toggle="modal" data-bs-target="#fbModal">VIEW LOGS</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fbModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div id="modalContent" class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
            <div class="modal-header bg-dark p-4">
                <h5 class="modal-title text-white fw-800">SYSTEM COMMAND</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5 text-center">
                <div class="p-4 rounded-4" style="background: #f8fafc; border-left: 6px solid var(--mg-red);">
                    <p class="fs-4 mb-0 fw-800 italic">"{{ $saleReports->feedback_command ?? 'No logs found.' }}"</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() { document.getElementById('liveTime').innerText = new Date().toLocaleString(); }
    setInterval(updateClock, 1000); updateClock();

    document.addEventListener("DOMContentLoaded", function() {
        // 1. QR Code
        var phone = "{{ str_replace(' ', '', $saleReports->phone_number) }}";
        if(phone.trim() !== "") { new QRCode(document.getElementById("qrcode"), { text: "tel:"+phone.trim(), width: 120, height: 120 }); }

        // 2. Feedback Alert
        var fb = "{{ $saleReports->feedback_command }}";
        if(fb && fb.trim() !== "") {
            const myModal = new bootstrap.Modal(document.getElementById('fbModal'));
            myModal.show();
            document.getElementById('modalContent').classList.add('shake-trigger');
            setTimeout(() => { document.getElementById("notifSound").play(); }, 1000);
        }

        // 3. Days Tracker
        const invoiceDateRaw = "{{ $saleReports->invoice_date ? preg_replace('/^00/', '20', $saleReports->invoice_date) : '' }}";
        if(invoiceDateRaw) {
            const invDate = new Date(invoiceDateRaw);
            const diff = Math.ceil((invDate - new Date()) / (1000 * 60 * 60 * 24));
            document.getElementById('countdownNum').innerText = Math.abs(diff);
            document.getElementById('countdownLabel').innerText = diff >= 0 ? "Days Pending" : "Days Archived";
        }

        // 4. Progress Logic
        const price = parseFloat("{{ $saleReports->price }}") || 0;
        let p = Math.min(Math.round((price / 100000) * 100), 100);
        document.getElementById('goalBar').style.width = p + "%";
        document.getElementById('goalPercent').innerText = p + "%";
    });
</script>
@endsection