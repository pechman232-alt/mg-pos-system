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

    .id-badge {
        background: #EEF2FF; color: var(--primary); padding: 4px 10px; 
        border-radius: 6px; font-size: 0.85rem; border: 1px solid #C7D2FE; margin-left: 8px;
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

    .form-control-custom::placeholder { color: #9CA3AF; font-weight: 400; text-transform: none; }
    
    .is-invalid { border-color: #EF4444 !important; background-color: #FEF2F2 !important; }

    .text-error-custom {
        color: #EF4444; font-size: 0.8rem; font-weight: 500;
        margin-top: 6px; display: flex; align-items: center; gap: 4px;
    }

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
                <i class="far fa-id-card text-muted"></i> MODIFY MEMBER CARD IDENTIFIER 
                <span class="id-badge">#{{ $member_card->id }}</span>
            </h5>
            <a href="{{ url('/member-cards') }}" class="btn-back-clean">
                <i class="fas fa-times"></i> Discard Changes
            </a>
        </div>
        
        <div class="card-body-custom">
            <form id="updateCardForm" action="{{ url('member-cards/update', encrypt($member_card->id)) }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-8 col-lg-6 mb-4 stagger-1">
                        <label class="form-label" for="member_card_value">
                            <i class="fas fa-barcode"></i> Update Unique Serial Number
                        </label>
                        <input type="text" id="member_card_value" name="member_card" 
                               placeholder="Enter updated card identifier..." 
                               class="form-control-custom @error('member_card') is-invalid @enderror" 
                               value="{{ old('member_card', $member_card->member_card) }}" 
                               required autofocus autocomplete="off" style="text-transform: uppercase;">
                    
                        @error('member_card')
                            <div class="text-error-custom">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="mt-4 pt-4 border-top d-flex gap-3 stagger-1">
                    <button type="button" class="btn-save" onclick="confirmUpdate()">
                        <i class="fas fa-arrows-rotate"></i> SYNC & UPDATE ASSET
                    </button>
                    <a href="{{ url('/member-cards') }}" class="btn-cancel">
                        CANCEL
                    </a>
                </div>
                <input type="hidden" name="last_url" value="{{ url()->previous() }}">
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function confirmUpdate() {
        const newValue = document.getElementById('member_card_value').value;

        if (!newValue.trim()) {
            Swal.fire({ 
                icon: 'error', 
                title: 'Input Missing', 
                text: 'Please enter a valid serial identifier.',
                confirmButtonColor: '#4F46E5',
                customClass: { popup: 'rounded-4 shadow-sm border-0' }
            });
            return;
        }

        Swal.fire({
            title: 'Authorize Update?',
            html: `Change identifier to:<br><b style="color:var(--primary); font-size: 1.2rem; display:block; margin-top:10px;">${newValue.toUpperCase()}</b>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#E5E7EB',
            confirmButtonText: 'Yes, Commit Sync',
            cancelButtonText: '<span style="color:#4B5563">Abort</span>',
            fontFamily: 'Inter',
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-sm border-0',
                actions: 'd-flex flex-wrap justify-content-center gap-2 w-100 mt-4',
                confirmButton: 'btn fw-bold rounded-3 px-4 py-2 border-0 shadow-sm text-white',
                cancelButton: 'btn fw-bold rounded-3 px-4 py-2 border-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Syncing Database...',
                    text: 'Please wait while we update the registry.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); },
                    customClass: { popup: 'rounded-4 shadow-sm border-0' }
                });
                document.getElementById('updateCardForm').submit();
            }
        });
    }
</script>

@endsection