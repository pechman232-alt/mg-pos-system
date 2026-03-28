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

    /* 4-Color Gradient Header */
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

    /* --- PERMISSION MATRIX --- */
    .permission-matrix {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .permission-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        transition: all 0.3s ease;
    }

    .permission-card:hover {
        border-color: var(--brand-indigo);
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .main-perm-wrapper {
        border-bottom: 1.5px solid #e2e8f0;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .sub-perm-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-left: 8px;
    }

    .form-check-input:checked {
        background-color: var(--brand-indigo);
        border-color: var(--brand-indigo);
    }

    /* --- BUTTONS --- */
    .btn-update {
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

    .btn-update:hover { background-color: #000; transform: translateY(-2px); color: white; }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 18px;
        text-decoration: none;
        border-radius: 8px;
    }

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
                <i class="fas fa-shield-halved me-2"></i>UPDATE SECURITY ROLE: {{ $userRole->name }}
            </h5>
            <a href="{{ url('/userRoles') }}" class="btn btn-back">
                <i class="fas fa-arrow-left me-1"></i> DISCARD CHANGES
            </a>
        </div>

        <div class="card-body p-5">
            <form action="{{ url('/userRoles/update', encrypt($userRole->id)) }}" method="POST" id="userRoleForm">
                @csrf
                
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-tag text-primary"></i> Role Designation Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. Senior Administrator" value="{{ old('name', $userRole->name) }}" required>
                        @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                    </div>
                </div>

                <hr class="my-5">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="fw-800 text-dark m-0"><i class="fas fa-key me-2 text-warning"></i>ACCESS CONTROL POLICY</h6>
                        <small class="text-muted">Modify access levels for this specific role designation</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" id="selectAllBtn">
                            <i class="fas fa-check-double me-1"></i> SELECT ALL
                        </button>
                    </div>
                </div>

                <div class="permission-matrix">
                    @foreach($mainMenus as $mainMenu)
                        <div class="permission-card">
                            <div class="main-perm-wrapper">
                                <div class="form-check form-switch">
                                    <input class="form-check-input main-permission" type="checkbox" 
                                           name="ch_main[]" value="{{ $mainMenu->id }}" 
                                           id="main_{{ $mainMenu->id }}" data-main-id="{{ $mainMenu->id }}"
                                           {{ $mainMenu->checked == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="main_{{ $mainMenu->id }}">
                                        {{ $mainMenu->name }}
                                    </label>
                                </div>
                            </div>

                            <div class="sub-perm-list">
                                @php $subMenusForMain = $subMenus->where('sub_of', $mainMenu->id); @endphp
                                @forelse($subMenusForMain as $subMenu)
                                    <div class="form-check">
                                        <input class="form-check-input sub-permission" type="checkbox" 
                                               name="ch_sub[]" value="{{ $subMenu->id }}" 
                                               id="sub_{{ $subMenu->id }}" data-parent="{{ $mainMenu->id }}"
                                               {{ $subMenu->checked == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label small text-muted fw-500" for="sub_{{ $subMenu->id }}">
                                            {{ $subMenu->name }}
                                        </label>
                                    </div>
                                @empty
                                    <small class="text-muted italic opacity-50">Global Module (No Sub-items)</small>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-top">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-update w-100" id="btnSubmit">
                                <i class="fas fa-save me-2"></i> COMMIT CHANGES & SYNC
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainPermissions = document.querySelectorAll('.main-permission');
    const selectAllBtn = document.getElementById('selectAllBtn');

    // Auto-Toggle children when main category is selected
    mainPermissions.forEach(main => {
        main.addEventListener('change', function() {
            const parentId = this.dataset.mainId;
            const relatedSubs = document.querySelectorAll(`.sub-permission[data-parent="${parentId}"]`);
            relatedSubs.forEach(sub => {
                sub.checked = this.checked;
            });
        });
    });

    // Global Toggle (Select/Deselect All)
    selectAllBtn.addEventListener('click', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(allCheckboxes).some(cb => !cb.checked);
        allCheckboxes.forEach(cb => cb.checked = anyUnchecked);
        
        this.innerHTML = anyUnchecked ? 
            '<i class="fas fa-times-circle me-1"></i> DESELECT ALL' : 
            '<i class="fas fa-check-double me-1"></i> SELECT ALL';
    });

    // Loading State Feedback
    document.getElementById('userRoleForm').onsubmit = function() {
        Swal.fire({
            title: 'Updating Permissions...',
            text: 'Reconfiguring security protocols in the database',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        document.getElementById('btnSubmit').disabled = true;
    };
});
</script>

@endsection