@extends('layouts.homePages')
@section('content')

    <style>
        /* Fade in the whole card */
        .card {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInCard 0.6s forwards;
        }

        /* Animate the card header */
        .card-header {
            opacity: 0;
            transform: translateY(-10px);
            animation: fadeInHeader 0.6s forwards;
            animation-delay: 0.2s;
        }

        /* Form inputs fade in */
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

        /* Checkbox row animation */
        .row.gy-3 {
            opacity: 0;
            transform: translateX(20px);
            animation: fadeInRow 0.5s forwards;
        }

        /* Staggered animation for each row */
        .row.gy-3:nth-child(1) {
            animation-delay: 0.6s;
        }

        .row.gy-3:nth-child(2) {
            animation-delay: 0.7s;
        }

        .row.gy-3:nth-child(3) {
            animation-delay: 0.8s;
        }

        .row.gy-3:nth-child(4) {
            animation-delay: 0.9s;
        }

        /* Add more if needed */

        /* Buttons animation */
        .btn {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInButton 0.5s forwards;
            animation-delay: 1.0s;
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

        @keyframes fadeInRow {
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

        /* Card header styling */
        .card-header {
            background: linear-gradient(90deg, #DEAA5E, #F5C16C);
            color: white;
            font-size: 20px;
            font-weight: 700;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInHeader 0.6s forwards;
            animation-delay: 0.2s;
            transition: all 0.3s ease;
        }

        /* Hover effect for header */
        .card-header:hover {
            background: linear-gradient(90deg, #F5C16C, #DEAA5E);
            cursor: default;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        /* Keyframe for header fade-in */
        @keyframes fadeInHeader {
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
                    <h5 class="card-header">Add User Roles</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/userRoles/store') }}" method="POST" action="/upload"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Name User Roles</label>
                                    <input type="text" placeholder="Enter Name User Role"
                                        class="form-control @error('name') border-danger @enderror" name="name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            @foreach ($mainMenus as $mainMenu)
                                <div class="row gy-3">
                                    <div class="col-md">
                                        <small class="text-dark fw-bold">MainMenus</small>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" value="{{ $mainMenu->id }}"
                                                id="{{ $mainMenu->id }}" name="ch_main[]">
                                            <label class="form-check-label"
                                                for="{{ $mainMenu->id }}">{{ $mainMenu->name }}</label>
                                        </div>
                                    </div>
                                    @foreach ($subMenus as $subMenu)
                                        @if ($subMenu->sub_of == $mainMenu->id)
                                            <div class="col-md">
                                                <small class="text-light fw-normal">SubMenus</small>
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" type="checkbox" value="{{ $subMenu->id }}"
                                                        id="{{ $subMenu->id }}" name="ch_sub[]">
                                                    <label class="form-check-label"
                                                        for="{{ $subMenu->id }}">{{ $subMenu->name }}</label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('/userRoles') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection