@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>

<style>
    /* ================= PROFESSIONAL UI SYSTEM ================= */
    :root {
        --brand-red: #e31e24;
        --brand-blue: #002663;
        --text-heading: #0f172a;
        --text-body: #475569;
        --text-muted: #64748b;
        --bg-main: #f8fafc;
        --border-color: #cbd5e1;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        background-color: var(--bg-main);
        color: var(--text-body);
    }

    .report-fluid-container { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* SHAKE ANIMATION */
    @keyframes shake-modal {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
    .shake-trigger { animation: shake-modal 0.8s cubic-bezier(.36,.07,.19,.97) both; }

    /* HEADER & BUTTONS */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; background: #ffffff; padding: 15px 25px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
    .page-title { font-size: 1.4rem; font-weight: 800; color: var(--brand-blue); margin: 0; letter-spacing: -0.5px; }

    .btn-outline-custom { background: #ffffff; color: var(--text-heading); border: 2px solid var(--border-color); border-radius: 10px; font-weight: 700; padding: 8px 20px; transition: 0.3s; }
    .btn-outline-custom:hover { background: #f1f5f9; transform: translateY(-2px); }
    
    .btn-print { background: var(--text-heading); color: #ffffff; border: none; border-radius: 10px; font-weight: 700; padding: 10px 24px; transition: 0.3s; box-shadow: 0 4px 6px rgba(15, 23, 42, 0.2); }
    .btn-print:hover { background: #000000; transform: translateY(-2px); color: #ffffff;}

    /* DATA CARDS */
    .app-card { background: #ffffff; border-radius: 14px; margin-bottom: 24px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); overflow: hidden; }
    .app-card-header { display: flex; align-items: center; gap: 12px; background: #fcfcfd; border-bottom: 1px solid var(--border-color); padding: 14px 20px; }
    .app-card-header h6 { margin: 0; font-weight: 800; color: var(--brand-blue); text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.95rem; }

    /* 🌟 BORDERED COMPACT TABLE 🌟 */
    .table-compact { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    .table-compact th, .table-compact td { border: 1px solid var(--border-color); padding: 10px 16px; vertical-align: middle; }
    .table-compact th { background-color: #f8fafc; width: 20%; color: var(--text-muted); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; text-align: right; letter-spacing: 0.5px; }
    .table-compact td { background-color: #ffffff; width: 30%; color: var(--text-heading); font-weight: 700; font-size: 0.9rem; }
    
    .mono-font { font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: var(--brand-blue);}
    .highlight-cell { background-color: #fff5f5 !important; color: var(--brand-red) !important; font-weight: 800 !important; }

    /* KPI WIDGETS */
    .widget-blue { background: linear-gradient(135deg, var(--brand-blue), #0f4492); border-radius: 14px; padding: 24px; color: white; box-shadow: 0 10px 20px rgba(0, 38, 99, 0.15); }
    .widget-dark { background: linear-gradient(135deg, #0f172a, #334155); border-radius: 14px; padding: 30px 20px; color: white; box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15); }
    
    .btn-call-action { background: linear-gradient(135deg, var(--brand-red), #b91c1c); color: white; border-radius: 12px; padding: 14px; font-weight: 800; width: 100%; transition: 0.3s; border: none; }
    .btn-call-action:hover { transform: translateY(-3px); color: white;}
    .btn-logs { background: transparent; color: var(--text-heading); border: 2px solid var(--border-color); border-radius: 12px; padding: 12px; font-weight: 800; width: 100%; transition: 0.3s; }
    .btn-logs:hover { background: #f1f5f9; }

    .sticky-hud { position: sticky; top: 20px; }

    /* PRINT STYLES */
    @media print { 
        .no-print { display: none !important; } 
        .report-fluid-container { padding: 0 !important; background: white;} 
        .app-card { border: 1px solid #000; margin-bottom: 15px;}
        .table-compact th, .table-compact td { border: 1px solid #000; padding: 6px 10px; }
        .table-compact th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }
    }
</style>





<div class="report-fluid-container">
    <div class="page-header no-print">
        <div>
            <h1 class="page-title"><i class="fas fa-file-invoice me-2 text-danger"></i> SALES REPORT ARCHIVE</h1>
            <div id="liveTime" class="fw-bold text-danger small mt-1" style="letter-spacing: 0.5px;"></div>
        </div>
        <div class="d-flex gap-3">
            <button onclick="window.history.back()" class="btn-outline-custom"><i class="fas fa-arrow-left me-1"></i> BACK</button>
            <button onclick="window.print()" class="btn-print"><i class="fas fa-print me-1"></i> PRINT REPORT</button>
        </div>
    </div>

    <div class="d-none d-print-block mb-4 border-bottom pb-2">
        <h2 class="fw-800 text-dark mb-1">OFFICIAL SALES REPORT</h2>
        <div class="fw-bold text-muted small">Generated on: {{ now()->format('d-m-Y H:i:s') }}</div>
    </div>

    <div class="row">
        <div class="col-xl-9 col-lg-8">
            
            <div class="widget-blue mb-4 no-print">
                <div class="d-flex justify-content-between fw-800 small mb-2 text-uppercase" style="letter-spacing: 0.5px;">
                    <span><i class="fas fa-chart-line me-2"></i> Transaction Value Progress</span>
                    <span id="goalPercent" class="fs-6">0%</span>
                </div>
                <div class="progress bg-white bg-opacity-25" style="height: 12px; border-radius: 12px;">
                    <div id="goalBar" class="progress-bar bg-white shadow-sm" style="width: 0%; border-radius: 12px;"></div>
                </div>
            </div>

            <div class="app-card">
                <div class="app-card-header"><i class="fas fa-user-circle text-primary fs-4"></i><h6>Core Information</h6></div>
                <div class="p-0">
                    <table class="table-compact m-0" style="border-style: hidden;">
                        <tbody>
                            <tr>
                                <th>Customer Name</th><td>{{ $saleReports->customer_name ?: 'N/A' }}</td>
                                <th>Gender</th><td>{{ $saleReports->gender ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Phone Number</th><td class="mono-font highlight-cell"><i class="fas fa-phone-alt me-1"></i> {{ $saleReports->phone_number ?: 'N/A' }}</td>
                                <th>Date of Birth</th><td>{{ $saleReports->dob ? \Carbon\Carbon::parse(preg_replace('/^00/', '20', $saleReports->dob))->format('d-m-Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Address (Province)</th><td colspan="3">{{ $saleReports->address ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Brand Name</th><td class="text-danger fw-800">{{ $saleReports->brand_name ?? 'N/A' }}</td>
                                <th>BRAND MODEL</th>
                                <td class="text-danger fw-800">
                                    @php
                                        $modelVal = $saleReports->model_name; // អាចជាលេខ 1 ឬជាឈ្មោះឡានផ្ទាល់
                                        $finalCarName = $modelVal; 
                                        
                                        // ប្រសិនបើវាជា "លេខ" នោះយើងឱ្យវាទៅរកឈ្មោះក្នុងតារាង car_models
                                        if (is_numeric($modelVal)) {
                                            $carModelObj = \App\Models\CarModelModel::find($modelVal);
                                            if ($carModelObj) {
                                                $finalCarName = $carModelObj->model_name;
                                            }
                                        }
                                    @endphp
                                    
                                    {{ $finalCarName ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Branch</th><td colspan="3">{{ $saleReports->branchs ?: 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="app-card">
                <div class="app-card-header"><i class="fas fa-file-contract text-warning fs-4"></i><h6>Contractual & Source Data</h6></div>
                <div class="p-0">
                    <table class="table-compact m-0" style="border-style: hidden;">
                        <tbody>
                            <tr>
                                <th>Contract No</th><td class="mono-font">{{ $saleReports->contract_no ?: 'N/A' }}</td>
                                <th>Contract Date</th><td>{{ $saleReports->contract_date ? \Carbon\Carbon::parse(preg_replace('/^00/', '20', $saleReports->contract_date))->format('d-m-Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Sales Person</th><td>{{ $saleReports->sale_person ?: 'N/A' }}</td>
                                <th>Member Card</th><td class="mono-font">{{ $saleReports->memberCard ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Source Customer</th><td colspan="3">{{ $saleReports->source_id ?: 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="app-card">
                <div class="app-card-header"><i class="fas fa-car-side text-danger fs-4"></i><h6>Vehicle Specifications</h6></div>
                <div class="p-0">
                    <table class="table-compact m-0" style="border-style: hidden;">
                        <tbody>
                            <tr>
                                <th>VIN / Chassis</th><td class="mono-font">{{ $saleReports->vinNumber ?? 'N/A' }}</td>
                                <th>Engine Number</th><td class="mono-font">{{ $saleReports->engineNumber ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Plate Number</th><td class="highlight-cell">{{ $saleReports->plate_number ?: 'N/A' }}</td>
                                <th>Battery IR</th><td>{{ $saleReports->vihicle_battery ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Warranty MG</th><td>{{ $saleReports->warranty ?: 'N/A' }}</td>
                                <th>Warranty LeapMotor</th><td>{{ $saleReports->warranty_leap_motor ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>VPC VDR Status</th><td colspan="3">{{ $saleReports->vpc_vdr ?: 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="app-card">
                <div class="app-card-header"><i class="fas fa-gift text-success fs-4"></i><h6>🎁 FREE ACCESSORIES & OFFERS</h6></div>
                <div class="p-0">
                    <table class="table-compact m-0" style="border-style: hidden;">
                        <tbody>
                            <tr>
                                <th>Floor Mat</th><td class="{!! strtolower($saleReports->offer_floor_mat) == 'yes' ? 'text-success' : 'text-muted' !!}">{{ $saleReports->offer_floor_mat ?: 'No' }}</td>
                                <th>UV Film</th><td class="{!! strtolower($saleReports->offer_uv_film) == 'yes' ? 'text-success' : 'text-muted' !!}">{{ $saleReports->offer_uv_film ?: 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Side Step</th><td class="{!! strtolower($saleReports->side_step) == 'yes' ? 'text-success' : 'text-muted' !!}">{{ $saleReports->side_step ?: 'No' }}</td>
                                <th>Auto Tailgate</th><td class="{!! strtolower($saleReports->auto_tailgate) == 'yes' ? 'text-success' : 'text-muted' !!}">{{ $saleReports->auto_tailgate ?: 'No' }}</td>
                            </tr>
                            <tr>
                                <th>Rear Spoiler</th><td class="{!! strtolower($saleReports->rear_spoiler) == 'yes' ? 'text-success' : 'text-muted' !!}">{{ $saleReports->rear_spoiler ?: 'No' }}</td>
                                <th>Certificate Engine Oil</th>
                                <td class="{{ $saleReports->certificate_engine_oil ? 'text-success' : 'text-muted' }}">
                                    {{ $saleReports->engineOil ? $saleReports->engineOil->name : 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="app-card mb-xl-0">
                <div class="app-card-header"><i class="fas fa-file-invoice-dollar text-info fs-4"></i><h6>Accounting & Remarks</h6></div>
                <div class="p-0">
                    <table class="table-compact m-0" style="border-style: hidden;">
                        <tbody>
                            <tr>
                                <th>Invoice No</th><td class="mono-font">{{ $saleReports->invoice_no ?: 'N/A' }}</td>
                                <th>Invoice Date</th><td>{{ $saleReports->invoice_date ? \Carbon\Carbon::parse($saleReports->invoice_date)->format('d-m-Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Date</th><td class="text-success">{{ $saleReports->delivery_date ? \Carbon\Carbon::parse($saleReports->delivery_date)->format('d-m-Y') : 'N/A' }}</td>
                                <th>Price</th><td class="highlight-cell fs-6">${{ number_format((float)$saleReports->price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>SA Remarks</th><td colspan="3" class="fst-italic" style="white-space: pre-line;">{{ $saleReports->remark_sa ?: 'No remarks available.' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="sticky-hud">
                
                <div class="app-card text-center p-3 mb-4">
                    <div style="border-radius: 10px; overflow: hidden; background: #f8fafc; border: 1px solid var(--border-color);">
                        <img src="{{ url($saleReports->image_name == '' || $saleReports->image_name == 'default.png' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $saleReports->image_name) }}" class="img-fluid w-100" alt="Car Image">
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-danger fw-800 px-3 py-2 fs-6 rounded-pill shadow-sm">{{ $saleReports->brand_name }} UNIT</span>
                    </div>
                </div>

                <div class="widget-dark text-center mb-4">
                    <div id="countdownNum" style="font-size: 3.5rem; font-weight: 800; line-height: 1;">--</div>
                    <div id="countdownLabel" class="fw-800 small text-uppercase mt-2 text-info" style="letter-spacing: 1px;">Days in System</div>
                </div>

                <div class="app-card text-center no-print">
                    <div class="app-card-body">
                        <div id="qrcode" class="mb-4 d-inline-block p-2 bg-white rounded-3 border shadow-sm"></div>
                        
                        <div class="d-grid gap-3">
                            <a href="tel:{{ str_replace(' ', '', $saleReports->phone_number) }}" class="btn-call-action text-decoration-none">
                                <i class="fas fa-phone-alt me-2"></i> CALL CLIENT
                            </a>
                            <button class="btn-logs" data-bs-toggle="modal" data-bs-target="#fbModal">
                                <i class="fas fa-history me-2"></i> VIEW LOGS
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fbModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div id="modalContent" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-800 text-dark"><i class="fas fa-terminal text-danger me-2 fs-4"></i> SYSTEM LOGS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="p-4 rounded-3 text-start" style="background: #f8fafc; border-left: 6px solid var(--brand-red);">
                    <p class="fs-6 mb-0 fw-700 text-secondary" style="white-space: pre-line;">{{ $saleReports->feedback_command ?? 'No logs or feedback recorded.' }}</p>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-0 justify-content-center">
                <button type="button" class="btn btn-light fw-bold px-5 py-2 rounded-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() { document.getElementById('liveTime').innerText = new Date().toLocaleString(); }
    setInterval(updateClock, 1000); updateClock();

    document.addEventListener("DOMContentLoaded", function() {
        // 1. QR Code Generator
        var phone = "{{ str_replace(' ', '', $saleReports->phone_number) }}";
        if(phone.trim() !== "") { 
            new QRCode(document.getElementById("qrcode"), { 
                text: "tel:"+phone.trim(), 
                width: 140, 
                height: 140,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            }); 
        }

        // 2. Days Tracker
        const invoiceDateRaw = "{{ $saleReports->invoice_date ? preg_replace('/^00/', '20', $saleReports->invoice_date) : '' }}";
        if(invoiceDateRaw) {
            const invDate = new Date(invoiceDateRaw);
            const diff = Math.ceil((invDate - new Date()) / (1000 * 60 * 60 * 24));
            document.getElementById('countdownNum').innerText = Math.abs(diff);
            document.getElementById('countdownLabel').innerText = diff >= 0 ? "Days Pending" : "Days Archived";
        }

        // 3. Transaction Value Progress Logic
        const price = parseFloat("{{ str_replace(',', '', $saleReports->price) }}") || 0;
        let p = Math.min(Math.round((price / 100000) * 100), 100); 
        document.getElementById('goalBar').style.width = p + "%";
        document.getElementById('goalPercent').innerText = p + "%";
    });
</script>
@endsection