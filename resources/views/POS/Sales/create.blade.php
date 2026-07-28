@extends('layouts.homePages')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .checkout-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; }
    .table-hover tbody tr:hover { background-color: #f1f5f9; }
    .qty-input, .price-input { font-weight: 600; color: #0f172a; }
</style>

<div class="container-fluid flex-grow-1 container-p-y checkout-container ">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ផ្នែកលក់ /</span> បង្កើតវិក្កយបត្រ (POS Checkout)
        </h4>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary shadow-sm" onclick="window.history.back()">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </button>
            <button type="button" id="btn-save-top" class="btn btn-primary shadow-sm" style="background-color: #CE181E; border: none;">
                <i class="bx bx-save me-1"></i> រក្សាទុក & បោះពុម្ព
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7 col-12 mb-4">
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="m-0 fw-bold text-primary" style="color: #CE181E !important;"><i class="bx bx-user-circle me-1"></i> ព័ត៌មានអ្នកលក់ និង វិក្កយបត្រ</h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4 px-lg-3">
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">ឈ្មោះអ្នកលក់ (Sale Person)</label>
                            <div class="input-group input-group-merge shadow-none border rounded">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-user-badge"></i></span>
                                <input type="text" id="customer_name" class="form-control border-0 ps-0" placeholder="ឈ្មោះបុគ្គលិកផ្នែកលក់...">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">សាខា / ទីតាំង (Location) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-none border rounded">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-store"></i></span>
                                <select id="location_id" class="form-select border-0 ps-0" required>
                                    <option value="">-- ជ្រើសរើសទីតាំង --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">លេខវិក្កយបត្រ (Invoice No)</label>
                            <div class="input-group input-group-merge shadow-none border rounded bg-light">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-receipt"></i></span>
                                <input type="text" value="{{ $invoiceNo }}" class="form-control bg-transparent border-0 ps-0 fw-bold text-primary" readonly>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <label class="form-label fw-semibold text-muted">កាលបរិច្ឆេទ (Date)</label>
                            <div class="input-group input-group-merge shadow-none border rounded bg-light">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-calendar"></i></span>
                                <input type="date" value="{{ date('Y-m-d') }}" class="form-control bg-transparent border-0 ps-0 text-muted" readonly>
                            </div>
                        </div>

                        <div class="col-12"><hr class="my-2 border-secondary"></div>
                        
                        <div class="col-12">
                            <h6 class="text-primary fw-bold mb-0 mt-2" style="color: #CE181E !important;"><i class="bx bx-car me-1"></i> ព័ត៌មានឡាន និង ចំណាំ (Car Info & Remark)</h6>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <label class="form-label fw-semibold text-muted">អ្នកទិញឡាន (Car Buyer Name)</label>
                            <div class="input-group input-group-merge shadow-none border rounded">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-user-pin"></i></span>
                                <input type="text" id="car_buyer_name" class="form-control border-0 ps-0" placeholder="ឈ្មោះអ្នកទិញឡាន...">
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <label class="form-label fw-semibold text-muted">លេខវិក្កយបត្រឡាន <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-none border rounded" id="wrapper-car-invoice">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-hash"></i></span>
                                <input type="text" id="car_invoice_no" class="form-control border-0 ps-0" placeholder="វិក្កយបត្រឡាន..." required>
                            </div>
                            <small id="car-invoice-feedback" class="text-danger fw-bold mt-1 d-none">
                                <i class="bx bx-error-circle"></i> លេខនេះធ្លាប់បញ្ចេញកាដូរួចហើយ!
                            </small>
                        </div>

                        <div class="col-xl-4 col-md-12">
                            <label class="form-label fw-semibold text-muted">ចំណាំ / មូលហេតុ (Remark)</label>
                            <div class="input-group input-group-merge shadow-none border rounded" id="wrapper-reason">
                                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-comment-detail"></i></span>
                                <input type="text" id="reason" class="form-control border-0 ps-0" placeholder="វាយចំណាំផ្សេងៗ...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bx bx-cart-alt me-1"></i> បញ្ជីមុខទំនិញ (Items)</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-row" style="color: #CE181E; border-color: #CE181E;">
                        <i class="bx bx-plus me-1"></i> បន្ថែមជួរថ្មី
                    </button>
                </div>
                <div class="table-responsive text-nowrap" style="min-height: 350px;">
                    <table class="table table-hover align-middle" id="itemTable">
                        <thead class="table-light">
                            <tr>
                                <th width="40" class="text-center">#</th>
                                <th>ឈ្មោះទំនិញ (Product)</th>
                                <th width="180">វិក្កយបត្រទិញ (PI No)</th>
                                <th width="100" class="text-center">បរិមាណ</th>
                                <th width="130" class="text-end">តម្លៃ ($)</th>
                                <th width="130" class="text-end">សរុប ($)</th>
                                <th width="50" class="text-center"><i class="bx bx-trash text-danger"></i></th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td class="text-center fw-bold text-muted pt-2">1</td>
                                <td>
                                    <select class="form-select select-product shadow-none bg-light border-0">
                                        <option value="">-- រើសទំនិញ --</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->selling_price ?? $p->unit_price ?? $p->price ?? 0 }}">
                                                {{ $p->item_code }} - {{ $p->item_name }} (ស្តុក: {{ $p->stock_qty }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select pi-input shadow-none bg-light border-0">
                                        <option value="">-- រើស PI --</option>
                                        @if(isset($purchaseInvoices))
                                            @foreach($purchaseInvoices as $pi)
                                                <option value="{{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}">
                                                    {{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td><input type="number" class="form-control text-center qty-input shadow-none bg-light border-0" value="1" min="1"></td>
                                <td><input type="number" class="form-control text-end price-input shadow-none bg-light border-0" value="0.00" step="0.01"></td>
                                <td><input type="text" class="form-control text-end row-total bg-transparent border-0 fw-bold text-danger shadow-none" value="0.00" readonly></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon text-danger remove-row"><i class="bx bx-trash fs-5"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card border-0 shadow-sm sticky-top" style="top: 1.5rem;">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="bx bx-calculator me-1"></i> សរុបទឹកប្រាក់ (Payment Summary)</h6>
                </div>
                <div class="card-body pt-4">
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-semibold">សរុប (Subtotal):</span>
                        <span id="txt-subtotal" class="fw-bold fs-5 text-dark">$0.00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold">បញ្ចុះតម្លៃ (Discount):</span>
                        <div class="w-50">
                            <div class="input-group input-group-merge border rounded shadow-none">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="number" id="discount_amount" class="form-control text-end border-0 fw-bold" value="0" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-muted fw-semibold">ប្រាក់ពន្ធ (Tax):</span>
                        <div class="w-50">
                            <div class="input-group input-group-merge border rounded shadow-none">
                                <input type="number" id="tax_percent" class="form-control text-end border-0 fw-bold" value="0" min="0">
                                <span class="input-group-text bg-light border-0">%</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-secondary">

                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" style="background-color: rgba(206, 24, 30, 0.05); border: 1px dashed rgba(206, 24, 30, 0.3);">
                        <span class="h5 fw-bold mb-0 text-dark">សរុបរួម:</span>
                        <span id="txt-grandtotal" class="h2 fw-bold mb-0" style="color: #CE181E;">$0.00</span>
                    </div>

                    <button type="button" id="btn-save" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="background-color: #CE181E; border: none; padding: 15px 0;">
                        <i class="bx bx-check-circle me-1"></i> គិតលុយ (Checkout)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// រៀបចំ Options នៃ Purchase Invoices ទុកជាមុន សម្រាប់ពេលចុចបន្ថែមជួរ
const piOptions = `<option value="">-- រើស PI --</option>
    @if(isset($purchaseInvoices))
        @foreach($purchaseInvoices as $pi)
            <option value="{{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}">
                {{ $pi->purchase_invoice_no ? $pi->purchase_invoice_no : $pi->invoice_no }}
            </option>
        @endforeach
    @endif`;

$(document).ready(function() {
    let rowCount = 1;
    let isCarInvoiceDuplicate = false; 

    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonText: 'យល់ព្រម',
            customClass: { confirmButton: 'btn btn-primary' },
            buttonsStyling: false
        });
    }

    $('#car_invoice_no').on('blur', function() {
        let car_inv = $(this).val();
        if(car_inv) {
            $.post("{{ route('checkout.check-car-invoice') }}", {
                _token: "{{ csrf_token() }}",
                car_invoice_no: car_inv
            }, function(res) {
                if(res.exists) {
                    isCarInvoiceDuplicate = true;
                    $('#car-invoice-feedback').removeClass('d-none');
                    $('#wrapper-car-invoice, #wrapper-reason').addClass('border-danger');
                    $('#reason').attr('placeholder', 'សូមបញ្ចូលមូលហេតុដែលចេញកាដូស្ទួន...');
                } else {
                    isCarInvoiceDuplicate = false;
                    $('#car-invoice-feedback').addClass('d-none');
                    $('#wrapper-car-invoice, #wrapper-reason').removeClass('border-danger');
                    $('#reason').attr('placeholder', 'វាយចំណាំផ្សេងៗនៅទីនេះ...');
                }
            });
        }
    });

    $('#add-row').click(function() {
        rowCount++;
        let newRow = `<tr>
            <td class="text-center fw-bold text-muted pt-2">${rowCount}</td>
            <td>
                <select class="form-select select-product shadow-none bg-light border-0">
                    <option value="">-- រើសទំនិញ --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->selling_price ?? $p->unit_price ?? $p->price ?? 0 }}">
                            {{ $p->item_code }} - {{ $p->item_name }} (ស្តុក: {{ $p->stock_qty }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select class="form-select pi-input shadow-none bg-light border-0">
                    ${piOptions}
                </select>
            </td>
            <td><input type="number" class="form-control text-center qty-input shadow-none bg-light border-0" value="1" min="1"></td>
            <td><input type="number" class="form-control text-end price-input shadow-none bg-light border-0" value="0.00" step="0.01"></td>
            <td><input type="text" class="form-control text-end row-total bg-transparent border-0 fw-bold text-danger shadow-none" value="0.00" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-icon text-danger remove-row"><i class="bx bx-trash fs-5"></i></button>
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
            showAlert('warning', 'មិនអាចលុបបានទេ!', 'វិក្កយបត្រត្រូវមានទំនិញយ៉ាងហោចណាស់ ១ ជួរ។');
        }
    });

    function reorderRows() {
        $('#itemTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
        rowCount = $('#itemTable tbody tr').length;
    }

    // ✅ Update ទី៣: ប្រើ .attr('data-price') ដើម្បីចាប់តម្លៃ
    $(document).on('change', '.select-product', function() {
        let selectedOption = $(this).find('option:selected');
        
        let rawPrice = selectedOption.attr('data-price');
        let price = parseFloat(rawPrice) || 0; 
        
        let product_id = $(this).val(); 
        let row = $(this).closest('tr');
        let piSelect = row.find('.pi-input');
        
        // ទម្លាក់តម្លៃលក់ចូលប្រអប់អូតូ
        row.find('.price-input').val(price.toFixed(2));
        
        if (product_id) {
            let originalOptions = piSelect.html();
            piSelect.html('<option value="">កំពុងស្វែងរក...</option>');

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
        
        // គណនាលុយសរុបអូតូក្រោយពេលបោះតម្លៃចូលប្រអប់
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

    $('#btn-save, #btn-save-top').click(function(e) {
        e.preventDefault();
        
        let cart = [];
        let isValid = true;

        $('#itemTable tbody tr').each(function() {
            let p_id = $(this).find('.select-product').val();
            let p_qty = $(this).find('.qty-input').val();
            let p_price = $(this).find('.price-input').val();
            let p_pi = $(this).find('.pi-input').val(); // ចាប់យកលេខ PI

            if (!p_id) {
                isValid = false;
                $(this).find('.select-product').addClass('is-invalid border border-danger');
            } else {
                $(this).find('.select-product').removeClass('is-invalid border border-danger');
                cart.push({ id: p_id, qty: p_qty, price: p_price, purchase_invoice_no: p_pi });
            }
        });

        let loc_id = $('#location_id').val();
        if (!loc_id) {
            showAlert('warning', 'ខ្វះព័ត៌មាន!', 'សូមជ្រើសរើសទីតាំង (Location) ជាមុនសិន។');
            $('#location_id').focus();
            return;
        }

        let car_invoice = $('#car_invoice_no').val();
        if (!car_invoice) {
            showAlert('warning', 'ខ្វះព័ត៌មាន!', 'សូមបញ្ចូលលេខវិក្កយបត្រឡាន (Car Invoice)។');
            $('#car_invoice_no').focus();
            return;
        }

        if (isCarInvoiceDuplicate) {
            let reason = $('#reason').val();
            if(!reason) {
                showAlert('warning', 'លេខឡានស្ទួន!', 'សូមបញ្ជាក់មូលហេតុនៅកន្លែង (Remark) ជាមុនសិន។');
                $('#reason').focus();
                return;
            }
        }

        if (!isValid || cart.length === 0) {
            showAlert('warning', 'ទិន្នន័យមិនពេញលេញ', 'សូមជ្រើសរើសទំនិញឱ្យបានត្រឹមត្រូវ។');
            return;
        }

        let payload = {
            _token: "{{ csrf_token() }}",
            location_id: loc_id,
            customer_name: $('#customer_name').val(),
            car_buyer_name: $('#car_buyer_name').val(), 
            car_invoice_no: car_invoice,                  
            reason: $('#reason').val(),                 
            tax_percent: $('#tax_percent').val() || 0,
            discount_amount: $('#discount_amount').val() || 0,
            cart: cart
        };

        $('#btn-save, #btn-save-top').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> កំពុងដំណើរការ...');

        $.ajax({
            url: "{{ route('checkout.process') }}",
            method: "POST",
            data: payload,
            success: function(res) {
                if(res.success) {
                    let historyUrl = "{{ url('/pos/sales/history') }}"; 
                    
                    Swal.fire({
                        title: 'ទូទាត់ជោគជ័យ!',
                        text: 'វិក្កយបត្រត្រូវបានរក្សាទុករួចរាល់ក្នុងប្រព័ន្ធ។',
                        icon: 'success',
                        showCancelButton: false,
                        showDenyButton: true,
                        confirmButtonText: '<i class="bx bx-history me-1"></i> ទៅប្រវត្តិលក់',
                        denyButtonText: '<i class="bx bx-plus me-1"></i> លក់ថ្មី',
                        customClass: {
                            confirmButton: 'btn btn-primary me-2',
                            denyButton: 'btn btn-success'
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
                    showAlert('error', 'បរាជ័យ', res.message);
                    resetButtons();
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server Error';
                showAlert('error', 'មិនអាចរក្សាទុកបានទេ', errorMsg);
                resetButtons();
            }
        });
    });

    function resetButtons() {
        $('#btn-save').prop('disabled', false).html('<i class="bx bx-check-circle me-1"></i> គិតលុយ (Checkout)');
        $('#btn-save-top').prop('disabled', false).html('<i class="bx bx-save me-1"></i> រក្សាទុក & បោះពុម្ព');
    }
});
</script>
@endsection