@extends('layouts.homePages')
@section('content')
    <style>
        /* Table header styling */
        #datatable thead th {
            background-color: #DEAA5E;
            /* gold-like header */
            color: white;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            padding: 12px;
            border-bottom: 2px solid #cfa060;
            opacity: 0;
            transform: translateY(-10px);
            animation: fadeInHeader 0.5s forwards;
        }

        /* Animate header fade-in */
        @keyframes fadeInHeader {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Table row animation */
        #datatable tbody tr {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInRow 0.5s forwards;
        }

        /* Stagger rows for sequential animation */
        #datatable tbody tr:nth-child(1) {
            animation-delay: 0.1s;
        }

        #datatable tbody tr:nth-child(2) {
            animation-delay: 0.2s;
        }

        #datatable tbody tr:nth-child(3) {
            animation-delay: 0.3s;
        }

        #datatable tbody tr:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Add more if needed */

        /* Fade-in row keyframe */
        @keyframes fadeInRow {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover effect for rows */
        #datatable tbody tr:hover {
            background-color: #f5f5f5;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        /* Action buttons */
        .btn-outline-secondary {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Delete button specific */
        .btn-outline-secondary[style*="background-color: red"] {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary[style*="background-color: red"]:hover {
            transform: scale(1.1);
            background-color: #ff4c4c;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Optional: badge animation */
        .badge {
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.1);
        }
    </style>

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
                                                    <img src="{{ url($user->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $user->profile_pic) }}"
                                                        alt="Avatar" class="rounded-circle">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate">{{ $user->full_name }}</span>
                                                <small class="emp_post text-truncate text-muted">UserID:
                                                    {{ $user->user_id }}</small>
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
                                        <span
                                            class="badge  bg-label-{{$user->status == '1' ? 'success' : 'warning'}}">{{ $user->status == '1' ? 'Active' : 'Unactive' }}</span>
                                    </td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="{{ url('/users/edit', encrypt($user->id)) }}"
                                                class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                            <a onclick="return confirm('Are you sure, you want to delete?')"
                                                href="{{ url('/users/destroy', encrypt($user->id)) }}"
                                                class="btn btn-outline-secondary" style="background-color: red; color:white"><i
                                                    class="tf-icons bx bx-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- {{ auth('users')->user() }} --}}
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#datatable').DataTable({
            "iDisplayLength": 25
        });


        $(document).ready(function () {
            $('.offcanvasTop').click(function (e) {
                e.preventDefault();

                var id = $(this).val();
                $('#id').val(id);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection