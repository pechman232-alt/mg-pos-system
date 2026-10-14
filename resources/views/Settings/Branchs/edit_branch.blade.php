@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
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
        
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
    }

    body { font-family: var(--font-body); background-color: var(--bg-body); color: var(--text-main); margin: 0; -webkit-font-smoothing: antialiased; }
    .fluid-page-wrapper { width: 100% !important; max-width: 100% !important; padding: 24px 32px !important; }

    /* Animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .animate-card { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .stagger-1 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 100ms; opacity: 0; }
    .stagger-2 { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; animation-delay: 200ms; opacity: 0; }

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
        color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px; text-transform: uppercase;
    }

    .btn-back-clean {
        background: #FFFFFF; color: var(--text-muted); border: 1px solid var(--border-color);
        font-size: 0.875rem; font-weight: 600; padding: 8px 16px; border-radius: 8px;
        text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-back-clean:hover { background: #F3F4F6; color: var(--text-main); }

    /* --- INPUTS --- */
    .card-body-custom { padding: 32px 40px !important; }

    .form-label {
        font-weight: 600; font-size: 0.8rem; color: #4B5563;
        margin-bottom: 8px; display: flex; align-items: center; gap: 6px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .form-label i { color: #9CA3AF; font-size: 0.9rem; }

    .form-control-custom, .form-select-custom {
        height: 48px; padding: 12px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); font-size: 0.9rem;
        background-color: var(--input-bg); color: var(--text-main);
        transition: all 0.2s ease; width: 100%; font-weight: 500;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        background-color: #FFFFFF; border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none;
    }

    .form-control-custom::placeholder { color: #9CA3AF; font-weight: 400; text-transform: none; }
    
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    .text-error-custom {
        color: #EF4444; font-size: 0.8rem; font-weight: 500;
        margin-top: 6px; display: flex; align-items: center; gap: 4px;
    }

    /* Info Box */
    .info-box {
        background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 8px;
        padding: 16px; display: flex; gap: 12px; align-items: flex-start;
    }
    .info-box i { color: #3B82F6; font-size: 1.1rem; margin-top: 2px; }
    .info-box p { margin: 0; font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; }

    /* --- ACTION BUTTONS --- */
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

<div class="fluid-page-wrapper">
    <div class="form-card animate-card">
        
        <!-- Clean Header -->
        <div class="card-header-clean">
            <h5 class="header-title">
                <i class="fas fa-building text-muted"></i> 
                {{ isset($branch) ? 'UPDATE INFRASTRUCTURE RECORD' : 'NEW BRANCH REGISTRY' }}
            </h5>
            <a href="{{ url('/branchs') }}" class="btn-back-clean">
                <i class="fas fa-arrow-left"></i> Back to Network
            </a>
        </div>

        <div class="card-body-custom">
            <form id="branchForm" action="{{ isset($branch) ? url('branch/update', encrypt($branch->id)) : url('/branchs/store') }}" method="POST">
                @csrf

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        
                        <div class="mb-4 stagger-1">
                            <label class="form-label" for="branch_name">
                                <i class="fas fa-map-pin"></i> Registered Branch Name
                            </label>
                            <input type="text" id="branch_name" name="branch_name" 
                                   class="form-control-custom @error('branch_name') is-invalid @enderror" 
                                   placeholder="e.g. Phnom Penh Central Hub"
                                   value="{{ old('branch_name', $branch->branch_name ?? '') }}"
                                   required autofocus autocomplete="off">
                            @error('branch_name')
                                <div class="text-error-custom">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4 stagger-1">
                            <label class="form-label" for="status">
                                <i class="fas fa-signal"></i> Operational Status
                            </label>
                            <select name="status" id="status" class="form-select-custom">
                                <option value="1" {{ (old('status', $branch->status ?? '') == '1') ? 'selected' : '' }}>Active (Live Hub)</option>
                                <option value="0" {{ (old('status', $branch->status ?? '') == '0') ? 'selected' : '' }}>Inactive (Decommissioned)</option>
                            </select>
                        </div>

                        <div class="info-box stagger-2">
                            <i class="fas fa-info-circle"></i>
                            <p>Registering or updating a branch impacts global logistics routes and personnel assignments. Ensure the nomenclature is distinct and operational status is accurate.</p>
                        </div>

                    </div>
                </div>

                <!-- Action Footer -->
                <div class="mt-5 pt-4 border-top d-flex justify-content-center gap-3 stagger-2">
                    <button type="submit" id="submitBtn" class="btn-save">
                        <i class="fas fa-cloud-upload-alt"></i> 
                        {{ isset($branch) ? 'SYNCHRONIZE CHANGES' : 'COMMISSION BRANCH' }}
                    </button>
                    <a href="{{ url('/branchs') }}" class="btn-cancel">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('branchForm');
    const submitBtn = document.getElementById('submitBtn');
    const branchInput = document.getElementById('branch_name');

    // UX: Automatic Title Case formatting
    branchInput.addEventListener('blur', function() {
        if(this.value.trim() !== "") {
            this.value = this.value.trim().replace(/\b\w/g, l => l.toUpperCase());
        }
    });

    // Handle Loading State and SweetAlert Feedback
    form.addEventListener('submit', function(e) {
        if(branchInput.value.trim() !== "") {
            Swal.fire({
                title: 'Updating Records...',
                text: 'Please wait while we synchronize the infrastructure data.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); },
                customClass: { popup: 'rounded-4 shadow-sm border-0', title: 'font-weight-bold' }
            });
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> PROCESSING...';
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