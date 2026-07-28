@extends('layouts.homePages')

@section('content')

@php
    // 🌟 កំណត់សិទ្ធិ និងសាខារបស់ User បច្ចុប្បន្នសម្រាប់ប្រើក្នុងទំព័រនេះ
    $isAdminOrDirector = in_array((int) $user->role_id, [1, 2, 6]);
    $myBranchNameLower = strtolower($myBranch ?? '');
    
    // ឆែកមើលថាតើគាត់នៅសាខា Leap ឬសាខាផ្សេង (MG)
    $isLeapBranch = str_contains($myBranchNameLower, 'leap');
    $isMGBranch = !$isLeapBranch; // បើមិនមែន Leap ទេ ចាត់ទុកជា MG ទាំងអស់
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    :root {
        --saas-primary: #4f46e5;
        --saas-primary-hover: #4338ca;
        --saas-bg: #f8fafc;
        --saas-card-bg: #ffffff;
        --saas-border: #e2e8f0;
        --saas-text-main: #0f172a;
        --saas-text-muted: #64748b;
        --radius-xl: 20px;
        --radius-lg: 16px;
        --radius-md: 12px;
        --transition: all 0.25s ease-in-out;
    }

    body { background-color: var(--saas-bg); font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .khmer-data { font-family: 'Noto Sans Khmer', sans-serif !important; }

    /* ✨ Page Animation (Premium Staggered Effect) */
    @keyframes fadeInSaaS {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .saas-container { 
        width: 100%; max-width: 1200px; margin: 0 auto; padding: 30px 20px; 
    }
    
    .anim-item { opacity: 0; animation: fadeInSaaS 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* 🎨 SaaS Header */
    .saas-page-header {
        display: flex; justify-content: space-between; align-items: flex-end;
        margin-bottom: 30px;
    }
    .saas-page-title { font-size: 1.8rem; font-weight: 800; color: var(--saas-text-main); margin: 0; letter-spacing: -0.5px; }
    .saas-page-subtitle { font-size: 0.95rem; font-weight: 500; color: var(--saas-text-muted); margin-top: 6px; }
    
    .btn-cancel {
        background: #fff; border: 1px solid var(--saas-border); color: var(--saas-text-main);
        padding: 10px 20px; border-radius: var(--radius-md); font-weight: 700; font-size: 0.9rem;
        text-decoration: none; transition: var(--transition); display: flex; align-items: center; gap: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .btn-cancel:hover { background: var(--saas-bg); border-color: #cbd5e1; color: #000; transform: translateY(-1px); }

    /* 🎨 SaaS Cards */
    .saas-card {
        background: var(--saas-card-bg); border-radius: var(--radius-xl);
        border: 1px solid #f1f5f9; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
        padding: 32px; margin-bottom: 24px; height: 100%;
    }
    
    .saas-section-title {
        font-size: 1.1rem; font-weight: 800; color: var(--saas-text-main); margin-bottom: 25px;
        display: flex; align-items: center; gap: 12px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;
    }
    .saas-section-icon {
        background: #eef2ff; color: var(--saas-primary); width: 36px; height: 36px;
        display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 0.9rem;
    }

    /* 🎨 Minimal Info Card */
    .info-status-bar { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 30px; }
    .status-badge { 
        display: flex; align-items: center; gap: 8px; background: #fff; 
        border: 1px solid var(--saas-border); padding: 8px 16px; border-radius: 30px; 
        font-size: 0.85rem; font-weight: 700; color: var(--saas-text-main);
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .status-badge i { color: var(--saas-primary); }

    /* 🌟 Clean Form Styling - Professional Upgrade */
    .field-label { 
        font-weight: 800; color: var(--saas-text-muted); font-size: 0.75rem; 
        margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .input-wrapper { position: relative; margin-bottom: 24px; }
    
    .input-clean { 
        width: 100%; padding: 14px 16px 14px 44px !important; background: #f8fafc; 
        border: 1px solid var(--saas-border); border-radius: var(--radius-md); 
        font-weight: 600; font-size: 0.95rem; height: 50px; transition: var(--transition); color: var(--saas-text-main);
    }
    .input-clean:hover { border-color: #cbd5e1; background: #fff; }
    .input-clean:focus { background: #fff; border-color: var(--saas-primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
    
    .field-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1rem; z-index: 10; pointer-events: none; transition: 0.2s; }
    .input-clean:focus ~ .field-icon { color: var(--saas-primary); }

    /* 🎨 Select2 SaaS Fixed CSS */
    .select2-container--default .select2-selection--multiple {
        background-color: #f8fafc !important; border: 1px solid var(--saas-border) !important;
        border-radius: var(--radius-md) !important; min-height: 50px !important; 
        padding-left: 40px !important; padding-top: 6px !important; display: flex !important; align-items: center !important;
        transition: var(--transition);
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple { background-color: #fff !important; border-color: var(--saas-primary) !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important; }
    .select2-search__field { margin-top: 0 !important; font-family: inherit !important; font-size: 0.95rem !important; font-weight: 500 !important; }
    .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #fff !important; border: 1px solid var(--saas-border) !important; border-radius: 8px !important; padding: 4px 10px !important; font-weight: 700 !important; color: var(--saas-text-main) !important; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }

    .textarea-clean { 
        width: 100%; padding: 16px 16px 16px 44px !important; background: #f8fafc; 
        border: 1px solid var(--saas-border); border-radius: var(--radius-md); 
        font-weight: 500; font-size: 0.95rem; transition: var(--transition); line-height: 1.6;
    }
    .textarea-clean:focus { background: #fff; border-color: var(--saas-primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }

    .btn-submit { 
        background: linear-gradient(135deg, var(--saas-primary) 0%, var(--saas-primary-hover) 100%);
        color: #fff; padding: 16px 24px; border: none; border-radius: var(--radius-md); 
        font-weight: 800; font-size: 1.05rem; width: 100%; cursor: pointer; transition: var(--transition);
        display: flex; justify-content: center; align-items: center; gap: 10px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3); }

    /* Custom Form Grid for SaaS */
    .saas-grid { display: flex; flex-direction: column; gap: 24px; }
    @media (min-width: 992px) { 
        .saas-grid { flex-direction: row; align-items: stretch; } 
        .saas-col-side { flex: 0 0 35%; }
        .saas-col-main { flex: 0 0 65%; }
    }

    /* 🌟 Premium SweetAlert2 Custom Styling 🌟 */
    .swal2-popup.saas-popup {
        border-radius: 20px !important;
        padding: 24px !important;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1) !important;
    }
    .swal-data-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        text-align: left;
        margin: 20px 0;
    }
    .swal-data-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .swal-data-row:last-child {
        margin-bottom: 0; padding-bottom: 0; border-bottom: none;
    }
    .swal-data-col { display: flex; flex-direction: column; }
    .swal-data-col.text-end { text-align: right; align-items: flex-end; }
    .swal-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .swal-value { font-size: 0.95rem; font-weight: 800; color: #0f172a; }
    .swal-value.sale-name { color: #ef4444; }
    .swal-value.date-alert { color: #f59e0b; }
    
    .swal-badges { display: flex; gap: 8px; margin-top: 15px; }
    .swal-badge-model { background: #1e293b; color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; }
    .swal-badge-status { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }

    .swal2-textarea.saas-textarea {
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 12px !important;
        padding: 16px !important;
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
        font-size: 0.9rem !important;
        transition: 0.3s;
    }
    .swal2-textarea.saas-textarea:focus {
        background: #fff !important;
        border-color: var(--saas-primary) !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
    }
</style>

<div class="saas-container">
    
    <div class="saas-page-header anim-item">
        <div>
            <h1 class="saas-page-title">Create New Lead</h1>
            <p class="saas-page-subtitle">Add a new prospective customer to the pipeline.</p>
        </div>
        <a href="{{ route('leads.index') }}" class="btn-cancel"><i class="fas fa-arrow-left"></i> Pipeline</a>
    </div>

    @if(!in_array($user->role_id, [1, 2, 5, 6]))
        <div class="info-status-bar anim-item delay-1">
            <div class="status-badge"><i class="fas fa-user-circle"></i> <span class="khmer-data">{{ $user->full_name }}</span></div>
            <div class="status-badge"><i class="fas fa-map-marker-alt text-danger"></i> {{ $myBranch }}</div>
            <div class="status-badge"><i class="fas fa-clock text-warning"></i> <span id="js_live_date">-- -- ----</span></div>
        </div>
    @endif

    <form id="leadForm" action="{{ route('leads.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sales_person" value="{{ $user->full_name }}">
        <input type="hidden" name="branch_name" value="{{ $myBranch }}">
        <input type="hidden" name="date" id="hidden_reg_date">
        
        <input type="hidden" name="override_reason" id="hidden_override_reason" value="">

        <div class="saas-grid">
            
            {{-- 🌟 SIDE COLUMN (CONTEXT) --}}
            <div class="saas-col-side anim-item delay-2">
                <div class="saas-card">
                    <div class="saas-section-title">
                        <div class="saas-section-icon"><i class="fas fa-bullseye"></i></div>
                        Attribution Context
                    </div>

                    @if(in_array($user->role_id, [1, 2, 5, 6]))
                        <label class="field-label">Registry Date</label>
                        <div class="input-wrapper">
                            <input type="date" name="date" id="admin_reg_date" class="form-control input-clean" required>
                            <i class="fas fa-calendar-day field-icon"></i>
                        </div>
                        
                        <label class="field-label">Sales Representative</label>
                        <div class="input-wrapper">
                            <select name="sales_person" class="form-select input-clean khmer-data" required>
                                @foreach($staff as $name)
                                    <option value="{{ $name }}" {{ $user->full_name == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-user-tie field-icon"></i>
                        </div>

                        <label class="field-label">Operating Branch</label>
                        <div class="input-wrapper">
                            <select name="branch_name" class="form-select input-clean khmer-data" required>
                                @foreach($allBranches as $b)
                                    <option value="{{ $b }}" {{ $myBranch == $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-layer-group field-icon"></i>
                        </div>
                    @endif

                    <label class="field-label">Marketing Channel</label>
                    <div class="input-wrapper mb-0">
                        <select name="source" class="form-select input-clean fw-bold text-primary" required>
                            <option value="" disabled selected>Identify Channel...</option>
                            <optgroup label="Digital Channels">
                                <option value="Facebook" selected>Facebook</option>
                                <option value="TikTok">TikTok</option>
                                <option value="Telegram">Telegram</option>
                                <option value="YouTube">YouTube</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Official Website">Official Website</option>
                            </optgroup>
                            <optgroup label="Physical / Offline">
                                <option value="Showroom Walk-in">Showroom Walk-in</option>
                                <option value="Event/Exhibition">Event/Exhibition</option>
                                <option value="Roadshow">Roadshow</option>
                            </optgroup>
                            <option value="Customer Referral">Customer Referral</option>
                        </select>
                        <i class="fas fa-share-alt field-icon"></i>
                    </div>
                </div>
            </div>

            {{-- 🌟 MAIN COLUMN (LEAD INFO) --}}
            <div class="saas-col-main anim-item delay-3">
                <div class="saas-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    
                    <div>
                        <div class="saas-section-title">
                            <div class="saas-section-icon"><i class="fas fa-id-card"></i></div>
                            Lead Information
                        </div>

                        <div class="row gx-4">
                            <div class="col-md-6">
                                <label class="field-label">Primary Phone <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" name="contact_info" id="contact_info" class="form-control input-clean fw-bold text-primary fs-5" placeholder="0XX XXX XXX" required autocomplete="off">
                                    <i class="fas fa-phone-alt field-icon" id="phoneIcon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Client Full Name <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <input type="text" name="customer_name" class="form-control input-clean khmer-data" placeholder="Enter full name..." required>
                                    <i class="fas fa-user field-icon"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row gx-4">
                            <div class="col-md-6">
                                <label class="field-label">Province / Location <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <select name="location" id="location" class="form-select input-clean fw-bold" required>
                                        <option value="" disabled selected>Select Province...</option>
                                        <option value="Phnom Penh">Phnom Penh</option>
                                        <option value="Banteay Meanchey">Banteay Meanchey</option>
                                        <option value="Battambang">Battambang</option>
                                        <option value="Kampong Cham">Kampong Cham</option>
                                        <option value="Kampong Chhnang">Kampong Chhnang</option>
                                        <option value="Kampong Speu">Kampong Speu</option>
                                        <option value="Kampong Thom">Kampong Thom</option>
                                        <option value="Kampot">Kampot</option>
                                        <option value="Kandal">Kandal</option>
                                        <option value="Kep">Kep</option>
                                        <option value="Koh Kong">Koh Kong</option>
                                        <option value="Kratie">Kratie</option>
                                        <option value="Mondulkiri">Mondulkiri</option>
                                        <option value="Oddar Meanchey">Oddar Meanchey</option>
                                        <option value="Pailin">Pailin</option>
                                        <option value="Preah Sihanouk">Preah Sihanouk</option>
                                        <option value="Preah Vihear">Preah Vihear</option>
                                        <option value="Prey Veng">Prey Veng</option>
                                        <option value="Pursat">Pursat</option>
                                        <option value="Ratanakiri">Ratanakiri</option>
                                        <option value="Siem Reap">Siem Reap</option>
                                        <option value="Stung Treng">Stung Treng</option>
                                        <option value="Svay Rieng">Svay Rieng</option>
                                        <option value="Takeo">Takeo</option>
                                        <option value="Tboung Khmum">Tboung Khmum</option>
                                    </select>
                                    <i class="fas fa-map-marker-alt field-icon text-danger"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Interested Models <span class="text-muted fw-normal" style="text-transform: none;">(Max 5)</span> <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <select name="model[]" id="model_select" class="form-select" multiple="multiple" required>
                                        
                                        @if($isAdminOrDirector || $isMGBranch)
                                            <optgroup label="MG Motors">
                                                @foreach(['MG ZS', 'MG HS', 'MG5', 'MG7', 'MG RX5', 'MG ONE', 'MG4 EV', 'MG Cyberster'] as $m)
                                                    <option value="{{ $m }}">{{ $m }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                        @if($isAdminOrDirector || $isLeapBranch)
                                            <optgroup label="Leapmotor">
                                                @foreach(['Leapmotor T03', 'Leapmotor C10', 'Leapmotor C11', 'Leapmotor C01', 'Leapmotor C16'] as $m)
                                                    <option value="{{ $m }}">{{ $m }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                    </select>
                                    <i class="fas fa-car field-icon"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row gx-4">
                            <div class="col-md-6">
                                <label class="field-label">Pipeline Stage</label>
                                <div class="input-wrapper">
                                    <select name="status" class="form-select input-clean fw-bold" style="color: var(--saas-primary);" required>
                                        <option value="Prospecting" selected>Prospecting</option>
                                        <option value="Appointment">Appointment</option>
                                        <option value="Meet & Greet">Meet & Greet</option>
                                        <option value="Consulting ( Q & A )">Consulting ( Q & A )</option>
                                        <option value="Double Open">Double Open</option>
                                        <option value="Presentation">Presentation</option>
                                        <option value="Test Drive">Test Drive</option>
                                        <option value="Trail Close">Trail Close</option>
                                        <option value="Negotiation">Negotiation</option>
                                        <option value="Double Close">Double Close</option>
                                        <option value="Sales ( contract of sales )">Contract of Sales</option>
                                        <option value="Delivery">Delivery</option>
                                    </select>
                                    <i class="fas fa-tasks field-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Next Scheduled Action</label>
                                <div class="input-wrapper">
                                    <input type="date" name="remind_date" id="remind_date_input" class="form-control input-clean fw-bold text-success" required>
                                    <i class="fas fa-calendar-check field-icon"></i>
                                </div>
                            </div>
                        </div>

                        <label class="field-label">Internal Briefing (Notes)</label>
                        <div class="input-wrapper">
                            <textarea name="comment" class="form-control textarea-clean khmer-data" rows="3" placeholder="Add specific requirements, customer feedback, or follow-up notes here..."></textarea>
                            <i class="fas fa-align-left field-icon" style="top: 26px; transform: none;"></i>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-submit" id="btnMainSubmit">
                            <i class="fas fa-check-circle fa-lg"></i> Create Lead Record
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // 🌟 1. JavaScript Live Date Fix
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
    const dbDate = `${year}-${month}-${day}`; 
    const viewDate = `${day} ${monthNames[now.getMonth()]} ${year}`;

    $('#admin_reg_date').val(dbDate);
    $('#hidden_reg_date').val(dbDate);
    $('#js_live_date').text(viewDate);
    $('#remind_date_input').val(dbDate);

    // 🌟 2. Success Popup
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Successfully Created!', text: "{{ session('success') }}", confirmButtonColor: '#4f46e5', timer: 3000 });
    @endif

    // 🌟 3. Select2 Init
    $('#model_select').select2({ placeholder: "  Select interested models...", maximumSelectionLength: 5, width: '100%' });

    // 🌟 4. Phone Formatting & AJAX Duplicate Check with "Lead Expiration"
    let typingTimer;
    let isPhoneValid = true; // ដើម្បីបញ្ជាការ Submit

    $('#contact_info').on('input', function() {
        let val = $(this).val().replace(/\D/g, ''); 
        if (val.length > 3 && val.length <= 6) val = val.replace(/(\d{3})(\d+)/, '$1 $2');
        else if (val.length > 6) val = val.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        $(this).val(val);

        clearTimeout(typingTimer);
        let rawPhone = val.replace(/\s/g, '');
        
        // reset validation status
        isPhoneValid = true;
        $('#hidden_override_reason').val(''); 
        
        if (rawPhone.length >= 8) {
            $('#phoneIcon').attr('class', 'fas fa-spinner fa-spin field-icon').css('color', '#f59e0b');
            typingTimer = setTimeout(() => { checkDuplicate(rawPhone); }, 600);
        } else {
            $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#94a3b8');
        }
    });

    function checkDuplicate(phone) {
        $.ajax({
            url: "/leads/check-duplicate",
            type: "POST",
            data: { 
                phone: phone, 
                _token: "{{ csrf_token() }}" 
            },
            success: function(res) {
                if (res.exists) {
                    $('#phoneIcon').attr('class', 'fas fa-exclamation-triangle field-icon').css('color', '#ef4444');
                    
                    // 🔴 ករណីទី ១: Lead នៅសកម្ម (មិនទាន់ដល់ថ្ងៃអនុញ្ញាតឱ្យយក)
                    if (!res.allow_takeover) {
                        isPhoneValid = false; 
                        
                        Swal.fire({
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-eraser me-2"></i> CLEAR & RETRY',
                            confirmButtonColor: '#0f172a',
                            width: '480px',
                            allowOutsideClick: false,
                            customClass: { popup: 'saas-popup', confirmButton: 'btn btn-dark fw-bold px-4 py-2 rounded-3' },
                            html: `
                                <div style="text-align: center; margin-top: 10px;">
                                    <div style="width: 64px; height: 64px; background: #fef2f2; color: #ef4444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 16px; box-shadow: 0 0 0 8px #fee2e2;">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <h3 style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #0f172a; font-size: 1.4rem; margin: 0; text-transform: uppercase;">Duplicate Lead</h3>
                                    <p style="color: #ef4444; font-size: 0.85rem; margin-top: 6px; font-weight: 700;">លេខទូរស័ព្ទនេះនៅសកម្មក្នុងប្រព័ន្ធនៅឡើយ!</p>
                                </div>
                                
                                <div class="swal-data-box khmer-data">
                                    <div class="swal-data-row">
                                        <div class="swal-data-col">
                                            <span class="swal-label">Customer</span>
                                            <span class="swal-value"><i class="fas fa-user-circle text-primary opacity-50 me-1"></i> ${res.customer_name}</span>
                                        </div>
                                        <div class="swal-data-col text-end">
                                            <span class="swal-label">Last Updated</span>
                                            <span class="swal-value date-alert">${res.last_update} <br><small style="font-size:0.7rem;">(${res.days_inactive} ថ្ងៃមុន)</small></span>
                                        </div>
                                    </div>
                                    <div class="swal-data-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                                        <div class="swal-data-col">
                                            <span class="swal-label">Sales Person</span>
                                            <span class="swal-value sale-name"><i class="fas fa-user-tie opacity-50 me-1"></i> ${res.sales_person}</span>
                                        </div>
                                        <div class="swal-data-col text-end">
                                            <span class="swal-label">Branch</span>
                                            <span class="swal-value" style="color: #475569;"><i class="fas fa-map-marker-alt text-success opacity-50 me-1"></i> ${res.branch}</span>
                                        </div>
                                    </div>
                                    <div class="swal-badges">
                                        <span class="swal-badge-model">${res.model}</span>
                                        <span class="swal-badge-status"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> ${res.status}</span>
                                    </div>
                                </div>
                                
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 15px; font-weight: 500; text-align: center;">
                                    <i class="fas fa-info-circle text-primary"></i> អ្នកអាចទាញយកលេខអតិថិជនចាស់បាន លុះត្រាតែលេខនោះត្រូវបានទុកចោលមិនមានការអាប់ដេតលើសពី 15 ថ្ងៃ។
                                </div>
                            `
                        }).then(() => { 
                            $('#contact_info').val('').focus(); 
                            $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#94a3b8');
                            isPhoneValid = true;
                        });
                    } 
                    
                    // 🟢 ករណីទី ២: Lead ទុកចោលយូរ (អនុញ្ញាតអោយយកមកកាន់កាប់បន្ត)
                    else {
                        isPhoneValid = false; 

                        Swal.fire({
                            width: '500px',
                            customClass: { 
                                popup: 'saas-popup', 
                                input: 'saas-textarea',
                                confirmButton: 'btn btn-primary fw-bold px-4 py-2 rounded-3 me-2',
                                cancelButton: 'btn btn-light border fw-bold px-4 py-2 rounded-3 text-dark'
                            },
                            html: `
                                <div style="text-align: center; margin-top: 10px;">
                                    <div style="width: 64px; height: 64px; background: #e0e7ff; color: #4f46e5; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 16px; box-shadow: 0 0 0 8px #e0e7ff;">
                                        <i class="fas fa-unlock-alt"></i>
                                    </div>
                                    <h3 style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; color: #0f172a; font-size: 1.4rem; margin: 0; text-transform: uppercase;">INACTIVE LEAD FOUND</h3>
                                    <p style="color: #4f46e5; font-size: 0.85rem; margin-top: 6px; font-weight: 700;">លេខនេះត្រូវបានទុកចោល <b>${res.days_inactive} ថ្ងៃ</b> ហើយ!</p>
                                </div>

                                <div class="swal-data-box khmer-data">
                                    <div class="swal-data-row">
                                        <div class="swal-data-col">
                                            <span class="swal-label">Customer</span>
                                            <span class="swal-value"><i class="fas fa-user-circle text-primary opacity-50 me-1"></i> ${res.customer_name}</span>
                                        </div>
                                        <div class="swal-data-col text-end">
                                            <span class="swal-label">Last Updated</span>
                                            <span class="swal-value date-alert">${res.last_update}</span>
                                        </div>
                                    </div>
                                    <div class="swal-data-row" style="border-bottom:none; margin-bottom:0; padding-bottom:0;">
                                        <div class="swal-data-col">
                                            <span class="swal-label">Previous Sales</span>
                                            <span class="swal-value" style="color: #64748b;"><i class="fas fa-user-tie opacity-50 me-1"></i> ${res.sales_person}</span>
                                        </div>
                                    </div>
                                </div>

                                <p style="font-size: 0.85rem; color: #475569; font-weight: 600; text-align: left; margin-bottom: 10px;">
                                    ដើម្បីទាញយកអតិថិជននេះមកកាន់កាប់ សូមបញ្ជាក់ហេតុផលខាងក្រោម៖
                                </p>
                            `,
                            input: 'textarea',
                            inputPlaceholder: 'បញ្ជាក់ហេតុផលរបស់អ្នកទីនេះ (ឧ. អតិថិជនឆាតមកផេកថ្មី...)',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-check-circle me-1"></i> យល់ព្រម & ទាញយក',
                            cancelButtonText: 'បោះបង់ (Clear)',
                            allowOutsideClick: false,
                            buttonsStyling: false,
                            preConfirm: (reason) => {
                                if (!reason || reason.trim() === '') {
                                    Swal.showValidationMessage('សូមបញ្ជាក់ហេតុផលជាដាច់ខាត!')
                                }
                                return reason;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#hidden_override_reason').val(result.value);
                                isPhoneValid = true; 
                                $('#phoneIcon').attr('class', 'fas fa-check-circle field-icon').css('color', '#10b981');
                                
                                Swal.fire({
                                    toast: true, position: 'top-end', icon: 'success',
                                    title: 'អ្នកអាចបន្តបំពេញទិន្នន័យបាន។',
                                    showConfirmButton: false, timer: 3000
                                });
                            } else {
                                $('#contact_info').val('').focus();
                                $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#94a3b8');
                                isPhoneValid = true;
                            }
                        });
                    }
                } else {
                    $('#phoneIcon').attr('class', 'fas fa-check-circle field-icon').css('color', '#10b981');
                    isPhoneValid = true;
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#94a3b8');
            }
        });
    }

    // 🌟 5. Form Submit Validation & Loading
    $('#leadForm').on('submit', function(e) {
        if (!isPhoneValid) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Action Required', text: 'Please resolve the phone number issue first.', confirmButtonColor: '#0f172a' });
            return false;
        }
        Swal.fire({ title: 'Saving Lead...', didOpen: () => { Swal.showLoading(); }, allowOutsideClick: false });
    });
});
</script>

@endsection