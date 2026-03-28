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
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-family);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: #1e293b;
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* --- PREMIUM CARD STYLING --- */
    .full-width-card {
        width: 100% !important;
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    /* Professional 4-Color Header Gradient */
    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%) !important;
        padding: 20px 25px;
        border-bottom: none;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    /* --- FORM ELEMENTS --- */
    .form-label {
        font-weight: 700;
        font-size: 0.75rem;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
    }

    .form-control-custom {
        width: 100%;
        height: 52px;
        padding: 10px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 0 12px 12px 0; /* Matches input group icon */
        font-size: 0.95rem;
        background-color: #fcfdfe;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .input-group-text-custom {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        border-radius: 12px 0 0 12px;
        color: var(--brand-indigo);
        padding: 0 18px;
    }

    /* --- BUTTONS --- */
    .btn-action-save {
        background-color: #1e293b;
        color: white;
        border: none;
        font-weight: 800;
        padding: 16px 35px;
        border-radius: 12px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        text-transform: uppercase;
    }

    .btn-action-save:hover { 
        background-color: #000; 
        transform: translateY(-2px); 
        color: white; 
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 18px;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.2s;
    }
    .btn-back:hover { background: rgba(255, 255, 255, 0.3); color: #fff; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card full-width-card">
        <div class="card-header-gradient d-flex justify-content-between align-items-center">
            <h5 class="header-title">
                <i class="fas fa-sitemap me-2"></i>CREATE ORGANIZATIONAL UNIT
            </h5>
            <a href="{{ url('/departments') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-1"></i> BACK TO DIRECTORY
            </a>
        </div>

        <div class="card-body p-5">
            <form id="departmentForm" action="{{ url('/departments/store') }}" method="POST">
                @csrf
                
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-7">
                        
                        <div class="mb-5">
                            <label class="form-label" for="name">
                                <i class="fas fa-building-user text-primary"></i> Official Department Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text-custom">
                                    <i class="fas fa-briefcase"></i>
                                </span>
                                <input type="text" 
                                       id="name"
                                       name="name" 
                                       class="form-control-custom @error('name') is-invalid @enderror"
                                       placeholder="e.g. Technical Operations, Strategic Marketing" 
                                       value="{{ old('name') }}"
                                       required 
                                       autofocus>
                            </div>
                            @error('name')
                                <small class="text-danger d-block mt-2 fw-600">
                                    <i class='fas fa-exclamation-circle me-1'></i> {{ $message }}
                                </small>
                            @enderror
                            
                            <div class="mt-4 p-4 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid var(--brand-indigo);">
                                <h6 class="fw-bold text-dark small mb-2">Policy Guidelines:</h6>
                                <ul class="text-muted small mb-0 ps-3">
                                    <li>Ensure department names are formal and accurately represent the team function.</li>
                                    <li>Avoid using special characters or excessive abbreviations.</li>
                                    <li>This name will appear on official personnel documentation and system logs.</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-center">
                    <button type="submit" class="btn btn-action-save px-5 shadow-sm" id="submitBtn">
                        <i class='fas fa-plus-square me-2'></i> INITIALIZE DEPARTMENT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('departmentForm');
    const submitBtn = document.getElementById('submitBtn');
    const inputField = document.getElementById('name');

    // UX: Automatic Title Case formatting
    inputField.addEventListener('blur', function() {
        if(this.value.trim() !== "") {
            this.value = this.value.trim().replace(/\b\w/g, l => l.toUpperCase());
        }
    });

    // Loading State Feedback
    form.addEventListener('submit', function() {
        if(inputField.value.trim() !== "") {
            Swal.fire({
                title: 'Provisioning Unit...',
                text: 'Creating the department record in your infrastructure.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESSING...';
        }
    });

    // Hotkey Logic: Ctrl+S to Save
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.requestSubmit();
        }
    });
});
</script>

@endsection