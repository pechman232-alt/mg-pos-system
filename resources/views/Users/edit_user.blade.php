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

        .select2-container .select2-selection--single {
            height: 40px !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding: 0.4375rem 1.875rem 0.4375rem 0.875rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.53 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0px !important;
            right: 10px !important;
            height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            height: 40px !important;
            margin-right: 30px !important;
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Users</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/users/update', encrypt($users->id)) }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Full-Name</label>
                                    <input type="text" placeholder="enter full name" class="form-control" name="full_name" value="{{ $users->full_name }}">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Username</label>
                                    <input type="text" placeholder="enter username" class="form-control" name="username" value="{{ $users->username }}">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">User-ID</label>
                                    <input type="text" placeholder="Enter UserID" class="form-control" name="user_id" value="{{ $users->user_id }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="" disabled>Gender select</option>
                                        <option value="Male" @if (old('gender', $users->gender ?? '') == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if (old('gender', $users->gender ?? '') == 'Female') selected @endif>Female</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Email</label>
                                    <input type="text" placeholder="example@gmail.com" class="form-control" name="email" value="{{ $users->email }}">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Phone Number</label>
                                    <input type="text" placeholder="+00 000 000" class="form-control" name="phone_number" value="{{ $users->phone_number }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Branch</label>
                                    <select name="branch_id" class="form-select">
                                        <option value="" disabled selected>Branch select</option>
                                        @foreach ($branchs as $item)
                                            <option @if ($item->id == $users->branch_id) selected @endif value="{{ $item->id }}" >{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="" disabled selected>Department select</option>
                                        @foreach ($departs as $item)
                                            <option @if ($item->id == $users->department_id) selected @endif value="{{ $item->id }}" >{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Position</label>
                                    <input type="hidden" id="old-position-id" value="{{ old('position_id', $users->position_id ?? '') }}">
                                    <select id="position-select" class="form-select" name="position_id">
                                        <option value="">Select Position</option>
                                        @if (old('position_id'))
                                            <option value="{{ old('position_id') }}" selected>
                                                {{ optional(App\Models\PositionModel::find(old('position_id')))->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Date Start Work</label>
                                    <input class="form-control" type="date" id="html5-date-input" name="start_work_date" value="{{ $users->start_work_date }}">
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">Passowrd</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control" id="show-password" placeholder="············" aria-describedby="show-password">
                                        <span id="basic-default-password2" class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()"><i class="bx bx-hide" id="toggle-icon"></i></span>
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="basic-default-fullname">UserRole</label>
                                    <select name="role_id" class="form-select">
                                        <option value="" disabled selected>UserRoles select</option>
                                        @foreach ($userRole as $item)
                                            <option @if ($item->id == $users->role_id) selected @endif value="{{ $item->id }}" >{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4 mt-3">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ url($users->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $users->profile_pic) }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="target">

                                    <div class="button-wrapper">
                                        <label for="select_image" class="btn btn-primary me-2 mb-1">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" name="profile_pic" id="select_image" onchange="selectImg()" hidden accept="image/png, image/jpeg">
                                        </label>
                                        <p class="text-muted mb-0">Allowed JPEG, JPG or PNG. Max size of 1024K</p>
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

    {{-- function select2 --}}
    <script>
        $('#position-select').select2({
            placeholder: "Select Position",
            allowClear: true,
            ajax: {
                url: "{{ url('/users/create') }}", 
                type: "GET",
                dataType: 'json',
                delay: 250, // Delay to reduce server load
                data: function (params) {
                    return {
                        search: params.term // Search query
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });

         // Preselect the old value if it exists
        let oldPositionId = $('#old-position-id').val();
        if (oldPositionId) {
            $.ajax({
                url: "{{ url('/users/edit') }}/" + "{{ $users->id ?? '' }}", // Use the same endpoint
                type: "GET",
                data: { search: '' }, // Adjust this if needed
                success: function (data) {
                    let selectedItem = data.find(item => item.id == oldPositionId);
                    if (selectedItem) {
                        let option = new Option(selectedItem.name, selectedItem.id, true, true);
                        $('#position-select').append(option).trigger('change');
                    }
                }
            });
        }

        // Event listener for selection
        $('#position-select').on('change', function () {
            let selectedOption = $(this).val();
            console.log('Selected option:', selectedOption);
        });
    </script>

@endsection
