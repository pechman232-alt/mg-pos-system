@extends('layouts.homePages')
<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&family=JetBrains+Mono:wght@800&display=swap"
    rel="stylesheet">
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
            -webkit-font-smoothing: antialiased;
        }

        .khmer-font {
            font-family: 'Siemreap', sans-serif !important;
            line-height: 1.5;
        }

        .label-bold {
            font-weight: 800;
        }

        .container-full {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1rem 2rem !important;
        }

        /* 🌟 ការពារការដាច់ទំព័រ */
        .avoid-page-break {
            page-break-inside: avoid !important;
        }

        /* ================= ULTIMATE PRINT ENGINE (FIX 1 PAGE & FOOTER) ================= */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0mm !important;
            }

            /* ១. លាក់អ្វីៗទាំងអស់នៅលើវេបសាយ (Header, Footer, Menu, Sidebar...) */
            body * {
                visibility: hidden !important;
            }

            /* ២. បង្ហាញតែប្រអប់ឯកសារកិច្ចសន្យា និងរបស់នៅខាងក្នុងវាប៉ុណ្ណោះ */
            #full-document-wrapper,
            #full-document-wrapper * {
                visibility: visible !important;
            }

            /* ៣. ទាញឯកសារមកដាក់លើគេបង្អស់ ឆ្វេងគេបង្អស់ ឱ្យពេញក្រដាស មិនខ្វល់ពី Layout ចាស់ */
            #full-document-wrapper {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none !important;
            }

            /* ៤. បង្ខំឱ្យបើក Tabs ទាំង ៦ លោតមកជាជួរ (បំបែកការលាក់របស់ Bootstrap) */
            #full-document-wrapper .tab-content {
                display: block !important;
            }

            #full-document-wrapper .tab-pane {
                display: flex !important;
                flex-direction: column !important;
                opacity: 1 !important;
                visibility: visible !important;

                /* កំណត់ទំហំ A4 ពិតប្រាកដ */
                width: 210mm !important;
                height: 297mm !important;
                padding: 15mm !important;
                /* គម្លាតពីគែមក្រដាសកុំឱ្យដាច់អក្សរ */
                box-sizing: border-box !important;

                /* កាត់ទំព័ររាល់ពេលចប់ ១ Tab */
                page-break-after: always !important;
                page-break-inside: avoid !important;

                /* លុបពណ៌ផ្ទៃខាងក្រោយរំខានផ្សេងៗ */
                background: #fff !important;

            }

            /* ការពារកុំឱ្យចេញក្រដាសសនៅទំព័រដំបូង */
            #full-document-wrapper .tab-pane:first-child {
                page-break-before: avoid !important;
            }

            /* លុប Animation របស់ Bootstrap ដែលខំប្រឹងលាក់ទំព័រ */
            .fade {
                transition: none !important;
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
            filter: brightness(0) saturate(100%) invert(81%) sepia(38%) saturate(608%) hue-rotate(358deg) brightness(91%) contrast(89%);
            transition: 0.3s;
        }

        .card-brand-logos img:hover {
            filter: brightness(1.2);
            transform: scale(1.1);
        }

        :root {
            --pdi-border: #000000;
            --pdi-bg-light: #f8fafc;
            --pdi-text-muted: #64748b;
        }

        .pdi-content {
            font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif !important;
            color: #000;
            line-height: 1.4;
        }

        .pdi-section-title {
            background-color: var(--pdi-bg-light);
            border: 1px solid var(--pdi-border);
            padding: 6px 10px;
            font-weight: 800;
            font-size: 8.5pt;
            text-transform: uppercase;
            margin-bottom: 0;
        }

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

        .diagram-box {
            height: 160px;
            background: #ffffff;
            border: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(#cbd5e1 0.7px, transparent 0.7px);
            background-size: 15px 15px;
            overflow: hidden;
        }

        .vehicle-diagram-img {
            max-height: 130px;
            width: auto;
            opacity: 0.65;
            filter: grayscale(100%);
            z-index: 1;
            object-fit: contain;
        }

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

        .diagram-note {
            position: absolute;
            bottom: 5px;
            right: 12px;
            font-size: 7pt;
            color: #000;
            font-style: italic;
        }

        :root {
            --tf-black: #000000;
            --tf-gray: #4a4a4a;
            --tf-light-gray: #f8fafc;
            --tf-border: #d1d5db;
        }

        .contract-container {
            font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif !important;
            color: var(--tf-black);
            line-height: 1.5;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .italic {
            font-style: italic;
        }

        .contract-header {
            border-bottom: 2px solid var(--tf-black);
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .info-grid {
            font-size: 9.5pt;
            margin-bottom: 20px;
            color: #1e293b;
        }

        .info-label {
            min-width: 130px;
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            font-weight: 800;
            color: #000;
            border-bottom: 1px dashed #cbd5e1;
            flex-grow: 1;
            padding-left: 8px;
            transition: all 0.3s ease;
        }

        .intro-box {
            /* background-color: var(--tf-light-gray); */
            /* border: 1px solid var(--tf-border);
                    border-radius: 8px;
                    padding: 15px; */
            margin-bottom: 10px;
            font-size: 8.5pt;
        }

        .term-item {
            margin-bottom: 10px;
        }

        .term-item:last-child {
            margin-bottom: 0;
        }

        @media print {
            .print-page {
                padding: 0 !important;
            }

            .intro-box {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
            }
        }

        .moul-bold {
            font-family: 'Khmer OS Muol Light', 'Moul', serif !important;
            font-size: 18pt;
            color: #000;
            margin-bottom: 2px;
            line-height: 1.5;
        }

        .english-bold {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 900;
            font-size: 11pt;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #000;
        }

        .text-highlight {
            color: #4f46e5;
            background-color: #f5f3ff;
            padding: 0 8px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
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
            border-bottom: 2px solid #ab171c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

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
            color: #ab171c;
        }

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

        /* ប្រអប់ព័ត៌មានរថយន្តផ្នែកខាងលើ */
        .vehicle-info-header {
            background-color: #f8fafc;
            /* ពណ៌ផ្ទៃដូចផ្នែក Offer */
            border-left: 4px solid var(--brand-indigo);
            /* បន្ទាត់ចំហៀងពណ៌ខៀវ */
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-item-group {
            display: flex;
            flex-direction: column;
        }

        .info-item-label {
            font-size: 7.5pt;
            color: #64748b;
            /* ពណ៌ប្រផេះស្រាល */
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-item-value {
            font-size: 11pt;
            font-weight: 900;
            color: #1e293b;
            /* ពណ៌ខ្មៅដិត */
            text-transform: uppercase;
        }

        .divider-line {
            width: 1px;
            height: 35px;
            background-color: #e2e8f0;
            margin: 0 20px;
        }

        .btn-sm-clean {
            background: white !important;
            padding: 6px 16px !important;
            /* កំណត់ទំហំឱ្យតូចសមសួន */
            border-radius: 8px !important;
            /* រាងមូលស្អាតបែបទំនើប */
            font-size: 11px !important;
            /* ទំហំអក្សរតូចងាយអាន */
            font-weight: 800 !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            /* ស្រមោលស្រាលៗ */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: inline-flex;
            align-items: center;
        }

        /* 🌟 ចលនា Animation ពេល Hover */
        .btn-sm-clean:hover {
            transform: translateY(-2px) scale(1.03);
            /* រីកធំបន្តិច និងអណ្តែតឡើងលើ */
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
            /* បន្ថែមពន្លឺបៃតងស្រាលៗ */
            color: #059669 !important;
            /* ប្តូរពណ៌អក្សរឱ្យដិតជាងមុនបន្តិច */
        }

        /* ចលនាពេលចុច (Active) */
        .btn-sm-clean:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-close-outline {
            background: transparent !important;
            color: white !important;
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
            /* បន្ទាត់ស្តើងព្រាលៗ */
            box-shadow: none !important;
            text-decoration: none;
        }

        /* Animation ពេល Hover */
        .btn-close-outline:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            /* ចេញពណ៌សព្រាលៗខាងក្រោយ */
            border-color: white !important;
            color: white !important;
            transform: translateY(-2px) scale(1.03);
            /* អណ្តែតឡើងដូចប៊ូតុងផ្សេងទៀត */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        /* ចលនាពេលចុច (Active) */
        .btn-close-outline:active {
            transform: translateY(0) scale(0.98);
        }
        .id-card-placeholder-pro {
        height: 250px; /* កំណត់កម្ពស់ឱ្យល្មមសម្រាប់ក្រដាស A4 */
        width: 100%;
        max-width: 450px; /* បង្រួមទទឹងមិនឱ្យរីកពេញទំព័រពេក */
        margin: 15px auto 35px auto; /* ដាក់កណ្តាល និងបន្ថែមគម្លាតលើក្រោម */
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 10px; /* បន្ថែម Padding ខាងក្នុងបន្តិច */
        box-sizing: border-box;
    }

    .id-card-placeholder-pro img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* ធានាថារូបភាពបង្ហាញពេញលេញមិនដាច់ */
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* បន្ថែមស្រមោលឱ្យរូបភាពលេចឡើងស្អាត */
    }
    /* ========================================================
   VIP MEMBERSHIP CARD TEMPLATE (EXACT MATCH)
   ======================================================== */
   .vip-card-wrapper {
        display: flex;
        flex-direction: column; /* តម្រៀបកាតមុខ និងកាតក្រោយជាជួរលើក្រោម */
        align-items: center;    /* ទាញកាតឱ្យមកចំកណ្តាល (Center Horizontal) */
        justify-content: center;
        gap: 30px;              /* គម្លាតរវាងកាតទី១ និងកាតទី២ */
        width: 100%;            /* ត្រូវមាន 100% ទើបវាអាច Center បានត្រឹមត្រូវ */
        margin: 20px auto 30px auto; /* បន្ថែមគម្លាតពីលើ និងក្រោម */
    
}

.vip-card {
    width: 400px;
    height: 250px;
    background: linear-gradient(135deg, #1f1f1f 0%, #121212 100%);
    border-radius: 6px;
    position: relative;
    box-sizing: border-box;
    overflow: hidden;
    color: #d4af37; /* ពណ៌មាស / Gold Color */
    font-family: 'Plus Jakarta Sans', 'Siemreap', sans-serif;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

/* --------- FRONT CARD --------- */
.vip-front-top {
    display: flex;
    flex-direction: column;
    padding: 20px 25px;
}

.vip-logo-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100px;
}

.vip-t-circle {
    width: 45px;
    height: 45px;
    border: 1.5px solid #d4af37;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 24px;
    font-family: serif;
    margin-bottom: 8px;
    margin-left: 10px;
}

.vip-tf-text {
    font-size: 11px;
    letter-spacing: 1px;
    font-weight: 600;
}

.vip-title-section {
        position: absolute;
        top: 80px; /* គម្លាតពីលើចុះក្រោម (អាចសារ៉េឡើងចុះបន្តិចបានតាមការចូលចិត្ត) */
        left: 0;
        width: 100%; /* 🌟 លាតពេញទទឹងកាតដើម្បីអាចរកចំណុចកណ្តាលឃើញ */
        display: flex;
        flex-direction: column;
        align-items: center; /* 🌟 ទាញទាំងអក្សរ និងប្រអប់ ឱ្យមកកណ្តាលផ្តេក (Center Horizontal) */
    }

    .vip-mem-title {
        font-size: 15px; /* ដំឡើងទំហំអក្សរបន្តិចឱ្យលេចធ្លោ */
        font-weight: 700;
        letter-spacing: 4px;
        margin-bottom: 10px;
        color: #d4af37;
        text-align: center;
    }

    /* 🌟 ប្រអប់ពណ៌សសម្រាប់ដាក់ឈ្មោះ */
    .vip-white-bar {
        background: #ffffff;
        height: 26px;         
        width: 280px;        /* ពង្រីកទទឹងប្រអប់បន្តិចឱ្យសមនឹងកាតកណ្តាល */
        color: #000000;      
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 1px;
        display: flex;
        align-items: center;  
        justify-content: center; 
        border-radius: 2px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2); /* បន្ថែមស្រមោលឱ្យប្រអប់លេចឡើង */
    }

.vip-logos-bottom {
    position: absolute;
    bottom: 25px;
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 30px;
    align-items: center;
}

.vip-logos-bottom img {
    height: 35px;
    /* បំប្លែង Logo ធម្មតាឱ្យទៅជាពណ៌មាស / Convert to Gold */
    filter: brightness(0) saturate(100%) invert(77%) sepia(21%) saturate(836%) hue-rotate(7deg) brightness(94%) contrast(89%);
}

/* --------- BACK CARD --------- */
.vip-back-top {
    display: flex;
    justify-content: space-between;
    padding: 15px 25px;
    font-size: 8px;
    line-height: 1.5;
    color: #c9a74b;
}

.vip-back-top .contacts span {
    color: #888; /* ពណ៌ប្រផេះសម្រាប់ឈ្មោះក្រុមហ៊ុន */
}

.vip-back-top .website {
    display: flex;
    align-items: flex-end;
}

.vip-white-strip {
    background: #ffffff;
    width: 100%;
    padding: 15px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 10px;
}

.vip-watermark {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: repeating-linear-gradient(-35deg, transparent, transparent 20px, rgba(0,0,0,0.04) 20px, rgba(0,0,0,0.04) 40px);
    z-index: 1;
}

.vip-customer-name {
    position: relative;
    z-index: 2;
    color: #444;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.vip-back-terms {
    padding: 20px 25px;
    text-align: center;
    font-size: 8.5px;
    line-height: 1.8;
    color: #d4af37;
}
/* 🌟 PDI Header Styling */
.pdi-header-box {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 3px solid #1e293b;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .pdi-brand-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #0f172a;
        margin: 0;
    }

    .pdi-doc-title {
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        color: #475569;
        margin: 0;
        letter-spacing: 1px;
    }

    /* 🌟 ប្រអប់ព័ត៌មាន (Info Grid) ស្ទីល Professional */
    .pdi-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background-color: #f8fafc;
        margin-bottom: 25px;
        overflow: hidden;
    }

    .pdi-info-col {
        padding: 12px 18px;
    }

    .pdi-info-col:first-child {
        border-right: 1px solid #cbd5e1; 
    }

    .pdi-info-row {
        display: flex;
        align-items: flex-end;
        margin-bottom: 10px;
        font-size: 11.5px;
    }

    .pdi-info-row:last-child {
        margin-bottom: 0;
    }

    .pdi-label {
        font-weight: 700;
        color: #64748b;
        width: 85px; 
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .pdi-value {
        flex-grow: 1;
        font-weight: 800;
        color: #0f172a;
        border-bottom: 1px dashed #94a3b8; 
        padding-left: 5px;
        padding-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; 
    }

    .pdi-highlight {
        color: #b91c1c !important; 
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 1px;
    }

    .font-family-mono {
        font-family: 'JetBrains Mono', monospace;
    }
    /* 🌟 Professional Signature Section */
    .signature-wrapper {
        display: flex;
        justify-content: space-between;
        gap: 50px; /* បង្កើតគម្លាតកណ្តាលឱ្យធំទូលាយ */
        margin-top: 40px; 
    }

    .sig-inline-block {
        flex: 1;
        display: flex;
        align-items: flex-end; /* ទាញអក្សរ និងបន្ទាត់ឱ្យស្មើគូទគ្នា */
    }

    .sig-inline-label {
        font-weight: 800;
        font-size: 11px;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 15px;
        padding-bottom: 5px; /* លើកអក្សរឱ្យផុតពីបន្ទាត់បន្តិច */
        white-space: nowrap;
    }

    .sig-inline-line {
        flex-grow: 1;
        height: 50px;
        border-bottom: 1.5px dashed #94a3b8; /* បន្ទាត់ដាច់ៗពណ៌ប្រផេះ */
        background-color: #f8fafc; /* ពណ៌ផ្ទៃព្រាលៗសម្គាល់កន្លែងស៊ីញ៉េ */
        border-radius: 4px 4px 0 0;
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
                    <button onclick="downloadPDF()" class="btn btn-white btn-sm-clean text-success">
                        <i class="fas fa-file-pdf mr-1"></i> PDF PACK
                    </button>
                    {{-- <button onclick="window.print()" 
                class="btn btn-white btn-sm-clean text-danger">
                <i class="fas fa-print mr-1"></i> PRINT
            </button> --}}
                    <a href="{{ route('contracts.index') }}" class="btn-sm-clean btn-close-outline">
                        CLOSE
                    </a>
                </div>
            </div>
            <ul class="nav nav-pills justify-content-center gap-2 mb-4 bg-white p-2 rounded-2xl shadow-sm border">
                <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#page1">1. Contract</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page2">2. ID/Member</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page3">3. PDI Check</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page4">4. Warranty I</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page5">5. Maintenance</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#page6">6. Exclusions</a></li>
               
            </ul>
        </div>

        <div id="full-document-wrapper" class="document-wrapper contract-container">
            <div class="tab-content">
                <div class="tab-pane fade show active print-page p-4" id="page1">

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
                            <span class="badge badge-success"
                                style="background-color: #10b981; color: white; padding: 4px 12px; border-radius: 50px; font-size: 7pt;">VALIDATED</span>
                        </div>
                    </div>

                    <div class="row info-grid khmer-font">
                        <div class="col-7">
                            <div class="d-flex mb-2 align-items-end">
                                <span class="info-label">អតិថិជន / Name:</span>
                                <span class="info-value">{{ $contract->customer_name_kh }} /
                                    {{ strtoupper($contract->customer_name) }}</span>
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
                                <span class="source-badge font-bold">{{ $contract->source_id }}</span>
                            </div>
                            <div class="d-flex mb-2 justify-content-end align-items-center">
                                <span class="mr-2">ផ្នែកលក់ / Sales:</span>
                                <span class="label-bold">{{ $contract->sale_person }}</span>
                            </div>
                            <div class="d-flex justify-content-end text-muted small mt-1">
                                <span class="mr-2">កាលបរិច្ឆេទ / Date:</span>
                                <span
                                    class="font-bold text-dark">{{ \Carbon\Carbon::parse($contract->order_date)->format('d-M-Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="intro-box khmer-font text-center">
                        <div class="mb-2">
                            <strong>អ្នកទិញដែលចុះហត្ថលេខាខាងក្រោមនេះយល់ព្រមទិញ ហើយក្រុមហ៊ុន ធី អេហ្វ ម៉ូធ័រ (ខេមបូឌា)
                                យល់ព្រមលក់ទំនិញដូចខាងក្រោម។</strong><br>
                            <span class="italic text-muted">The undersigned Purchaser hereby agrees to buy and TF Motors
                                Cambodia Co., Ltd (Seller) agrees to sell the undermentioned goods.</span>
                        </div>
                        <div class="small">
                            <strong>កិច្ចសន្យានេះពុំមានសុពលភាពទេ
                                ប្រសិនមិនមានការអនុញ្ញាតិពីអ្នកគ្រប់គ្រងទូទៅរបស់អ្នកលក់។</strong><br>
                            <span class="italic text-muted">This contract is not valid unless authorized by the General
                                Manager.</span>
                        </div>
                    </div>
                    <table class="table-hd khmer-font avoid-page-break">
                        <thead>
                            <tr>
                                <th width="50">N°</th>
                                <th>បរិយាយ / DESCRIPTION</th>
                                <th width="80">ចំនួន / QTY</th>
                                <th width="140">ទឹកប្រាក់ / AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="avoid-page-break">
                                <td class="text-center align-middle" style="font-weight: 800; color: #64748b;">01</td>
                                <td class="p-3">
                                    <div class="mb-2 pl-1">
                                        <div class="d-flex align-items-baseline mb-2">
                                            <span class="mr-2 text-muted" style="font-size: 8pt;">●</span>
                                            <span class="text-muted small uppercase mr-2"
                                                style="font-size: 7.5pt; width: 90px;">Vehicle Model:</span>
                                            <span
                                                class="font-weight-normal text-dark mb-0 text-uppercase">{{ $contract->model }}</span>
                                        </div>
                                        <div class="d-flex align-items-baseline">
                                            <span class="mr-2 text-muted" style="font-size: 8pt;">●</span>
                                            <span class="text-muted small uppercase mr-2"
                                                style="font-size: 7.5pt; width: 90px;">Exterior Color:</span>
                                            <span class="font-weight-bold text-dark mb-0">{{ $contract->color }}</span>
                                        </div>
                                    </div>
                                    @php
                                        $offers = is_array($contract->offers)
                                            ? $contract->offers
                                            : explode(',', $contract->offers);
                                        $offers = array_filter(array_map('trim', $offers));
                                    @endphp

                                    @if (count($offers) > 0)
                                        <div class="mt-2 p-2 rounded"
                                            style="background-color: #f8fafc; border-left: 3px solid var(--brand-indigo);">
                                            <div class="small font-weight-bold text-primary mb-1 uppercase"
                                                style="font-size: 7.5pt;">
                                                <i class="fas fa-gift mr-1"></i> Special Offers
                                            </div>
                                            <ul class="list-unstyled mb-0" style="line-height: 2.0;">
                                                @foreach ($offers as $index => $item)
                                                    <li class="khmer-font text-dark d-flex align-items-start"
                                                        style="font-size: 8.5pt;">
                                                        <span class="mr-2 text-muted">{{ $index + 1 }}.</span>
                                                        <span>{{ $item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="h6 font-weight-bold">{{ $contract->qty }}</span>
                                    <div class="small text-muted uppercase" style="font-size: 7pt;"></div>
                                </td>
                                <td class="text-center align-middle px-3">
                                    <span class="h6 font-weight-bold text-dark">
                                        ${{ number_format($contract->sub_total, 2) }}
                                    </span>

                                    <div class="small text-muted uppercase"
                                        style="font-size: 7pt; letter-spacing: 0.5px;">

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="khmer-font">
                            {{-- ទឹកប្រាក់សរុប / Sub-Total --}}
                            <tr>
                                <td colspan="3" class="text-right align-middle py-2 pr-3"
                                    style="border: none !important; text-align: right;">
                                    <span class="font-bold text-dark" style="font-size: 8.5pt;">ទឹកប្រាក់សរុប / SUB
                                        TOTAL:</span>
                                </td>
                                <td class="text-center align-middle font-bold px-3 text-dark"
                                    style="border-top: 1.5px solid #000 !important; border-bottom: 1px solid #000 !important; border-left: 1.5px solid #000 !important; border-right: 1.5px solid #000 !important; width: 140px;">
                                    ${{ number_format($contract->sub_total, 2) }}
                                </td>
                            </tr>

                            {{-- បញ្ចុះតម្លៃ / Discount (បង្ហាញជានិច្ចទោះជា 0 ក៏ដោយ) --}}
                            <tr>
                                <td colspan="3" class="text-right align-middle py-1 pr-3"
                                    style="border: none !important; text-align: right;">
                                    <span class="font-bold text-danger" style="font-size: 8.5pt;">បញ្ចុះតម្លៃ /
                                        DISCOUNT:</span>
                                </td>
                                <td class="text-center align-middle font-bold text-danger px-3"
                                    style="border-top: none !important; border-bottom: 1px solid #000 !important; border-left: 1.5px solid #000 !important; border-right: 1.5px solid #000 !important; width: 140px;">
                                    -${{ number_format($contract->discount ?? 0, 2) }}
                                </td>
                            </tr>

                            {{-- ប្រាក់កក់ / Deposit --}}
                            @if ($contract->deposit > 0)
                                <tr>
                                    <td colspan="3" class="text-right align-middle py-1 pr-3"
                                        style="border: none !important; text-align: right;">
                                        <span class="font-bold text-primary" style="font-size: 8.5pt;">ប្រាក់កក់ /
                                            DEPOSIT:</span>
                                    </td>
                                    <td class="text-center align-middle font-bold text-primary px-3"
                                        style="border-top: none !important; border-bottom: 1px solid #000 !important; border-left: 1.5px solid #000 !important; border-right: 1.5px solid #000 !important; width: 140px;">
                                        -${{ number_format($contract->deposit, 2) }}
                                    </td>
                                </tr>
                            @endif

                            {{-- សរុបចុងក្រោយ / Grand Total --}}
                            <tr class="bg-light">
                                <td colspan="3" class="text-right align-middle py-2 pr-3"
                                    style="border: none !important; text-align: right;">
                                    <span class="font-black text-uppercase text-dark" style="font-size: 9.5pt;">សរុប /
                                        GRAND TOTAL:</span>
                                </td>
                                {{-- 🌟 តភ្ជាប់បន្ទាត់ឆ្វេងស្តាំកុំឱ្យដាច់ --}}
                                <td class="text-center align-middle font-black py-2 px-3 text-dark"
                                    style="border-top: none !important; border-bottom: 1px solid #000 !important; border-left: 1.5px solid #000 !important; border-right: 1.5px solid #000 !important; font-size: 11pt; width: 140px;">
                                    ${{ number_format($contract->grand_total, 2) }}
                                </td>
                            </tr>

                            {{-- ប្រភេទទូទាត់ / Payment Type --}}
                            <tr>
                                <td colspan="3" class="text-right align-middle py-2 pr-3"
                                    style="border: none !important; text-align: right;">
                                    <span class="font-bold text-muted"
                                        style="font-size: 8pt; letter-spacing: 0.5px;">ប្រភេទទូទាត់ / PAYMENT TYPE:</span>
                                </td>
                                {{-- 🌟 បិទគូទប្រអប់ដោយប្រើបន្ទាត់ក្រាស់ --}}
                                <td class="text-center align-middle font-bold px-3 text-primary text-uppercase"
                                    style="border-top: none !important; border-bottom: 1.5px solid #000 !important; border-left: 1.5px solid #000 !important; border-right: 1.5px solid #000 !important; font-size: 8.5pt; letter-spacing: 0.5px; width: 140px;">
                                    {{ $contract->payment_type ?? 'CASH' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="terms-wrapper khmer-font avoid-page-break">
                        <div class="label-bold text-uppercase border-bottom mb-2 pb-1">
                            លក្ខខណ្ឌ / Terms and Conditions
                        </div>

                        <div class="term-row mb-2">
                            <div class="term-kh">
                                ១. ការផ្ទេរកម្មសិទ្ធិយានយន្ត នឹងត្រូវអនុវត្ត
                                បន្ទាប់ពីអ្នកទិញបានបំពេញការទូទាត់ទឹកប្រាក់ទាំងស្រុង តាមតម្លៃដែលបានព្រមព្រៀង។
                            </div>
                            <div class="term-en text-muted">
                                1. Transfer of vehicle ownership will be completed only after the purchaser has fully
                                settled the agreed payment amount.
                            </div>
                        </div>

                        <div class="term-row mb-2">
                            <div class="term-kh">
                                ២. ប្រាក់ទូទាត់ទាំងអស់ រួមមាន ប្រាក់កក់ និងប្រាក់ទូទាត់ផ្សេងៗ មិនអាចដកវិញបានទេ
                                ប្រសិនបើបោះបង់ការទិញ ឬមិនបង់ប្រាក់នៅសល់ក្នុង ៥ ថ្ងៃ បន្ទាប់ពីជូនដំណឹងដឹកជញ្ជូន។
                            </div>
                            <div class="term-en text-muted">
                                2. All payments are non-refundable if the purchase is canceled or the remaining balance is
                                not paid within 5 days after delivery notice.
                            </div>
                        </div>

                        <div class="term-row mb-2">
                            <div class="term-kh">
                                ៣. ក្នុងករណីការទិញត្រូវបានធ្វើតាមរយៈឥណទាន ឬកម្ចីពីធនាគារ ប្រាក់ដំបូងមិនអាចដកវិញបាន
                                ប្រសិនបើមិនបំពេញលក្ខខណ្ឌធនាគារក្នុង ១៥ ថ្ងៃ។
                            </div>
                            <div class="term-en text-muted">
                                3. For purchases made through bank financing or loans, the initial payment is non-refundable
                                if the purchaser fails to meet the bank’s requirements or submit required documents within
                                15 days from the application date.
                            </div>
                        </div>

                        <div class="term-row mb-2">
                            <div class="term-kh">
                                ៤. ក្នុងករណីដែលពាក្យស្នើសុំឥណទានមិនត្រូវបានអនុម័ត អ្នកទិញអាចទទួលបានការសងប្រាក់វិញពេញលេញ
                                ដោយភ្ជាប់ឯកសារបញ្ជាក់ ឬលិខិតបដិសេធពីធនាគារ។
                            </div>
                            <div class="term-en text-muted">
                                4. If the loan application is not approved, the purchaser is eligible for a full refund,
                                subject to providing valid supporting documents or a bank rejection letter.
                            </div>
                        </div>
                    </div>

                    <table
                        style="width: 100%; text-align: center; margin-top: auto; border-collapse: collapse; page-break-inside: avoid;"
                        class="khmer-font">
                        <tr>
                            <td colspan="4" style="height: 100px;"></td>
                        </tr>
                        <tr>
                            <td style="width: 25%; padding: 5px; vertical-align: bottom;">
                                <div
                                    style="border-top: 1.5px solid #000; width: 85%; margin: 0 auto; padding-top: 5px; font-size: 8.5pt; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">
                                    CUSTOMER'S SIGNATURE
                                </div>
                            </td>
                            <td style="width: 25%; padding: 5px; vertical-align: bottom;">
                                <div
                                    style="border-top: 1.5px solid #000; width: 85%; margin: 0 auto; padding-top: 5px; font-size: 8.5pt; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">
                                    SALEMAN
                                </div>
                            </td>
                            <td style="width: 25%; padding: 5px; vertical-align: bottom;">
                                <div
                                    style="border-top: 1.5px solid #000; width: 85%; margin: 0 auto; padding-top: 5px; font-size: 8.5pt; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">
                                    SALES MANAGER
                                </div>
                            </td>
                            <td style="width: 25%; padding: 5px; vertical-align: bottom; position: relative;">
                                <div
                                    style="position: absolute; top: -80px; left: 0; right: 0; text-align: center; font-size: 5pt; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    <div style="font-weight: bold;" class="khmer-font">ជ.ក្នុងនាមជា/FOR AND ON BEHALF OF
                                    </div>
                                    <div>T F MOTORS (CAMBODIA) CO.,LTD</div>
                                </div>
                                <div
                                    style="border-top: 1.5px solid #000; width: 85%; margin: 0 auto; padding-top: 5px; font-size: 8.5pt; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">
                                    SALES DIRECTOR
                                </div>
                            </td>
                        </tr>
                    </table>

                </div>

                <div class="tab-pane fade print-page p-4" id="page2">
                    <div class="d-flex justify-content-between align-items-end border-bottom border-dark pb-1 mb-3">
                        <h6 class="khmer-font label-bold uppercase mb-0">អត្តសញ្ញាណប័ណ្ណ / Customer ID Card</h6>
                    </div>
                    
                    <div class="id-card-placeholder-pro">
                        @if ($contract->customer_id_card)
                            <img src="{{ asset('storage/' . $contract->customer_id_card) }}">
                        @else
                            <div class="text-center text-muted opacity-50">
                                <i class="fas fa-id-card fa-2x mb-2"></i>
                                <p class="small mb-0 font-bold" style="font-size: 8pt; letter-spacing: 1px;">ATTACH ID COPY HERE</p>
                            </div>
                        @endif
                    </div>
                
                    <div class="d-flex justify-content-between align-items-end border-bottom border-dark pb-1 mb-4 mt-2">
                        <h6 class="khmer-font label-bold uppercase mb-0">កាតសមាជិក / Membership Card</h6>
                    </div>
                
                    <div class="vip-card-wrapper">
                        <div class="vip-card">
                            <div class="vip-front-top">
                                <div class="vip-logo-section">
                                    <div class="vip-t-circle">T</div>
                                    <div class="vip-tf-text">TF MOTORS</div>
                                </div>
                            </div>
                            
                            <div class="vip-title-section">
                                <div class="vip-mem-title">MEMBERSHIP</div>
                                
                                <div class="vip-white-bar">
                                    @if(isset($saleReports) && $saleReports->memberCard)
                                        {{ $saleReports->memberCard }}
                                    @elseif(isset($contract) && $contract->membership_number)
                                        {{ $contract->membership_number }}
                                    @else
                                        11006465
                                    @endif
                                </div>
                            </div>
                    
                            <div class="vip-logos-bottom">
                                <img src="{{ asset('assets/images/logos/mg-gold.png') }}" alt="MG">
                                <img src="{{ asset('assets/images/logos/re-gold.png') }}" alt="Royal Enfield">
                                <img src="{{ asset('assets/images/logos/maxus-gold.png') }}" alt="Maxus">
                            </div>
                        </div>
                 
                
                        <div class="vip-card">
                            <div class="vip-back-top">
                                <div class="contacts">
                                    <span>MG Cambodia:</span> +855 92 666 281<br>
                                    <span>Maxus Cambodia:</span> +855 92 666 281<br>
                                    <span>Royal Enfield:</span> +855 95 666 381
                                </div>
                                <div class="website">
                                    www.taifu.com.hk
                                </div>
                            </div>
                
                            <div class="vip-white-strip">
                                <div class="vip-watermark"></div>
                                <div class="vip-customer-name khmer-font">
                                   MR. {{ strtoupper($contract->customer_name) }}
                                </div>
                            </div>
                
                            <div class="vip-back-terms khmer-font">
                                ប័ណ្ណនេះជាកម្មសិទ្ធិរបស់ក្រុមហ៊ុន ហើយចាំបាច់ត្រូវតែប្រគល់មកវិញ នៅពេលដែលលោកអ្នកបានប្រទះ<br>
                                ឃើញវានៅកន្លែងណាមួយ ឬក៏នៅពេលមានការស្នើសុំពីក្រុមហ៊ុន ។<br>
                                ប័ណ្ណនេះមិនអាចផ្ទេរទៅឲ្យនរណាម្នាក់ទៀតបានឡើយ ។<br>
                                ការប្រើប្រាស់ប័ណ្ណនេះ គឺអាស្រ័យទៅតាមលក្ខខណ្ឌនានារបស់ក្រុមហ៊ុន ដែលអនុវត្តក្នុងពេលបច្ចុប្បន្ននេះ ។
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade print-page p-5 pdi-content" id="page3">
                    <div class="pdi-header-box">
                        <div>
                            <h1 class="pdi-brand-title">TF MOTORS</h1>
                        </div>
                        <div>
                            <h3 class="pdi-doc-title">Vehicle Pre-Delivery Check List</h3>
                        </div>
                    </div>
                
                    <div class="pdi-info-grid khmer-font">
                        <div class="pdi-info-col">
                            <div class="pdi-info-row">
                                <span class="pdi-label">Customer:</span>
                                <span class="pdi-value">{{ strtoupper($contract->customer_name) }}</span>
                            </div>
                            <div class="pdi-info-row">
                                <span class="pdi-label">Phone:</span>
                                <span class="pdi-value font-family-mono">{{ $contract->phone_number }}</span>
                            </div>
                            <div class="pdi-info-row">
                                <span class="pdi-label">Address:</span>
                                <span class="pdi-value" style="white-space: normal; line-height: 1.4; border-bottom: none;">{{ $contract->address }}</span>
                            </div>
                        </div>
                
                        <div class="pdi-info-col">
                            <div class="pdi-info-row">
                                <span class="pdi-label">Contract No:</span>
                                <span class="pdi-value pdi-highlight">{{ $contract->contract_number }}</span>
                            </div>
                            <div class="pdi-info-row">
                                <span class="pdi-label">Sales Rep:</span>
                                <span class="pdi-value">{{ strtoupper($contract->sale_person) }}</span>
                            </div>
                            <div class="pdi-info-row">
                                <span class="pdi-label">VIN No:</span>
                                <span class="pdi-value font-family-mono">{{ strtoupper($contract->vin_number) }}</span>
                            </div>
                            
                            <div style="display: flex; gap: 15px; margin-top: 10px;">
                                <div class="pdi-info-row" style="flex: 1; margin-bottom: 0;">
                                    <span class="pdi-label" style="width: 40px;">Date:</span>
                                    <span class="pdi-value">{{ date('d-M-Y') }}</span>
                                </div>
                                <div class="pdi-info-row" style="flex: 1; margin-bottom: 0;">
                                    <span class="pdi-label" style="width: 45px;">Color:</span>
                                    <span class="pdi-value">{{ strtoupper($contract->color) }}</span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 15px; margin-top: 10px;">
                                <div class="pdi-info-row" style="flex: 1; margin-bottom: 0;">
                                    <span class="pdi-label" style="width: 55px;">Mileage:</span>
                                    <span class="pdi-value"></span> </div>
                                <div class="pdi-info-row" style="flex: 1; margin-bottom: 0;">
                                    <span class="pdi-label" style="width: 50px;">Source:</span>
                                    <span class="pdi-value">{{ strtoupper($contract->source_id) }}</span>
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

                    <div class="signature-wrapper">
                        <div class="sig-inline-block">
                            <span class="sig-inline-label">Sale Signature:</span>
                            <div class="sig-inline-line"></div>
                        </div>
                        
                        <div class="sig-inline-block">
                            <span class="sig-inline-label">Customer Signature:</span>
                            <div class="sig-inline-line"></div>
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
                        <div class="khmer-font mb-3" style="font-size: 8.5pt; text-align: justify;;">
                            <strong>និយមន័យ៖</strong> ការធានារបស់ក្រុមហ៊ុនមានន័យថា ធានាដោះស្រាយ និងជួសជុលរាល់បញ្ហាខូចខាត
                            ឬកំហុសបច្ចេកទេសដែលកើតចេញពីការផលិតពីរោងចក្រផ្ទាល់។<br>
                            <span class="italic text-muted"><strong>Definition:</strong> The company's warranty covers
                                repairs for any damage or technical errors originating directly from the factory.</span>
                        </div>

                        <div class="khmer-font mb-4 p-2"
                            style="font-size: 8pt; line-height: 1.6; border-left: 3px solid #ef4444; background: #fff5f5;">

                            <p class="mb-1 text-danger">
                                <strong>បញ្ជាក់៖</strong>
                                ក្រុមហ៊ុនមិនទទួលខុសត្រូវចំពោះការខូចខាតដែលបណ្តាលពីការប្រើប្រាស់មិនត្រឹមត្រូវ។
                                ការមិនគោរពតាមសេចក្តីណែនាំ អាចធ្វើឲ្យការធានាអស់សុពលភាព។
                            </p>

                            <p class="italic text-muted mb-0">
                                <strong>NOTED:</strong> The company is not responsible for damage caused by improper use.
                                Failure to follow guidelines may void the warranty.
                            </p>

                        </div>

                        <h6 class="label-bold text-uppercase mb-2" style="font-size: 8.5pt; color: var(--brand-indigo);">
                            ការធានាទូទៅ / NORMAL WARRANTY
                        </h6>
                        <ul class="khmer-font mb-4" style="font-size: 8.5pt; list-style-type: none; padding-left: 10px;">
                            
                            {{-- ជួរចំណងជើង --}}
                            <li class="mb-2 d-flex justify-content-between align-items-end">
                                <span class="mr-1">● ការផ្តល់ជូន / Offer for:</span> 
                                <span class="small italic font-bold text-muted">
                                    (*មួយណាដល់មុន / whichever comes first)
                                </span>
                            </li>
                            
                            {{-- ប្រើ PHP ដើម្បីទាញយក និងបំបែកទិន្នន័យ Offer --}}
                            @php
                                $offers = [];
                                if(isset($contract->offers) && $contract->offers) {
                                    $offers = is_array($contract->offers) ? $contract->offers : explode(',', $contract->offers);
                                    $offers = array_filter(array_map('trim', $offers));
                                }
                            @endphp
                        
                            {{-- ទាញទិន្នន័យមកបង្ហាញជាចំណុចៗ (ចុះបន្ទាត់) --}}
                            <div class="mb-3" style="padding-left: 25px;">
                                @if(count($offers) > 0)
                                    @foreach($offers as $offer)
                                        <div class="d-flex align-items-end mb-2">
                                            <span class="mr-2 text-success font-bold" style="font-size: 9pt;">✓</span>
                                            <span class="label-bold text-primary border-bottom border-dark pb-1" style="min-width: 250px;">
                                                {{ $offer }}
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- បង្ហាញបន្ទាត់ទទេ ប្រសិនបើគ្មានទិន្នន័យ --}}
                                    <div class="d-flex align-items-end mb-2">
                                        <span class="mr-2 text-muted font-bold" style="font-size: 9pt;">✓</span>
                                        <div class="border-bottom border-dark pb-1" style="min-width: 250px;"></div>
                                    </div>
                                @endif
                            </div>
                            
                            <li class="pl-3 mb-1">● គ្របដណ្តប់លើ៖ ម៉ាស៊ីន, ប៉ុង, ប្រព័ន្ធម៉ាស៊ីនត្រជាក់, ប្រព័ន្ធភ្លើង, និងប្រអប់លេខ។ល។</li>
                            <li class="pl-3 italic text-muted">Covers: Engine, Axle (Front/Rear), Air Conditioning System, Electrical System, Transmission, etc.</li>
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
                            ❖ លក្ខខណ្ឌនៃការមិនធានារបស់ក្រុមហ៊ុន / Warranty Exclusions
                        </h6>

                        <div class="khmer-font" style="line-height: 1.6; text-align: justify;">

                            <div class="mb-2">
                                <strong>1.</strong>
                                ការខូចខាតបណ្តាលមកពីការរក្សាទុកមិនត្រឹមត្រូវ ដូចជា ការខូចម៉ាស៊ីន ប្រព័ន្ធប្រេង អាគុយមិនសាក
                                ឬការខូចខាតថ្នាំពណ៌ដោយសារបរិស្ថាន។
                                <br>
                                Any failure caused by improper storage, such as engine or fuel system damage due to oil
                                stagnation,
                                discharged battery, or paint deterioration caused by environmental conditions.
                            </div>

                            <div class="mb-2">
                                <strong>2.</strong>
                                គ្រឿងបន្លាស់ដែលត្រូវផ្លាស់ប្តូរតាមការប្រើប្រាស់ ឬការសឹករហែក ឧ. សំបកកង់ ស្បែកហ្វាំង
                                ស្បែកក្ដាប់ អំពូល ផ្លិតទឹក ។ល។
                                <br>
                                Parts subject to wear and tear requiring replacement or maintenance, e.g. tires, brake pads,
                                clutch lining, bulbs, wiper blades, etc.
                            </div>

                            <div class="mb-2">
                                <strong>3.</strong>
                                ការខូចខាតដោយសារខ្វះការថែទាំ ឬមិនប្រើប្រាស់ប្រេងឥន្ធនៈ ប្រេងរំអិល ទឹកស្អំម៉ាស៊ីន
                                ឬហ្គាសតាមការណែនាំរោងចក្រ នឹងមិនស្ថិតក្នុងការធានា។
                                <br>
                                Damage caused by lack of maintenance or failure to use fuel, oil, lubricants, or coolant as
                                specified in the manufacturer’s manual is not covered under warranty.
                            </div>

                            <div class="mb-2">
                                <strong>4.</strong>
                                ការខូចខាតពីសារធាតុបរទេស ឬគ្រោះធម្មជាតិ ដូចជា ភ្លៀងអាស៊ីត ថ្ម ព្រិល រន្ទះ រញ្ជួយដី
                                និងទឹកជំនន់។
                                <br>
                                Damage caused by external substances or natural disasters such as chemical exposure, acid
                                rain, stones, hail, lightning, earthquake, or flooding.
                            </div>

                            <div class="mb-2">
                                <strong>5.</strong>
                                ការប្រើប្រាស់គ្រឿងបន្លាស់ដែលមិនបានអនុម័តដោយរោងចក្រ។
                                <br>
                                Damage resulting from the use of non-authorized factory parts.
                            </div>

                            <div class="mb-2">
                                <strong>6.</strong>
                                ការខូចខាតពីការប៉ះទង្គិច ភ្លើង ការផ្ទុះ ចោរកម្ម អាកាសធាតុមិនល្អ ឬគ្រោះថ្នាក់ចរាចរណ៍។
                                <br>
                                Damage caused by collision, fire, explosion, theft or attempted theft, adverse weather
                                conditions, or traffic accidents.
                            </div>

                            <div class="mb-2">
                                <strong>7.</strong>
                                ការប្រើប្រាស់មិនត្រឹមត្រូវ ឬការថែទាំមិនល្អ រួមទាំងការផ្ទុកលើសទម្ងន់ ឬការប្រណាំង។
                                <br>
                                Damage caused by improper use or poor maintenance, including overloading or racing.
                            </div>

                            <div class="mb-2">
                                <strong>8.</strong>
                                ការកែប្រែ ឬកែច្នៃរថយន្ត (តួ ម៉ាស៊ីន ប្រព័ន្ធអេឡិចត្រូនិច ឬប្រព័ន្ធផ្សេងៗ)។
                                <br>
                                Damage caused by modification or alteration of the vehicle, including body, chassis,
                                powertrain, or electronic systems.
                            </div>

                            <div class="mb-2">
                                <strong>9.</strong>
                                ការកែប្រែឬផ្តាច់ម៉ាស៊ីនវាស់ចម្ងាយ (Odometer) ដោយគ្មានការអនុញ្ញាតពីរោងចក្រ។
                                <br>
                                Any vehicle with a disconnected or altered odometer without authorization from the factory
                                or authorized dealer.
                            </div>

                            <div class="mb-2">
                                <strong>10.</strong>
                                រាល់ការចំណាយបន្ថែមដូចជា ការបាត់បង់ការប្រើប្រាស់យានយន្ត ប្រាក់ឈ្នួល ថ្លៃជួល ឬថ្លៃធ្វើដំណើរ។
                                <br>
                                Any indirect costs such as loss of vehicle use, wages, rental, accommodation, meals, or
                                travel expenses.
                            </div>

                            <div class="mb-2">
                                <strong>11.</strong>
                                ការកែប្រែយានយន្តក្រោយការលក់ ដើម្បីអោយស្របតាមច្បាប់មូលដ្ឋាន លើកលែងតែតម្រូវដោយច្បាប់ ឬរោងចក្រ។
                                <br>
                                Repairs or modifications required after sale to comply with local regulations, except those
                                required by law or factory specification.
                            </div>

                            <div class="mb-2">
                                <strong>12.</strong>
                                ការខូចខាតបណ្តាលមកពីការប្រើប្រាស់មិនត្រឹមត្រូវក្នុងពេលយានយន្តមានបញ្ហា។
                                <br>
                                Damage caused by improper use while the vehicle is experiencing mechanical issues.
                            </div>

                            <div class="mb-2">
                                <strong>13.</strong>
                                ការជួសជុល ឬថែទាំដែលអនុវត្តដោយយាន្តដ្ឋានគ្មានការអនុញ្ញាត។
                                <br>
                                Any repair or maintenance performed by unauthorized workshops or service providers.
                            </div>

                        </div>


                        <div class="mt-4 p-3 border border-dark bg-light text-center khmer-font avoid-page-break"
                            style="font-size: 8.5pt;">
                            <p class="font-bold mb-1">ចំណាំ៖ បន្ទាប់ពីទទួលបានការពន្យល់រួចមក ខ្ញុំយល់ច្បាស់អំពីលក្ខខណ្ឌធានា
                                និងគោលការណ៍ធានារបស់ក្រុមហ៊ុន។</p>
                            <p class="mb-3 italic">I acknowledge that I have received a full explanation regarding the
                                terms and policies of the warranty.</p>

                            <table style="width: 100%; text-align: center; margin-top: 20px; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 50%; padding: 5px; vertical-align: bottom;">
                                        <div
                                            style="border-top: 1px solid #000; width: 60%; margin: 0 auto; padding-top: 5px; font-size: 7.5pt; font-weight: 800; text-transform: uppercase;">
                                            ហត្ថលេខាអតិថិជន / Customer Signature
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding: 5px; vertical-align: bottom;">
                                        <div
                                            style="border-top: 1px solid #000; width: 60%; margin: 0 auto; padding-top: 5px; font-size: 7.5pt; font-weight: 800; text-transform: uppercase;">
                                            ហត្ថលេខាអនុម័ត / Authorized Signature
                                        </div>
                                    </td>
                                </tr>
                            </table>
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
                    letterRendering: false,
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


        function printA4() {
            // បង្ហាញផ្ទាំង Loading
            Swal.fire({
                title: 'កំពុងរៀបចំឯកសារ...',
                text: 'សូមរង់ចាំបន្តិច',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // ១. បង្កើត Iframe (ផ្ទាំងលាក់កំបាំង) ដើម្បីកុំឱ្យជាប់កូដ CSS របស់ Admin Dashboard
            const iframe = document.createElement('iframe');
            iframe.style.position = 'absolute';
            iframe.style.top = '-9999px';
            iframe.style.width = '210mm';
            document.body.appendChild(iframe);

            // ២. ទាញយកតែមាតិកាខាងក្នុងនៃ Tabs ទាំង ៦ (យកមកតម្រៀបជាជួរពីលើចុះក្រោម)
            let allPagesHtml = '';
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(pane => {
                // រុំវាជាមួយ Class ថ្មី ដើម្បីទម្លាយការលាក់របស់ Bootstrap Tabs
                allPagesHtml += `<div class="a4-page">${pane.innerHTML}</div>`;
            });

            // ៣. ទាញយក CSS ទាំងអស់ពីវេបសាយដើម ទៅកាន់ Iframe
            let styles = '';
            document.querySelectorAll('link[rel="stylesheet"], style').forEach(el => {
                styles += el.outerHTML;
            });

            // ៤. បញ្ចូលកូដទៅក្នុង Iframe និងរៀបចំទំហំ A4 ស្តង់ដារ
            const doc = iframe.contentWindow.document;
            doc.open();
            doc.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Authorized Sales Pack</title>
            ${styles}
            <style>
                /* លុប Header (ថ្ងៃខែ) និង Footer របស់ Browser */
                @page { 
                    size: A4 portrait; 
                    margin: 0mm !important; 
                }
                body { 
                    margin: 0 !important; 
                    padding: 0 !important; 
                    background: #fff !important; 
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                /* បង្កើតទំព័រ A4 នីមួយៗឱ្យពេញខ្នាត 210x297mm */
                .a4-page {
                    display: flex;
                    flex-direction: column;
                    width: 210mm;
                    height: 297mm;
                    padding: 12mm 15mm; /* គម្លាតពីគែមក្រដាស */
                    box-sizing: border-box;
                    page-break-after: always;
                    page-break-inside: avoid;
                    background: #fff;
                    margin: 0;
                }
            </style>
        </head>
        <body>
            ${allPagesHtml}
        </body>
        </html>
    `);
            doc.close();

            // ៥. រង់ចាំឱ្យកូដដំណើរការពេញលេញ សឹមបញ្ជា Print
            iframe.onload = function() {
                setTimeout(() => {
                    Swal.close(); // បិទផ្ទាំង Loading

                    // បញ្ជា Print (ត្រង់នេះអ្នកគ្រាន់តែជ្រើសរើស "Save as PDF" ជាការស្រេច)
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    // លុប Iframe ចោលវិញក្រោយពេល Print ចប់ ដើម្បីកុំឱ្យធ្ងន់វេបសាយ
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);

                }, 1000); // រង់ចាំ ១ វិនាទី ឱ្យរូបភាព និង CSS ដើរស្រួលបួល
            };
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
