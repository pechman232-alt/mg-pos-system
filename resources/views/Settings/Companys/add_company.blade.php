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

        @include('Helps.alert')

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Company</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/companys/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Company Name</label>
                                    <input type="text" placeholder="Enter Company Name" class="form-control @error('company_name') border-danger @enderror" name="company_name" value="{{ old('company_name') }}">
                                    @error('company_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/companys') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
