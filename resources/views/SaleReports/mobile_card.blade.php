<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MG VIP Digital Card | {{ $contract->customer_name ?? 'Customer' }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --mg-red: #CE181E;
            --bg-body: #f1f5f9;
            --text-dark: #0f172a;
            --text-slate: #475569;
            --card-radius: 28px;
        }

        * { box-sizing: border-box; -webkit-font-smoothing: antialiased; }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif; 
            margin: 0; 
            padding: 20px; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            min-height: 100vh;
        }

        .digital-card { 
            background: #ffffff; 
            width: 100%; 
            max-width: 400px; 
            border-radius: var(--card-radius); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); 
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.8);
        }

        /* Top Brand Header */
        .brand-header {
            padding: 30px 20px 20px;
            text-align: center;
        }

        .mg-logo-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 4px;
            color: var(--text-dark);
            margin: 0;
        }

        .member-badge {
            display: inline-block;
            background: #fef2f2;
            color: var(--mg-red);
            font-size: 10px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 2px;
            margin-top: 8px;
            border: 1px solid #fee2e2;
        }

        /* Profile Section */
        .profile-section {
            text-align: center;
            padding: 10px 20px 25px;
        }

        .profile-section h3 {
            margin: 0;
            font-size: 22px;
            color: var(--text-dark);
            font-weight: 700;
        }

        .profile-section p {
            margin: 6px 0 0;
            color: var(--text-slate);
            font-size: 14px;
            font-weight: 500;
        }

        /* Content Body */
        .card-content {
            padding: 0 24px 24px;
        }

        .data-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 10px 18px;
            margin-bottom: 18px;
            border: 1px solid #f1f5f9;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child { border-bottom: none; }

        .label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-slate);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .label i { color: #94a3b8; font-size: 14px; width: 18px; text-align: center; }

        .value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            text-align: right;
        }

        .value.highlight {
            color: var(--mg-red);
        }

        /* Status Indicator */
        .status-pill {
            background: #dcfce7;
            color: #166534;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Action Group */
        .action-group {
            padding: 0 24px 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-main {
            background: var(--mg-red);
            color: #ffffff;
            text-align: center;
            padding: 16px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 10px 20px rgba(206, 24, 30, 0.2);
            transition: 0.3s;
        }

        .btn-main:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(206, 24, 30, 0.3); }

        .secondary-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn-sub {
            background: #ffffff;
            color: var(--text-dark);
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 14px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-sub:hover { background: #f8fafc; border-color: #cbd5e1; }

        .footer-text {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 1px;
            padding-bottom: 25px;
        }

    </style>
</head>
<body>

    <div class="digital-card">
        <div class="brand-header">
            <div class="mg-logo-text">MG</div>
            <div class="member-badge">VIP MEMBERSHIP</div>
        </div>

        <div class="profile-section">
            <h3>{{ $contract->customer_name ?? 'N/A' }}</h3>
            <p>{{ $contract->phone_number ?? 'N/A' }}</p>
        </div>

        <div class="card-content">
            <div class="data-card">
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-car-side"></i> ម៉ូដែលរថយន្ត</span>
                    <span class="value highlight">{{ $contract->brand_name ?? 'MG' }} {{ $contract->brand_model ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-calendar-day"></i> ថ្ងៃទទួលឡាន</span>
                    <span class="value">{{ $contract->delivery_date ? \Carbon\Carbon::parse($contract->delivery_date)->format('d-M-Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-palette"></i> ពណ៌</span>
                    <span class="value">{{ $contract->color_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-location-dot"></i> សាខា</span>
                    <span class="value">{{ $contract->branch->branch_name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="data-card">
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-shield-halved"></i> ការធានារថយន្ត</span>
                    <span class="value">
                        @php
                            $warrantyText = 'ស្តង់ដារក្រុមហ៊ុន';
                            if (is_object($contract->warranty) || is_array($contract->warranty)) {
                                $warrantyText = $contract->warranty->name ?? $contract->warranty['name'] ?? 'ស្តង់ដារក្រុមហ៊ុន';
                            } elseif (is_string($contract->warranty) && str_starts_with($contract->warranty, '{')) {
                                $warrantyText = json_decode($contract->warranty)->name ?? 'ស្តង់ដារក្រុមហ៊ុន';
                            } elseif (!empty($contract->warranty) && $contract->warranty != 'N/A') {
                                $warrantyText = $contract->warranty;
                            }
                        @endphp
                        {{ $warrantyText }}
                    </span>
                </div>

                @if(!empty($contract->warranty_leap_motor) && $contract->warranty_leap_motor != 'N/A')
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-plug-circle-bolt"></i> ការធានាថ្ម (EV)</span>
                    <span class="value">{{ $contract->warranty_leap_motor }}</span>
                </div>
                @endif

                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-oil-can"></i> ប័ណ្ណដូរប្រេង</span>
                    @if(strtolower($contract->certificate_engine_oil ?? '') == 'yes')
                        <span class="value" style="color: #16a34a;">មាន (Available)</span>
                    @else
                        <span class="value" style="color: var(--text-slate);">មិនមាន (N/A)</span>
                    @endif
                </div>

                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-screwdriver-wrench"></i> ថែទាំលើកក្រោយ</span>
                    <span class="value" style="color: #ea580c;">រៀងរាល់ 10,000 គ.ម</span>
                </div>
            </div>

            <div class="data-card" style="margin-bottom: 0;">
                <div class="info-row">
                    <span class="label"><i class="fa-solid fa-shield-check"></i> ស្ថានភាពកាត</span>
                    <span class="status-pill">ACTIVE</span>
                </div>
            </div>
        </div>

        <div class="action-group">
            <a href="https://mg-dealer.netem.online/bWctYm9va2luZy1zZXJ2aWNl" class="btn-main">
                <i class="fa-solid fa-calendar-check me-2"></i> កក់សេវាកម្ម / Book Service
            </a>
            
            <div class="secondary-actions">
                <a href="tel:023232111" class="btn-sub">
                    <i class="fa-solid fa-headset" style="color: var(--mg-red);"></i> ផ្នែកសេវាកម្ម
                </a>
                <a href="tel:023232123" class="btn-sub">
                    <i class="fa-solid fa-truck-fast" style="color: var(--mg-red);"></i> ឡានសង្គ្រោះ
                </a>
            </div>
        </div>

        <div class="footer-text">
            © 2026 MG CAMBODIA • SMART SOLUTIONS
        </div>
    </div>

</body>
</html>