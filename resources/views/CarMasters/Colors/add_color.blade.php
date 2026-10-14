@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* =========================================================
       CLEAN SaaS DESIGN SYSTEM 
       ========================================================= */
    :root {
        --primary: #4F46E5;      /* Indigo 600 */
        --primary-hover: #4338CA;
        --surface: #FFFFFF;
        --bg-body: #F8FAFC;      /* Slate 50 */
        --text-main: #111827;    /* Gray 900 */
        --text-muted: #6B7280;   /* Gray 500 */
        --border-color: #E5E7EB; /* Gray 200 */
        --input-bg: #F9FAFB;
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
    }

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); margin: 0; -webkit-font-smoothing: antialiased; }
    .fluid-page-wrapper { width: 100% !important; padding: 24px 32px !important; }

    /* Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-card { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .stagger-1 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 100ms; opacity: 0; }
    .stagger-2 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 200ms; opacity: 0; }

    /* --- FORM CARD --- */
    .form-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden; width: 100%;
    }

    /* Clean Header */
    .card-header-clean {
        padding: 20px 24px; border-bottom: 1px solid var(--border-color);
        display: flex; justify-content: space-between; align-items: center;
        background: #FFFFFF;
    }

    .header-title {
        font-family: var(--font-display); font-size: 1.1rem; font-weight: 700;
        color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;
    }

    .btn-back-clean {
        background: #FFFFFF; color: var(--text-muted); border: 1px solid var(--border-color);
        font-size: 0.875rem; font-weight: 600; padding: 8px 16px; border-radius: 8px;
        text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-back-clean:hover { background: #F3F4F6; color: var(--text-main); }

    /* --- INPUTS --- */
    .card-body-custom { padding: 32px 40px !important; }

    .form-label {
        font-weight: 600; font-size: 0.8rem; color: #4B5563;
        margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
    }
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    .form-control {
        height: 44px; padding: 8px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 0.875rem;
        background-color: var(--input-bg); color: var(--text-main);
        transition: all 0.2s ease; width: 100%; font-weight: 500;
    }

    .form-control:focus {
        background-color: #FFFFFF; border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none;
    }

    .form-control::placeholder { color: #9CA3AF; font-weight: 400; }
    
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    .text-error-custom {
        color: #EF4444; font-size: 0.8rem; font-weight: 500;
        margin-top: 6px; display: flex; align-items: center; gap: 4px;
    }

    /* Color Picker Specific Styling */
    .color-picker-input {
        width: 44px; height: 44px; padding: 0; border: 1px solid var(--border-color);
        border-radius: 8px; cursor: pointer; background: var(--input-bg); transition: 0.2s;
    }
    .color-picker-input::-webkit-color-swatch {
        border: 2px solid #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .color-picker-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }

    .btn-random {
        height: 44px; width: 44px; display: flex; align-items: center; justify-content: center;
        background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 8px;
        color: var(--text-muted); transition: 0.2s; cursor: pointer;
    }
    .btn-random:hover { background: #F3F4F6; color: var(--primary); border-color: var(--border-color); }

    .hex-input { font-family: var(--font-mono); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }

    /* --- ACTION BUTTONS --- */
    .btn-save {
        background-color: var(--primary); color: white; border: none;
        font-weight: 600; padding: 12px 24px; border-radius: 8px;
        font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-save:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

    .btn-cancel {
        background: #FFFFFF; color: var(--text-muted); border: 1px solid var(--border-color);
        font-weight: 600; padding: 12px 24px; border-radius: 8px; font-size: 0.9rem;
        text-decoration: none; display: inline-flex; align-items: center; transition: 0.2s; gap: 8px;
    }
    .btn-cancel:hover { background: #F9FAFB; color: var(--text-main); }
</style>

<div class="fluid-page-wrapper">
    <div class="form-card animate-card">
        
        <!-- Clean Header -->
        <div class="card-header-clean">
            <h5 class="header-title">
                <i class="fas fa-palette text-muted"></i> NEW COLOR REGISTRY
            </h5>
            <a href="{{ url('/colors') }}" class="btn-back-clean">
                <i class="fas fa-arrow-left"></i> Back to Colors
            </a>
        </div>
        
        <div class="card-body-custom">
            <form action="{{ url('/colors/store') }}" method="POST" id="colorForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6 mb-2 stagger-1">
                        <label class="form-label" for="color_name">
                            <i class="fas fa-tag"></i> Color Marketing Name
                        </label>
                        <input type="text" id="color_name" name="color_name" 
                               placeholder="e.g. Phantom Black Metallic" 
                               class="form-control @error('color_name') is-invalid @enderror" 
                               value="{{ old('color_name') }}" required autofocus autocomplete="off">
                    
                        @error('color_name')
                            <div class="text-error-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-2 stagger-2">
                        <label class="form-label" for="hex_code">
                            <i class="fas fa-hashtag"></i> Hexadecimal Spectrum
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="color" id="visual_picker" class="color-picker-input" 
                                   value="{{ old('code', '#4F46E5') }}" oninput="syncInputs(this.value, 'text')">
                            
                            <div class="flex-grow-1">
                                <input type="text" id="hex_code" name="code" placeholder="#FFFFFF" 
                                       class="form-control hex-input @error('code') is-invalid @enderror" 
                                       value="{{ old('code', '#4F46E5') }}" maxlength="7" 
                                       oninput="syncInputs(this.value, 'visual')">
                            </div>

                            <button type="button" class="btn-random" onclick="generateRandomHex()" title="Generate Random Color">
                                <i class="fas fa-dice"></i>
                            </button>
                        </div>
                        @error('code')
                            <div class="text-error-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="mt-5 pt-4 border-top d-flex gap-3 stagger-2">
                    <button type="submit" class="btn-save" id="btnSubmit">
                        <i class="fas fa-check-circle"></i> PUBLISH COLOR
                    </button>
                    <a href="{{ url('/colors') }}" class="btn-cancel">
                        CANCEL
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

    // Form Loading State
    document.getElementById('colorForm').onsubmit = function() {
        Swal.fire({
            title: 'Saving Color...',
            text: 'Adding asset to the system registry.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); },
            customClass: { popup: 'rounded-4 shadow-sm border-0', title: 'font-weight-bold' }
        });
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> PROCESSING...';
    };
</script>

@endsection