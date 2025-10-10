@extends('layouts.homePages')
@section('content')

    <style>
        #datatable thead th {
            background-color: #DEAA5E;
            /* gold-like header */
            color: white;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            padding: 12px;
            border-bottom: 2px solid #cfa060;
            opacity: 0;
            transform: translateY(-10px);
            animation: fadeInHeader 0.5s forwards;
        }

        /* Header fade-in animation */
        @keyframes fadeInHeader {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Table row animation */
        #datatable tbody tr {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInRow 0.5s forwards;
        }

        /* Stagger rows for sequential animation */
        #datatable tbody tr:nth-child(1) {
            animation-delay: 0.1s;
        }

        #datatable tbody tr:nth-child(2) {
            animation-delay: 0.2s;
        }

        #datatable tbody tr:nth-child(3) {
            animation-delay: 0.3s;
        }

        /* Add more if needed */

        /* Row fade-in keyframe */
        @keyframes fadeInRow {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover effect for rows */
        #datatable tbody tr:hover {
            background-color: #f5f5f5;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        /* Action buttons hover effect */
        .btn-outline-secondary {
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Delete button specific hover effect */
        .btn-outline-secondary[style*="background-color: red"]:hover {
            transform: scale(1.1);
            background-color: #ff4c4c;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
