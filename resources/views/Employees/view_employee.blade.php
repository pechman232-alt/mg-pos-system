@extends('layouts.homePages')
@section('content')
    <style>
        .navbar.bg-light {
            /* background-color: #fde9e9 !important; */
            background-color: #f5e2c5 !important;
            border-radius: 10px;
            margin-top: 15px;
            margin-left: 15px;
            margin-right: 15px;
        }

        .navbar-expand-lg .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }

        .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_paginate,
        .dataTables_info {
            margin-top: 20px;
        }

        table#datatable {
            border: 1px solid #ccc;
        }

        table.dataTable thead>tr>th.sorting:after,
        table.dataTable thead>tr>th.sorting_asc:after {
            display: none;
        }

        table.dataTable thead>tr>th.sorting:before,
        table.dataTable thead>tr>th.sorting_asc:before {
            display: none;
        }

        #datatable thead tr th {
            height: 35px;
            background-color: #DEAA5E;
            color: white;
        }

        #datatable thead tr th, #datatable tbody tr td{
            text-align: center;
        }

        .mr-1 {
            margin-right: 0.25rem !important; 
        }
        .mr-2 {
            margin-right: 0.45rem !important; 
        }

        .btn-outline-import {
            color: #696cff;
            border-color: #696cff;
            background: transparent;
        }
       
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <nav class="navbar navbar-example navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;"> 
                                <i class='bx bx-package'></i> Employee Details
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="card mb-6">
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                        <img src="{{ url($employees->image_employee == '' ? 'backend/assets/img/avatars/1.png' : '/imageEmployee/' . $employees->image_employee) }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                        <div class="card-header">
                            <h3 class="mb-1">{{ $employees->full_name }} ({{ $employees->first_name_kh }} {{ $employees->last_name_kh }})</h3>
                            <p class="my-0 card-subtitle">Gender: <b>{{ $employees->gender }}</b> | UserID: <b>{{ $employees->user_id }}</b> </p>
                            <p class="my-0 card-subtitle">Age: <b>{{ $employees->age }}</b> | Date of Birth: <b>{{ $employees->dob }}</b> </p>
                            <p class="my-0 card-subtitle">Phone Number: <b>{{ $employees->phone_number }}</b> </p>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Position</label>
                            <input class="form-control" type="text" value="{{ $employees->postionName }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Department</label>
                            <input class="form-control" type="text" value="{{ $employees->departmentName }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Company</label>
                            <input class="form-control" type="text" value="{{ $employees->companyName }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Branch</label>
                            <input class="form-control" type="text" value="{{ $employees->branchName }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Contract Date</label>
                            <input class="form-control" type="text" value="{{ $employees->contract }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Group Domain</label>
                            <input class="form-control" type="text" value="{{ $employees->group_domain }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Official UserID</label>
                            <input class="form-control" type="text" value="{{ $employees->official_userid }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Official Password</label>
                            <input class="form-control" type="text" value="{{ $employees->official_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Synology UserID</label>
                            <input class="form-control" type="text" value="{{ $employees->s_user_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Synology Password</label>
                            <input class="form-control" type="text" value="{{ $employees->s_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Microsoft UserID</label>
                            <input class="form-control" type="text" value="{{ $employees->m_user_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Microsoft Passoword</label>
                            <input class="form-control" type="text" value="{{ $employees->m_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">HR UserID</label>
                            <input class="form-control" type="text" value="{{ $employees->hr_user_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">HR Password</label>
                            <input class="form-control" type="text" value="{{ $employees->hr_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">RAPP UserID</label>
                            <input class="form-control" type="text" value="{{ $employees->rapp_user_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">RAPP Password</label>
                            <input class="form-control" type="text" value="{{ $employees->rapp_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">User ID - Datasheet</label>
                            <input class="form-control" type="text" value="{{ $employees->user_id_data_sheet }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Password - Datasheet</label>
                            <input class="form-control" type="text" value="{{ $employees->data_sheet_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Extension ID</label>
                            <input class="form-control" type="text" value="{{ $employees->extension_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Register Passwaord</label>
                            <input class="form-control" type="text" value="{{ $employees->register_pass }}" disabled>
                        </div>
                    </div>

                    <div class="row g-6">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Access Door</label>
                            <input class="form-control" type="text" value="{{ $employees->access_door }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Activate</label>
                            <input class="form-control" type="text" value="{{ $employees->activate }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="firstName" class="form-label">Note</label>
                        <input class="form-control" type="text" value="{{ $employees->note }}" disabled>
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
@endsection
