@extends('layouts.homePages')
@section('content')

    <style>
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

        .card-header, .card-footer {
            background: #f5e2c5;
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Edit Branch</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('branch/update', encrypt($branch->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Branch Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter branch" class="form-control" name="branch_name" value="{{ $branch->branch_name }}">
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/branchs') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection