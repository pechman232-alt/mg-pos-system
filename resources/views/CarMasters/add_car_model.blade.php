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
                    <h5 class="card-header">Add Car Model</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/car-models/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Model Name</label>
                                    <input type="text" placeholder="Enter model name" class="form-control @error('model_name') border-danger @enderror" name="model_name" value="{{ old('model_name') }}">
                                    @error('model_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Colors</label>
                                    <select name="color_id" class="form-select @error('color_id') border-danger @enderror">
                                        <option value="" disabled selected>--- select color ---</option>
                                        @foreach ($colors as $item)
                                            <option value="{{ $item->id }}" @if (old('color_id') == $item->id){{ 'selected' }} @endif>{{ $item->color_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('color_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/car-models') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
