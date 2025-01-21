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
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- @include('alert') --}}

        <div class="card">
            <nav class="navbar navbar-example navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;"> 
                                <i class='bx bx-package'></i> List of Car Models
                            </a>
                        </div>
                        <a href="{{ url('/car-models/create') }}" class="btn btn-outline-primary">Add Car Model</a>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: center">Model Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carModels as $index => $carModel)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: start">{{ $carModel->model_name }}</td>
                                    <td>{{ $carModel->created_at }}</td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                            <a onclick="return confirm('Are you sure, you want to delete?')" href="" class="btn btn-outline-secondary" style="background-color: red; color:white"><i class="tf-icons bx bx-trash"></i></a>
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
