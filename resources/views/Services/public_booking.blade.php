<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>MG Service Booking</title>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #B70101;
            --red-light: #FEF2F2;
            --red-mid: #FECACA;
            --blue: #002663;
            --blue-light: #EFF6FF;
            --dark: #0F172A;
            --mid: #475569;
            --soft: #94A3B8;
            --bg: #F1F5F9;
            --white: #ffffff;
            --border: #E2E8F0;
            --green: #059669;
            --green-bg: #ECFDF5;
            --font: 'DM Sans', 'Noto Sans Khmer', sans-serif;
        }

        body {
            font-family: var(--font);
            background-color: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 24px 16px 40px;
        }

        /* ── Top Nav ── */
        .topbar {
            width: 100%;
            max-width: 1060px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .topbar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-badge {
            width: 36px; height: 36px;
            background: var(--red);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 12px; font-weight: 700; letter-spacing: 0.5px;
        }
        .logo-text { font-size: 14px; font-weight: 600; color: var(--dark); }
        .logo-sub { font-size: 11px; color: var(--soft); font-weight: 400; }
        .topbar-steps {
            display: flex; align-items: center; gap: 6px;
        }
        .step-dot {
            width: 28px; height: 4px; border-radius: 2px;
            background: var(--border);
        }
        .step-dot.done { background: var(--red); }
        .step-dot.active { background: var(--red); opacity: 0.4; }

        /* ── Main Card ── */
        .main-card {
            width: 100%;
            max-width: 1060px;
            background: var(--white);
            border-radius: 16px;
            border: 0.5px solid var(--border);
            overflow: hidden;
            display: flex;
        }

        /* ── Left Panel ── */
        .left-panel {
            flex: 0 0 300px;
            background: var(--blue);
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .left-top { }
        .left-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px;
        }
        .left-icon i { color: var(--red); font-size: 1.3rem; }
        .left-panel h2 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.35;
            margin-bottom: 12px;
        }
        .left-panel p {
            color: #94a3b8;
            font-size: 0.875rem;
            font-weight: 300;
            line-height: 1.7;
        }
        .feature-list {
            list-style: none;
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #cbd5e1;
            font-weight: 300;
        }
        .feat-check {
            width: 20px; height: 20px;
            background: rgba(5, 150, 105, 0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feat-check i { color: var(--green); font-size: 0.65rem; }
        .left-bottom {
            padding-top: 32px;
            border-top: 0.5px solid rgba(255,255,255,0.1);
        }
        .hours-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
        .hours-item {
            display: flex; justify-content: space-between;
            font-size: 0.8rem; color: #94a3b8; padding: 3px 0;
        }
        .hours-item span:last-child { color: #cbd5e1; font-weight: 500; }

        /* ── Right Panel (Form) ── */
        .right-panel {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            max-height: 88vh;
        }
        .right-panel::-webkit-scrollbar { width: 4px; }
        .right-panel::-webkit-scrollbar-track { background: transparent; }
        .right-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* ── Section ── */
        .form-section {
            padding-bottom: 28px;
            margin-bottom: 28px;
            border-bottom: 0.5px dashed var(--border);
        }
        .form-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .section-icon {
            width: 30px; height: 30px;
            background: var(--red-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .section-icon i { color: var(--red); font-size: 0.8rem; }
        .section-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .col-full { grid-column: 1 / -1; }

        /* ── Field ── */
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field-label {
            font-size: 11px;
            font-weight: 500;
            color: var(--soft);
            letter-spacing: 0.3px;
        }
        .field-label .req { color: var(--red); margin-left: 2px; }

        .field input,
        .field select,
        .field textarea {
            font-family: var(--font);
            font-size: 13px;
            font-weight: 500;
            color: var(--dark);
            background: var(--bg);
            border: 0.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
            outline: none;
            transition: border-color 0.15s, background 0.15s;
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
        }
        .field input::placeholder,
        .field textarea::placeholder { color: var(--soft); font-weight: 400; }
        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--dark);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(15,23,42,0.06);
        }
        .field textarea { resize: none; }

        .select-wrap { position: relative; }
        .select-wrap select { padding-right: 32px; cursor: pointer; }
        .select-wrap::after {
            content: '';
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            width: 0; height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid var(--soft);
            pointer-events: none;
        }

        /* ── Date row ── */
        .date-row { display: flex; gap: 12px; }
        .date-badge {
            display: flex; align-items: center; gap: 8px;
            background: var(--red-light);
            border: 0.5px solid var(--red-mid);
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            flex: 1;
        }
        .date-badge i { color: var(--red); font-size: 0.85rem; }
        .date-badge-text { font-size: 12px; color: var(--red); font-weight: 600; }
        .date-badge-sub { font-size: 10px; color: #f87171; }
        .date-input-hidden {
            position: absolute;
            width: 0; height: 0;
            opacity: 0;
            pointer-events: none;
        }

        /* ── Slot Grid ── */
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 10px;
        }
        .slot-card { cursor: pointer; position: relative; }
        .slot-card input { position: absolute; opacity: 0; width: 0; height: 0; }
        .slot-content {
            display: flex; flex-direction: column; align-items: center;
            padding: 10px 4px;
            border: 0.5px solid var(--border);
            border-radius: 8px;
            background: var(--white);
            transition: all 0.15s;
        }
        .slot-card:not(.disabled):hover .slot-content {
            border-color: var(--blue);
            background: var(--blue-light);
        }
        .slot-card input:checked + .slot-content {
            background: var(--blue);
            border-color: var(--blue);
        }
        .slot-card input:checked + .slot-content .slot-time { color: #fff; }
        .slot-card input:checked + .slot-content .slot-status { color: rgba(255,255,255,0.65); }
        .slot-card.disabled .slot-content {
            background: var(--bg); border-color: transparent; cursor: not-allowed;
        }
        .slot-time { font-size: 11px; font-weight: 700; color: var(--dark); }
        .slot-status { font-size: 10px; font-weight: 600; margin-top: 3px; }
        .slot-status.avail { color: var(--green); }
        .slot-status.full { color: #f87171; }
        .slot-status.past { color: var(--soft); }
        .slot-card.disabled .slot-time { color: var(--soft); text-decoration: line-through; }

        .slot-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: var(--soft);
        }
        .slot-loading {
            grid-column: 1 / -1;
            text-align: center;
            padding: 20px;
            color: var(--blue);
            font-size: 13px;
        }

        /* ── Submit ── */
        .btn-submit {
            width: 100%;
            background: var(--dark);
            color: #fff;
            border: none;
            border-radius: 10px;
            height: 50px;
            font-family: var(--font);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.2px;
        }
        .btn-submit:hover:not(:disabled) { background: #1e293b; }
        .btn-submit:active:not(:disabled) { transform: scale(0.99); }
        .btn-submit:disabled { background: var(--border); color: var(--soft); cursor: not-allowed; }

        /* ── SweetAlert override ── */
        .swal2-popup { font-family: var(--font) !important; border-radius: 14px !important; }

        /* ── Footer ── */
        .footer { margin-top: 20px; font-size: 12px; color: var(--soft); text-align: center; }

        /* ── Ticket ── */
        .ticket-wrap {
            background: var(--white); border-radius: 12px;
            border: 0.5px solid var(--border); text-align: left;
            font-family: var(--font);
        }
        .ticket-head {
            padding: 20px; text-align: center;
            background: var(--green-bg);
            border-radius: 12px 12px 0 0;
            border-bottom: 0.5px solid #d1fae5;
        }
        .ticket-body { padding: 16px 20px; }
        .ticket-row {
            display: flex; justify-content: space-between;
            align-items: flex-start;
            padding: 5px 0;
            font-size: 0.85rem;
        }
        .ticket-row .tl { color: var(--soft); min-width: 80px; }
        .ticket-row .tv { font-weight: 600; color: var(--dark); text-align: right; }
        .ticket-divider { height: 0.5px; background: var(--border); margin: 10px 0; }
        .ref-badge {
            display: inline-block;
            background: var(--red-light); color: var(--red);
            padding: 3px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700; margin-top: 6px;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            body { padding: 0 0 40px; }
            .topbar { padding: 16px 16px 0; margin-bottom: 12px; }
            .main-card { flex-direction: column; border-radius: 0; border: none; }
            .left-panel { flex: none; padding: 24px 20px; }
            .feature-list { display: none; }
            .left-bottom { display: none; }
            .left-panel h2 { font-size: 1.2rem; }
            .right-panel { max-height: none; padding: 20px 16px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr 1fr; }
            .slot-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 480px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .slot-grid { grid-template-columns: repeat(3, 1fr); }
            .topbar-steps { display: none; }
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="topbar-logo">
            <div class="logo-badge">MG</div>
            <div>
                <div class="logo-text">MG Cambodia</div>
                <div class="logo-sub">Service Booking Portal</div>
            </div>
        </div>
        <div class="topbar-steps">
            <div class="step-dot done"></div>
            <div class="step-dot done"></div>
            <div class="step-dot active"></div>
            <div class="step-dot"></div>
        </div>
    </div>

    <div class="main-card">

        <!-- Left Panel -->
        <div class="left-panel">
            <div class="left-top">
                <div class="left-icon"><i class="fas fa-tools"></i></div>
                <h2>Book Your<br>Service Today</h2>
                <p>Fast, easy, and reliable service booking at MG Cambodia's certified service centers.</p>
                <ul class="feature-list">
                    <li>
                        <div class="feat-check"><i class="fas fa-check"></i></div>
                        Certified MG technicians
                    </li>
                    <li>
                        <div class="feat-check"><i class="fas fa-check"></i></div>
                        100% genuine factory parts
                    </li>
                    <li>
                        <div class="feat-check"><i class="fas fa-check"></i></div>
                        Fast turnaround time
                    </li>
                    <li>
                        <div class="feat-check"><i class="fas fa-check"></i></div>
                        Nationwide branch network
                    </li>
                </ul>
            </div>
            <div class="left-bottom">
                <div class="hours-label">HQ Working Hours</div>
                <div class="hours-item"><span>Mon – Sat</span><span>07:00 – 17:00</span></div>
                <div class="hours-item"><span>Sunday</span><span style="color:#f87171">Closed</span></div>
            
                <br>
            
                <div class="hours-label">Branch Working Hours</div>
                <div class="hours-item"><span>Mon – Sat</span><span>08:00 – 17:00</span></div>
                <div class="hours-item"><span>Sunday</span><span style="color:#f87171">Closed</span></div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <form id="bookingForm" action="{{ route('services.public_store') }}" method="POST">
                @csrf

                <!-- Step 1: Customer -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="far fa-user"></i></div>
                        <div class="section-title">Customer Information</div>
                    </div>
                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label">Full Name <span class="req">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" placeholder="e.g. សុខ ដារ៉ា" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Phone Number <span class="req">*</span></label>
                            <input type="tel" name="contact" id="contact" placeholder="012 345 678" required>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Vehicle & Service -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-car-side"></i></div>
                        <div class="section-title">Vehicle & Service</div>
                    </div>
                    <div class="grid-3" style="margin-bottom:12px">
                        <div class="field">
                            <label class="field-label">Model <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select name="model" id="model" required>
                                    <option value="" disabled selected>Select model</option>
                                    <optgroup label="SUV">
                                        <option value="MG ZS 1.5">MG ZS 1.5</option>
                                        <option value="MG ZS 1.0 / 1.5">MG ZS 1.0 / 1.5</option>
                                        <option value="MG ZS Hybrid">MG ZS Hybrid</option>
                                        <option value="MG ZS EV">MG ZS EV</option>
                                        <option value="MG ZS32">MG ZS32</option>
                                        <option value="MG HS">MG HS</option>
                                        <option value="MG HS 2026 Hybrid">MG HS 2026 Hybrid</option>
                                        <option value="MG RX5 2017–2021">MG RX5 2017–2021</option>
                                        <option value="MG RX5 Reborn / Sport">MG RX5 Reborn / Sport</option>
                                        <option value="MG RX5 MCE">MG RX5 MCE</option>
                                        <option value="MG RX8">MG RX8</option>
                                        <option value="MG RX9">MG RX9</option>
                                        <option value="MG GS 2.0">MG GS 2.0</option>
                                        <option value="MG D60">MG D60</option>
                                    </optgroup>
                                    <optgroup label="Sedan & Sport">
                                        <option value="MG GT 2024">MG GT 2024</option>
                                        <option value="MG GT 2026">MG GT 2026</option>
                                        <option value="MG 7 1.5T">MG 7 1.5T</option>
                                        <option value="MG 7 2.0T">MG 7 2.0T</option>
                                        <option value="MG 6">MG 6</option>
                                        <option value="MG 350">MG 350</option>
                                        <option value="MG 550">MG 550</option>
                                        <option value="MG 750">MG 750</option>
                                        <option value="MG Cyberster">MG Cyberster</option>
                                    </optgroup>
                                    <optgroup label="Electric (EV)">
                                        <option value="MG 4 Urban">MG 4 Urban</option>
                                        <option value="MG 4 (Comfort)">MG 4 (Comfort)</option>
                                        <option value="MG 4 (Luxury)">MG 4 (Luxury)</option>
                                        <option value="MG Marvel R">MG Marvel R</option>
                                    </optgroup>
                                    <optgroup label="Maxus & MIFA">
                                        <option value="Maxus G50">Maxus G50</option>
                                        <option value="MG G50 PHEV">MG G50 PHEV</option>
                                        <option value="Maxus T60">Maxus T60</option>
                                        <option value="MG T70">MG T70</option>
                                        <option value="Maxus T90">Maxus T90</option>
                                        <option value="Maxus V80">Maxus V80</option>
                                        <option value="MIFA 7">MIFA 7</option>
                                        <option value="MIFA 9">MIFA 9</option>
                                    </optgroup>
                                    <optgroup label="Leapmotor & IM">
                                        <option value="IM 5">IM 5</option>
                                        <option value="IM 6">IM 6</option>
                                        <option value="LPM T03">LPM T03</option>
                                        <option value="LPM B10">LPM B10</option>
                                        <option value="LPM C10 (CH)">LPM C10 (CH)</option>
                                        <option value="LPM C10 (Global)">LPM C10 (Global)</option>
                                        <option value="LPM C16 (Front)">LPM C16 (Front)</option>
                                        <option value="LPM C16 (Rear)">LPM C16 (Rear)</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-label">Plate No. <span class="req">*</span></label>
                            <input type="text" name="plate" id="plate" class="text-uppercase" placeholder="2AB-1234" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Mileage (km) <span class="req">*</span></label>
                            <input type="number" name="mileage" id="mileage" placeholder="25000" required>
                        </div>
                    </div>
                    <div class="grid-2" style="margin-bottom:12px">
                        <div class="field col-full">
                            <label class="field-label">Service Type <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select name="service_type" id="service_type" required>
                                    <option value="" disabled selected>Select service type</option>
                                    <optgroup label="Inspection & Repair">
                                        <option value="Engine Malfunction">Engine Check | លោតឆេកម៉ាស៊ីន</option>
                                        <option value="General Repair">General Repair | ជួសជុលទូទៅ</option>
                                        <option value="General Inspection">General Inspection | ត្រួតពិនិត្យ</option>
                                        <option value="Check Vibration">Check Vibration | ត្រួតពិនិត្យរំញ័រ</option>
                                        <option value="Check Noise">Check Noise | ត្រួតពិនិត្យសំឡេង</option>
                                    </optgroup>
                                    <optgroup label="Mileage-Based Service">
                                        <option value="Interval Maintenance">Interval Service | ថែទាំតាមកាលកំណត់</option>
                                        <option value="Service 20000KM">Service 20,000 km</option>
                                        <option value="Service 40000KM">Service 40,000 km</option>
                                        <option value="Service 50000KM">Service 50,000 km</option>
                                        <option value="Service 60000KM">Service 60,000 km</option>
                                    </optgroup>
                                    <optgroup label="Other Services">
                                        <option value="Body and Paint">Body & Paint | តោន/ថ្នាំ</option>
                                        <option value="Warranty">Warranty | ការធានា</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Remarks (Optional)</label>
                        <textarea name="remark" id="remark" rows="2" placeholder="Describe any additional issues (if any)..."></textarea>
                    </div>
                </div>

                <!-- Step 3: Branch & Date & Time -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="far fa-calendar-check"></i></div>
                        <div class="section-title">Branch & Schedule</div>
                    </div>

                    <div class="grid-2" style="margin-bottom:16px">
                        <div class="field">
                            <label class="field-label">Branch Location <span class="req">*</span></label>
                            <div class="select-wrap">
                                <select name="location" id="location" required>
                                    <option value="" disabled selected>Select branch</option>
                                    <optgroup label="Phnom Penh">
                                        <option value="13">Head Office (ការិយាល័យកណ្តាល)</option>
                                        <option value="11">Street 50M (ផ្លូវ ៥០ ម)</option>
                                    </optgroup>
                                    <optgroup label="Province">
                                        <option value="12">Siem Reap (សៀមរាប)</option>
                                        <option value="10">Battambang (បាត់ដំបង)</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label class="field-label">Booking Date <span class="req">*</span></label>
                            <input type="date" name="booking_date" id="booking_date"
                                   min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="field-label" style="margin-bottom:8px">
                        Available Time Slots <span class="req">*</span>
                    </div>
                    <div id="slot-container" class="slot-grid">
                        <div class="slot-empty">Please select a branch and date first.</div>
                    </div>
                </div>

                <button type="submit" id="btnSubmit" class="btn-submit">
                    <i class="far fa-paper-plane"></i>
                    Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <div class="footer">Designed by Pech &copy; ២០២៦</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function fetchAvailableSlots() {
        const dateInput = $('#booking_date').val();
        const loc = $('#location').val();

        if (!dateInput || !loc) {
            $('#slot-container').html('<div class="slot-empty">Please select a branch and date first.</div>');
            return;
        }

        const selectedDate = new Date(dateInput);
        if (selectedDate.getDay() === 0) {
            $('#slot-container').html('<div class="slot-empty" style="color:#f87171">Closed on Sundays. Please choose another day.</div>');
            $('#btnSubmit').prop('disabled', true);
            return;
        }
        $('#btnSubmit').prop('disabled', false);

        $('#slot-container').html('<div class="slot-loading"><i class="fas fa-circle-notch fa-spin"></i> Loading slots...</div>');

        $.get("{{ route('slots.check') }}", { date: dateInput, location: loc }, function(data) {

            if (data && data.isHoliday) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Public Holiday',
                    text: data.message,
                    confirmButtonColor: '#B70101',
                    confirmButtonText: 'Choose Another Date',
                    allowOutsideClick: false,
                }).then(() => {
                    $('#booking_date').val('');
                    $('#slot-container').html('<div class="slot-empty">Please select another date.</div>');
                    $('#btnSubmit').prop('disabled', true);
                });
                return;
            }

            if (!data || data.length === 0) {
                $('#slot-container').html('<div class="slot-empty" style="color:#f87171">No available slots for this date.</div>');
                return;
            }

            const now = new Date();
            const todayStr = now.getFullYear() + '-' +
                String(now.getMonth() + 1).padStart(2, '0') + '-' +
                String(now.getDate()).padStart(2, '0');

            let html = '';
            data.forEach(function(slot) {
                let isPast = false;
                if (dateInput === todayStr) {
                    const [time, mod] = slot.time.split(' ');
                    let [h, m] = time.split(':');
                    if (h === '12') h = '00';
                    if (mod === 'PM') h = parseInt(h) + 12;
                    const slotDt = new Date();
                    slotDt.setHours(parseInt(h), parseInt(m), 0, 0);
                    if (slotDt <= now) isPast = true;
                }

                const isDisabled = (slot.isFull || isPast);
                const disabledAttr = isDisabled ? 'disabled' : '';
                const cardClass = isDisabled ? 'slot-card disabled' : 'slot-card';

                let statusHtml = '';
                if (isPast) {
                    statusHtml = '<span class="slot-status past">Passed</span>';
                } else if (slot.isFull) {
                    statusHtml = '<span class="slot-status full">Full</span>';
                } else {
                    statusHtml = '<span class="slot-status avail">' + slot.remaining + ' left</span>';
                }

                html += '<label class="' + cardClass + '">' +
                    '<input type="radio" name="booking_time" value="' + slot.time + '" ' + disabledAttr + ' required>' +
                    '<div class="slot-content">' +
                    '<span class="slot-time">' + slot.time + '</span>' +
                    statusHtml +
                    '</div></label>';
            });

            $('#slot-container').html(html);

        }).fail(function() {
            $('#slot-container').html('<div class="slot-empty" style="color:#f87171">Server connection error. Please try again.</div>');
        });
    }

    $(document).ready(function() {
        fetchAvailableSlots();

        $('#booking_date, #location').on('change', function() {
            const dateInput = $('#booking_date').val();
            const today = new Date().toISOString().split('T')[0];
            if (dateInput < today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Date',
                    text: 'Please select today or a future date.',
                    confirmButtonColor: '#002663'
                });
                $('#booking_date').val(today);
            }
            fetchAvailableSlots();
        });

        $('#plate').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();

            if (!$("input[name='booking_time']:checked").val()) {
                Swal.fire({
                    icon: 'info',
                    title: 'Select a Time Slot',
                    text: 'Please choose your preferred time slot.',
                    confirmButtonColor: '#002663'
                });
                return;
            }

            const $btn = $('#btnSubmit');
            $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Submitting...');

            Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        const name    = $('#customer_name').val();
                        const phone   = $('#contact').val();
                        const model   = $('#model').val();
                        const plate   = $('#plate').val();
                        const service = $('#service_type').val();
                        const branch  = $('#location option:selected').text();
                        const date    = $('#booking_date').val();
                        const time    = $("input[name='booking_time']:checked").val();

                        const ticket = `
                        <div class="ticket-wrap">
                            <div class="ticket-head">
                                <div style="width:52px;height:52px;background:#ECFDF5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                                    <i class="fas fa-check" style="color:#059669;font-size:1.4rem;"></i>
                                </div>
                                <div style="font-size:15px;font-weight:700;color:#0F172A;">Booking Confirmed!</div>
                                <div class="ref-badge">#MG-${response.booking_id}</div>
                            </div>
                            <div class="ticket-body">
                                <div class="ticket-row"><span class="tl">Name</span><span class="tv">${name}</span></div>
                                <div class="ticket-row"><span class="tl">Phone</span><span class="tv">${phone}</span></div>
                                <div class="ticket-row"><span class="tl">Vehicle</span><span class="tv">${model} · ${plate}</span></div>
                                <div class="ticket-row"><span class="tl">Service</span><span class="tv" style="color:#B70101">${service}</span></div>
                                <div class="ticket-divider"></div>
                                <div class="ticket-row"><span class="tl">Date</span><span class="tv">${date}</span></div>
                                <div class="ticket-row"><span class="tl">Time</span><span class="tv" style="color:#002663">${time}</span></div>
                                <div class="ticket-row"><span class="tl">Branch</span><span class="tv" style="font-size:0.8rem">${branch}</span></div>
                                <div style="margin-top:12px;font-size:11px;color:#94A3B8;text-align:center;">Please screenshot this as reference.</div>
                            </div>
                        </div>`;

                        Swal.fire({
                            html: ticket,
                            confirmButtonText: 'Done',
                            confirmButtonColor: '#0F172A',
                            allowOutsideClick: false,
                            width: '380px',
                            customClass: { popup: 'swal2-popup' }
                        }).then(() => { window.location.reload(); });
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="far fa-paper-plane"></i> Confirm Booking');
                    let msg = 'Technical error. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({ icon: 'error', title: 'Failed', text: msg, confirmButtonColor: '#B70101' });
                }
            });
        });
    });
</script>
</body>
</html>