@extends('layouts.homePages')
@section('content')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* --- GLOBAL BRANDING & OPTIMIZATION --- */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f1f5f9;
        --surface-white: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-color: #cbd5e1;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-fluid {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        color: var(--text-main);
    }

    /* --- ANIMATIONS (GPU Accelerated) --- */
    @keyframes fadeUp {
        0% { opacity: 0; transform: translate3d(0, 20px, 0); }
        100% { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .anim-fade-up {
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        will-change: opacity, transform;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }

    /* 🌟 Fit Screen Document Wrapper 🌟 */
    .document-wrapper {
        background: var(--surface-white);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        padding: 25px 35px;
        margin: 0;
        width: 100%;
        border: 1px solid var(--border-color);
    }

    /* Professional Gradient Header */
    .page-header {
        margin-bottom: 25px;
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }
    .page-title {
        font-size: 20px; font-weight: 800; color: var(--text-main);
        margin: 0; display: flex; align-items: center; gap: 10px; text-transform: uppercase;
    }
    .page-title i {
        background: linear-gradient(135deg, var(--brand-indigo) 0%, var(--brand-purple) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Section Divider */
    .section-header {
        font-size: 13px; font-weight: 800; color: var(--brand-indigo);
        background: #f5f3ff; border-left: 4px solid var(--brand-indigo);
        padding: 10px 15px; border-radius: 0 8px 8px 0;
        margin: 2rem 0 1.2rem; display: flex; align-items: center; gap: 8px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Avatar Upload */
    .avatar-upload-wrapper { display: flex; align-items: center; gap: 25px; padding: 10px 0; }
    .avatar-preview { width: 100px; height: 100px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .btn-upload {
        background: var(--bg-body); color: var(--text-main);
        border: 1px solid var(--border-color); border-radius: 8px;
        padding: 8px 20px; cursor: pointer; font-weight: 700; font-size: 13px; transition: 0.2s;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-upload:hover { background: #e2e8f0; transform: translateY(-2px); }

    /* Form Controls */
    .label-custom {
        font-weight: 700; color: var(--text-muted); font-size: 11px;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block;
    }
    .form-control, .form-select, .input-group-text {
        border: 1px solid var(--border-color) !important;
        border-radius: 8px !important; padding: 10px 15px !important;
        height: 44px !important; background-color: #fff !important;
        font-weight: 600; font-size: 13px; color: var(--text-main); transition: 0.2s;
    }
    .input-group .form-control { border-right: none !important; border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
    .input-group .input-group-text { border-left: none !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; background: #fff !important; cursor: pointer; color: var(--text-muted); }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand-indigo) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
    }

    /* Select2 */
    .select2-container .select2-selection--single {
        height: 44px !important; border: 1px solid var(--border-color) !important;
        border-radius: 8px !important; display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-weight: 600; font-size: 13px; padding-left: 15px !important; color: var(--text-main) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }

    /* Buttons */
    .form-actions {
        margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;
        display: flex; justify-content: flex-end; gap: 12px;
    }
    .btn-save {
        background: var(--text-main); color: white; border: none;
        padding: 10px 30px; border-radius: 8px; font-weight: 800; font-size: 13px;
        text-transform: uppercase; letter-spacing: 0.5px; transition: 0.2s;
    }
    .btn-save:hover { background: var(--brand-indigo); transform: translateY(-2px); box-shadow: 0 8px 15px rgba(79,70,229,0.2); }
    .btn-cancel {
        background: #fff; color: var(--text-muted); border: 1px solid var(--border-color);
        padding: 10px 25px; border-radius: 8px; font-weight: 700; font-size: 13px;
        text-decoration: none; transition: 0.2s;
    }
    .btn-cancel:hover { background: #f8fafc; color: var(--text-main); }
</style>
@endpush

<div class="container-fluid flex-grow-1 px-3 py-4">
    
    <div class="document-wrapper anim-fade-up">
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title"><i class='fas fa-user-shield text-indigo-600'></i> MODIFY PERSONNEL DOSSIER</h1>
                <p class="text-muted mt-2 mb-0 fw-bold" style="font-size: 12px;">Update access credentials and organizational role for <span class="text-dark">{{ $users->full_name }}</span>.</p>
            </div>
            <a href="{{ url('/users') }}" class="btn btn-light btn-sm fw-bold border" style="border-radius: 8px; padding: 8px 16px;">
                <i class="fas fa-list me-1"></i> DIRECTORY
            </a>
        </div>

        <form action="{{ url('/users/update', encrypt($users->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Avatar Section -->
            <div class="avatar-upload-wrapper anim-fade-up delay-100">
                <img id="target" src="{{ url($users->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $users->profile_pic) }}" class="avatar-preview">
                <div>
                    <label for="select_image" class="btn-upload shadow-sm">
                        <i class="fas fa-camera"></i> Change Photo
                        <input type="file" name="profile_pic" id="select_image" onchange="selectImg()" hidden accept="image/*">
                    </label>
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px; font-weight: 600;">Allowed JPEG, PNG. Max 1MB.</p>
                </div>
            </div>

            <!-- 1. Basic Identity -->
            <div class="section-header anim-fade-up delay-100"><i class="fas fa-id-card"></i> 1. Basic Information</div>
            <div class="row g-3 anim-fade-up delay-100">
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" value="{{ $users->full_name }}" required>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male" @if($users->gender == 'Male') selected @endif>Male</option>
                        <option value="Female" @if($users->gender == 'Female') selected @endif>Female</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Email Address</label>
                    <input type="email" class="form-control" name="email" value="{{ $users->email }}">
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number" value="{{ $users->phone_number }}">
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Date Start Work</label>
                    <input class="form-control" type="date" name="start_work_date" value="{{ $users->start_work_date }}">
                </div>
            </div>

            <!-- 2. Organizational Deployment -->
            <div class="section-header anim-fade-up delay-200"><i class="fas fa-sitemap"></i> 2. Organizational Deployment</div>
            <div class="row g-3 anim-fade-up delay-200">
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Assigned Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" class="form-select select2-simple" required>
                        @foreach ($branchs as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $users->branch_id) selected @endif>{{ $item->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="form-select select2-simple" required>
                        @foreach ($departs as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $users->department_id) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="label-custom">Functional Position <span class="text-danger">*</span></label>
                    <select id="position-select" class="form-select" name="position_id" required>
                        @if($users->position_id)
                            <!-- 🌟 បញ្ចូលឈ្មោះតំណែងចាស់ដោយផ្ទាល់តាម Blade (គ្មាន Error ដើរលឿន) 🌟 -->
                            <option value="{{ $users->position_id }}" selected="selected">
                                {{ $users->positionName ?? 'Current Position' }}
                            </option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- 3. System Credentials -->
            <div class="section-header anim-fade-up delay-200"><i class="fas fa-shield-alt"></i> 3. Access Credentials</div>
            <div class="row g-3 anim-fade-up delay-200">
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">User ID (UID) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-primary fw-bold" name="user_id" value="{{ $users->user_id }}" readonly style="background: #f8fafc !important;">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">System Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control fw-bold" name="username" value="{{ $users->username }}" required>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">Change Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="show-password" placeholder="Leave blank to keep old">
                        <span class="input-group-text" onclick="togglePasswordVisibility()">
                            <i class="fas fa-eye-slash" id="toggle-icon"></i>
                        </span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="label-custom">System Access Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select select2-simple" required>
                        @foreach ($userRole as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $users->role_id) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions anim-fade-up delay-200">
                <a href="{{ url('users') }}" class="btn-cancel shadow-sm">Cancel</a>
                <button type="submit" class="btn-save shadow-sm" id="btnUpdate">
                    <i class="fas fa-save me-2"></i> Save Changes
                </button>
            </div>

        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // 1. Image Preview
    function selectImg() {
        const src = document.getElementById("select_image");
        const target = document.getElementById("target");
        if (src.files && src.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => { target.src = e.target.result; }
            reader.readAsDataURL(src.files[0]);
        }
    }

    // 2. Toggle Password Visibility
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('show-password');
        const toggleIcon = document.getElementById('toggle-icon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        }
    }

    // 3. Initialize Select2 & Form Submission
    $(document).ready(function() {
        
        // Simple Select2 សម្រាប់ Dropdown ធម្មតា
        $('.select2-simple').select2({ width: '100%' });

        // AJAX Select2 សម្រាប់ Position ដើរពេលចុចវាយស្វែងរកប៉ុណ្ណោះ
        $('#position-select').select2({
            placeholder: "Search Position...",
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ url('/users/create') }}", 
                type: "GET",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { search: params.term };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results ? data.results : data, function (item) {
                            return { id: item.id, text: item.name || item.text };
                        })
                    };
                },
                cache: true
            }
        });

        // 🌟 Loading State ពេលចុច Save Changes 🌟
        $('form').on('submit', function() {
            const btn = $('#btnUpdate');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');
        });
        
    }); // <--- 🌟 សញ្ញាបិទដែលធ្លាប់បាត់ ឥឡូវមានវិញហើយ 🌟
</script>
@endpush

@endsection