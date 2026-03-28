@extends('layouts.homePages')

@section('content')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Siemreap&family=JetBrains+Mono:wght@600&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {

            --brand-indigo: #4f46e5;
            --brand-purple: #7c3aed;
            --brand-pink: #ec4899;
            --brand-red: #ef4444;
            --tf-gradient: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        }
        

        /* Standardized Small Typography */
        body {
            font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif !important;
            background-color: #f1f5f9;
            color: #000;
            font-size: 8.5pt;
            /* Standardized Small Font */
            -webkit-font-smoothing: antialiased;
        }

        .khmer-font {
            font-family: 'Siemreap', sans-serif !important;
            line-height: 1.4;
        }

        .label-bold {
            font-weight: 800;
        }

        .container-full {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1rem 2rem !important;
        }

        /* PDF Multi-Page Engine */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .print-page {
                display: block !important;
                page-break-after: always !important;
                padding: 12mm !important;
                width: 210mm;
                min-height: 297mm;
                background: white !important;
                margin: 0 auto !important;
                box-sizing: border-box;
                border: none !important;
            }
        }

        /* UI Styling */
        .document-wrapper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .header-gradient {
            background: var(--tf-gradient);
            border-radius: 15px;
            padding: 1.2rem 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 800;
            border-radius: 10px;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 12px;
        }

        .nav-pills .nav-link.active {
            background: white;
            color: var(--brand-indigo);
            border: 1.5px solid var(--brand-indigo);
        }

        /* Tables & Compact Elements */
        .table-hd {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.8rem;
        }

        .table-hd th {
            background: #f8fafc;
            border: 1px solid #000 !important;
            padding: 5px !important;
            font-size: 8pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
            text-align: center;
        }

        .table-hd td {
            border: 1px solid #000 !important;
            padding: 5px !important;
            font-size: 8.5pt;
            vertical-align: top;
            color: #000;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 3rem;
            text-align: center;
            font-size: 7.5pt;
            font-weight: 800;
            padding-top: 4px;
            text-transform: uppercase;
        }

        .terms-box {
            font-size: 7.5pt;
            line-height: 1.3;
            border: 1px solid #000;
            padding: 8px;
            background: #fafafa;
        }

        .membership-card {
            background: #000;
            color: #fff;
            width: 380px;
            height: 220px;
            border-radius: 15px;
            padding: 25px;
            margin: auto;
            position: relative;
            border: 1px solid #333;
        }

        /* Watermark Styling */
        .watermark-container {
            position: relative;
            overflow: hidden;
        }

        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.03);
            /* Extremely light shadow name */
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
            text-transform: uppercase;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .content-overlay {
            position: relative;

            z-index: 1;
        }

        /* --- Card Container --- */
        .card-preview-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            padding: 20px;
            background: #fff;
        }

        /* --- FRONT CARD (Luxury Black) --- */
        .tf-card-front {
            width: 450px;
            height: 270px;
            background: linear-gradient(145deg, #1a1a1a, #2d2d2d);
            border-radius: 15px;
            position: relative;
            color: #d4af37;
            /* Premium Gold */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid #444;
        }

        /* Top Branding Area */
        .card-header-front {
            padding: 25px;
            text-align: center;
        }

        .tf-badge-logo {
            width: 45px;
            height: 45px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 5px;
            font-weight: 900;
            font-size: 18pt;
        }

        .brand-name {
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 11pt;
        }

        .membership-text {
            font-size: 24pt;
            font-weight: 900;
            letter-spacing: 3px;
            margin-top: 5px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* White Name Bar */
        .name-bar {
            background: #fff;
            width: 80%;
            height: 35px;
            margin: 10px auto;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: 900;
            font-size: 12pt;
            text-transform: uppercase;
        }

        /* Bottom Logos Area */
        .bottom-logos {
            position: absolute;
            bottom: 25px;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 35px;
            align-items: center;
        }

        .logo-gold {
            height: 35px;
            filter: brightness(1.2);
        }

        /* --- BACK CARD (Professional Layout) --- */
        .tf-card-back {
            width: 450px;
            height: 270px;
            background: #1a1a1a;
            border-radius: 15px;
            position: relative;
            color: #fff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid #444;
        }

        .back-top-strip {
            background: #222;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            border-bottom: 0.5px solid #444;
        }

        .white-signature-strip {
            background: #fff;
            width: 100%;
            height: 40px;
            margin-top: 15px;
            background-image: repeating-linear-gradient(45deg, #eee, #eee 10px, #fff 10px, #fff 20px);
        }

        .back-content-area {
            padding: 25px;
            text-align: center;
        }

        .terms-khmer {
            font-family: 'Siemreap', sans-serif !important;
            font-size: 8pt;
            line-height: 1.6;
            color: #d4af37;
            /* Gold text for terms */
            margin-top: 15px;
        }

        .back-footer-url {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            font-size: 7.5pt;
            color: #888;
        }

        @media print {

            .tf-card-front,
            .tf-card-back {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .id-card-placeholder {
            height: 320px;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Membership Card Pro Styling */
        .card-pro-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }

        .membership-card-pro {
            width: 450px;
            height: 275px;
            border-radius: 18px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 1px solid #333;
        }

        /* --- FRONT CARD --- */
        .card-front-premium {
            background: linear-gradient(145deg, #1a1a1a, #262626);
            color: #d4af37;
            /* Premium Gold */
            padding: 25px;
            text-align: center;
        }

        .card-logo-badge {
            width: 50px;
            height: 50px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 900;
            font-size: 20pt;
        }

        .card-membership-title {
            font-size: 24pt;
            font-weight: 900;
            letter-spacing: 4px;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .card-name-bar {
            background: #ffffff;
            color: #000;
            width: 85%;
            margin: 15px auto;
            padding: 8px 0;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13pt;
            text-transform: uppercase;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-footer-logos {
            position: absolute;
            bottom: 25px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 35px;
            align-items: center;
        }

        .card-footer-logos img {
            height: 32px;
            filter: brightness(1.2);
        }

        /* --- BACK CARD --- */
        .card-back-premium {
            background: #111;
            color: #fff;
        }

        .magnetic-strip-pro {
            width: 100%;
            height: 45px;
            background: #000;
            margin-top: 25px;
        }

        .back-content-grid {
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
        }

        .back-terms {
            font-size: 8pt;
            line-height: 1.6;
            color: #d4af37;
            text-align: center;
            padding: 0 30px;
        }

        .card-barcode-pro {
            position: absolute;
            bottom: 20px;
            right: 30px;
            text-align: center;
        }

        .barcode-line-pro {
            width: 130px;
            height: 35px;
            background: #fff;
            background-image: repeating-linear-gradient(90deg, #000, #000 1.5px, #fff 1.5px, #fff 4px);
            margin-bottom: 3px;
        }

        @media print {
            .membership-card-pro {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .card-brand-logos img {
            height: 35px;
            margin-bottom: 15px;
            /* ប្រើ filter ដើម្បីដូរពណ៌ទៅជា Goldish/Yellow Metallic */
            filter: brightness(0) saturate(100%) invert(81%) sepia(38%) saturate(608%) hue-rotate(358deg) brightness(91%) contrast(89%);
            transition: 0.3s;
        }

        .card-brand-logos img:hover {
            filter: brightness(1.2);
            transform: scale(1.1);
        }

        /* variables សម្រាប់គ្រប់គ្រងពណ៌ឱ្យស៊ីគ្នា */
        :root {
            --pdi-border: #000000;
            --pdi-bg-light: #f8fafc;
            --pdi-text-muted: #64748b;
        }

        /* រចនាប័ទ្មទូទៅនៃទំព័រ */
        .pdi-content {
            font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif !important;
            color: #000;
            line-height: 1.4;
        }

        /* Section Titles (A, B, C, D) */
        .pdi-section-title {
            background-color: var(--pdi-bg-light);
            border: 1px solid var(--pdi-border);
            padding: 6px 10px;
            font-weight: 800;
            font-size: 8.5pt;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        /* Checklist Rows & Tables */
        .pdi-row {
            border: 1px solid var(--pdi-border);
            padding: 5px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8pt;
        }

        .pdi-row-no-top {
            border-top: none;
        }

        /* Vehicle Diagram Area */
        .diagram-box {
            height: 140px;
            background: #ffffff;
            border: 1px solid var(--pdi-border);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(#cbd5e1 0.5px, transparent 0.5px);
            background-size: 15px 15px;
            /* បង្កើតក្រឡាចំណុចៗបច្ចេកទេស */
        }

        .diagram-placeholder {
            font-size: 14pt;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.05);
            letter-spacing: 2px;
        }

        /* Signature Styles */
        .sig-label {
            font-size: 8pt;
            font-weight: 800;
        }

        .sig-line-dashed {
            border-bottom: 1px dashed var(--pdi-border);
            height: 40px;
            flex-grow: 1;
        }

        /* បង្កើនគុណភាពពេល Print */
        @media print {
            .pdi-section-title {
                -webkit-print-color-adjust: exact;
            }
        }

        /* ប្រអប់ដ្យាក្រាមរួម - បានលុប Border ចេញ */
        .diagram-box {
            height: 160px;
            background: #ffffff;
            /* លុបបន្ទាត់ស៊ុមចេញ */
            border: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            /* រក្សាទុកក្រឡាការ៉ូបច្ចេកទេស (Technical Grid) */
            background-image: radial-gradient(#cbd5e1 0.7px, transparent 0.7px);
            background-size: 15px 15px;
            overflow: hidden;
        }

        /* ស្ទីលសម្រាប់រូបភាពរថយន្ត */
        .vehicle-diagram-img {
            max-height: 130px;
            width: auto;
            opacity: 0.65;
            filter: grayscale(100%);
            z-index: 1;
            object-fit: contain;
        }

        /* អត្ថបទ Watermark នៅពីក្រោយ */
        .diagram-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16pt;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.04);
            letter-spacing: 2px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        /* ផ្នែក Note នៅខាងក្រោមស្ដាំ */
        .diagram-note {
            position: absolute;
            bottom: 5px;
            right: 12px;
            font-size: 7pt;
            color: #000;
            font-style: italic;
        }

        .border {
            border-width: 0 !important;
        }

        .border {
            border-color: transparent !important;
        }
        :root {
        --tf-black: #000000;
        --tf-gray: #4a4a4a;
        --tf-light-gray: #f8fafc;
        --tf-border: #d1d5db;
    }

    /* រចនាប័ទ្មទូទៅនៃកិច្ចសន្យា */
    .contract-container {
        font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif !important;
        color: var(--tf-black);
        line-height: 1.5;
    }

    .khmer-font { font-family: 'Siemreap', sans-serif !important; }
    .label-bold { font-weight: 800; }
    .text-uppercase { text-transform: uppercase; }
    .italic { font-style: italic; }

    /* Header & Branding */
    .contract-header {
        border-bottom: 2px solid var(--tf-black);
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    /* Information Grid */
    .info-grid { font-size: 9.5pt; margin-bottom: 20px; }
    .info-label { color: var(--tf-gray); min-width: 150px; display: inline-block; }
    .info-value { border-bottom: 1px solid var(--tf-border); font-weight: 700; flex-grow: 1; }

    /* Terms & Conditions Box */
    .intro-box {
        background-color: var(--tf-light-gray);
        border: 1px solid var(--tf-border);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 25px;
        font-size: 8.5pt;
    }

    /* Table Styling */
    .table-hd {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .table-hd th {
        background: #f1f5f9;
        border: 1px solid var(--tf-black);
        padding: 10px;
        font-size: 8pt;
        text-align: center;
    }
    .table-hd td {
        border: 1px solid var(--tf-black);
        padding: 12px;
        font-size: 9pt;
        vertical-align: top;
    }

    /* Terms & Conditions (Compact) */
    .terms-box {
        border: 1px solid var(--tf-black);
        padding: 12px;
        font-size: 8pt;
        border-radius: 4px;
    }
    .term-item { margin-bottom: 10px; }
    .term-item:last-child { margin-bottom: 0; }

    /* Signatures */
    .sig-section { margin-top: 40px; }
    .sig-line {
        border-top: 1px solid var(--tf-black);
        margin-top: 50px;
        padding-top: 5px;
        font-size: 7.5pt;
        font-weight: 800;
        text-align: center;
    }

    @media print {
        .print-page { padding: 0 !important; }
        .intro-box { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
    }
    /* រចនាប័ទ្មសម្រាប់ចំណងជើងខ្មែរ (Moul Light) */
    .contract-header {
        border-bottom: 2px solid #000;
        padding-bottom: 15px;
        margin-bottom: 30px;
        text-align: center;
    }

    /* ចំណងជើងខ្មែរ: ប្រើ Moul Light (ដិតស្រាប់) */
    .moul-bold {
        font-family: 'Khmer OS Muol Light', 'Moul', serif !important;
        font-size: 18pt; /* បង្កើនទំហំបន្តិច */
        color: #000;
        margin-bottom: 2px;
        line-height: 1.5;
    }

    /* ចំណងជើងអង់គ្លេស: បន្ថែម Font Weight 900 (Extra Bold) */
    .english-bold {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 900; /* កម្រិតដិតបំផុត */
        font-size: 11pt;
        letter-spacing: 3px; /* បង្កើនគម្លាតអក្សរឱ្យមើលទៅ Luxury */
        text-transform: uppercase;
        color: #000;
    }


    .info-grid {
        font-size: 9.5pt;
        margin-bottom: 25px;
        color: #1e293b;
    }

    /* រចនាប័ទ្មសម្រាប់ Label */
    .info-label {
        min-width: 130px;
        color: #64748b;
        font-weight: 500;
    }

    /* រចនាប័ទ្មសម្រាប់តម្លៃទិន្នន័យ (Highlighted) */
    .info-value {
        font-weight: 800;
        color: #000;
        border-bottom: 1px dashed #cbd5e1; /* បន្ទាត់ពីក្រោមស្តើងៗ */
        flex-grow: 1;
        padding-left: 8px;
        transition: all 0.3s ease;
    }

    /* Highlight ពណ៌សម្រាប់ទិន្នន័យសំខាន់ */
    .text-highlight {
        color: #4f46e5; /* ពណ៌ Indigo */
        background-color: #f5f3ff;
        padding: 0 8px;
        border-radius: 4px;
        font-family: 'JetBrains Mono', monospace; /* សម្រាប់លេខឯកសារ */
    }

    .source-badge {
        background-color: #f1f5f9;
        padding: 1px 10px;
        border-radius: 12px;
        font-size: 8pt;
        border: 1px solid #e2e8f0;
    }
    .contract-header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #ab171c; /* ប្រើពណ៌ Indigo ជាបន្ទាត់ខណ្ឌ */
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    /* ចំណងជើងកណ្តាល */
    .header-titles {
        text-align: center;
        flex-grow: 1;
    }

    .moul-bold-indigo {
        font-family: 'Khmer OS Muol Light', 'Moul', serif !important;
        font-size: 15pt;
        color: #1e293b;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .english-bold-indigo {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 900;
        font-size: 10pt;
        text-transform: uppercase;
        color: #ab171c; /* ពណ៌ Indigo លេចធ្លោ */
    }

    /* Logo ឬ Badge បន្ថែម */
    .brand-badge {
        background-color: #ab171c;
        color: white;
        padding: 10px 20px;
        border-radius: 0 0 15px 15px;
        font-weight: 800;
        font-size: 10pt;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    </style>

    <div class="container-full">
        <div class="no-print mb-4">
            <div class="header-gradient d-flex justify-content-between align-items-center shadow-lg">
                <div>
                    <h1 class="font-black mb-1 h5 text-white">Registry Authorization Center</h1>
                    <p class="opacity-80 small mb-0 font-bold text-white uppercase">AUTHORIZED SALES PACK | REF:
                        {{ $contract->contract_number }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="downloadPDF()"
                        class="btn btn-white bg-white text-success px-4 py-2 rounded-xl font-black shadow-lg hover:scale-105 transition">
                        <i class="fas fa-file-pdf mr-2"></i> DOWNLOAD 6-PAGE PDF
                    </button>
                    <button onclick="window.print()"
                        class="btn btn-white bg-white text-danger px-4 py-2 rounded-xl font-black shadow-lg hover:scale-105 transition">
                        <i class="fas fa-print mr-2"></i> PRINT A4
                    </button>
                    <a href="{{ route('contracts.index') }}"
                        class="btn btn-outline-light px-3 rounded-xl font-bold border-2">CLOSE</a>
                </div>
            </div>
            <ul class="nav nav-pills justify-content-center gap-2 mb-4 bg-white p-2 rounded-2xl shadow-sm border">
                <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#page1">1. Contract</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page2">2. ID/Member</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page3">3. PDI Check</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page4">4. Warranty I</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page5">5. Maintenance</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page6">6. Exclusions</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page7">7. Acceptance</a></li>
            </ul>
        </div>

        <div id="full-document-wrapper" class="document-wrapper contract-container">
            <div class="tab-content">
                <div class="tab-pane fade show active print-page p-5" id="page1">
                    
                    <div class="contract-header-wrapper">
                        <div style="width: 150px;">
                            <div class="brand-badge text-center">Official</div>
                        </div>
                    
                        <div class="header-titles">
                            <h1 class="moul-bold-indigo">កិច្ចសន្យាការលក់</h1>
                            <div class="english-bold-indigo">Sales Contract</div>
                        </div>
                    
                        <div style="width: 150px; text-align: right;">
                            <div class="small text-muted mb-1">Status:</div>
                            <span class="badge badge-success" style="background-color: #10b981; color: white; padding: 4px 12px; border-radius: 50px; font-size: 7pt;">VALIDATED</span>
                        </div>
                    </div>
        
                    <div class="row info-grid khmer-font">
                        <div class="col-7">
                            <div class="d-flex mb-2 align-items-end">
                                <span class="info-label">អតិថិជន / Name:</span>
                                <span class="info-value">{{ strtoupper($contract->customer_name) }}</span>
                            </div>
                            <div class="d-flex mb-2 align-items-end">
                                <span class="info-label">ទំនាក់ទំនង / Phone:</span>
                                <span class="info-value">{{ $contract->phone_number }}</span>
                            </div>
                            <div class="d-flex align-items-end">
                                <span class="info-label">អាសយដ្ឋាន / Address:</span>
                                <span class="info-value">{{ $contract->address }}</span>
                            </div>
                        </div>
                    
                        <div class="col-5">
                            <div class="d-flex mb-2 justify-content-end align-items-center">
                                <span class="mr-2">លេខឯកសារ / No:</span>
                                <span class="label-bold text-highlight">{{ $contract->contract_number }}</span>
                            </div>
                            <div class="d-flex mb-2 justify-content-end align-items-center">
                                <span class="mr-2 text-muted small">ប្រភព / Source:</span>
                                <span class="source-badge font-bold">{{ $contract->source }}</span>
                            </div>
                            <div class="d-flex mb-2 justify-content-end align-items-center">
                                <span class="mr-2">ផ្នែកលក់ / Sales:</span>
                                <span class="label-bold">{{ $contract->sale_person }}</span>
                            </div>
                            <div class="d-flex justify-content-end text-muted small mt-1">
                                <span class="mr-2">កាលបរិច្ឆេទ / Date:</span>
                                <span class="font-bold text-dark">{{ \Carbon\Carbon::parse($contract->order_date)->format('d-M-Y') }}</span>
                            </div>
                        </div>
                    </div>
        
                    <div class="intro-box khmer-font text-center">
                        <div class="mb-2">
                            <strong>អ្នកទិញដែលចុះហត្ថលេខាខាងក្រោមនេះយល់ព្រមទិញ ហើយក្រុមហ៊ុន ធី អេហ្វ ម៉ូធ័រ (ខេមបូឌា) យល់ព្រមលក់ទំនិញដូចខាងក្រោម។</strong><br>
                            <span class="italic text-muted">The undersigned Purchaser hereby agrees to buy and TF Motors Cambodia Co., Ltd (Seller) agrees to sell the undermentioned goods.</span>
                        </div>
                        <div class="small">
                            <strong>កិច្ចសន្យានេះពុំមានសុពលភាពទេ ប្រសិនមិនមានការអនុញ្ញាតិពីអ្នកគ្រប់គ្រងទូទៅរបស់អ្នកលក់។</strong><br>
                            <span class="italic text-muted">This contract is not valid unless authorized by the General Manager.</span>
                        </div>
                    </div>
        
                    <table class="table-hd khmer-font">
                        <thead>
                            <tr>
                                <th width="50">N°</th>
                                <th>បរិយាយ / DESCRIPTION</th>
                                <th width="80">ចំនួន / QTY</th>
                                <th width="140">ទឹកប្រាក់ / AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="height: 120px;">
                                <td class="text-center">1</td>
                                <td>
                                    <div class="label-bold text-uppercase mb-1">Model: {{ $contract->model }}</div>
                                    <div class="mb-2">Colour: {{ $contract->color }}</div>
                                    <div class="p-2 border rounded bg-light small italic text-primary" style="font-size: 8pt;">
                                        <strong>OFFERS:</strong> {{ $contract->offers }}
                                    </div>
                                </td>
                                <td class="text-center font-bold">{{ $contract->qty }}</td>
                                <td class="text-center label-bold">${{ number_format($contract->sub_total, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right py-1">ទឹកប្រាក់សរុប / SUB TOTAL:</td>
                                <td class="text-center font-bold">${{ number_format($contract->sub_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right py-1 text-danger">បញ្ចុះតម្លៃ / DISCOUNT:</td>
                                <td class="text-center text-danger font-bold">-${{ number_format($contract->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right py-1 text-primary">ប្រាក់កក់ / DEPOSIT:</td>
                                <td class="text-center text-primary font-bold">-${{ number_format($contract->deposit, 2) }}</td>
                            </tr>
                            <tr class="bg-light">
                                <td colspan="3" class="text-right label-bold text-uppercase py-2">សរុប / GRAND TOTAL:</td>
                                <td class="text-center label-bold h6 mb-0 py-2" style="font-size: 11pt;">${{ number_format($contract->grand_total, 2) }}</td>
                            </tr>
                            
                        </tfoot>
                    </table>
        
                    <div class="terms-wrapper khmer-font">
                        <div class="label-bold text-uppercase border-bottom mb-2 pb-1">លក្ខខណ្ឌ / Terms and Conditions</div>
                        
                        <div class="term-row">
                            <div class="term-kh">១. ការផ្ទេរកម្មសិទ្ធិត្រូវធ្វើឡើង នៅពេលទឹកប្រាក់ត្រូវបានទូទាត់គ្រប់ចំនួន។</div>
                            <div class="term-en">1. Ownership transfer occurs only upon full payment settlement.</div>
                        </div>
                
                        <div class="term-row">
                            <div class="term-kh">២. ប្រាក់កក់ទុក ប្រាក់បង់ដំបូង និងប្រាក់ទូទាត់ផ្សេងទៀតសម្រាប់គោលបំណងនៃកិច្ចព្រមព្រៀងស្តីពីការលក់នេះ មិនអាចដកវិញបានទេ ក្នុងករណីអ្នកទិញដកខ្លួនពីកិច្ចសន្យា ឬខកខានក្នុងការទូទាត់សមតុល្យដែលនៅសល់ក្នុងរយៈពេល ៥ ថ្ងៃធ្វើការ បន្ទាប់ពីការជូនដំណឹងអំពីការដឹកជញ្ជូន។</div>
                            <div class="term-en">2. Payments: Booking Deposit, Down Payment, and other payments for the purpose of this sales agreement are non-refundable in the event of the purchaser's withdrawal from the contract or failure to pay the remaining balance within 5 business days of delivery notification.</div>
                        </div>
                
                        <div class="term-row">
                            <div class="term-kh">៣. ប្រសិនបើការលក់នេះត្រូវបានធ្វើឡើងតាមរយៈកម្ចីរថយន្ត ឬភតិសន្យាហិរញ្ញវត្ថុ ទឹកប្រាក់ទូទាត់ដំបូងដែលមានចែងក្នុងកិច្ចសន្យានេះ នឹងមិនអាចដកវិញបានទេ នៅពេលដែលអ្នកទិញមិនអាចផ្តល់ឯកសារតាមតម្រូវការរបស់ធនាគារក្នុងរយៈពេល ១៥ ថ្ងៃ គិតចាប់ពីថ្ងៃស្នើសុំប្រាក់កម្ចី។</div>
                            <div class="term-en">3. If this sale is closed through a car loan or financial leasing, the initial payment stated in this contract will be non-refundable if the purchaser is unable to meet bank requirements within 15 days of the loan application date.</div>
                        </div>
                
                        <div class="term-row">
                            <div class="term-kh">៤. ក្នុងករណីអ្នកទិញកំពុងស្នើសុំកម្ចីរថយន្ត ហើយពាក្យស្នើសុំត្រូវបានបដិសេធ អ្នកទិញអាចទទួលបានប្រាក់បង្វិលសងវិញគ្រប់ចំនួន ដោយភ្ជាប់ជាមួយឯកសារយោងដែលមានសុពលភាព ឬលិខិតបដិសេធពីខាងធនាគារ។</div>
                            <div class="term-en">4. If the loan application is rejected, the purchaser shall receive a full refund upon providing a valid bank rejection letter.</div>
                        </div>
                    </div>
        
                    <div class="row sig-section khmer-font">
                        <div class="col-3"><div class="sig-line">អ្នកទិញ / PURCHASER</div></div>
                        <div class="col-3"><div class="sig-line">ផ្នែកលក់ / SALESMAN</div></div>
                        <div class="col-3"><div class="sig-line">អ្នកគ្រប់គ្រង / MANAGER</div></div>
                        <div class="col-3"><div class="sig-line">នាយក / DIRECTOR</div></div>
                    </div>
        
        </div>
                <div class="tab-pane fade print-page p-5" id="page2">
                    <h6 class="khmer-font border-bottom border-dark pb-1 mb-3 label-bold uppercase">អត្តសញ្ញាណប័ណ្ឌ /
                        Customer ID Card</h6>
                    <div class="id-card-placeholder mb-5">
                        @if ($contract->customer_id_card)
                            <img src="{{ asset('storage/' . $contract->customer_id_card) }}"
                                style="max-width:98%; max-height:98%; object-fit:contain; border-radius: 10px;">
                        @else
                            <div class="text-center text-muted opacity-50">
                                <i class="fas fa-id-card fa-3x mb-2"></i>
                                <p class="small mb-0">ATTACH ID COPY HERE</p>
                            </div>
                        @endif
                    </div>

                    <h6 class="khmer-font border-bottom border-dark pb-1 mb-4 label-bold uppercase">កាតសមាជិក / Membership
                        Card</h6>

                    <div class="card-pro-wrapper">
                        <div class="membership-card-pro card-front-premium">
                            <div class="card-logo-badge">T</div>
                            <div class="small font-bold text-white opacity-75">TF MOTORS</div>
                            <div class="card-membership-title">MEMBERSHIP</div>

                            <div class="card-name-bar">
                                {{ strtoupper($contract->customer_name) }}
                            </div>

                            <div class="card-footer-logos">
                                <img src="{{ asset('assets/images/logos/mg-gold.png') }}" alt="MG">
                                <img src="{{ asset('assets/images/logos/re-gold.png') }}" alt="Royal Enfield">
                                <img src="{{ asset('assets/images/logos/maxus-gold.png') }}" alt="Maxus">
                            </div>
                        </div>

                        <div class="membership-card-pro card-back-premium">
                            <div class="magnetic-strip-pro"></div>

                            <div class="back-content-grid">
                                <div class="text-start opacity-75">
                                    MG: +855 92 666 281<br>
                                    RE: +855 95 666 381
                                </div>
                                <div class="text-end font-bold text-gold">
                                    www.taifu.com.kh
                                </div>
                            </div>

                            <div class="back-terms khmer-font">
                                ប័ណ្ណនេះជាកម្មសិទ្ធិរបស់ក្រុមហ៊ុន។ ប្រសិនបើរើសបានវិញ សូមមេត្តាប្រគល់មកឱ្យក្រុមហ៊ុនវិញ។ <br>
                                ការប្រើប្រាស់ប័ណ្ណនេះ គឺអនុលោមតាមលក្ខខណ្ឌរបស់ក្រុមហ៊ុនដែលមានផ្តល់ជូនក្នុងវេបសាយ។
                            </div>

                            <div class="card-barcode-pro">
                                <div class="barcode-line-pro"></div>
                                <span
                                    class="barcode-number small font-mono">{{ $contract->id_number ?? '22001459' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade print-page p-5 pdi-content" id="page3">
                    <div class="d-flex justify-content-between align-items-end border-bottom border-dark mb-4 pb-2">
                        <div class="h4 font-black mb-0">TF MOTORS</div>
                        <div class="text-right">
                            <h3 class="font-black mb-0 h6 text-uppercase">Vehicle Pre-Delivery Check List</h3>
                        </div>
                    </div>

                    <div class="row mb-4 khmer-font" style="font-size: 8.5pt;">
                        <div class="col-6 border-right pr-4">
                            <div class="d-flex mb-1">Customer: <span
                                    class="border-bottom flex-grow-1 ml-2 font-bold">{{ $contract->customer_name }}</span>
                            </div>
                            <div class="d-flex mb-1">Phone: <span
                                    class="border-bottom flex-grow-1 ml-2">{{ $contract->phone_number }}</span></div>
                            <div class="d-flex mb-1">VIN: <span
                                    class="label-bold uppercase border-bottom flex-grow-1 ml-2">{{ $contract->vin_number }}</span>
                            </div>
                            <div class="d-flex">Address: <span
                                    class="border-bottom flex-grow-1 ml-2">{{ $contract->address }}</span></div>
                        </div>
                        <div class="col-6 pl-4">
                            <div class="d-flex mb-1">Contract No: <span
                                    class="label-bold uppercase border-bottom flex-grow-1 ml-2">{{ $contract->contract_number }}</span>
                            </div>
                            <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <div class="d-flex mb-1 text-muted italic">Mileage: <span
                                            class="border-bottom flex-grow-1 ml-2"></span></div>
                                </div>
                                <div class="col-6 pl-1">
                                    <div class="d-flex mb-1">Date: <span
                                            class="border-bottom flex-grow-1 ml-2">{{ date('d-M-Y') }}</span></div>
                                </div>
                                <div class="col-6 pr-1">
                                    <div class="d-flex text-muted italic">Color: <span
                                            class="border-bottom flex-grow-1 ml-2">{{ $contract->color }}</span></div>
                                </div>
                                <div class="col-6 pl-1">
                                    <div class="d-flex">Source: <span
                                            class="border-bottom flex-grow-1 ml-2">{{ $contract->source }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="pdi-section-title">A. Inside the Vehicle</h6>
                    <div class="mb-3">
                        <div class="pdi-row font-bold bg-light"><span>Inspection Item / Description</span><span>OK |
                                WORK</span></div>
                        <div class="pdi-row pdi-row-no-top"><span>1. Fuel Level (E, 1/4, 1/2, 3/4, F)</span><span>☐ |
                                ☐</span></div>
                        <div class="pdi-row pdi-row-no-top"><span>2. Warning Light (Check for Operation)</span><span>☐ |
                                ☐</span></div>
                        <div class="pdi-row pdi-row-no-top"><span>3. Horn & Washer & Wiper</span><span>☐ | ☐</span></div>
                        <div class="pdi-row pdi-row-no-top"><span>4. Handbrake Operation</span><span>☐ | ☐</span></div>
                    </div>
                    <div class="small mb-3 d-flex align-items-center opacity-75">
                        <span class="mr-3 italic">* Explain to customer about all major functions.</span>
                        <span class="ml-auto">YES ☐ NO ☐</span>
                    </div>

                    <h6 class="pdi-section-title">B. Around the Vehicle</h6>
                    <div class="row no-gutters border p-2 mb-4 bg-white">
                        <div class="col-4 border-right pr-2" style="font-size: 7.5pt;">
                            <div class="mb-1">1. <strong>B</strong>=Body Damage</div>
                            <div class="mb-1">2. <strong>P</strong>=Paint Damage</div>
                            <div class="mb-1">3. <strong>G</strong>=Glass Damage</div>
                            <div class="mb-3">4. <strong>E</strong>=Lights Damage</div>
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between mb-1">Spare Tire / Kits: <span>OK ☐ NO ☐</span>
                                </div>
                                <div class="d-flex justify-content-between">Tools Kit: <span>OK ☐ NO ☐</span></div>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="diagram-box ml-2" style="position: relative; overflow: hidden;">
                                <img src="{{ asset('assets/images/logos/vehicle-pdi-diagram.png') }}"
                                    class="vehicle-diagram-img">
                                <div class="diagram-placeholder">[ VEHICLE DIAGRAM AREA ]</div>
                                <div class="text-right p-1"
                                    style="position: absolute; bottom: 0; right: 0; font-size: 6.5pt;">Note: _____________
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-12 mb-3">
                            <h6 class="pdi-section-title">C. Under Hood / Bonnet</h6>
                            <div class="pdi-row pdi-row-no-top"><span>1. General Visual Condition Check</span><span>OK ☐ NO
                                    ☐</span></div>
                            <div class="pdi-row pdi-row-no-top"><span>2. Engine Oil & Fluid Levels</span><span>OK ☐ NO
                                    ☐</span></div>
                        </div>
                        <div class="col-12 mb-4">
                            <h6 class="pdi-section-title">D. Under the Vehicle</h6>
                            <div class="pdi-row pdi-row-no-top"><span>1. General Visual Condition Check</span><span>OK ☐ NO
                                    ☐</span></div>
                        </div>
                    </div>

                    <div class="row pt-4 mt-auto">
                        <div class="col-6 d-flex align-items-end">
                            <span class="sig-label mr-2">Sale Signature:</span>
                            <div class="sig-line-dashed"></div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <span class="sig-label mr-2">Customer Signature:</span>
                            <div class="sig-line-dashed"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade print-page p-5 watermark-container" id="page4">
                    <div class="watermark-text">MG CAMBODIA</div>

                    <div class="content-overlay">
                        <div class="text-center mb-4">
                            <h2 class="font-black mb-1 h3 khmer-font">លិខិតធានាគុណភាព</h2>
                            <h2 class="font-black mb-1 uppercase h5">Warranty Letter</h2>
                        </div>

                        <div class="border rounded-xl p-3 mb-4 khmer-font"
                            style="border: 1.5px solid #000 !important; border-radius: 15px !important; font-size: 8.5pt; background-color: #fcfcfc;">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-2">ឈ្មោះអតិថិជន / Customer Name: <span
                                            class="label-bold border-bottom ml-1">{{ $contract->customer_name }}</span>
                                    </div>
                                    <div class="mb-2">លេខទំនាក់ទំនង / Phone Number: <span
                                            class="label-bold border-bottom ml-1">{{ $contract->phone_number }}</span>
                                    </div>
                                    <div class="mb-2">លេខតួ / VIN Number: <span
                                            class="label-bold uppercase border-bottom ml-1">{{ $contract->vin_number }}</span>
                                    </div>
                                    <div class="mb-2">ម៉ូឌែល / Model: <span
                                            class="label-bold border-bottom ml-1">{{ $contract->model }}</span></div>
                                    <div>អាសយដ្ឋាន / Address: <span
                                            class="border-bottom ml-1">{{ $contract->address }}</span></div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-2">លេខឯកសារ / Contract No: <span
                                            class="label-bold uppercase border-bottom ml-1">{{ $contract->contract_number }}</span>
                                    </div>
                                    <div class="mb-2">ផ្នែកលក់ / Sales Person: <span
                                            class="label-bold border-bottom ml-1">{{ $contract->sale_person }}</span>
                                    </div>
                                    <div class="mb-2">ថ្ងៃប្រគល់ / Delivery Date: <span
                                            class="label-bold border-bottom ml-1">{{ \Carbon\Carbon::now()->format('d-M-Y') }}</span>
                                    </div>
                                    <div>ស្លាកលេខ / Plate No: <span class="border-bottom ml-1">____________________</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="label-bold border-bottom mb-2 uppercase" style="font-size: 8.5pt;">I. លក្ខខណ្ឌ
                            និងគោលការណ៍ធានា / CONDITIONS AND WARRANTY POLICY</h6>
                        <div class="khmer-font mb-3" style="font-size: 8.5pt; text-align: justify;">
                            <strong>និយមន័យ៖</strong> ការធានារបស់ក្រុមហ៊ុនមានន័យថា ធានាដោះស្រាយ និងជួសជុលរាល់បញ្ហាខូចខាត
                            ឬកំហុសបច្ចេកទេសដែលកើតចេញពីការផលិតពីរោងចក្រផ្ទាល់។<br>
                            <span class="italic text-muted"><strong>Definition:</strong> The company's warranty covers
                                repairs for any damage or technical errors originating directly from the factory.</span>
                        </div>

                        <div class="khmer-font mb-4 p-2"
                            style="font-size: 8pt; line-height: 1.5; border-left: 3px solid #ef4444; background: #fff5f5;">
                            <p class="mb-1 text-danger"><strong>បញ្ជាក់៖</strong> ការបែកបាក់
                                ឬការខូចខាតដែលបណ្តាលមកពីការប្រើប្រាស់ខុសបច្ចេកទេសរបស់អតិថិជន ក្រុមហ៊ុនមិនទទួលខុសត្រូវឡើយ។
                                ក្នុងករណីអតិថិជនខកខានមិនបានបំពេញតាមគោលការណ៍ណែនាំក្នុងសៀវភៅថែទាំ
                                នោះសុពលភាពនៃការធានានឹងត្រូវបញ្ចប់។</p>
                            <p class="italic text-muted mb-0"><strong>NOTED:</strong> The company is not responsible for
                                any damage caused by the customer's improper use. Failure to comply with the guidelines set
                                out in the owner's handbook may invalidate the vehicle's warranty.</p>
                        </div>

                        <h6 class="label-bold text-uppercase mb-2" style="font-size: 8.5pt; color: var(--brand-indigo);">
                            ការធានាទូទៅ / NORMAL WARRANTY</h6>
                        <ul class="khmer-font mb-4" style="font-size: 8.5pt; list-style-type: none; padding-left: 10px;">
                            <li class="mb-2">● ការផ្តល់ជូន / Offer for: <span
                                    class="label-bold">____________________</span> <span
                                    class="float-right small italic font-bold">(*មួយណាដល់មុន / whichever comes
                                    first)</span></li>
                            <li class="pl-3">● គ្របដណ្តប់លើ៖ ម៉ាស៊ីន, ប៉ុង, ប្រព័ន្ធម៉ាស៊ីនត្រជាក់, ប្រព័ន្ធភ្លើង,
                                និងប្រអប់លេខ។ល។</li>
                            <li class="pl-3 italic text-muted">Covers: Engine, Axle (Front/Rear), Air Conditioning System,
                                Electrical System, Transmission, etc.</li>
                        </ul>

                        <h6 class="label-bold text-uppercase mb-2" style="font-size: 8.5pt; color: var(--brand-indigo);">
                            ការធានាលើរថយន្តអគ្គីសនី / ELECTRIC VEHICLE WARRANTY</h6>
                        <div class="khmer-font pl-2 mb-4" style="font-size: 8pt; line-height: 1.6;">
                            <div class="mb-2">1. <strong>ម៉ូទ័រជំរុញ (Drive Motor):</strong> ធានារយៈពេល ៦០ ខែ ឬ ១៥០,០០០
                                គ.ម (Drive Motor 60 Months or 150,000 KM).</div>
                            <div class="mb-2">2. <strong>ប្រអប់អេឡិចត្រូនិច (Power Electronic Box):</strong> ធានារយៈពេល
                                ៦០ ខែ ឬ ១៥០,០០០ គ.ម (60 Months or 150,000 KM).</div>
                            <div class="mb-2">3. <strong>ប្រអប់បញ្ជា (Vehicle Control Unit):</strong> ធានារយៈពេល ៦០ ខែ ឬ
                                ១៥០,០០០ គ.ម (60 Months or 150,000 KM).</div>
                            <div>4. <strong>អាគុយហាយវ៉ុល (High-Voltage Battery):</strong> ធានារយៈពេល ៦០ ខែ ឬ ១៥០,០០០ គ.ម (60
                                Months or 150,000 KM).</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade print-page p-5 watermark-container" id="page5">
                    <div class="watermark-text">MG CAMBODIA</div>

                    <div class="content-overlay">
                        <h6 class="label-bold border-bottom mb-3 text-uppercase" style="font-size: 8.5pt;">II. ការធានា -
                            សឹក និង រហែក / WARRANTY - WEAR AND TEAR</h6>
                        <table class="table-hd khmer-font">
                            <thead>
                                <tr>
                                    <th style="width:40px">ល.រ</th>
                                    <th>ឈ្មោះ/Item</th>
                                    <th style="width:250px">រយៈពេលធានា/ WARRANTY PERIOD</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 7.8pt;">
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>តម្រងខ្យល់/Air filter</td>
                                    <td>3 ខែ/10,000 គ. ម / 3 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>តម្រងម៉ាស៊ីនត្រជាក់/Particle /pollen filter</td>
                                    <td>3 ខែ/10,000 គ. ម / 3 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>តម្រងប្រេងម៉ាស៊ីន/Oil filter</td>
                                    <td>3 ខែ/5,000 គ. ម / 3 months/5,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td>តម្រងសាំង/Fuel filter</td>
                                    <td>6 ខែ/10,000 គ. ម / 6 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td>ប៊ូស៊ី/Spark plugs</td>
                                    <td>3 ខែ/5,000 គ. ម / 3 months/5,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">6</td>
                                    <td>ស្បែកហ្វ្រាំង / ឌីសហ្វ្រាំង/Brake pads/break disc</td>
                                    <td>6 ខែ/10,000 គ. ម / 6 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">7</td>
                                    <td>ឌីស អំ យ៉ាហ្ស/Clutch disc</td>
                                    <td>6 ខែ/10,000 គ. ម / 6 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">8</td>
                                    <td>សំបកកង់/Tires</td>
                                    <td>6 ខែ/10,000 គ. ម / 6 months/10,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">9</td>
                                    <td>អាគុយ/Battery</td>
                                    <td>12 ខែ/20,000 គ. ម / 12 months/20,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">10</td>
                                    <td>ថ្មសោ/Remote handset battery</td>
                                    <td>3 ខែ/5,000 គ. ម / 3 months/5,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">11</td>
                                    <td>ផ្លិតទឹក/Wiper blades</td>
                                    <td>3 ខែ/5,000 គ. ម / 3 months/5,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">12</td>
                                    <td>អំពូល/ Light Bulbs</td>
                                    <td>3 ខែ/5,000 គ. ម / 3 months/5,000 KM</td>
                                </tr>
                                <tr>
                                    <td class="text-center">13</td>
                                    <td>តម្រង់កង់/ Wheel balance & Wheel alignment</td>
                                    <td>6 ខែ/10,000 គ. ម / 6 months/10,000 KM</td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="label-bold border-bottom mb-2 mt-4 text-uppercase" style="font-size: 8.5pt;">III.
                            ផែនការថែទាំទៀងទាត់ / REGULAR MAINTENANCE</h6>
                        <p class="khmer-font mb-2" style="font-size: 8pt; line-height: 1.5;">
                            ផែនការថែទាំធម្មតាមានសមាសភាពនៃសេវាកម្មតូច (ប្រភេទ A) និងសេវាកម្មធំ (ប្រភេទ B)។
                            ប្រភេទទាំងពីរត្រូវបានអនុវត្តនៅចន្លោះពេលដែលបានពិពណ៌នាខាងក្រោម។<br>
                            <span class="italic text-muted">The regular maintenance plan is composed of minor Service (Type
                                A) and Major Service (Type B) services. The two types are carried out at the intervals
                                described below.</span>
                        </p>

                        <table class="table-hd text-center khmer-font" style="font-size: 7.5pt;">
                            <thead>
                                <tr class="bg-light">
                                    <th>Maintenance Type</th>
                                    <th>A</th>
                                    <th>B</th>
                                    <th>A1</th>
                                    <th>B1</th>
                                    <th>A2</th>
                                    <th>B2</th>
                                    <th>A3</th>
                                    <th>B3</th>
                                    <th>A4</th>
                                    <th>B4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="font-bold">Distance (x 1,000 km) / Time (month)</td>
                                    <td>10/6</td>
                                    <td>20/12</td>
                                    <td>30/18</td>
                                    <td>40/24</td>
                                    <td>50/30</td>
                                    <td>60/36</td>
                                    <td>70/42</td>
                                    <td>80/48</td>
                                    <td>90/54</td>
                                    <td>100/60</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="khmer-font mt-3" style="font-size: 7.5pt; line-height: 1.5;">
                            <strong>ចំណាំ/ Note :</strong>
                            <div class="ml-2">1. ចំនួនគីឡូម៉ែត្រ ឬ ខែ (គិតយកមួយណាដល់មុន)។ / Number of kilometers or month
                                (whichever comes first).</div>
                            <div class="ml-2">2. ប្រសិនបើយានយន្តត្រូវបានបំពាក់ដោយមុខងាររំលឹកការថែទាំ
                                សូមយោងទៅលើសារដាស់តឿននៅលើអេក្រង់។ / If the vehicle is equipped with a maintenance reminder
                                function. Please refer to the prompt message on the screen.</div>
                            <div class="ml-2">3. សម្រាប់មគ្គុទ្ទេសក៍ពេញលេញចំពោះការធានា សូមយោងទៅសៀវភៅណែនាំ។ / For a
                                complete guide to the warranty please refer to the handbook.</div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade print-page p-5 watermark-container" id="page6">
                    <div class="watermark-text">MG CAMBODIA</div>

                    <div class="content-overlay">
                        <h6 class="label-bold border-bottom mb-3 text-uppercase" style="font-size: 8.5pt;">
                            ❖ លក្ខខណ្ឌនៃការមិនធានារបស់ក្រុមហ៊ុន/ This Warranty Scope DOES NOT Cover
                        </h6>

                        <div class="khmer-font" style="font-size: 7.6pt; line-height: 1.5; text-align: justify;">
                            <div class="mb-2">
                                <strong>1.</strong> ការខូចដែលបណ្តាលមកពីការរក្សាទុកក្នុងឃ្លាំងមិនត្រឹមត្រូវ
                                ដូចជាការខូចខាតម៉ាស៊ីន ឬប្រព័ន្ធប្រេងដែលបណ្តាលមកពីការជាប់គាំងនៃប្រេងម៉ាស៊ីន អាគុយមិនសាក
                                ឬការប្រតិកម្មថ្នាំ។/
                                Any failure caused by incorrect storage methods, such as engine or fuel system damage caused
                                by engine oil stagnation, battery becoming discharged or deterioration of paintwork caused
                                by the environment.
                            </div>

                            <div class="mb-2">
                                <strong>2.</strong> គ្រឿងបន្លាស់មួយចំនួនដែលត្រូវបានតម្រូវអោយផ្លាស់ប្តូរ
                                ឬថែទាំដោយសារការខូចខាត ឬត្រូវបានទទួលរងនូវការសឹក និងរហែក ឧទាហរណ៍៖ សំបកកង់ ស្បែកហ្វាំង
                                ស្បែកអំប្រាយ៉ាស អំពូល ផ្លិតទឹក ជាដើម។/
                                Those items which require replacement or maintenance due to damage or which have been
                                subjected to fair wear and tear e.g. tires, brake pads, clutch lining, bulbs, wiper blades
                                etc...
                            </div>

                            <div class="mb-2">
                                <strong>3.</strong> ការខូចខាតដែលបណ្តាលមកពីការខ្វះការថែទាំ ឬមិនបានប្រើប្រាស់ប្រេងឥន្ធនៈ
                                ប្រេងរំអិល ទឹកស្អំម៉ាស៊ីន ហ្គាសប្រព័ន្ឋម៉ាស៊ីនត្រជាក់
                                ដូចដែលបានណែនាំនៅក្នុងសៀវភៅណែនាំរបស់រោងចក្រ។
                                ការខូចខាតបណ្តាលមកពីការមិនអនុវត្តតាមការណែនាំពីការថែទាំនៅក្នុងសៀវភៅណែនាំរបស់រោងចក្រ។/
                                Any failure caused by lack of maintenance or not using fuel, oil, lubricants or coolants, as
                                recommended in the owner Handbook. Damage caused by not conforming to the maintenance
                                instructions in the Owner Handbook.
                            </div>

                            <div class="mb-2">
                                <strong>4.</strong> ការខូចដែលបណ្តាលមកពីសារធាតុ (សារធាតុគីមី ភ្លៀងអាស៊ីត) ថ្ម ព្រិល រន្ទះ
                                រញ្ជួយដី ទឹកជំនន់ ។ល។/
                                Damage caused by falling substances (chemical substances, acid rain), stones, hail,
                                lightning, earthquake, floods, etc.....
                            </div>

                            <div class="mb-2">
                                <strong>5.</strong> ការខូចដែលបណ្តាលមកពីការប្រើប្រាស់គ្រឿងបន្លាស់ដែលមិនទទួលស្គាល់ដោយរោងចក្រ។/
                                Vehicle damage caused by using products not Authorized by factory.
                            </div>

                            <div class="mb-2">
                                <strong>6.</strong> ការខូចដែលបណ្តាលមកពីការប៉ះទង្គិច ភ្លើង ការផ្ទុះ ចោរកម្ម ឬប៉ុនប៉ងលួច
                                លក្ខខណ្ឌអាកាសធាតុមិនល្អ គ្រោះថ្នាក់ចរាចរណ៍ កុបកម្ម។/
                                Damage caused by collision, fire, explosion, theft or attempted theft, adverse weather
                                conditions, traffic accidents, riots.
                            </div>

                            <div class="mb-2">
                                <strong>7.</strong> ការខូចដែលបណ្តាលមកពីការប្រើប្រាស់មិនបានត្រឹមត្រូវ និងការថែទាំមិនល្អ
                                រួមទាំងការផ្ទុកលើសទម្ងន់ ឬការប្រណាំង។/
                                Damage caused by inappropriate use and poor maintenance, including overloading or racing.
                            </div>

                            <div class="mb-2">
                                <strong>8.</strong> ការខូចដែលបណ្តាលមកពីការកែប្រែ ឬ កែច្នៃរថយន្ត (រួមទាំងតួ តួសាក់ស៊ី ម៉ាស៊ីន
                                គ្រឿងអេឡិចត្រូនិច ឬប្រព័ន្ធផ្សេងៗ)/
                                Damage caused by modification to the vehicle (including body, chassis, power train,
                                electronic or other systems).
                            </div>

                            <div class="mb-2">
                                <strong>9.</strong> យានជំនិះទាំងឡាយណាដែលបានផ្តាច់កុងទ័រវាស់ល្បឿន ឬផ្លាស់ប្តូរ កែ
                                (ដោយយាន្តដ្ឋានគ្មានការអនុញ្ញាតពីរោងចក្រ) ។/
                                Any vehicle that has had the odometer disconnected or changed (other than a change by
                                Authorized Dealer or Distributor as a result of a repair or adjustment carried out under the
                                Carload Quality Warranty).
                            </div>

                            <div class="mb-2">
                                <strong>10.</strong> រាល់ការចំណាយហិរញ្ញវត្ថុ ដូចជាការខាតបង់ការប្រើប្រាស់យានជំនិះ
                                ខាតបង់ប្រាក់ឈ្នួល ភាពរអាក់រអួល ថ្លៃស្តុកទុក ថ្លៃជួលយានជំនិះ កន្លែងស្នាក់នៅ អាហារ
                                ឬថ្លៃធ្វើដំណើរ។/
                                Any financial expenditure, such as loss of vehicle use, loss of wages, inconvenience,
                                storage costs, cost of vehicle rental, accommodation, meals or travel expenses.
                            </div>

                            <div class="mb-2">
                                <strong>11.</strong> រាល់ការជួសជុល រថយន្តក្រោយពេលលក់ ដែលតម្រូវឱ្យរថយន្តបំពេញតាមច្បាប់របស់
                                រដ្ឋាភិបាលក្នុងតំបន់ លើកលែងតែអ្វីដែលតម្រូវដោយច្បាប់ ឬកំណត់ដោយរោងចក្រ។/
                                Any repair and /or refit the vehicle after sold that is required for the car to meet local
                                government legislation, except those required by law or stipulated by factory.
                            </div>

                            <div class="mb-2">
                                <strong>12.</strong>
                                ការខូតដែលកើតឡើងដោយសារការប្រើប្រាស់មិនបានត្រឹមត្រូវក្នុងកំឡុងពេលរថយន្តកំពុងមានបញ្ហា។/
                                Damage caused by improper disposal while vehicle problems occur.
                            </div>

                            <div class="mb-2">
                                <strong>13.</strong> រាល់ការជួសជុលនិងការថែទាំណាមួយដែល អនុវត្តដោយយាន្តដ្ឋាន
                                គ្មានការអនុញ្ញាតពីរោងចក្រ ។/
                                Any reparation and maintenance are carried out by an unauthorized dealer.
                            </div>
                        </div>

                        <div class="mt-4 p-3 border border-dark bg-light text-center khmer-font"
                            style="font-size: 8.5pt;">
                            <p class="font-bold mb-1">ចំណាំ៖ បន្ទាប់ពីទទួលបានការពន្យល់រួចមក ខ្ញុំយល់ច្បាស់អំពីលក្ខខណ្ឌធានា
                                និងគោលការណ៍ធានារបស់ក្រុមហ៊ុន។</p>
                            <p class="mb-3 italic">I acknowledge that I have received a full explanation regarding the
                                terms and policies of the warranty.</p>
                            <div class="row mt-4">
                                <div class="col-6 text-center">
                                    <div class="sig-line label-bold" style="font-size: 7.5pt;">ហត្ថលេខាអតិថិជន / Customer
                                        Signature</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="sig-line label-bold" style="font-size: 7.5pt;">ហត្ថលេខាអនុម័ត / Authorized
                                        Signature</div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>


            </div>
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('full-document-wrapper');
            const panes = document.querySelectorAll('.tab-pane');

            panes.forEach(p => {
                p.classList.add('show', 'active');
            });

            const opt = {
                margin: 0,
                filename: 'Full_Authorized_Pack_{{ $contract->contract_number }}.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 3,
                    useCORS: true,
                    letterRendering: true,
                    scrollY: 0
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy']
                }
            };

            Swal.fire({
                title: 'Generating Pack...',
                text: 'Finalizing 6-Page Authorized Pack',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            html2pdf().set(opt).from(element).save().then(() => {
                panes.forEach((p, index) => {
                    if (index !== 0) p.classList.remove('show', 'active');
                });
                Swal.fire('Success', 'PDF Pack Downloaded', 'success');
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
