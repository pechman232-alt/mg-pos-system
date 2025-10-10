@extends('layouts.homePages')
@section('content')
    <style>
  /* Table header with solid color */
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

        /* Header fade-in animation */
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

        /* Add more if needed */

        /* Row fade-in keyframe */
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

        /* Action buttons hover effect */
        .btn-outline-secondary {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Delete button specific hover effect */
        .btn-outline-secondary[style*="background-color: red"]:hover {
            transform: scale(1.1);
            background-color: #ff4c4c;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
                                <i class='bx bx-package'></i> List of Member Card
                            </a>
                        </div>
                        <button type="button" class="btn btn-success mr-1" data-bs-toggle="modal" data-bs-target="#basicModal">Import Excel</button>
                        <div class="modal fade" id="basicModal" tabindex="-1" style="display: none;" aria-hidden="true">
                            <form action="" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Member Card</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col mb-6">
                                                <a href="" class="btn btn-outline-primary">Download Template Excel</a>
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
                        <a href="{{ url('member-cards/create') }}" class="btn btn-outline-primary">Add Member Card</a>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="datatable" class="display">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: start">Member Card</th>
                                <th>Created At</th>
                                <th>Udated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member_cards as $index => $member_card)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: start">{{ $member_card->member_card }}</td>
                                    <td>{{ $member_card->created_at }}</td>
                                    <td>{{ $member_card->updated_at }}</td>
                                    <td style="width: 100px">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            <a href="{{ url('/member-cards/edit', encrypt($member_card->id)) }}" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                            <a onclick="return confirm('Are you sure, you want to delete?')" href="{{ url('/member-cards/destroy', encrypt($member_card->id)) }}" class="btn btn-outline-secondary" style="background-color: red; color:white"><i class="tf-icons bx bx-trash"></i></a>
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
