@extends('layouts.homePages')
@section('content')

    <style>
           /* Fade-in the whole card */
           .card {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInCard 0.6s forwards;
        }

        /* Animate card header */
        .card-header {
            background-color: #DEAA5E;
            /* solid color header */
            color: white;
            font-weight: 700;
            font-size: 20px;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInHeader 0.6s forwards;
            animation-delay: 0.2s;
            transition: all 0.3s ease;
        }

        /* Input fields animation */
        .form-control {
            opacity: 0;
            transform: translateX(-20px);
            animation: fadeInInput 0.5s forwards;
            animation-delay: 0.4s;
            transition: all 0.3s ease;
        }

        /* Input focus effect */
        .form-control:focus {
            border-color: #DEAA5E;
            box-shadow: 0 0 8px rgba(222, 170, 94, 0.5);
        }

        /* Buttons animation */
        .btn {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInButton 0.5s forwards;
            animation-delay: 0.6s;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        /* Keyframes */
        @keyframes fadeInCard {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInHeader {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInInput {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInButton {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Logistics</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/logistics/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
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
                                    <input type="text" placeholder="enter exterior color" class="form-control @error('exterior_color') border-danger @enderror" name="exterior_color" value="{{ old('exterior_color') }}">
                                    @error('exterior_color')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Interior Color</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter interior color" class="form-control @error('interior_color') border-danger @enderror" name="interior_color" value="{{ old('interior_color') }}">
                                    @error('interior_color')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Model Year</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter model year" class="form-control @error('model_year') border-danger @enderror" name="model_year" value="{{ old('model_year') }}">
                                    @error('model_year')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Vin Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enter vin number" class="form-control @error('vin_number') border-danger @enderror" name="vin_number" value="{{ old('vin_number') }}">
                                    @error('vin_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Engine Number</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="enger engine number" class="form-control @error('engine_number') border-danger @enderror" name="engine_number" value="{{ old('engine_number') }}">
                                    @error('engine_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label" for="basic-default-name">Remarks</label>
                                <div class="col-sm-10">
                                    <textarea name="remark" class="form-control" placeholder="Enter remarks" style="height: 103px;"></textarea>
                                    {{-- <textarea name="remark" class="form-control" placeholder="enter remarks" aria-label="Hi, Do you have a moment to talk Joe?" aria-describedby="basic-icon-default-message2" style="height: 103px;"></textarea> --}}
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
