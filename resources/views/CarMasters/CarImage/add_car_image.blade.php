@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ====== BRAND IDENTITY & COLORS ====== */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --border-color: #e2e8f0;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        color: #1e293b;
        margin: 0;
    }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-upload-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: slideUp 0.5s ease-out;
    }

    .card-custom {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 40px rgba(79, 70, 229, 0.06) !important;
        background-color: #fff;
        overflow: hidden;
        width: 100%;
    }

    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important;
        color: #ffffff !important;
        padding: 1.5rem 2.5rem !important;
        font-size: 1.1rem !important;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: 0.5px;
    }

    /* ====== UPLOAD AREA ====== */
    .upload-zone {
        background: #fcfdfe;
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 3rem;
        transition: 0.3s ease;
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .upload-zone:hover {
        border-color: var(--brand-indigo);
        background: #f8faff;
        transform: scale(1.005);
    }

    #target {
        border-radius: 15px;
        object-fit: contain;
        border: 4px solid #fff;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        background: white;
        transition: 0.3s;
    }

    /* ====== FORM CONTROLS ====== */
    .form-label {
        font-weight: 700;
        color: #64748b;
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.7rem;
    }

    .form-control, .form-select {
        height: 48px !important;
        border-radius: 12px;
        border: 1.5px solid var(--border-color);
        padding: 12px 20px;
        font-size: 14px !important;
        font-weight: 600;
        transition: 0.3s;
        background-color: #f8fafc;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: var(--brand-indigo) !important;
        box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1) !important;
        outline: none;
    }

    /* ====== BUTTONS ====== */
    .btn-save {
        background: linear-gradient(135deg, var(--brand-indigo), var(--brand-purple)) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 800;
        padding: 14px 40px !important;
        border-radius: 12px;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
    }

    .btn-upload-trigger {
        background-color: var(--brand-indigo) !important;
        color: white !important;
        font-weight: 800;
        border-radius: 10px;
        padding: 12px 25px !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
    }

    .btn-cancel {
        border: 1.5px solid var(--border-color) !important;
        background-color: #fff;
        color: #64748b !important;
        font-weight: 700;
        padding: 14px 30px !important;
        border-radius: 12px;
        text-decoration: none !important;
        display: inline-block;
    }

    .btn-cancel:hover { background-color: #fef2f2; color: var(--brand-red) !important; border-color: #fecaca !important; }

    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="fluid-upload-wrapper">
    <div class="card-custom">
        <div class="card-header-gradient">
            <i class="fas fa-plus-circle"></i> ADD NEW VEHICLE ASSET
        </div>

        <div class="card-body p-4 p-lg-5">
            <form action="{{ url('/car-images/store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="upload-zone shadow-sm">
                    <img src="{{ url('backend/assets/img/avatars/image.png') }}" 
                         alt="preview" width="220" height="150" id="target">
                    
                    <div class="mt-4">
                        <label for="select_image" class="btn-upload-trigger shadow-sm">
                            <i class="fas fa-cloud-upload-alt"></i> CHOOSE VEHICLE PHOTO
                            <input type="file" name="image_name" id="select_image" onchange="selectImg()" hidden accept="image/*">
                        </label>
                        <p class="text-muted small mt-3 mb-0 fw-600">Supports: JPG, PNG, WEBP (Max: 2MB)</p>
                        @error('image_name')
                            <div class="text-danger mt-2 fw-800 small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 mb-4">
                        <label class="form-label">Vehicle Model Assignment</label>
                        <select name="model_id" class="form-select @error('model_id') border-danger @enderror">
                            <option value="" disabled selected>Select from active inventory...</option>
                            @foreach ($carModels as $item)
                                <option value="{{ $item->id }}" {{ old('model_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->model_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('model_id')
                            <div class="text-danger mt-1 fw-700 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-xl-6 mb-4">
                        <label class="form-label">Color Specification</label>
                        <select name="color_id" class="form-select @error('color_id') border-danger @enderror">
                            <option value="" disabled selected>Assign visual color theme...</option>
                            @foreach ($colors as $item)
                                <option value="{{ $item->id }}" {{ old('color_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->color_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('color_id')
                            <div class="text-danger mt-1 fw-700 small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex gap-3 justify-content-start">
                    <button type="submit" class="btn-save shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> PUBLISH TO REPOSITORY
                    </button>
                    <a href="{{ url('/car-images') }}" class="btn btn-cancel">
                        DISCARD
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // PRESERVED YOUR ORIGINAL LOGIC
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
        if(src.files && src.files[0]) {
            showImage(src, target);
        }
    }
</script>

@endsection