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
        border-radius: 0 12px 12px 0;
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
    .btn-action-update {
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

    .btn-action-update:hover { 
        background-color: #000; 
        transform: translateY(-2px); 
        color: white; 
    }

    .btn-soft-danger {
        background-color: #fff1f2;
        color: #e11d48;
        border: 1.5px solid #ffe4e6;
        padding: 14px 25px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .btn-soft-danger:hover {
        background-color: #e11d48;
        color: #fff;
        border-color: #e11d48;
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
                <i class="fas fa-flask-vial me-2"></i>MODIFY LUBRICANT SPECIFICATION
            </h5>
            <a href="{{ url('/engineOils') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-1"></i> BACK TO INVENTORY
            </a>
        </div>

        <div class="card-body p-5">
            <form id="engineOilEditForm" action="{{ url('engineOils/update', encrypt($engine_oil->id)) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        
                        <div class="mb-5">
                            <label class="form-label" for="name">
                                <i class="fas fa-droplet text-primary"></i> Lubricant Brand & Grade Profile
                            </label>
                            <div class="input-group shadow-sm" style="border-radius: 12px;">
                                <span class="input-group-text-custom">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <input type="text" 
                                       id="name"
                                       name="name" 
                                       class="form-control-custom @error('name') is-invalid @enderror"
                                       placeholder="e.g. Shell Helix Ultra 5W-40" 
                                       value="{{ old('name', $engine_oil->name) }}"
                                       required 
                                       autofocus>
                            </div>
                            @error('name')
                                <small class="text-danger d-block mt-2 fw-600">
                                    <i class='fas fa-exclamation-circle me-1'></i> {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="p-4 rounded-3 mb-5" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid var(--brand-indigo);">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small fw-bold d-block uppercase mb-1">Stock ID</span>
                                    <span class="text-dark fw-800">#OIL-{{ str_pad($engine_oil->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small fw-bold d-block uppercase mb-1">Registration Date</span>
                                    <span class="text-dark fw-600">{{ $engine_oil->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <button type="button" class="btn btn-soft-danger" onclick="confirmDelete('{{ encrypt($engine_oil->id) }}', '{{ $engine_oil->name }}')">
                        <i class='fas fa-trash-can me-2'></i> DECOMMISSION SPECIFICATION
                    </button>

                    <div class="d-flex gap-3">
                        <a href="{{ url('/engineOils') }}" class="btn btn-link text-muted text-decoration-none fw-600">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="btn btn-action-update px-5 shadow-sm">
                            <i class='fas fa-save me-2'></i> SYNCHRONIZE CHANGES
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('engineOilEditForm');
    const submitBtn = document.getElementById('submitBtn');
    const inputField = document.getElementById('name');

    // UX: Automatic Title Case formatting
    inputField.addEventListener('blur', function() {
        if(this.value.trim() !== "") {
            this.value = this.value.trim().replace(/\b\w/g, l => l.toUpperCase());
        }
    });

    // Loading State & SweetAlert Feedback
    form.addEventListener('submit', function() {
        if(inputField.value.trim() !== "") {
            Swal.fire({
                title: 'Synchronizing...',
                text: 'Updating lubricant data in secure database.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SYNCING...';
        }
    });

    // Hotkey: Ctrl+S to save instantly
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.requestSubmit();
        }
    });
});

// Professional SweetAlert Delete Flow
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Specification?',
        html: `Are you sure you want to remove <b>${name}</b>?<br><span class="text-danger small">This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, Delete Record',
        cancelButtonText: 'Keep it',
        reverseButtons: true,
        fontFamily: 'Plus Jakarta Sans'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Processing...', didOpen: () => { Swal.showLoading(); } });
            const deleteForm = document.getElementById('delete-form');
            deleteForm.action = "{{ url('engineOils/destroy') }}/" + id;
            deleteForm.submit();
        }
    });
}
</script>

@endsection