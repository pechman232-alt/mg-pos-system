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
        --text-main: #566a7f;
        --border-light: #d9dee3;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body {
        background-color: #f8fafc;
        margin: 0;
    }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-page-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: softRise 0.6s ease-out;
        font-family: var(--font-family);
    }

    @keyframes softRise {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ====== CARD STYLING ====== */
    .card-custom {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        background: #fff;
        overflow: hidden;
        width: 100%;
    }

    /* ====== BRANDED GRADIENT HEADER ====== */
    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important;
        padding: 1.5rem 2rem !important;
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* ====== TEXT HIGHLIGHT ====== */
    .highlight-label {
        font-weight: 700;
        color: var(--brand-indigo); 
        background: rgba(79, 70, 229, 0.08);
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 12px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ====== FORM CONTROLS ====== */
    .form-control-custom {
        border-radius: 12px;
        border: 1.5px solid var(--border-light);
        padding: 12px 18px;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        width: 100%;
    }

    .form-control-custom:focus {
        border-color: var(--brand-indigo);
        background-color: #fff;
        box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* Native Color Picker Styling */
    .color-picker-input {
        width: 50px;
        height: 50px;
        padding: 0;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        background: none;
    }
    .color-picker-input::-webkit-color-swatch {
        border: 2px solid #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* ====== BUTTONS ====== */
    .btn-save {
        background: linear-gradient(135deg, var(--brand-indigo), var(--brand-purple)) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 800;
        border-radius: 12px;
        padding: 14px 45px !important;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
    }

    .btn-cancel {
        border: 1.5px solid var(--border-light) !important;
        background-color: #fff;
        color: var(--text-main);
        font-weight: 700;
        padding: 14px 30px !important;
        border-radius: 12px;
        text-decoration: none !important;
        display: inline-block;
        transition: 0.2s;
    }
    .btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; }

</style>

<div class="fluid-page-wrapper">
    <div class="card-custom">
        <div class="card-header-gradient">
            <i class="fas fa-eye-dropper"></i> ADD NEW COLOR RECORD
        </div>
        
        <div class="card-body p-4 p-lg-5">
            <form action="{{ url('/colors/store') }}" method="POST" id="colorForm">
                @csrf
                <div class="row g-5">
                    <div class="col-xl-6 col-lg-12">
                        <label class="highlight-label">Color Marketing Name</label>
                        <input type="text" name="color_name" placeholder="e.g. Phantom Black Metallic" 
                               class="form-control-custom @error('color_name') border-danger @enderror" 
                               value="{{ old('color_name') }}" required>
                        @error('color_name')
                            <small class="text-danger fw-800 mt-2 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-xl-6 col-lg-12">
                        <label class="highlight-label">Hexadecimal Spectrum</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" id="visual_picker" class="color-picker-input shadow-sm" 
                                   value="{{ old('code', '#4f46e5') }}" oninput="syncInputs(this.value, 'text')">
                            
                            <div class="flex-grow-1">
                                <input type="text" id="hex_code" name="code" placeholder="#FFFFFF" 
                                       class="form-control-custom fw-bold text-uppercase @error('code') border-danger @enderror" 
                                       value="{{ old('code', '#4f46e5') }}" maxlength="7" 
                                       oninput="syncInputs(this.value, 'visual')">
                            </div>

                            <button type="button" class="btn btn-light border-2 fw-bold rounded-3 py-2 px-3 shadow-sm" onclick="generateRandomHex()" title="Random Color">
                                <i class="fas fa-dice-five fs-5"></i>
                            </button>
                        </div>
                        @error('code')
                            <small class="text-danger fw-800 mt-2 d-block"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex gap-3 justify-content-start">
                    <button type="submit" class="btn-save shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> PUBLISH COLOR
                    </button>
                    <a href="{{ url('/colors') }}" class="btn btn-cancel">
                        DISCARD
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Syncs the color between the visual picker and the text field
     */
    function syncInputs(value, targetType) {
        let hex = value;
        
        if (targetType === 'visual') {
            if (!hex.startsWith('#') && hex.length > 0) hex = '#' + hex;
            if (hex.length === 7) {
                document.getElementById('visual_picker').value = hex;
            }
        } else {
            document.getElementById('hex_code').value = hex.toUpperCase();
        }
    }

    /**
     * Generates a random high-quality Hex code
     */
    function generateRandomHex() {
        const randomColor = '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0').toUpperCase();
        document.getElementById('hex_code').value = randomColor;
        document.getElementById('visual_picker').value = randomColor;
    }
</script>

@endsection