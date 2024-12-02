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

        @include('Helps.alert')

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Logistics</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/branchs/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Name</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter model name" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Exterior Color</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter exterior color" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Interior Color</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter interior color" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Year</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter model year" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vin Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter vin number" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Engine Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enger engine number" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Remarks</label>
                                <div class="col-sm-10">
                                    {{-- <input type="text" placeholder="enter remarks" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}"> --}}
                                    <textarea id="basic-default-message" class="form-control" placeholder="enter remarks" aria-label="Hi, Do you have a moment to talk Joe?" aria-describedby="basic-icon-default-message2" style="height: 103px;"></textarea>
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
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
