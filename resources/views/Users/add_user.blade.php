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
                    <h5 class="card-header">Add Users</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/users/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Full-Name</label>
                                    <input type="text" placeholder="Enter full-name" class="form-control @error('full_name') border-danger @enderror" name="full_name" value="{{ old('full_name') }}">
                                    @error('full_name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Username</label>
                                    <input type="text" placeholder="Enter username" class="form-control @error('username') border-danger @enderror" name="username" value="{{ old('username') }}">
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">User-ID</label>
                                    <input type="text" placeholder="Enter UserID" class="form-control @error('user_id') border-danger @enderror" name="user_id" value="{{ old('user_id') }}">
                                    @error('user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Gender</label>
                                    <select name="gender" class="form-select @error('gender') border-danger @enderror">
                                        <option value="" disabled selected>Gender select</option>
                                        <option value="Male" @if (old('gender') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender') == 'Female') selected @endif>Female</option>
                                    </select>
                                    @error('gender')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Email</label>
                                    <input type="text" placeholder="example@gmail.com" class="form-control @error('email') border-danger @enderror" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Phone Number</label>
                                    <input type="text" placeholder="+00 000 000" class="form-control @error('phone_number') border-danger @enderror" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Branch</label>
                                    <select name="branch_id" class="form-select @error('branch_id') border-danger @enderror">
                                        <option value="" disabled selected>Branch select</option>
                                        @foreach ($branchs as $item)
                                            <option value="{{ $item->id }}" @if (old('branch_id') == $item->id){{ 'selected' }} @endif>{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Department</label>
                                    <select name="department_id" class="form-select @error('department_id') border-danger @enderror">
                                        <option value="" disabled selected>Department select</option>
                                        @foreach ($departs as $item)
                                            <option value="{{ $item->id }}" @if (old('department_id') == $item->id){{ 'selected' }} @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Position</label>
                                    <select name="position_id" class="form-select @error('position_id') border-danger @enderror">
                                        <option value="" disabled selected>Position select</option>
                                        @foreach ($positions as $item)
                                            <option value="{{ $item->id }}" @if (old('position_id') == $item->id){{ 'selected' }} @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Date Start Work</label>
                                    <input class="form-control @error('start_work_date') border-danger @enderror" type="date" id="html5-date-input" name="start_work_date" value="{{ old('start_work_date') }}">
                                    @error('start_work_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Passowrd</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control @error('password') border-danger @enderror" id="show-password" placeholder="············" aria-describedby="show-password">
                                        <span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()"><i class="bx bx-hide" id="toggle-icon"></i></span>
                                    </div>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">UserRole</label>
                                    <select name="user_role_id" class="form-select @error('user_role_id') border-danger @enderror">
                                        <option value="" disabled selected>UserRoles select</option>
                                        @foreach ($userRole as $item)
                                            <option value="{{ $item->id }} @if (old('user_role_id') == $item->id){{ 'selected' }} @endif">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_role_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 mt-3">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="target">
                                    <div class="button-wrapper">
                                        <label for="select_image" class="btn btn-primary me-2 mb-1">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" name="profile_pic" id="select_image" onchange="selectImg()" hidden accept="image/png, image/jpeg">
                                        </label>
                                        <p class="text-muted mb-0">Allowed JPEG, JPG or PNG. Max size of 1024K</p>
                                        @error('profile_pic')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save</button>
                                <a href="{{ url('users') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(src, target) {
            var fr = new FileReader();
            fr.onload = function() {
                target.src = fr.result;
            }
            fr.readAsDataURL(src.files[0]);
        }

        function selectImg() {
            var src = document.getElementById("select_image");
            var target = document.getElementById("target");
            showImage(src, target);
        }
    </script>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('show-password');
            const toggleIcon = document.getElementById('toggle-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }
    </script>

@endsection
