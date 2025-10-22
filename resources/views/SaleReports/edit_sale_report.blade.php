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
                 <form action="{{ url('sale-reports/update', encrypt($logistics->id)) }}" method="POST" action="/upload" enctype="multipart/form-data">
                    @csrf
                    <div class="card mb-4">
                        <h5 class="card-header">Customer information</h5>
                        <hr class="my-0">
                        <div class="card-body">
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Customer name</label>
                                <div class="col">
                                    <input type="text" placeholder="enter customer name" class="form-control" name="customer_name" value="{{ $saleReports->customer_name }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Branch Name</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->brand_name }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Sale person</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->sale_person }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->phone_number }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Menber Card</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->memberCard }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Gender</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->gender }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Date of birth</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->dob }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">age</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->age }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Address</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->address }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <h5 class="card-header">Vehicle basic information</h5>
                        <hr class="my-0">
                        <div class="card-body">
                            <div style="padding-bottom: 25px">
                                <img src="{{ url($saleReports->car_image == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $saleReports->car_image) }}"
                                    alt="collapse-image" height="125" class="me-6 mb-sm-0 mb-2">
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Name</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->model_name }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Color</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->color_name }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Year</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->modelYear }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vin</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->vinNumber }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Engine number</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->engineNumber }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Warranty</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->warranty }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vihicle Battery</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->vihicle_battery }}" disabled>
                                </div>
                                <label class="col-sm-2 col-form-label" for="basic-default-name">VPC & VDR</label>
                                <div class="col">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name"
                                        value="{{ $saleReports->vpc_vdr }}" disabled>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Battery Warranty For LeapMotor</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="warranty_leap_motor" class="form-control" name="model_name"
                                        value="{{ $saleReports->warranty_leap_motor }}" disabled>
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
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->follow_up }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Feedback Command</label>
                                <div class="col-sm-4">
                                    <textarea class="form-control" name="feedback_command" placeholder="feedback command" style="height: 103px;" disabled>{{ $saleReports->feedback_command }}</textarea>
                                    <button type="button" class="btn bg-label-info mt-1" data-bs-toggle="modal" data-bs-target="#modalCenter"><i class="tf-icons bx bx-edit"></i></button>
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

    <div class="modal fade" id="modalCenter" tabindex="-1" style="display: none;" aria-hidden="true">
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
    </div>
@endsection