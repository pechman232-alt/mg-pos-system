@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Kantumruy+Pro:wght@400;700&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root { 
        --brand-indigo: #4f46e5; --brand-purple: #7c3aed; --brand-pink: #ec4899; --brand-red: #ef4444;
        --tf-gradient-4: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        --slate-200: #e2e8f0; --slate-600: #475569; --slate-800: #1e293b;
    }
    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: #f4f7fa; color: var(--slate-800); }
    .container-full { width: 100% !important; max-width: 100% !important; padding: 1.5rem 2.5rem !important; }
    .brand-gradient { background: var(--tf-gradient-4); border-radius: 24px; padding: 2.5rem 3rem; color: white; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3); }
    .section-container { background: #ffffff; padding: 2.25rem; border-radius: 24px; border: 1px solid var(--slate-200); margin-bottom: 1.5rem; }
    .section-title { font-size: 0.75rem; font-weight: 800; color: var(--brand-indigo); text-transform: uppercase; letter-spacing: 0.12em; display: flex; align-items: center; margin-bottom: 2rem; }
    .section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; margin-left: 1.25rem; }
    .input-style { width: 100%; border-radius: 12px; border: 1.5px solid var(--slate-200); padding: 0.75rem 1rem; font-size: 0.9rem; font-weight: 600; transition: 0.2s; background: #fff; }
    .input-style:focus { border-color: var(--brand-indigo); outline: none; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08); }
    label { display: block; margin-bottom: 8px; font-weight: 800; color: var(--slate-600); font-size: 10px; text-transform: uppercase; }
    .btn-brand { background: var(--tf-gradient-4); color: white; border: none; font-weight: 900; padding: 1rem 2.5rem; border-radius: 16px; transition: 0.3s; }
    .btn-brand:hover { transform: translateY(-2px); box-shadow: 0 10px 15px rgba(79, 70, 229, 0.4); }
    .btn-circle { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: none; transition: 0.2s; }
    .btn-plus { background: #f5f3ff; color: var(--brand-indigo); }
    .btn-minus { background: #fff1f2; color: var(--brand-red); }
</style>

<div class="container-full">
    <div class="brand-gradient mb-5 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="font-black mb-1 display-6 text-white">បង្កើតកិច្ចសន្យាថ្មី (New Sales Contract)</h1>
            <p class="opacity-75 font-bold mb-0 text-white uppercase small">TF Motors Showroom Management Intelligence</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-light rounded-pill px-4 py-2 font-black shadow-sm">BACK TO LIST</a>
    </div>

    <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data" id="contractForm">
        @csrf
        <div class="row g-4 text-start">
            
            <div class="col-12">
                <div class="section-container">
                    <h3 class="section-title">01. Showroom Logistics / ព័ត៌មានកិច្ចសន្យា</h3>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label>Showroom Branch</label>
                            <select name="branch_name" class="input-style" required>
                                <option value="" disabled selected>Select Showroom</option>
                                <option value="Phnom Penh Showroom">Phnom Penh Showroom</option>
                                <option value="Siem Reap Showroom">Siem Reap Showroom</option>
                                <option value="Battambang Showroom">Battambang Showroom</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Contract Number / លេខឯកសារ</label>
                            <input type="text" name="contract_number" class="input-style text-primary" placeholder="C26V1-XXXX" required>
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" class="input-style fw-bold">
                                <option value="Booking">BOOKING / កក់ទុក</option>
                                <option value="Payoff">PAYOFF / ទូទាត់ផ្តាច់</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Order Date</label>
                            <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="input-style" required>
                        </div>
                        <div class="col-md-2">
                            <label>Sales Person / ផ្នែកលក់</label>
                            <select name="sale_person" class="input-style" required>
                                <option value="" disabled selected>Select Staff</option>
                                <option value="Pum Sorya">Pum Sorya</option>
                                <option value="Sanh Rady">Sanh Rady</option>
                                <option value="Sem SereyYanuth">Sem SereyYanuth</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="section-container">
                    <h3 class="section-title">02. Customer Identity / អតិថិជន</h3>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label>Customer Name / ឈ្មោះអតិថិជន</label>
                            <input type="text" name="customer_name" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Phone / លេខទំនាក់ទំនង</label>
                            <input type="text" name="phone_number" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Location / ខេត្ត/ក្រុង</label>
                            <select name="location_province" class="input-style" required>
                                <option value="Phnom Penh">Phnom Penh</option>
                                <option value="Siem Reap">Siem Reap</option>
                                <option value="Battambang">Battambang</option>
                                <option value="Kandal">Kandal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Source / ប្រភព</label>
                            <select name="source" class="input-style">
                                <option value="Direct">Direct Walk-in</option>
                                <option value="Facebook">Facebook / Social Media</option>
                                <option value="Referral">Referral</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label>Address / អាសយដ្ឋាន</label>
                            <textarea name="address" rows="1" class="input-style" required></textarea>
                        </div>
                        <div class="col-12">
                            <label>Identity Document / ឯកសារអត្តសញ្ញាណ (ID/Passport)</label>
                            <div class="rounded-2xl p-4 text-center border-2 border-dashed bg-light" id="drop-area" onclick="document.getElementById('customer_id_card').click();">
                                <input type="file" name="customer_id_card" id="customer_id_card" class="d-none" accept="image/*" onchange="previewImage(event)">
                                <div id="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-primary opacity-50"></i>
                                    <p class="mb-0 font-bold text-slate-500 small">CLICK TO UPLOAD ID CARD IMAGE</p>
                                </div>
                                <div id="image-preview-container" class="d-none">
                                    <img id="output-image" class="img-fluid rounded shadow-sm border mb-2" style="max-height: 180px;">
                                    <button type="button" class="btn btn-sm btn-danger d-block mx-auto" onclick="removeImage(event)">REMOVE</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="section-container">
                    <h3 class="section-title">03. Vehicle Specs & Offers / ព័ត៌មានយានយន្ត</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label>Model / ម៉ូឌែល</label>
                            <input type="text" name="model" class="input-style" placeholder="MG7 / MG ZS" required>
                        </div>
                        <div class="col-md-6">
                            <label>Color / ពណ៌</label>
                            <input type="text" name="color" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Quantity</label>
                            <input type="number" name="qty" value="1" class="input-style text-center" required>
                        </div>
                        <div class="col-md-6">
                            <label>VIN / លេខតួ</label>
                            <input type="text" name="vin_number" class="input-style font-mono" maxlength="17" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-md-6">
                            <label>Engine / លេខម៉ាស៊ីន</label>
                            <input type="text" name="engine_number" class="input-style font-mono" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div class="col-12">
                            <label>Offers / ការផ្តល់ជូន</label>
                            <div id="offers-container">
                                <div class="d-flex gap-2 mb-2 offer-item align-items-center">
                                    <select name="offers[]" class="input-style flex-grow-1">
                                        <option value="Warranty 5 Years or 150,000 KM">Warranty 5 Years or 150,000 KM</option>
                                        <option value="Normal Plate Number">Normal Plate Number</option>
                                        <option value="UV Film On Windows">UV Film On Windows</option>
                                        <option value="Floor Mats">Floor Mats</option>
                                        <option value="1 Year Full Service">1 Year Full Service</option>
                                    </select>
                                    <button type="button" class="btn-circle btn-plus" onclick="addOfferField()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="section-container" style="border: 2px solid var(--brand-indigo);">
                    <h3 class="section-title">04. Financial Settlement / ហិរញ្ញវត្ថុ</h3>
                    <div class="row g-4 align-items-center">
                        <div class="col-md-3">
                            <label>Sub Total ($)</label>
                            <input type="number" step="0.01" id="sub_total" name="sub_total" class="input-style font-black h5 calc-input" required>
                        </div>
                        <div class="col-md-2">
                            <label>Discount ($)</label>
                            <input type="number" step="0.01" id="discount" name="discount" class="input-style text-danger calc-input" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label>Deposit ($)</label>
                            <input type="number" step="0.01" id="deposit" name="deposit" class="input-style text-primary calc-input" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="text-indigo-600 font-black">Grand Total (Balance Due) ($)</label>
                            <input type="number" id="grand_total" name="grand_total" class="input-style border-indigo-200 text-indigo-700 font-black h5" readonly value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label>Payment Type</label>
                            <select name="payment_type" class="input-style" required>
                                <option value="Cash">Cash (សាច់ប្រាក់)</option>
                                <option value="ABA">ABA Bank</option>
                                <option value="Leasing">Leasing (បង់រំលស់)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end mb-5">
                <button type="submit" class="btn btn-brand shadow-lg px-5 py-3">
                    <i class="fas fa-check-circle me-2"></i> AUTHORIZE & SAVE CONTRACT
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Auto Calculation Logic
    $(document).ready(function() {
        $('.calc-input').on('input', function() {
            let sub = parseFloat($('#sub_total').val()) || 0;
            let dis = parseFloat($('#discount').val()) || 0;
            let dep = parseFloat($('#deposit').val()) || 0;
            let grand = sub - dis - dep;
            $('#grand_total').val(grand.toFixed(2));
        });
    });

    // 2. Image Preview Logic
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('output-image').src = reader.result;
            document.getElementById('upload-placeholder').classList.add('d-none');
            document.getElementById('image-preview-container').classList.remove('d-none');
        }
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function removeImage(event) {
        event.stopPropagation();
        document.getElementById('customer_id_card').value = "";
        document.getElementById('upload-placeholder').classList.remove('d-none');
        document.getElementById('image-preview-container').classList.add('d-none');
    }

    // 3. Dynamic Offer Fields
    function addOfferField() {
        const container = document.getElementById('offers-container');
        const firstSelect = container.querySelector('select').innerHTML;
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2 offer-item align-items-center';
        div.innerHTML = `
            <select name="offers[]" class="input-style flex-grow-1">${firstSelect}</select>
            <button type="button" class="btn-circle btn-minus" onclick="this.closest('.offer-item').remove()">
                <i class="fas fa-minus"></i>
            </button>
        `;
        container.appendChild(div);
    }
</script>
@endsection