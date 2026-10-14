<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
    .content-footer {
        --footer-border: #e8ebf0;
        --footer-surface: #f8fafc;
        --footer-text-muted: #64748b;
        --footer-text-soft: #94a3b8;
        --footer-navy: #0a1a2f;
        --footer-red: #e31837;
        --footer-fb: #1877f2;
        --footer-tg: #229ed9;
        --footer-radius: 8px;
        --footer-transition: 0.2s ease;

        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #ffffff;
        border-top: 1px solid var(--footer-border);
        font-size: 13px;
        padding: 18px 0;
        position: relative;
        z-index: 10;
    }

    .content-footer .footer-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .content-footer .footer-copyright {
        color: var(--footer-text-muted);
        font-weight: 500;
    }

    .content-footer .footer-brand-text {
        color: var(--footer-red);
        font-weight: 700;
    }

    .content-footer .footer-divider {
        color: var(--footer-border);
        margin: 0 10px;
    }

    .content-footer .footer-links {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .content-footer .footer-link-item {
        color: var(--footer-text-muted);
        font-weight: 500;
        text-decoration: none;
        padding-bottom: 2px;
        border-bottom: 1px solid transparent;
        transition: color var(--footer-transition), border-color var(--footer-transition);
    }

    .content-footer .footer-link-item:hover {
        color: var(--footer-navy);
        border-bottom-color: var(--footer-navy);
    }

    .content-footer .footer-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .content-footer .footer-icon-group {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-right: 16px;
        border-right: 1px solid var(--footer-border);
    }

    .content-footer .icon-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--footer-radius);
        background: var(--footer-surface);
        border: 1px solid var(--footer-border);
        color: var(--footer-text-soft);
        font-size: 18px;
        text-decoration: none;
        transition: color var(--footer-transition), background var(--footer-transition),
            border-color var(--footer-transition), transform var(--footer-transition);
    }

    .content-footer .icon-btn:hover {
        transform: translateY(-1px);
    }

    .content-footer .icon-btn.fb:hover {
        color: #ffffff;
        background: var(--footer-fb);
        border-color: var(--footer-fb);
    }

    .content-footer .icon-btn.tg:hover {
        color: #ffffff;
        background: var(--footer-tg);
        border-color: var(--footer-tg);
    }

    .content-footer .icon-btn.top:hover {
        color: #ffffff;
        background: var(--footer-navy);
        border-color: var(--footer-navy);
    }

    .content-footer .icon-btn:focus-visible {
        outline: 2px solid var(--footer-navy);
        outline-offset: 2px;
    }

    @media (max-width: 768px) {
        .content-footer .footer-inner {
            flex-direction: column;
            text-align: center;
            gap: 14px;
        }

        .content-footer .footer-icon-group {
            border-right: none;
            padding-right: 0;
        }
    }
</style>

<footer class="content-footer footer mt-auto">
    <div class="container-xxl footer-inner">

        <div class="footer-copyright">
            Copyright © <script>document.write(new Date().getFullYear());</script>
            <span class="footer-divider">|</span>
            Powered by <span class="footer-brand-text">Pech</span>
        </div>

        <nav class="footer-links">
            <a href="#" class="footer-link-item">Dashboard</a>
            <a href="#" class="footer-link-item">Appointment</a>
            <a href="#" class="footer-link-item">Help</a>
        </nav>

        <div class="footer-actions">
            <div class="footer-icon-group">
                <a href="https://facebook.com/mgcambodia" target="_blank" rel="noopener" class="icon-btn fb" aria-label="Follow on Facebook">
                    <i class="bx bxl-facebook-circle"></i>
                </a>
                <a href="https://t.me/Pechlp" target="_blank" rel="noopener" class="icon-btn tg" aria-label="Message on Telegram">
                    <i class="bx bxl-telegram"></i>
                </a>
            </div>

            <button type="button" class="icon-btn top border-0" id="backToTop" aria-label="Back to top">
                <i class="bx bx-up-arrow-alt"></i>
            </button>
        </div>

    </div>
</footer>

<script>
    document.getElementById('backToTop').addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>