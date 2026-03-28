@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
    /* ====== BRAND IDENTITY ====== */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
        --radius: 16px;
    }

    body {
        background-color: var(--bg-body);
        font-family: var(--font-family);
        margin: 0;
    }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-page-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        padding: 1.5rem !important;
        animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ====== FULL-WIDTH CARD ====== */
    .card-custom {
        border: none !important;
        border-radius: var(--radius) !important;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.08) !important;
        background-color: #fff;
        overflow: hidden;
        width: 100%;
    }

    /* PREMIUM GRADIENT HEADER */
    .card-header-gradient {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%) !important;
        color: white !important;
        padding: 1.5rem 2.5rem !important;
        font-weight: 800;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 15px;
        border: none;
        letter-spacing: 0.5px;
    }

    /* ====== FORM ELEMENTS ====== */
    .card-body {
        padding: 4rem 3rem !important; /* Increased padding for widescreen */
    }

    .highlight-label {
        font-weight: 700;
        color: var(--brand-indigo); 
        background: rgba(79, 70, 229, 0.08);
        padding: 8px 16px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 1.5rem;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-control-premium {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        padding: 20px 25px;
        font-size: 18px !important;
        font-weight: 600;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        color: #1e293b;
        width: 100%;
    }

    .form-control-premium:focus {
        background-color: #fff;
        border-color: var(--brand-indigo) !important;
        box-shadow: 0 0 0 6px rgba(79, 70, 229, 0.1) !important;
        outline: none;
    }

    /* ====== ACTION BUTTONS ====== */
    .btn-publish {
        background: linear-gradient(135deg, var(--brand-indigo), var(--brand-purple)) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 800;
        padding: 18px 60px !important;
        border-radius: 14px;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.2);
        cursor: pointer;
        font-size: 14px;
    }

    .btn-publish:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(124, 58, 237, 0.4);
    }

    .btn-cancel {
        border: 1.5px solid #e2e8f0 !important;
        background-color: #fff;
        color: #64748b !important;
        font-weight: 700;
        padding: 18px 40px !important;
        border-radius: 14px;
        transition: 0.3s;
        text-decoration: none !important;
        display: inline-block;
    }

    .btn-cancel:hover {
        background-color: #fef2f2;
        color: var(--brand-red) !important;
        border-color: #fecaca !important;
    }
</style>

<div class="fluid-page-wrapper">
    <div class="card-custom">
        <div class="card-header-gradient">
            <i class="fas fa-crown"></i>
            NEW MEMBER CARD AUTHORIZATION
        </div>

        <div class="card-body">
            <form action="{{ url('/member-cards/store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="mb-5 col-12">
                        <label class="highlight-label" for="member_card">Card Security Identifier</label>
                        <input type="text" 
                               id="member_card"
                               placeholder="Assign digital identifier (e.g. TF-CARD-10029-2026)" 
                               class="form-control-premium @error('member_card') border-danger @enderror" 
                               name="member_card" 
                               value="{{ old('member_card') }}"
                               required autofocus>
                        
                        @error('member_card')
                            <div class="mt-3 text-danger fw-800 small">
                                <i class="fas fa-exclamation-triangle me-2"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mt-5 border-top pt-5 d-flex gap-4 justify-content-start">
                    <button type="submit" class="btn-publish shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> AUTHORIZE & SYNC RECORD
                    </button>
                    <a href="{{ url('/member-cards') }}" class="btn btn-cancel shadow-sm">
                        DISCARD
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /**
     * Trigger Confetti Celebration on Success session
     */
    window.onload = function() {
        @if(session('success'))
            var duration = 3 * 1000;
            var end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 4,
                    angle: 60,
                    spread: 60,
                    origin: { x: 0 },
                    colors: ['#4f46e5', '#7c3aed', '#ec4899']
                });
                confetti({
                    particleCount: 4,
                    angle: 120,
                    spread: 60,
                    origin: { x: 1 },
                    colors: ['#4f46e5', '#7c3aed', '#ec4899']
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        @endif
    };
</script>

@endsection