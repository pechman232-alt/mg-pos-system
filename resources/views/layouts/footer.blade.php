<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    /* FOOTER BASE */
    .content-footer {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        font-size: 13px;
        padding: 20px 0;
        
        /* 🌟 កូដ ៣ ជួរនេះជាថ្នាំស័ក្តិសិទ្ធិ */
        position: relative !important; 
        z-index: 99999 !important; /* បង្ខំឱ្យវាផុសមកលើគេបង្អស់ ផុតពីអាគម្របដែលបាំងវា */
        pointer-events: auto !important; /* បង្ខំឱ្យអាចចុចបាន ១០០% */
    }

    .footer-brand-text {
        color: #e31837; /* MG Red */
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* 🌟 បន្ថែម Footer Links Menu */
    .footer-links {
        display: flex;
        gap: 15px;
        margin-right: 15px;
    }
    
    .footer-link-item {
        color: #64748b;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .footer-link-item:hover {
        color: #e31837;
    }

    /* PROFESSIONAL SOCIAL ICONS */
    .social-link {
        font-size: 22px;
        color: #94a3b8; /* Slate gray default */
        margin-right: 8px;
        width: 40px; /* 🌟 បង្រួមតូចបន្តិចឱ្យសមរម្យ */
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px; /* Modern Squircle Shape */
        background: #f8fafc;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
        border: 1px solid #edf2f7;
    }

    /* Facebook Hover State */
    .social-link.fb:hover {
        color: #ffffff !important;
        background: #1877F2; /* Official FB Blue */
        border-color: #1877F2;
        box-shadow: 0 8px 15px rgba(24, 119, 242, 0.25);
        transform: translateY(-3px);
    }

    /* Telegram Hover State */
    .social-link.tg:hover {
        color: #ffffff !important;
        background: #229ED9; /* Official Telegram Blue */
        border-color: #229ED9;
        box-shadow: 0 8px 15px rgba(34, 158, 217, 0.25);
        transform: translateY(-3px);
    }

    /* BACK TO TOP BUTTON */
    .back-to-top {
        background: #0a1a2f; /* MG Navy */
        color: white !important;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(10, 26, 47, 0.15);
        cursor: pointer;
    }
    
    .back-to-top:hover {
        background: #e31837; /* MG Red on Hover */
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(227, 24, 55, 0.3);
    }

    /* RESPONSIVE TWEAK */
    @media (max-width: 768px) {
        .content-footer .container-xxl {
            flex-direction: column !important;
            gap: 15px;
            text-align: center;
        }
        .border-end { border: none !important; padding: 0 !important; margin: 0 !important; }
        .footer-links { justify-content: center; margin-right: 0; margin-bottom: 10px; }
    }
</style>

<footer class="content-footer footer mt-auto">
    <div class="container-xxl d-flex flex-wrap justify-content-between align-items-center">
        
        <div class="mb-2 mb-md-0 fw-semibold text-muted">
            Copyright © <script>document.write(new Date().getFullYear());</script>
            <span class="mx-2" style="color: #cbd5e1;">|</span> 
            Powered By <span class="footer-brand-text ms-1">MG Cambodia</span>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-center">
            
            {{-- 🌟 បន្ថែម Menu Links សម្រាប់ចុចទៅទំព័រផ្សេងៗ (ប្តូរ route ទៅតាមតម្រូវការ) --}}
            <div class="footer-links">
                <a href="{{ route('dashboard') }}" class="footer-link-item">Dashboard</a>
                <a href="{{ route('leads.index') }}" class="footer-link-item">Leads</a>
                <a href="#" class="footer-link-item">Help</a>
            </div>

            <div class="me-3 border-end pe-3 d-flex border-start ps-3">
                <a href="https://facebook.com/mgcambodia" target="_blank" class="social-link fb" title="Follow on Facebook">
                    <i class="bx bxl-facebook-circle"></i>
                </a>
                <a href="https://t.me/Pechlp" target="_blank" class="social-link tg" title="Message on Telegram">
                    <i class="bx bxl-telegram"></i>
                </a>
            </div>
            
            <button type="button" class="back-to-top border-0" id="backToTop">
                <i class="bx bx-up-arrow-alt fs-4"></i>
            </button>
        </div>
    </div>
</footer>

<script>
    // Smooth Scroll to Top functionality
    document.getElementById('backToTop').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>