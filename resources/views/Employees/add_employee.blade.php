@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root {
        --brand-primary: #DEAA5E;
        --brand-dark: #1e293b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --radius-lg: 20px;
        --radius-md: 12px;
    }

    body { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; background-color: var(--bg-light); color: #334155; }
    
    /* Card Styling */
    .card-custom { border: none; border-radius: var(--radius-lg); box-shadow: 0 10px 30px rgba(0,0,0,0.04); background: #fff; overflow: hidden; margin-bottom: 2rem; }
    .card-header-custom { background: var(--brand-dark); padding: 1.5rem 2rem; border: none; }
    .card-header-custom h5 { margin: 0; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px; font-size: 1.1rem; letter-spacing: 0.5px; }

    /* Section Divider */
    .section-header { font-size: 0.85rem; font-weight: 800; color: var(--brand-dark); margin: 2.5rem 0 1.2rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 10px; text-transform: uppercase; }
    .section-header i { color: var(--brand-primary); font-size: 1rem; }

    /* Avatar Section */
    .avatar-upload-wrapper { text-align: center; padding: 2rem; background: #fff; border: 2px dashed var(--border-color); border-radius: var(--radius-md); margin-bottom: 2rem; transition: 0.3s; }
    .avatar-upload-wrapper:hover { border-color: var(--brand-primary); background: #fcfdfe; }
    .avatar-preview { width: 130px; height: 130px; border-radius: 40px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 8px 20px rgba(0,0,0,0.08); margin-bottom: 1rem; }
    .btn-upload { background: var(--brand-dark); color: #fff !important; border-radius: 10px; padding: 8px 20px; cursor: pointer; font-weight: 700; font-size: 0.75rem; display: inline-block; transition: 0.3s; }
    .btn-upload:hover { background: var(--brand-primary); transform: translateY(-2px); }

    /* Form Controls */
    .label-custom { font-weight: 700; color: #64748b; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .form-control, .form-select, .select2-container--default .select2-selection--single {
        border: 1.5px solid var(--border-color) !important; border-radius: 10px !important; padding: 0.65rem 1rem !important; 
        height: auto !important; background-color: #fff !important; font-weight: 600; font-size: 0.85rem; transition: 0.3s;
    }
    .form-control:focus { border-color: var(--brand-primary) !important; box-shadow: 0 0 0 4px rgba(222, 170, 94, 0.1) !important; outline: none; }

    /* Account Cards */
    .account-card { background: #f8fafc; border-radius: var(--radius-md); padding: 1.25rem; border: 1.5px solid var(--border-color); height: 100%; transition: 0.3s; }
    .account-card:hover { background: #fff; border-color: var(--brand-primary); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
    .account-card label { font-weight: 800; font-size: 0.72rem; color: var(--brand-primary); margin-bottom: 12px; display: block; }

    /* Actions */
    .form-actions { margin-top: 3.5rem; padding-top: 1.5rem; border-top: 1.5px solid var(--border-color); display: flex; gap: 15px; }
    .btn-save { background: var(--brand-primary); color: white; border: none; padding: 1rem 3rem; border-radius: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; flex-grow: 1; }
    .btn-save:hover { background: #cfa060; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(222, 170, 94, 0.25); }
    .btn-cancel { background: #f1f5f9; color: #64748b; padding: 1rem 2rem; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.3s; }
    .btn-cancel:hover { background: #e2e8f0; color: #334155; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card-custom shadow-sm">
        <div class="card-header-custom">
            <h5><i class='fas fa-user-plus'></i> REGISTER NEW PERSONNEL INTELLIGENCE</h5>
        </div>
        <div class="card-body p-4 p-md-5">
            <form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="avatar-upload-wrapper">
                    <img id="target" src="{{ url('backend/assets/img/avatars/1.png') }}" class="avatar-preview">
                    <div>
                        <label for="select_image" class="btn-upload shadow-sm">
                            <i class='fas fa-camera me-2'></i> UPLOAD PROFILE PHOTO
                            <input type="file" name="image_employee" id="select_image" onchange="selectImg()" hidden accept="image/*">
                        </label>
                        <p class="text-muted small mt-2 mb-0 fw-600">Recomended: Square JPG/PNG (Max 2MB)</p>
                    </div>
                </div>

                <div class="section-header"><i class='fas fa-id-card'></i> 1. Personal Identity</div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="label-custom">First Name (EN)</label>
                        <input type="text" name="first_name" class="form-control" placeholder="John" required>
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">Last Name (EN)</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Doe" required>
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">នាម (KH)</label>
                        <input type="text" name="first_name_kh" class="form-control" placeholder="នាម">
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">គោត្តនាម (KH)</label>
                        <input type="text" name="last_name_kh" class="form-control" placeholder="គោត្តនាម">
                    </div>
                    <div class="col-md-2">
                        <label class="label-custom">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="label-custom">Age</label>
                        <input type="number" name="age" class="form-control" placeholder="00">
                    </div>
                    <div class="col-md-4">
                        <label class="label-custom">Date of Birth</label>
                        <input type="date" name="dob" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="label-custom">Employee ID (UID)</label>
                        <input type="text" name="user_id" class="form-control" placeholder="TF-001" required>
                    </div>
                </div>

                <div class="section-header"><i class='fas fa-sitemap'></i> 2. Organizational Deployment</div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="label-custom">Company Entity</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select Entity</option>
                            @foreach ($company as $item) <option value="{{ $item->id }}">{{ $item->company_name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="label-custom">Work Branch</label>
                        <select name="branch_id" class="form-select" required>
                            <option value="">Select Branch</option>
                            @foreach ($branch as $item) <option value="{{ $item->id }}">{{ $item->branch_name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="label-custom">Department</label>
                        <select id="department-select" name="department_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="label-custom">Functional Position</label>
                        <select id="position-select" name="position_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="label-custom">Contract Initiation Date</label>
                        <input type="date" name="contract" class="form-control">
                    </div>
                </div>

                <div class="section-header"><i class='fas fa-key'></i> 3. System Access Control</div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="account-card shadow-sm">
                            <label><i class="fas fa-desktop me-2"></i>Official Core System</label>
                            <input type="text" name="official_userid" placeholder="User ID" class="form-control mb-2">
                            <input type="text" name="official_pass" placeholder="Initial Password" class="form-control text-danger">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="account-card shadow-sm">
                            <label><i class="fas fa-envelope me-2"></i>Microsoft 365 / Email</label>
                            <input type="text" name="m_user_id" placeholder="M365 Account" class="form-control mb-2">
                            <input type="text" name="m_pass" placeholder="M365 Password" class="form-control text-danger">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="account-card shadow-sm">
                            <label><i class="fas fa-server me-2"></i>Synology NAS Storage</label>
                            <input type="text" name="s_user_id" placeholder="NAS Username" class="form-control mb-2">
                            <input type="text" name="s_pass" placeholder="NAS Password" class="form-control text-danger">
                        </div>
                    </div>
                </div>

                <div class="section-header"><i class='fas fa-phone-alt'></i> 4. Infrastructure & Contact</div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="label-custom">Mobile Number</label>
                        <input type="text" name="phone_number" class="form-control" placeholder="+855...">
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">Internal Extension</label>
                        <input type="text" name="extension_id" class="form-control" placeholder="EXT-000">
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">VoIP Register Pass</label>
                        <input type="text" name="register_pass" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">Access Door ID</label>
                        <input type="text" name="access_door" class="form-control" placeholder="Card Number">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save shadow" id="submitBtn">
                        <i class='fas fa-check-circle me-2'></i> REGISTER EMPLOYEE DOSSIER
                    </button>
                    <a href="{{ url('/employees') }}" class="btn-cancel shadow-sm">CANCEL</a>
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
                                return { results: data.results }; 
                            },
                            cache: true
                        }
                    });
                }
            }

            // Execute Initialization
            initSelect2('#position-select', 'position', '--- Search Positions ---');
            initSelect2('#department-select', 'department', '--- Search Departments ---');

            // Handle Form Submission Styling
            $('#employeeForm').on('submit', function() {
                const btn = $('#submitBtn');
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin me-2"></i> ARCHIVING DATA...');
            });
        });
    })(jQuery);

    // Image Preview Handler
    function selectImg() {
        const src = document.getElementById("select_image");
        const target = document.getElementById("target");
        if (src.files && src.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                target.src = e.target.result;
            };
            reader.readAsDataURL(src.files[0]);
        }
    }
</script>

@endsection