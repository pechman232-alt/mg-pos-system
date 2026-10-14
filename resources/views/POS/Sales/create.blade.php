@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* ============================================================
   TOKENS & GLOBAL
   ============================================================ */
.checkout-container{
  --brand:        #CE181E;
  --brand-dark:   #A1121A;
  --brand-tint:   #FCEBEC;
  --brand-tint-2: #FFF6F6;

  --ink:          #12141A;
  --body-c:       #4B5565;
  --muted:        #8A93A3;

  --canvas:       #F3F4F7;
  --surface:      #FFFFFF;
  --border:       #E4E7EC;
  --border-soft:  #EDEFF3;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;

  --shadow-xs: 0 1px 2px rgba(18,20,26,0.04);
  --shadow-sm: 0 2px 8px rgba(18,20,26,0.06);
  --shadow-md: 0 8px 24px rgba(18,20,26,0.08);
  --shadow-brand: 0 8px 20px rgba(206,24,30,0.25);

  font-family: 'Plus Jakarta Sans', sans-serif !important;
  background: var(--canvas);
}
.checkout-container *{ box-sizing: border-box; }

/* ============================================================
   TOP BAR
   ============================================================ */
.pos-topbar{
  position: sticky;
  top: 0;
  z-index: 40;
  background: rgba(243,244,247,0.9);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  margin: -1.5rem -1rem 24px;
  padding: 16px 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  border-bottom: 1px solid var(--border-soft);
}
.pos-topbar .eyebrow{
  font-size: 12px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
  color: var(--muted); display: block; margin-bottom: 2px;
}
.pos-topbar h1{
  font-size: 21px; font-weight: 800; color: var(--ink); margin: 0; letter-spacing: -0.01em;
}
.pos-actions{ display:flex; gap:10px; }

