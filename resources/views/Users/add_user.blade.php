@extends('layouts.homePages')

@section('content')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* --- GLOBAL BRANDING --- */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: #1e293b;
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* --- PREMIUM CARD STYLING --- */
    .full-width-card {
        width: 100% !important;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }

    /* 4-Color Gradient Header */
    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%) !important;
        padding: 20px 25px;
        border-bottom: none;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* --- FORM ELEMENTS --- */
    .form-label {
        font-weight: 700;
        font-size: 0.75rem;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
    }

    .form-control, .form-select {
        height: 48px;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.85rem;
        background-color: #fcfdfe;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .is-invalid {
        border-color: var(--brand-red) !important;
        background-color: #fef2f2 !important;
    }

    /* --- BUTTONS --- */
    .btn-save {
        background-color: #1e293b;
        color: white;
        border: none;
        font-weight: 800;
        padding: 14px 30px;
        border-radius: 12px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        text-transform: uppercase;
    }

    .btn-save:hover { 
        background-color: #000; 
        transform: translateY(-2px); 
        color: white; 
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 18px;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.2s;
    }
    .btn-back:hover { background: rgba(255, 255, 255, 0.3); color: #fff; }

    /* --- AVATAR PREVIEW --- */
    #target {
        border-radius: 16px;
        object-fit: cover;
        border: 4px solid #f8fafc;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    /* --- SELECT2 OVERRIDES --- */
    .select2-container .select2-selection--single {
        height: 48px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 10px !important;
        background-color: #fcfdfe !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px !important;
        font-weight: 500;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }

    .input-group-text {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 0 10px 10px 0;
        color: #94a3b8;
    }

    /* Animation */
    .animate-up {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeInUp 0.4s ease forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card full-width-card animate-up">
                <div class="card-header-gradient d-flex justify-content-between align-items-center">
                    <h5 class="header-title">
                        <i class="fas fa-user-plus me-2"></i>CREATING PERSONNEL ACCOUNT
                    </h5>
                    <a href="{{ url('/users') }}" class="btn btn-back">
                        <i class="fas fa-times me-1"></i> CANCEL
                    </a>
                </div>

                <div class="card-body p-5">
                    <form action="{{ url('/users/store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-id-card text-primary"></i> Full Name</label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                    name="full_name" value="{{ old('full_name') }}" placeholder="John Doe">
                                @error('full_name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-user-tag text-purple"></i> Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    name="username" value="{{ old('username') }}" placeholder="johndoe">
                                @error('username') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-fingerprint text-pink"></i> User-ID</label>
                                <input type="text" class="form-control" name="user_id" value="{{ old('user_id') }}" placeholder="EMP-XXX">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-venus-mars text-info"></i> Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="" disabled selected>Choose Gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-envelope text-danger"></i> Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" placeholder="name@domain.com">
                                @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-phone text-success"></i> Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" placeholder="+855 ...">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-building text-warning"></i> Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="" disabled selected>Select Branch</option>
                                    @foreach ($branchs as $item)
                                        <option value="{{ $item->id }}" {{ old('branch_id') == $item->id ? 'selected' : '' }}>{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-sitemap text-secondary"></i> Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="" disabled selected>Select Department</option>
                                    @foreach ($departs as $item)
                                        <option value="{{ $item->id }}" {{ old('department_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 🌟 កែសម្រួល៖ Position ប្រើ Loop ធម្មតា ជំនួសឱ្យ AJAX 🌟 --}}
                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-briefcase text-primary"></i> Position</label>
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
                                <label class="form-label"><i class="fas fa-calendar-check text-success"></i> Date Start Work</label>
                                <input type="date" class="form-control" name="start_work_date" value="{{ old('start_work_date') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-lock text-danger"></i> Password Access</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="show-password" placeholder="••••••••">
                                    <span class="input-group-text" onclick="togglePasswordVisibility()" style="cursor: pointer;">
                                        <i class="fas fa-eye-slash" id="toggle-icon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label"><i class="fas fa-user-shield text-purple"></i> System Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="" disabled selected>Select Role</option>
                                    @foreach ($userRole as $item)
                                        <option value="{{ $item->id }}" {{ old('role_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex align-items-center gap-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                                    <img src="{{ url('backend/assets/img/avatars/1.png') }}" id="target" width="100" height="100">
                                    <div>
                                        <label for="select_image" class="btn btn-outline-primary btn-sm mb-2 fw-bold">
                                            <i class="fas fa-upload me-1"></i> UPLOAD PROFILE PHOTO
                                            <input type="file" name="profile_pic" id="select_image" hidden onchange="selectImg()">
                                        </label>
                                        <p class="text-muted mb-0 small">Supported formats: JPG, PNG. Recommended size: 500x500px.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-save w-100" id="btnSubmit">
                                    <i class="fas fa-user-check me-2"></i> INITIALIZE NEW ACCOUNT
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

    function togglePasswordVisibility() {
        let field = document.getElementById('show-password');
        let icon = document.getElementById('toggle-icon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }

    $(document).ready(function() {
        // 🌟 កែសម្រួល៖ Initialize Select2 ជាលក្ខណៈធម្មតា លែងប្រើ AJAX
        $('.select2-init').select2({
            placeholder: "Select Position",
            allowClear: true,
            width: '100%'
        });

        // Effect ពេលចុចបញ្ជូនទិន្នន័យ
        $('#mainForm').on('submit', function() {
            let btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> INITIALIZING...';
        });
    });
</script>
@endpush
@endsection