@extends('layouts.homePages')
@section('content')

    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
        font-family: var(--font-family);
        color: #1e293b;
        margin: 0;
    }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-form-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: slideUp 0.5s ease-out;
    }

    /* Full Width Card Styling */
    .card-custom {
        border: none !important;
        border-radius: var(--radius) !important;
        box-shadow: 0 10px 30px 0 rgba(79, 70, 229, 0.08) !important;
        background-color: #fff;
        overflow: hidden;
        width: 100%; 
    }

    /* Branded Gradient Header */
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

    /* ====== ENLARGED FORM STYLING ====== */
    .card-body {
        padding: 3rem !important; /* Increased padding for wide screens */
    }

    label.form-label {
        font-weight: 700;
        color: #64748b;
        font-size: 14px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: block;
    }

    /* Larger, Premium Input Field */
    .form-control-lg-custom {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 16px 24px; /* Larger padding */
        font-size: 16px !important;
        font-weight: 600;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        color: #1e293b;
        width: 100%;
    }

    .form-control-lg-custom:focus {
        background-color: #fff;
        border-color: var(--brand-indigo) !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
        outline: none;
    }

    /* ====== ACTION BUTTONS ====== */
    .btn-save {
        background: linear-gradient(135deg, var(--brand-indigo), var(--brand-purple)) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 700;
        padding: 14px 50px !important; /* Wider buttons */
        border-radius: 10px;
        transition: 0.3s;
        cursor: pointer;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
    }

    .btn-cancel {
        border: 1.5px solid #e2e8f0 !important;
        background-color: #fff;
        color: #64748b !important;
        font-weight: 700;
        padding: 14px 35px !important;
        border-radius: 10px;
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

    .text-error-custom {
        color: var(--brand-red);
        font-size: 13px;
        font-weight: 600;
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    </style>

    <div class="fluid-form-container">
        <div class="card-custom">
            <div class="card-header-gradient">
                <i class='bx bx-plus-circle' style="font-size: 24px;"></i> 
                CAR MODEL REGISTRY: NEW ENTRY
            </div>
            
            <div class="card-body">
                <form action="{{ url('/car-models/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="mb-5 col-12">
                            <label class="form-label" for="model_name">Official Model Designation</label>
                            <input type="text" 
                                   id="model_name"
                                   placeholder="Type model name here... (e.g., MG ZS 2026 Premium)" 
                                   class="form-control-lg-custom @error('model_name') is-invalid @enderror" 
                                   name="model_name" 
                                   value="{{ old('model_name') }}"
                                   required>
                        
                            @error('model_name')
                                <div class="text-error-custom">
                                    <i class='bx bx-error-alt'></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top d-flex gap-3">
                        <button type="submit" class="btn-save shadow-sm">
                            <i class='bx bx-check-double me-1'></i> SAVE RECORD
                        </button>
                        <a href="{{ url('/car-models') }}" class="btn btn-cancel">
                            CANCEL & EXIT
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection