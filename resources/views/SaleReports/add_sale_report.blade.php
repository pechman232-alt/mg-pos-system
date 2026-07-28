@extends('layouts.homePages')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        /* ==================== PROFESSIONAL FORM UI SYSTEM ==================== */
        :root {
            --brand-red: #CE181E;
            --brand-blue: #002663;
            --bg-body: #f8fafc;
            --text-heading: #0f172a;
            --text-muted: #64748b;
            --border-soft: #cbd5e1;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: var(--bg-body); margin: 0; padding: 0; }
        .content-fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

        /* HEADER */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
            background: #ffffff; padding: 15px 25px; border-radius: 16px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); position: sticky; top: 15px; z-index: 100;
        }
        .page-title { font-size: 1.25rem; font-weight: 800; color: var(--text-heading); margin: 0; display: flex; align-items: center; gap: 10px; }
        .page-title i { color: var(--brand-red); }
        .btn-outline-custom { background: #ffffff; color: var(--text-heading); border: 2px solid var(--border-soft); border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-outline-custom:hover { background: #f1f5f9; border-color: #94a3b8; color: var(--text-heading); }
        .btn-submit-node { background: var(--brand-red); color: #ffffff; border: none; border-radius: 10px; font-weight: 700; padding: 10px 24px; transition: 0.3s; box-shadow: 0 4px 6px rgba(206, 24, 30, 0.2); display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
        .btn-submit-node:hover { background: #a91217; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(206, 24, 30, 0.3); color: #ffffff;}

        /* ENTRY CARDS */
        .entry-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); margin-bottom: 24px; padding: 24px; }
        .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-heading); display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px dashed #e2e8f0; }
        .section-title i { color: var(--brand-red); background: #fff5f5; padding: 8px; border-radius: 8px; }

        /* FORM INPUTS */
        .form-label-custom { font-size: 0.75rem !important; font-weight: 800 !important; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; display: block; letter-spacing: 0.5px; }
        .form-control, .form-select { height: 44px !important; border-radius: 8px !important; border: 1.5px solid var(--border-soft) !important; font-size: 0.9rem !important; font-weight: 600 !important; color: var(--text-heading) !important; transition: 0.2s; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-red) !important; box-shadow: 0 0 0 4px rgba(206, 24, 30, 0.1) !important; background-color: #ffffff; outline: none; }
        .text-primary-bold { color: var(--brand-red) !important; font-weight: 800; }
        .required-mark { color: var(--brand-red); font-size: 1rem; margin-left: 2px; }

        /* ================= 🌟 BRANCH CARDS (ងាយស្រួលចុច) 🌟 ================= */
        .branch-selector { display: none; } 
        .branch-card { 
            display: flex; flex-direction: column; align-items: center; justify-content: center; 
            padding: 15px 10px; border: 2px solid #e2e8f0; border-radius: 12px; 
            background: #f8fafc; cursor: pointer; transition: all 0.2s ease-in-out; text-align: center; 
            height: 100%;
        }
        .branch-card i { font-size: 1.8rem; color: #94a3b8; margin-bottom: 8px; transition: 0.2s; }
        .branch-card span { font-weight: 800; font-size: 0.9rem; color: #475569; }
        .branch-card:hover { border-color: #fca5a5; background: #fef2f2; }
        
        .branch-selector:checked + .branch-card { 
            border-color: var(--brand-red); background: #fff5f5; box-shadow: 0 4px 12px rgba(206, 24, 30, 0.15); 
        }
        .branch-selector:checked + .branch-card i, .branch-selector:checked + .branch-card span { color: var(--brand-red); }

        /* ================= CUSTOM CHECKBOX TOGGLE ================= */
        .check-toggle { display: none; }
        .check-toggle-label {
            display: flex; align-items: center; justify-content: center; height: 44px;
            width: 100%; border: 1.5px solid var(--border-soft); border-radius: 8px; 
            background: #ffffff; color: var(--text-muted); font-weight: 700; font-size: 0.85rem; 
            cursor: pointer; transition: all 0.2s; margin: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .check-toggle-label:hover { background: #f8fafc; border-color: #cbd5e1; }
        .check-toggle:checked + .check-toggle-label {
            background: #ecfdf5; border-color: #10b981; color: #059669; box-shadow: inset 0 0 0 1px #10b981;
        }
        .check-toggle:checked + .check-toggle-label::before { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; margin-right: 6px; font-size: 1rem; }
        .check-toggle-label::before { content: '\f111'; font-family: 'Font Awesome 6 Free'; font-weight: 400; margin-right: 6px; font-size: 1rem; color: #cbd5e1; }

        /* ================= CUSTOM RADIO BUTTONS (PILL STYLE) ================= */
        .radio-pill-group { display: flex; flex-wrap: wrap; gap: 8px; }
        .radio-pill-input { display: none; }
        .radio-pill-label {
            padding: 8px 16px; border: 1.5px solid var(--border-soft); border-radius: 20px;
            background: #ffffff; color: var(--text-muted); font-weight: 700; font-size: 0.85rem;
            cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02); white-space: nowrap;
        }
        .radio-pill-label:hover { background: #f8fafc; border-color: #cbd5e1; }
        .radio-pill-input:checked + .radio-pill-label {
            background: #fff5f5; border-color: var(--brand-red); color: var(--brand-red); box-shadow: inset 0 0 0 1px var(--brand-red);
        }

        /* SELECT2 FIXES FOR SEARCHABLE BOXES */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single { 
            height: 44px !important; border: 1.5px solid var(--border-soft) !important; border-radius: 8px !important; 
            background-color: #f8fafc !important; padding: 5px 12px; transition: 0.2s; outline: none; display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single:focus-within, 
        .select2-container--default.select2-container--open .select2-selection--single { 
            border-color: var(--brand-red) !important; box-shadow: 0 0 0 4px rgba(206, 24, 30, 0.1) !important; background-color: #ffffff !important; 
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: var(--text-heading) !important; font-weight: 600 !important; line-height: normal !important; padding-left: 0 !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; right: 12px !important; }
        
        .bg-light-box { background: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; }
    </style>

    <div class="content-fluid-wrapper">
        @include('Helps.alert')

        <form action="{{ url('/sale-reports/store') }}" method="POST" enctype="multipart/form-data" id="createReportForm">
            @csrf

            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-file-invoice-dollar"></i> NEW SALE REPORT</h1>
                <div class="d-flex gap-3">
                    <a href="{{ url('sale-reports') }}" class="btn-outline-custom"><i class="fas fa-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn-submit-node"><i class="fas fa-save"></i> Save Report</button>
                </div>
            </div>

            <div class="entry-card">
                <div class="section-title"><i class="fa-solid fa-building-flag"></i> ១. ជ្រើសរើសសាខា (Select Branch) <span class="required-mark">*</span></div>
                <div class="row g-3">
                    @foreach($branchs as $branch)
                    <div class="col-6 col-md-3">
                        <input type="radio" name="branch_id" id="branch_{{ $branch->id }}" value="{{ $branch->id }}" class="branch-selector" {{ old('branch_id') == $branch->id ? 'checked' : '' }} required>
                        <label for="branch_{{ $branch->id }}" class="branch-card">
                            <i class="fa-solid fa-store"></i>
                            <span>{{ $branch->branch_name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="entry-card h-100 m-0">
                        <div class="section-title"><i class="fas fa-user-tie"></i> ២. ព័ត៌មានអតិថិជន (Customer Info)</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Customer Name <span class="required-mark">*</span></label>
                                <input type="text" name="customer_name" class="form-control text-primary-bold" placeholder="Enter full name" value="{{ old('customer_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="012 345 678" value="{{ old('phone_number') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Gender</label>
                                <select name="gender" class="form-select select2-searchable">
                                    <option value="" selected disabled>-- ជ្រើសរើស --</option>
                                    <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                                    <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                            </div>
                            
                            <div class="col-6">
                                <label class="form-label-custom">Province</label>
                                <select name="province_id" class="form-select select2-searchable">
                                    <option value="" selected disabled>-- ស្វែងរកខេត្ត --</option>
                                    @foreach ($provinces as $item)
                                        <option value="{{ $item->id }}" @selected(old('province_id') == $item->id)>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Source Customer</label>
                                <select name="source_id" class="form-select select2-searchable">
                                    <option value="" selected disabled>-- ស្វែងរកប្រភព (Marketing Channel) --</option>
                                    
                                    <optgroup label="Digital Channels">
                                        <option value="Facebook" @selected(old('source_id') == 'Facebook')>Facebook</option>
                                        <option value="TikTok" @selected(old('source_id') == 'TikTok')>TikTok</option>
                                        <option value="Telegram" @selected(old('source_id') == 'Telegram')>Telegram</option>
                                        <option value="YouTube" @selected(old('source_id') == 'YouTube')>YouTube</option>
                                        <option value="Instagram" @selected(old('source_id') == 'Instagram')>Instagram</option>
                                        <option value="Official Website" @selected(old('source_id') == 'Official Website')>Official Website</option>
                                    </optgroup>
                                    
                                    <optgroup label="Physical / Offline">
                                        <option value="Showroom Walk-in" @selected(old('source_id') == 'Showroom Walk-in')>Showroom Walk-in</option>
                                        <option value="Event/Exhibition" @selected(old('source_id') == 'Event/Exhibition')>Event/Exhibition</option>
                                        <option value="Roadshow" @selected(old('source_id') == 'Roadshow')>Roadshow</option>
                                        <option value="Call In" @selected(old('source_id') == 'Call In')>Call In</option> </optgroup>
                                    
                                    <optgroup label="Other">
                                        <option value="Customer Referral" @selected(old('source_id') == 'Customer Referral')>Customer Referral</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-12 mt-3 pt-3" style="border-top: 1px dashed var(--border-soft);">
                                <label class="form-label-custom text-primary-bold">Member Card (Search)</label>
                                <input type="hidden" id="old-memberCard-id" value="{{ old('member_card_id') }}">
                                <select id="memberCard-select" name="member_card_id" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="entry-card h-100 m-0">
                        <div class="section-title"><i class="fas fa-file-signature"></i> ៣. ព័ត៌មានការលក់ (Commercial)</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Total Price ($) <span class="required-mark">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control fw-bold text-danger fs-5" placeholder="0.00" value="{{ old('price') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Invoice No <span class="required-mark">*</span></label>
                                <input type="text" name="invoice_no" class="form-control fw-bold" placeholder="INV-2026..." value="{{ old('invoice_no') }}" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-custom text-primary-bold">Sale Person (អ្នកលក់) <span class="required-mark">*</span></label>
                                <select name="employee_id" class="form-select select2-searchable" required>
                                    <option value="" disabled selected>-- ស្វែងរកឈ្មោះអ្នកលក់ --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Contract No</label>
                                <input type="text" name="contract_no" class="form-control" placeholder="CT-001..." value="{{ old('contract_no') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Contract Date</label>
                                <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Invoice Date <span class="required-mark">*</span></label>
                                <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="entry-card m-0 h-100">
                        <div class="section-title"><i class="fas fa-car-side"></i> ៤. យានយន្ត និងការធានា (Vehicle Specs)</div>
                        
                        <div class="bg-light-box text-center position-relative mb-4">
                            <img src="{{ asset('backend/assets/img/avatars/image.png') }}" id="target" style="height: 100px; object-fit: contain; margin-bottom: 10px;" />
                            <div class="fw-800 text-danger fs-6 mb-2" id="modelName">NO MODEL SELECTED</div>
                            <button type="button" class="btn btn-dark btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#largeModal">
                                <i class="fas fa-search me-1"></i> Choose Brand Model
                            </button>
                            <input type="hidden" name="image" id="carImageId" value="{{ old('image') }}">
                            <input type="hidden" name="model_name" id="modelNameInput" value="{{ old('model_name') }}">
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-custom text-primary-bold">Search VIN / Engine (Logistics) <span class="required-mark">*</span></label>
                                <input type="hidden" id="old-logistic-id" value="{{ old('logistics_id') }}">
                                <select id="logistic-select" name="logistics_id" style="width: 100%;" required></select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Brand Name</label>
                                <select name="brand_name" class="form-select select2-searchable">
                                    <option value="MG" @selected(old('brand_name') == 'MG')>MG</option>
                                    <option value="Maxus" @selected(old('brand_name') == 'Maxus')>Maxus</option>
                                    <option value="LeapMotor" @selected(old('brand_name') == 'LeapMotor')>LeapMotor</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Plate Number</label>
                                <input type="text" name="plate_number" class="form-control" placeholder="ABC-1234" value="{{ old('plate_number') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Warranty MG</label>
                                <select name="warranty_id" class="form-select select2-searchable">
                                    <option value="" selected>-- ស្វែងរក --</option>
                                    @foreach ($warrantys as $item) 
                                        <option value="{{ $item->id }}" @selected(old('warranty_id') == $item->id)>{{ $item->name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Battery Warranty Leap</label>
                                <select name="warranty_leap_motor" class="form-select select2-searchable">
                                    <option value="" selected>-- ស្វែងរក --</option>
                                    @foreach ($warrantys as $item) 
                                        <option value="{{ $item->id }}" @selected(old('warranty_leap_motor') == $item->id)>{{ $item->name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="entry-card m-0 h-100">
                        <div class="section-title"><i class="fas fa-gift"></i> ៥. កាដូថែមជូន និងផ្សេងៗ (Offers & Remarks)</div>
                        
                        <div class="bg-light-box mb-4">
                            <label class="form-label-custom mb-2">Free Accessories (Click to Enable)</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="hidden" name="offer_floor_mat" value="No">
                                    <input type="checkbox" id="chk_mat" name="offer_floor_mat" value="Yes" class="check-toggle" {{ old('offer_floor_mat') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_mat" class="check-toggle-label">Floor Mat</label>
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="offer_uv_film" value="No">
                                    <input type="checkbox" id="chk_uv" name="offer_uv_film" value="Yes" class="check-toggle" {{ old('offer_uv_film') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_uv" class="check-toggle-label">UV Film</label>
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="offer_license_plate" value="No">
                                    <input type="checkbox" id="chk_plate" name="offer_license_plate" value="Yes" class="check-toggle" {{ old('offer_license_plate') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_plate" class="check-toggle-label">License Plate</label>
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="side_step" value="No">
                                    <input type="checkbox" id="chk_step" name="side_step" value="Yes" class="check-toggle" {{ old('side_step') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_step" class="check-toggle-label">Side Step</label>
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="auto_tailgate" value="No">
                                    <input type="checkbox" id="chk_tailgate" name="auto_tailgate" value="Yes" class="check-toggle" {{ old('auto_tailgate') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_tailgate" class="check-toggle-label">Auto Tailgate</label>
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="rear_spoiler" value="No">
                                    <input type="checkbox" id="chk_spoiler" name="rear_spoiler" value="Yes" class="check-toggle" {{ old('rear_spoiler') == 'Yes' ? 'checked' : '' }}>
                                    <label for="chk_spoiler" class="check-toggle-label">Rear Spoiler</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-custom text-primary-bold"><i class="fas fa-award me-1"></i> Certificate Engine Oil</label>
                                <div class="radio-pill-group mt-1">
                                    <input type="radio" id="oil_na" name="certificate_engine_oil" value="" class="radio-pill-input" checked>
                                    <label for="oil_na" class="radio-pill-label"><i class="fas fa-times me-1"></i> N/A</label>
                                    @if(isset($engineOils))
                                        @foreach ($engineOils as $item)
                                            <input type="radio" id="oil_{{ $item->id }}" name="certificate_engine_oil" value="{{ $item->id }}" class="radio-pill-input" {{ old('certificate_engine_oil') == $item->id ? 'checked' : '' }}>
                                            <label for="oil_{{ $item->id }}" class="radio-pill-label">{{ $item->name }}</label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-6">
                                <label class="form-label-custom">Pro Offer Oil</label>
                                <input type="text" name="pro_offer_oil" class="form-control" placeholder="Ex: Free 2 Times" value="{{ old('pro_offer_oil') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label-custom">Pro Oil Date</label>
                                <input type="date" name="pro_offer_oil_date" class="form-control" value="{{ old('pro_offer_oil_date') }}">
                            </div>
                            
                            <div class="col-md-6 mt-3">
                                <label class="form-label-custom">Remark SA</label>
                                <textarea name="remark_sa" class="form-control" rows="2" style="height: auto !important; resize: none;" placeholder="ការកត់សម្គាល់...">{{ old('remark_sa') }}</textarea>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label class="form-label-custom">Feedback Command</label>
                                <textarea name="feedback_command" class="form-control" rows="2" style="height: auto !important; resize: none;" placeholder="មតិយោបល់អតិថិជន...">{{ old('feedback_command') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end pb-5 mt-2">
                    <button type="submit" class="btn-submit-node btn-lg" style="background: #CE181E;">
                        <i class="fas fa-save me-2"></i> SUBMIT SALE REPORT
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div class="modal-header border-bottom" style="background: #f8fafc; padding: 20px 24px;">
                    <h5 class="fw-800 m-0 text-dark"><i class="fas fa-car me-2 text-danger"></i> Select Model Node</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <table id="datatable" class="table table-hover mb-0 align-middle">
                        <thead style="background: #f1f5f9;">
                            <tr><th class="ps-4">No</th><th>Preview</th><th>Model Name</th><th>Color Specs</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($carImages as $index => $c)
                                <tr class="select-image" style="cursor: pointer;" data-id="{{ $c->id }}" data-model="{{ $c->modelName }}" data-image="{{ url($c->image_name ? '/imageCar/'.$c->image_name : 'backend/assets/img/avatars/1.png') }}">
                                    <td class="ps-4 text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td><img src="{{ url($c->image_name ? '/imageCar/'.$c->image_name : 'backend/assets/img/avatars/1.png') }}" width="80" height="50" style="object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; padding: 2px;"></td>
                                    <td class="fw-800 text-dark">{{ $c->modelName }}</td>
                                    <td class="small fw-bold text-muted">{{ $c->colorName }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // ការពារការចុច Submit ច្រើនដង
            $('#createReportForm').on('submit', function() {
                let btns = $(this).find('button[type="submit"]');
                btns.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving Report...');
                // ដក disabled ចេញសិន ដើម្បីការពារកុំឱ្យ Browser ខ្លះមិនព្រមបញ្ជូន Form ពេល Button ត្រូវ disabled លឿនពេក
                // btns.prop('disabled', true); 
            });

            $('#datatable').DataTable({
                "pageLength": 10,
                "language": { "search": "Search models:", "lengthMenu": "Show _MENU_ models" }
            });

            // 🌟 1. បើកមុខងារ Search សម្រាប់ប្រអប់ Select ធម្មតាទាំងអស់ 🌟
            $('.select2-searchable').select2({
                width: '100%',
                placeholder: "-- សូមវាយអក្សរស្វែងរក --",
                allowClear: true
            });

            // 🌟 2. កូដ AJAX សម្រាប់ VIN/Engine ដើមរបស់លោកអ្នក (រក្សាទុកនៅដដែល) 🌟
            $('#logistic-select').select2({
                placeholder: "Search VIN / Engine Number", allowClear: true, width: '100%',
                ajax: {
                    url: "{{ url('/sale-reports/create') }}", type: "GET", dataType: 'json', delay: 250,
                    data: function(params) { return { search: params.term, type: 'logistic' }; },
                    processResults: function(data) {
                        return { results: $.map(data, function(item) { return { id: item.id, text: item.model_name + ' - ' + item.vin_number + ' - ' + item.engine_number }; }) };
                    }, cache: true
                }
            });

            let oldLogisticId = $('#old-logistic-id').val();
            if (oldLogisticId) {
                $.ajax({
                    url: "{{ url('/sale-reports/create') }}", type: "GET", data: { search: '', type: 'logistic' },
                    success: function(data) {
                        let selectedItem = data.find(item => item.id == oldLogisticId);
                        if (selectedItem) {
                            let optionText = selectedItem.model_name + ' - ' + selectedItem.vin_number + ' - ' + selectedItem.engine_number;
                            let option = new Option(optionText, selectedItem.id, true, true);
                            $('#logistic-select').append(option).trigger('change');
                        }
                    }
                });
            }

            // 🌟 3. កូដ AJAX សម្រាប់ Member Card ដើមរបស់លោកអ្នក 🌟
            $('#memberCard-select').select2({
                placeholder: "Search Member Card", allowClear: true, width: '100%',
                ajax: {
                    url: "{{ url('/sale-reports/create') }}", type: "GET", dataType: 'json', delay: 250,
                    data: function(params) { return { search: params.term, type: 'memberCard' }; },
                    processResults: function(data) {
                        return { results: $.map(data, function(item) { return { id: item.id, text: item.member_card }; }) };
                    }, cache: true
                }
            });

            let oldMemberCardId = $('#old-memberCard-id').val();
            if (oldMemberCardId) {
                $.ajax({
                    url: "{{ url('/sale-reports/create') }}", type: "GET", data: { search: '', type: 'memberCard' },
                    success: function(data) {
                        let selectedItem = data.find(item => item.id == oldMemberCardId);
                        if (selectedItem) {
                            let option = new Option(selectedItem.member_card, selectedItem.id, true, true);
                            $('#memberCard-select').append(option).trigger('change');
                        }
                    }
                });
            }
        });

        // 🌟 4. Modal Image Selector Logic (កន្លែងដែលបានកែតម្រូវ) 🌟
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('datatable');
            if(table) {
                table.addEventListener('click', function(e) {
                    const row = e.target.closest('tr.select-image');
                    if (!row) return;

                    document.getElementById('target').src = row.getAttribute('data-image');
                    document.getElementById('modelName').innerText = row.getAttribute('data-model');
                    document.getElementById('carImageId').value = row.getAttribute('data-id');
                    
                    // 🚨 បន្ថែមបន្ទាត់នេះ ដើម្បីបញ្ចូលឈ្មោះម៉ូដែលទៅកាន់ input hidden 🚨
                    document.getElementById('modelNameInput').value = row.getAttribute('data-model');

                    bootstrap.Modal.getInstance(document.getElementById('largeModal')).hide();
                });
            }
        });
    </script>
@endsection