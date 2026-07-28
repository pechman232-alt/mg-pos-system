<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goods Receipt Note - {{ $receiveHeader->document_no }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        /* -------------------------------------
           ១. ទម្រង់អេក្រង់ & ប៊ូតុងបញ្ជា
           ------------------------------------- */
        body {
            background-color: #525659; margin: 0;
            font-family: 'Battambang', Arial, sans-serif;
            display: flex; flex-direction: column; align-items: center;
        }
        .control-bar {
            background-color: #2b2c40; padding: 15px; display: flex;
            justify-content: center; gap: 15px; position: sticky; top: 0;
            z-index: 999; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%;
        }
        .btn {
            padding: 10px 25px; border-radius: 5px; text-decoration: none;
            font-family: Arial, sans-serif; font-weight: bold; font-size: 14px;
            cursor: pointer; border: none; display: flex; align-items: center;
            gap: 8px; transition: 0.3s;
        }
        .btn-print { background-color: #CE181E; color: white; }
        .btn-print:hover { background-color: #a81116; }
        .btn-back { background-color: #696cff; color: white; }
        .btn-back:hover { background-color: #5f61e6; }
        
        .preview-area { padding: 40px 0; }

        /* -------------------------------------
           ២. ខ្នាតក្រដាស A4 ស្តង់ដារ (អេក្រង់)
           ------------------------------------- */
        .a4-paper {
            background: #ffffff; width: 210mm; height: 296.5mm; max-height: 296.5mm;
            padding: 12mm 15mm; box-sizing: border-box; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: #000; font-size: 12px; display: flex; flex-direction: column; 
            overflow: hidden; margin: 0 auto;
        }

        /* -------------------------------------
           ៣. Header (Logo និង ព័ត៌មានក្រុមហ៊ុន)
           ------------------------------------- */
        .dn-header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 15px;
        }
        .dn-logo { width: 150px; text-align: left; }
        .dn-logo img { max-width: 140px; max-height: 80px; object-fit: contain; }
        .company-info-box { text-align: center; flex-grow: 1; }
        .company-kh { font-family: 'Moul', cursive; font-size: 18px; margin-bottom: 6px; color: #000; letter-spacing: 0.5px; }
        .company-en { font-weight: bold; font-size: 16px; margin-bottom: 6px; font-family: Arial; text-transform: uppercase; }
        .company-info { font-size: 11px; line-height: 1.8; color: #222; }
        .header-spacer { width: 150px; } 

        .doc-title { text-align: center; margin: 5px 0 20px 0; }
        .doc-title-kh { font-family: 'Moul', cursive; font-size: 18px; margin-bottom: 5px;}
        .doc-title-en { font-weight: bold; font-size: 16px; font-family: Arial; text-decoration: underline;}

        /* -------------------------------------
           ៤. ប្រអប់ព័ត៌មាន (Box Info ថ្មី)
           ------------------------------------- */
        .info-container {
            display: flex; justify-content: space-between; gap: 15px; margin-bottom: 15px;
        }
        .info-box {
            flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 15px;
            background-color: #fff;
        }
        .info-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .info-label { width: 130px; font-weight: 600; color: #334155; }
        .info-value { font-weight: bold; color: #0f172a; }
        .text-red { color: #dc2626 !important; }
        .text-blue { color: #2563eb !important; }

        /* -------------------------------------
           ៥. តារាងទំនិញ
           ------------------------------------- */
        .items-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; }
        .items-table th { 
            border: 1px solid #000; padding: 10px 5px; text-align: center; font-weight: bold; 
            background-color: #f1f5f9 !important; text-transform: uppercase; font-size: 11px; 
        }
        .items-table td { 
            border-left: 1px solid #000; border-right: 1px solid #000; 
            padding: 8px 6px; vertical-align: middle; text-align: center; height: 28px; 
        }

        /* -------------------------------------
           ៦. សរុបប្រាក់
           ------------------------------------- */
        .total-section { width: 100%; margin-top: 10px; display: flex; justify-content: flex-end; }
        .total-table { border-collapse: collapse; width: 40%; }
        .total-table td { padding: 6px 12px; border: 1.5px solid #000; font-weight: bold; }
        .total-label { text-align: right; background-color: #f1f5f9 !important; }
        .total-amount { text-align: right; }

        /* -------------------------------------
           ៧. ហត្ថលេខា
           ------------------------------------- */
        .signature-section { 
            display: flex; justify-content: space-between; margin-top: auto; 
            padding-top: 25px; padding-bottom: 10px;
        }
        .signature-box { text-align: center; width: 22%; }
        .signature-title { font-weight: bold; font-size: 12px; margin-bottom: 70px; }
        .signature-line { border-top: 2px dotted #000; width: 90%; margin: 0 auto 5px auto; }
        .signature-date { font-size: 11px; color: #555; }

        /* -------------------------------------
           ៨. Print Settings
           ------------------------------------- */
        @media print {
            @page { size: A4 portrait; margin: 0mm !important; }
            html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
            .control-bar { display: none !important; }
            .preview-area { padding: 0 !important; margin: 0 !important; }
            .a4-paper { 
                box-shadow: none !important; width: 210mm !important; 
                height: 296.5mm !important; max-height: 296.5mm !important;
                padding: 10mm 15mm !important; margin: 0 !important; 
                page-break-after: avoid !important; page-break-before: avoid !important;
                page-break-inside: avoid !important;
            }
            .info-box { border: 1px solid #94a3b8 !important; } /* បង្ខំឱ្យចេញស៊ុមពេល Print */
            .items-table th, .total-label { background-color: #f1f5f9 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body>
    
    <div class="control-bar no-print">
        <button onclick="window.print()" class="btn btn-print"><i class='bx bx-printer'></i> បោះពុម្ព (Print)</button>
        <button onclick="window.close()" class="btn btn-back"><i class='bx bx-arrow-back'></i> ត្រឡប់ក្រោយ (Back)</button>
    </div>

    <div class="preview-area">
        <div class="a4-paper">
            
            @php
                $company = $receiveHeader->branch->company ?? null;
            @endphp

            <div class="dn-header">
                <div class="dn-logo">
                    @if($company && $company->logo)
                        <img src="{{ asset($company->logo) }}" alt="Logo">
                    @else
                        <div style="font-weight:bold; font-size:24px; color:#198754; font-family: 'Times New Roman', serif;">
                            {{ substr($company->company_name_en ?? 'LOGO', 0, 2) }}
                        </div>
                    @endif
                </div>
                <div class="company-info-box">
                    <div class="company-kh">{{ $company->company_name_kh ?? 'ឈ្មោះក្រុមហ៊ុន' }}</div>
                    <div class="company-en">{{ $company->company_name_en ?? 'COMPANY NAME' }}</div>
                    <div class="company-info">
                        @if($company && $company->vat_tin) លេខអត្តសញ្ញាណកម្មអាករ (VAT TIN): {{ $company->vat_tin }}<br> @endif
                        @if($company && $company->address) អាសយដ្ឋានៈ {{ $company->address }}<br> @endif
                        @if($company && $company->phone) ទូរស័ព្ទលេខ {{ $company->phone }} @endif
                    </div>
                </div>
                <div class="header-spacer"></div>
            </div>

            <div class="doc-title">
                <div class="doc-title-kh">ប័ណ្ណទទួលទំនិញចូលស្តុក</div>
                <div class="doc-title-en">GOODS RECEIPT NOTE</div>
            </div>

            <div class="info-container">
                <div class="info-box">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">អ្នកផ្គត់ផ្គង់ <br><small class="text-muted fw-normal">( Vendor ):</small></td>
                            <td class="info-value">{{ $receiveHeader->vendor->vendor_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">អ្នកដឹកជញ្ជូន <br><small class="text-muted fw-normal">( Tran ):</small></td>
                            <td class="info-value text-blue">{{ $receiveHeader->tran_vendor ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">ទទួលចូលសាខា <br><small class="text-muted fw-normal">( Branch ):</small></td>
                            <td class="info-value">{{ $receiveHeader->branch->branch_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="info-box">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">លេខឯកសារ <br><small class="text-muted fw-normal">( No ):</small></td>
                            <td class="info-value text-red">{{ $receiveHeader->document_no }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">កាលបរិច្ឆេទ <br><small class="text-muted fw-normal">( Date ):</small></td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($receiveHeader->date)->format('d-M-Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">យោង <br><small class="text-muted fw-normal">( Ref Inv ):</small></td>
                            <td class="info-value">{{ $receiveHeader->purchase_invoice_no ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">Nº</th>
                        <th width="15%">Item Code</th>
                        <th width="40%">Description</th>
                        <th width="10%">Qty</th>
                        <th width="15%">Unit Cost</th>
                        <th width="15%">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalAmount = 0; 
                    @endphp
                    
                    @foreach($receiveHeader->details as $index => $item)
                    @php 
                        $itemTotal = $item->qty * ($item->unit_cost ?? $item->cost ?? 0);
                        $totalAmount += $itemTotal;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: bold;">{{ $item->product->item_code ?? $item->item_code ?? '' }}</td>
                        <td style="text-align: left;">{{ $item->product->item_name ?? $item->item_name ?? '' }}</td>
                        <td style="font-weight: bold; color: #198754;">{{ number_format($item->qty) }}</td>
                        <td style="text-align: right;">${{ number_format($item->unit_cost ?? $item->cost ?? 0, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;">${{ number_format($itemTotal, 2) }}</td>
                    </tr>
                    @endforeach

                    @for($i = 0; $i < (7 - count($receiveHeader->details)); $i++)
                    <tr>
                        <td style="color: transparent;">-</td>
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                    <tr style="border-top: 1.5px solid #000;"><td colspan="6" style="height: 1px; padding: 0;"></td></tr>
                </tbody>
            </table>

            <div class="total-section">
                <table class="total-table">
                    <tr>
                        <td class="total-label" style="font-size: 13px;">សរុបរួម / Grand Total:</td>
                        <td class="total-amount" style="font-size: 15px; color: #198754;">${{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </table>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">អ្នករៀបចំ<br>Prepared By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកផ្គត់ផ្គង់<br>Delivered By (Vendor)</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកត្រួតពិនិត្យ<br>Checked By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកទទួលចូលស្តុក<br>Received By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>