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
    }

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
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control {
        height: 48px;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.85rem;
        background-color: #fcfdfe;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* --- BUTTONS --- */
    .btn-save {
        background-color: #1e293b;
        color: white;
        border: none;
        font-weight: 800;
        padding: 14px 30px;
        border-radius: 12px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        text-transform: uppercase;
    }

    .btn-save:hover { 
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

    /* Loading state override */
    .btn-loading { position: relative; color: transparent !important; pointer-events: none; }
    .btn-loading::after {
        content: '';
        position: absolute;
        left: 50%; top: 50%;
        transform: translate(-50%, -50%);
        width: 20px; height: 20px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: translate(-50%, -50%) rotate(360deg); } }

    .animate-up {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeInUp 0.4s ease forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card full-width-card animate-up">
        <div class="card-header-gradient d-flex justify-content-between align-items-center">
            <h5 class="header-title">
                <i class="fas fa-plus-circle me-2"></i>ADD NEW BRANCH OFFICE
            </h5>
            <a href="{{ url('/branchs') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-1"></i> BACK TO LIST
            </a>
        </div>

        <div class="card-body p-5">
            <form id="branchForm" action="{{ url('/branchs/store') }}" method="POST">
                @csrf
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label" for="branchName">
                                <i class="fas fa-building text-primary"></i> Official Branch Name
                            </label>
                            <div class="input-group">
                                <input type="text" 
                                       id="branchName"
                                       name="branch_name" 
                                       class="form-control @error('branch_name') is-invalid @enderror"
                                       placeholder="e.g. Phnom Penh Central Hub" 
                                       value="{{ old('branch_name') }}"
                                       required 
                                       autofocus>
                            </div>
                            @error('branch_name')
                                <small class="text-danger d-block mt-2 fw-600">
                                    <i class='fas fa-exclamation-circle me-1'></i> {{ $message }}
                                </small>
                            @enderror
                            <div class="mt-3 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <small class="text-muted d-flex align-items-start">
                                    <i class='fas fa-info-circle me-2 text-info mt-1'></i> 
                                    Branch names are used for logistics tracking and personnel assignment. Ensure the name is distinct and recognizable.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-center">
                    <button type="submit" class="btn btn-save px-5 shadow-sm" id="submitBtn">
                        <i class='fas fa-check-circle me-2'></i> INITIALIZE BRANCH
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('branchForm');
    const submitBtn = document.getElementById('submitBtn');
    const branchInput = document.getElementById('branchName');

    // Auto-capitalize first letters for professional look
    branchInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = this.value
                .toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ')
                .trim();
        }
    });

    // Loading State Feedback
    form.addEventListener('submit', function() {
        if(branchInput.value.trim() !== "") {
            Swal.fire({
                title: 'Registering Branch...',
                text: 'Updating global infrastructure records',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });

    // Keyboard Hotkeys
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.requestSubmit();
        }
        if (e.key === 'Escape') {
            window.location.href = "{{ url('/branchs') }}";
        }
    });
});
</script>

@endsection