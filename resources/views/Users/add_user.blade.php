@extends('layouts.homePages')

@section('content')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* =========================================================
       CLEAN SaaS DESIGN SYSTEM FOR FORMS
       ========================================================= */
    :root {
        --primary: #4F46E5;      /* Indigo 600 */
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      
        --text-main: #111827;    
        --text-muted: #6B7280;   
        --border-color: #E5E7EB; 
        --input-bg: #F9FAFB;
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
    }

    body, .container-xxl {
        font-family: var(--font-body);
        background-color: var(--bg-body);
        color: var(--text-main);
    }

    .container-xxl { padding: 24px 32px !important; max-width: 100% !important; width: 100% !important; }

    /* --- FORM CARD --- */
    .form-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    /* Clean Header */
    .card-header-clean {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FFFFFF;
    }

    .header-title {
        font-family: var(--font-display);
        color: var(--text-main);
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-back-clean {
        background: #FFFFFF;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
        font-size: 0.875rem;
        font-weight: 600;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-back-clean:hover { background: #F3F4F6; color: var(--text-main); }

    /* --- FORM INPUTS --- */
    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: #4B5563;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    /* ធ្វើឱ្យ Icon ពណ៌ប្រផេះដូចៗគ្នា កុំឱ្យរញ៉េរញ៉ៃ */
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    .form-control, .form-select {
        height: 44px;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-size: 0.875rem;
        background-color: var(--input-bg);
        color: var(--text-main);
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        background-color: #FFFFFF;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    /* Password Input Group */
    .input-group { position: relative; display: flex; align-items: stretch; width: 100%; }
    .input-group .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; }
    .input-group-text {
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-left: none;
        border-radius: 0 8px 8px 0;
        color: #9CA3AF;
        padding: 0 16px;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .input-group:focus-within .input-group-text { background: #FFFFFF; border-color: var(--primary); }

    /* --- UPLOAD SECTION --- */
    .upload-area {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border-radius: 8px;
        border: 1px dashed #D1D5DB;
        background: #F9FAFB;
    }
    #target {
        width: 80px; height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #FFFFFF;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-upload {
        background: #FFFFFF;
        border: 1px solid var(--border-color);
        color: #4B5563;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-upload:hover { background: #F3F4F6; color: var(--text-main); }

    /* --- SUBMIT BUTTON --- */
    .btn-save {
        background-color: var(--primary);
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-save:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    /* --- SELECT2 OVERRIDES --- */
    .select2-container .select2-selection--single {
        height: 44px !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 8px !important;
        background-color: var(--input-bg) !important;
        display: flex; align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 16px !important; font-size: 0.875rem; color: var(--text-main) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }

    /* Animation */
    .anim-fade-up {
        animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0; transform: translateY(15px);
    }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="form-card anim-fade-up">
                
                <div class="card-header-clean">
                    <h5 class="header-title">
                        <i class="fas fa-user-plus text-muted"></i> Register New Personnel
                    </h5>
                    <a href="{{ url('/users') }}" class="btn-back-clean">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4 p-md-5">
                    <form action="{{ url('/users/store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Section: Basic Info -->
                            <div class="col-12 mb-2">
                                <h6 class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px;">Basic Information</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="far fa-id-card"></i> Full Name</label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" placeholder="E.g. John Doe">
                                @error('full_name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="far fa-user"></i> Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="E.g. johndoe">
                                @error('username') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-fingerprint"></i> User-ID</label>
                                <input type="text" class="form-control" name="user_id" value="{{ old('user_id') }}" placeholder="EMP-XXX">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="" disabled selected>Select Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="far fa-envelope"></i> Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="name@domain.com">
                                @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-phone-alt"></i> Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" placeholder="+855 ...">
                            </div>

                            <!-- Section: Assignment & Security -->
                            <div class="col-12 mt-5 mb-2">
                                <h6 class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px;">Assignment & Security</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="far fa-building"></i> Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="" disabled selected>Select Branch</option>
                                    @foreach ($branchs as $item)
                                        <option value="{{ $item->id }}" {{ old('branch_id') == $item->id ? 'selected' : '' }}>{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-sitemap"></i> Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach ($departs as $item)
                                        <option value="{{ $item->id }}" {{ old('department_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-briefcase"></i> Position</label>
                                <select id="position-select" class="form-select select2-init" name="position_id" style="width: 100%" required>
                                    <option value="" disabled selected>Select Position</option>
                                    @if(isset($positions))
                                        @foreach ($positions as $item)
                                            <option value="{{ $item->id }}" {{ old('position_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="far fa-calendar-check"></i> Date Start Work</label>
                                <input type="date" class="form-control" name="start_work_date" value="{{ old('start_work_date') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-shield-alt"></i> System Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="" disabled selected>Select Role</option>
                                    @foreach ($userRole as $item)
                                        <option value="{{ $item->id }}" {{ old('role_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-lock"></i> Password Access</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="show-password" placeholder="••••••••">
                                    <span class="input-group-text" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                                        <i class="fas fa-eye-slash" id="toggle-icon"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Section: Profile Photo -->
                            <div class="col-12 mt-4">
                                <div class="upload-area">
                                    <img src="{{ url('backend/assets/img/avatars/1.png') }}" id="target" alt="Profile Preview">
                                    <div>
                                        <label for="select_image" class="btn-upload">
                                            <i class="fas fa-cloud-upload-alt"></i> Upload Photo
                                            <input type="file" name="profile_pic" id="select_image" hidden onchange="selectImg()">
                                        </label>
                                        <p class="text-muted mt-2 mb-0" style="font-size: 0.75rem;">JPG or PNG. Recommended size: 500x500px.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn-save" id="btnSubmit">
                                    <i class="fas fa-check-circle"></i> Create User Account
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Image Preview
    function selectImg() {
        let input = document.getElementById("select_image");
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("target").src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Password Toggle
    function togglePasswordVisibility() {
        let field = document.getElementById('show-password');
        let icon = document.getElementById('toggle-icon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            icon.style.color = '#4F46E5'; // Highlight when visible
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
            icon.style.color = '#9CA3AF'; // Muted when hidden
        }
    }

    $(document).ready(function() {
        // Initialize Select2
        $('.select2-init').select2({
            placeholder: "Select Position",
            allowClear: true,
            width: '100%'
        });

        // Submit Button Loading State
        $('#mainForm').on('submit', function() {
            let btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
            btn.style.opacity = '0.8';
        });
    });
</script>
@endpush
@endsection