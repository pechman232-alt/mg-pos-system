@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ================= VARIABLES & BRANDING ================= */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --border-color: #cbd5e1; 
        --font-main: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
    }

    body { 
        font-family: var(--font-main); 
        background-color: var(--bg-body); 
        color: #1e293b;
        -webkit-font-smoothing: antialiased;
    }

    /* --- ANIMATIONS --- */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-card { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    .stagger-1 { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 100ms; opacity: 0; }
    .stagger-2 { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 200ms; opacity: 0; }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    /* --- PREMIUM CARD STYLING --- */
    .full-width-card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        background: #fff;
        overflow: hidden;
    }

    .card-header-gradient {
        background: linear-gradient(135deg, var(--brand-indigo) 0%, var(--brand-purple) 100%) !important;
        padding: 20px 25px;
        border-bottom: 1px solid var(--border-color);
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.15rem;
        margin: 0;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
    }

    /* --- FORM ELEMENTS --- */
    .form-label {
        font-weight: 800;
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.5px;
    }

    .form-control {
        height: 48px;
        padding: 10px 18px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        font-size: 0.9rem;
        background-color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
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
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .permission-card {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 18px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .permission-card:hover {
        border-color: var(--brand-indigo);
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }

    .main-perm-wrapper {
        border-bottom: 1px dashed var(--border-color);
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .sub-perm-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-left: 10px;
    }

    /* Custom Switch & Checkbox */
    .form-check-input { 
        cursor: pointer; 
        width: 20px; height: 20px; 
        border: 1px solid var(--border-color);
        transition: 0.2s;
    }
    .form-switch .form-check-input { height: 20px; width: 36px; }
    
    .form-check-input:checked {
        background-color: var(--brand-indigo);
        border-color: var(--brand-indigo);
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
    }

    .form-check-label { cursor: pointer; font-weight: 700; color: #0f172a; margin-top: 2px; }
    .sub-perm-list .form-check-label { font-weight: 600; color: #475569; font-size: 0.8rem; }

    /* --- BUTTONS --- */
    .btn-update {
        background: #0f172a;
        color: white;
        border: none;
        font-weight: 800;
        padding: 14px 30px;
        border-radius: 10px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-update:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2); 
        background: #000;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 8px 18px;
        text-decoration: none;
        border-radius: 8px;
        transition: 0.3s;
    }
    .btn-back:hover { background: #fff; color: var(--brand-indigo); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card full-width-card animate-card">
        
        <!-- Header -->
        <div class="card-header-gradient d-flex justify-content-between align-items-center">
            <h5 class="header-title">
                <i class="fas fa-shield-halved me-2"></i>UPDATE SECURITY ROLE: <span class="ms-2 text-warning">{{ $userRole->name }}</span>
            </h5>
            <a href="{{ url('/userRoles') }}" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> DISCARD CHANGES
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ url('/userRoles/update', encrypt($userRole->id)) }}" method="POST" id="userRoleForm">
                @csrf
                
                <!-- Role Name Input -->
                <div class="row mb-5 stagger-1">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-tag text-indigo-500"></i> Role Designation Name <span style="font-family: 'Noto Sans Khmer'; text-transform: none; margin-left: 5px; color: #94a3b8;">(ឈ្មោះតួនាទី)</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. Senior Administrator" value="{{ old('name', $userRole->name) }}" required>
                        @error('name') <small class="text-danger mt-1 d-block fw-bold">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Access Control List Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom stagger-1">
                    <div>
                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem; text-transform: uppercase;">
                            <i class="fas fa-key me-2 text-warning"></i> ACCESS CONTROL POLICY
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">Modify access levels for this specific role designation</small>
                    </div>
                    <button type="button" class="btn btn-light border btn-sm rounded-pill fw-bold px-3 text-indigo-600 shadow-sm" id="selectAllBtn" style="font-size: 0.7rem;">
                        <i class="fas fa-check-double me-1"></i> SELECT ALL
                    </button>
                </div>

                <!-- Permissions Grid -->
                <div class="permission-matrix">
                    @foreach($mainMenus as $index => $mainMenu)
                        <!-- បន្ថែម Delay Animation តាមលំដាប់ -->
                        <div class="permission-card" style="animation-delay: {{ ($index * 50) + 150 }}ms; opacity: 0;">
                            
                            <!-- Main Module -->
                            <div class="main-perm-wrapper">
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input main-permission" type="checkbox" 
                                           name="ch_main[]" value="{{ $mainMenu->id }}" 
                                           id="main_{{ $mainMenu->id }}" data-main-id="{{ $mainMenu->id }}"
                                           {{ $mainMenu->checked == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="main_{{ $mainMenu->id }}">
                                        {{ $mainMenu->name }}
                                    </label>
                                </div>
                            </div>

                            <!-- Sub Modules -->
                            <div class="sub-perm-list">
                                @php $subMenusForMain = $subMenus->where('sub_of', $mainMenu->id); @endphp
                                @forelse($subMenusForMain as $subMenu)
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input sub-permission" type="checkbox" 
                                               name="ch_sub[]" value="{{ $subMenu->id }}" 
                                               id="sub_{{ $subMenu->id }}" data-parent="{{ $mainMenu->id }}"
                                               {{ $subMenu->checked == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sub_{{ $subMenu->id }}">
                                            {{ $subMenu->name }}
                                        </label>
                                    </div>
                                @empty
                                    <small class="text-muted opacity-50 fst-italic">Global Module (No Sub-items)</small>
                                @endforelse
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Action Footer -->
                <div class="mt-5 pt-4 border-top stagger-2">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-update w-100 shadow-sm" id="btnSubmit">
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
    const subPermissions = document.querySelectorAll('.sub-permission');
    const selectAllBtn = document.getElementById('selectAllBtn');

    // 1. Check initial state for "Select All" button
    function updateSelectAllButton() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(allCheckboxes).some(cb => !cb.checked);
        if(!anyUnchecked) {
            selectAllBtn.innerHTML = '<i class="fas fa-times-circle me-1"></i> DESELECT ALL';
            selectAllBtn.className = 'btn btn-danger btn-sm rounded-pill fw-bold px-3 shadow-sm';
        } else {
            selectAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i> SELECT ALL';
            selectAllBtn.className = 'btn btn-light border btn-sm rounded-pill fw-bold px-3 text-indigo-600 shadow-sm';
        }
    }
    updateSelectAllButton();

    // 2. Toggle sub-permissions when main is toggled
    mainPermissions.forEach(main => {
        main.addEventListener('change', function() {
            const parentId = this.dataset.mainId;
            const relatedSubs = document.querySelectorAll(`.sub-permission[data-parent="${parentId}"]`);
            relatedSubs.forEach(sub => {
                sub.checked = this.checked;
            });
            updateSelectAllButton();
        });
    });

    // 3. Auto-check/uncheck main if subs are changed
    subPermissions.forEach(sub => {
        sub.addEventListener('change', function() {
            const parentId = this.dataset.parent;
            const mainCheckbox = document.getElementById(`main_${parentId}`);
            const relatedSubs = document.querySelectorAll(`.sub-permission[data-parent="${parentId}"]`);
            const anySubChecked = Array.from(relatedSubs).some(s => s.checked);
            
            if (this.checked) {
                mainCheckbox.checked = true;
            } else if (!anySubChecked) {
                // ប្រសិនបើដោះធីកកូនអស់ នោះវានឹងដោះធីកមេដែរ
                mainCheckbox.checked = false;
            }
            updateSelectAllButton();
        });
    });

    // 4. Select All Toggle
    selectAllBtn.addEventListener('click', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(allCheckboxes).some(cb => !cb.checked);
        
        allCheckboxes.forEach(cb => cb.checked = anyUnchecked);
        updateSelectAllButton();
    });

    // 5. Form Loading State with SweetAlert2
    document.getElementById('userRoleForm').onsubmit = function() {
        Swal.fire({
            title: 'Updating Permissions...',
            text: 'Reconfiguring security protocols in the database',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SYNCING DATA...';
    };
});
</script>

@endsection