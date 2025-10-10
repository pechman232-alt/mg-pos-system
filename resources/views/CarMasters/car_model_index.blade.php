@extends('layouts.homePages')
@section('content')
<style>
/* ====== CARD STYLING ====== */
.card {
    border: 1px solid #DEAA5E;
    border-radius: 16px;
    box-shadow: 0 4px 10px rgba(222, 170, 94, 0.15);
    overflow: hidden;
    background-color: #fff;
    animation: fadeInUp 0.6s ease-out;
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(222, 170, 94, 0.25);
}

/* ====== NAVBAR HEADER ====== */
.navbar.bg-light {
    background: linear-gradient(90deg, #f7f3ea, #ffffff);
    border-bottom: 1px solid #DEAA5E;
    animation: fadeInDown 0.6s ease-out;
}

.navbar-nav .nav-link {
    color: #444;
    font-weight: 600;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #DEAA5E;
}



.btn-outline-primary:hover {
    background-color: #DEAA5E;
    color: #fff;
    transform: scale(1.05);
}

/* ====== TABLE STYLE ====== */
.table-responsive {
    animation: fadeIn 0.8s ease;
}

table.display {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(222, 170, 94, 0.1);
}

table.display thead {
    background-color: #DEAA5E;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

table.display thead th {
    padding: 12px;
    font-weight: 600;
    text-align: center;
}

table.display tbody tr {
    background-color: #fff;
    transition: all 0.3s ease;
    animation: fadeRow 0.6s ease forwards;
}

table.display tbody tr:hover {
    background-color: #fef8f0;
    transform: scale(1.01);
    box-shadow: 0 2px 6px rgba(222, 170, 94, 0.1);
}

table.display tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f0e3ce;
}

/* ====== ACTION BUTTONS ====== */
.btn-outline-secondary {
    border: 1px solid #ccc;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: scale(1.1);
    border-color: #DEAA5E;
    color: #DEAA5E;
}

/* ====== ANIMATIONS ====== */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeRow {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ====== TABLE HOVER SHADOW ====== */
tr:hover td {
    transition: background 0.3s ease;
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
</script>
@endsection
