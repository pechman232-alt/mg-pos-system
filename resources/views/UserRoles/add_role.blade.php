@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&family=Noto+Sans+Khmer:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        
        --font-body: 'Inter', 'Noto Sans Khmer', sans-serif;
        --font-display: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif;
    }

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); -webkit-font-smoothing: antialiased; }
    .container-xxl { max-width: 100% !important; width: 100% !important; padding: 24px 32px !important; }

    /* Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-card { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .stagger-1 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 100ms; opacity: 0; }
    .stagger-2 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 200ms; opacity: 0; }

    /* --- FORM CARD --- */
    .form-card {
        background: var(--surface); border: 1px solid var(--border-color);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .card-header-clean {
        padding: 20px 24px; border-bottom: 1px solid var(--border-color);
        display: flex; justify-content: space-between; align-items: center;
        background: #FFFFFF;
    }

    .header-title {
        font-family: var(--font-display); font-size: 1.1rem; font-weight: 700;
        color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;
    }
    .kh-sub { font-size: 0.8rem; font-weight: 500; color: var(--text-muted); margin-left: 8px; border-left: 1px solid #D1D5DB; padding-left: 10px; }

    .btn-back-clean {
        background: #FFFFFF; color: var(--text-muted); border: 1px solid var(--border-color);
        font-size: 0.875rem; font-weight: 600; padding: 8px 16px; border-radius: 8px;
        text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-back-clean:hover { background: #F3F4F6; color: var(--text-main); }

    /* --- INPUTS --- */
    .form-label { font-weight: 600; font-size: 0.8rem; color: #4B5563; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    .form-control {
        height: 44px; padding: 8px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 0.875rem;
        background-color: var(--input-bg); color: var(--text-main); transition: all 0.2s ease;
    }
    .form-control:focus { background-color: #FFFFFF; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    /* --- PERMISSION MATRIX --- */
    .section-title {
        font-family: var(--font-display); font-size: 0.9rem; font-weight: 700; text-transform: uppercase;
        color: var(--text-main); display: flex; align-items: center; gap: 8px; margin: 0;
    }

    .btn-select-all {
        background: #F3F4F6; color: var(--text-main); border: 1px solid var(--border-color);
        font-size: 0.75rem; font-weight: 600; padding: 6px 12px; border-radius: 999px;
        transition: all 0.2s ease; cursor: pointer;
    }
    .btn-select-all:hover { background: #E5E7EB; }
    .btn-select-all.active { background: #FEE2E2; color: #DC2626; border-color: #FECACA; }

    .permission-matrix {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px; margin-top: 20px;
    }

    .permission-card {
        background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 10px;
        padding: 16px; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .permission-card:hover { border-color: var(--primary); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }

    .main-perm-wrapper { border-bottom: 1px solid #F3F4F6; padding-bottom: 12px; margin-bottom: 12px; }
    .sub-perm-list { display: flex; flex-direction: column; gap: 10px; padding-left: 12px; }

    /* Custom Checkboxes/Switches */
    .form-check-input { cursor: pointer; width: 18px; height: 18px; border: 1px solid #D1D5DB; transition: 0.2s; border-radius: 4px; }
    .form-switch .form-check-input { height: 20px; width: 36px; border-radius: 999px; }
    .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
    .form-check-input:focus { box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); border-color: var(--primary); }

    .form-check-label { cursor: pointer; font-weight: 600; color: var(--text-main); margin-top: 2px; font-size: 0.875rem; }
    .sub-perm-list .form-check-label { font-weight: 500; color: var(--text-muted); font-size: 0.8rem; }

    /* --- SUBMIT BUTTON --- */
    .btn-save {
        background-color: var(--primary); color: white; border: none;
        font-weight: 600; padding: 12px 24px; border-radius: 8px;
        font-size: 0.9rem; cursor: pointer; transition: all 0.2s ease;
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-save:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="form-card animate-card">
        
        <!-- Header -->
        <div class="card-header-clean">
            <h5 class="header-title">
                <i class="fas fa-shield-halved text-muted"></i> CREATE SECURITY ROLE 
                <span class="kh-sub">បង្កើតតួនាទីថ្មី</span>
            </h5>
            <a href="{{ url('/userRoles') }}" class="btn-back-clean">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="{{ url('/userRoles/store') }}" method="POST" id="userRoleForm">
                @csrf
                
                <!-- Role Name Input -->
                <div class="row mb-5 stagger-1">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="far fa-id-card"></i> Role Designation Name 
                            <span style="font-weight: 400; color: #9CA3AF; margin-left: 4px;">(ឈ្មោះតួនាទី)</span>
                        </label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. Branch Manager" value="{{ old('name') }}" required autocomplete="off">
                        @error('name') <small class="text-danger mt-1 d-block fw-medium">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- Access Control List Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom stagger-1">
                    <h6 class="section-title">
                        <i class="fas fa-key text-muted"></i> ACCESS CONTROL LIST 
                        <span style="font-weight: 400; font-size: 0.75rem; color: #9CA3AF; margin-left: 4px; text-transform: none;">(កំណត់សិទ្ធិចូលប្រើប្រាស់)</span>
                    </h6>
                    <button type="button" class="btn-select-all" id="selectAllBtn">
                        <i class="fas fa-check-double me-1"></i> Select All
                    </button>
                </div>

                <!-- Permissions Grid -->
                <div class="permission-matrix stagger-2">
                    @foreach($mainMenus as $index => $mainMenu)
                        <div class="permission-card">
                            
                            <!-- Main Module -->
                            <div class="main-perm-wrapper">
                                <div class="form-check form-switch d-flex align-items-center gap-2 m-0">
                                    <input class="form-check-input main-permission" type="checkbox" 
                                           name="ch_main[]" value="{{ $mainMenu->id }}" 
                                           id="main_{{ $mainMenu->id }}" data-main-id="{{ $mainMenu->id }}">
                                    <label class="form-check-label" for="main_{{ $mainMenu->id }}">
                                        {{ $mainMenu->name }}
                                    </label>
                                </div>
                            </div>

                            <!-- Sub Modules -->
                            <div class="sub-perm-list">
                                @php $subMenusForMain = $subMenus->where('sub_of', $mainMenu->id); @endphp
                                @forelse($subMenusForMain as $subMenu)
                                    <div class="form-check d-flex align-items-center gap-2 m-0">
                                        <input class="form-check-input sub-permission" type="checkbox" 
                                               name="ch_sub[]" value="{{ $subMenu->id }}" 
                                               id="sub_{{ $subMenu->id }}" data-parent="{{ $mainMenu->id }}">
                                        <label class="form-check-label" for="sub_{{ $subMenu->id }}">
                                            {{ $subMenu->name }}
                                        </label>
                                    </div>
                                @empty
                                    <small class="text-muted opacity-50 fst-italic" style="font-size: 0.75rem;">No sub-modules</small>
                                @endforelse
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Action Footer -->
                <div class="mt-5 pt-4 border-top stagger-2">
                    <button type="submit" class="btn-save" id="btnSubmit">
                        <i class="fas fa-check-circle"></i> AUTHORIZE & SAVE ROLE
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
                mainCheckbox.checked = false;
            }
        });
    });

    // 3. Select All Toggle (Clean Style)
    selectAllBtn.addEventListener('click', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        const anyUnchecked = Array.from(allCheckboxes).some(cb => !cb.checked);
        
        allCheckboxes.forEach(cb => cb.checked = anyUnchecked);
        
        if(anyUnchecked) {
            this.innerHTML = '<i class="fas fa-times me-1"></i> Deselect All';
            this.classList.add('active');
        } else {
            this.innerHTML = '<i class="fas fa-check-double me-1"></i> Select All';
            this.classList.remove('active');
        }
    });

    // 4. Form Loading State with SweetAlert2 (Clean Theme)
    document.getElementById('userRoleForm').onsubmit = function() {
        Swal.fire({
            title: 'Saving Role...',
            text: 'Please wait while we secure the protocols.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); },
            customClass: { popup: 'rounded-4 shadow-sm border-0', title: 'font-weight-bold' }
        });
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> PROCESSING...';
    };
});
</script>

@endsection