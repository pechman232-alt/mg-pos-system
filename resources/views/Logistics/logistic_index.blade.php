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
                                <i class='bx bx-package'></i> List of Logistics
                            </a>
                        </div>

                        <button type="button" class="btn btn-success mr-1" data-bs-toggle="modal" data-bs-target="#basicModal">Import Excel</button>

                        <div class="modal fade" id="basicModal" tabindex="-1" style="display: none;" aria-hidden="true">
                            <form action="{{ url('/logistics/import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-6">
                                                <a href="{{ url('/download-template-logistic') }}" class="btn btn-outline-primary">Download Template Excel</a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col mb-6">
                                                <label for="nameBasic" class="form-label">Upload File Excel</label>
                                                <input type="file" name="file" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <a href="{{ url('/logistics/create') }}" class="btn btn-outline-primary">Add Logistics</a>
                    </div>
                </div>
            </nav>
            
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: start">Model Name</th>
                                <th style="text-align: start">Exterior Color</th>
                                <th style="text-align: start">Interior Color</th>
                                <th style="text-align: start">Model Year</th>
                                <th style="text-align: start">Vin Number</th>
                                <th style="text-align: start">Engine Number</th>
                                <th style="text-align: center">Remark</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logistics as $index => $logistic)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: start">{{ $logistic->model_name }}</td>
                                    <td style="text-align: start">{{ $logistic->exterior_color }}</td>
                                    <td style="text-align: start">{{ $logistic->interior_color }}</td>
                                    <td style="text-align: start">{{ $logistic->model_year }}</td>
                                    <td style="text-align: start">{{ $logistic->vin_number }}</td>
                                    <td style="text-align: start">{{ $logistic->engine_number }}</td>
                                    <td style="text-align: start">{{ $logistic->remark == "" ? "---":$logistic->remark }}</td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="{{ url('/logistics/edit', encrypt($logistic->id)) }}" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                            <a onclick="return confirm('Are you sure, you want to delete logistic?')" href="{{ url('/logistics/destroy', encrypt($logistic->id)) }}" class="btn btn-outline-secondary" style="background-color: red; color:white"><i class="tf-icons bx bx-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#datatable').DataTable({
            "iDisplayLength": 25
        });


        $(document).ready(function() {
            $('.offcanvasTop').click(function(e) {
                e.preventDefault();

                var id = $(this).val();
                $('#id').val(id);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection
