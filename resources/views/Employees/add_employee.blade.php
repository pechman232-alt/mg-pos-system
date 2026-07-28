@extends('layouts.homePages')
@section('content')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ================= COMPACT & FULL WIDTH UI ================= */
    :root { 
        --brand-red: #dc2626; 
        --brand-red-hover: #b91c1c;
        --text-heading: #0f172a; 
        --text-body: #475569; 
        --bg-main: #f1f5f9; 
        --border-color: #cbd5e1; 
        --card-bg: #ffffff;
    }

    body { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-main); 
        color: var(--text-body); 
    }
    
    /* 🌟 Fit Screen Document Wrapper 🌟 */
    .document-wrapper {
        background: var(--card-bg);
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        padding: 20px 25px; 
        margin: 0; 
        width: 100%;
    }
    
    /* Page Header */
    .page-header { 
        margin-bottom: 20px; 
        display: flex; justify-content: space-between; align-items: center; 
        border-bottom: 1px solid var(--border-color); 
        padding-bottom: 15px;
    }
    .page-title { 
        font-size: 18px; font-weight: 800; color: var(--text-heading); 
        margin: 0; display: flex; align-items: center; gap: 10px; text-transform: uppercase;
    }
    .page-subtitle { 
        font-size: 12px; font-weight: 600; color: var(--text-body); 
        margin-top: 4px; 
    }

    /* Modern Section Divider */
    .section-header { 
        font-size: 12px; font-weight: 800; color: var(--brand-red); 
        background: rgba(220, 38, 38, 0.08); 
        padding: 8px 15px; border-radius: 6px; 
        margin: 1.5rem 0 1rem; 
        display: flex; align-items: center; gap: 8px; 
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Avatar Upload Section (Compact) */
    .avatar-upload-wrapper { 
        display: flex; align-items: center; gap: 20px;
        padding: 10px 0 15px; 
    }
    .avatar-preview { 
        width: 80px; height: 80px; 
        border-radius: 8px; 
        object-fit: cover; 
        border: 1px solid var(--border-color); 
    }
    .btn-upload { 
        background: var(--bg-main); color: var(--text-heading) !important; 
        border: 1px solid var(--border-color); border-radius: 6px; 
        padding: 6px 15px; cursor: pointer; 
        font-weight: 600; font-size: 12px; transition: 0.2s; 
        display: inline-block; margin-bottom: 5px;
    }
    .btn-upload:hover { background: #e2e8f0; }

    /* Form Controls (Compact Format) */
    .label-custom { 
        font-weight: 700; color: var(--text-body); font-size: 11px; 
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 5px; display: block; 
    }
    .form-control, .form-select {
        border: 1px solid var(--border-color) !important; 
        border-radius: 6px !important; padding: 6px 12px !important; 
        height: 38px !important; 
        background-color: #fff !important; 
        font-weight: 500; font-size: 13px; color: var(--text-heading); transition: 0.2s;
    }
    .form-control:focus, .form-select:focus { 
        border-color: var(--brand-red) !important; 
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important; 
    }
    .pw-font { font-family: 'JetBrains Mono', monospace; color: var(--brand-red); font-weight: bold; }
    .form-control::placeholder { color: #94a3b8; font-weight: 400; }

    /* Select2 Custom Styles */
    .select2-container .select2-selection--single {
        height: 38px !important; border: 1px solid var(--border-color) !important;
        border-radius: 6px !important; display: flex; align-items: center;
        background-color: #fff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { 
        font-weight: 500; font-size: 13px; padding-left: 12px !important; color: var(--text-heading) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }

    /* Account Cards */
    .account-card { 
        background: #fff; border-radius: 8px; padding: 1.25rem; 
        border: 1px solid var(--border-color); height: 100%; 
    }
    .account-card label { 
        font-weight: 800; font-size: 12px; color: var(--text-heading); 
        text-transform: uppercase;
        margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
    }

    /* Actions */
    .form-actions { 
        margin-top: 2rem; padding-top: 1rem; 
        border-top: 1px solid var(--border-color); 
        display: flex; justify-content: flex-end; gap: 10px; 
    }
    .btn-save { 
        background: var(--brand-red); color: white; border: none; 
        padding: 8px 24px; border-radius: 6px; font-weight: 700; 
        font-size: 13px; transition: 0.2s; 
    }
    .btn-save:hover { background: var(--brand-red-hover); color: white; }
    
    .btn-cancel { 
        background: #fff; color: var(--text-body); border: 1px solid var(--border-color);
        padding: 8px 20px; border-radius: 6px; font-weight: 600; 
        font-size: 13px; text-decoration: none; transition: 0.2s; 
    }
    .btn-cancel:hover { background: #f8fafc; color: var(--text-heading); }
</style>
@endpush

<div class="container-fluid flex-grow-1 px-3 py-3">
    
    <div class="document-wrapper">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title"><i class='fas fa-user-plus text-danger'></i> REGISTER NEW PERSONNEL</h1>
                <div class="page-subtitle">Complete the form below to add a new entity to the intelligence system.</div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-light btn-sm fw-bold border" style="border-radius: 6px;">
                <i class="fas fa-list me-1"></i> DIRECTORY
            </a>
        </div>
        
        <form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Avatar Section -->
            <div class="avatar-upload-wrapper">
                <img id="target" src="{{ url('backend/assets/img/avatars/1.png') }}" class="avatar-preview">
                <div>
                    <label for="select_image" class="btn-upload">
                        <i class='fas fa-camera me-1'></i> Choose Photo
                        <input type="file" name="image_employee" id="select_image" onchange="selectImg()" hidden accept="image/*">
                    </label>
                    <p class="text-muted mb-0" style="font-size: 11px;">Square JPG, PNG. Max size 2MB.</p>
                </div>
            </div>

            <!-- 1. Personal Identity -->
            <div class="section-header"><i class='fas fa-id-card'></i> 1. Personal Demographics</div>
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">First Name (EN) <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" placeholder="John" required>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Last Name (EN) <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">នាម (KH)</label>
                    <input type="text" name="first_name_kh" class="form-control" placeholder="នាម">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">គោត្តនាម (KH)</label>
                    <input type="text" name="last_name_kh" class="form-control" placeholder="គោត្តនាម">
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Age</label>
                    <input type="number" name="age" class="form-control" placeholder="e.g. 25">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Date of Birth</label>
                    <input type="date" name="dob" class="form-control">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Mobile Number</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="e.g. +855 12 345 678">
                </div>
            </div>

            <!-- 2. Organizational Deployment -->
            <div class="section-header"><i class='fas fa-sitemap'></i> 2. Organizational Deployment</div>
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Company Entity <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select select2-init" required>
                        <option value="">Select Entity</option>
                        @if(isset($company))
                            @foreach ($company as $item) 
                                <option value="{{ $item->id }}">{{ $item->company_name }}</option> 
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Work Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select select2-init" required>
                        <option value="">Select Branch</option>
                        @if(isset($branch))
                            @foreach ($branch as $item) 
                                <option value="{{ $item->id }}">{{ $item->branch_name }}</option> 
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Department <span class="text-danger">*</span></label>
                    <select id="department-select" name="department_id" class="form-control select2-init" required>
                        <option value="">Select Department</option>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Functional Position <span class="text-danger">*</span></label>
                    <select id="position-select" name="position_id" class="form-control select2-init" required>
                        <option value="">Select Position</option>
                    </select>
                </div>
                
                <!-- 🌟 កែសម្រួល Contract Date & Employee ID 🌟 -->
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Contract Date <span class="text-danger">*</span></label>
                    <input type="date" name="contract" id="contract_date" class="form-control" required>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Employee ID (UID) <span class="text-danger">*</span></label>
                    <input type="text" name="user_id" id="employee_id" class="form-control fw-bold text-danger" 
                           placeholder="Auto generated after picking date..." required readonly 
                           style="background-color: #f8fafc !important; cursor: not-allowed;">
                </div>
            </div>

            <!-- 3. Infrastructure & Security -->
            <div class="section-header"><i class='fas fa-shield-alt'></i> 3. Infrastructure & Security</div>
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="account-card">
                        <label><i class="fas fa-desktop text-primary"></i> Official Core System</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="label-custom">User ID</label>
                                <input type="text" class="form-control" name="official_userid" placeholder="System User ID">
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">Password</label>
                                <input type="text" class="form-control pw-font" name="official_pass" placeholder="Initial Password">
                            </div>
                            <div class="col-md-12">
                                <label class="label-custom">Email Domain</label>
                                <select name="group_domain" class="form-select">
                                    <option value="tfmotors.com.kh">tfmotors.com.kh</option>
                                    <option value="mgmotors.com.kh">mgmotors.com.kh</option>
                                    <option value="taifu.com.kh">taifu.com.kh</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="account-card">
                        <label><i class="fas fa-cloud text-success"></i> Cloud & Access Controls</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="label-custom">M365 Email User</label>
                                <input type="text" class="form-control" name="m_user_id" placeholder="M365 Email Account">
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">M365 Password</label>
                                <input type="text" class="form-control pw-font" name="m_pass" placeholder="M365 Password">
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">Internal Extension</label>
                                <input type="text" class="form-control" name="extension_id" placeholder="e.g. 101">
                            </div>
                            <div class="col-md-6">
                                <label class="label-custom">Access Door Card</label>
                                <input type="text" class="form-control" name="access_door" placeholder="Card Number">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('employees.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save" id="btnUpdate">
                    <i class='fas fa-check me-1'></i> Register Employee
                </button>
            </div>
            
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function selectImg() {
        const src = document.getElementById("select_image");
        const target = document.getElementById("target");
        if (src.files && src.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => { target.src = e.target.result; }
            reader.readAsDataURL(src.files[0]);
        }
    }

    (function($) {
        $(document).ready(function() {
            // Function for Select2 AJAX initialization
            function initSelect2(selector, type, placeholder) {
                if (typeof $.fn.select2 !== 'undefined') {
                    $(selector).select2({
                        placeholder: placeholder,
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: "{{ route('employees.create') }}",
                            dataType: 'json',
                            delay: 300,
                            data: function (params) {
                                return { search: params.term, type: type };
                            },
                            processResults: function (data) {
                                return { results: data.results }; 
                            },
                            cache: true
                        }
                    });
                }
            }

            // Initialize select2 for fields that need AJAX
            initSelect2('#position-select', 'position', 'Search Positions...');
            initSelect2('#department-select', 'department', 'Search Departments...');
            
            // បន្ថែម Select2 ធម្មតាសម្រាប់ Dropdown ដែលមិនមែន AJAX
            $('.select2-init').not('#position-select, #department-select').select2({
                width: '100%'
            });

            // 🌟 មុខងារ AJAX សម្រាប់ Generate ID ពេលជ្រើសរើស Contract Date 🌟
            $('#contract_date').on('change', function() {
                let selectedDate = $(this).val();
                let employeeIdInput = $('#employee_id');

                if (selectedDate) {
                    employeeIdInput.val('Generating ID...');
                    
                    $.ajax({
                        url: "{{ route('employees.generate_id') }}", // ត្រូវប្រាកដថាបងបានដាក់ Route នេះក្នុង web.php ដូចដែលបានប្រាប់មុននេះ
                        type: "GET",
                        data: { date: selectedDate },
                        success: function(response) {
                            if (response.success) {
                                employeeIdInput.val(response.new_id);
                            } else {
                                employeeIdInput.val('Error generating ID');
                            }
                        },
                        error: function() {
                            employeeIdInput.val('Server Error');
                        }
                    });
                } else {
                    employeeIdInput.val('');
                }
            });

            $('#employeeForm').on('submit', function() {
                const btn = $('#btnUpdate');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Registering...');
            });
        });
    })(jQuery);
</script>
@endpush

@endsection