.btn-pos{
  font-family: inherit; font-weight: 600; font-size: 14px;
  border-radius: var(--radius-sm); padding: 10px 18px; border: 1px solid transparent;
  display: inline-flex; align-items: center; gap: 6px;
  transition: transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;
  cursor: pointer;
}
.btn-pos:active{ transform: translateY(1px); }
.btn-pos-ghost{ background: var(--surface); border-color: var(--border); color: var(--body-c); }
.btn-pos-ghost:hover{ border-color: #CBD1DB; color: var(--ink); box-shadow: var(--shadow-xs); }
.btn-pos-brand{ background: var(--brand); color: #fff; box-shadow: var(--shadow-brand); }
.btn-pos-brand:hover{ background: var(--brand-dark); color:#fff; }
.btn-pos-brand:disabled{ opacity:.6; cursor:not-allowed; }
.btn-pos-outline{ background: var(--brand-tint-2); border-color: var(--brand); color: var(--brand-dark); }
.btn-pos-outline:hover{ background: var(--brand-tint); color: var(--brand-dark); }
.btn-pos-outline:disabled{ opacity:.6; cursor:not-allowed; border-color: var(--border); color: var(--muted); background: var(--surface); }

/* ============================================================
   CARDS
   ============================================================ */
.pos-card{
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
  box-shadow: var(--shadow-xs); margin-bottom: 20px; overflow: hidden;
}
.pos-card-head{
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 24px; border-bottom: 1px solid var(--border-soft);
}
.pos-card-head .title{ display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 700; color: var(--ink); }
.pos-card-head .title .icon-badge{
  width: 30px; height: 30px; border-radius: 9px; background: var(--brand-tint); color: var(--brand);
  display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
}
.pos-card-body{ padding: 22px 24px 26px; }
.pos-subsection-label{
  font-size: 12px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: var(--muted);
  margin: 22px 0 14px; display: flex; align-items: center; gap: 8px;
}
.pos-subsection-label::after{ content:""; flex: 1; height: 1px; background: var(--border-soft); }

/* ============================================================
   FIELDS & SELECT2
   ============================================================ */
.pos-field label{ font-size: 12.5px; font-weight: 600; color: var(--body-c); margin-bottom: 6px; display: block; }
.pos-field label .req{ color: var(--brand); margin-left: 2px; }

.pos-input-wrap{
  position: relative; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
  background: var(--surface); display: flex; align-items: center;
  transition: border-color .14s ease, box-shadow .14s ease;
}
.pos-input-wrap:focus-within{ border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-tint); }
.pos-input-wrap.border-danger{ border-color: var(--brand) !important; background: var(--brand-tint-2); }
.pos-input-wrap.is-readonly{ background: #FAFBFC; }
.pos-input-wrap .ic{
  width: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
  color: var(--muted); font-size: 16px;
}
.checkout-container .pos-input-wrap .ic,
.checkout-container .pos-input-wrap input,
.checkout-container .pos-input-wrap select,
.checkout-container .pos-input-wrap textarea{
  border: 0 !important;
  outline: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
  border-radius: 0 !important;
}
.checkout-container .pos-input-wrap input,
.checkout-container .pos-input-wrap select{
  width: 100%;
  padding: 10px 12px 10px 0;
  font-family: inherit;
  font-size: 14px;
  font-weight: 500;
  color: var(--ink);
}

.checkout-container .pos-input-wrap .select2-container--default .select2-selection--single{
  border: 0 !important; box-shadow: none !important; outline: 0 !important; background: transparent !important;
}
.checkout-container .pos-input-wrap:has(.select2-container--open){
  border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-tint);
}
.pos-input-wrap input::placeholder{ color: #B0B7C3; font-weight: 400; }
.pos-input-wrap input[readonly]{ color: var(--body-c); }
.pos-feedback{ font-size: 12px; font-weight: 700; color: var(--brand); margin-top: 7px; display: flex; align-items: center; gap: 4px; }

.select2-container{ width: 100% !important; }
.select2-container .select2-selection--single{
  height: 42px !important; border: none !important; background: transparent !important;
  display: flex; align-items: center; padding-left: 0;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
  padding-left: 0; font-size: 14px; font-weight: 500; color: var(--ink);
}
.select2-container--default .select2-selection--single .select2-selection__arrow{ height: 40px !important; }
.select2-dropdown{ border-color: var(--border) !important; border-radius: var(--radius-sm) !important; overflow:hidden; }
.select2-container--default .select2-results__option--highlighted[aria-selected]{ background: var(--brand) !important; }

/* ============================================================
   ITEMS TABLE
   ============================================================ */
.pos-table-wrap{ overflow-x: auto; }
table.pos-table{ width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 0; }
table.pos-table thead th{
  background: #FAFBFC; color: var(--muted); font-size: 11.5px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.04em; padding: 12px 14px; border-bottom: 1px solid var(--border-soft); white-space: nowrap;
}
table.pos-table tbody td{ padding: 10px 14px; border-bottom: 1px solid var(--border-soft); vertical-align: middle; }
table.pos-table tbody tr:hover{ background: #FBFBFD; }
table.pos-table tbody tr:last-child td{ border-bottom: none; }
.row-index{ font-weight: 700; color: var(--muted); font-size: 13px; width: 28px; text-align: center; }

.pos-table select.form-select, .pos-table input.form-control{
  border: 1.5px solid transparent !important; background: #F6F7F9 !important; border-radius: 8px !important;
  padding: 9px 10px; font-size: 13.5px; font-weight: 500; color: var(--ink);
  transition: border-color .12s ease, background .12s ease; box-shadow: none !important;
}
.pos-table select.form-select:focus, .pos-table input.form-control:focus{
  outline: none; background: #fff !important; border-color: var(--brand) !important; box-shadow: 0 0 0 3px var(--brand-tint) !important;
}
.pos-table input.qty-input{ text-align:center; font-variant-numeric: tabular-nums; }
.pos-table input.price-input{ text-align:right; font-variant-numeric: tabular-nums; }
.pos-table input.is-invalid, .pos-table select.is-invalid{ border-color: var(--brand) !important; background: var(--brand-tint-2) !important; }

.row-total{
  font-weight: 700; color: var(--brand-dark); text-align: right; font-variant-numeric: tabular-nums;
  font-size: 14px; background: transparent !important; border: none !important; padding-right: 4px !important;
}

.btn-remove-row{
  width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-soft); background: #fff;
  color: var(--muted); display: inline-flex; align-items: center; justify-content: center;
  transition: all .12s ease; cursor: pointer;
}
.btn-remove-row:hover{ background: var(--brand-tint); color: var(--brand); border-color: var(--brand-tint); }

/* ============================================================
   RECEIPT-STYLE SUMMARY
   ============================================================ */
.receipt-card{
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md); position: sticky; top: 92px; overflow: hidden;
}
.receipt-perf{
  height: 14px; width: 100%;
  background-image: radial-gradient(circle at 10px 0, var(--canvas) 6px, transparent 6.5px);
  background-size: 20px 14px; background-repeat: repeat-x; background-position: top;
}
.receipt-head{ padding: 20px 24px 4px; text-align: center; }
.receipt-head .icon-badge{
  width: 34px; height:34px; margin: 0 auto 8px; border-radius: 10px; background: var(--brand-tint); color: var(--brand);
  display:flex; align-items:center; justify-content:center; font-size: 18px;
}
.receipt-head h6{ font-size: 13px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--muted); margin: 0; }
.receipt-body{ padding: 8px 24px 24px; }

.receipt-line{ display: flex; justify-content: space-between; align-items: center; padding: 10px 0; font-size: 14px; }
.receipt-line .label{ color: var(--body-c); font-weight: 500; }
.receipt-line .value{ font-weight: 700; color: var(--ink); font-variant-numeric: tabular-nums; }

.receipt-input-group{
  display: flex; align-items: center; border: 1.5px solid var(--border); border-radius: 8px;
  overflow: hidden; width: 128px; transition: border-color .12s ease, box-shadow .12s ease;
}
.receipt-input-group:focus-within{ border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-tint); }
.receipt-input-group span{
  background: #FAFBFC; color: var(--muted); font-size: 12.5px; font-weight: 700; padding: 8px 9px; border-right: 1.5px solid var(--border);
}
.receipt-input-group.suffix span{ border-right:none; border-left: 1.5px solid var(--border); order:2; }
.receipt-input-group input{
  border: none !important; outline: none; width: 100%; text-align: right; padding: 8px 10px;
  font-family: inherit; font-weight: 700; font-size: 13.5px; color: var(--ink); font-variant-numeric: tabular-nums;
  background: transparent !important; box-shadow:none !important;
}

