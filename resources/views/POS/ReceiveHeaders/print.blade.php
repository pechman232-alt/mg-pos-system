<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goods Receipt Note - {{ $receiveHeader->document_no }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Moul&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <style>
        /* -------------------------------------
           ១. ទម្រង់អេក្រង់ & ប៊ូតុងបញ្ជា
           ------------------------------------- */
        :root {
            --brand-dark: #1e293b;
            --brand-red: #CE181E;
            --brand-blue: #3b82f6;
            --text-main: #334155;
            --text-muted: #94a3b8;
            --bg-soft: #f8fafc;
            --border-color: #e2e8f0;
            --success: #16a34a;
        }

        body {
            background-color: #525659; 
            margin: 0;
            font-family: 'Plus Jakarta Sans', 'Battambang', Arial, sans-serif;
            display: flex; flex-direction: column; align-items: center;
        }
        
        .control-bar {
            background-color: #2b2c40; padding: 15px; display: flex;
            justify-content: center; gap: 15px; position: sticky; top: 0;
            z-index: 999; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%;
        }
        
        .btn {
            padding: 10px 25px; border-radius: 6px; text-decoration: none;
            font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 14px;
            cursor: pointer; border: none; display: flex; align-items: center;
            gap: 8px; transition: 0.3s;
        }
        .btn-print { background-color: var(--brand-red); color: white; }
        .btn-print:hover { background-color: #a81116; }
        .btn-back { background-color: #696cff; color: white; }
        .btn-back:hover { background-color: #5f61e6; }
        
        .preview-area { padding: 40px 0; }

        /* -------------------------------------
           ២. ខ្នាតក្រដាស A4 ស្តង់ដារ
           ------------------------------------- */
        .a4-paper {
            background: #ffffff; width: 210mm; height: 296.5mm; 
            padding: 12mm 15mm; box-sizing: border-box; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: var(--brand-dark); font-size: 12px; display: flex; flex-direction: column; 
            margin: 0 auto; position: relative; overflow: hidden;
        }

        /* -------------------------------------
           ៣. Header (Logo និង ព័ត៌មានក្រុមហ៊ុន)
           ------------------------------------- */
        .dn-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 15px; position: relative;
        }
        .dn-logo { width: 140px; text-align: left; }
        .dn-logo img { max-width: 100%; max-height: 70px; object-fit: contain; }
        
        .company-info-box { text-align: center; flex-grow: 1; margin-top: 5px; }
        .company-kh { font-family: 'Moul', cursive; font-size: 17px; margin-bottom: 5px; color: var(--brand-dark); }
        .company-en { font-weight: 800; font-size: 15px; margin-bottom: 6px; letter-spacing: 0.5px; }
        .company-info { font-size: 10.5px; line-height: 1.6; color: var(--text-main); font-family: 'Battambang'; }
        
        .header-spacer { width: 140px; display: flex; justify-content: flex-end; align-items: flex-start; } 
        .qr-placeholder { 
            width: 50px; height: 50px; border: 1px dashed #cbd5e1; border-radius: 6px;
            display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--text-muted);
        }

        /* -------------------------------------
           Accent Line (បន្ទាត់ពណ៌ខាងក្រោម Header)
           ------------------------------------- */
        .accent-divider {
            height: 3px; width: 100%; margin-bottom: 25px;
            background: linear-gradient(to right, var(--brand-red) 25%, var(--border-color) 25%);
        }

        /* -------------------------------------
           ចំណងជើងឯកសារ
           ------------------------------------- */
        .doc-title { text-align: center; margin-bottom: 25px; color: var(--brand-dark); }
        .doc-title-kh { font-family: 'Moul', cursive; font-size: 17px; margin-bottom: 8px; }
        .doc-title-en { font-weight: 800; font-size: 15px; text-transform: uppercase; border-bottom: 2px solid var(--brand-dark); display: inline-block; padding-bottom: 3px; letter-spacing: 0.5px;}

        /* -------------------------------------
           ៤. ប្រអប់ព័ត៌មាន (Info Boxes)
           ------------------------------------- */
        .info-container {
            display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px;
        }
        .info-box {
            flex: 1; border: 1px solid var(--border-color); border-radius: 8px; padding: 15px 20px;
            background-color: var(--bg-soft); position: relative; overflow: hidden;
        }
        .info-box.box-left { border-top: 3px solid #6366f1; } /* Blue Accent */
        .info-box.box-right { border-top: 3px solid var(--brand-red); } /* Red Accent */

        .info-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .info-table td { padding: 6px 0; vertical-align: middle; }
        
        .info-label-group { display: flex; flex-direction: column; width: 110px; }
        .lbl-kh { font-weight: 700; color: var(--brand-dark); font-size: 11px; }
        .lbl-en { font-size: 10px; color: var(--text-muted); font-weight: 500; }
        
        .info-value { font-weight: 700; color: var(--brand-dark); font-size: 12px; }
        .text-red { color: var(--brand-red) !important; font-weight: 800; }
        .text-blue { color: var(--brand-blue) !important; font-weight: 800; }

        /* -------------------------------------
           ៥. តារាងទំនិញ
           ------------------------------------- */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .items-table th { 
            background-color: #f1f5f9 !important; color: #64748b; font-size: 10px; 
            font-weight: 800; text-transform: uppercase; padding: 12px 10px; border: none;
            letter-spacing: 0.5px;
        }
        /* ធ្វើឱ្យកែងតារាងមានរាងមូលបន្តិច */
        .items-table th:first-child { border-top-left-radius: 6px; border-bottom-left-radius: 6px; text-align: center; }
        .items-table th:last-child { border-top-right-radius: 6px; border-bottom-right-radius: 6px; text-align: right; }
        
        .items-table td { 
            padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 12px; 
            color: var(--text-main); vertical-align: middle;
        }
        .items-table td:first-child { text-align: center; color: var(--text-muted); }
        .items-table td:last-child { text-align: right; }

        .item-code { font-weight: 800; color: var(--brand-dark); }
        .item-qty { font-weight: 800; color: var(--success); }
        .item-total { font-weight: 800; color: var(--brand-dark); }

        /* -------------------------------------
           ៦. សរុបប្រាក់ (Grand Total)
           ------------------------------------- */
        .total-section { display: flex; justify-content: flex-end; width: 100%; margin-bottom: 30px; }
        .total-box { 
            display: flex; border: 1.5px solid var(--brand-dark); border-radius: 6px; 
            background: #fff; overflow: hidden; 
        }
        .total-label { 
            padding: 10px 15px; font-weight: 800; color: var(--brand-dark); 
            border-right: 1.5px solid var(--brand-dark); font-size: 12px;
            display: flex; align-items: center;
        }
        .total-amount { 
            padding: 10px 20px; font-weight: 800; color: var(--success); font-size: 15px; 
            min-width: 100px; text-align: right;
        }

        /* -------------------------------------
           ៧. ហត្ថលេខា (Signatures)
           ------------------------------------- */
        .signature-section { 
            display: flex; justify-content: space-between; margin-top: auto; 
            padding-bottom: 15px; width: 100%;
        }
        .sig-block { width: 22%; text-align: center; }
        .sig-line { border-top: 1px dashed var(--text-muted); padding-top: 10px; margin-bottom: 5px; }
        .sig-kh { font-family: 'Battambang'; font-weight: 700; font-size: 11px; color: var(--brand-dark); line-height: 1.4; }
        .sig-en { font-weight: 700; font-size: 11px; color: var(--brand-dark); }
        .sig-date { font-size: 10px; color: var(--text-muted); margin-top: 5px; }

        /* -------------------------------------
           ៨. Footer Note
           ------------------------------------- */
        .page-footer {
            display: flex; justify-content: space-between; font-size: 9px; 
            color: #cbd5e1; border-top: 1px solid #f1f5f9; padding-top: 10px;
        }

        /* -------------------------------------
           ៩. Print Settings
           ------------------------------------- */
        @media print {
            @page { size: A4 portrait; margin: 0 !important; }
            html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
            .control-bar { display: none !important; }
            .preview-area { padding: 0 !important; margin: 0 !important; }
            .a4-paper { 
                box-shadow: none !important; width: 210mm !important; 
                height: 297mm !important; padding: 12mm 15mm !important; 
                page-break-after: avoid; page-break-inside: avoid;
            }
            .info-box { border: 1px solid var(--border-color) !important; }
            .items-table th { background-color: #f1f5f9 !important; }
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

            <!-- 1. Header -->
            <div class="dn-header">
                <div class="dn-logo">
                    @if($company && $company->logo)
                        <img src="{{ asset($company->logo) }}" alt="Logo">
                    @else
                        <!-- Logo Placeholder ដដែលបើសិនគ្មាន Logo -->
                        <div style="width:50px; height:50px; border:2px solid #000; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:20px;">/</div>
                        <div style="font-weight:800; font-size:10px; margin-top:5px;">LEAPMOTOR</div>
                    @endif
                </div>
                
                <div class="company-info-box">
                    <div class="company-kh">{{ $company->company_name_kh ?? 'ក្រុមហ៊ុន វី ម៉ូទ័រ ឯ.ក' }}</div>
                    <div class="company-en">{{ $company->company_name_en ?? 'VIVE MOTORS CO., LTD' }}</div>
                    <div class="company-info">
                        លេខអត្តសញ្ញាណកម្មអាករ (VAT TIN): {{ $company->vat_tin ?? 'K007-902103674' }}<br>
                        អាសយដ្ឋានៈ {{ $company->address ?? 'អគារលេខ ១៦៤, ១៦៥, ១៦៦ និង ១៦៧ ផ្លូវ ឧកញ៉ា ម៉ុង ឫទ្ធី ភូមិ បាយ៉ាប សង្កាត់ភ្នំពេញថ្មី ខណ្ឌសែនសុខ រាជធានីភ្នំពេញ' }} <br>
                        ទូរស័ព្ទលេខ: {{ $company->phone ?? '010 882 316' }}
                    </div>
                </div>
                
                <div class="header-spacer">
                    <div class="qr-placeholder">QR</div>
                </div>
            </div>

            <!-- Accent Divider -->
            <div class="accent-divider"></div>

            <!-- 2. Document Title -->
            <div class="doc-title">
                <div class="doc-title-kh">ប័ណ្ណទទួលទំនិញចូលស្តុក</div>
                <div class="doc-title-en">GOODS RECEIPT NOTE</div>
            </div>

            <!-- 3. Info Boxes -->
            <div class="info-container">
                <!-- Left Box (Blue) -->
                <div class="info-box box-left">
                    <table class="info-table">
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">អ្នកផ្គត់ផ្គង់</span>
                                    <span class="lbl-en">Vendor</span>
                                </div>
                            </td>
                            <td class="info-value">{{ $receiveHeader->vendor->vendor_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">អ្នកដឹកជញ្ជូន</span>
                                    <span class="lbl-en">Tran</span>
                                </div>
                            </td>
                            <td class="info-value text-blue">{{ $receiveHeader->tran_vendor ?? 'Logistic' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">ទទួលចូលសាខា</span>
                                    <span class="lbl-en">Branch</span>
                                </div>
                            </td>
                            <td class="info-value">{{ $receiveHeader->branch->branch_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Right Box (Red) -->
                <div class="info-box box-right">
                    <table class="info-table">
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">លេខឯកសារ</span>
                                    <span class="lbl-en">No</span>
                                </div>
                            </td>
                            <td class="info-value text-red">{{ $receiveHeader->document_no }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">កាលបរិច្ឆេទ</span>
                                    <span class="lbl-en">Date</span>
                                </div>
                            </td>
                            <td class="info-value">{{ \Carbon\Carbon::parse($receiveHeader->date)->format('d-M-Y H:i A') }}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="info-label-group">
                                    <span class="lbl-kh">លេខយោង</span>
                                    <span class="lbl-en">Ref Inv</span>
                                </div>
                            </td>
                            <td class="info-value">{{ $receiveHeader->purchase_invoice_no ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 4. Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">Nº</th>
                        <th width="15%" style="text-align: left;">Item Code</th>
                        <th width="40%" style="text-align: left;">Description</th>
                        <th width="10%" style="text-align: center;">Qty</th>
                        <th width="15%" style="text-align: center;">Unit Cost</th>
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
                        <td class="item-code">{{ $item->product->item_code ?? $item->item_code ?? '' }}</td>
                        <td>{{ $item->product->item_name ?? $item->item_name ?? '' }}</td>
                        <td style="text-align: center;" class="item-qty">{{ number_format($item->qty) }}</td>
                        <td style="text-align: center;">${{ number_format($item->unit_cost ?? $item->cost ?? 0, 2) }}</td>
                        <td class="item-total">${{ number_format($itemTotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- 5. Grand Total -->
            <div class="total-section">
                <div class="total-box">
                    <div class="total-label">សរុបរួម / Grand Total</div>
                    <div class="total-amount">${{ number_format($totalAmount, 2) }}</div>
                </div>
            </div>

            <!-- 6. Signatures (Pushed to bottom automatically by flex) -->
            <div class="signature-section">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-kh">អ្នករៀបចំ</div>
                    <div class="sig-en">Prepared by</div>
                    <div class="sig-date">Date:......./......./.......</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-kh">អ្នកផ្គត់ផ្គង់</div>
                    <div class="sig-en">Delivered by (Vendor)</div>
                    <div class="sig-date">Date:......./......./.......</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-kh">អ្នកត្រួតពិនិត្យ</div>
                    <div class="sig-en">Checked by</div>
                    <div class="sig-date">Date:......./......./.......</div>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <div class="sig-kh">អ្នកទទួលចូលស្តុក</div>
                    <div class="sig-en">Received by</div>
                    <div class="sig-date">Date:......./......./.......</div>
                </div>
            </div>

            <!-- 7. Footer Page Info -->
            <div class="page-footer">
                <div>Generated by system on {{ \Carbon\Carbon::now()->format('d-M-Y H:i') }}</div>
                <div>Page 1 of 1</div>
            </div>

        </div>
    </div>
</body>
</html>