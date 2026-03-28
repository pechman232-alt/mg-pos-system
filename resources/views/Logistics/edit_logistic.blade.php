@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* --- GLOBAL BRANDING --- */
    :root {
        --brand-primary: #4f46e5;    /* Indigo */
        --brand-success: #10b981;    /* Emerald */
        --brand-warning: #f59e0b;    /* Amber */
        --brand-danger: #ef4444;     /* Rose */
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: #1e293b;
    }

    /* Force Full Width */
    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* Professional 4-Color Header Gradient */
    .card-header-gradient {
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 33%, #ec4899 66%, #ef4444 100%) !important;
        padding: 20px 25px;
        border-radius: 12px 12px 0 0 !important;
        border-bottom: none;
    }

    .full-width-card {
        width: 100% !important;
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .form-label {
        font-weight: 700;
        font-size: 0.75rem;
        color: #475569;
        text-transform: none;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control, .form-select {
        padding: 12px 16px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.85rem;
        background-color: #ffffff;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .is-invalid {
        border-color: var(--brand-danger) !important;
        background-color: #fef2f2 !important;
    }

    .btn-update {
        background-color: #1e293b;
        color: white;
        border: none;
        font-weight: 700;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
    }

    .btn-update:hover { background-color: #000; transform: translateY(-1px); color: white; }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 16px;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.2s;
    }
    .btn-back:hover { background: rgba(255, 255, 255, 0.3); color: #fff; }

    /* Animation */
    .animate-up {
        opacity: 0;
        transform: translateY(12px);
        animation: fadeInUp 0.4s ease forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card full-width-card animate-up">
                <div class="card-header-gradient d-flex justify-content-between align-items-center">
                    <h5 class="header-title">
                        <i class='fas fa-edit me-2'></i>UPDATE LOGISTICS DOSSIER
                    </h5>
                    <a href="{{ url('/logistics') }}" class="btn btn-back">
                        <i class="fas fa-times me-1"></i> DISCARD CHANGES
                    </a>
                </div>

                <div class="card-body p-5">
                    <form action="{{ url('logistics/update', encrypt($logistics->id)) }}" method="POST" id="editForm">
                        @csrf
                        <div class="row g-4">
                            
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-car-side text-primary"></i> Vehicle Model</label>
                                <select name="model_name_id" class="form-select @error('model_name_id') is-invalid @enderror">
                                    <option value="" disabled>Select Model</option>
                                    @foreach ($carModels as $item)
                                        <option value="{{ $item->id }}" @if ($item->id == $logistics->model_name_id) selected @endif>
                                            {{ $item->model_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-calendar-alt text-warning"></i> Model Year</label>
                                <input type="number" name="model_year" class="form-control @error('model_year') is-invalid @enderror" value="{{ $logistics->model_year }}" placeholder="YYYY">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-palette text-success"></i> Exterior Color</label>
                                <select name="exterior_color" class="form-select @error('exterior_color') is-invalid @enderror">
                                    <option value="" disabled>Select Color</option>
                                    @foreach ($colors as $item)
                                        <option value="{{ $item->color_name }}" @if ($item->color_name == $logistics->exterior_color) selected @endif>
                                            {{ $item->color_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-couch text-info"></i> Interior Color</label>
                                <input type="text" name="interior_color" class="form-control @error('interior_color') is-invalid @enderror" value="{{ $logistics->interior_color }}" placeholder="Interior Trim">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-barcode text-danger"></i> VIN Number</label>
                                <input type="text" id="vin_input" name="vin_number" class="form-control @error('vin_number') is-invalid @enderror" value="{{ $logistics->vin_number }}" placeholder="CHASSIS NO.">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-engine text-secondary"></i> Engine Number</label>
                                <input type="text" name="engine_number" class="form-control @error('engine_number') is-invalid @enderror" value="{{ $logistics->engine_number }}" placeholder="ENGINE NO.">
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class="fas fa-sticky-note text-muted"></i> Internal Remarks</label>
                                <textarea name="remark" class="form-control" rows="3" placeholder="Enter additional notes...">{{ $logistics->remark }}</textarea>
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-update w-100" id="btnUpdate">
                                    <i class='fas fa-sync-alt me-2'></i> COMMIT UPDATES
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
    });

    // --- VIN AUTO-UPPERCASE ---
    const vinInput = document.getElementById('vin_input');
    if(vinInput) {
        vinInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // --- Loading State ---
    document.getElementById('editForm').onsubmit = function() {
        Swal.fire({
            title: 'Updating...',
            text: 'Synchronizing record with database',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        document.getElementById('btnUpdate').disabled = true;
    };

    // --- Validation Toasts ---
    @if($errors->any())
        Toast.fire({
            icon: 'error',
            title: "Update Failed",
            text: "{{ $errors->first() }}"
        });
    @endif
</script>

@endsection