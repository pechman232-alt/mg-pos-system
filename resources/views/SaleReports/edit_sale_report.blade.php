@extends('layouts.homePages')
@section('content')
    <style>
        .navbar.bg-light {
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

        #datatable thead tr th,
        #datatable tbody tr td {
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

        body {
            background-color: #f5f6fa;
            /* light gray background for dashboard */
            font-family: 'Poppins', sans-serif;
        }

        /* Card styling */
        .card {
            background: linear-gradient(145deg, #ffffff, #f8f9fb);
            border: 1px solid #e0e4ea;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease-in-out;
            overflow: hidden;
            position: relative;
        }

        /* Subtle overlay on hover */
        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(13, 110, 253, 0.03);
            /* light blue tint */
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 12px;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .card-header {
            background-color: #f1f3f6;
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e3e6ea;
        }

        /* Body */
        .card-body {
            padding: 25px;
            background-color: rgba(255, 255, 255, 0.95);
        }

        /* Labels & Inputs */
        .col-form-label {
            font-weight: 500;
            color: #444;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #d0d7de;
            padding: 10px 12px;
            font-size: 0.95rem;
            background-color: #fff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
        }

        .row.mb-6 {
            opacity: 0;
            transform: translateY(15px);
            animation: fadeInUp 0.5s ease forwards;
        }

        .row.mb-6:nth-child(1) {
            animation-delay: 0.1s;
        }

        .row.mb-6:nth-child(2) {
            animation-delay: 0.2s;
        }

        .row.mb-6:nth-child(3) {
            animation-delay: 0.3s;
        }

        .row.mb-6:nth-child(4) {
            animation-delay: 0.4s;
        }

        .row.mb-6:nth-child(5) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            border: 1px solid #DEAA5E;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(222, 170, 94, 0.15);
            background-color: #fff;
            transition: all 0.3s ease;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                 <form action="{{ url('sale-reports/update', encrypt($saleReports->id)) }}" method="POST" action="/upload" enctype="multipart/form-data">
                    @csrf
                    {{-- done Customer information --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Customer information</h5>
                        <hr class="my-0">
                        <div class="card-body">
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Customer name</label>
                                <div class="col">
                                    <input type="text" placeholder="enter customer name" class="form-control" name="customer_name" value="{{ $saleReports->customer_name }}">
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Branch Name</label>
                                <div class="col">
                                    <select name="brand_name" class="form-select">
                                        <option value="" disabled selected>select brand name</option>
                                         <option value="MG" {{ old('brand_name', $saleReports->brand_name) == 'MG' ? 'selected' : '' }}>MG</option>
                                         <option value="Maxus" {{ old('brand_name', $saleReports->brand_name) == 'Maxus' ? 'selected' : '' }}>Maxus</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Sale person</label>
                                <div class="col">
                                    <select name="employee_id" class="form-select">
                                        <option value="" disabled selected>select employee id</option>
                                        @foreach ($employees as $item)
                                            <option @if ($item->id == $saleReports->employee_id) selected @endif value="{{ $item->id }}">{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter phone number" class="form-control" name="phone_number" value="{{ $saleReports->phone_number }}">
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Menber Card</label>
                                <div class="col">
                                    <input type="hidden" id="old-member-card" value="{{ old('member_card', $saleReports->member_card ?? '') }}">
                                    <select id="member-card-select" class="form-select" name="member_card">
                                        <option value="">Select Member Card</option>
                                        @if (old('member_card', $saleReports->member_card ?? ''))
                                            @php
                                                $selectedMember = App\Models\MemberCard::find(old('member_card', $saleReports->member_card ?? ''));
                                            @endphp
                                            @if ($selectedMember)
                                                <option value="{{ $selectedMember->id }}" selected>{{ $selectedMember->member_card }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 col-form-label" for="basic-default-name">Gender</label>
                                <div class="col">
                                    <select name="gender" class="form-select">
                                        <option value="" disabled selected>select gender name</option>
                                         <option value="Male" {{ old('gender', $saleReports->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                         <option value="Female" {{ old('gender', $saleReports->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Date of birth</label>
                                <div class="col">
                                    <input id="dob" onchange="calculateAge()" class="form-control" type="date" id="html5-date-input" name="dob" value="{{ $saleReports->dob }}">
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">age</label>
                                <div class="col">
                                    <input id="age" type="text" placeholder="age" class="form-control" name="age" value="{{ $saleReports->age }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Address</label>
                                <div class="col-sm-4">
                                    <select name="province_id" class="form-select">
                                        <option value="" disabled selected>select address id</option>
                                        @foreach ($provinces as $item)
                                            <option @if ($item->id == $saleReports->province_id) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- done Customer information end --}}
                    
                    <div class="card mb-4">
                        <h5 class="card-header">Vehicle basic information</h5>
                        <hr class="my-0">
                        <div class="card-body">

                            <div class="row mb-6">
                                <div class="col-sm-10">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <img  src="{{ url($getCarImages?->image_name ? '/imageCar/' . $getCarImages->image_name : 'backend/assets/img/avatars/image.png') }}" alt="user-avatar" class="rounded-circle-custome"  height="125" id="target"/>
                                        <div class="button-wrapper">
                                            <button type="button" class="btn btn-success mr-1" data-bs-toggle="modal" data-bs-target="#largeModal">Upload Brand Model</button>
                                            <p class="text-muted mb-0">Model Name: <span id="modelName">{{ $getCarImages?->modelName }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="image" id="carImageId">
                          
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Name</label>
                                <div class="col">
                                    <input type="text" id="modelName" placeholder="enter model name" class="form-control" name="model_name" value="{{ $getCarImages?->modelName }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Color</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $getCarImages?->colorName }}" disabled>
                                </div>
                            </div>


                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vehicle Model</label>
                                <div class="col">
                                    <input type="hidden" id="old-logistic-id" value="{{ old('logistics_id', $saleReports->logistics_id ?? '') }}">
                                    <select id="logistic-select" class="form-select" name="logistics_id">
                                        <option value="">Select Member Card</option>
                                        @if (old('logistics_id', $saleReports->logistics_id ?? ''))
                                            @php
                                                $selectedLogistic = App\Models\LogisticsModel::find(old('logistics_id', $saleReports->logistics_id ?? ''));
                                            @endphp
                                            @if ($selectedLogistic)
                                                <option value="{{ $selectedLogistic->id }}" selected>{{ $selectedLogistic->model_name }} - {{ $selectedLogistic->vin_number }} - {{ $selectedLogistic->engine_number }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 col-form-label" for="basic-default-name">Warranty</label>
                                <div class="col-sm-4">
                                    <select name="warranty_id" class="form-select">
                                        <option value="" disabled selected>select address id</option>
                                        @foreach ($warrantys as $item)
                                            <option @if ($item->id == $saleReports->warranty_id) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Engine number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->engineNumber }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Warranty</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->warranty }}" disabled>
                                </div>
                            </div> --}}

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vihicle Battery</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->vihicle_battery }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">VPC & VDR</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->vpc_vdr }}" disabled>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Battery Warranty For LeapMotor</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="warranty_leap_motor" class="form-control" name="model_name" value="{{ $saleReports->warranty_leap_motor }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">Promotion basic information</h5>
                        <hr class="my-0">
                        <div class="card-body">
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Plate Number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->plate_number }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Offer UV film</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->offer_uv_film }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Offer floor mat</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->offer_floor_mat }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Side Step</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->side_step }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Auto tailgate</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->auto_tailgate }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Rear Spoiler</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->rear_spoiler }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Certificate Engine Oil</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->certificate_engine_oil }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer oil </label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->pro_offer_oil }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer Oil date</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->pro_offer_oil_date }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Remark SA</label>
                                <div class="col">
                                    <textarea class="form-control" placeholder="feedback command" style="height: 103px;" disabled>{{ $saleReports->remark_sa }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">Case information</h5>
                        <hr class="my-0">
                        <div class="card-body">
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Source</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->source_id }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->contract_no }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Date</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->contract_date }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->invoice_no }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice Date</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->invoice_date }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Delivery Date</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->delivery_date }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Price</label>
                                <div class="col">
                                    <input type="text" placeholder="price" class="form-control" name="model_name"
                                        value="{{ $saleReports->price }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Follow Up</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="follow_up" value="{{ $saleReports->follow_up }}" disabled>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Feedback Command</label>
                                <div class="col-sm-4">
                                    <textarea class="form-control" name="feedback_command" placeholder="feedback command" style="height: 103px;">{{ $saleReports->feedback_command }}</textarea>
                                    {{-- <button type="button" class="btn bg-label-info mt-1" data-bs-toggle="modal" data-bs-target="#modalCenter"><i class="tf-icons bx bx-edit"></i></button> --}}
                                </div>
                            </div>
                             
                            <div class="row mb-6 branch-animate"> 
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Attach File</label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="file" id="formFile" name="attach_file"
                                        accept="application/pdf">
                                </div>
                            </div>

                            <div class="row mb-6 branch-animate"> 
                                <div class="mt-5">
                                    <button type="submit" class="btn btn-primary me-2">Update</button>
                                    <a href="{{ url('/sale-reports') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="modalCenter" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Edit Feedback Command</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-6">
                            <label for="nameWithTitle" class="form-label">Feedback Command</label>
                            <textarea class="form-control" placeholder="feedback command" style="height: 103px;">{{ $saleReports->feedback_command }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="largeModal" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel3">Model Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive text-nowrap">
                        <table id="datatable" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th style="text-align: start">Model Name</th>
                                    <th style="text-align: start">Colors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carImages as $index => $carImage)
                                    <tr class="select-image"
                                        data-id="{{ $carImage->id }}"
                                        data-image="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}"
                                        data-model="{{ $carImage->modelName }}">
                                        <td style="text-align: center; width: 20px">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            <img src="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}" alt="image" class="rounded-circle-custome" width="100">
                                        </td>
                                        <td style="text-align: start">
                                            {{ $carImage->modelName }}
                                        </td>
                                        <td style="text-align: start">
                                            {{ $carImage->colorName }}
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#member-card-select').select2({
            placeholder: "Select Member Card",
            allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}", 
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: 'memberCard',
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.member_card
                            };
                        })
                    };
                },
                cache: true
            }
        });

        let oldMemberCard = $('#old-member-card').val();
        if (oldMemberCard) {
            $.ajax({
                url: "{{ url('/sale-reports/create') }}",
                type: "GET",
                data: { type: 'memberCard', search: '' },
                success: function (data) {
                    let selectedItem = data.find(item => item.id == oldMemberCard);
                    if (selectedItem) {
                        let option = new Option(selectedItem.member_card, selectedItem.id, true, true);
                        $('#member-card-select').append(option).trigger('change');
                    }
                }
            });
        }

        $('#logistic-select').select2({
            placeholder: "Select Logistic",
            allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}", // Replace with the correct route
                type: "GET",
                dataType: 'json',
                delay: 250, // Delay to reduce server load
                data: function (params) {
                    return {
                        search: params.term, // Search query
                        type: 'logistic'

                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.model_name + ' - ' + item.vin_number + ' - ' + item.engine_number
                            };
                        })
                    };
                },
                cache: true
            }
        });

        let oldLogisticId = $('#old-logistic-id').val();
        if (oldLogisticId) {
            $.ajax({
                url: "{{ url('/sale-reports/create') }}", // Use the same endpoint
                type: "GET",
                data: { search: '', type: 'logistic' }, // Adjust this if needed
                success: function (data) {
                    let selectedItem = data.find(item => item.id == oldLogisticId);
                    if (selectedItem) {
                        let optionText = selectedItem.model_name + ' - ' + selectedItem.vin_number + ' - ' + selectedItem.engine_number;
                        let option = new Option(optionText, selectedItem.id, true, true);
                        $('#logistic-select').append(option).trigger('change');
                    }
                }
            });
        }

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

        $('#datatable').DataTable({
            "iDisplayLength": 25
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".select-image").forEach(row => {
                row.addEventListener("click", function () {
                    let imageUrl = this.getAttribute("data-image");
                    let modelName = this.getAttribute("data-model");
                    let imageId = this.getAttribute("data-id");

                    document.getElementById("target").src = imageUrl;
                    document.getElementById("modelName").innerText = modelName;
                    document.getElementById("carImageId").value = imageId;

                    // Close the modal after selection
                    let modal = document.getElementById('largeModal');
                    let modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            });
        });
    </script>
@endsection