<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="dynamic-token" content="{{ session('booking_token') }}">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta name="robots" content="noindex, nofollow">

    <title>MG Service Booking</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@300;400;700&family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Brand */
            --red: #B70101;
            --red-dark: #7E0000;
            --red-glow: rgba(183, 1, 1, 0.28);
            --navy: #002663;
            --navy-dark: #001433;

            /* Graphite / workshop tones */
            --graphite-900: #7E0000;
            --graphite-800: #002663;
            --graphite-700: #001433;
            --graphite-line: rgba(255, 255, 255, 0.08);

            /* Paper / surfaces */
            --paper: #EEF0F1;
            --card: #FFFFFF;
            --line: #DCE0E3;
            --line-strong: #BFC6CC;

            /* Text */
            --ink: #14171C;
            --steel: #5B6572;
            --steel-soft: #8A93A0;

            /* Status */
            --green: #147A45;
            --green-bg: #E7F5EC;
            --amber: #B4530A;
            --amber-bg: #FBEEE1;

            /* Dashboard readout */
            --lcd: #21C186;
            --lcd-dim: #0B1F17;

            --font-display: 'Rajdhani', 'Battambang', sans-serif;
            --font-body: 'Inter', 'Battambang', sans-serif;
            --font-mono: 'IBM Plex Mono', 'Battambang', monospace;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background-color: var(--paper);
            background-image:
                repeating-linear-gradient(0deg, rgba(0,38,99,0.05) 0px, rgba(0,38,99,0.05) 1px, transparent 1px, transparent 34px),
                repeating-linear-gradient(90deg, rgba(0,38,99,0.05) 0px, rgba(0,38,99,0.05) 1px, transparent 1px, transparent 34px);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px 40px;
        }

        /* =========================================================
           TOP BAR
        ========================================================= */
        .topbar {
            width: 100%;
            max-width: 1080px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .topbar-logo { display: flex; align-items: center; gap: 11px; }

        .logo-badge {
            width: 40px; height: 40px;
            background: var(--red);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-family: var(--font-display); font-size: 14px; font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px var(--red-glow);
        }

        .logo-text { font-family: var(--font-display); font-size: 15px; font-weight: 700; color: var(--ink); letter-spacing: 0.3px; }
        .logo-sub { margin-top: 1px; font-size: 11px; color: var(--steel-soft); font-weight: 400; letter-spacing: 0.3px; }

        .gauge {
            display: flex; align-items: center; gap: 10px;
            font-family: var(--font-mono);
        }
        .gauge-track { display: flex; align-items: center; gap: 4px; }
        .gauge-seg {
            width: 26px; height: 6px;
            border-radius: 1px;
            background: var(--line-strong);
        }
        .gauge-seg.done { background: var(--navy); }
        .gauge-seg.active {
            background: var(--red);
            box-shadow: 0 0 0 2px var(--red-glow);
        }
        .gauge-readout {
            font-size: 11px; color: var(--steel); letter-spacing: 0.5px;
            border-left: 1px solid var(--line-strong); padding-left: 10px;
        }
        .gauge-readout b { color: var(--ink); font-weight: 600; }

        /* =========================================================
           MAIN CARD & PANELS
        ========================================================= */
        .main-card {
            width: 100%; max-width: 1080px;
            background: var(--card);
            border-radius: 6px;
            border: 1px solid var(--line);
            overflow: hidden; display: flex;
            box-shadow: 0 16px 40px rgba(18, 21, 26, 0.08);
        }

        .left-panel {
            flex: 0 0 300px;
            position: relative;
            background: linear-gradient(160deg, var(--graphite-800), var(--graphite-900));
            display: flex; flex-direction: column;
        }

        .hazard-strip {
            height: 8px; width: 100%; flex-shrink: 0;
            background: repeating-linear-gradient(135deg, var(--red) 0 10px, var(--red-dark) 10px 20px);
        }

        .left-panel-inner {
            flex: 1;
            padding: 28px 30px 30px;
            display: flex; flex-direction: column; justify-content: space-between;
        }

        .left-eyebrow {
            font-family: var(--font-mono); font-size: 10px; letter-spacing: 1.4px; text-transform: uppercase;
            color: #E8896A; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
        }
        .left-eyebrow::before { content: ''; width: 14px; height: 1px; background: #E8896A; display: inline-block; }

        .left-icon {
            width: 44px; height: 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--graphite-line);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .left-icon i { color: #fff; font-size: 1.05rem; }

        .left-panel h2 { font-family: var(--font-display); color: #fff; font-size: 1.6rem; font-weight: 700; line-height: 1.3; margin-bottom: 10px; letter-spacing: 0.2px; }
        .left-panel p { color: #9AA3AF; font-size: 0.85rem; font-weight: 400; line-height: 1.7; }

        .feature-list { list-style: none; margin-top: 28px; display: flex; flex-direction: column; gap: 13px; }
        .feature-list li { display: flex; align-items: center; gap: 10px; font-size: 0.82rem; color: #C7CDD5; font-weight: 400; }

        .feat-check {
            width: 19px; height: 19px;
            background: rgba(33, 193, 134, 0.12);
            border: 1px solid rgba(33, 193, 134, 0.25);
            border-radius: 3px; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feat-check i { color: var(--lcd); font-size: 0.58rem; }

        .left-bottom { padding-top: 26px; border-top: 1px solid var(--graphite-line); }
        .hours-label { font-family: var(--font-mono); font-size: 10px; color: #6B7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .hours-item { display: flex; justify-content: space-between; font-size: 0.79rem; color: #9AA3AF; padding: 3px 0; font-family: var(--font-mono); }
        .hours-item span:last-child { color: #D5D9DF; font-weight: 500; }

        .right-panel { flex: 1; padding: 38px 42px; overflow-y: auto; max-height: 88vh; }
        .right-panel::-webkit-scrollbar { width: 5px; }
        .right-panel::-webkit-scrollbar-track { background: transparent; }
        .right-panel::-webkit-scrollbar-thumb { background: var(--line-strong); border-radius: 5px; }

        /* =========================================================
           FORM SECTIONS & GRID
        ========================================================= */
        .form-section { padding-bottom: 26px; margin-bottom: 26px; border-bottom: 1px dashed var(--line-strong); }
        .form-section:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }

        .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .section-index {
            font-family: var(--font-mono); font-size: 11px; font-weight: 600; color: var(--red);
            border: 1px solid var(--red); border-radius: 2px; width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .section-title { font-family: var(--font-display); font-size: 0.95rem; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: 0.6px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .col-full { grid-column: 1 / -1; }

        /* =========================================================
           FIELD SETTINGS
        ========================================================= */
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field-label { font-size: 11px; font-weight: 600; color: var(--steel); letter-spacing: 0.3px; text-transform: uppercase; }
        .field-label .req { color: var(--red); margin-left: 2px; }

        .field input, .field select, .field textarea {
            font-family: var(--font-body); font-size: 13px; font-weight: 500;
            color: var(--ink); background: var(--paper);
            border: 1px solid var(--line); border-radius: 4px;
            padding: 10px 12px; outline: none;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
            width: 100%; appearance: none; -webkit-appearance: none;
        }
        .field input::placeholder, .field textarea::placeholder { color: var(--steel-soft); font-weight: 400; }
        .field input:hover, .field select:hover, .field textarea:hover { border-color: var(--line-strong); }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--navy); background: var(--card);
            box-shadow: 0 0 0 3px rgba(0, 38, 99, 0.08);
        }
        .field input:invalid:not(:placeholder-shown) { border-color: #E0A0A0; }
        .field textarea { resize: vertical; min-height: 70px; }

        #plate, #mileage { font-family: var(--font-mono); letter-spacing: 0.5px; }

        .select-wrap { position: relative; }
        .select-wrap select { padding-right: 34px; cursor: pointer; }
        .select-wrap::after {
            content: ''; position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            width: 0; height: 0;
            border-left: 4px solid transparent; border-right: 4px solid transparent; border-top: 5px solid var(--steel-soft);
            pointer-events: none;
        }

        /* =========================================================
           DASHBOARD READOUT (estimated duration)
        ========================================================= */
        .lcd-panel {
            background: linear-gradient(160deg, var(--graphite-800), var(--lcd-dim));
            border: 1px solid var(--graphite-line);
            border-radius: 5px;
            padding: 13px 16px;
            margin-bottom: 16px;
            align-items: center;
            gap: 14px;
        }
        .lcd-icon {
            width: 34px; height: 34px; flex-shrink: 0;
            border: 1px solid rgba(33, 193, 134, 0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .lcd-icon i { color: var(--lcd); font-size: 0.95rem; }
        .lcd-label {
            display: block; font-family: var(--font-mono); font-size: 10px; letter-spacing: 1.1px;
            text-transform: uppercase; color: #7C8894; margin-bottom: 3px;
        }
        .lcd-value {
            font-family: var(--font-mono); font-weight: 600; font-size: 15px; color: var(--lcd);
            letter-spacing: 0.4px;
        }
        .lcd-note { font-size: 10.5px; color: #6B7684; margin-top: 2px; }

        /* =========================================================
           SLOT GRID
        ========================================================= */
        .slot-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 10px; }
        .slot-card { cursor: pointer; position: relative; }
        .slot-card input { position: absolute; opacity: 0; width: 0; height: 0; }
        .slot-content {
            position: relative;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            min-height: 58px; padding: 9px 4px;
            border: 1px solid var(--line); border-radius: 4px; background: var(--card);
            background-image: radial-gradient(circle, var(--paper) 1.5px, transparent 1.5px);
            background-size: 9px 9px;
            background-position: top center;
            background-repeat: repeat-x;
            transition: all 0.15s ease;
            overflow: hidden;
        }
        .slot-card:not(.disabled):hover .slot-content { border-color: var(--navy); background-color: #F2F6FC; transform: translateY(-1px); }
        .slot-card input:checked + .slot-content { background-color: var(--navy); border-color: var(--navy); box-shadow: 0 4px 10px rgba(0, 38, 99, 0.18); }
        .slot-card input:checked + .slot-content .slot-time { color: #fff; }
        .slot-card input:checked + .slot-content .slot-status { color: rgba(255, 255, 255, 0.72); }
        .slot-card.disabled .slot-content { background-color: var(--paper); border-color: transparent; cursor: not-allowed; opacity: 0.75; }

        .slot-time { font-family: var(--font-mono); font-size: 11px; font-weight: 700; color: var(--ink); }
        .slot-status { font-size: 10px; font-weight: 600; margin-top: 3px; }
        .slot-status.avail { color: var(--green); }
        .slot-status.full { color: #C1302F; }
        .slot-status.past { color: var(--steel-soft); }
        .slot-card.disabled .slot-time { color: var(--steel-soft); text-decoration: line-through; }

        .slot-empty, .slot-loading { grid-column: 1 / -1; text-align: center; padding: 20px; font-size: 12px; color: var(--steel-soft); }
        .slot-loading { color: var(--navy); }

        /* =========================================================
           SUBMIT & BUTTONS
        ========================================================= */
        .btn-submit {
            width: 100%; background: var(--red); color: #fff; border: none; border-radius: 4px;
            min-height: 50px; padding: 0 18px; font-family: var(--font-display); font-size: 15px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.6px;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 9px;
            margin-top: 24px; transition: background 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }
        .btn-submit:hover:not(:disabled) { background: var(--red-dark); box-shadow: 0 6px 15px var(--red-glow); }
        .btn-submit:active:not(:disabled) { transform: scale(0.99); }
        .btn-submit:disabled { background: var(--line); color: var(--steel-soft); cursor: not-allowed; box-shadow: none; }

        .footer { margin-top: 20px; font-size: 12px; color: var(--steel-soft); text-align: center; font-family: var(--font-mono); }

        /* =========================================================
           TICKET
        ========================================================= */
        .ticket-wrap { background: var(--card); border-radius: 6px; border: 1px solid var(--line); text-align: left; font-family: var(--font-body); overflow: hidden; }
        .ticket-head {
            padding: 20px; text-align: center; background: var(--green-bg);
            border-bottom: 1px dashed #A9D9BB;
            background-image: radial-gradient(circle, var(--card) 2px, transparent 2px);
            background-size: 12px 12px; background-position: bottom center; background-repeat: repeat-x;
        }
        .ticket-body { padding: 18px 20px; }
        .ticket-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; padding: 6px 0; font-size: 0.85rem; }
        .ticket-row .tl { color: var(--steel); min-width: 90px; }
        .ticket-row .tv { font-weight: 600; color: var(--ink); text-align: right; word-break: break-word; font-family: var(--font-mono); }
        .ticket-divider { height: 1px; background: var(--line); margin: 10px 0; }
        .ref-badge {
            display: inline-block; background: var(--graphite-900); color: #fff; padding: 4px 14px;
            border-radius: 2px; font-family: var(--font-mono); font-size: 12px; font-weight: 600;
            letter-spacing: 0.6px; margin-top: 8px;
        }

        /* =========================================================
           HONEYPOT
        ========================================================= */
        .hp-field { position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; }

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 900px) {
            body { padding: 0 0 40px; }
            .topbar { padding: 16px 16px 0; margin-bottom: 12px; }
            .gauge-readout { display: none; }
            .main-card { flex-direction: column; border-radius: 0; border-left: none; border-right: none; }
            .left-panel { flex: none; }
            .left-panel-inner { padding: 22px 20px; }
            .feature-list, .left-bottom { display: none; }
            .left-panel h2 { font-size: 1.25rem; }
            .right-panel { max-height: none; padding: 24px 16px; }
            .grid-2, .grid-3 { grid-template-columns: 1fr 1fr; }
            .slot-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 480px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .slot-grid { grid-template-columns: repeat(3, 1fr); }
            .right-panel { padding: 20px 14px; }
            .ticket-row { font-size: 0.82rem; }
        }
    </style>
</head>
<body>

    {{-- =========================================================
         TOP BAR
    ========================================================== --}}
    <div class="topbar">
        <div class="topbar-logo">
            <div class="logo-badge">MG</div>
            <div>
                <div class="logo-text">MG Cambodia</div>
                <div class="logo-sub">Service Booking Portal</div>
            </div>
        </div>
        <div class="gauge">
            <div class="gauge-track">
                <div class="gauge-seg done"></div>
                <div class="gauge-seg done"></div>
                <div class="gauge-seg active"></div>
                <div class="gauge-seg"></div>
            </div>
            <div class="gauge-readout">STEP <b>03</b>/04</div>
        </div>
    </div>

    {{-- =========================================================
         MAIN CARD
    ========================================================== --}}
    <div class="main-card">

        {{-- =====================================================
             LEFT PANEL
        ====================================================== --}}
        <div class="left-panel">
            <div class="hazard-strip"></div>
            <div class="left-panel-inner">
                <div>
                    <div class="left-eyebrow">Certified Workshop</div>
                    <div class="left-icon">
                        <i class="fas fa-gauge-high"></i>
                    </div>
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
                    <div class="hours-item">
                        <span>Mon – Sat</span>
                        <span>07:00 – 17:00</span>
                    </div>
                    <div class="hours-item">
                        <span>Sunday</span>
                        <span style="color:#E8896A;">Closed</span>
                    </div>
                    <br>
                    <div class="hours-label">Branch Working Hours</div>
                    <div class="hours-item">
                        <span>Mon – Sat</span>
                        <span>08:00 – 17:00</span>
                    </div>
                    <div class="hours-item">
                        <span>Sunday</span>
                        <span style="color:#E8896A;">Closed</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- =====================================================
             RIGHT PANEL
        ====================================================== --}}
        <div class="right-panel">
            <form id="bookingForm" action="{{ route('services.public_store') }}" method="POST" autocomplete="off">
                @csrf

                {{-- HONEYPOT --}}
                <div class="hp-field" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                {{-- CUSTOMER INFO --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-index">01</div>
                        <div class="section-title">Customer Information</div>
                    </div>
                    <div class="grid-2">
                        <div class="field">
                            <label class="field-label" for="customer_name">
                                Full Name <span class="req">*</span>
                            </label>
                            <input type="text" name="customer_name" id="customer_name" maxlength="100" autocomplete="name" placeholder="e.g. សុខ ដារ៉ា" required>
                        </div>
                        <div class="field">
                            <label class="field-label" for="contact">
                                Phone Number <span class="req">*</span>
                            </label>
                            <input type="tel" name="contact" id="contact" maxlength="20" inputmode="tel" autocomplete="tel" placeholder="012 345 678" required>
                        </div>
                    </div>
                </div>

                {{-- VEHICLE & SERVICE --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-index">02</div>
                        <div class="section-title">Vehicle & Service</div>
                    </div>
                    
                    <div class="grid-3" style="margin-bottom:12px;">
                        {{-- Model --}}
                        <div class="field">
                            <label class="field-label" for="model">
                                Vehicle Model <span class="req">*</span>
                            </label>
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

                        {{-- Plate --}}
                        <div class="field">
                            <label class="field-label" for="plate">
                                License Plate <span class="req">*</span>
                            </label>
                            <input type="text" name="plate" id="plate" maxlength="30" autocomplete="off" style="text-transform:uppercase;" placeholder="2AB-1234" required>
                        </div>

                        {{-- Mileage --}}
                        <div class="field">
                            <label class="field-label" for="mileage">
                                Mileage (km) <span class="req">*</span>
                            </label>
                            <input type="number" name="mileage" id="mileage" min="0" max="2000000" step="1" inputmode="numeric" placeholder="25000" required>
                        </div>
                    </div>

                    {{-- Service Type --}}
                    <div class="grid-2" style="margin-bottom:12px;">
                        <div class="field col-full">
                            <label class="field-label" for="service_type">
                                Service Type <span class="req">*</span>
                            </label>
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
                                        <option value="Service 80000KM">Service 80,000 km</option>
                                        <option value="Service 100000KM">Service 100,000 km</option>
                                    </optgroup>
                                    <optgroup label="Other Services">
                                        <option value="Body and Paint">Body & Paint | តោន/ថ្នាំ</option>
                                        <option value="Warranty">Warranty | ការធានា</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Estimated duration dashboard readout --}}
                    <div id="est-duration-box" class="lcd-panel" style="display:none;">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <div class="lcd-icon">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                            <div>
                                <span class="lcd-label">Estimated Service Time</span>
                                <span id="duration-val" class="lcd-value">...</span>
                                <div class="lcd-note">Actual duration may vary</div>
                            </div>
                        </div>
                    </div>

                    {{-- Remark --}}
                    <div class="field">
                        <label class="field-label" for="remark">
                            Customer Remarks <span style="font-weight:400; text-transform:none;">(Optional)</span>
                        </label>
                        <textarea name="remark" id="remark" maxlength="1000" rows="3" placeholder="Describe any additional issues (if any)..."></textarea>
                    </div>
                </div>

                {{-- =================================================
                     BRANCH & SCHEDULE
                ================================================== --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-index">03</div>
                        <div class="section-title">Branch & Schedule</div>
                    </div>

                    <div class="grid-2" style="margin-bottom:16px;">
                        {{-- Branch --}}
                        <div class="field">
                            <label class="field-label" for="location">
                                Service Branch <span class="req">*</span>
                            </label>
                            <div class="select-wrap">
                                <select name="location" id="location" required>
                                    <option value="" disabled selected>Select branch</option>
                                    <optgroup label="Phnom Penh">
                                        <option value="13">Head Office</option>
                                        <option value="11">Street 50M</option>
                                    </optgroup>
                                    <optgroup label="Province">
                                        <option value="12">Siem Reap</option>
                                        <option value="10">Battambang</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="field">
                            <label class="field-label" for="booking_date">
                                Appointment Date <span class="req">*</span>
                            </label>
                            <input type="date" name="booking_date" id="booking_date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="field-label" style="margin-bottom:8px;">
                        Available Time Slots <span class="req">*</span>
                    </div>

                    <div id="slot-container" class="slot-grid">
                        <div class="slot-empty">Please select a branch and date first.</div>
                    </div>
                </div>

                {{-- =================================================
                     SUBMIT
                ================================================== --}}
                <button type="submit" id="btnSubmit" class="btn-submit" disabled>
                    <i class="far fa-paper-plane"></i> Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <div class="footer">
        Designed by Pech &copy; 2026
    </div>

    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        /* =========================================================
           CONFIG
        ========================================================== */
        const SLOT_URL = @json(route('slots.check'));
        const BOOKING_URL = @json(route('services.public_store'));
        const CSRF_TOKEN = @json(csrf_token());
        const TODAY = @json(now()->format('Y-m-d'));

        // Service durations (Khmer display)
        const serviceDurations = {
            'Engine Malfunction': '4-6 ម៉ោង',
            'Check Vibration': '4-6 ម៉ោង',
            'General Inspection': '1.5 ម៉ោង',
            'Check Noise': '3-4 ម៉ោង',
            'Interval Maintenance': '1.5 ម៉ោង',
            'Service 20000KM': '3 ម៉ោង',
            'Service 40000KM': '4 ម៉ោង',
            'Service 50000KM': '4 ម៉ោង',
            'Service 60000KM': '5 ម៉ោង',
            'Service 80000KM': '6 ម៉ោង',
            'Service 100000KM': '6 ម៉ោង',
            'General Repair': '3 ម៉ោង',
            'Body and Paint': '8 ម៉ោង',
            'Warranty': '2 ម៉ោង'
        };

        /* =========================================================
           UTILITIES
        ========================================================== */
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function safeText(value) { return escapeHtml(value); }

        function getLocalDate() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function isSunday(dateString) {
            const date = new Date(dateString + 'T00:00:00');
            return date.getDay() === 0;
        }

        function disableSubmit() {
            $('#btnSubmit').prop('disabled', true).html('<i class="far fa-paper-plane"></i> Confirm Booking');
        }

        function updateSubmitState() {
            const hasSlot = $("input[name='booking_time']:checked").length > 0;
            const validForm = $('#bookingForm')[0].checkValidity();
            $('#btnSubmit').prop('disabled', !(hasSlot && validForm));
        }

        /* =========================================================
           FETCH SLOTS
        ========================================================== */
        function fetchAvailableSlots() {
            const dateInput = $('#booking_date').val();
            const location = $('#location').val();

            if (!dateInput || !location) {
                $('#slot-container').html('<div class="slot-empty">Please select a branch and date first.</div>');
                disableSubmit();
                return;
            }

            if (dateInput < getLocalDate()) {
                $('#booking_date').val(getLocalDate());
                $('#slot-container').html('<div class="slot-empty">Please select a valid date.</div>');
                disableSubmit();
                return;
            }

            if (isSunday(dateInput)) {
                $('#slot-container').html('<div class="slot-empty" style="color:#C1302F;">Closed on Sundays. Please choose another day.</div>');
                disableSubmit();
                return;
            }

            $('#slot-container').html('<div class="slot-loading"><i class="fas fa-circle-notch fa-spin"></i> Loading available slots...</div>');
            disableSubmit();

            $.ajax({
                url: SLOT_URL,
                method: 'GET',
                dataType: 'json',
                data: { date: dateInput, location: location },
                timeout: 15000,
                success: function(data) {
                    if (data && data.isHoliday) {
                        Swal.fire({
                            icon: 'warning', title: 'Public Holiday',
                            text: safeText(data.message || 'This date is unavailable.'),
                            confirmButtonColor: '#B70101', confirmButtonText: 'Choose Another Date',
                            allowOutsideClick: false
                        }).then(function() {
                            $('#booking_date').val('');
                            $('#slot-container').html('<div class="slot-empty">Please select another date.</div>');
                            disableSubmit();
                        });
                        return;
                    }

                    if (!Array.isArray(data)) {
                        $('#slot-container').html('<div class="slot-empty" style="color:#C1302F;">Unable to load available slots.</div>');
                        disableSubmit();
                        return;
                    }

                    if (data.length === 0) {
                        $('#slot-container').html('<div class="slot-empty" style="color:#C1302F;">No available slots for this date.</div>');
                        disableSubmit();
                        return;
                    }

                    const now = new Date();
                    const todayStr = getLocalDate();
                    let html = '';

                    data.forEach(function(slot) {
                        const slotTime = safeText(slot.time);
                        let isPast = false;

                        if (dateInput === todayStr && slot.time) {
                            const match = String(slot.time).match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
                            if (match) {
                                let hour = parseInt(match[1], 10);
                                const minute = parseInt(match[2], 10);
                                const modifier = match[3].toUpperCase();

                                if (modifier === 'PM' && hour !== 12) hour += 12;
                                if (modifier === 'AM' && hour === 12) hour = 0;

                                const slotDate = new Date();
                                slotDate.setHours(hour, minute, 0, 0);
                                if (slotDate <= now) isPast = true;
                            }
                        }

                        const isFull = Boolean(slot.isFull);
                        const isDisabled = isFull || isPast;
                        const disabledAttr = isDisabled ? 'disabled' : '';
                        const cardClass = isDisabled ? 'slot-card disabled' : 'slot-card';
                        let statusHtml = '';

                        if (isPast) {
                            statusHtml = '<span class="slot-status past">Passed</span>';
                        } else if (isFull) {
                            statusHtml = '<span class="slot-status full">Full</span>';
                        } else {
                            const remaining = Number.isFinite(Number(slot.remaining)) ? Number(slot.remaining) : 0;
                            statusHtml = `<span class="slot-status avail">${escapeHtml(remaining)} left</span>`;
                        }

                        html += `<label class="${cardClass}">
                                    <input type="radio" name="booking_time" value="${slotTime}" ${disabledAttr} required>
                                    <div class="slot-content">
                                        <span class="slot-time">${slotTime}</span>
                                        ${statusHtml}
                                    </div>
                                 </label>`;
                    });

                    $('#slot-container').html(html);
                    updateSubmitState();
                },
                error: function(xhr) {
                    let message = 'Server connection error. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $('#slot-container').html(`<div class="slot-empty" style="color:#C1302F;">${safeText(message)}</div>`);
                    disableSubmit();
                }
            });
        }

        /* =========================================================
           DOCUMENT READY
        ========================================================== */
        $(document).ready(function() {

            $('#booking_date').attr('min', getLocalDate());

            $('#booking_date, #location').on('change', function() {
                const selectedDate = $('#booking_date').val();
                if (selectedDate && selectedDate < getLocalDate()) {
                    Swal.fire({
                        icon: 'warning', title: 'Invalid Date',
                        text: 'Please select today or a future date.',
                        confirmButtonColor: '#002663'
                    });
                    $('#booking_date').val(getLocalDate());
                }
                fetchAvailableSlots();
            });

            if ($('#location').val() && $('#booking_date').val()) {
                fetchAvailableSlots();
            }

            $('#plate').on('input', function() {
                this.value = this.value.toUpperCase().replace(/[<>]/g, '');
            });

            $('#contact').on('input', function() {
                this.value = this.value.replace(/[^\d+\-\s()]/g, '').slice(0, 20);
            });

            $('#mileage').on('input', function() {
                let value = parseInt(this.value || '0', 10);
                if (value < 0) value = 0;
                if (value > 2000000) value = 2000000;
                this.value = value || '';
            });

            // Populate the estimated-duration dashboard readout when a service is chosen
            $('#service_type').on('change', function() {
                const sType = $(this).val();
                const duration = serviceDurations[sType] || '1.5 ម៉ោង';
                
                $('#duration-val').text(duration);
                $('#est-duration-box').css('display', 'flex').hide().fadeIn();
            });

            $('#bookingForm').on('input change', 'input, select, textarea', function() {
                updateSubmitState();
            });

            $(document).on('change', "input[name='booking_time']", function() {
                updateSubmitState();
            });

            /* FORM SUBMISSION */
            $('#bookingForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if ($('#website').val().trim() !== '') {
                    return; // Honeypot trap
                }

                const selectedSlot = $("input[name='booking_time']:checked").val();
                if (!selectedSlot) {
                    Swal.fire({
                        icon: 'info', title: 'Select a Time Slot',
                        text: 'Please choose your preferred appointment time.',
                        confirmButtonColor: '#002663'
                    });
                    return;
                }

                const name = safeText($('#customer_name').val());
                const phone = safeText($('#contact').val());
                const model = safeText($('#model').val());
                const plate = safeText($('#plate').val());
                
                const serviceVal = $('#service_type').val();
                const serviceName = safeText($('#service_type option:selected').text());
                const estDuration = serviceDurations[serviceVal] || '1.5 ម៉ោង';
                
                const branch = safeText($('#location option:selected').text());
                const date = safeText($('#booking_date').val());
                const time = safeText(selectedSlot);

                const $btn = $('#btnSubmit');
                $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> Submitting...');

                Swal.fire({
                    title: 'Processing...', text: 'Please wait while we confirm your booking.',
                    allowOutsideClick: false, allowEscapeKey: false,
                    didOpen: function() { Swal.showLoading(); }
                });

                $.ajax({
                    url: BOOKING_URL,
                    method: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-DYNAMIC-TOKEN': $('meta[name="dynamic-token"]').attr('content') || ''
                    },
                    data: $(form).serialize(),
                    timeout: 30000,
                    success: function(response) {
                        if (!response || response.success !== true) {
                            throw new Error(response && response.message ? response.message : 'Booking could not be completed.');
                        }

                        const bookingId = escapeHtml(String(response.booking_id ?? ''));

                        const ticket = `
                            <div class="ticket-wrap">
                                <div class="ticket-head">
                                    <div style="width:52px;height:52px;background:#ECFDF5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
                                        <i class="fas fa-check" style="color:#147A45;font-size:1.4rem;"></i>
                                    </div>
                                    <div style="font-family:'Rajdhani',sans-serif;font-size:15px;font-weight:700;color:#14171C;letter-spacing:0.3px;">ការកក់ជោគជ័យ!</div>
                                    <div class="ref-badge">#MG-${bookingId}</div>
                                </div>
                                <div class="ticket-body">
                                    <div class="ticket-row"><span class="tl">អតិថិជន:</span><span class="tv" style="font-family:inherit;">${name}</span></div>
                                    <div class="ticket-row"><span class="tl">ទូរស័ព្ទ:</span><span class="tv">${phone}</span></div>
                                    <div class="ticket-row"><span class="tl">រថយន្ត:</span><span class="tv" style="font-family:inherit;">${model} · ${plate}</span></div>
                                    <div class="ticket-divider"></div>
                                    <div class="ticket-row"><span class="tl">សេវាកម្ម:</span><span class="tv" style="color:#B70101;font-family:inherit;">${serviceName}</span></div>
                                    <div class="ticket-row"><span class="tl">រយៈពេលប៉ាន់ស្មាន:</span><span class="tv" style="color:#147A45;font-family:inherit;"><i class="far fa-clock"></i> ${estDuration}</span></div>
                                    <div class="ticket-divider"></div>
                                    <div class="ticket-row"><span class="tl">កាលបរិច្ឆេទ:</span><span class="tv">${date}</span></div>
                                    <div class="ticket-row"><span class="tl">ម៉ោង:</span><span class="tv" style="color:#002663;">${time}</span></div>
                                    <div class="ticket-row"><span class="tl">សាខា:</span><span class="tv" style="font-size:0.8rem;font-family:inherit;">${branch}</span></div>
                                    <div style="margin-top:16px;font-size:11px;color:#8A93A0;text-align:center;">សូម Screenshot ទុកជាឯកសារយោងនៅពេលមកដល់សាខា។</div>
                                </div>
                            </div>
                        `;

                        Swal.fire({
                            html: ticket,
                            confirmButtonText: 'រួចរាល់',
                            confirmButtonColor: '#12151A',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            width: '380px',
                            customClass: { popup: 'swal2-popup' }
                        }).then(function() {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="far fa-paper-plane"></i> Confirm Booking');
                        let message = 'Technical error. Please try again.';

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const firstError = Object.values(xhr.responseJSON.errors).flat()[0];
                            if (firstError) message = firstError;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'warning', title: 'Booking Unsuccessful',
                            text: safeText(message),
                            confirmButtonColor: '#002663', allowOutsideClick: false
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>