@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --brand-indigo: #4f46e5; --exec-slate: #0f172a; --bg-body: #f8fafc; }
    body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; color: var(--exec-slate); }
    
    /* 🚀 Full Width 100% Layout */
    .container-full-width { 
        width: 100%; 
        padding: 20px; 
    }
    
    .card-auth { 
        border-radius: 15px; border: none; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        background: #fff; overflow: hidden; 
        animation: smoothEntry 0.6s ease-out;
        width: 100%;
    }

    .card-header-premium { 
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        padding: 20px 30px; color: #fff; 
        display: flex; justify-content: space-between; align-items: center;
    }
    
    .header-info h4 { font-size: 1.1rem; font-weight: 800; margin: 0; text-transform: uppercase; }
    .header-info p { font-size: 0.7rem; margin: 0; opacity: 0.8; }

    .section-header { 
        font-weight: 800; color: var(--brand-indigo); font-size: 0.75rem; text-transform: uppercase; 
        letter-spacing: 1px; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
        display: flex; align-items: center; gap: 10px;
    }

    /* 📏 Full Width Column Styling */
    .form-group-custom { margin-bottom: 20px; position: relative; }
    .label-custom { font-weight: 700; color: #475569; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .label-khmer { font-family: 'Noto Sans Khmer'; font-size: 0.65rem; color: var(--brand-indigo); font-weight: 600; }
    
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .field-icon { position: absolute; left: 18px; color: #94a3b8; font-size: 1rem; }

    .input-custom { 
        width: 100%; padding: 14px 15px 14px 50px; background: #f8fafc; border: 1.5px solid #e2e8f0; 
        border-radius: 12px; font-weight: 600; font-size: 0.95rem; transition: 0.3s; color: var(--exec-slate);
    }
    .input-custom:focus { 
        border-color: var(--brand-indigo); outline: none; background: #fff; 
        box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.08); 
    }

    .btn-authorize { 
        width: 100%; background: linear-gradient(90deg, var(--brand-indigo), #7c3aed); 
        color: #fff; padding: 18px; border: none; border-radius: 15px; font-weight: 800; font-size: 1rem; 
        text-transform: uppercase; letter-spacing: 1px; margin-top: 10px; transition: 0.3s; cursor: pointer;
    }
    .btn-authorize:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(79, 70, 229, 0.3); }

    @keyframes smoothEntry { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-fluid container-full-width">
    <div class="card-auth">
        <div class="card-header-premium">
            <div class="header-info">
                <h4><i class="fas fa-user-plus me-2"></i> Prospect Intake</h4>
                <p>Authorized Lead Management System</p>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-800" style="font-size: 11px;">
                <i class="fas fa-times me-1"></i> CLOSE
            </a>
        </div>

        <div class="card-body p-4 p-md-5">
            <form id="leadForm" action="{{ route('leads.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-12">
                        <div class="section-header"><i class="fas fa-id-card"></i> 01. Context & Attribution</div>
                        
                        <div class="form-group-custom">
                            <label class="label-custom">Registration Date <span class="label-khmer">(ថ្ងៃចុះឈ្មោះ)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-calendar-alt field-icon"></i>
                                <input type="date" name="date" class="input-custom" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Sales Consultant <span class="label-khmer">(ទីប្រឹក្សាលក់)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user-tie field-icon"></i>
                                <select name="sales_person" class="input-custom" required>
                                    <option value="" disabled selected>Select consultant...</option>
                                    @foreach($staff as $name) <option value="{{ $name }}">{{ $name }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Assigned Branch <span class="label-khmer">(សាខា)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-building field-icon"></i>
                                <select name="branch" class="input-custom" required>
                                    <option value="" disabled selected>Select Branch...</option>
                                    <option value="MG Head Office">MG Head Office</option>
                                    <option value="MG 60M Branch">MG 60M Branch</option>
                                    <option value="MG Siem Reap">MG Siem Reap</option>
                                    <option value="MG Battambang">MG Battambang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="section-header"><i class="fas fa-user-circle"></i> 02. Customer Identity & Asset Interest</div>

                        <div class="form-group-custom">
                            <label class="label-custom">Primary Contact <span class="label-khmer">(លេខទូរស័ព្ទ)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone-alt field-icon"></i>
                                <input type="text" name="contact_info" id="contact_info" class="input-custom" placeholder="0XX XXX XXX" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Client Full Name <span class="label-khmer">(ឈ្មោះអតិថិជន)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user field-icon"></i>
                                <input type="text" name="customer_name" class="input-custom" placeholder="Enter client identity" required>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Province / City <span class="label-khmer">(រាជធានី-ខេត្ត)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt field-icon"></i>
                                <select name="location" class="input-custom" required>
                                    <option value="" disabled selected>Select Location...</option>
                                    <option value="Phnom Penh">Phnom Penh – ភ្នំពេញ</option>
                                    <option value="Banteay Meanchey">Banteay Meanchey – បន្ទាយមានជ័យ</option>
                                    <option value="Battambang">Battambang – បាត់ដំបង</option>
                                    <option value="Kampong Cham">Kampong Cham – កំពង់ចាម</option>
                                    <option value="Kampong Chhnang">Kampong Chhnang – កំពង់ឆ្នាំង</option>
                                    <option value="Kampong Speu">Kampong Speu – កំពង់ស្ពឺ</option>
                                    <option value="Kampong Thom">Kampong Thom – កំពង់ធំ</option>
                                    <option value="Kampot">Kampot – កំពត</option>
                                    <option value="Kandal">Kandal – កណ្ដាល</option>
                                    <option value="Koh Kong">Koh Kong – កោះកុង</option>
                                    <option value="Kratie">Kratie – ក្រចេះ</option>
                                    <option value="Mondulkiri">Mondulkiri – មណ្ឌលគិរី</option>
                                    <option value="Oddar Meanchey">Oddar Meanchey – ឧត្តរមានជ័យ</option>
                                    <option value="Pailin">Pailin – ប៉ៃលិន</option>
                                    <option value="Preah Vihear">Preah Vihear – ព្រះវិហារ</option>
                                    <option value="Prey Veng">Prey Veng – ព្រៃវែង</option>
                                    <option value="Pursat">Pursat – ពោធិ៍សាត់</option>
                                    <option value="Ratanakiri">Ratanakiri – រតនគិរី</option>
                                    <option value="Siem Reap">Siem Reap – សៀមរាប</option>
                                    <option value="Preah Sihanouk">Preah Sihanouk – ព្រះសីហនុ</option>
                                    <option value="Stung Treng">Stung Treng – ស្ទឹងត្រែង</option>
                                    <option value="Svay Rieng">Svay Rieng – ស្វាយរៀង</option>
                                    <option value="Takeo">Takeo – តាកែវ</option>
                                    <option value="Tbong Khmum">Tbong Khmum – ត្បូងឃ្មុំ</option>
                                    <option value="Kep">Kep – កែប</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Interested Model <span class="label-khmer">(ម៉ូដែលឡាន)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-car field-icon"></i>
                                <select name="model" class="input-custom" required>
                                    <option value="" disabled selected>Select Model...</option>
                                    <option value="MG ZS">MG ZS</option>
                                    <option value="MG HS">MG HS</option>
                                    <option value="MG5">MG5</option>
                                    <option value="MG4 EV">MG4 EV</option>
                                    <option value="MG Extender">MG Extender</option>
                                    <option value="MG RX5">MG RX5</option>
                                    <option value="MG ZS EV">MG ZS EV</option>
                                    <option value="MG Marvel R">MG Marvel R</option>
                                    <option value="MG7">MG7</option>
                                    <option value="MG Cyberster">MG Cyberster</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-custom">
                            <label class="label-custom">Lead Source <span class="label-khmer">(ប្រភពអតិថិជន)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-bullhorn field-icon"></i>
                                <select name="source" class="input-custom" required>
                                    <option value="" disabled selected>Choose source...</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Website">Website</option>
                                    <option value="Telegram">Telegram</option>
                                    <option value="TikTok">TikTok</option>
                                    <option value="Google Ads">Google Ads</option>
                                    <option value="Email Marketing">Email Marketing</option>
                                    <option value="Showroom Walk-in">Showroom Walk-in</option>
                                    <option value="Event / Exhibition">Event / Exhibition</option>
                                    <option value="Roadshow">Roadshow</option>
                                    <option value="Customer Referral">Customer Referral</option>
                                    <option value="Staff Referral">Staff Referral</option>
                                    <option value="Partner / Dealer">Partner / Dealer</option>
                                    <option value="Phone Call">Phone Call</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group-custom mt-2">
                    <label class="label-custom">Intelligence Remark <span class="label-khmer">(កំណត់ចំណាំលម្អិត)</span></label>
                    <textarea name="comment" class="input-custom" rows="4" style="padding-left: 20px;" placeholder="Add specific needs or follow-up notes..."></textarea>
                </div>

                <button type="submit" class="btn-authorize shadow-lg">AUTHORIZE LEAD RECORD & SYNC</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // ⚡ ១. លក្ខណៈបច្ចេកទេសបិទចន្លោះលេខទូរស័ព្ទ (Auto-spacing)
    $('#contact_info').on('input', function() {
        let n = $(this).val().replace(/[^\d]/g, '');
        if (n.length > 3 && n.length <= 6) n = n.replace(/(\d{3})(\d+)/, '$1 $2');
        else if (n.length > 6) n = n.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        $(this).val(n);
    });

    // ⚡ ២. ឆែកលេខស្ទួន Real-time (AJAX Check)
    $('#contact_info').on('blur', function() {
        let rawPhone = $(this).val().replace(/\s/g, ''); 
        if (rawPhone.length >= 9) {
            $.get("{{ url('/leads/check-duplicate') }}", { phone: rawPhone }, function(res) {
                if (res.duplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'លេខទូរស័ព្ទនេះមានរួចហើយ!',
                        html: `អតិថិជន៖ <b>${res.customer_name}</b><br>ម្ចាស់ Lead៖ <b>${res.sales_person}</b>`,
                        confirmButtonColor: '#4f46e5',
                    });
                    $('#contact_info').css('border-color', '#ef4444').val('');
                } else {
                    $('#contact_info').css('border-color', '#10b981');
                }
            });
        }
    });

    // ⚡ ៣. បង្ហាញ Loading ពេលចុច Submit
    $('#leadForm').on('submit', function() {
        Swal.fire({
            title: 'Authorizing Data...',
            html: 'Please wait while we sync with the master pipeline.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });
});
</script>
@endsection