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
                    <h5 class="card-header">Edit User Roles</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/userRoles/update', encrypt($userRole->id)) }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Name User Roles</label>
                                    <input type="text" placeholder="Enter Name User Role" class="form-control" name="name" value="{{ $userRole->name }}">
                                </div>
                            </div>

                            @foreach ($mainMenus as $mainMenu)
                                <div class="row gy-3">
                                    <div class="col-md">
                                        <small class="text-dark fw-bold">MainMenus</small>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" {{ $mainMenu->checked == '1' ? 'checked': ''}} type="checkbox" value="{{ $mainMenu->id }}" id="{{ $mainMenu->id }}" name="ch_main[]">
                                            <label class="form-check-label" for="{{ $mainMenu->id }}">{{ $mainMenu->name }}</label>
                                        </div>
                                    </div>
                                    @foreach ($subMenus as $subMenu)
                                        @if ($subMenu->sub_of == $mainMenu->id)
                                            <div class="col-md">
                                                <small class="text-light fw-normal">SubMenus</small>
                                                <div class="form-check mt-3">
                                                    <input class="form-check-input" {{ $subMenu->checked == '1' ? 'checked': '' }} type="checkbox" value="{{ $subMenu->id }}" id="{{ $subMenu->id }}" name="ch_sub[]">
                                                    <label class="form-check-label" for="{{ $subMenu->id }}">{{ $subMenu->name }}</label>
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
