@extends('layouts.homePages')

@section('content')
@php
    $user = auth('users')->user();
    
    // 🌟 រៀបចំទិន្នន័យ Offers ចាស់ៗដែលធ្លាប់ Save
    $existingOffers = is_array($contract->offers) ? $contract->offers : (json_decode($contract->offers, true) ?: []);
    if (empty($existingOffers)) { $existingOffers = []; }
    
    $isAdmin = in_array($user->role_id, [1, 2, 6]);
    $isManager = in_array($user->role_id, [3, 4, 5, 7, 8]);
    $canEditStaff = $isAdmin || $isManager;

    $carModels = ['Forland Cargo Truck', 'Maxus D60', 'Maxus G50', 'Maxus T60', 'Maxus T90', 'Maxus Truck S50-D19', 'Maxus V80', 'MG 4 EV Luxury', 'MG 4 EV STD', 'MG 7 1.5 4 cly', 'MG 7 2.0 4 cly', 'MG Cyberster', 'MG G50', 'MG GT Deluxe', 'MG HS', 'MG Marvel R', 'MG RX5', 'MG RX5 LUX Sport', 'MG RX8', 'MG RX9', 'MG ZS', 'Mifi 7', 'Mifi 9'];
    $offersList = ['1 Year Full Service/3times', 'Auto Tailgate', 'Floor Mats', 'Long Plate Number', 'Normal Plate Number', 'Rear Spoiler', 'Side Step', 'UV Film On Windows', 'Vouchers', 'Warranty 3 Years or 100,000 KM', 'Warranty 5 Years or 100,000 KM', 'Warranty 5 Years or 150,000 KM'];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* 🎨 Premium Compact Theme (Fit Screen) */
    :root {
        --primary: #3b82f6; 
        --primary-hover: #2563eb;
        --secondary: #f8fafc;
        --accent: #10b981; 
        --danger: #ef4444; 
        --warning: #f59e0b;
        --text-dark: #1e293b;
        --text-gray: #64748b;
        --border-color: #e2e8f0;
        --bg-body: #f1f5f9; 
        --card-bg: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --shadow-md: 0 10px 25px -5px rgba(0,0,0,0.05);
        --radius-lg: 16px;
        --radius-md: 8px;
    }

    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: var(--bg-body); color: var(--text-dark); }
    .container-premium { max-width: 1280px; margin: 0 auto; padding: 2rem 1.5rem; }

    /* 🌟 Header Section */
    .header-wrapper { display: flex; align-items: center; margin-bottom: 2rem; }
    .btn-back-modern {
        display: flex; align-items: center; justify-content: center;
        width: 45px; height: 45px; background: white; border: 1px solid var(--border-color);
        border-radius: 12px; color: var(--text-dark); font-size: 1.2rem;
        box-shadow: var(--shadow-sm); transition: 0.3s; margin-right: 1.5rem; text-decoration: none !important;
    }
    .btn-back-modern:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateX(-3px); }
    .page-title { font-size: 22px; font-weight: 800; margin: 0; color: var(--text-dark); letter-spacing: -0.5px; }
    .page-subtitle { font-size: 12px; font-weight: 600; color: var(--warning); margin: 0; text-transform: uppercase; letter-spacing: 1px; }

    /* 🌟 Card Design */
    .modern-card { background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid rgba(226,232,240,0.8); padding: 2rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-md); }
    .card-header-title { font-size: 16px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 12px; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--bg-body); }
    .icon-box { width: 36px; height: 36px; border-radius: 10px; background: #fffbeb; color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 16px; }

    /* 🌟 Form Inputs */
    .form-group { margin-bottom: 1.2rem; }
    .form-label { font-weight: 700; font-size: 11px; color: var(--text-gray); margin-bottom: 6px; display: block; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control-modern { width: 100%; height: 45px; border-radius: var(--radius-md); border: 1.5px solid var(--border-color); background-color: var(--secondary); padding: 0 1rem; font-size: 14px; font-weight: 600; color: var(--text-dark); transition: 0.3s; }
    .form-control-modern:focus { border-color: var(--warning); background-color: #fff; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); outline: none; }
    textarea.form-control-modern { padding: 1rem; height: auto !important; }

    /* Select2 Override */
    .select2-container--default .select2-selection--single { height: 45px !important; border-radius: var(--radius-md) !important; border: 1.5px solid var(--border-color) !important; background-color: var(--secondary) !important; display: flex !important; align-items: center !important; padding-left: 6px !important; font-weight: 600; font-size: 14px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 43px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: var(--warning) !important; background-color: #fff !important; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1) !important; }

    /* 🌟 Upload Zone (Fixed Image Stretch Issue) */
    .upload-zone { 
        border: 2px dashed #cbd5e1; border-radius: 12px; padding: 1.5rem 1rem; 
        text-align: center; background: #f8fafc; cursor: pointer; transition: 0.3s; margin-bottom: 1.5rem;
        overflow: hidden; /* 🌟 ការពារកុំឱ្យរូបភាពលៀនចេញក្រៅ */
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 160px;
    }
    .upload-zone:hover { border-color: var(--warning); background: #fffbeb; }
    .upload-icon { font-size: 32px; color: #94a3b8; margin-bottom: 10px; transition: 0.3s; }
    .upload-zone:hover .upload-icon { color: var(--warning); transform: translateY(-3px); }
    
    #id-preview { 
        max-width: 100% !important; 
        max-height: 180px !important; /* 🌟 Lock កម្ពស់រូបភាពកុំឱ្យធំពេក */
        width: auto;
        object-fit: contain; 
        border-radius: 8px; 
    }

    /* 🌟 Checkboxes */
    .offer-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 10px; margin-bottom: 1rem; }
    .offer-item { background: var(--secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 12px; display: flex; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; }
    .offer-item:hover { border-color: var(--warning); background: #fffbeb; }
    .offer-item input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--warning); }
    .offer-item label { margin: 0; font-weight: 600; color: var(--text-dark); cursor: pointer; font-size: 12px; width: 100%; }

    /* 🌟 Financial Summary */
    .finance-summary { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 1.8rem; text-align: center; color: white; margin-bottom: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.3); }
    .amount-label { font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 2px; margin-bottom: 5px; }
    .amount-display { font-size: 38px; font-weight: 900; margin: 0; letter-spacing: -1px; }

    /* 🌟 Buttons & Actions */
    .btn-submit { background: var(--warning); color: white; border: none; padding: 14px; border-radius: var(--radius-md); font-weight: 800; font-size: 15px; width: 100%; transition: 0.3s ease; display: flex; justify-content: center; align-items: center; gap: 8px; }
    .btn-submit:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3); }

    .offer-row { display: flex; gap: 10px; margin-bottom: 10px; }
    .btn-action { width: 45px; height: 45px; border-radius: var(--radius-md); border: none; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; transition: 0.2s; flex-shrink: 0; }
    .btn-add { background: var(--warning); } .btn-add:hover { background: #d97706; }
    .btn-remove { background: var(--danger); } .btn-remove:hover { background: #b91c1c; }

    .sticky-finance { position: sticky; top: 1.5rem; z-index: 10; }
</style>

<div class="container-premium text-start">
    
    <div class="header-wrapper">
        <a href="{{ route('contracts.index') }}" class="btn-back-modern" title="Back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title">កែប្រែទិន្នន័យកិច្ចសន្យា</h1>
            <p class="page-subtitle"><i class="fas fa-edit me-1"></i> Edit Registry Record</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm p-3 mb-4">
            <ul class="mb-0 fw-bold small">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <form action="{{ route('contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-xl-8 col-lg-7">
                
                <div class="modern-card">
                    <h3 class="card-header-title">
                        <div class="icon-box"><i class="fas fa-user-edit"></i></div>
                        ព័ត៌មានអតិថិជន (Customer Profile)
                    </h3>
                    
                    <div class="upload-zone" id="upload-zone">
                        <div id="upload-text" style="{{ $contract->customer_id_card ? 'display: none;' : 'display: block;' }}">
                            <i class="fas fa-camera upload-icon"></i>
                            <h6 class="fw-800 text-dark mb-1" style="font-size: 14px;">ផ្លាស់ប្តូរឯកសារយោង (បើមាន)</h6>
                            <p class="small text-muted mb-0">ចុចទីនេះបើចង់ Upload អត្តសញ្ញាណប័ណ្ណថ្មីជំនួស</p>
                        </div>

                        <input type="file" name="customer_id_card" id="id_image_input" class="d-none" accept="image/*">
                        
                        @if($contract->customer_id_card)
                            <img id="id-preview" src="{{ asset('storage/' . $contract->customer_id_card) }}" style="display: block;">
                        @else
                            <img id="id-preview" style="display: none;">
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" value="{{ $contract->customer_name }}" class="form-control-modern" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">ឈ្មោះ (Khmer)</label>
                            <input type="text" name="customer_name_kh" value="{{ $contract->customer_name_kh ?? '' }}" class="form-control-modern">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" value="{{ $contract->phone_number }}" class="form-control-modern" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">National ID / Passport</label>
                            <input type="text" name="id_card_number" value="{{ $contract->id_card_number }}" class="form-control-modern">
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label">Member Card Assignment</label>
                            <select name="membership_number" class="form-control-modern select2-js">
                                <option value="{{ $contract->membership_number }}">{{ $contract->membership_number }} (Current)</option>
                                @foreach($availableCards ?? [] as $card) <option value="{{ $card->member_card }}">{{ $card->member_card }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="form-label">Address / អាសយដ្ឋាន</label>
                            <textarea name="address" rows="2" class="form-control-modern">{{ $contract->address }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modern-card">
                    <h3 class="card-header-title">
                        <div class="icon-box"><i class="fas fa-car-side"></i></div>
                        ប្រតិបត្តិការរថយន្ត (Vehicle Specs)
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6 form-group">
                            <label class="form-label">Branch / សាខា</label>
                            <select name="branch_name" class="form-control-modern select2-js" @disabled(!$isAdmin)>
                                @foreach($allBranches ?? [] as $b) <option value="{{ $b }}" @selected($contract->branch_name == $b)>{{ $b }}</option> @endforeach
                            </select>
                            @if(!$isAdmin) <input type="hidden" name="branch_name" value="{{ $contract->branch_name }}"> @endif
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Salesperson</label>
                            <select name="sale_person" class="form-control-modern select2-js" @disabled(!$canEditStaff)>
                                @foreach($staff ?? [] as $s) <option value="{{ $s }}" @selected($contract->sale_person == $s)>{{ $s }}</option> @endforeach
                            </select>
                            @if(!$canEditStaff) <input type="hidden" name="sale_person" value="{{ $contract->sale_person }}"> @endif
                        </div>
                        
                        <div class="col-12"><hr style="border-color: var(--border-color); margin: 0.5rem 0 1rem;"></div>

                        <div class="col-md-4 form-group">
                            <label class="form-label text-primary">Contract ID <span class="text-danger">*</span></label>
                            <input type="text" name="contract_number" value="{{ $contract->contract_number }}" class="form-control-modern text-primary" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label text-primary">Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" value="{{ \Carbon\Carbon::parse($contract->order_date)->format('Y-m-d') }}" class="form-control-modern" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">Source / ប្រភព</label>
                            <select name="source_id" class="form-control-modern select2-js">
                                <option value="">-- រើសប្រភព --</option>
                                <option value="Facebook" @selected($contract->source_id == 'Facebook')>Facebook</option>
                                <option value="TikTok" @selected($contract->source_id == 'TikTok')>TikTok</option>
                                <option value="Direct Walk-in" @selected($contract->source_id == 'Direct Walk-in')>Direct Walk-in</option>
                                <option value="Referral" @selected($contract->source_id == 'Referral')>Referral</option>
                            </select>
                        </div>
                        
                        <div class="col-md-8 form-group">
                            <label class="form-label">Vehicle Model <span class="text-danger">*</span></label>
                            <select name="model" class="form-control-modern select2-js" required>
                                <option value="" disabled>-- ជ្រើសរើសម៉ូដែល --</option>
                                @foreach($carModels as $model)
                                    <option value="{{ $model }}" @selected($contract->model == $model)>{{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">Qty / ចំនួន</label>
                            <input type="number" id="qty" name="qty" class="form-control-modern text-center fw-bold calc-val" value="{{ $contract->qty ?? 1 }}" min="1" required>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label class="form-label">Color / ពណ៌</label>
                            <input type="text" name="color" value="{{ $contract->color ?? '' }}" class="form-control-modern" placeholder="ក្រៅ / ក្នុង">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">VIN Number</label>
                            <input type="text" name="vin_number" value="{{ $contract->vin_number }}" class="form-control-modern font-monospace">
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label">Engine No.</label>
                            <input type="text" name="engine_number" value="{{ $contract->engine_number ?? '' }}" class="form-control-modern font-monospace">
                        </div>
                        
                        <div class="col-12 form-group mb-0 mt-3">
                            <label class="form-label">Campaign Offers / កាដូថែមជូន</label>
                            <div class="offer-grid">
                                @foreach($offersList as $index => $offer)
                                    <label class="offer-item" for="offer_{{ $index }}">
                                        <input type="checkbox" id="offer_{{ $index }}" name="offers[]" value="{{ $offer }}" 
                                            @checked(in_array($offer, $existingOffers))>
                                        <span>{{ $offer }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <label class="form-label mt-3">ថែមអ្វីផ្សេងទៀត (Other Accessories)</label>
                            <div id="offers-list">
                                @php
                                    // រកមើល Offer ដែលភ្ញៀវវាយបញ្ចូលដោយដៃ
                                    $customOffers = array_diff($existingOffers, $offersList);
                                @endphp

                                @forelse($customOffers as $index => $customOffer)
                                    <div class="offer-row">
                                        <input type="text" name="offers[]" value="{{ $customOffer }}" class="form-control-modern" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
                                        <button type="button" class="btn-action btn-remove shadow-sm" onclick="$(this).parent().remove()"><i class="fas fa-minus"></i></button>
                                    </div>
                                @empty
                                    <div class="offer-row">
                                        <input type="text" name="offers[]" class="form-control-modern" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
                                        <button type="button" class="btn-action btn-add shadow-sm" onclick="addOfferField()"><i class="fas fa-plus"></i></button>
                                    </div>
                                @endforelse
                                
                                @if(count($customOffers) > 0)
                                    <div class="offer-row" id="add-btn-wrapper">
                                        <input type="text" name="offers[]" class="form-control-modern" placeholder="បន្ថែមថ្មីទៀត...">
                                        <button type="button" class="btn-action btn-add shadow-sm" onclick="addOfferField()"><i class="fas fa-plus"></i></button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="modern-card sticky-finance">
                    <h3 class="card-header-title">
                        <div class="icon-box"><i class="fas fa-wallet"></i></div>
                        ហិរញ្ញវត្ថុ (Settlement)
                    </h3>
                    
                    <div class="finance-summary">
                        <p class="amount-label">BALANCE DUE</p>
                        @php 
                            $initBal = ($contract->qty ?? 1) * ($contract->unit_price ?? $contract->sub_total) - ($contract->discount ?? 0) - ($contract->deposit ?? 0);
                            $initBal = $initBal < 0 ? 0 : $initBal;
                        @endphp
                        <p class="amount-display">$ <span id="span_total">{{ number_format($initBal, 2) }}</span></p>
                        <input type="hidden" id="balance" name="balance" value="{{ $initBal }}">
                    </div>

                    <div class="row g-3">
                        <div class="col-6 form-group">
                            <label class="form-label">Unit Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="unit_price" name="unit_price" value="{{ $contract->unit_price ?? $contract->sub_total }}" class="form-control-modern calc-val" required>
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label text-muted">Sub Total / សរុប</label>
                            <input type="text" id="sub_total_display" class="form-control-modern text-muted bg-light border-0" value="{{ number_format(($contract->qty ?? 1) * ($contract->unit_price ?? $contract->sub_total), 2) }}" readonly>
                            <input type="hidden" id="sub_total" name="sub_total" value="{{ ($contract->qty ?? 1) * ($contract->unit_price ?? $contract->sub_total) }}">
                        </div>

                        <div class="col-6 form-group">
                            <label class="form-label text-danger">Discount ($)</label>
                            <input type="number" step="0.01" id="discount" name="discount" value="{{ $contract->discount ?? 0 }}" class="form-control-modern calc-val text-danger">
                        </div>
                        <div class="col-6 form-group">
                            <label class="form-label text-success">Deposit Paid ($)</label>
                            <input type="number" step="0.01" id="deposit" name="deposit" value="{{ $contract->deposit ?? 0 }}" class="form-control-modern calc-val text-success">
                        </div>
                        
                        <div class="col-12 form-group">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control-modern select2-js" required>
                                <option value="Cash" @selected($contract->payment_type == 'Cash')>Cash (សាច់ប្រាក់)</option>
                                <option value="Bank Transfer" @selected($contract->payment_type == 'Bank Transfer' || $contract->payment_type == 'Bank Loan')>Bank Transfer / Loan</option>
                                <option value="Installment" @selected($contract->payment_type == 'Installment')>Installment (រំលស់)</option>
                            </select>
                        </div>

                        @php 
                            $isBank = in_array($contract->payment_type, ['Bank Transfer', 'Bank Loan']);
                        @endphp
                        <div class="col-12 form-group" id="bank_name_wrapper" style="display: {{ $isBank ? 'block' : 'none' }};">
                            <label class="form-label text-primary">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ $contract->bank_name ?? '' }}" class="form-control-modern border-primary" placeholder="e.g. ABA, Campu Bank...">
                        </div>

                        <div class="col-12 form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control-modern text-primary fw-bold">
                                <option value="Booking" @selected($contract->status == 'Booking')>BOOKING / កក់ទុក</option>
                                <option value="Payoff" @selected($contract->status == 'Payoff')>PAYOFF / ទូទាត់ផ្ដាច់</option>
                            </select>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> រក្សាទុកការកែប្រែ
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
    $('.select2-js').select2({ width: '100%' });

    $('#payment_type').on('change', function() {
        if ($(this).val() === 'Bank Transfer') {
            $('#bank_name_wrapper').slideDown();
        } else {
            $('#bank_name_wrapper').slideUp();
            $('input[name="bank_name"]').val('');
        }
    });

    $('#upload-zone').on('click', function(e) {
        if (e.target.id !== 'id_image_input') $('#id_image_input').click();
    });
    $('#id_image_input').on('click', function(e) { e.stopPropagation(); });

    // 🌟 បង្ហាញរូបភាពថ្មី និងលាក់អក្សរពេលរើសរួច
    $('#id_image_input').change(function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = (e) => { 
                $('#id-preview').attr('src', e.target.result).show(); 
                $('#upload-text').hide();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

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

        $('#span_total').text(balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#balance').val(balance);
    });
});

function addOfferField() {
    $(`
        <div class="offer-row">
            <input type="text" name="offers[]" class="form-control-modern" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
            <button type="button" class="btn-action btn-remove shadow-sm" onclick="$(this).parent().remove()"><i class="fas fa-minus"></i></button>
        </div>
    `).insertBefore('#add-btn-wrapper');
    
    if($('#add-btn-wrapper').length === 0) {
        $('#offers-list').append(`
            <div class="offer-row">
                <input type="text" name="offers[]" class="form-control-modern" placeholder="វាយឈ្មោះកាដូផ្សេងៗ...">
                <button type="button" class="btn-action btn-remove shadow-sm" onclick="$(this).parent().remove()"><i class="fas fa-minus"></i></button>
            </div>
        `);
    }
}

$('#editForm').on('submit', function() {
    Swal.fire({ 
        title: 'កំពុងអាប់ដេតទិន្នន័យ...', 
        text: 'សូមរង់ចាំបន្តិច',
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); } 
    });
});
</script>
@endsection