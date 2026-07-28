@extends('layouts.homePages')

@section('content')
    @php
        $user = auth('users')->user();
        $isAdmin = in_array($user->role_id, [1, 2, 6]);
        $isManager = in_array($user->role_id, [3, 4, 5, 7, 8]);
        $canEditStaff = $isAdmin || $isManager;

        $carModels = [
            'Forland Cargo Truck', 'Maxus D60', 'Maxus G50', 'Maxus T60', 'Maxus T90',
            'Maxus Truck S50-D19', 'Maxus V80', 'MG 4 EV Luxury', 'MG 4 EV STD',
            'MG 7 1.5 4 cly', 'MG 7 2.0 4 cly', 'MG Cyberster', 'MG G50', 'MG GT Deluxe',
            'MG HS', 'MG Marvel R', 'MG RX5', 'MG RX5 LUX Sport', 'MG RX8', 'MG RX9',
            'MG ZS', 'Mifi 7', 'Mifi 9',
        ];
        $offersList = [
            '1 Year Full Service/3times', 'Auto Tailgate', 'Floor Mats', 'Long Plate Number',
            'Normal Plate Number', 'Rear Spoiler', 'Side Step', 'UV Film On Windows',
            'Vouchers', 'Warranty 3 Years or 100,000 KM', 'Warranty 5 Years or 100,000 KM',
            'Warranty 5 Years or 150,000 KM',
        ];
    @endphp

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* 🎨 Professional Corporate CRM Theme - FULL SCREEN */
        :root {
            --primary: #2563eb;       /* Professional Royal Blue */
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --secondary: #f8fafc;
            --accent: #059669;        /* Emerald Green for Success/Money */
            --danger: #dc2626;
            --dark: #0f172a;          /* Slate 900 for main text */
            --gray-text: #475569;     /* Slate 600 for labels */
            --border: #e2e8f0;
            --bg-app: #f1f5f9;
            --card-bg: #ffffff;
            --shadow-subtle: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --radius-md: 10px;
            --radius-sm: 6px;
        }

        body {
            font-family: 'Inter', 'Kantumruy Pro', sans-serif !important;
            background-color: var(--bg-app);
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
        }

        /* 🌟 Full Screen Layout Adjustment */
        .container-pro {
            width: 100%; 
            max-width: 100%; /* លាតពេញ ១០០% */
            margin: 0; 
            padding: 1rem 1.5rem; /* បង្រួម Padding ជុំវិញ */
        }

        /* 🌟 Page Header */
        .pro-header {
            display: flex; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem;
            border-bottom: 1px solid #cbd5e1;
        }
        .btn-back-pro {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; background: white; border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--gray-text); font-size: 14px;
            box-shadow: var(--shadow-subtle); transition: 0.2s; margin-right: 1.25rem; text-decoration: none !important;
        }
        .btn-back-pro:hover { background: var(--bg-app); color: var(--dark); }
        .page-title { font-size: 20px; font-weight: 700; margin: 0; color: var(--dark); letter-spacing: -0.025em; }
        .page-subtitle { font-size: 12px; font-weight: 500; color: var(--gray-text); margin: 2px 0 0 0; }

        /* 🌟 Professional Cards */
        .pro-card {
            background: var(--card-bg); border-radius: var(--radius-md);
            border: 1px solid var(--border); margin-bottom: 1rem; /* បន្ថយ Margin */
            box-shadow: var(--shadow-subtle); padding: 1.25rem 1.5rem; /* បន្ថយ Padding ក្នុងប្រអប់ */
        }
        .card-header-pro {
            display: flex; align-items: center; gap: 10px; margin-bottom: 1.25rem;
            padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);
        }
        .card-title-text { font-size: 15px; font-weight: 600; color: var(--dark); margin: 0; }
        .icon-pro {
            width: 28px; height: 28px; border-radius: var(--radius-sm); background: var(--bg-app);
            color: var(--gray-text); display: flex; align-items: center; justify-content: center; font-size: 13px;
        }

        /* 🌟 Form UI */
        .form-label {
            font-weight: 600; font-size: 12px; color: var(--gray-text);
            margin-bottom: 6px; display: block;
        }
        .form-control-pro {
            width: 100%; height: 38px; border-radius: var(--radius-sm);
            border: 1px solid var(--border); background-color: #fff;
            padding: 0 12px; font-size: 13px; color: var(--dark);
            transition: all 0.2s ease-in-out; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) inset;
        }
        .form-control-pro:focus {
            border-color: var(--primary); outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .form-control-pro::placeholder { color: #94a3b8; font-weight: 400; }
        textarea.form-control-pro { padding: 10px 12px; height: auto !important; line-height: 1.5; }
        
        .fw-semibold { font-weight: 600 !important; }

        /* Select2 Corporate Override */
        .select2-container--default .select2-selection--single {
            height: 38px !important; border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border) !important; background-color: #fff !important;
            display: flex !important; align-items: center !important; padding-left: 4px !important;
            font-size: 13px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) inset;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        }

        /* 🌟 Upload Zone */
        .upload-zone-pro {
            border: 1px dashed #94a3b8; border-radius: var(--radius-sm); padding: 1.25rem;
            text-align: center; background: #f8fafc; cursor: pointer; transition: 0.2s; margin-bottom: 1.25rem;
        }
        .upload-zone-pro:hover { border-color: var(--primary); background: var(--primary-light); }
        .upload-icon-pro { font-size: 22px; color: #64748b; margin-bottom: 8px; }
        #id-preview { max-width: 100%; height: 100px; object-fit: contain; display: none; margin-top: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); }

        /* 🌟 Pill Checkboxes (Clean & Compact) */
        .offer-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 0.5rem; }
        .offer-pill { position: relative; display: inline-flex; align-items: center; cursor: pointer; }
        .offer-pill input[type="checkbox"] { display: none; }
        .offer-pill span {
            padding: 5px 12px; background: white; border: 1px solid var(--border);
            border-radius: 4px; font-size: 12px; font-weight: 500; color: var(--gray-text);
            transition: 0.2s; user-select: none; box-shadow: var(--shadow-subtle);
        }
        .offer-pill:hover span { border-color: #cbd5e1; background: #f8fafc; }
        .offer-pill input[type="checkbox"]:checked + span {
            background: var(--primary-light); border-color: var(--primary); color: var(--primary); font-weight: 600;
        }
        .offer-pill input[type="checkbox"]:checked + span::before { content: '✓'; margin-right: 6px; }

        /* 🌟 Financial Invoice Block (Highly Professional) */
        .finance-block {
            background: #0f172a; /* Slate 900 */
            border-radius: var(--radius-md); padding: 1.25rem 1.5rem; color: white; margin-bottom: 1.25rem;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
            border: 1px solid #1e293b;
        }
        .finance-label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .finance-value { font-size: 32px; font-weight: 700; margin: 0; font-family: 'Inter', monospace; letter-spacing: -1px; }

        /* 🌟 Actions / Buttons */
        .btn-submit-pro {
            background: var(--primary); color: white; border: none; padding: 0 20px; height: 42px;
            border-radius: var(--radius-sm); font-weight: 600; font-size: 13px; width: 100%;
            transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .btn-submit-pro:hover { background: var(--primary-hover); box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2); }

        .btn-action-pro {
            width: 38px; height: 38px; border-radius: var(--radius-sm); border: 1px solid var(--border);
            background: white; color: var(--gray-text); display: flex; align-items: center; justify-content: center;
            transition: 0.2s; flex-shrink: 0; box-shadow: var(--shadow-subtle);
        }
        .btn-add-pro:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }
        .btn-remove-pro { color: var(--danger); border-color: #fecaca; }
        .btn-remove-pro:hover { background: #fef2f2; }
        
        .offer-row { display: flex; gap: 8px; margin-bottom: 8px; }
        .sticky-column { position: sticky; top: 1rem; z-index: 10; }
        
        /* Grid Adjustments */
        .g-pro { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
        .section-label { font-size: 13px; font-weight: 600; color: var(--dark); border-bottom: 1px solid var(--border); padding-bottom: 6px; margin: 1.25rem 0 0.75rem 0; }
    </style>

    <div class="container-pro text-start">

        <!-- Header -->
        <div class="pro-header">
            <a href="{{ route('contracts.index') }}" class="btn-back-pro" title="Back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title">ចុះឈ្មោះកិច្ចសន្យាថ្មី (New Registry Record)</h1>
                <p class="page-subtitle">Fill in the required information to generate a new sales contract.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-2 border-0 shadow-sm p-3 mb-3" style="background-color: #fef2f2; color: #991b1b;">
                <ul class="mb-0 fw-semibold small" style="list-style-type: square; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data" id="addForm">
            @csrf
            <!-- 🌟 បន្ថយ Gap ដើម្បីឱ្យហាប់ណែនល្អ -->
            <div class="row align-items-start g-3">

                <!-- Left Column -->
                <div class="col-xl-8 col-lg-8">

                    <!-- ================= CARD 1: CUSTOMER PROFILE ================= -->
                    <div class="pro-card">
                        <div class="card-header-pro">
                            <div class="icon-pro"><i class="fas fa-user"></i></div>
                            <h3 class="card-title-text">ព័ត៌មានអតិថិជន (Customer Profile)</h3>
                        </div>

                        <div class="upload-zone-pro" id="upload-zone">
                            <i class="fas fa-id-card upload-icon-pro"></i>
                            <div class="fw-semibold text-dark" style="font-size: 13px;">ឯកសារយោង (ID Card / Passport)</div>
                            <p class="small text-muted mb-0 mt-1">ចុចទីនេះដើម្បីជ្រើសរើសរូបភាពអត្តសញ្ញាណប័ណ្ណបញ្ជាក់</p>
                            <input type="file" name="customer_id_card" id="id_image_input" class="d-none" accept="image/*">
                            <img id="id-preview">
                        </div>

                        <div class="row g-pro">
                            <div class="col-md-6">
                                <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control-pro fw-semibold" placeholder="e.g. VANN DADA" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ឈ្មោះ (Khmer)</label>
                                <input type="text" name="customer_name_kh" class="form-control-pro" placeholder="ឧ. វ៉ាន់ ដាដា">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" class="form-control-pro fw-semibold" placeholder="01X XXX XXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">National ID / Passport</label>
                                <input type="text" name="id_card_number" class="form-control-pro" placeholder="XXXXXXXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Member Card Assignment</label>
                                <select name="membership_number" class="form-control-pro select2-js">
                                    <option value="">-- ជ្រើសរើសលេខកាតពីស្តុក --</option>
                                    @foreach ($availableCards ?? [] as $card)
                                        <option value="{{ $card->member_card }}">{{ $card->member_card }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address / អាសយដ្ឋាន</label>
                                <textarea name="address" rows="2" class="form-control-pro" placeholder="អាសយដ្ឋានបច្ចុប្បន្នរបស់អតិថិជន..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ================= CARD 2: CONTRACT & VEHICLE ================= -->
                    <div class="pro-card mb-0">
                        <div class="card-header-pro">
                            <div class="icon-pro"><i class="fas fa-file-invoice"></i></div>
                            <h3 class="card-title-text">កិច្ចសន្យា និង រថយន្ត (Contract & Vehicle Details)</h3>
                        </div>

                        <!-- Contract Info -->
                        <div class="section-label mt-0">ព័ត៌មានឯកសារ (Document Details)</div>
                        <div class="row g-pro mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Contract ID <span class="text-danger">*</span></label>
                                <input type="text" name="contract_number" class="form-control-pro fw-bold" style="color: var(--primary);" placeholder="HXXXXXX" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="form-control-pro" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Source / ប្រភពអតិថិជន</label>
                                <select name="source_id" class="form-control-pro select2-js">
                                    <option value="" selected>-- រើសប្រភព --</option>
                                    <optgroup label="Digital Channels">
                                        <option value="Facebook">Facebook</option>
                                        <option value="TikTok">TikTok</option>
                                        <option value="Telegram">Telegram</option>
                                    </optgroup>
                                    <optgroup label="Physical / Offline">
                                        <option value="Showroom Walk-in">Showroom Walk-in</option>
                                        <option value="Event / Exhibition">Event / Exhibition</option>
                                    </optgroup>
                                    <optgroup label="Others">
                                        <option value="Customer Referral">Customer Referral</option>
                                        <option value="Phone Call">Phone Call</option>
                                    </optgroup>
                                </select>
                            </div>
                            
                            <!-- 🌟 ប្រអប់ Branch (បង្ហាញតែ Admin/Director) 🌟 -->
                            @if ($isAdmin)
                            <div class="col-md-6">
                                <label class="form-label">Branch / សាខា <span class="text-danger">*</span></label>
                                <select name="branch_name" class="form-control-pro select2-js" required>
                                    <option value="">-- ជ្រើសរើសសាខា --</option>
                                    @foreach ($allBranches ?? [] as $b)
                                        <option value="{{ $b }}">{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="branch_name" value="{{ $allBranches[0] ?? '' }}">
                            @endif

                            <!-- 🌟 ប្រអប់ Sale Person (បង្ហាញតែអ្នកមានសិទ្ធិ) 🌟 -->
                            @if ($canEditStaff)
                            <div class="col-md-6">
                                <label class="form-label">Sales Representative <span class="text-danger">*</span></label>
                                <select name="sale_person" class="form-control-pro select2-js" required>
                                    <option value="">-- ជ្រើសរើសអ្នកលក់ --</option>
                                    @foreach ($staff ?? [] as $s)
                                        <option value="{{ $s }}" @selected($user->full_name == $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="sale_person" value="{{ $user->full_name }}">
                            @endif

                            <!-- 🌟 សារបង្ហាញប្រាប់ Sale ធម្មតាថាគាត់កំពុងប្រើឈ្មោះនិងសាខាអ្វី (លាក់ប្រអប់មិនឱ្យគាត់កែបាន) 🌟 -->
                            @if (!$isAdmin || !$canEditStaff)
                            <div class="col-12">
                                <div class="alert alert-info py-2 mb-0" style="font-size: 12.5px; background-color: var(--primary-light); border: 1px solid #bfdbfe; color: var(--primary-hover);">
                                    <i class="fas fa-info-circle me-1"></i> កិច្ចសន្យានេះនឹងត្រូវរក្សាទុកក្រោមឈ្មោះ <strong>{{ $user->full_name }}</strong> ប្រចាំសាខា <strong>{{ $allBranches[0] ?? '' }}</strong>.
                                </div>
                            </div>
                            @endif
                        </div>

                        @php
                            $currentUser = auth('users')->user();
                            $isAdminOrDirector = $currentUser ? in_array((int) $currentUser->role_id, [1, 2, 6]) : false;
                            $myBranchNameLower = strtolower(
                                $currentUser ? \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name') : ''
                            );
                            $isLeapBranch = str_contains($myBranchNameLower, 'leap');
                            $isMGBranch = !$isLeapBranch;
                        @endphp

                        <!-- Vehicle Specs -->
                        <div class="section-label">លក្ខណៈបច្ចេកទេសរថយន្ត (Vehicle Specifications)</div>
                        <div class="row g-pro">
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Model <span class="text-danger">*</span></label>
                                <select name="model" class="form-control-pro select2-js" required>
                                    <option value="" disabled selected>-- ជ្រើសរើសម៉ូដែលរថយន្ត --</option>
                                    @if ($isAdminOrDirector || $isMGBranch)
                                        <optgroup label="MG Motors">
                                            @foreach (['MG ZS', 'MG HS', 'MG5', 'MG7', 'MG RX5', 'MG RX8', 'MG ONE', 'MG4 EV', 'MG ZS EV', 'MG Marvel R', 'MG Cyberster', 'MG Maxus 9'] as $m)
                                                <option value="{{ $m }}">{{ $m }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if ($isAdminOrDirector || $isLeapBranch)
                                        <optgroup label="Leapmotor">
                                            @foreach (['Leapmotor T03', 'Leapmotor C10', 'Leapmotor C11', 'Leapmotor C01', 'Leapmotor C16'] as $m)
                                                <option value="{{ $m }}">{{ $m }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" id="qty" name="qty" class="form-control-pro text-center fw-semibold calc-val" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Color / ពណ៌</label>
                                <input type="text" name="color" class="form-control-pro" placeholder="e.g. White / Black">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">VIN Number (លេខតួ)</label>
                                <input type="text" name="vin_number" class="form-control-pro fw-semibold" style="font-family: monospace;" placeholder="Enter VIN...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Engine Number (លេខម៉ាស៊ីន)</label>
                                <input type="text" name="engine_number" class="form-control-pro fw-semibold" style="font-family: monospace;" placeholder="Enter Engine No...">
                            </div>
                            
                            <!-- Offers -->
                            <div class="col-12 mt-2">
                                <label class="form-label">Campaign Offers / កញ្ចប់កាដូថែមជូន</label>
                                <div class="offer-grid">
                                    @foreach ($offersList as $index => $offer)
                                        <label class="offer-pill">
                                            <input type="checkbox" id="offer_{{ $index }}" name="offers[]" value="{{ $offer }}">
                                            <span>{{ $offer }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 mt-1">
                                <label class="form-label">Other Accessories / ថែមអ្វីផ្សេងទៀត</label>
                                <div id="offers-list">
                                    <div class="offer-row">
                                        <input type="text" name="offers[]" class="form-control-pro" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
                                        <button type="button" class="btn-action-pro btn-add-pro" onclick="addOfferField()"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sticky Financials) -->
                <div class="col-xl-4 col-lg-4">
                    <div class="pro-card sticky-column">
                        <div class="card-header-pro mb-3">
                            <div class="icon-pro"><i class="fas fa-calculator"></i></div>
                            <h3 class="card-title-text">ហិរញ្ញវត្ថុ (Financial Settlement)</h3>
                        </div>

                        <!-- Professional Invoice Block -->
                        <div class="finance-block">
                            <div class="finance-label">Balance Due (USD)</div>
                            <div class="finance-value">$<span id="span_total">0.00</span></div>
                            <input type="hidden" id="balance" name="balance" value="0">
                        </div>

                        <div class="row g-pro">
                            <div class="col-12">
                                <label class="form-label">Unit Price ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="unit_price" name="unit_price" class="form-control-pro calc-val fw-bold" placeholder="0.00" required>
                            </div>
                            
                            <div class="col-6">
                                <label class="form-label">Discount ($)</label>
                                <input type="number" step="0.01" id="discount" name="discount" class="form-control-pro calc-val text-danger fw-semibold" placeholder="0.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Deposit Paid ($)</label>
                                <input type="number" step="0.01" id="deposit" name="deposit" class="form-control-pro calc-val text-success fw-semibold" placeholder="0.00">
                            </div>

                            <div class="col-12 mt-1">
                                <label class="form-label">Sub Total ($)</label>
                                <input type="text" id="sub_total_display" class="form-control-pro fw-semibold" style="background-color: var(--bg-app); border-color: var(--bg-app); color: var(--gray-text);" value="0.00" readonly>
                                <input type="hidden" id="sub_total" name="sub_total" value="0">
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_type" id="payment_type" class="form-control-pro select2-js" required>
                                    <option value="Cash">Cash (សាច់ប្រាក់)</option>
                                    <option value="Bank Transfer">Bank Transfer / Loan</option>
                                    <option value="Installment">Installment (រំលស់)</option>
                                </select>
                            </div>

                            <div class="col-12 mt-2" id="bank_name_wrapper" style="display: none;">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control-pro" placeholder="e.g. ABA, Campu Bank...">
                            </div>

                            <div class="col-12 mt-2 mb-2">
                                <label class="form-label">Order Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control-pro fw-semibold select2-js" required>
                                    <option value="Booking" selected>BOOKING / កក់ប្រាក់</option>
                                    <option value="Payoff">PAYOFF / ទូទាត់ផ្ដាច់រួចរាល់</option>
                                    <option value="Delivery">DELIVERY / ប្រគល់រថយន្ត</option>
                                    <option value="Cancelled">CANCELLED / លុបចោលការកក់</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn-submit-pro">
                                    <i class="fas fa-save"></i> Save Contract
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Init Select2
            $('.select2-js').select2({ width: '100%' });

            // Handle Bank Transfer Input Display
            $('#payment_type').on('change', function() {
                if ($(this).val() === 'Bank Transfer') {
                    $('#bank_name_wrapper').slideDown(200);
                } else {
                    $('#bank_name_wrapper').slideUp(200);
                    $('input[name="bank_name"]').val('');
                }
            });

            // Handle ID Card Upload Area
            $('#upload-zone').on('click', function(e) {
                if (e.target.id !== 'id_image_input') $('#id_image_input').click();
            });
            $('#id_image_input').on('click', function(e) { e.stopPropagation(); });

            $('#id_image_input').change(function() {
                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#id-preview').attr('src', e.target.result).fadeIn();
                        $('.upload-icon-pro').removeClass('fa-id-card').addClass('fa-check-circle text-success');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Auto Calculation Logic
            $('.calc-val').on('input', function() {
                let qty = parseFloat($('#qty').val()) || 1;
                let unitPrice = parseFloat($('#unit_price').val()) || 0;
                let dis = parseFloat($('#discount').val()) || 0;
                let dep = parseFloat($('#deposit').val()) || 0;

                let subTotal = qty * unitPrice;
                $('#sub_total_display').val(subTotal.toFixed(2));
                $('#sub_total').val(subTotal);

                let balance = subTotal - dis - dep;
                balance = balance < 0 ? 0 : balance;

                $('#span_total').text(balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#balance').val(balance);
            });
        });

        // Add Offer Field Dynamically
        function addOfferField() {
            $('#offers-list').append(`
                <div class="offer-row">
                    <input type="text" name="offers[]" class="form-control-pro" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
                    <button type="button" class="btn-action-pro btn-remove-pro" onclick="$(this).parent().remove()"><i class="fas fa-times"></i></button>
                </div>
            `);
        }

        // Loading Alert on Submit
        $('#addForm').on('submit', function() {
            Swal.fire({
                title: 'កំពុងរក្សាទុកទិន្នន័យ...',
                text: 'សូមរង់ចាំបន្តិច ប្រព័ន្ធកំពុងដំណើរការ',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        });
    </script>
@endsection