.receipt-divider{ border: none; border-top: 1.5px dashed var(--border); margin: 14px 0; }

.receipt-total{
  background: linear-gradient(135deg, var(--brand-tint) 0%, var(--brand-tint-2) 100%);
  border: 1px dashed rgba(206,24,30,0.35); border-radius: var(--radius-md); padding: 16px 18px;
  display: flex; justify-content: space-between; align-items: center; margin: 6px 0 20px;
}
.receipt-total .label{ font-size: 13.5px; font-weight: 700; color: var(--ink); }
.receipt-total .amount{ font-size: 28px; font-weight: 800; color: var(--brand); letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }

.btn-checkout{
  width: 100%; background: var(--brand); color: #fff; border: none; border-radius: var(--radius-sm);
  padding: 15px 0; font-size: 15.5px; font-weight: 700; display: flex; align-items:center; justify-content:center; gap:8px;
  box-shadow: var(--shadow-brand); transition: transform .12s ease, background .12s ease; cursor: pointer;
}
.btn-checkout:hover{ background: var(--brand-dark); color:#fff; transform: translateY(-1px); }
.btn-checkout:disabled{ opacity:.65; cursor:not-allowed; transform:none; }

.receipt-hint{ text-align:center; font-size: 11.5px; color: var(--muted); margin-top: 12px; display:flex; align-items:center; justify-content:center; gap:5px; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 991px){
  .pos-topbar{ margin: -1rem -0.75rem 20px; padding: 14px 0.75rem; }
  .receipt-card{ position: static; margin-top: 4px; }
}
@media (max-width: 575px){
  .pos-actions{ width: 100%; }
  .pos-actions .btn-pos{ flex:1; justify-content:center; }
  .receipt-total .amount{ font-size: 24px; }
}
.checkout-container a:focus-visible,
.checkout-container button:focus-visible,
.checkout-container input:focus-visible,
.checkout-container select:focus-visible{ outline: 2px solid var(--brand); outline-offset: 2px; }

/* SweetAlert Custom UI */
.swal2-styled.swal2-confirm.btn-pos { margin-right: 10px; }
</style>

<div class="container-fluid flex-grow-1 container-p-y checkout-container">

    <div class="pos-topbar">
        <div>
            <span class="eyebrow">Sales Department</span>
            <h1>Create Invoice <span style="color:var(--muted); font-weight:600;">— POS Checkout</span></h1>
        </div>
        <div class="pos-actions">
            <button type="button" class="btn-pos btn-pos-ghost" onclick="window.history.back()">
                <i class="bx bx-arrow-back"></i> Back
            </button>
            <button type="button" id="btn-save-top" class="btn-pos btn-pos-brand">
                <i class="bx bx-save"></i> Save & Print
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8 col-lg-7 col-12">

            <!-- Sales & Invoice Information -->
            <div class="pos-card">
                <div class="pos-card-head">
                    <div class="title"><span class="icon-badge"><i class="bx bx-user-circle"></i></span> Sales & Invoice Information</div>
                </div>
                <div class="pos-card-body">
                    <div class="row g-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Sales Person <span class="req">*</span></label>
                                <div class="pos-input-wrap">
                                    <span class="ic"><i class="bx bx-user-pin"></i></span>
                                    <select id="employee_id" name="employee_id" class="select2" required style="width:100%;">
                                        <option value="">-- Select Employee --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Location / Branch <span class="req">*</span></label>
                                <div class="pos-input-wrap">
                                    <span class="ic"><i class="bx bx-store"></i></span>
                                    <select id="location_id" name="location_id" required>
                                        <option value="">-- Select Location --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('location_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Invoice No</label>
                                <div class="pos-input-wrap is-readonly">
                                    <span class="ic"><i class="bx bx-receipt"></i></span>
                                    <input type="text" value="{{ $invoiceNo }}" readonly style="color:var(--brand-dark); font-weight:700;">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Date</label>
                                <div class="pos-input-wrap is-readonly">
                                    <span class="ic"><i class="bx bx-calendar"></i></span>
                                    <input type="date" value="{{ date('Y-m-d') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="pos-subsection-label"><i class="bx bx-car" style="color:var(--brand); font-size:15px;"></i> Vehicle Information & Notes</div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Car Buyer Name</label>
                                <div class="pos-input-wrap">
                                    <span class="ic"><i class="bx bx-user-pin"></i></span>
                                    <input type="text" id="car_buyer_name" placeholder="Enter buyer name...">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Car Invoice <span class="req">*</span></label>
                                <div class="pos-input-wrap" id="wrapper-car-invoice">
                                    <span class="ic"><i class="bx bx-hash"></i></span>
                                    <input type="text" id="car_invoice_no" placeholder="Enter car invoice..." required>
                                </div>
                                <small id="car-invoice-feedback" class="pos-feedback d-none">
                                    <i class="bx bx-error-circle"></i> Invoice already exists!
                                </small>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Reason <span id="req-reason" class="req" style="display:none;">*</span></label>
                                <div class="pos-input-wrap" id="wrapper-reason">
                                    <span class="ic"><i class="bx bx-question-mark"></i></span>
                                    <input type="text" id="reason" placeholder="Reason for duplicate...">
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="pos-field">
                                <label>Remark</label>
                                <div class="pos-input-wrap">
                                    <span class="ic"><i class="bx bx-comment-detail"></i></span>
                                    <input type="text" id="remark" placeholder="Additional remarks...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item List -->
            <div class="pos-card">
                <div class="pos-card-head">
                    <div class="title"><span class="icon-badge"><i class="bx bx-cart-alt"></i></span> Item List</div>
                    <button type="button" class="btn-pos btn-pos-outline" id="add-row" disabled>
                        <i class="bx bx-plus"></i> Add Row
                    </button>
                </div>
                <div class="pos-table-wrap">
                    <table class="pos-table" id="itemTable">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Product</th>
                                <th style="width:140px;">PI No</th>
                                <th style="width:90px;" class="text-center">Qty</th>
                                <th style="width:110px;" class="text-end">Price ($)</th>
                                <th style="width:140px;">Remark</th> <!-- Added Remark Column Here -->
                                <th style="width:110px;" class="text-end">Total ($)</th>
                                <th style="width:48px;" class="text-center"><i class="bx bx-trash text-danger"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="row-index">1</td>
                                <td>
                                    <select class="form-select select-product">
                                        <option value="">-- Select Location First --</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select pi-input">
                                        <option value="">-- Select PI --</option>
                                    </select>
                                </td>
                                <td><input type="number" class="form-control qty-input" value="1" min="1"></td>
                                <td><input type="number" class="form-control price-input" value="0.00" step="0.01"></td>
                                <td><input type="text" class="form-control remark-input" placeholder="Item remark..."></td>
                                <td><input type="text" class="row-total" value="0.00" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-row remove-row"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 col-12">
            <div class="receipt-card">
                <div class="receipt-head">
                    <div class="icon-badge"><i class="bx bx-calculator"></i></div>
                    <h6>Payment Summary</h6>
                </div>
                <div class="receipt-body">

                    <div class="receipt-line">
                        <span class="label">Subtotal</span>
                        <span id="txt-subtotal" class="value">$0.00</span>
                    </div>

                    <div class="receipt-line">
                        <span class="label">Discount</span>
                        <div class="receipt-input-group">
                            <span>$</span>
                            <input type="number" id="discount_amount" value="0" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="receipt-line">
                        <span class="label">Tax</span>
                        <div class="receipt-input-group suffix">
                            <input type="number" id="tax_percent" value="0" min="0">
                            <span>%</span>
                        </div>
                    </div>

                    <hr class="receipt-divider">

                    <div class="receipt-total">
                        <span class="label">Grand Total:</span>
                        <span id="txt-grandtotal" class="amount">$0.00</span>
                    </div>

                    <button type="button" id="btn-save" class="btn-checkout">
                        <i class="bx bx-check-circle"></i> Checkout
                    </button>
                    <div class="receipt-hint"><i class="bx bx-lock-alt"></i> Information will be saved immediately.</div>
                </div>
                <div class="receipt-perf" style="transform:rotate(180deg);"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const piOptionsStatic = `<option value="">-- Select PI --</option>
        @if(isset($purchaseInvoices))
            @foreach($purchaseInvoices as $pi)
                <option value="{{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}">
                    {{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}
                </option>
            @endforeach
        @endif`;
    
    $(document).ready(function() {
    
        // 1. Initialize Select2 for Sale Person
        $('.select2').select2({
            placeholder: "-- Select Employee --",
            allowClear: true,
            width: 'resolve'
        });
    
        let rowCount = 1;
        let isCarInvoiceDuplicate = false;
        let currentBranchItems = []; 
    
        // 2. Alert Function Helper 
        function showAlert(icon, title, text) {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn-pos btn-pos-brand' },
                buttonsStyling: false
            });
        }
    
        // ==========================================
        // 3. Dependent Dropdown: Location -> Items
        // ==========================================
        $('#location_id').on('change', function() {
            let loc_id = $(this).val();
            let prev_loc = $(this).data('prev');
    
            let hasSelectedItems = false;
            $('.select-product').each(function() {
                if($(this).val() !== '') hasSelectedItems = true;
            });
    
            if (hasSelectedItems && prev_loc) {
                Swal.fire({
                    title: 'Change Location?',
                    text: 'Selected items will be cleared. Do you want to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it',
                    cancelButtonText: 'Cancel',
                    customClass: { confirmButton: 'btn-pos btn-pos-brand', cancelButton: 'btn-pos btn-pos-ghost' },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetchItemsForBranch(loc_id);
                    } else {
                        $(this).val(prev_loc);
                    }
                });
            } else {
                fetchItemsForBranch(loc_id);
            }
        });

        function fetchItemsForBranch(loc_id) {
            let productSelects = $('.select-product');
    
            if (loc_id) {
                productSelects.html('<option value="">Loading data...</option>');
                $('#add-row').prop('disabled', true);
                
                let fetchUrl = "{{ url('/pos/checkout/get-items-by-branch') }}/" + loc_id;
                
                $.ajax({
                    url: fetchUrl,
                    type: "GET",
                    success: function(res) {
                        currentBranchItems = res;
                        
                        let optionsHtml = '<option value="">-- Select Product --</option>';
                        
                        if (res.length > 0) {
                            res.forEach(item => {
                                let price = item.selling_price || item.unit_price || item.price || 0;
                                let itemId = item.id || item.item_code;
                                optionsHtml += `<option value="${itemId}" data-price="${price}">
                                    ${item.item_code} - ${item.item_name} (Stock: ${item.qty})
                                </option>`;
                            });
                            
                            productSelects.html(optionsHtml);
                            $('#add-row').prop('disabled', false); 
                            $('#location_id').data('prev', loc_id); 
                        } else {
                            productSelects.html('<option value="">No items in stock for this location</option>');
                            $('#add-row').prop('disabled', true);
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'Out of Stock!',
                                text: 'The selected location has no items in stock. Please select another location.',
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn-pos btn-pos-brand' },
                                buttonsStyling: false
                            });
                            
                            $('#location_id').val(''); 
                            $('#location_id').data('prev', '');
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr.responseText); 
                        
                        let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Unable to load product data. Please check your connection.';
                        showAlert('error', 'System Error (' + xhr.status + ')', errorMsg);
                        
                        currentBranchItems = [];
                        productSelects.html('<option value="">-- Error --</option>');
                    }
                });
            } else {
                currentBranchItems = [];
                productSelects.html('<option value="">-- Select Location First --</option>');
                $('#add-row').prop('disabled', true);
                $('#location_id').data('prev', '');
            }
    
            $('.price-input').val('0.00');
            $('.row-total').val('0.00');
            $('.remark-input').val('');
            $('.pi-input').html('<option value="">-- Select PI --</option>');
            calculateAll();
        }

        // ==========================================
        // 5. Dynamic Table Add/Remove Rows
        // ==========================================
        $('#add-row').click(function() {
            if (!$('#location_id').val()) {
                showAlert('warning', 'Location Required', 'Please select a location first before adding items.');
                return;
            }
    
            rowCount++;
            
            let optionsHtml = '<option value="">-- Select Product --</option>';
            currentBranchItems.forEach(item => {
                let price = item.selling_price || item.unit_price || item.price || 0;
                let itemId = item.id || item.item_code;
                optionsHtml += `<option value="${itemId}" data-price="${price}">
                    ${item.item_code} - ${item.item_name} (Stock: ${item.qty})
                </option>`;
            });
    
            let newRow = `<tr>
                <td class="row-index">${rowCount}</td>
                <td>
                    <select class="form-select select-product">
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <select class="form-select pi-input">
                        ${piOptionsStatic}
                    </select>
                </td>
                <td><input type="number" class="form-control qty-input" value="1" min="1"></td>
                <td><input type="number" class="form-control price-input" value="0.00" step="0.01"></td>
                <td><input type="text" class="form-control remark-input" placeholder="Item remark..."></td>
                <td><input type="text" class="row-total" value="0.00" readonly></td>
                <td class="text-center">
                    <button type="button" class="btn-remove-row remove-row"><i class="bx bx-trash"></i></button>
                </td>
            </tr>`;
            $('#itemTable tbody').append(newRow);
        });
    
        $(document).on('click', '.remove-row', function() {
            if ($('#itemTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                reorderRows();
                calculateAll();
            } else {
                showAlert('warning', 'Cannot delete!', 'Invoice must have at least 1 item.');
            }
        });
    
        function reorderRows() {
            $('#itemTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
            rowCount = $('#itemTable tbody tr').length;
        }
    
        // ==========================================
        // 6. Handle Item Selection & Prices
        // ==========================================
        $(document).on('change', '.select-product', function() {
            let selectedOption = $(this).find('option:selected');
            let rawPrice = selectedOption.attr('data-price');
            let price = parseFloat(rawPrice) || 0;
    
            let product_id = $(this).val();
            let row = $(this).closest('tr');
            let piSelect = row.find('.pi-input');
    
            row.find('.price-input').val(price.toFixed(2));
    
            if (product_id) {
                let originalOptions = piSelect.html();
                piSelect.html('<option value="">Searching PI...</option>');
    
                let ajaxUrl = "{{ url('/pos/checkout/get-latest-pi') }}/" + product_id;
    
                $.get(ajaxUrl, function(res) {
                    piSelect.html(originalOptions);
                    if(res.pi_no) {
                        if (piSelect.find("option[value='" + res.pi_no + "']").length === 0) {
                            piSelect.append('<option value="' + res.pi_no + '">' + res.pi_no + '</option>');
                        }
                        piSelect.val(res.pi_no);
                    } else {
                        piSelect.val('');
                    }
                }).fail(function() {
                    piSelect.html(originalOptions);
                    piSelect.val('');
                });
            } else {
                piSelect.val('');
            }
            $(this).removeClass('is-invalid border border-danger');
            calculateAll();
        });
    
        $(document).on('input', '.qty-input, .price-input, #tax_percent, #discount_amount', function() {
            calculateAll();
        });
    
        function calculateAll() {
            let subtotal = 0;
            $('#itemTable tbody tr').each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let price = parseFloat($(this).find('.price-input').val()) || 0;
                let total = qty * price;
                $(this).find('.row-total').val(total.toFixed(2));
                subtotal += total;
            });
    
            let discount = parseFloat($('#discount_amount').val()) || 0;
            let afterDiscount = subtotal - discount;
            if(afterDiscount < 0) afterDiscount = 0;
    
            let taxPercent = parseFloat($('#tax_percent').val()) || 0;
            let taxAmount = afterDiscount * (taxPercent / 100);
            let grandTotal = afterDiscount + taxAmount;
    
            $('#txt-subtotal').text('$' + subtotal.toFixed(2));
            $('#txt-grandtotal').text('$' + grandTotal.toFixed(2));
        }
    
        // ==========================================
        // 7. Save / Checkout Action
        // ==========================================
        $('#btn-save, #btn-save-top').click(function(e) {
            e.preventDefault();
    
            let cart = [];
            let isValid = true;
    
            $('#itemTable tbody tr').each(function() {
                let p_id = $(this).find('.select-product').val();
                let p_qty = $(this).find('.qty-input').val();
                let p_price = $(this).find('.price-input').val();
                let p_pi = $(this).find('.pi-input').val();
                let p_remark = $(this).find('.remark-input').val();
    
                if (!p_id) {
                    isValid = false;
                    $(this).find('.select-product').addClass('is-invalid border border-danger');
                } else {
                    $(this).find('.select-product').removeClass('is-invalid border border-danger');
                    cart.push({ id: p_id, qty: p_qty, price: p_price, purchase_invoice_no: p_pi, remark: p_remark });
                }
            });
    
            let loc_id = $('#location_id').val();
            if (!loc_id) {
                showAlert('warning', 'Missing Information!', 'Please select a Location first.');
                $('#location_id').focus();
                return;
            }
    
            let emp_id = $('#employee_id').val();
            if (!emp_id) {
                showAlert('warning', 'Missing Information!', 'Please select a Sales Person first.');
                $('#employee_id').select2('open');
                return;
            }
    
            let car_invoice = $('#car_invoice_no').val();
            if (!car_invoice) {
                showAlert('warning', 'Missing Information!', 'Please enter the Car Invoice No.');
                $('#car_invoice_no').focus();
                return;
            }
    
            if (isCarInvoiceDuplicate) {
                let reason = $('#reason').val();
                if(!reason) {
                    showAlert('warning', 'Duplicate Car Invoice!', 'Please specify a Reason, as this car invoice number already exists.');
                    $('#reason').focus();
                    return;
                }
            }
    
            if (!isValid || cart.length === 0) {
                showAlert('warning', 'Incomplete Data', 'Please select products correctly.');
                return;
            }
    
            let payload = {
                _token: "{{ csrf_token() }}",
                location_id: loc_id,
                employee_id: emp_id,
                customer_name: 'General Customer',
                car_buyer_name: $('#car_buyer_name').val(),
                car_invoice_no: car_invoice,
                reason: $('#reason').val(),
                remark: $('#remark').val(),
                tax_percent: $('#tax_percent').val() || 0,
                discount_amount: $('#discount_amount').val() || 0,
                cart: cart
            };
    
            $('#btn-save, #btn-save-top').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Processing...');
    
            $.ajax({
                url: "{{ route('checkout.process') }}",
                method: "POST",
                data: payload,
                success: function(res) {
                    if(res.success) {
                        let historyUrl = "{{ route('sales.history') }}";
    
                        Swal.fire({
                            title: 'Checkout Successful!',
                            text: 'The invoice has been saved successfully in the system.',
                            icon: 'success',
                            showCancelButton: false,
                            showDenyButton: true,
                            confirmButtonText: '<i class="bx bx-history me-1"></i> Go to History',
                            denyButtonText: '<i class="bx bx-plus me-1"></i> New Sale',
                            customClass: {
                                confirmButton: 'btn-pos btn-pos-brand me-2',
                                denyButton: 'btn-pos btn-pos-outline'
                            },
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = historyUrl;
                            } else if (result.isDenied) {
                                location.reload();
                            }
                        });
    
                    } else {
                        showAlert('error', 'Failed', res.message);
                        resetButtons();
                    }
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server Error';
                    showAlert('error', 'Unable to save', errorMsg);
                    resetButtons();
                }
            });
        });
    
        function resetButtons() {
            $('#btn-save').prop('disabled', false).html('<i class="bx bx-check-circle"></i> Checkout');
            $('#btn-save-top').prop('disabled', false).html('<i class="bx bx-save"></i> Save & Print');
        }
    });
</script>
@endsection