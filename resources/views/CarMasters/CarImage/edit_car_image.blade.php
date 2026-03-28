@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ====== GLOBAL BRAND REFINEMENT ====== */
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
    .fluid-edit-wrapper {
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

    /* ====== INTERACTIVE PREVIEW AREA ====== */
    .preview-section {
        background: #fcfdfe;
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 4rem 2rem;
        transition: 0.3s ease;
        text-align: center;
        perspective: 1000px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .preview-section:hover {
        border-color: var(--brand-indigo);
        background: #f8faff;
    }

    #target {
        border-radius: 15px;
        object-fit: contain;
        border: 4px solid #fff;
        box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        background: white;
        transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        cursor: pointer;
        max-width: 100%;
        height: auto;
    }

    .preview-section:hover #target {
        transform: rotateY(12deg) rotateX(4deg) scale(1.03);
    }

    /* ====== FORM CONTROLS ====== */
    .form-label {
        font-weight: 700;
        color: #64748b;
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.8rem;
        display: block;
    }

    .form-control, .form-select {
        height: 52px !important;
        border-radius: 12px;
        border: 1.5px solid var(--border-color);
        padding: 12px 20px;
        font-size: 15px !important;
        font-weight: 600;
        transition: 0.3s;
        background-color: #f8fafc;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: var(--brand-indigo) !important;
        box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1) !important;
        outline: none;
    }

    /* ====== ACTION BUTTONS ====== */
    .btn-update {
        background: linear-gradient(135deg, var(--brand-indigo), var(--brand-purple)) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 800;
        padding: 16px 45px !important;
        border-radius: 14px;
        transition: 0.3s;
        letter-spacing: 0.5px;
        cursor: pointer;
    }

    .btn-update:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(124, 58, 237, 0.3);
    }

    .btn-cancel {
        border: 1.5px solid var(--border-color) !important;
        background-color: #fff;
        color: #64748b !important;
        font-weight: 700;
        padding: 16px 35px !important;
        border-radius: 14px;
        text-decoration: none !important;
        display: inline-block;
        transition: 0.2s;
    }

    .btn-cancel:hover { background-color: #fef2f2; color: var(--brand-red) !important; border-color: #fecaca !important; }

    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="fluid-edit-wrapper">
    <div class="card-custom">
        <div class="card-header-gradient">
            <i class="fas fa-edit"></i> 
            ASSET MODIFICATION ENGINE: ID #{{ $carImage->id }}
        </div>

        <div class="card-body p-4 p-lg-5">
            <form action="{{ url('car-images/update', encrypt($carImage->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-5">
                    <div class="col-xl-5 col-lg-12">
                        <div class="preview-section shadow-sm">
                            <p class="text-muted small mb-4 fw-800">DIGITAL ASSET PREVIEW (360° ACTIVE)</p>
                            <img src="{{ $carImage->image_name == '' ? url('backend/assets/img/avatars/1.png') : url('/imageCar/' . $carImage->image_name) }}" 
                                 alt="car-preview" width="380" height="240" id="target">
                            
                            <div class="mt-5">
                                <label for="select_image" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-4 py-2" style="cursor: pointer;">
                                    <i class="fas fa-sync-alt me-2"></i> SWAP SOURCE FILE
                                    <input type="file" name="image_name" id="select_image" onchange="selectImg()" hidden accept="image/*">
                                </label>
                                <p class="text-muted small mt-3 mb-0 fw-600">Replaces existing database entry (JPG/PNG). Max 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7 col-lg-12 d-flex flex-column justify-content-between">
                        <div>
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label">Vehicle Model Association</label>
                                    <select name="model_id" class="form-select">
                                        @foreach ($carModels as $item)
                                            <option value="{{ $item->id }}" @if ($item->id == $carImage->model_id) selected @endif>
                                                {{ $item->model_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Color Specification</label>
                                    <select name="color_id" class="form-select">
                                        @foreach ($colors as $item)
                                            <option value="{{ $item->id }}" @if ($item->id == $carImage->color_id) selected @endif>
                                                {{ $item->color_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mt-4">
                                    <div class="p-4 rounded-4" style="background: #f8faff; border: 1.5px solid #e0e7ff;">
                                        <h6 class="fw-800 text-indigo mb-2 small uppercase"><i class="fas fa-info-circle me-2"></i>Modification Policy</h6>
                                        <p class="text-muted small mb-0 fw-600">Updating the Model or Color will instantly sync all associated Sales Reports using this asset node. Ensure accuracy before synchronization.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex gap-3 justify-content-start">
                            <button type="submit" class="btn-update shadow-sm">
                                <i class="fas fa-save me-2"></i> COMMIT CHANGES
                            </button>
                            <a href="{{ url('/car-images') }}" class="btn btn-cancel">
                                DISCARD
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // YOUR ORIGINAL PREVIEW LOGIC RETAINED
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