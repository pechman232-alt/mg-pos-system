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

        .rounded-circle-custome {
            border-radius: 10% !important;
        }

    </style>

    <div class="container-xxl flex-grow-1 container-p-y">


        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Add Car Images</h5>
                    <hr class="my-0">
                    <div class="card-body">
                        <form action="{{ url('/car-images/store') }}" method="POST" action="/upload" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4 mt-3">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ url('backend/assets/img/avatars/1.png') }}" alt="user-avatar" class="rounded-circle-custome" width="120" id="target">
                                    <div class="button-wrapper">
                                        <label for="select_image" class="btn btn-primary me-2 mb-1">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" name="image_name" id="select_image" onchange="selectImg()" hidden accept="image/png, image/jpeg">
                                        </label>
                                        <p class="text-muted mb-0">Allowed JPEG, JPG or PNG. Max size of 2048K (2MB)</p>
                                        @error('image_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="basic-default-fullname">Model name</label>
                                    <select name="model_id" class="form-select @error('model_id') border-danger @enderror">
                                        <option value="" disabled selected>--- select model name ---</option>
                                        @foreach ($carModels as $item)
                                            <option value="{{ $item->id }}" @if (old('model_id') == $item->id){{ 'selected' }} @endif>{{ $item->model_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('model_id')
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
                                <a href="{{ url('/car-images') }}" class="btn btn-outline-secondary">Cancel</a>
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

@endsection
