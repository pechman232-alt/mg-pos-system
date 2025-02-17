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
            height: 25px;
            background-color: #DEAA5E;
            color: white;
        }

        #datatable thead tr th, #datatable tbody tr td{
            /* text-align: center; */
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
                                <i class='bx bx-package'></i> List of Users
                            </a>
                        </div>
                        <a href="{{ url('/users/create') }}" class="btn btn-outline-primary">Add Users</a>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>UserRole</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="avatar-wrapper">
                                                <div class="avatar me-2">
                                                    <img src="{{ url($user->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $user->profile_pic) }}" alt="Avatar" class="rounded-circle">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate">{{ $user->full_name }}</span>
                                                <small class="emp_post text-truncate text-muted">UserID: {{ $user->user_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: start">{{ $user->username }}</td>
                                    <td>{{ $user->gender }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->branchName }}</td>
                                    <td>{{ $user->departName }}</td>
                                    <td>
                                        {{ $user->positionName }}
                                    </td>
                                    <td>
                                        <span class="badge  bg-label-success">{{ $user->user_role_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge  bg-label-{{$user->status == '1' ? 'success':'warning'}}">{{ $user->status == '1' ? 'Active':'Unactive' }}</span>
                                    </td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="{{ url('/users/edit', encrypt($user->id)) }}" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                            <a onclick="return confirm('Are you sure, you want to delete?')" href="{{ url('/users/destroy', encrypt($user->id)) }}" class="btn btn-outline-secondary" style="background-color: red; color:white"><i class="tf-icons bx bx-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- {{  auth('users')->user() }} --}}
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
