@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Battambang:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .product-container {
        --brand: #CE181E;
        --brand-dark: #a81116;
        --brand-soft: rgba(206, 24, 30, .08);
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e6e9ef;
        --bg-soft: #f8fafc;
        --success: #16a34a;
        --radius: 14px;
        font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important;
    }

    /* ---------- breadcrumb / header ---------- */
    .pc-eyebrow {
        font-size: .8rem;
        font-weight: 600;
        color: var(--muted);
        letter-spacing: .02em;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }
    .pc-title {
        color: var(--ink);
        font-weight: 800;
        letter-spacing: -.01em;
        margin: 0;
    }

    /* ---------- card ---------- */
    .pc-card {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        overflow: hidden;
        animation: pc-rise .35s ease both;
    }
    @keyframes pc-rise {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .pc-card-header {
        background: linear-gradient(180deg, #fff, var(--bg-soft));
    }
    .pc-icon-chip {
        width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px;
        background: var(--brand-soft);
        color: var(--brand);
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    /* ---------- section headers within the form ---------- */
    .pc-section {
        padding-top: 1.75rem;
        margin-top: 1.75rem;
        border-top: 1px dashed var(--line);
    }
    .pc-section:first-child { padding-top: 0; margin-top: 0; border-top: none; }
    .pc-section-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: var(--ink);
        font-size: .92rem;
        margin-bottom: 1rem;
    }
    .pc-section-label i { color: var(--brand); font-size: 1.05rem; }

    /* ---------- inputs ---------- */
    .product-container label.form-label {
        font-size: .82rem;
        font-weight: 600;
        color: var(--muted);
    }
    .product-container .input-group {
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .product-container .input-group:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 0.2rem var(--brand-soft);
    }
    .product-container .input-group-text {
        background: transparent;
        color: var(--muted);
        border: none;
    }
    .product-container .form-control,
    .product-container .form-select {
        border: none !important;
        box-shadow: none !important;
        padding-top: .6rem;
        padding-bottom: .6rem;
    }
    .product-container .form-control:disabled,
    .product-container .form-control[readonly] {
        background: var(--bg-soft);
        color: var(--ink);
    }
    .product-container .form-control::placeholder { color: #b7c0cc; }

    /* required-field validation feedback */
    .product-container .was-validated .input-group:has(:invalid) {
        border-color: #dc3545;
    }
    .pc-hint { font-size: .76rem; color: var(--muted); margin-top: .35rem; }

    /* ---------- item code controls ---------- */
    .pc-icon-btn {
        width: 42px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #fff;
        color: var(--muted);
        display: inline-flex; align-items: center; justify-content: center;
        transition: .15s;
        flex-shrink: 0;
    }
    .pc-icon-btn:hover { border-color: var(--brand); color: var(--brand); }
    .pc-icon-btn.pc-spin i { transition: transform .35s ease; }
    .pc-icon-btn.pc-spin.is-active i { transform: rotate(180deg); }
    .form-switch .form-check-input:checked {
        background-color: var(--brand);
        border-color: var(--brand);
    }

    /* ---------- stock stepper ---------- */
    .pc-stepper { display: flex; align-items: stretch; }
    .pc-stepper .form-control { text-align: center; }
    .pc-stepper button {
        border: 1px solid var(--line);
        background: var(--bg-soft);
        color: var(--ink);
        width: 42px;
        font-weight: 700;
    }
    .pc-stepper button:first-child { border-radius: 10px 0 0 10px; border-right: none; }
    .pc-stepper button:last-child { border-radius: 0 10px 10px 0; border-left: none; }
    .pc-stepper .input-group { border-radius: 0; border-left: none; border-right: none; }

    /* ---------- profit readout ---------- */
    .pc-profit {
        margin-top: .6rem;
        padding: .55rem .8rem;
        border-radius: 10px;
        background: var(--bg-soft);
        font-size: .82rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--muted);
    }
    .pc-profit strong { color: var(--ink); }
    .pc-profit.is-positive strong.pc-margin { color: var(--success); }
    .pc-profit.is-negative strong.pc-margin { color: var(--brand); }

    /* ---------- actions ---------- */
    .btn-brand {
        background: var(--brand);
        color: #fff;
        border: none;
        transition: .2s;
    }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; transform: translateY(-1px); }
    .btn-brand:disabled { opacity: .7; transform: none; }

    @media (max-width: 767.98px) {
        .pc-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            padding: .85rem 0;
            margin: 0 -1rem;
            padding-inline: 1rem;
            box-shadow: 0 -6px 16px rgba(15, 23, 42, .06);
        }
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <div class="pc-eyebrow">
                <i class="bx bx-cube"></i>
                <span>ស្តុកទំនិញ</span>
                <i class="bx bx-chevron-right"></i>
                <span>បន្ថែមទំនិញថ្មី</span>
            </div>
            <h4 class="pc-title">Add Product</h4>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary shadow-sm fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card pc-card shadow-sm">
                <div class="card-header pc-card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="pc-icon-chip me-3"><i class="bx bx-box"></i></div>
                        <div>
                            <h5 class="m-0 fw-bold" style="color: var(--ink);">ព័ត៌មានទំនិញ</h5>
                            <div class="text-muted" style="font-size:.82rem;">បំពេញព័ត៌មានខាងក្រោម ដើម្បីបន្ថែមទំនិញទៅក្នុងស្តុក</div>
                        </div>
                    </div>
                    <span class="d-none d-sm-inline text-muted" style="font-size:.78rem;">
                        <span class="text-danger">*</span> វាលដែលចាំបាច់ត្រូវបំពេញ
                    </span>
                </div>

                <div class="card-body pt-4">
                    <form action="{{ route('products.store') }}" method="POST" id="productForm" class="needs-validation" novalidate>
                        @csrf

                        <!-- ========== SECTION: General ========== -->
                        <div class="pc-section">
                            <div class="pc-section-label"><i class="bx bx-info-circle"></i> ព័ត៌មានទូទៅ (General)</div>
                            <div class="row g-4">

                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label mb-0">Item Code (កូដទំនិញ) <span class="text-danger">*</span></label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="autoGenerateCheck" checked>
                                            <label class="form-check-label text-muted" style="font-size:.8rem;" for="autoGenerateCheck">Auto Generate</label>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <div class="input-group flex-grow-1">
                                            <span class="input-group-text"><i class="bx bx-barcode-reader"></i></span>
                                            <input type="text" name="item_code" id="item_code" class="form-control ps-0" required placeholder="Ex: MG-284915" readonly>
                                        </div>
                                        <button type="button" id="regenerateBtn" class="pc-icon-btn pc-spin" title="បង្កើតកូដថ្មី">
                                            <i class="bx bx-refresh"></i>
                                        </button>
                                        <button type="button" id="copyBtn" class="pc-icon-btn" title="ចម្លងកូដ">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                    <div class="pc-hint" id="codeHint">កូដនឹងផ្លាស់ប្តូរស្វ័យប្រវត្តិទៅតាមប្រភេទដែលអ្នកជ្រើសរើស</div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                    <label class="form-label mb-1">Product Name (ឈ្មោះទំនិញ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-package"></i></span>
                                        <input type="text" name="item_name" class="form-control ps-0" required placeholder="Ex: MG Oil Filter">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION: Classification ========== -->
                        <div class="pc-section">
                            <div class="pc-section-label"><i class="bx bx-category-alt"></i> ចំណាត់ថ្នាក់ & ទីតាំង (Classification & Location)</div>
                            <div class="row g-4">

                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                    <label class="form-label mb-1">Category (ប្រភេទទំនិញ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-category"></i></span>
                                        <select name="category" id="category" class="form-select ps-0" required>
                                            <option value="">-- ជ្រើសរើសប្រភេទ --</option>
                                            <optgroup label="4 WHEEL (រថយន្ត)">
                                                <option value="MG 4WHEEL" data-prefix="MG">MG 4WHEEL</option>
                                                <option value="LP 4WHEEL" data-prefix="LP">LP 4WHEEL (LEAPMOTOR)</option>
                                                <option value="IM 4WHEEL" data-prefix="IM">IM 4WHEEL</option>
                                            </optgroup>
                                            <optgroup label="2 WHEEL (ម៉ូតូ)">
                                                <option value="RE 2WHEEL" data-prefix="RE">RE 2WHEEL (ROYAL ENFIELD)</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                    <label class="form-label mb-1">Location / Branch (ឃ្លាំងទទួលទំនិញ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-store-alt"></i></span>
                                        <select name="location_id" class="form-select ps-0" required>
                                            <option value="">-- ជ្រើសរើសឃ្លាំង/សាខា --</option>
                                            <option value="25">Mg Cambodia Warehouse (MG)</option>
                                            <option value="23">VIVe Warehouse (Leapmotor)</option>
                                            <option value="26">IM Warehouse (IM)</option>
                                            <option value="27">Royal Warehouse (Royal Enfield)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION: Pricing & Stock ========== -->
                        <div class="pc-section">
                            <div class="pc-section-label"><i class="bx bx-dollar-circle"></i> តម្លៃ & ស្តុក (Pricing & Stock)</div>
                            <div class="row g-4">

                                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                    <label class="form-label mb-1">Initial Stock (ស្តុកដើម)</label>
                                    <div class="pc-stepper">
                                        <button type="button" id="stockMinus" tabindex="-1">−</button>
                                        <div class="input-group">
                                            <input type="number" name="stock_qty" id="stock_qty" class="form-control ps-0" value="0" min="0">
                                        </div>
                                        <button type="button" id="stockPlus" tabindex="-1">+</button>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                    <label class="form-label mb-1">Cost (តម្លៃទិញចូល) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold" style="color: var(--muted);">$</span>
                                        <input type="number" step="0.01" min="0" name="cost" id="cost" class="form-control ps-0" required placeholder="0.00">
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                    <label class="form-label mb-1">Price (តម្លៃលក់ចេញ) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold" style="color: var(--brand);">$</span>
                                        <input type="number" step="0.01" min="0" name="price" id="price" class="form-control ps-0" required placeholder="0.00">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="pc-profit" id="profitBox">
                                        <span>ចំណេញប៉ាន់ស្មាន (Estimated Profit)</span>
                                        <span><strong id="profitAmount">$0.00</strong> &nbsp;•&nbsp; <strong class="pc-margin" id="profitMargin">0%</strong> margin</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========== SECTION: Description ========== -->
                        <div class="pc-section">
                            <div class="pc-section-label"><i class="bx bx-comment-detail"></i> ការពិពណ៌នា (Description)</div>
                            <div class="input-group">
                                <span class="input-group-text align-items-start pt-2"><i class="bx bx-detail"></i></span>
                                <textarea name="description" class="form-control ps-0" rows="3" placeholder="ព័ត៌មានបន្ថែមពីទំនិញ... (ស្រេចចិត្ត)"></textarea>
                            </div>
                        </div>

                        <div class="pc-actions d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-light fw-semibold px-4 shadow-sm border">
                                <i class="bx bx-x me-1"></i> បោះបង់
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-brand fw-bold px-5 shadow-sm">
                                <i class="bx bx-save me-1"></i> រក្សាទុកទំនិញ
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const autoGenerateCheck = document.getElementById('autoGenerateCheck');
    const itemCodeInput     = document.getElementById('item_code');
    const categorySelect    = document.getElementById('category');
    const regenerateBtn     = document.getElementById('regenerateBtn');
    const copyBtn           = document.getElementById('copyBtn');
    const codeHint          = document.getElementById('codeHint');

    function currentPrefix() {
        const opt = categorySelect.options[categorySelect.selectedIndex];
        return (opt && opt.dataset.prefix) ? opt.dataset.prefix : 'PRD';
    }

    function generateCode() {
        const prefix = currentPrefix();
        const digits = Math.floor(100000 + Math.random() * 900000);
        return `${prefix}-${digits}`;
    }

    function refreshCode() {
        itemCodeInput.value = generateCode();
        regenerateBtn.classList.add('is-active');
        setTimeout(() => regenerateBtn.classList.remove('is-active'), 350);
    }

    function setAutoMode(isAuto) {
        if (isAuto) {
            itemCodeInput.readOnly = true;
            refreshCode();
            codeHint.textContent = 'កូដនឹងផ្លាស់ប្តូរស្វ័យប្រវត្តិទៅតាមប្រភេទដែលអ្នកជ្រើសរើស';
        } else {
            itemCodeInput.readOnly = false;
            itemCodeInput.value = '';
            itemCodeInput.focus();
            codeHint.textContent = 'សូមវាយកូដទំនិញដោយខ្លួនឯង';
        }
    }

    // init
    setAutoMode(autoGenerateCheck.checked);

    autoGenerateCheck.addEventListener('change', () => setAutoMode(autoGenerateCheck.checked));
    regenerateBtn.addEventListener('click', () => { if (autoGenerateCheck.checked) refreshCode(); });
    categorySelect.addEventListener('change', () => { if (autoGenerateCheck.checked) refreshCode(); });

    copyBtn.addEventListener('click', function () {
        if (!itemCodeInput.value) return;
        navigator.clipboard.writeText(itemCodeInput.value).then(() => {
            const icon = copyBtn.querySelector('i');
            icon.classList.remove('bx-copy');
            icon.classList.add('bx-check', 'text-success');
            setTimeout(() => {
                icon.classList.remove('bx-check', 'text-success');
                icon.classList.add('bx-copy');
            }, 1200);
        });
    });

    // ---- stock stepper ----
    const stockInput = document.getElementById('stock_qty');
    document.getElementById('stockMinus').addEventListener('click', () => {
        stockInput.value = Math.max(0, (parseInt(stockInput.value) || 0) - 1);
    });
    document.getElementById('stockPlus').addEventListener('click', () => {
        stockInput.value = (parseInt(stockInput.value) || 0) + 1;
    });

    // ---- live profit readout ----
    const costInput   = document.getElementById('cost');
    const priceInput  = document.getElementById('price');
    const profitBox   = document.getElementById('profitBox');
    const profitAmt   = document.getElementById('profitAmount');
    const profitMgn   = document.getElementById('profitMargin');

    function updateProfit() {
        const cost  = parseFloat(costInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const profit = price - cost;
        const margin = price > 0 ? (profit / price) * 100 : 0;

        profitAmt.textContent = (profit >= 0 ? '$' : '-$') + Math.abs(profit).toFixed(2);
        profitMgn.textContent = margin.toFixed(1) + '%';

        profitBox.classList.toggle('is-positive', profit > 0);
        profitBox.classList.toggle('is-negative', profit < 0);
    }
    costInput.addEventListener('input', updateProfit);
    priceInput.addEventListener('input', updateProfit);

    // ---- Bootstrap-style validation + submit lock ----
    const form = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> កំពុងរក្សាទុក...';
    });
});
</script>

@endsection