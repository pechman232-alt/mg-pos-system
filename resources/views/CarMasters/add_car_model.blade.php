@extends('layouts.homePages')
@section('content')

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); margin: 0; -webkit-font-smoothing: antialiased; }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-form-container { width: 100% !important; max-width: 100% !important; padding: 24px 32px !important; animation: slideUp 0.4s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

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

    /* ====== FORM STYLING ====== */
    .card-body-custom { padding: 32px 40px !important; }

    .form-label {
        font-weight: 600; font-size: 0.8rem; color: #4B5563;
        margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
    }
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    /* Premium Input Field */
    .form-control-custom {
        height: 48px; padding: 12px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 0.9rem;
        background-color: var(--input-bg); color: var(--text-main);
        transition: all 0.2s ease; width: 100%; font-weight: 500;
    }

    .form-control-custom:focus {
        background-color: #FFFFFF; border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none;
    }

    .form-control-custom::placeholder { color: #9CA3AF; font-weight: 400; }
    
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    .text-error-custom {
        color: #EF4444; font-size: 0.8rem; font-weight: 500;
        margin-top: 6px; display: flex; align-items: center; gap: 4px;
    }

    /* ====== ACTION BUTTONS ====== */
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

    <div class="fluid-form-container">
        <div class="form-card">
            
            <!-- Clean Header -->
            <div class="card-header-clean">
                <h5 class="header-title">
                    <i class="fas fa-layer-group text-muted"></i> NEW MODEL REGISTRY
                </h5>
                <a href="{{ url('/car-models') }}" class="btn-back-clean">
                    <i class="fas fa-arrow-left"></i> Back to Repository
                </a>
            </div>
            
            <div class="card-body-custom">
                <form action="{{ url('/car-models/store') }}" method="POST" id="modelForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8 col-lg-6 mb-4">
                            <label class="form-label" for="model_name">
                                <i class="fas fa-car"></i> Official Model Designation
                            </label>
                            <input type="text" id="model_name" name="model_name" 
                                   placeholder="e.g., MG ZS 2026 Premium" 
                                   class="form-control-custom @error('model_name') is-invalid @enderror" 
                                   value="{{ old('model_name') }}" required autofocus autocomplete="off">
                        
                            @error('model_name')
                                <div class="text-error-custom">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="mt-4 pt-4 border-top d-flex gap-3">
                        <button type="submit" class="btn-save" id="btnSubmit">
                            <i class="fas fa-check-circle"></i> SAVE MODEL RECORD
                        </button>
                        <a href="{{ url('/car-models') }}" class="btn-cancel">
                            CANCEL
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional: SweetAlert2 for loading state -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('modelForm').onsubmit = function() {
            Swal.fire({
                title: 'Saving Model...',
                text: 'Adding nomenclature to system registry.',
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