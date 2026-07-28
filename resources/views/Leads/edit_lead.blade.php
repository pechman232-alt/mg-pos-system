@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@php
    $currentUser = auth('users')->user(); 
    $isAdminOrDirector = $currentUser ? in_array($currentUser->role_id, [1, 2, 6]) : false;

    $selectedModels = explode(', ', $lead->model);
    
    // 🌟 Logic សម្រាប់បែងចែក MG និង Leapmotor
    $myBranchNameLower = strtolower($currentUser ? \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name') : '');
    $isLeapBranch = str_contains($myBranchNameLower, 'leap');
    $isMGBranch = !$isLeapBranch;
@endphp

<style>
    :root {
        --c-primary: #4f46e5;
        --c-slate-50: #f8fafc;
        --c-slate-200: #e2e8f0;
        --c-slate-700: #334155;
        --c-slate-900: #0f172a;
        --radius-lg: 20px;
        --radius-md: 12px;
    }

    body { background-color: var(--c-slate-50); font-family: 'Plus Jakarta Sans', sans-serif !important; }
    .container-full-width { width: 100%; padding: 30px 40px; }
    .card-clean { border-radius: var(--radius-lg); border: 1px solid var(--c-slate-200); background: #fff; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
    .card-header-subtle { padding: 30px 40px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .section-title { font-weight: 800; color: var(--c-primary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    .field-label { font-weight: 700; color: var(--c-slate-700); font-size: 0.85rem; margin-bottom: 8px; display: block; }
    .input-wrapper { position: relative; margin-bottom: 22px; }
    .input-clean { width: 100%; padding: 12px 16px 12px 46px !important; background: var(--c-slate-50); border: 1px solid var(--c-slate-200); border-radius: var(--radius-md); font-weight: 600; font-size: 0.95rem; height: 50px; color: var(--c-slate-900); }
    .input-clean:focus { background: #fff; border-color: var(--c-primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
    .field-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; z-index: 10; pointer-events: none; }

    /* SELECT2 CUSTOM */
    .select2-container--default .select2-selection--multiple { background-color: var(--c-slate-50) !important; border: 1px solid var(--c-slate-200) !important; border-radius: var(--radius-md) !important; min-height: 50px !important; padding-left: 40px !important; display: flex; align-items: center; }
    .select2-container--default.select2-container--focus .select2-selection--multiple { background-color: #fff !important; border-color: var(--c-primary) !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important; }
    .select2-container--default .select2-selection__choice { background-color: var(--c-primary) !important; color: #fff !important; border: none !important; border-radius: 6px !important; font-weight: 600; padding: 4px 8px !important; }

    /* STRATEGIC COMMENT AREA */
    .comment-area-highlight { background: #fcfcfd; border: 2px solid var(--c-slate-200); border-left: 6px solid var(--c-primary); border-radius: 16px; padding: 20px; transition: 0.3s; }
    .comment-area-highlight:focus-within { border-color: var(--c-primary); background: #fff; }
    .textarea-master { width: 100%; border: none; background: transparent; font-family: 'Noto Sans Khmer', sans-serif; font-size: 1rem; line-height: 1.8; color: var(--c-slate-900); outline: none; min-height: 150px; resize: vertical; }

    .btn-update { background: var(--c-primary); color: #fff; padding: 18px; border: none; border-radius: var(--radius-md); font-weight: 800; width: 100%; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
    .btn-update:hover { background: #4338ca; transform: translateY(-2px); }
</style>

<div class="container-fluid container-full-width">
    <div class="card-clean">
        <div class="card-header-subtle">
            <div>
                <h3 class="mb-1 fw-800 text-slate-900">Edit Master Record</h3>
                <p class="mb-0 text-muted small">Update intelligence for: <b class="text-primary">{{ $lead->customer_name }}</b></p>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-light border  py-2 fw-bold rounded-pill text-muted small">
                <i class="fas fa-arrow-left me-2"></i> Cancel & Back
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form id="editLeadForm" action="{{ route('leads.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 border-end pe-md-5">
                        <div class="section-title"><i class="fas fa-shield-halved"></i> 01. Attribution Context</div>
                        
                        <label class="field-label text-uppercase">Registry Date</label>
                        <div class="input-wrapper">
                            <i class="fas fa-calendar-day field-icon"></i>
                            <input type="date" name="date" class="form-control input-clean" 
                                   value="{{ \Carbon\Carbon::parse($lead->date)->format('Y-m-d') }}" 
                                   {{ !$isAdminOrDirector ? 'readonly tabindex=-1 style=background:#f1f5f9;cursor:not-allowed;opacity:0.9;' : '' }} required>
                        </div>
                    
                        <label class="field-label text-uppercase">Sales Consultant</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user-tie field-icon"></i>
                            <select name="sales_person" class="form-select input-clean khmer-data" 
                                    {{ !$isAdminOrDirector ? 'readonly tabindex=-1 style=background:#f1f5f9;cursor:not-allowed;pointer-events:none;' : '' }} required>
                                @foreach($staff as $name)
                                    <option value="{{ $name }}" {{ $lead->sales_person == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <label class="field-label text-uppercase">Operational Branch</label>
                        <div class="input-wrapper">
                            <i class="fas fa-layer-group field-icon"></i>
                            <select name="branch_name" class="form-select input-clean khmer-data" 
                                    {{ !$isAdminOrDirector ? 'readonly tabindex=-1 style=background:#f1f5f9;cursor:not-allowed;pointer-events:none;' : '' }} required>
                                {{-- 🌟 បន្ថែម Leap Motors --}}
                                @foreach(['MG Head Office', 'MG 60M Branch', 'MG Siem Reap', 'MG Battambang', 'Leap Motors'] as $branch)
                                    <option value="{{ $branch }}" {{ (isset($lead->branch_name) ? $lead->branch_name : $lead->branch) == $branch ? 'selected' : '' }}>{{ $branch }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <label class="field-label text-uppercase">Acquisition Source</label>
                        <div class="input-wrapper">
                            <i class="fas fa-bullseye field-icon"></i>
                            <select name="source" class="form-select input-clean" 
                                    {{ !$isAdminOrDirector ? 'readonly tabindex=-1 style=background:#f1f5f9;cursor:not-allowed;pointer-events:none;' : '' }} required>
                                @foreach(['Facebook', 'Website', 'Telegram', 'TikTok', 'Google Ads', 'Showroom Walk-in', 'Event / Exhibition', 'Roadshow', 'Customer Referral', 'Phone Call'] as $src)
                                    <option value="{{ $src }}" {{ $lead->source == $src ? 'selected' : '' }}>{{ $src }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mt-2 text-muted" style="font-size: 10px; font-style: italic;">
                            <i class="fas fa-info-circle"></i> Fields above are managed by Administration.
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-5 mt-4 mt-md-0">
                        <div class="section-title"><i class="fas fa-user-astronaut"></i> 02. Lead Intelligence</div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Primary Phone</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone-alt field-icon" id="phoneIcon"></i>
                                    <input type="text" name="contact_info" id="phoneInput" class="form-control input-clean fw-bold text-primary" value="{{ $lead->contact_info }}" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Full Client Name</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user field-icon"></i>
                                    <input type="text" name="customer_name" class="form-control input-clean khmer-data" value="{{ $lead->customer_name }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Province / Location <span class="text-danger">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-marker-alt field-icon text-danger"></i>
                                    <select name="location" class="form-select input-clean" required>
                                        <option value="" disabled>Select Province...</option>
                                        {{-- 🌟 បន្ថែម Field Location (ខេត្តក្រុង) --}}
                                        @foreach(['Phnom Penh', 'Banteay Meanchey', 'Battambang', 'Kampong Cham', 'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom', 'Kampot', 'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri', 'Oddar Meanchey', 'Pailin', 'Preah Sihanouk', 'Preah Vihear', 'Prey Veng', 'Pursat', 'Ratanakiri', 'Siem Reap', 'Stung Treng', 'Svay Rieng', 'Takeo', 'Tboung Khmum'] as $prov)
                                            <option value="{{ $prov }}" {{ $lead->location == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Interested Models</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-car field-icon" style="z-index: 11;"></i>
                                    <select name="model[]" id="model_select" class="form-select" multiple="multiple" required>
                                        
                                        {{-- 🌟 បង្ហាញឡាន MG --}}
                                        @if($isAdminOrDirector || $isMGBranch)
                                            <optgroup label="MG Motors">
                                                @foreach(['MG ZS', 'MG HS', 'MG5', 'MG7', 'MG RX5', 'MG ONE', 'MG4 EV', 'MG Cyberster'] as $m)
                                                    <option value="{{ $m }}" {{ in_array($m, $selectedModels) ? 'selected' : '' }}>{{ $m }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                        {{-- 🌟 បង្ហាញឡាន Leapmotor --}}
                                        @if($isAdminOrDirector || $isLeapBranch)
                                            <optgroup label="Leapmotor">
                                                @foreach(['Leapmotor T03', 'Leapmotor C10', 'Leapmotor C11', 'Leapmotor C01', 'Leapmotor C16'] as $m)
                                                    <option value="{{ $m }}" {{ in_array($m, $selectedModels) ? 'selected' : '' }}>{{ $m }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Pipeline Stage</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-tasks field-icon"></i>
                                    <select name="status" class="form-select input-clean" required>
                                        @foreach(['Prospecting', 'Appointment', 'Meet & Greet', 'Consulting ( Q & A )', 'Double Open', 'Presentation', 'Test Drive', 'Trail Close', 'Negotiation', 'Double Close', 'Sales ( contract of sales )', 'Delivery', 'Lost'] as $st)
                                            <option value="{{ $st }}" {{ $lead->status == $st ? 'selected' : '' }}>{{ strtoupper($st) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label text-uppercase">Next Action Date</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar-check field-icon"></i>
                                    <input type="date" name="remind_date" class="form-control input-clean text-primary" value="{{ $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('Y-m-d') : '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="section-title"><i class="fas fa-pen-nib"></i> 03. Internal Briefing & Strategic Remark</div>
                                <div class="comment-area-highlight">
                                    <textarea name="comment" class="textarea-master khmer-data" placeholder="Enter detailed strategic notes here...">{{ $lead->comment }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-5 ms-auto">
                                <button type="submit" class="btn-update"><i class="fas fa-save me-2"></i> COMMIT & SAVE</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#model_select').select2({ placeholder: "  Select models...", maximumSelectionLength: 5, width: '100%' });

    // 🌟 លេខដើមរបស់អតិថិជន (ដើម្បីកុំឱ្យវាឆែកស្ទួនលេខខ្លួនឯងពេលកែប្រែ)
    const originalPhone = "{{ str_replace(' ', '', $lead->contact_info) }}";
    let isPhoneDuplicate = false;

    $('#phoneInput').on('input', function() {
        let val = $(this).val().replace(/\D/g, ''); 
        
        // Formatting 012 345 678
        if (val.length > 3 && val.length <= 6) val = val.replace(/(\d{3})(\d+)/, '$1 $2');
        else if (val.length > 6) val = val.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        $(this).val(val);

        let rawPhone = val.replace(/\s/g, '');

        // 🌟 បើគាត់មិនបានកែលេខទូរស័ព្ទទេ មិនបាច់ឆែកទេ (ការពារកុំឱ្យ Error ជាន់លេខខ្លួនឯង)
        if (rawPhone === originalPhone) {
            isPhoneDuplicate = false;
            $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#64748b');
            $('#phoneInput').css('border-color', 'var(--c-slate-200)');
            return;
        }

        // ឆែកចាប់ពី ៨ ខ្ទង់ឡើងទៅ
        if (rawPhone.length >= 8) { 
            $('#phoneIcon').attr('class', 'fas fa-spinner fa-spin field-icon').css('color', '#f59e0b');
            checkDuplicateEdit(rawPhone);
        } else { 
            isPhoneDuplicate = false; 
            $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#64748b');
            $('#phoneInput').css('border-color', 'var(--c-slate-200)'); 
        }
    });

    function checkDuplicateEdit(phone) {
        // ប្រើ Relative URL ការពារ Mixed Content (HTTPS)
        $.ajax({
            url: "/leads/check-duplicate",
            type: "POST",
            data: { 
                phone: phone, 
                _token: "{{ csrf_token() }}" 
            },
            success: function(res) {
                if (res.exists) {
                    isPhoneDuplicate = true;
                    $('#phoneIcon').attr('class', 'fas fa-exclamation-triangle field-icon').css('color', '#ef4444');
                    $('#phoneInput').css('border-color', '#ef4444');
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'លេខទូរស័ព្ទស្ទួន!', 
                        text: 'អតិថិជននេះជារបស់ Sale ' + res.sales_person + ' នៅសាខា ' + res.branch + ' រួចហើយ។', 
                        confirmButtonColor: '#e11d48' 
                    });
                } else { 
                    isPhoneDuplicate = false; 
                    $('#phoneIcon').attr('class', 'fas fa-check-circle field-icon').css('color', '#10b981');
                    $('#phoneInput').css('border-color', '#10b981'); 
                }
            },
            error: function() {
                $('#phoneIcon').attr('class', 'fas fa-phone-alt field-icon').css('color', '#64748b');
            }
        });
    }

    // ការពារការ Save បើលេខទូរស័ព្ទជាន់គេ
    $('#editLeadForm').on('submit', function(e) {
        if (isPhoneDuplicate) { 
            e.preventDefault(); 
            Swal.fire('Error', 'សូមប្តូរលេខទូរស័ព្ទសិន ព្រោះលេខនេះស្ទួន!', 'warning'); 
            return false; 
        }
        Swal.fire({ title: 'Saving Update...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    });
});
</script>
@endsection