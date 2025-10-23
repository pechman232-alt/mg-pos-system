@extends('layouts.homePages')
@section('content')

    <style>
        /* ---------- General Image Styles ---------- */
        .rounded-circle {
            object-fit: cover;
            animation: fadeIn 0.8s ease forwards;
        }

        .my-img {
            background: #000;
            height: 200px;
            width: 200px;
            border-radius: 50%;
            border: 3px solid #DEAA5E;
            transition: transform 0.3s ease;
            animation: popIn 0.8s ease forwards;
        }

        .my-img:hover {
            transform: scale(1.05);
        }

        .my-img-2 {
            height: 70px;
            width: 70px;
            object-fit: cover;
            border-radius: 50%;
            animation: fadeIn 0.8s ease forwards;
        }

        /* ---------- Card Styles ---------- */
        .card {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.8s forwards;
            animation-delay: 0.2s;
        }

        .card-header,
        .card-footer {
            background-color: #f5e2c5;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 2rem 1.5rem;
        }

        /* ---------- Form Styles ---------- */
        .form-control {
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease forwards;
        }

        .form-control:focus {
            border-color: #DEAA5E;
            box-shadow: 0 0 0 2px rgba(222, 170, 94, 0.2);
            outline: none;
        }

        .form-select {
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease forwards;
        }

        .form-select:focus {
            border-color: #DEAA5E;
            box-shadow: 0 0 0 2px rgba(222, 170, 94, 0.2);
            outline: none;
        }

        .border-danger {
            border-color: #dc3545 !important;
        }

        label.col-form-label {
            font-weight: 500;
            color: #555;
            animation: fadeIn 0.8s ease forwards;
        }

        /* ---------- Upload Button ---------- */
        .button-wrapper label {
            cursor: pointer;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease forwards;
        }

        .button-wrapper label:hover {
            background-color: #DEAA5E;
            color: #fff;
        }

        .text-muted {
            font-size: 0.85rem;
            color: #888 !important;
            animation: fadeIn 0.8s ease forwards;
        }

        /* ---------- Buttons ---------- */
        .btn-primary {
            background-color: #DEAA5E;
            border-color: #DEAA5E;
            transition: all 0.3s ease;
            animation: popIn 0.8s forwards;
        }

        .btn-primary:hover {
            background-color: #c78f47;
            border-color: #c78f47;
        }

        .btn-outline-secondary {
            border-color: #DEAA5E;
            color: #DEAA5E;
            transition: all 0.3s ease;
            animation: popIn 0.8s forwards;
        }

        .btn-outline-secondary:hover {
            background-color: #DEAA5E;
            color: #fff;
        }

        /* ---------- Select2 Styles ---------- */
        .select2-container {
            width: 100% !important;
            animation: fadeIn 0.8s ease forwards;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .my-img {
                height: 150px;
                width: 150px;
            }
        }

        /* ---------- Animations ---------- */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


    <div class="container-xxl flex-grow-1 container-p-y">

        @include('Helps.alert')

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Employees</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/employees/store') }}" method="POST" action="/upload"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4 mt-3">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt="user-avatar"
                                        class="d-block rounded" height="100" width="100" id="target">
                                    <div class="button-wrapper">
                                        <label for="select_image" class="btn btn-primary me-2 mb-1">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" name="image_employee" id="select_image"
                                                onchange="selectImg()" hidden accept="image/png, image/jpeg">
                                        </label>
                                        <p class="text-muted mb-0">Allowed JPEG, JPG or PNG. Max size of 1024K</p>
                                        @error('image_employee')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter first name"
                                        class="form-control @error('first_name') border-danger @enderror" name="first_name"
                                        value="{{ old('first_name') }}">
                                    @error('first_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter last name"
                                        class="form-control @error('last_name') border-danger @enderror" name="last_name"
                                        value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">First Name Kh</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter first name kh"
                                        class="form-control @error('first_name_kh') border-danger @enderror"
                                        name="first_name_kh" value="{{ old('first_name_kh') }}">
                                    @error('first_name_kh')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Last Name Kh</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter last name kh"
                                        class="form-control @error('last_name_kh') border-danger @enderror"
                                        name="last_name_kh" value="{{ old('last_name_kh') }}">
                                    @error('last_name_kh')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Gender</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select gender</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Date of Birth</label>
                                <div class="col-sm-10">
                                    <input id="dob" onchange="calculateAge()"
                                        class="form-control @error('dob') border-danger @enderror" type="date"
                                        id="html5-date-input" name="dob" value="{{ old('dob') }}">
                                    @error('dob')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Age</label>
                                <div class="col-sm-10">
                                    <input id="age" type="text" disabled placeholder="age" class="form-control" name="age"
                                        value="{{ old('age') }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">User-ID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter user id"
                                        class="form-control @error('user_id') border-danger @enderror" name="user_id"
                                        value="{{ old('user_id') }}">
                                    @error('user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Positions</label>
                                <div class="col-sm-10">
                                    <input type="hidden" id="old-position-id" value="{{ old('position_id') }}">
                                    <select id="position-select" class="form-select" name="position_id">
                                        <option value="">Select Position</option>
                                        @if (old('position_id'))
                                            <option value="{{ old('position_id') }}" selected>
                                                {{ optional(App\Models\PositionModel::find(old('position_id')))->name }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('position_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Department</label>
                                <div class="col-sm-10">
                                    <input type="hidden" id="old-department-id" value="{{ old('department_id') }}">
                                    <select id="department-select" class="form-select" name="department_id">
                                        <option value="">Select Department</option>
                                        @if (old('department_id'))
                                            <option value="{{ old('department_id') }}" selected>
                                                {{ optional(App\Models\DepartmentModel::find(old('department_id')))->name }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('department_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Company</label>
                                <div class="col-sm-10">
                                    <select name="company_id"
                                        class="form-select @error('company_id') border-danger @enderror">
                                        <option value="" disabled selected>select company</option>
                                        @foreach ($company as $item)
                                            <option value="{{ $item->id }}" @if (old('company_id') == $item->id){{ 'selected' }}
                                            @endif>{{ $item->company_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Branch</label>
                                <div class="col-sm-10">
                                    <select name="branch_id"
                                        class="form-select @error('branch_id') border-danger @enderror">
                                        <option value="" disabled selected>select branch</option>
                                        @foreach ($branch as $item)
                                            <option value="{{ $item->id }}" @if (old('branch_id') == $item->id){{ 'selected' }}
                                            @endif>{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('contract') border-danger @enderror" type="date"
                                        id="html5-date-input" name="contract" value="{{ old('contract') }}">
                                    @error('contract')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Official UserID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter official userID"
                                        class="form-control @error('official_userid') border-danger @enderror"
                                        name="official_userid" value="{{ old('official_userid') }}">
                                    @error('official_userid')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Official Password</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter official password"
                                        class="form-control @error('official_pass') border-danger @enderror"
                                        name="official_pass" value="{{ old('official_pass') }}">
                                    @error('official_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Group Domain</label>
                                <div class="col-sm-10">
                                    <select name="group_domain"
                                        class="form-select @error('group_domain') border-danger @enderror">
                                        <option value="" disabled selected>select group domain</option>
                                        <option value="mgmotors.com.kh" @if (old('group_domain') == 'mgmotors.com.kh')
                                        selected @endif>mgmotors.com.kh</option>
                                        <option value="taifu.com.hk" @if (old('group_domain') == 'taifu.com.hk') selected
                                        @endif>taifu.com.hk</option>
                                        <option value="tfmotors.com.com.kh" @if (old('group_domain') == 'tfmotors.com.com.kh')
                                        selected @endif>tfmotors.com.com.kh</option>
                                    </select>
                                    @error('group_domain')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Synology UserID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter synology userid"
                                        class="form-control @error('s_user_id') border-danger @enderror" name="s_user_id"
                                        value="{{ old('s_user_id') }}">
                                    @error('s_user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Synology Password</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter synology password"
                                        class="form-control @error('s_pass') border-danger @enderror" name="s_pass"
                                        value="{{ old('s_pass') }}">
                                    @error('s_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Microsoft UserID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter microsoft userID"
                                        class="form-control @error('m_user_id') border-danger @enderror" name="m_user_id"
                                        value="{{ old('m_user_id') }}">
                                    @error('m_user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Microsoft Passoword</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter microsoft password"
                                        class="form-control @error('m_pass') border-danger @enderror" name="m_pass"
                                        value="{{ old('m_pass') }}">
                                    @error('m_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">HR UserID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter HR userID"
                                        class="form-control @error('hr_user_id') border-danger @enderror" name="hr_user_id"
                                        value="{{ old('hr_user_id') }}">
                                    @error('hr_user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">HR Password</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter HR password"
                                        class="form-control @error('hr_pass') border-danger @enderror" name="hr_pass"
                                        value="{{ old('hr_pass') }}">
                                    @error('hr_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">RAPP UserID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter rapp userID"
                                        class="form-control @error('rapp_user_id') border-danger @enderror"
                                        name="rapp_user_id" value="{{ old('rapp_user_id') }}">
                                    @error('rapp_user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">RAPP Password</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter rapp password"
                                        class="form-control @error('rapp_pass') border-danger @enderror" name="rapp_pass"
                                        value="{{ old('rapp_pass') }}">
                                    @error('rapp_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">User ID - Datasheet</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter userID - datasheet"
                                        class="form-control @error('user_id_data_sheet') border-danger @enderror"
                                        name="user_id_data_sheet" value="{{ old('user_id_data_sheet') }}">
                                    @error('user_id_data_sheet')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Password - Datasheet</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter datasheet password"
                                        class="form-control @error('data_sheet_pass') border-danger @enderror"
                                        name="data_sheet_pass" value="{{ old('data_sheet_pass') }}">
                                    @error('data_sheet_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Extension ID</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter extension id"
                                        class="form-control @error('extension_id') border-danger @enderror"
                                        name="extension_id" value="{{ old('extension_id') }}">
                                    @error('extension_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Register Passwaord</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter register password"
                                        class="form-control @error('register_pass') border-danger @enderror"
                                        name="register_pass" value="{{ old('register_pass') }}">
                                    @error('register_pass')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Activate</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('activate') border-danger @enderror" type="date"
                                        id="html5-date-input" name="activate" value="{{ old('activate') }}">
                                    @error('activate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Deactivate</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('deactivate') border-danger @enderror" type="date"
                                        id="html5-date-input" name="deactivate" value="{{ old('deactivate') }}">
                                    @error('deactivate')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter phone number"
                                        class="form-control @error('phone_number') border-danger @enderror"
                                        name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Access Door</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter access door"
                                        class="form-control @error('access_door') border-danger @enderror"
                                        name="access_door" value="{{ old('access_door') }}">
                                    @error('access_door')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Note</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter note"
                                        class="form-control @error('full_name') border-danger @enderror" name="full_name"
                                        value="{{ old('full_name') }}">
                                    @error('full_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/employees') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ------------------------ Display image ---------------------
        function showImage(src, target) {
            var fr = new FileReader();
            fr.onload = function () {
                target.src = fr.result;
            }
            fr.readAsDataURL(src.files[0]);
        }

        function selectImg() {
            var src = document.getElementById("select_image");
            var target = document.getElementById("target");
            showImage(src, target);
        }

        // ------------------------ Get Position ---------------------
        $('#position-select').select2({
            placeholder: "Select Position",
            allowClear: true,
            ajax: {
                url: "{{ url('/employees/create') }}", // Replace with the correct route
                type: "GET",
                dataType: 'json',
                delay: 250, // Delay to reduce server load
                data: function (params) {
                    return {
                        search: params.term, // Search query
                        type: 'position'

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

        let oldPositionId = $('#old-position-id').val();
        if (oldPositionId) {
            $.ajax({
                url: "{{ url('/employees/create') }}", // Use the same endpoint
                type: "GET",
                data: { search: '', type: 'position' }, // Adjust this if needed
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
        // ------------------------ Get Position ---------------------

        // ------------------------ Get Department ---------------------
        $('#department-select').select2({
            placeholder: "Select Department",
            allowClear: true,
            ajax: {
                url: "{{ url('/employees/create') }}", // Replace with the correct route
                type: "GET",
                dataType: 'json',
                delay: 250, // Delay to reduce server load
                data: function (params) {
                    return {
                        search: params.term, // Search query
                        type: 'department'

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
        // ------------------------ Get Department ---------------------

        // ------------------------ Calulator Age ------------------------
        function calculateAge() {
            const dobInput = document.getElementById('dob');
            const ageInput = document.getElementById('age');
            const dob = new Date(dobInput.value);
            const today = new Date();

            if (dobInput.value) {
                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                const dayDiff = today.getDate() - dob.getDate();

                // Adjust age if the birthday hasn't occurred yet this year
                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age--;
                }

                ageInput.value = age > 0 ? age : "Invalid Date of Birth";
            } else {
                ageInput.value = "";
            }
        }
    </script>

@endsection