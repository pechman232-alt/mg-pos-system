@extends('layouts.homePages')
@section('content')
    <style>
      /* Table row fade-in animation */
#datatable tbody tr {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInRow 0.5s forwards;
}

/* Stagger animation for each row */
#datatable tbody tr:nth-child(1) { animation-delay: 0.1s; }
#datatable tbody tr:nth-child(2) { animation-delay: 0.2s; }
#datatable tbody tr:nth-child(3) { animation-delay: 0.3s; }
#datatable tbody tr:nth-child(4) { animation-delay: 0.4s; }
#datatable tbody tr:nth-child(5) { animation-delay: 0.5s; }
/* Add more if needed */

@keyframes fadeInRow {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover effect for table rows */
#datatable tbody tr:hover {
    background-color: #f5f5f5;
    transform: scale(1.02);
    transition: all 0.3s ease;
}

/* Action buttons animation */
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

/* Optional: smooth badge animation */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}
/* Table header background and text color */
#datatable thead th {
    background-color: #DEAA5E; /* gold-like color */
    color: white;
    font-weight: 600;
    font-size: 16px;
    text-align: center;
    padding: 12px;
    border-bottom: 2px solid #cfa060;
    transition: all 0.3s ease;
}

/* Optional: hover effect for header cells */
#datatable thead th:hover {
    background-color: #cfa060; /* slightly darker on hover */
    cursor: default;
}

/* Align specific headers if needed */
#datatable thead th:nth-child(2),
#datatable tbody td:nth-child(2) {
    text-align: start; /* left-align "Name User Role" */
}

/* Optional: header text animation on page load */
#datatable thead th {
    opacity: 0;
    transform: translateY(-10px);
    animation: fadeInHeader 0.5s forwards;
}

@keyframes fadeInHeader {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}


    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card">
            <nav class="navbar navbar-example navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;">
                                <i class='bx bx-package'></i> List of User Roles
                            </a>
                        </div>
                        <a href="{{ url('/userRoles/create') }}" class="btn btn-outline-primary">Add User Roles</a>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: start">Name User Role</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userRoles as $index => $userRole)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: start"><span class="badge bg-label-success me-1">{{ $userRole->name }}</span></td>
                                    <td>{{ $userRole->created_at }}</td>
                                    <td>{{ $userRole->updated_at }}</td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="{{ url('/userRoles/edit', encrypt($userRole->id)) }}" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
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
