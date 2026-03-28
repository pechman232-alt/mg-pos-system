@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
/* ====== GLOBAL BRAND REFINEMENT ====== */
:root {
    --brand-indigo: #4f46e5;
    --brand-purple: #7c3aed;
    --brand-pink: #ec4899;
    --brand-red: #ef4444;
    --brand-success: #71dd37;
    --bg-body: #f8fafc;
    --font-family: 'Plus Jakarta Sans', sans-serif !important;
    --radius: 12px;
}

body {
    background-color: var(--bg-body);
    margin: 0;
}

/* ====== 100% FIT SCREEN WRAPPER ====== */
.fluid-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    padding: 1.5rem !important;
    animation: slideUp 0.5s ease-out;
    font-family: var(--font-family);
}

.card-custom {
    border: none !important;
    border-radius: var(--radius) !important;
    box-shadow: 0 10px 30px 0 rgba(79, 70, 229, 0.08) !important;
    background-color: #fff;
    overflow: hidden;
    width: 100%; 
}

.card-header-gradient {
    background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important;
    color: #ffffff !important;
    padding: 1.5rem 2rem !important;
    font-size: 18px !important;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 12px;
    border: none;
}

.card-body {
    padding: 3rem !important; /* Increased padding for wide screens */
}

label.form-label {
    font-weight: 700;
    color: #64748b;
    font-size: 14px !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
    display: block;
}

.form-control-lg-custom {
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    padding: 16px 24px;
    font-size: 16px !important;
    font-weight: 600;
    transition: all 0.3s ease;
    background-color: #f8fafc;
    color: #1e293b;
    width: 100%; /* Stretch to full width */
}

.form-control-lg-custom:focus {
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
    padding: 14px 45px !important;
    border-radius: 12px;
    transition: 0.3s;
    cursor: pointer;
    letter-spacing: 0.5px;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
}

.btn-cancel {
    border: 1.5px solid #e2e8f0 !important;
    background-color: #fff;
    color: #64748b !important;
    font-weight: 700;
    padding: 14px 30px !important;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: 0.2s;
}

.btn-cancel:hover {
    background-color: #f1f5f9;
    color: var(--brand-red) !important;
    border-color: var(--brand-red) !important;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="fluid-wrapper">
    <div class="card-custom">
        <div class="card-header-gradient">
            <i class="fas fa-pen-to-square"></i> 
            MODIFY VEHICLE MODEL RECORD
        </div>
        
        <div class="card-body">
            <form id="updateForm" action="{{ url('car-models/update', encrypt($carModels->id)) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="mb-5 col-12">
                        <label class="form-label" for="model_name">Official Model Name</label>
                        <input type="text" 
                               id="model_name"
                               placeholder="Edit model name..." 
                               class="form-control-lg-custom @error('model_name') is-invalid @enderror" 
                               name="model_name" 
                               value="{{ old('model_name', $carModels->model_name) }}"
                               required>
                    
                        @error('model_name')
                            <div style="color: var(--brand-red); font-size: 13px; font-weight: 600; margin-top: 12px;">
                                <i class="fas fa-circle-exclamation me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top d-flex gap-3">
                    <button type="button" class="btn-update shadow-sm" onclick="confirmUpdate()">
                        <i class="fas fa-arrows-rotate me-2"></i> UPDATE RECORD
                    </button>
                    <a href="{{ url('/car-models') }}" class="btn btn-cancel">
                        DISCARD CHANGES
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    /**
     * SweetAlert2 Update Confirmation
     */
    function confirmUpdate() {
        const modelName = document.getElementById('model_name').value;

        if (!modelName.trim()) {
            Swal.fire('Required', 'Please enter a valid model name.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Confirm Changes?',
            text: "Are you sure you want to update this vehicle model nomenclature?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Confirm Update',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-primary px-4 me-3',
                cancelButton: 'btn btn-label-secondary px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('updateForm').submit();
            }
        });
    }
</script>
@endsection