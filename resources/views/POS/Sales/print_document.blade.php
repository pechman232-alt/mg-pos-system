<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($docType == 'tax_invoice') Tax Sale Voucher 
        @elseif($docType == 'delivery_note') Delivery Note 
        @else Invoice @endif 
        - {{ $sale->invoice_no }}
    </title>
    
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        /* Control Bar */
        body { background-color: #525659; margin: 0; font-family: 'Battambang', Arial, sans-serif; display: flex; flex-direction: column; align-items: center; }
        .control-bar { background-color: #2b2c40; padding: 12px; display: flex; justify-content: center; gap: 15px; position: sticky; top: 0; z-index: 999; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%; }
        .btn { padding: 8px 20px; border-radius: 5px; text-decoration: none; font-family: Arial, sans-serif; font-weight: bold; font-size: 14px; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-print { background-color: #CE181E; color: white; }
        .btn-print:hover { background-color: #a81116; }
        .btn-back { background-color: #696cff; color: white; }
        .btn-back:hover { background-color: #5f61e6; }
        .preview-area { padding: 20px 0; }

        /* A4 Paper Strict 1 Page */
        .a4-paper {
            background: #ffffff; 
            width: 210mm; 
            height: 295mm; 
            max-height: 295mm;
            padding: 8mm 12mm; 
            box-sizing: border-box; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: #000; 
            font-size: 11.5px; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            margin: 0 auto;
        }

        /* Header */
        .dn-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 10px; }
        .dn-logo { width: 140px; text-align: left; }
        .dn-logo img { max-width: 130px; max-height: 65px; object-fit: contain; }
        .company-info-box { text-align: center; flex-grow: 1; }
        .company-kh { font-family: 'Moul', cursive; font-size: 15px; margin-bottom: 3px; color: #000; letter-spacing: 0.5px; }
        .company-en { font-weight: bold; font-size: 13.5px; margin-bottom: 3px; font-family: Arial; text-transform: uppercase; }
        .company-info { font-size: 10px; line-height: 1.5; color: #222; }
        .header-spacer { width: 140px; } 

        /* Title & Info */
        .doc-title { text-align: center; margin: 2px 0 8px 0; }
        .doc-title-kh { font-family: 'Moul', cursive; font-size: 15px; margin-bottom: 2px;}
        .doc-title-en { font-weight: bold; font-size: 13.5px; font-family: Arial; text-decoration: underline;}
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11px; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-label { width: 120px; font-weight: 600; }
        .info-colon { width: 12px; text-align: center; font-weight: bold; }
        .data-value { font-weight: bold; color: #000; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; border: 1.5px solid #000; }
        .items-table th { border: 1px solid #000; padding: 5px 4px; text-align: center; font-weight: bold; background-color: #f8f9fa !important; text-transform: uppercase; font-size: 10.5px; }
        .items-table td { border-left: 1px solid #000; border-right: 1px solid #000; padding: 4px 5px; vertical-align: middle; text-align: center; height: 20px; }

        /* បន្ទាត់ Solid Line เฉพาะត្រង់កន្លែងដែលមាន Item ពិតប្រាកដ */
        .items-table tbody tr.has-item { border-bottom: 1px solid #000 !important; }
        .items-table tbody tr.has-item td { border-bottom: 1px solid #000 !important; }
        .items-table tbody tr.empty-row td { border-bottom: none !important; }

        /* Total Section */
        .total-section { width: 100%; margin-top: 6px; display: flex; justify-content: flex-end; }
        .total-table { border-collapse: collapse; width: 38%; font-size: 11px; }
        .total-table td { padding: 4px 8px; border: 1.5px solid #000; font-weight: bold; }
        .total-label { text-align: right; background-color: #f8f9fa !important; }
        .total-amount { text-align: right; }

        /* Signatures */
        .signature-section { 
            display: flex; justify-content: space-between; 
            margin-top: auto; 
            padding-top: 10px; padding-bottom: 2px;
        }
        .signature-box { text-align: center; width: 22%; }
        .signature-title { font-weight: bold; font-size: 11px; margin-bottom: 35px; }
        
        .signature-line { border-top: 2px dotted #000; width: 85%; margin: 0 auto 5px auto; }
        .signature-date { font-size: 10px; color: #333; font-weight: 600; }

        /* Print Settings (Strict 1 Page A4) */
        @media print {
            @page { size: A4 portrait; margin: 0mm !important; }
            html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; height: 100% !important; overflow: hidden !important; }
            .control-bar { display: none !important; }
            .preview-area { padding: 0 !important; margin: 0 !important; }
            
            .a4-paper { 
                box-shadow: none !important; 
                width: 210mm !important; 
                height: 297mm !important; 
                max-height: 297mm !important;
                padding: 8mm 12mm !important; 
                margin: 0 !important; 
                page-break-after: avoid !important; 
                page-break-before: avoid !important;
                page-break-inside: avoid !important; 
                overflow: hidden !important;
            }
            .items-table th, .total-label { background-color: #f8f9fa !important; }
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
                $company = $sale->branch->company ?? null;
                $employee = \Illuminate\Support\Facades\DB::table('employees')->where('id', $sale->employee_id)->first();
            @endphp

            <div class="dn-header">
                <div class="dn-logo">
                    @if($company && $company->logo)
                        <img src="{{ asset($company->logo) }}" alt="Logo">
                    @else
                        <div style="font-weight:bold; font-size:20px; color:#CE181E; font-family: 'Times New Roman', serif;">
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
                @if($docType == 'tax_invoice')
                    <div class="doc-title-kh">ប័ណ្ណលក់សម្រាប់ប្រកាសពន្ធ</div>
                    <div class="doc-title-en">TAX SALE VOUCHER</div>
                @elseif($docType == 'delivery_note')
                    <div class="doc-title-kh">ប័ណ្ណប្រគល់ទំនិញ</div>
                    <div class="doc-title-en">DELIVERY NOTE</div>
                @else
                    <div class="doc-title-kh">វិក្កយបត្រ</div>
                    <div class="doc-title-en">INVOICE</div>
                @endif
            </div>

            <table class="info-table">
                <tr>
                    <td class="info-label">Sale Person</td>
                    <td class="info-colon">:</td>
                    <td width="40%"><span class="data-value">{{ $employee ? $employee->full_name : ($sale->customer_name ?? '-') }}</span></td>
                    <td width="18%" style="text-align: right; padding-right: 10px; font-weight:bold;">Invoice Nº</td>
                    <td class="info-colon">:</td>
                    <td width="20%"><span class="data-value" style="color: #CE181E;">{{ $sale->invoice_no }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Car Buyer Name</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value">{{ $sale->car_buyer_name ?? '-' }}</span></td>
                    <td style="text-align: right; padding-right: 10px;">Date</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value">{{ date('d-M-Y', strtotime($sale->sale_date)) }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Warehouse</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value">{{ $sale->branch->branch_name ?? '-' }}</span></td>
                    <td style="text-align: right; padding-right: 10px;">Car Invoice</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value">{{ $sale->car_invoice_no ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td class="info-label">Reason (ស្ទួន)</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value text-danger">{{ $sale->reason ?? '-' }}</span></td>
                    <td style="text-align: right; padding-right: 10px;">Remark</td>
                    <td class="info-colon">:</td>
                    <td><span class="data-value text-info">{{ $sale->remark ?? '-' }}</span></td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">Nº</th>
                        <th width="15%">Product No</th>
                        <th width="{{ $docType == 'tax_invoice' ? '30%' : '35%' }}">Description</th>
                        <th width="8%">Qty</th>
                        
                        @if($docType == 'tax_invoice')
                            <th width="12%">Unit Price</th>
                            <th width="15%">Amount</th>
                        @else
                            <th width="15%">Purchase Invoice</th>
                            <th width="22%">Remark</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->details as $index => $item)
                    <tr class="has-item">
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: bold;">{{ $item->item_code }}</td>
                        <td style="text-align: left;">{{ $item->item_name }}</td>
                        <td style="font-weight: bold;">{{ number_format($item->qty) }}</td>
                        
                        @if($docType == 'tax_invoice')
                            <td style="text-align: right;">${{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align: right; font-weight: bold;">${{ number_format($item->total_price, 2) }}</td>
                        @else
                            <td>{{ $item->purchase_invoice_no ?: '-' }}</td> 
                            
                            <!-- UPDATED REMARK COLUMN -->
                            <td style="font-size: 10px;">{{ $item->remark ?: ($sale->reason ?: ($sale->remark ?: '-')) }}</td> 
                        @endif
                    </tr>
                    @endforeach

                    @php
                        $emptyRows = $docType == 'tax_invoice' ? 4 : 7;
                        $remainingRows = max(0, $emptyRows - count($sale->details));
                    @endphp

                    @for($i = 0; $i < $remainingRows; $i++)
                    <tr class="empty-row">
                        <td style="color: transparent;">-</td>
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                    <tr style="border-top: 1.5px solid #000;"><td colspan="6" style="height: 1px; padding: 0;"></td></tr>
                </tbody>
            </table>

            @if($docType == 'tax_invoice')
            <div class="total-section">
                <table class="total-table">
                    <tr>
                        <td class="total-label">សរុប / Sub Total:</td>
                        <td class="total-amount">${{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->tax_percent > 0)
                    <tr>
                        <td class="total-label">អាករ / VAT ({{ $sale->tax_percent }}%):</td>
                        <td class="total-amount">${{ number_format($sale->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="total-label" style="font-size: 12px;">សរុបរួម / Grand Total:</td>
                        <td class="total-amount" style="font-size: 13px; color: #CE181E;">${{ number_format($sale->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
            @endif

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">អ្នករៀបចំ<br>Prepared By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកត្រួតពិនិត្យ<br>Checked By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកដឹកជញ្ជូន<br>Delivered By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">អ្នកទទួល<br>Received By</div>
                    <div class="signature-line"></div>
                    <div class="signature-date">Date: ...../...../.....</div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>