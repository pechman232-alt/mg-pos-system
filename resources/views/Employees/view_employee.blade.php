@extends('layouts.homePages')
@section('content')
    <style>
        /* === Container === */
        .container-xxl {
            margin-top: 20px;
            animation: fadeSlideIn 0.8s ease forwards;
        }

        /* === Navbar === */
        .navbar.bg-light {
            background-color: #f8f9fa !important;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* === Card Styling === */
        .card {
            background: #fff;
            border: none;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.8s ease-out forwards;
        }

        /* === Avatar === */


        .avatar:hover {
            transform: scale(1.05);
        }

        /* === Header Text === */
        .card-header h3 {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .card-header p {
            margin: 0;
            font-size: 15px;
            color: #555;
        }

        /* === View Fields === */
        .view-field {
            margin-bottom: 15px;
            position: relative;
        }

        .view-field label {
            display: block;
            font-weight: 600;
            color: #444;
            margin-bottom: 5px;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .view-field input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
            padding: 10px 14px;
            font-size: 15px;
            color: #333;
            transition: all 0.3s ease;
        }

        .view-field input:hover {
            background: #fff;
            border-color: #DEAA5E;
            box-shadow: 0 0 6px rgba(222, 170, 94, 0.4);
        }

        /* === Animation Keyframes === */
        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* === Print Mode === */
        @media print {
            body {
                background: #fff;
            }

            .navbar,
            .btn,
            .sidebar {
                display: none !important;
            }

            .card {
                box-shadow: none;
                border: 1px solid #000;
                padding: 20px;
            }

            .avatar {
                border: none;
            }

            input {
                border: none;
                background: transparent !important;
                color: #000 !important;
            }
        }

        /* === Optional: Print Button Animation === */
        @keyframes pulsePrint {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 rgba(222, 170, 94, 0.4);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 12px rgba(222, 170, 94, 0.6);
            }
        }

        /* === Employee Info Header === */
        .d-flex.align-items-center.gap-4.mb-4 {
            background: linear-gradient(135deg, #f5e2c5 0%, #fff 100%);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            animation: slideFadeHeader 0.8s ease-out forwards;
            transition: all 0.3s ease;
        }

        /* Subtle hover lift effect */
        .d-flex.align-items-center.gap-4.mb-4:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(222, 170, 94, 0.3);
        }

        /* Avatar Style */
        .avatar-use {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #DEAA5E;
            object-fit: cover;
            animation: zoomInAvatar 0.9s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.08);
            box-shadow: 0 0 12px rgba(222, 170, 94, 0.5);
        }

        /* Header Text */
        .card-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            animation: fadeInText 1s ease forwards;
        }

        .card-header p {
            color: #555;
            font-size: 15px;
            line-height: 1.4;
            margin-bottom: 5px;
            animation: fadeInText 1.2s ease forwards;
        }

        /* === Keyframes === */
        @keyframes slideFadeHeader {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomInAvatar {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInText {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


    <div class="container-xxl">
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <div class="navbar-nav me-auto">
                    <a class="nav-item nav-link active" href="" style="font-size: 18px;">
                        <i class='bx bx-package'></i> Employee Details</a>
                </div>
            </div>
        </nav>

        <div class="card">
            <!-- Avatar and Name -->
            <div class="d-flex align-items-center gap-4 mb-4">
                <img src="{{ url($employees->image_employee == '' ? 'backend/assets/img/avatars/1.png' : '/imageEmployee/' . $employees->image_employee) }}"
                    class="avatar-use" alt="Avatar">
                <div class="card-header">
                    <h3>{{ $employees->full_name }} ({{ $employees->first_name_kh }} {{ $employees->last_name_kh }})</h3>
                    <p>Gender: <b>{{ $employees->gender }}</b> | UserID: <b>{{ $employees->user_id }}</b></p>
                    <p>Age: <b>{{ $employees->age }}</b> | DOB: <b>{{ $employees->dob }}</b></p>
                    <p>Phone: <b>{{ $employees->phone_number }}</b></p>
                </div>
            </div>

            <!-- Employee Info -->
            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Position</label>
                    <input type="text" value="{{ $employees->postionName }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Department</label>
                    <input type="text" value="{{ $employees->departmentName }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Company</label>
                    <input type="text" value="{{ $employees->companyName }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Branch</label>
                    <input type="text" value="{{ $employees->branchName }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Contract Date</label>
                    <input type="text" value="{{ $employees->contract }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Group Domain</label>
                    <input type="text" value="{{ $employees->group_domain }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Official UserID</label>
                    <input type="text" value="{{ $employees->official_userid }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Official Password</label>
                    <input type="text" value="{{ $employees->official_pass }}">
                </div>
            </div>

            <!-- Continue for Synology, Microsoft, HR, RAPP, Datasheet, Extension, Register, Access Door -->
            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Synology UserID</label>
                    <input type="text" value="{{ $employees->s_user_id }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Synology Password</label>
                    <input type="text" value="{{ $employees->s_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Microsoft UserID</label>
                    <input type="text" value="{{ $employees->m_user_id }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Microsoft Password</label>
                    <input type="text" value="{{ $employees->m_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>HR UserID</label>
                    <input type="text" value="{{ $employees->hr_user_id }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>HR Password</label>
                    <input type="text" value="{{ $employees->hr_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>RAPP UserID</label>
                    <input type="text" value="{{ $employees->rapp_user_id }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>RAPP Password</label>
                    <input type="text" value="{{ $employees->rapp_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>User ID - Datasheet</label>
                    <input type="text" value="{{ $employees->user_id_data_sheet }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Password - Datasheet</label>
                    <input type="text" value="{{ $employees->data_sheet_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Extension ID</label>
                    <input type="text" value="{{ $employees->extension_id }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Register Password</label>
                    <input type="text" value="{{ $employees->register_pass }}">
                </div>
            </div>

            <div class="row g-6">
                <div class="col-md-6 view-field">
                    <label>Access Door</label>
                    <input type="text" value="{{ $employees->access_door }}">
                </div>
                <div class="col-md-6 view-field">
                    <label>Activate</label>
                    <input type="text" value="{{ $employees->activate }}">
                </div>
            </div>

            <div class="view-field">
                <label>Note</label>
                <input type="text" value="{{ $employees->note }}">
            </div>
        </div>
    </div>

    <script>
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
    </script>
@endsection