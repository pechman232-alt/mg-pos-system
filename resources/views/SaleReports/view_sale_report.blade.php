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

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Custmer information</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Customer name</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->customer_name }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Branch Name</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->brand_name }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Sale person</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->sale_person }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->phone_number }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Menber Card</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->memberCard }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Gender</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->gender }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Date of birth</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->dob }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">age</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->age }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Address</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->address }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <h5 class="card-header">Vehicle basic information</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <div style="padding-bottom: 25px">
                            <img src="{{ url($saleReports->car_image == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $saleReports->car_image) }}" alt="collapse-image" height="125" class="me-6 mb-sm-0 mb-2">
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Model Name</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->model_name }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Color</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->color_name }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Year</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->modelYear }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Vin</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->vinNumber }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Engine number</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->engineNumber }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Warranty</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->warranty }}" disabled>
                            </div>
                        </div>
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
                    </div>
                </div>

                <div class="card mb-4">
                    <h5 class="card-header">Promotion basic information</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Plate Number</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->plate_number }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Offer UV film</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->offer_uv_film }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Offer floor mat</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->offer_floor_mat }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Side Step</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->side_step }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Auto tailgate</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->auto_tailgate }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Rear Spoiler</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->rear_spoiler }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Certificate Engine Oil</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->certificate_engine_oil }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer oil </label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->pro_offer_oil }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Pro Offer Oil date</label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->pro_offer_oil_date }}" disabled>
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
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->source_id }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Number</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->contract_no }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Contract Date</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->contract_date }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice number</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->invoice_no }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Invoice Date</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->invoice_date }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Delivery Date</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->delivery_date }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Price</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->price }}" disabled>
                            </div>
                            <label class="col-sm-2 col-form-label" for="basic-default-name">Follow Up</label>
                            <div class="col">
                                <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $saleReports->follow_up }}" disabled>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="" class="btn btn-primary me-2">Attactment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
