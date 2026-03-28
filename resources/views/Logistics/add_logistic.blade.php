@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- ENTERPRISE BRANDING --- */
    :root {
        --brand-primary: #4f46e5;    /* Indigo */
        --brand-success: #10b981;    /* Emerald */
        --brand-warning: #f59e0b;    /* Amber */
        --brand-danger: #ef4444;     /* Rose */
        --brand-navy: #0f172a;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body {
        background-color: var(--bg-body);
        font-family: var(--font-family);
        margin: 0;
    }

    /* FORCED 100% FIT SCREEN WRAPPER */
    .fluid-page-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* PROFESSIONAL CARD SYSTEM */
    .card-enterprise {
        width: 100% !important;
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(79, 70, 229, 0.06);
        background: #fff;
        overflow: hidden;
    }

    .card-header-gradient {
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 33%, #ec4899 66%, #ef4444 100%) !important;
        padding: 1.5rem 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.2rem;
        margin: 0;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* FORM STYLING */
    .card-body-premium {
        padding: 4rem 3.5rem !important; 
    }

    .form-label-enterprise {
        font-weight: 800;
        font-size: 0.72rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 1px;
    }

    .form-control-premium, .form-select-premium {
        padding: 14px 20px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-size: 1rem;
        background-color: #f8fafc;
        font-weight: 600;
        color: var(--brand-navy);
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-premium:focus, .form-select-premium:focus {
        background-color: #ffffff;
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 6px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* VIN COUNTER STYLING */
    #vin_counter {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        transition: 0.3s;
    }
    .vin-valid { color: var(--brand-success) !important; font-weight: 800; }
    .vin-invalid { color: var(--brand-danger) !important; }

    /* HIGHLIGHT CORE DATA (VIN/ENGINE) */
    .highlight-input {
        border: 2px solid #e0e7ff !important;
        background-color: #f5f8ff !important;
    }

    /* BUTTON SYSTEM */
    .btn-save-enterprise {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border: none;
        font-weight: 800;
        padding: 18px 40px;
        border-radius: 14px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-save-enterprise:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3);
        background: #000;
    }

    .btn-back-enterprise {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.8rem;
        font-weight: 700;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 10px;
        backdrop-filter: blur(5px);
        transition: 0.3s;
    }
    .btn-back-enterprise:hover { background: white; color: var(--brand-primary); }
</style>

<div class="fluid-page-container">
    <div class="card-enterprise">
        <div class="card-header-gradient">
            <h5 class="header-title">
                <i class='fas fa-truck-loading'></i> LOGISTICS RESOURCE CONFIGURATOR
            </h5>
            <a href="{{ url('/logistics') }}" class="btn-back-enterprise">
                <i class="fas fa-arrow-left me-1"></i> BACK TO REGISTRY
            </a>
        </div>

        <div class="card-body-premium">
            <form action="{{ url('/logistics/store') }}" method="POST" id="mainForm">
                @csrf
                <div class="row g-5">
                    <div class="col-xl-6">
                        <label class="form-label-enterprise"><i class="fas fa-car-side text-primary"></i> Vehicle Model</label>
                        <select name="model_name_id" class="form-select-premium @error('model_name_id') is-invalid @enderror">
                            <option value="" disabled selected>Choose model...</option>
                            @foreach ($carModels as $item)
                                <option value="{{ $item->id }}" {{ old('model_name_id') == $item->id ? 'selected' : '' }}>{{ $item->model_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label-enterprise"><i class="fas fa-calendar-check text-warning"></i> Production Year</label>
                        <input type="number" name="model_year" class="form-control-premium" value="{{ old('model_year', 2026) }}">
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label-enterprise text-primary"><i class="fas fa-barcode"></i> Global VIN Number</label>
                        <input type="text" id="vin_input" name="vin_number" class="form-control-premium highlight-input" 
                               value="{{ old('vin_number') }}" placeholder="17-Digit Chassis ID" maxlength="17">
                        <div id="vin_counter" class="mt-2 text-muted fw-bold">
                            <span id="char_count">0</span> / 17 Characters
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label-enterprise text-primary"><i class="fas fa-microchip"></i> Engine Number</label>
                        <input type="text" name="engine_number" class="form-control-premium highlight-input" 
                               value="{{ old('engine_number') }}" placeholder="Engine Serialization Code">
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label-enterprise"><i class="fas fa-palette text-success"></i> Exterior Color</label>
                        <select name="exterior_color" class="form-select-premium">
                            <option value="" disabled selected>Select color...</option>
                            @foreach ($colors as $item)
                                <option value="{{ $item->color_name }}">{{ $item->color_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-6">
                        <label class="form-label-enterprise"><i class="fas fa-couch text-info"></i> Interior Trim</label>
                        <input type="text" name="interior_color" class="form-control-premium" 
                               value="{{ old('interior_color') }}" placeholder="Interior Material/Color">
                    </div>

                    <div class="col-12">
                        <label class="form-label-enterprise"><i class="fas fa-sticky-note text-muted"></i> Configuration Remarks</label>
                        <textarea name="remark" class="form-control-premium" rows="3" placeholder="Additional logistics notes...">{{ old('remark') }}</textarea>
                    </div>

                    <div class="col-12 mt-5">
                        <button type="submit" class="btn-save-enterprise shadow-lg" id="btnSubmit">
                            <i class='fas fa-database me-2'></i> COMMIT TO LOGISTICS RECORD
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // --- VIN AUTO-UPPERCASE & LIVE COUNTER ---
    const vinInput = document.getElementById('vin_input');
    const charCount = document.getElementById('char_count');
    const vinCounterDiv = document.getElementById('vin_counter');

    if(vinInput) {
        vinInput.addEventListener('input', function() {
            // Force Uppercase
            this.value = this.value.toUpperCase();
            
            // Update Count
            const len = this.value.length;
            charCount.textContent = len;

            // UI Feedback
            if(len === 17) {
                vinCounterDiv.classList.add('vin-valid');
                vinCounterDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i> 17 / 17 Characters (Standard VIN)`;
            } else {
                vinCounterDiv.classList.remove('vin-valid');
                vinCounterDiv.innerHTML = `<span id="char_count">${len}</span> / 17 Characters`;
            }
        });
    }

    // --- Loading state ---
    document.getElementById('mainForm').onsubmit = function() {
        Swal.fire({
            title: 'Synchronizing...',
            text: 'Uploading logistics node to central server',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    };
</script>

@endsection