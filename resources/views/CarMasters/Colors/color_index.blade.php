@extends('layouts.homePages')
@section('content')
    <style>
     .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        animation: fadeInUp 0.8s ease-in-out;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
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

    /* ====== Header & Footer ====== */
    .card-header, .card-footer {
        background: #f5e2c5;
        font-weight: 600;
        color: #333;
        animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ====== Form Styling ====== */
    form {
        animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    label.form-label {
        font-weight: 600;
        color: #444;
        transition: color 0.3s ease;
    }

    label.form-label:hover {
        color: #DEAA5E;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ccc;
        padding: 10px;
        transition: all 0.3s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: #DEAA5E;
        box-shadow: 0 0 6px rgba(222, 170, 94, 0.5);
        transform: scale(1.01);
    }

    /* ====== Buttons ====== */
    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #DEAA5E;
        border-color: #DEAA5E;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #c8944b;
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(222, 170, 94, 0.4);
    }

    .btn-outline-secondary:hover {
        background-color: #DEAA5E;
        color: #fff;
        transform: scale(1.05);
    }

    /* ====== Image Style (Optional Future Use) ====== */
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
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- @include('alert') --}}

        <div class="card">
            <nav class="navbar navbar-example navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;"> 
                                <i class='bx bx-package'></i> List of Colors
                            </a>
                        </div>
                        <a href="{{ url('/colors/create') }}" class="btn btn-outline-primary">Add Colors</a>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: center">Color Name</th>
                                <th style="text-align: start">Code</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colors as $index => $color)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: start">{{ $color->color_name }}</td>
                                    <td style="text-align: start">{{ $color->code }}</td>
                                    <td>{{ $color->created_at }}</td>
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
