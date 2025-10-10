@extends('layouts.homePages')
@section('content')

    <style>
    /* ====== Card Styling & Animation ====== */
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
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Colors</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/colors/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Color Name</label>
                                    <input type="text" placeholder="Enter color name" class="form-control @error('color_name') border-danger @enderror" name="color_name" value="{{ old('color_name') }}">
                                    @error('color_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Code</label>
                                    <input type="text" placeholder="Enter code" class="form-control @error('code') border-danger @enderror" name="code" value="{{ old('code') }}">
                                    @error('code')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/colors') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
