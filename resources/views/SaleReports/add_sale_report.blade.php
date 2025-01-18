@extends('layouts.homePages')
@section('content')

    <style>
        .rounded-circle {
            object-fit: fill;
        }

        .my-img {
            background: black;
            height: 200px;
            width: 200px;
            border-radius: 50%;
        }

        .my-img-2 {
            height: 70px;
            width: 70px;
            object-fit: fill;
        }

        .card-header, .card-footer {
            background: #f5e2c5;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 40px !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff !important;
            border: 1px solid #d9dee3 !important;
            border-radius: 0.375rem !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding: 0.4375rem 1.875rem 0.4375rem 0.875rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.53 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0px !important;
            right: 10px !important;
            height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            height: 40px !important;
            margin-right: 30px !important;
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        @include('Helps.alert')

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Sale Report</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="#" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Image</label>
                                <div class="col-sm-10">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="target">
                                        <div class="button-wrapper">
                                            <label for="select_image" class="btn btn-primary me-2 mb-1">
                                                <span class="d-none d-sm-block">Upload new photo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" name="image" id="select_image" onchange="selectImg()" hidden accept="image/png, image/jpeg">
                                            </label>
                                            <p class="text-muted mb-0">Allowed JPEG, JPG or PNG. Max size of 1024K</p>
                                            @error('image')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Branch</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select branch</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Member Card</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter member card" class="form-control @error('member_card') border-danger @enderror" name="member_card" value="{{ old('member_card') }}">
                                    @error('member_card')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Brand Name</label>
                                <div class="col-sm-10">
                                    <select name="brand_name" class="form-select @error('brand_name') border-danger @enderror">
                                        <option value="" disabled selected>select brand name</option>
                                        <option value="MG" @if (old('brand_name') == 'MG') selected @endif>MG</option>
                                        <option value="Maxus" @if (old('brand_name') == 'Maxus') selected @endif>Maxus</option>
                                    </select>
                                    @error('brand_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Customer Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter customer name" class="form-control @error('customer_name') border-danger @enderror" name="customer_name" value="{{ old('customer_name') }}">
                                    @error('customer_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter phone number" class="form-control @error('phone_number') border-danger @enderror" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
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
                                    <input id="dob" onchange="calculateAge()" class="form-control @error('dob') border-danger @enderror" type="date" id="html5-date-input" name="dob" value="{{ old('dob') }}">
                                    @error('dob')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Age</label>
                                <div class="col-sm-10">
                                    <input id="age" type="text" disabled placeholder="age" class="form-control" name="age" value="{{ old('age') }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Source Customer</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select source customer</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Call In</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Referral</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Facebook Page</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Walk In</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Personal Page</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- select logistics_id -->
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vehicle Model</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select vehicle model</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Plate Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter plate number" class="form-control @error('plate_number') border-danger @enderror" name="plate_number" value="{{ old('plate_number') }}">
                                    @error('plate_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice No</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter invoice no" class="form-control @error('invoice_no') border-danger @enderror" name="invoice_no" value="{{ old('invoice_no') }}">
                                    @error('invoice_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('invoice_date') border-danger @enderror" type="date" id="html5-date-input" name="invoice_date" value="{{ old('invoice_date') }}">
                                    @error('invoice_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Delivery Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('delivery_date') border-danger @enderror" type="date" id="html5-date-input" name="delivery_date" value="{{ old('delivery_date') }}">
                                    @error('delivery_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Follow Up</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter follow up" class="form-control @error('follow_up') border-danger @enderror" name="follow_up" value="{{ old('follow_up') }}">
                                    @error('follow_up')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Warranty</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select warranty</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Select from employee full name and position (sale) -->
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Sale Person</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select employee id</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Address</label>
                                <div class="col-sm-10">
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>select address</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Contract No</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter contract no" class="form-control @error('contract_no') border-danger @enderror" name="contract_no" value="{{ old('contract_no') }}">
                                    @error('contract_no')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('contract_date') border-danger @enderror" type="date" id="html5-date-input" name="contract_date" value="{{ old('contract_date') }}">
                                    @error('contract_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">VPC VDR</label>
                                <div class="col-sm-10">
                                    <select name="vpc_vdr" class="form-select @error('vpc_vdr') border-danger @enderror">
                                        <option value="" disabled selected>select vpc vdr</option>
                                        <option value="Both" @if (old('vpc_vdr') == 'Both') selected @endif>Both</option>
                                        <option value="N/A" @if (old('vpc_vdr') == 'N/A') selected @endif>N/A</option>
                                    </select>
                                    @error('vpc_vdr')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Offer License Plate</label>
                                <div class="col-sm-10">
                                    <select name="offer_license_plate" class="form-select @error('offer_license_plate') border-danger @enderror">
                                        <option value="" disabled selected>select offer license plate</option>
                                        <option value="Yes" @if (old('offer_license_plate') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('offer_license_plate') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('offer_license_plate') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('offer_license_plate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Offer UV Film</label>
                                <div class="col-sm-10">
                                    <select name="offer_uv_film" class="form-select @error('offer_uv_film') border-danger @enderror">
                                        <option value="" disabled selected>select offer uv film</option>
                                        <option value="Yes" @if (old('offer_uv_film') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('offer_uv_film') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('offer_uv_film') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('offer_uv_film')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Offer Floor Mat</label>
                                <div class="col-sm-10">
                                    <select name="offer_floor_mat" class="form-select @error('offer_floor_mat') border-danger @enderror">
                                        <option value="" disabled selected>select offer floor mat</option>
                                        <option value="Yes" @if (old('offer_floor_mat') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('offer_floor_mat') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('offer_floor_mat') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('offer_floor_mat')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Side Step</label>
                                <div class="col-sm-10">
                                    <select name="side_step" class="form-select @error('side_step') border-danger @enderror">
                                        <option value="" disabled selected>select side step</option>
                                        <option value="Yes" @if (old('side_step') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('side_step') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('side_step') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('side_step')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Auto Tailgate</label>
                                <div class="col-sm-10">
                                    <select name="auto_tailgate" class="form-select @error('auto_tailgate') border-danger @enderror">
                                        <option value="" disabled selected>select auto tailgate</option>
                                        <option value="Yes" @if (old('auto_tailgate') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('auto_tailgate') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('auto_tailgate') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('auto_tailgate')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Rear Spoiler</label>
                                <div class="col-sm-10">
                                    <select name="rear_spoiler" class="form-select @error('rear_spoiler') border-danger @enderror">
                                        <option value="" disabled selected>select rear spoiler</option>
                                        <option value="Yes" @if (old('rear_spoiler') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('rear_spoiler') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('rear_spoiler') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('rear_spoiler')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Certificate Engine Oil</label>
                                <div class="col-sm-10">
                                    <select name="rear_spoiler" class="form-select @error('rear_spoiler') border-danger @enderror">
                                        <option value="" disabled selected>select certificate engine oil</option>
                                        <option value="Yes" @if (old('rear_spoiler') == 'Yes') selected @endif>Yes</option>
                                        <option value="No" @if (old('rear_spoiler') == 'No') selected @endif>No</option>
                                        <option value="Yes, Not Yet Install" @if (old('rear_spoiler') == 'Yes, Not Yet Install') selected @endif>Yes, Not Yet Install</option>
                                    </select>
                                    @error('rear_spoiler')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer Oil</label>
                                <div class="col-sm-10">
                                    <select name="pro_offer_oil" class="form-select @error('pro_offer_oil') border-danger @enderror">
                                        <option value="" disabled selected>select pro offer oil</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>First</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>1</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>2</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>3</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>4</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>5</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>6</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>7</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>8</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>9</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>10</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>Completed</option>
                                        <option value="Yes" @if (old('pro_offer_oil') == 'Yes') selected @endif>N/A</option>
                                    </select>
                                    @error('pro_offer_oil')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer Oil Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('pro_offer_oil_date') border-danger @enderror" type="date" id="html5-date-input" name="pro_offer_oil_date" value="{{ old('pro_offer_oil_date') }}">
                                    @error('pro_offer_oil_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vehicle Battery IR</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('vihicle_battery') border-danger @enderror" type="date" id="html5-date-input" name="vihicle_battery" value="{{ old('vihicle_battery') }}">
                                    @error('vihicle_battery')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Price</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter price" class="form-control @error('follow_up') border-danger @enderror" name="follow_up" value="{{ old('follow_up') }}">
                                    @error('follow_up')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Remark Sa</label>
                                <div class="col-sm-10">
                                    <textarea name="remark_sa" class="form-control" placeholder="Enter remark sa" style="height: 103px;"></textarea>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Attach File</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="file" id="formFile" name="attach_file">
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/sale-reports') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!------------- Script  ------------->
    <script>
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
