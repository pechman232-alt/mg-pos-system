@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-success: #10b981;
        --brand-navy: #0f172a;
        --bg-body: #f8fafc;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        margin: 0;
    }

    /* 🌟 FULL WIDTH FIT SCREEN */
    .fluid-full-container {
        width: 100%;
        padding: 1.25rem;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    .card-enterprise {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        background: #fff;
        overflow: hidden;
    }

    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%);
        padding: 1.25rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body-premium {
        padding: 2rem !important;
    }

    .form-label-enterprise {
        font-weight: 700;
        font-size: 0.72rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
    }

    /* 🌟 INPUTS & SELECTS */
    .form-control-premium, .form-select-premium {
        height: 46px;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.9rem;
        background-color: #f8fafc;
        font-weight: 600;
        color: var(--brand-navy);
        transition: all 0.2s ease;
        width: 100%;
    }

    .form-control-premium:focus, .form-select-premium:focus {
        background-color: #ffffff;
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* 🌟 ADD BUTTON (+) STYLING */
    .input-group-custom {
        display: flex;
        width: 100%;
    }

    .select-grouped {
        border-radius: 8px 0 0 8px !important;
    }

    .btn-add-model {
        background-color: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        border-left: none;
        color: var(--brand-indigo);
        padding: 0 15px;
        border-radius: 0 8px 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 20px;
        font-weight: bold;
        transition: 0.2s;
    }

    .btn-add-model:hover {
        background-color: #e2e8f0;
        color: var(--brand-navy);
    }

    #vin_counter {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        margin-top: 5px;
    }
    .vin-valid { color: var(--brand-success); font-weight: 700; }

    /* 🌟 BUTTON SAVE */
    .btn-save-enterprise {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        border: none;
        font-weight: 700;
        padding: 12px 35px;
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-save-enterprise:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
    }

    .btn-back-enterprise {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 7px 15px;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.2s;
    }
    .btn-back-enterprise:hover { background: white; color: var(--brand-indigo); }
</style>

<div class="fluid-full-container">
    <div class="card-enterprise">
        <div class="card-header-gradient">
            <h5 class="header-title">
                <i class='fas fa-truck-loading'></i> LOGISTICS ENTRY
            </h5>
            <a href="{{ url('/logistics') }}" class="btn-back-enterprise">
                <i class="fas fa-arrow-left me-1"></i> BACK
            </a>
        </div>

        <div class="card-body-premium">
            <form action="{{ url('/logistics/store') }}" method="POST" id="mainForm">
                @csrf
                <div class="row g-4">
                    
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise"><i class="fas fa-car-side text-primary"></i> Vehicle Model</label>
                        <div class="input-group-custom">
                            <select name="model_name_id" class="form-select-premium select-grouped @error('model_name_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Choose model...</option>
                                @foreach ($carModels as $item)
                                    <option value="{{ $item->id }}" {{ old('model_name_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->model_name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ url('/car-models/create') }}" class="btn-add-model" title="Add New Model">+</a>
                        </div>
                        @error('model_name_id')
                            <div class="text-danger mt-1" style="font-size: 11px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise"><i class="fas fa-calendar-check text-warning"></i> Production Year</label>
                        <input type="number" name="model_year" class="form-control-premium" value="{{ old('model_year', 2026) }}">
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise text-primary"><i class="fas fa-barcode"></i> VIN Number</label>
                        <input type="text" id="vin_input" name="vin_number" class="form-control-premium" 
                               value="{{ old('vin_number') }}" placeholder="17-Digit Chassis ID" maxlength="17">
                        <div id="vin_counter" class="text-muted">
                            <span id="char_count">0</span> / 17 Characters
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise text-primary"><i class="fas fa-microchip"></i> Engine Number</label>
                        <input type="text" name="engine_number" class="form-control-premium" 
                               value="{{ old('engine_number') }}" placeholder="Engine Code">
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise"><i class="fas fa-palette text-success"></i> Exterior Color</label>
                        <select name="exterior_color" class="form-select-premium">
                            <option value="" disabled selected>Select color...</option>
                            @foreach ($colors as $item)
                                <option value="{{ $item->color_name }}">{{ $item->color_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label class="form-label-enterprise"><i class="fas fa-couch text-info"></i> Interior Trim</label>
                        <input type="text" name="interior_color" class="form-control-premium" 
                               value="{{ old('interior_color') }}" placeholder="e.g. Black Leather">
                    </div>

                    <div class="col-12">
                        <label class="form-label-enterprise"><i class="fas fa-sticky-note text-muted"></i> Remarks</label>
                        <textarea name="remark" class="form-control-premium" rows="2" placeholder="Notes...">{{ old('remark') }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn-save-enterprise" id="btnSubmit">
                            <i class='fas fa-save me-2'></i> SAVE RECORD
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const vinInput = document.getElementById('vin_input');
    const charCount = document.getElementById('char_count');
    const vinCounterDiv = document.getElementById('vin_counter');

    if(vinInput) {
        vinInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            const len = this.value.length;
            charCount.textContent = len;

            if(len === 17) {
                vinCounterDiv.classList.add('vin-valid');
                vinCounterDiv.innerHTML = `<i class="fas fa-check-circle me-1"></i> 17 / 17 Valid`;
            } else {
                vinCounterDiv.classList.remove('vin-valid');
                vinCounterDiv.innerHTML = `<span id="char_count">${len}</span> / 17 Characters`;
            }
        });
    }

    document.getElementById('mainForm').onsubmit = function() {
        Swal.fire({
            title: 'Saving Data...',
            text: 'Please wait while we secure your record.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    };
</script>

@endsection