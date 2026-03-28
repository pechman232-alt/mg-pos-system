@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
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
        --font-main: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-main);
        background-color: var(--bg-body);
        font-size: 0.82rem;
        color: #1e293b;
        -webkit-font-smoothing: antialiased;
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* --- PREMIUM CARD STYLING --- */
    .full-width-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        background: #fff;
        overflow: hidden;
    }

    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%) !important;
        padding: 22px 30px;
        border-bottom: none;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.1rem;
        margin: 0;
        letter-spacing: -0.2px;
    }

    /* --- FORM ELEMENTS --- */
    .form-label {
        font-weight: 800;
        font-size: 0.68rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.8px;
    }

    .form-control {
        height: 46px;
        padding: 10px 18px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.85rem;
        background-color: #fcfdfe;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: var(--brand-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
        outline: none;
    }

    /* --- PERMISSION MATRIX --- */
    .permission-matrix {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .permission-card {
        background: #fcfdfe;
        border: 1.5px solid #edf2f7;
        border-radius: 16px;
        padding: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .permission-card:hover {
        border-color: var(--brand-indigo);
        background: #fff;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
    }

    .main-perm-wrapper {
        border-bottom: 1.5px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .sub-perm-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-left: 5px;
    }

    /* Custom Switch & Checkbox */
    .form-check-input { cursor: pointer; }
    .form-check-input:checked {
        background-color: var(--brand-indigo);
        border-color: var(--brand-indigo);
    }

    .form-check-label { cursor: pointer; font-weight: 600; }

    /* --- BUTTONS --- */
    .btn-save {
        background: linear-gradient(90deg, #1e293b, #0f172a);
        color: white;
        border: none;
        font-weight: 800;
        padding: 16px 30px;
        border-radius: 14px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.4s ease;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.15);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-save:hover { 
        transform: translateY(-3px); 
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.25); 
        filter: brightness(1.2);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 0.7rem;
        font-weight: 700;
        padding: 9px 18px;
        text-decoration: none;
        border-radius: 10px;
        transition: 0.3s;
    }
    .btn-back:hover { background: #fff; color: var(--brand-indigo); }

    .animate-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card full-width-card animate-up">
        <div class="card-header-gradient d-flex justify-content-between align-items-center">
            <h5 class="header-title">
                <i class="fas fa-shield-halved me-2"></i>CREATE SECURITY ROLE <span style="font-family: 'Noto Sans Khmer'; font-weight: 400; font-size: 0.75rem; opacity: 0.8; margin-left: 10px;">បង្កើតតួនាទីថ្មី</span>
            </h5>
            <a href="{{ url('/userRoles') }}" class="btn-back">
                <i class="fas fa-chevron-left me-1"></i> BACK TO LIST
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ url('/userRoles/store') }}" method="POST" id="userRoleForm">
                @csrf
                
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-id-card-clip text-primary"></i> Role Designation Name <span style="font-family: 'Noto Sans Khmer'; text-transform: none; margin-left: 5px;">(ឈ្មោះតួនាទី)</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. Branch Manager" value="{{ old('name') }}" required autocomplete="off">
                        @error('name') <small class="text-danger mt-1 d-block fw-bold">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                    <h6 class="fw-800 text-dark m-0"><i class="fas fa-key me-2 text-warning"></i>ACCESS CONTROL LIST <span style="font-family: 'Noto Sans Khmer'; font-weight: 400; font-size: 0.75rem; color: #64748b; margin-left: 8px;">(កំណត់សិទ្ធិចូលប្រើប្រាស់)</span></h6>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-800 px-3" id="selectAllBtn" style="font-size: 0.65rem;">
                        <i class="fas fa-check-double me-1"></i> SELECT ALL
                    </button>
                </div>

                <div class="permission-matrix">
                    @foreach($mainMenus as $mainMenu)
                        <div class="permission-card">
                            <div class="main-perm-wrapper">
                                <div class="form-check form-switch">
                                    <input class="form-check-input main-permission" type="checkbox" 
                                           name="ch_main[]" value="{{ $mainMenu->id }}" 
                                           id="main_{{ $mainMenu->id }}" data-main-id="{{ $mainMenu->id }}">
                                    <label class="form-check-label text-dark" for="main_{{ $mainMenu->id }}">
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
                                               id="sub_{{ $subMenu->id }}" data-parent="{{ $mainMenu->id }}">
                                        <label class="form-check-label small text-muted fw-500" for="sub_{{ $subMenu->id }}">
                                            {{ $subMenu->name }}
                                        </label>
                                    </div>
                                @empty
                                    <small class="text-muted opacity-50 italic">No sub-modules</small>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-top">
                    <button type="submit" class="btn btn-save w-100" id="btnSubmit">
                        <i class="fas fa-cloud-arrow-up me-2"></i> AUTHORIZE & SAVE SECURITY ROLE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainPermissions = document.querySelectorAll('.main-permission');
    const subPermissions = document.querySelectorAll('.sub-permission');
    const selectAllBtn = document.getElementById('selectAllBtn');

    // 1. Toggle sub-permissions when main is toggled
    mainPermissions.forEach(main => {
        main.addEventListener('change', function() {
            const parentId = this.dataset.mainId;
            const relatedSubs = document.querySelectorAll(`.sub-permission[data-parent="${parentId}"]`);
            relatedSubs.forEach(sub => {
                sub.checked = this.checked;
            });
        });
    });

    // 2. Auto-check main if any sub is checked
    subPermissions.forEach(sub => {
        sub.addEventListener('change', function() {
            const parentId = this.dataset.parent;
            const mainCheckbox = document.getElementById(`main_${parentId}`);
            const relatedSubs = document.querySelectorAll(`.sub-permission[data-parent="${parentId}"]`);
            const anySubChecked = Array.from(relatedSubs).some(s => s.checked);
            
            if (this.checked) {
                mainCheckbox.checked = true;
            } else if (!anySubChecked) {
                // Optional: Uncheck main if no subs are checked
                // mainCheckbox.checked = false;
            }
        });
    });

    // 3. Select All Toggle
    selectAllBtn.addEventListener('click', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(allCheckboxes).some(cb => !cb.checked);
        
        allCheckboxes.forEach(cb => cb.checked = anyUnchecked);
        
        this.innerHTML = anyUnchecked ? 
            '<i class="fas fa-times-circle me-1"></i> DESELECT ALL' : 
            '<i class="fas fa-check-double me-1"></i> SELECT ALL';
        
        this.classList.toggle('btn-outline-danger', anyUnchecked);
        this.classList.toggle('btn-outline-primary', !anyUnchecked);
    });

    // 4. Form Loading State with SweetAlert2
    document.getElementById('userRoleForm').onsubmit = function() {
        Swal.fire({
            title: 'Initializing Role...',
            text: 'Securing access protocols',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        document.getElementById('btnSubmit').disabled = true;
    };
});
</script>

@endsection