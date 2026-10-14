@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
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
    }

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); -webkit-font-smoothing: antialiased; }
    .fluid-full-container { width: 100%; padding: 24px 32px; }

    /* Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-card { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .stagger-1 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 100ms; opacity: 0; }

    /* --- FORM CARD --- */
    .form-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }

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
    .form-label { font-weight: 600; font-size: 0.8rem; color: #4B5563; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    .form-control, .form-select {
        height: 44px; padding: 8px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 0.875rem;
        background-color: var(--input-bg); color: var(--text-main); transition: all 0.2s ease; width: 100%;
    }
    .form-control:focus, .form-select:focus { background-color: #FFFFFF; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    /* Input Group with Button */
    .input-group-custom { display: flex; width: 100%; }
    .select-grouped { border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; }
    
    .btn-add-model {
        background: #F3F4F6; border: 1px solid var(--border-color); border-left: none;
        color: var(--text-muted); padding: 0 16px; border-radius: 0 8px 8px 0;
        display: flex; align-items: center; justify-content: center; text-decoration: none;
        font-size: 1rem; font-weight: 600; transition: all 0.2s ease;
    }
    .btn-add-model:hover { background: #E5E7EB; color: var(--primary); }

    /* VIN Counter */
    #vin_counter { font-family: 'Inter', monospace; font-size: 0.75rem; margin-top: 6px; font-weight: 500; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
    .vin-valid { color: #059669 !important; font-weight: 600 !important; }

    /* Textarea */
    textarea.form-control { height: auto; min-height: 80px; padding: 12px 16px; resize: vertical; }

    /* --- SUBMIT BUTTON --- */
    .btn-save {
        background-color: var(--primary); color: white; border: none;
        font-weight: 600; padding: 12px 24px; border-radius: 8px;
        font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-save:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

</style>

<div class="fluid-full-container">
    <div class="form-card animate-card">
        
        <!-- Header -->
        <div class="card-header-clean">
            <h5 class="header-title">
                <i class="fas fa-truck-loading text-muted"></i> NEW LOGISTICS ENTRY
            </h5>
            <a href="{{ url('/logistics') }}" class="btn-back-clean">
                <i class="fas fa-arrow-left"></i> Back to Registry
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ url('/logistics/store') }}" method="POST" id="mainForm">
                @csrf
                <div class="row g-4 stagger-1">
                    
                    <!-- Section Title -->
                    <div class="col-12 mb-2">
                        <h6 class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px;">Vehicle Specifications</h6>
                    </div>

                    <!-- Vehicle Model -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="fas fa-car-side"></i> Vehicle Model</label>
                        <div class="input-group-custom">
                            <select name="model_name_id" class="form-select select-grouped @error('model_name_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select model...</option>
                                @foreach ($carModels as $item)
                                    <option value="{{ $item->id }}" {{ old('model_name_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->model_name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ url('/car-models/create') }}" class="btn-add-model" title="Add New Model"><i class="fas fa-plus"></i></a>
                        </div>
                        @error('model_name_id')
                            <div class="text-danger mt-1 fw-medium" style="font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Production Year -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="far fa-calendar-check"></i> Production Year</label>
                        <input type="number" name="model_year" class="form-control" value="{{ old('model_year', date('Y')) }}">
                    </div>

                    <!-- Exterior Color -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="fas fa-palette"></i> Exterior Color</label>
                        <select name="exterior_color" class="form-select">
                            <option value="" disabled selected>Select color...</option>
                            @foreach ($colors as $item)
                                <option value="{{ $item->color_name }}" {{ old('exterior_color') == $item->color_name ? 'selected' : '' }}>{{ $item->color_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- VIN Number -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="fas fa-barcode"></i> Chassis / VIN Number</label>
                        <input type="text" id="vin_input" name="vin_number" class="form-control" 
                               value="{{ old('vin_number') }}" placeholder="Enter 17-digit code" maxlength="17" style="text-transform: uppercase;">
                        <div id="vin_counter">
                            <span id="char_count">0</span> / 17 Characters
                        </div>
                    </div>

                    <!-- Engine Number -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="fas fa-microchip"></i> Engine Number</label>
                        <input type="text" name="engine_number" class="form-control" 
                               value="{{ old('engine_number') }}" placeholder="Enter engine code">
                    </div>

                    <!-- Interior Trim -->
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label"><i class="fas fa-couch"></i> Interior Trim</label>
                        <input type="text" name="interior_color" class="form-control" 
                               value="{{ old('interior_color') }}" placeholder="e.g. Black Leather">
                    </div>

                    <!-- Remarks -->
                    <div class="col-12 mt-4">
                        <label class="form-label"><i class="far fa-sticky-note"></i> Remarks / Additional Notes</label>
                        <textarea name="remark" class="form-control" placeholder="Add any special instructions here...">{{ old('remark') }}</textarea>
                    </div>

                    <!-- Submit Action -->
                    <div class="col-12 mt-5 text-end border-top pt-4">
                        <button type="submit" class="btn-save" id="btnSubmit">
                            <i class='fas fa-check-circle'></i> REGISTER VEHICLE
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vinInput = document.getElementById('vin_input');
        const charCount = document.getElementById('char_count');
        const vinCounterDiv = document.getElementById('vin_counter');

        if(vinInput) {
            // Initial count on load (in case of old() value)
            updateVinCounter(vinInput.value);

            vinInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
                updateVinCounter(this.value);
            });
        }

        function updateVinCounter(value) {
            const len = value.length;
            charCount.textContent = len;

            if(len === 17) {
                vinCounterDiv.classList.add('vin-valid');
                vinCounterDiv.innerHTML = `<i class="fas fa-check-circle"></i> Valid VIN length (17/17)`;
            } else {
                vinCounterDiv.classList.remove('vin-valid');
                vinCounterDiv.innerHTML = `<span id="char_count">${len}</span> / 17 Characters`;
            }
        }

        // Form Submit Loading State (Clean Theme)
        document.getElementById('mainForm').onsubmit = function() {
            Swal.fire({
                title: 'Saving Record...',
                text: 'Registering vehicle data into the system.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'rounded-4 shadow-sm border-0', title: 'font-weight-bold' }
            });
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> PROCESSING...';
        };
    });
</script>

@endsection