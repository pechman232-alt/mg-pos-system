@extends('layouts.homePages')
@section('content')
    <style>
        .navbar.bg-light {
            /* background-color: #fde9e9 !important; */
            background-color: #fde9e9 !important;
            border-radius: 10px;
            margin-top: 15px;
            margin-left: 15px;
            margin-right: 15px;
        }

        .navbar-expand-lg .navbar-collapse {
            display: flex !important;
            flex-basis: auto;
        }
        .dataTables_filter{
            margin-bottom: 20px;
        }
        .dataTables_paginate, .dataTables_info{
            margin-top: 20px;
        }
        table#datatable{
            border: 1px solid #ccc;
        }
        table.dataTable thead>tr>th.sorting:after, table.dataTable thead>tr>th.sorting_asc:after{
            display: none;
        }
        table.dataTable thead>tr>th.sorting:before, table.dataTable thead>tr>th.sorting_asc:before{
            display: none;
        }
        #datatable thead tr th{
            height: 35px;
            background-color: #fde9e9;
        }
        #datatable thead tr th, #datatable tbody tr td{
            text-align: center;
        }
        #cls_bg_icon{
            background: #fff2d6;
            max-width: 80px;
        }

        .badge {
            vertical-align: middle;
            padding: 7px 12px;
            font-weight: 500;
            border-radius: 30px;
            font-size: 11px;
        }

        .bg_red {
            background-color: #CE181E;
        }

        .bg_yellow {
            background-color: #FAA61A;
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <nav class="navbar navbar-example navbar-expand-lg bg-light">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;"> <i class='bx bx-package'></i>List of Logistic</a>
                        </div>

                        <button type="button" class="btn btn-success mr-1" data-bs-toggle="modal" data-bs-target="#basicModal">Import Excel</button>
                        <div class="modal fade" id="basicModal" tabindex="-1" style="display: none;" aria-hidden="true">
                            <form action="{{ url('/logistics/import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel1">Logistic</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col mb-6">
                                                    <a href="{{ url('/download-template-logistic')}}" class="btn btn-outline-primary">Download Template Excel</a>
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
                        <a href="{{ url('logistics/create') }}" class="btn btn-outline-primary">Add Logistics</a>
                    </div>
                </div>
            </nav>
            <nav class="navbar navbar-example navbar-expand-lg bg-light" style="background-color: white !important;">
                <div class="container-fluid">
                    <form id="filterForm" method="GET" action="{{ url('logistics') }}">
                        <div class="input-group">
                            <select name="isSale" class="form-select bg-label-primary">
                                <option value="">status</option>
                                <option value="0" {{ request('isSale') == '0' ? 'selected' : '' }}>sold</option>
                                <option value="1" {{ request('isSale') == '1' ? 'selected' : '' }}>not yet sold</option>
                            </select>
                            <input class="form-control" type="date" id="date_from" name="date_from" value="{{ $date_from }}">
                            <input class="form-control" type="date" id="date_to" name="date_to" value="{{ $date_to }}">
                            <button type="submit" class="btn btn-success mr-1" style="width: 120px">Filter</button>
                            <button type="button" id="exportBtn" class="btn btn-danger mr-1">Export Excel</button>
                        </div>
                    </form>
                    <div class="collapse navbar-collapse" id="navbar-ex-3">
                        <div class="navbar-nav me-auto">
                            <a class="nav-item nav-link active" href="" style="font-size: 18px;"></a>
                        </div>
                        <form action="{{ route('search.logistics') }}" method="GET">
                            @csrf
                            <div class="input-group input-group-merge" style="width: 350px;">
                                <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search..." name="keyword" value="{{ request()->input('keyword') }}">
                                @if(request()->input('keyword') !="")
                                    <a class="cls_btn_clear" href="{{ url('logistics') }}">X</a>
                                @endif
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table id="posts" class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width:25px;">No</th>
                                <th style="text-align: start">Model Name</th>
                                <th style="text-align: start">Exterior Color</th>
                                <th style="text-align: start">Interior Color</th>
                                <th style="text-align: start">Model Year</th>
                                <th style="text-align: start">Vin Number</th>
                                <th style="text-align: start">Engine Number</th>
                                <th style="text-align: start">Is-Sale</th>
                                <th style="text-align: start">Remark</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($logistics->count())
                                @foreach ($logistics as $index => $item)
                                    <tr>
                                        <td>{{ $logistics->firstItem() + $index}}</td>
                                        <td style="text-align: start">{{ $item->model_name }} </td>
                                        <td style="text-align: start">{{ $item->exterior_color }}</td>
                                        <td style="text-align: start">{{ $item->interior_color }}</td>
                                        <td style="text-align: start">{{ $item->model_year }}</td>
                                        <td style="text-align: start">{{ $item->vin_number }}</td>
                                        <td style="text-align: start">{{ $item->engine_number }}</td>
                                        <td>
                                            @if ($item->is_sale == 0) 
                                                <span class="badge bg_red">sold</span>
                                            @else
                                                <span class="badge bg_yellow">not yet sold</span>
                                            @endif
                                        </td>
                                        <td style="text-align: start">{{ $item->remark == "" ? "- - - - - - - - -":$item->remark }}</td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="First group">
                                                <a href="{{ url('/logistics/edit', encrypt($item->id)) }}" class="btn btn-outline-secondary"><i class="tf-icons bx bx-edit"></i></a>
                                                <a onclick="return confirm('Are you sure, you want to delete ?')" title="Delete user" href="{{ url('/logistics/destroy', encrypt($item->id)) }}" class="btn btn-outline-secondary" style="background-color: red; color:white"><i class="tf-icons bx bx-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" style="text-align: center">
                                        Result not found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container-fluid d-flex flex-md-row flex-column justify-content-between align-items-md-center gap-1 container-p-x py-3">
                <div>
                    Showing
                    {{ $logistics->firstItem() }}
                    to
                    {{ $logistics->lastItem() }}
                    of
                    {{ $countLogistic }}
                    entries
                </div>
                <div>
                    {{ $logistics->appends(request()->input())->links('paginitaion_backend') }}
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('exportBtn').addEventListener('click', function () {
            const form = document.getElementById('filterForm');
            const oldAction = form.action;
            form.action = "{{ url('logistics/export') }}";
            form.submit();
            form.action = oldAction;
        });
    </script>
@endsection