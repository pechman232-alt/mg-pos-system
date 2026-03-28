@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;700&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { 
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --tf-gradient-4: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-600: #475569;
        --slate-800: #1e293b;
    }

    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: #f4f7fa; color: var(--slate-800); }
    .container-full { width: 100% !important; max-width: 100% !important; padding: 1.5rem 2.5rem !important; }

    .brand-gradient { 
        background: var(--tf-gradient-4); border-radius: 24px; padding: 2.5rem 3rem; color: white;
        box-shadow: 0 10px 25px -5px rgba(206, 24, 30, 0.3);
    }
    
    .section-container { background: #ffffff; padding: 2.25rem; border-radius: 24px; border: 1px solid var(--slate-200); margin-bottom: 1.5rem; }
    .section-title { font-size: 0.75rem; font-weight: 800; color: var(--brand-red); text-transform: uppercase; letter-spacing: 0.12em; display: flex; align-items: center; margin-bottom: 2rem; }
    .section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; margin-left: 1.25rem; }

    .input-style { width: 100%; border-radius: 12px; border: 1.5px solid var(--slate-200); padding: 0.75rem 1rem; font-size: 0.9rem; font-weight: 600; transition: 0.2s; background: #fff; }
    .input-style:focus { border-color: var(--brand-red); outline: none; box-shadow: 0 0 0 4px rgba(206, 24, 30, 0.08); }
    
    label { display: block; margin-bottom: 8px; font-weight: 800; color: var(--slate-600); font-size: 10px; text-transform: uppercase; }
    
    .btn-brand { background: var(--brand-red); color: white; border: none; font-weight: 900; padding: 1rem 2.5rem; border-radius: 16px; transition: 0.3s; }
    .btn-brand:hover { background: #b91c1c; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(185, 28, 28, 0.4); }

    .btn-circle { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: none; transition: 0.2s; }
    .btn-plus { background: #ecfdf5; color: #059669; }
    .btn-minus { background: #fff5f5; color: var(--brand-red); }

    .finance-highlight { border: 2px solid var(--brand-red) !important; background: #fffdfd; }

    /* Identity Upload Styling */
    #drop-area { transition: 0.3s ease; border: 2px dashed var(--slate-200); cursor: pointer; background: #fafbfc; position: relative; }
    #drop-area:hover { border-color: var(--brand-red); background-color: #fff5f5; }
</style>

<div class="container-full">
    <div class="brand-gradient mb-5 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="font-black mb-1 display-6 text-white">កែប្រែកិច្ចសន្យា (Edit Sales Contract)</h1>
            <p class="opacity-75 font-bold mb-0 text-white uppercase small">Official Registry Update: {{ $contract->contract_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('contracts.show', $contract->id) }}" class="btn btn-outline-light rounded-2xl px-4 py-2 font-black border-2">VIEW CURRENT</a>
            <a href="{{ route('contracts.index') }}" class="btn btn-light rounded-2xl px-4 py-2 font-black shadow-sm">DISCARD CHANGES</a>
        </div>
    </div>

    <form action="{{ route('contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4 text-start">
            
            <div class="col-12">
                <div class="section-container">
                    <h3 class="section-title">01. Showroom Logistics / ព័ត៌មានកិច្ចសន្យា</h3>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label>Showroom Branch</label>
                            <select name="branch_name" class="input-style" required>
                                <option value="Phnom Penh Showroom" @selected($contract->branch_name == 'Phnom Penh Showroom')>Phnom Penh Showroom</option>
                                <option value="Battambang Showroom" @selected($contract->branch_name == 'Battambang Showroom')>Battambang Showroom</option>
                                <option value="Siem Reap Showroom" @selected($contract->branch_name == 'Siem Reap Showroom')>Siem Reap Showroom</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Contract Number / លេខឯកសារ</label>
                            <input type="text" name="contract_number" value="{{ $contract->contract_number }}" class="input-style font-mono text-danger" required>
                        </div>
                        <div class="col-md-2">
                            <label>Status / ស្ថានភាព</label>
                            <select name="status" class="input-style border-danger text-danger font-bold">
                                <option value="Booking" @selected($contract->status == 'Booking')>BOOKING / កក់ទុក</option>
                                <option value="Payoff" @selected($contract->status == 'Payoff')>PAYOFF / ទូទាត់ផ្តាច់</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Order Date / កាលបរិច្ឆេទ</label>
                            <input type="date" name="order_date" value="{{ $contract->order_date->format('Y-m-d') }}" class="input-style" required>
                        </div>
                        <div class="col-md-2">
                            <label>Sales Person / ផ្នែកលក់</label>
                            <select name="sale_person" class="input-style" required>
                                @foreach(['Pum Sorya', 'Sanh Rady', 'Sem SereyYanuth', 'Thul Ratha', 'Poeng Lyly', 'Oum Kim', 'Bun Narith', 'Khat Ra', 'Say Toeun', 'Sib KimCheu', 'Liam Sophuon', 'Phoem Phearakyuth'] as $staff)
                                    <option value="{{ $staff }}" @selected($contract->sale_person == $staff)>{{ $staff }}</option>
                                @endforeach
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
                            <input type="text" name="customer_name" value="{{ $contract->customer_name }}" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Phone / លេខទំនាក់ទំនង</label>
                            <input type="text" name="phone_number" value="{{ $contract->phone_number }}" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Location / ខេត្ត/ក្រុង</label>
                            <select name="location_province" class="input-style" required>
                                @foreach(['Phnom Penh', 'Banteay Meanchey', 'Battambang', 'Kampong Cham', 'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom', 'Kampot', 'Kandal', 'Kep', 'Koh Kong', 'Kratié', 'Mondulkiri', 'Oddar Meanchey', 'Pailin', 'Preah Sihanouk', 'Preah Vihear', 'Prey Veng', 'Pursat', 'Ratanakiri', 'Siem Reap', 'Stung Treng', 'Svay Rieng', 'Takéo', 'Tboung Khmum'] as $province)
                                    <option value="{{ $province }}" @selected($contract->location_province == $province)>{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Source / ប្រភព</label>
                            <select name="source" class="input-style">
                                <option value="Direct" @selected($contract->source == 'Direct')>Direct Walk-in</option>
                                <option value="Facebook" @selected($contract->source == 'Facebook')>Facebook</option>
                                <option value="Referral" @selected($contract->source == 'Referral')>Referral</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label>Address / អាសយដ្ឋាន</label>
                            <textarea name="address" rows="1" class="input-style" required>{{ $contract->address }}</textarea>
                        </div>

                        <div class="col-12 mt-2">
                            <label>Identity Document / ឯកសារអត្តសញ្ញាណ (Click to Replace)</label>
                            <div class="rounded-2xl p-4 text-center" id="drop-area">
                                <input type="file" name="customer_id_card" id="customer_id_card" class="d-none" accept="image/*" onchange="previewImage(event)">
                                
                                {{-- Placeholder shows ONLY if there is no existing image --}}
                                <div id="upload-placeholder" class="{{ $contract->customer_id_card ? 'd-none' : '' }} py-4">
                                    <i class="fas fa-id-card fa-3x mb-2 text-slate-400"></i>
                                    <p class="mb-0 font-bold text-slate-500 small">NO IMAGE ATTACHED. CLICK TO UPLOAD.</p>
                                </div>

                                {{-- Image Preview Container --}}
                                <div id="image-preview-container" class="{{ !$contract->customer_id_card ? 'd-none' : '' }}">
                                    <img id="output-image" 
                                         src="{{ $contract->customer_id_card ? asset('storage/' . $contract->customer_id_card) : '#' }}" 
                                         class="img-fluid rounded-xl shadow-sm border mb-3" 
                                         style="max-height: 250px; object-fit: contain;">
                                    <div>
                                        <span class="badge bg-danger px-3 py-2 rounded-pill font-bold" style="cursor: pointer;" onclick="document.getElementById('customer_id_card').click();">
                                            <i class="fas fa-sync-alt me-1"></i> REPLACE IMAGE
                                        </span>
                                    </div>
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
                            <input type="text" name="model" value="{{ $contract->model }}" class="input-style font-bold" required>
                        </div>
                        <div class="col-md-6">
                            <label>Color / ពណ៌</label>
                            <input type="text" name="color" value="{{ $contract->color }}" class="input-style" required>
                        </div>
                        <div class="col-md-6">
                            <label>Quantity</label>
                            <input type="number" name="qty" value="{{ $contract->qty ?? 1 }}" class="input-style text-center font-black" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label>VIN Number / លេខតួ</label>
                            <input type="text" name="vin_number" value="{{ $contract->vin_number }}" class="input-style font-mono" placeholder="LSJXXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label>Engine Number / លេខម៉ាស៊ីន</label>
                            <input type="text" name="engine_number" value="{{ $contract->engine_number }}" class="input-style font-mono" placeholder="ENGXXXXXXXX">
                        </div>

                        <div class="col-12">
                            <label>Offers / ការផ្តល់ជូន (Click + to add)</label>
                            <div id="offers-container">
                                @php 
                                    $existingOffers = !empty($contract->offers) ? explode(', ', $contract->offers) : ['']; 
                                @endphp

                                @foreach($existingOffers as $offerItem)
                                <div class="d-flex gap-2 mb-2 offer-item">
                                    <input type="text" name="offers[]" value="{{ $offerItem }}" class="input-style" placeholder="Extra offer...">
                                    @if($loop->first)
                                        <button type="button" class="btn-circle btn-plus" onclick="addOfferField()"><i class="fas fa-plus"></i></button>
                                    @else
                                        <button type="button" class="btn-circle btn-minus" onclick="removeOfferField(this)"><i class="fas fa-minus"></i></button>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="section-container finance-highlight">
                    <h3 class="section-title">04. Financial Settlement / ហិរញ្ញវត្ថុ</h3>
                    <div class="row g-4 align-items-center">
                        <div class="col-md-3">
                            <label>Sub Total ($) / ទឹកប្រាក់សរុប</label>
                            <input type="number" step="0.01" id="sub_total" name="sub_total" value="{{ $contract->sub_total }}" class="input-style font-black h5 calc-input" required>
                        </div>
                        <div class="col-md-2">
                            <label>Discount ($) / បញ្ចុះតម្លៃ</label>
                            <input type="number" step="0.01" id="discount" name="discount" value="{{ $contract->discount }}" class="input-style text-danger calc-input">
                        </div>
                        <div class="col-md-2">
                            <label>Deposit ($) / ប្រាក់កក់</label>
                            <input type="number" step="0.01" id="deposit" name="deposit" value="{{ $contract->deposit }}" class="input-style text-primary calc-input">
                        </div>
                        <div class="col-md-3">
                            <label class="text-danger font-black">Balance Due / សមតុល្យ ($)</label>
                            <input type="number" id="grand_total" value="{{ $contract->grand_total }}" class="input-style border-danger text-danger font-black" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Payment Type</label>
                            <select name="payment_type" class="input-style font-bold">
                                <option value="Cash" @selected($contract->payment_type == 'Cash')>Cash (សាច់ប្រាក់)</option>
                                <option value="Leasing" @selected($contract->payment_type == 'Leasing')>Leasing (បង់រំលស់)</option>
                                <option value="ABA" @selected($contract->payment_type == 'ABA')>ABA Transfer</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text-end mb-5">
                <button type="submit" class="btn btn-brand shadow-lg px-5 py-3">
                    <i class="fas fa-sync-alt mr-2"></i> UPDATE REGISTRY RECORD
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Trigger File Input when clicking the drop area
    document.getElementById('drop-area').addEventListener('click', function(e) {
        if (e.target.tagName !== 'SPAN' && e.target.tagName !== 'I') {
            document.getElementById('customer_id_card').click();
        }
    });

    // 2. Real-time Image Preview logic
    function previewImage(event) {
        const reader = new FileReader();
        const output = document.getElementById('output-image');
        const placeholder = document.getElementById('upload-placeholder');
        const previewContainer = document.getElementById('image-preview-container');

        reader.onload = function() {
            output.src = reader.result;
            placeholder.classList.add('d-none');
            previewContainer.classList.remove('d-none');
        }
        
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // 3. Dynamic Offer Fields logic
    function addOfferField() {
        const container = document.getElementById('offers-container');
        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2 offer-item';
        div.innerHTML = `
            <input type="text" name="offers[]" class="input-style" placeholder="Extra offer...">
            <button type="button" class="btn-circle btn-minus" onclick="removeOfferField(this)"><i class="fas fa-minus"></i></button>
        `;
        container.appendChild(div);
    }

    function removeOfferField(btn) {
        btn.closest('.offer-item').remove();
    }

    // 4. Financial Calculation logic
    const calculateTotal = () => {
        const sub = parseFloat(document.getElementById('sub_total').value) || 0;
        const dis = parseFloat(document.getElementById('discount').value) || 0;
        const dep = parseFloat(document.getElementById('deposit').value) || 0;
        document.getElementById('grand_total').value = (sub - dis - dep).toFixed(2);
    };

    // Calculate on Load to show correct balance immediately
    document.addEventListener('DOMContentLoaded', calculateTotal);

    document.querySelectorAll('.calc-input').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
</script>
@endsection