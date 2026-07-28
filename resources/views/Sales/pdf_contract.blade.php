<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* ❌ បានលុប @page ចោល ❌ */
        body { 
            font-family: 'khmer', 'siemreap', sans-serif; 
            font-size: 9pt; 
            color: #000; 
            line-height: 1.4; 
            margin: 0;
            padding: 0;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td { vertical-align: top; padding: 5px; }
        
        .moul { font-family: 'moul', serif; font-size: 14pt; color: #1e293b; }
        .text-red { color: #ab171c; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; color: #64748b; }
        .border { border: 1px solid #000; }
        .bg-light { background-color: #f8fafc; }
        .badge { background: #10b981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 8pt; }
        .section-title { background-color: #eee; border: 1px solid #000; padding: 5px 10px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <table style="border-bottom: 2px solid #ab171c; margin-bottom: 20px;">
        <tr>
            <td width="20%"><div style="background:#ab171c; color:#fff; padding:6px; text-align:center; font-weight:bold;">OFFICIAL</div></td>
            <td width="60%" class="text-center">
                <div class="moul">កិច្ចសន្យាការលក់</div>
                <div class="text-red" style="font-size: 11pt;">SALES CONTRACT</div>
            </td>
            <td width="20%" class="text-right">
                <div style="font-size: 8pt; color: #64748b;">Status:</div>
                <span class="badge">VALIDATED</span>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="60%">
                <table style="width: 100%;">
                    <tr><td width="130">អតិថិជន / Name:</td><td style="border-bottom:1px dashed #ccc;"><strong>{{ $contract->customer_name_kh }} / {{ strtoupper($contract->customer_name) }}</strong></td></tr>
                    <tr><td>ទំនាក់ទំនង / Phone:</td><td style="border-bottom:1px dashed #ccc;"><strong>{{ $contract->phone_number }}</strong></td></tr>
                    <tr><td>អាសយដ្ឋាន / Address:</td><td style="border-bottom:1px dashed #ccc;">{{ $contract->address }}</td></tr>
                </table>
            </td>
            <td width="40%">
                <table style="width: 100%;" class="text-right">
                    <tr><td class="text-right">លេខឯកសារ / No:</td><td width="120" style="color:#4f46e5; font-weight:bold;">{{ $contract->contract_number }}</td></tr>
                    <tr><td class="text-right">ផ្នែកលក់ / Sales:</td><td><strong>{{ $contract->sale_person }}</strong></td></tr>
                    <tr><td class="text-right">កាលបរិច្ឆេទ / Date:</td><td>{{ \Carbon\Carbon::parse($contract->order_date)->format('d-M-Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="text-center" style="margin: 15px 0;">
        <p class="bold" style="margin-bottom: 2px;">អ្នកទិញយល់ព្រមទិញ ហើយក្រុមហ៊ុន ធី អេហ្វ ម៉ូធ័រ យល់ព្រមលក់ទំនិញដូចខាងក្រោម។</p>
        <span class="italic" style="font-size: 8pt;">The undersigned Purchaser agrees to buy and TF Motors Cambodia agrees to sell the undermentioned goods.</span>
    </div>

    <table class="border">
        <thead>
            <tr class="bg-light">
                <th class="border" width="40">N°</th>
                <th class="border">បរិយាយ / DESCRIPTION</th>
                <th class="border" width="70">ចំនួន</th>
                <th class="border" width="130">ទឹកប្រាក់ / AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-center" style="padding: 15px 5px;">01</td>
                <td class="border" style="padding: 10px;">
                    <div class="bold">{{ strtoupper($contract->model) }}</div>
                    <div style="font-size: 8.5pt; color: #444;">Exterior Color: {{ $contract->color }}</div>
                    <div style="margin-top: 5px; color: #4f46e5; font-size: 8pt;">Special Offers: {{ is_array($contract->offers) ? implode(', ', $contract->offers) : $contract->offers }}</div>
                </td>
                <td class="border text-center" style="padding: 15px 5px;">{{ $contract->qty }}</td>
                <td class="border text-center" style="padding: 15px 5px;">${{ number_format($contract->sub_total, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-right bold">ទឹកប្រាក់សរុប / SUB TOTAL:</td><td class="border text-center bold">${{ number_format($contract->sub_total, 2) }}</td></tr>
            <tr><td colspan="3" class="text-right bold">ប្រាក់កក់ / DEPOSIT:</td><td class="border text-center">-${{ number_format($contract->deposit, 2) }}</td></tr>
            <tr class="bg-light"><td colspan="3" class="text-right bold" style="font-size: 10pt;">សរុប / GRAND TOTAL:</td><td class="border text-center bold" style="font-size: 11pt; color: #ab171c;">${{ number_format($contract->grand_total, 2) }}</td></tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <div class="bold" style="border-bottom: 1px solid #000; text-transform: uppercase;">លក្ខខណ្ឌ / TERMS AND CONDITIONS</div>
        <div style="font-size: 8pt; margin-top: 5px;">
            <p>១. ការផ្ទេរកម្មសិទ្ធិត្រូវធ្វើឡើងនៅពេលទឹកប្រាក់ត្រូវបានទូទាត់គ្រប់ចំនួន។<br><span class="italic">1. Transferring of ownership shall be done when full payment is settled.</span></p>
            <p>២. ប្រាក់កក់ទុកមិនអាចដកវិញបានទេ ក្នុងករណីអ្នកទិញដកពីកិច្ចសន្យា។<br><span class="italic">2. Booking deposits are non-refundable in the event of purchaser's withdrawal.</span></p>
        </div>
    </div>

    <table style="margin-top: 40px; text-align: center;">
        <tr>
            <td width="33%"><div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 8pt;">CUSTOMER'S SIGNATURE</div></td>
            <td width="33%"><div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 8pt;">SALES PERSON</div></td>
            <td width="33%"><div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 8pt;">AUTHORIZED BY</div></td>
        </tr>
    </table>


    <pagebreak />


    <div class="section-title">អត្តសញ្ញាណប័ណ្ណ / CUSTOMER ID CARD</div>
    <div style="text-align: center; margin: 30px 0; border: 2px dashed #ccc; padding: 20px; border-radius: 15px;">
        @if($contract->customer_id_card)
            <img src="{{ public_path('storage/' . $contract->customer_id_card) }}" style="max-width: 550px; max-height: 350px;">
        @else
            <div style="color: #ccc; font-size: 20pt; padding: 50px;">ATTACH ID COPY HERE</div>
        @endif
    </div>

    <div class="section-title" style="margin-top: 40px;">កាតសមាជិក / MEMBERSHIP CARD</div>
    <table style="margin-top: 30px;">
        <tr>
            <td width="50%" align="center">
                <div style="background:#1a1a1a; color:#d4af37; width:360px; height:210px; border-radius:15px; padding:20px; border:1px solid #444;">
                    <div style="font-size:20pt; font-weight:bold; border:2px solid #d4af37; width:45px; border-radius:50%; margin:0 auto; line-height:45px; text-align:center;">T</div>
                    <div style="font-size:18pt; font-weight:900; letter-spacing:3px; margin-top:15px; text-align:center;">MEMBERSHIP</div>
                    <div style="background:#fff; color:#000; padding:8px; margin-top:20px; font-weight:bold; font-size:11pt; text-align:center;">{{ strtoupper($contract->customer_name) }}</div>
                </div>
            </td>
            <td width="50%" align="center">
                <div style="background:#111; color:#fff; width:360px; height:210px; border-radius:15px; border:1px solid #444; position: relative;">
                    <div style="background:#000; height:40px; margin-top:20px;"></div>
                    <div style="padding: 15px; font-size: 7.5pt; text-align: center; color: #d4af37;">
                        ប័ណ្ណនេះជាកម្មសិទ្ធិរបស់ក្រុមហ៊ុន។ ប្រសិនបើរើសបានវិញ សូមមេត្តាប្រគល់មកឱ្យក្រុមហ៊ុនវិញ។<br>
                        This card remains the property of TF Motors.
                    </div>
                    <div style="position:absolute; bottom: 15px; width: 100%; text-align: center; font-weight: bold; font-size: 10pt;">
                        ID: {{ $contract->id_card_number ?? '00000000' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>


    <pagebreak />


    <div class="section-title">VEHICLE PRE-DELIVERY CHECK LIST</div>
    <table class="border" style="margin-top: 10px;">
        <tr class="bg-light">
            <td class="border" width="50%"><strong>Customer Name:</strong> {{ $contract->customer_name }}</td>
            <td class="border" width="50%"><strong>Contract No:</strong> {{ $contract->contract_number }}</td>
        </tr>
        <tr>
            <td class="border"><strong>VIN Number:</strong> {{ $contract->vin_number }}</td>
            <td class="border"><strong>Model:</strong> {{ $contract->model }}</td>
        </tr>
    </table>

    <div class="bold" style="margin: 10px 0;">A. INSIDE THE VEHICLE</div>
    <table class="border">
        <tr class="bg-light"><td class="border">Inspection Items</td><td class="border" width="60">OK</td></tr>
        <tr><td class="border">1. Fuel Level (E, 1/4, 1/2, 3/4, F)</td><td class="border text-center">☐</td></tr>
        <tr><td class="border">2. Warning Lights (Check for Operation)</td><td class="border text-center">☐</td></tr>
        <tr><td class="border">3. Horn, Washer & Wiper functions</td><td class="border text-center">☐</td></tr>
    </table>

    <div class="bold" style="margin: 10px 0;">B. AROUND THE VEHICLE</div>
    <table class="border">
        <tr>
            <td class="border" width="30%">
                <div style="font-size: 8pt;">
                    1. B = Body Damage<br>
                    2. P = Paint Damage<br>
                    3. G = Glass Damage
                </div>
            </td>
            <td class="border" align="center">
                <div style="padding: 20px; color: #ccc;">[ VEHICLE DIAGRAM AREA ]</div>
            </td>
        </tr>
    </table>


    <pagebreak />


    <div class="text-center">
        <div class="moul">លិខិតធានាគុណភាព</div>
        <div class="bold" style="font-size: 12pt;">WARRANTY LETTER</div>
    </div>
    <div style="margin-top: 20px; text-align: justify;">
        <p class="bold">I. លក្ខខណ្ឌ និងគោលការណ៍ធានា / CONDITIONS AND WARRANTY POLICY</p>
        <p>ការធានានេះគ្របដណ្តប់លើការជួសជុលរាល់បញ្ហាខូចខាតបច្ចេកទេសដែលកើតចេញពីការផលិត។ សុពលភាពនៃការធានានឹងត្រូវបញ្ចប់ភ្លាមៗ ប្រសិនបើអតិថិជនមិនអនុវត្តតាមសៀវភៅណែនាំថែទាំ។</p>
        <table class="border" style="margin-top: 15px;">
            <tr class="bg-light">
                <td class="border bold" width="40%">ការធានាទូទៅ / NORMAL WARRANTY</td>
                <td class="border">3 Years or 100,000 KM (*Whichever comes first)</td>
            </tr>
        </table>
    </div>


    <pagebreak />


    <div class="section-title">II. ការធានា - សឹក និង រហែក / WARRANTY - WEAR AND TEAR</div>
    <table class="border" style="margin-top: 10px;">
        <thead>
            <tr class="bg-light">
                <th class="border">Item Name</th>
                <th class="border">Warranty Period</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="border">Air filter / Oil filter</td><td class="border">3 Months / 5,000 KM</td></tr>
            <tr><td class="border">Battery</td><td class="border">12 Months / 20,000 KM</td></tr>
            <tr><td class="border">Wiper blades / Light bulbs</td><td class="border">3 Months / 5,000 KM</td></tr>
            <tr><td class="border">Tires</td><td class="border">6 Months / 10,000 KM</td></tr>
        </tbody>
    </table>

    <div class="section-title" style="margin-top: 30px;">III. លក្ខខណ្ឌនៃការមិនធានា / WARRANTY EXCLUSIONS</div>
    <div style="font-size: 8.5pt; margin-top: 10px;">
        <p>1. ការខូចខាតដោយសារគ្រោះថ្នាក់ចរាចរណ៍ ឬការប្រើប្រាស់ខុសបច្ចេកទេស។</p>
        <p>2. ការកែប្រែគ្រឿងបន្លាស់ ឬប្រព័ន្ធភ្លើងដោយគ្មានការអនុញ្ញាតពីក្រុមហ៊ុន។</p>
        <p>3. ការខូចខាតដោយសារកត្តាធម្មជាតិ (ទឹកជំនន់, ភ្លើងឆេះ, រន្ទះបាញ់)។</p>
    </div>
    
    <div style="margin-top: 50px; padding: 15px; border: 1px solid #000; background: #f9f9f9; text-align: center;">
        <p class="bold">ខ្ញុំយល់ច្បាស់អំពីលក្ខខណ្ឌធានា និងគោលការណ៍របស់ក្រុមហ៊ុនខាងលើ។</p>
        <div class="italic">I fully understand the warranty terms and company policies stated above.</div>
        <table style="margin-top: 30px;">
            <tr>
                <td><div style="border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px;">CUSTOMER SIGNATURE</div></td>
                <td><div style="border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 5px;">AUTHORIZED SIGNATURE</div></td>
            </tr>
        </table>
    </div>

</body>
</html>