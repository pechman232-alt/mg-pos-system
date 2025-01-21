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
                    <h5 class="card-header">Edit Logistics</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('logistics/update', encrypt($logistics->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter model name" class="form-control" name="model_name" value="{{ $logistics->model_name }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Exterior Color</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter exterior color" class="form-control" name="exterior_color" value="{{ $logistics->exterior_color }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Interior Color</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter interior color" class="form-control" name="interior_color" value="{{ $logistics->interior_color }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Year</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter model year" class="form-control" name="model_year" value="{{ $logistics->model_year }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vin Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter vin number" class="form-control" name="vin_number" value="{{ $logistics->vin_number }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Engine Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enger engine number" class="form-control" name="engine_number" value="{{ $logistics->engine_number }}">
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Remarks</label>
                                <div class="col-sm-10">
                                    <textarea name="remark" class="form-control" placeholder="Enter remarks" style="height: 103px;">{{ $logistics->remark }}</textarea>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/logistics') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
