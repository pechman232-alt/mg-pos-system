@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --bg-body: #f8fafc;
        --radius: 20px;
    }

    body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; color: #1e293b; font-size: 0.82rem; }
    .card-custom { border: none; border-radius: var(--radius); box-shadow: 0 15px 45px rgba(0,0,0,0.05); background: #fff; overflow: hidden; animation: fadeIn 0.5s ease-in-out; }
    .card-header-gradient { background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 100%) !important; padding: 1.2rem 2rem; border: none; }
    
    .section-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 2rem 0 1rem; padding-bottom: 8px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f1f5f9; }
    .sec-personal { color: var(--brand-indigo); }
    .sec-org { color: var(--brand-purple); }
    .sec-security { color: var(--brand-pink); }

    label.col-form-label { font-weight: 700; color: #64748b; font-size: 0.68rem; text-transform: uppercase; margin-bottom: 5px; display: block; }
    .form-control, .form-select, .select2-container--default .select2-selection--single {
        border: 1.5px solid #f1f5f9 !important; border-radius: 10px !important; padding: 0.6rem 1rem !important; height: auto !important;
        background-color: #f8fafc !important; font-weight: 600; transition: 0.3s;
    }
    .form-control:focus { border-color: var(--brand-indigo) !important; background-color: #fff !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08) !important; outline: none; }

    .credential-card { background: #fcfdfe; border: 1.5px solid #f1f5f9; border-radius: 15px; padding: 18px; height: 100%; transition: 0.3s; }
    .credential-card:hover { border-color: var(--brand-indigo); background: #fff; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
    .pw-font { font-family: 'JetBrains Mono', monospace !important; color: #ef4444; font-weight: 700; }

    .img-preview { border-radius: 30px; border: 4px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); object-fit: cover; }
    .btn-update { background: var(--brand-indigo); border: none; color: white; font-weight: 800; border-radius: 14px; padding: 1rem 3rem; transition: 0.3s; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2); text-transform: uppercase; letter-spacing: 1px; }
    .btn-update:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3); color: white; }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card-custom">
        <div class="card-header-gradient d-flex justify-content-between align-items-center">
            <h5 class="text-white m-0 fw-800"><i class="fas fa-user-edit me-2"></i> MODIFY PERSONNEL NODE</h5>
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">BACK TO LIST</a>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form id="editForm" action="{{ route('employees.update', encrypt($employees->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-5 text-center text-md-start">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                        <img src="{{ $employees->image_employee ? url('/imageEmployee/' . $employees->image_employee) : url('backend/assets/img/avatars/1.png') }}" 
                             height="130" width="130" id="target" class="img-preview">
                        <div>
                            <label for="select_image" class="btn btn-dark fw-bold px-4 rounded-pill mb-2 shadow-sm">
                                <i class="fas fa-camera me-2"></i> CHANGE PHOTO
                                <input type="file" name="image_employee" id="select_image" onchange="selectImg()" hidden accept="image/*">
                            </label>
                            <p class="text-muted mb-0 small">Node Identity: <span class="text-primary fw-bold">#{{ $employees->user_id }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="section-label sec-personal"><i class="fas fa-id-card"></i> 1. Personnel Demographics</div>
                <div class="row g-3">
                    <div class="col-md-3"><label class="col-form-label">First Name (EN)</label><input type="text" class="form-control" name="first_name" value="{{ $employees->first_name }}" required></div>
                    <div class="col-md-3"><label class="col-form-label">Last Name (EN)</label><input type="text" class="form-control" name="last_name" value="{{ $employees->last_name }}" required></div>
                    <div class="col-md-3"><label class="col-form-label">នាម (KH)</label><input type="text" class="form-control" name="first_name_kh" value="{{ $employees->first_name_kh }}"></div>
                    <div class="col-md-3"><label class="col-form-label">គោត្តនាម (KH)</label><input type="text" class="form-control" name="last_name_kh" value="{{ $employees->last_name_kh }}"></div>
                    
                    <div class="col-md-3">
                        <label class="col-form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="Male" {{ $employees->gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $employees->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="col-form-label">Date of Birth</label><input class="form-control" type="date" name="dob" value="{{ $employees->dob }}"></div>
                    <div class="col-md-2"><label class="col-form-label">Age</label><input type="number" class="form-control" name="age" value="{{ $employees->age }}"></div>
                    <div class="col-md-4"><label class="col-form-label">Phone Number</label><input type="text" class="form-control" name="phone_number" value="{{ $employees->phone_number }}"></div>
                </div>

                <div class="section-label sec-org"><i class="fas fa-sitemap"></i> 2. Organizational Deployment</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="col-form-label">Company</label>
                        <select name="company_id" class="form-select">
                            @foreach ($company as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $employees->company_id ? 'selected' : '' }}>{{ $item->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            @foreach ($branch as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $employees->branch_id ? 'selected' : '' }}>{{ $item->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label">Department</label>
                        <select id="department-select" name="department_id" class="form-control">
                            <option value="{{ $employees->department_id }}" selected>{{ $employees->department->name ?? $employees->departmentName }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="col-form-label">Position</label>
                        <select id="position-select" name="position_id" class="form-control">
                            <option value="{{ $employees->position_id }}" selected>{{ $employees->position->name ?? $employees->postionName }}</option>
                        </select>
                    </div>
                    <div class="col-md-3"><label class="col-form-label">Contract Date</label><input class="form-control" type="date" name="contract" value="{{ $employees->contract }}"></div>
                    <div class="col-md-3">
                        <label class="col-form-label">Node Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $employees->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $employees->status == 0 ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                </div>

                <div class="section-label sec-security"><i class="fas fa-shield-alt"></i> 3. Infrastructure & Security</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="credential-card shadow-sm">
                            <h6 class="fw-bold mb-3 text-primary small text-uppercase"><i class="fas fa-desktop me-2"></i>Official System</h6>
                            <div class="row g-2">
                                <div class="col-6"><label class="col-form-label">User ID</label><input type="text" class="form-control" name="official_userid" value="{{ $employees->official_userid }}"></div>
                                <div class="col-6"><label class="col-form-label">Password</label><input type="text" class="form-control pw-font" name="official_pass" value="{{ $employees->official_pass }}"></div>
                                <div class="col-12 mt-2">
                                    <label class="col-form-label">Email Domain</label>
                                    <select name="group_domain" class="form-select">
                                        <option value="tfmotors.com.kh" {{ $employees->group_domain == 'tfmotors.com.kh' ? 'selected' : '' }}>tfmotors.com.kh</option>
                                        <option value="mgmotors.com.kh" {{ $employees->group_domain == 'mgmotors.com.kh' ? 'selected' : '' }}>mgmotors.com.kh</option>
                                        <option value="taifu.com.kh" {{ $employees->group_domain == 'taifu.com.kh' ? 'selected' : '' }}>taifu.com.kh</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="credential-card shadow-sm">
                            <h6 class="fw-bold mb-3 text-success small text-uppercase"><i class="fas fa-cloud me-2"></i>Cloud & Access</h6>
                            <div class="row g-2">
                                <div class="col-6"><label class="col-form-label">M365 User</label><input type="text" class="form-control" name="m_user_id" value="{{ $employees->m_user_id }}"></div>
                                <div class="col-6"><label class="col-form-label">HR User</label><input type="text" class="form-control" name="hr_user_id" value="{{ $employees->hr_user_id }}"></div>
                                <div class="col-6"><label class="col-form-label">Extension</label><input type="text" class="form-control" name="extension_id" value="{{ $employees->extension_id }}"></div>
                                <div class="col-6"><label class="col-form-label">Access Door</label><input type="text" class="form-control" name="access_door" value="{{ $employees->access_door }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-center">
                    <button type="submit" class="btn btn-update px-5 shadow" id="btnUpdate">
                        <i class="fas fa-sync-alt me-2"></i> SYNCHRONIZE RECORD
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
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
                                // ត្រូវប្រាកដថាចាប់យក data.results ដូចក្នុង Controller
                                return { results: data.results }; 
                            },
                            cache: true
                        }
                    });
                }
            }

            initSelect2('#position-select', 'position', 'Search Positions...');
            initSelect2('#department-select', 'department', 'Search Departments...');

            $('#editForm').on('submit', function() {
                const btn = $('#btnUpdate');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> UPDATING...');
            });
        });
    })(jQuery);

    function selectImg() {
        const src = document.getElementById("select_image");
        const target = document.getElementById("target");
        if (src.files && src.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => { target.src = e.target.result; }
            reader.readAsDataURL(src.files[0]);
        }
    }
</script>

@endsection