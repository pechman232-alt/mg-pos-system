@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    /* Professional 4-Color Header Gradient */
    .card-header {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%) !important;
        padding: 20px 25px !important;
        border-bottom: none !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        display: block;
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

    /* --- BUTTONS --- */
    .btn-primary {
        background: #1e293b !important;
        color: white !important;
        border: none;
        font-weight: 800;
        padding: 12px 25px;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-primary:hover {
        background: #000 !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .btn-outline-secondary {
        border-radius: 10px;
        font-weight: 700;
        padding: 12px 25px;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 16px;
        text-decoration: none;
        border-radius: 8px;
    }
    .btn-back:hover { background: rgba(255, 255, 255, 0.3); color: #fff; }

    /* --- AVATAR & ICONS --- */
    #target {
        border-radius: 16px;
        object-fit: cover;
        border: 4px solid #f8fafc;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .input-group-text {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 0 10px 10px 0;
        color: #94a3b8;
    }

    /* --- SELECT2 OVERRIDES --- */
    .select2-container .select2-selection--single {
        height: 48px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 10px !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px !important;
        color: #1e293b !important;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card full-width-card">
                <div class="card-header">
                    <h5 class="header-title">
                        <i class="fas fa-user-edit me-2"></i>MODIFY PERSONNEL DOSSIER
                    </h5>
                    <a href="{{ url('/users') }}" class="btn btn-back">CANCEL</a>
                </div>

                <div class="card-body p-5">
                    <form action="{{ url('/users/update', encrypt($users->id)) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" value="{{ $users->full_name }}">
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" value="{{ $users->username }}">
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">User-ID</label>
                                <input type="text" class="form-control" name="user_id" value="{{ $users->user_id }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" @if($users->gender == 'Male') selected @endif>Male</option>
                                    <option value="Female" @if($users->gender == 'Female') selected @endif>Female</option>
                                </select>
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" name="email" value="{{ $users->email }}">
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" value="{{ $users->phone_number }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" class="form-select">
                                    @foreach ($branchs as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $users->branch_id) selected @endif>{{ $item->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select">
                                    @foreach ($departs as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $users->department_id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Position</label>
                                <input type="hidden" id="old-position-id" value="{{ old('position_id', $users->position_id ?? '') }}">
                                <select id="position-select" class="form-select" name="position_id"></select>
                            </div>
                        </div>

                        <div class="row border-bottom mb-4 pb-2">
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Date Start Work</label>
                                <input class="form-control" type="date" name="start_work_date" value="{{ $users->start_work_date }}">
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="show-password" placeholder="••••••••">
                                    <span class="input-group-text" onclick="togglePasswordVisibility()">
                                        <i class="bx bx-hide" id="toggle-icon"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4 col-md-4">
                                <label class="form-label">User Role</label>
                                <select name="role_id" class="form-select">
                                    @foreach ($userRole as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $users->role_id) selected @endif>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-4 mb-5">
                            <img src="{{ url($users->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $users->profile_pic) }}"
                                 height="110" width="110" id="target">
                            <div>
                                <label for="select_image" class="btn btn-primary btn-sm mb-2">
                                    <i class="fas fa-upload me-2"></i>Upload Photo
                                    <input type="file" name="profile_pic" id="select_image" onchange="selectImg()" hidden accept="image/*">
                                </label>
                                <p class="text-muted mb-0 small">Allowed JPEG, PNG. Max 1024K</p>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <a href="{{ url('users') }}" class="btn btn-outline-secondary">Back to Directory</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    function showImage(src, target) {

        var fr = new FileReader();

        fr.onload = function() {

            target.src = fr.result;

        }

        fr.readAsDataURL(src.files[0]);

    }



    function selectImg() {

        var src = document.getElementById("select_image");

        var target = document.getElementById("target");

        showImage(src, target);

    }

</script>



<script>

    function togglePasswordVisibility() {

        const passwordField = document.getElementById('show-password');

        const toggleIcon = document.getElementById('toggle-icon');



        if (passwordField.type === 'password') {

            passwordField.type = 'text';

            toggleIcon.classList.remove('bx-hide');

            toggleIcon.classList.add('bx-show');

        } else {

            passwordField.type = 'password';

            toggleIcon.classList.remove('bx-show');

            toggleIcon.classList.add('bx-hide');

        }

    }

</script>



{{-- function select2 --}}

<script>

    $('#position-select').select2({

        placeholder: "Select Position",

        allowClear: true,

        ajax: {

            url: "{{ url('/users/create') }}", 

            type: "GET",

            dataType: 'json',

            delay: 250, // Delay to reduce server load

            data: function (params) {

                return {

                    search: params.term // Search query

                };

            },

            processResults: function (data) {

                return {

                    results: $.map(data, function (item) {

                        return {

                            id: item.id,

                            text: item.name

                        };

                    })

                };

            },

            cache: true

        }

    });



     // Preselect the old value if it exists

    let oldPositionId = $('#old-position-id').val();

    if (oldPositionId) {

        $.ajax({

            url: "{{ url('/users/edit') }}/" + "{{ $users->id ?? '' }}", // Use the same endpoint

            type: "GET",

            data: { search: '' }, // Adjust this if needed

            success: function (data) {

                let selectedItem = data.find(item => item.id == oldPositionId);

                if (selectedItem) {

                    let option = new Option(selectedItem.name, selectedItem.id, true, true);

                    $('#position-select').append(option).trigger('change');

                }

            }

        });

    }



    // Event listener for selection

    $('#position-select').on('change', function () {

        let selectedOption = $(this).val();

        console.log('Selected option:', selectedOption);

    });

</script>



@endsection