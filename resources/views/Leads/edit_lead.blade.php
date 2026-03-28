@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root {
        --brand-1: #0f172a; --brand-2: #1e3a8a; --brand-3: #3730a3; --brand-4: #1e293b;
        --bg-soft: #f8fafc; --slate-400: #94a3b8;
    }
    body { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; background-color: var(--bg-soft); color: #1e293b; -webkit-font-smoothing: antialiased; font-size: 0.8rem; }
    .container-full { width: 100% !important; max-width: 100% !important; padding: 25px 40px !important; }
    
    .card-edit { border-radius: 24px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.06); background: #fff; overflow: hidden; margin-bottom: 50px; }
    
    .card-header-premium { 
        background: linear-gradient(90deg, var(--brand-1) 0%, var(--brand-2) 33%, var(--brand-3) 66%, var(--brand-4) 100%) !important; 
        padding: 30px 40px; color: #fff; display: flex; justify-content: space-between; align-items: center; border: none;
    }
    
    .section-title { font-weight: 800; color: var(--brand-2); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1.5px; margin: 35px 0 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; display: flex; align-items: center; }
    .label-kh { font-family: 'Noto Sans Khmer', sans-serif; color: var(--brand-2); font-size: 0.65rem; margin-left: 5px; font-weight: 400; }
    
    .field-wrapper { position: relative; margin-bottom: 18px; }
    .label-custom { font-weight: 700; color: #64748b; font-size: 0.68rem; text-transform: uppercase; margin-bottom: 6px; display: block; }
    
    .input-custom { 
        width: 100%; padding: 12px 15px 12px 45px; background: #fdfdfd; border: 1.5px solid #e2e8f0; border-radius: 12px; 
        font-weight: 600; font-size: 0.85rem; transition: 0.3s; color: #1e293b;
    }
    .input-custom:focus { border-color: var(--brand-2); outline: none; box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.05); background: #fff; }
    
    .field-icon { position: absolute; left: 16px; top: 38px; color: var(--slate-400); font-size: 0.9rem; z-index: 5; }
    
    .stage-card { background: #f8fafc; padding: 20px; border-radius: 18px; border: 1.5px solid #eef2f6; transition: 0.3s; position: relative; height: 100%; }
    .stage-badge { position: absolute; top: -10px; left: 20px; background: var(--brand-2); color: #fff; padding: 2px 14px; border-radius: 50px; font-size: 10px; font-weight: 800; }
    
    .btn-update { background: linear-gradient(90deg, var(--brand-2), var(--brand-3)); color: #fff; padding: 18px; border: none; border-radius: 16px; font-weight: 800; font-size: 0.95rem; text-transform: uppercase; width: 100%; margin-top: 40px; cursor: pointer; transition: 0.3s; }
    .btn-update:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2); filter: brightness(1.1); }
</style>

<div class="container-full">
    <div class="card-edit">
        <div class="card-header-premium">
            <div>
                <h4 class="m-0 text-white fw-800 text-uppercase"><i class="fas fa-edit me-2"></i> Update Lead Master Record</h4>
                <p class="m-0 small text-white opacity-75 mt-1">Modifying File: #{{ $lead->id }} | Customer: {{ $lead->customer_name }}</p>
            </div>
            <a href="{{ route('leads.index') }}" class="btn-cancel text-white text-decoration-none border p-2 rounded-pill px-4" style="background: rgba(255,255,255,0.1); font-size: 0.75rem; font-weight: 700;">CANCEL & EXIT (បោះបង់)</a>
        </div>

        <div class="card-body p-5">
            <form action="{{ route('leads.update', $lead->id) }}" method="POST" id="editLeadForm">
                @csrf
                @method('PUT')

                <div class="section-title"><i class="fas fa-id-badge"></i> 01. Consultant & Branch Attribution <span class="label-kh">(ការកំណត់សាខា និងអ្នកលក់)</span></div>
                <div class="row g-4">
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Sales Consultant <span class="label-kh">(ទីប្រឹក្សាលក់)</span></label>
                        <i class="fas fa-user-tie field-icon"></i>
                        <select name="sales_person" class="input-custom" required>
                            @foreach($staff as $name)
                                <option value="{{ $name }}" {{ $lead->sales_person == $name ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Operating Branch <span class="label-kh">(សាខា)</span></label>
                        <i class="fas fa-building field-icon"></i>
                        <select name="branch" class="input-custom" required>
                            <option value="សាខាសៀមរាប" {{ $lead->branch == 'សាខាសៀមរាប' ? 'selected' : '' }}>សាខាសៀមរាប (ខេត្តសៀមរាប)</option>
                            <option value="សាខាបាត់ដំបង" {{ $lead->branch == 'សាខាបាត់ដំបង' ? 'selected' : '' }}>សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)</option>
                            <option value="សាខាការិយាល័យកណ្តាល" {{ $lead->branch == 'សាខាការិយាល័យកណ្តាល' ? 'selected' : '' }}>សាខាការិយាល័យកណ្តាលភ្មំពេញ (រាជធានីភ្មំពេញ)</option>
                            <option value="សាខាផ្លូវលេខ ៦០ម៉ែត្រ" {{ $lead->branch == 'សាខាផ្លូវលេខ ៦០ម៉ែត្រ' ? 'selected' : '' }}>សាខាផ្លូវលេខ ៦០ម៉ែត្រ (រាជធានីភ្មំពេញ)</option>
                        </select>
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Registration Date <span class="label-kh">(ថ្ងៃចុះឈ្មោះ)</span></label>
                        <i class="fas fa-calendar-alt field-icon"></i>
                        <input type="date" name="date" class="input-custom" value="{{ \Carbon\Carbon::parse($lead->date)->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="section-title"><i class="fas fa-signal"></i> 02. Lead Lifecycle Status <span class="label-kh">(ស្ថានភាពអតិថិជន និងព័ត៌មានទំនាក់ទំនង)</span></div>
                <div class="row g-4">
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Lead Status <span class="label-kh">(ស្ថានភាពអតិថិជន)</span></label>
                        <i class="fas fa-layer-group field-icon"></i>
                        <select name="status" class="input-custom" required>
                            <optgroup label="⚪ Top of Funnel">
                                <option value="captured" {{ $lead->status == 'captured' ? 'selected' : '' }}>Lead Captured (ទទួលបានអតិថិជនថ្មី)</option>
                                <option value="assigned" {{ $lead->status == 'assigned' ? 'selected' : '' }}>Lead Assigned (ចាត់ចែងជូនបុគ្គលិក)</option>
                                <option value="initial_contact" {{ $lead->status == 'initial_contact' ? 'selected' : '' }}>Initial Contact (ទំនាក់ទំនងដំបូង)</option>
                            </optgroup>
                            <optgroup label="🔵 Active Engagement">
                                <option value="engaged" {{ $lead->status == 'engaged' ? 'selected' : '' }}>Engaged (អតិថិជនឆ្លើយតប)</option>
                                <option value="needs_assessment" {{ $lead->status == 'needs_assessment' ? 'selected' : '' }}>Needs Assessment (វាយតម្លៃតម្រូវការ)</option>
                                <option value="qualified" {{ $lead->status == 'qualified' ? 'selected' : '' }}>Qualified Lead (អតិថិជនមានសក្តានុពល)</option>
                            </optgroup>
                            <optgroup label="🟠 Commitment Stage">
                                <option value="appointment" {{ $lead->status == 'appointment' ? 'selected' : '' }}>Appointment Scheduled (កំណត់ពេលជួប)</option>
                                <option value="test_drive_done" {{ $lead->status == 'test_drive_done' ? 'selected' : '' }}>Test Drive Completed (សាកល្បងបើកបរ)</option>
                                <option value="proposal" {{ $lead->status == 'proposal' ? 'selected' : '' }}>Proposal Presented (បង្ហាញតម្លៃ/ផ្តល់ជូន)</option>
                                <option value="negotiation" {{ $lead->status == 'negotiation' ? 'selected' : '' }}>Negotiation (ចរចារ)</option>
                                <option value="booking" {{ $lead->status == 'booking' ? 'selected' : '' }}>Booking / Deposit (កក់ / កក់ប្រាក់)</option>
                            </optgroup>
                            <optgroup label="💎 Closing Stage">
                                <option value="processing" {{ $lead->status == 'processing' ? 'selected' : '' }}>Document Processing (ដំណើរការឯកសារ)</option>
                                <option value="won" {{ $lead->status == 'won' ? 'selected' : '' }}>Closed Won (លក់បានជោគជ័យ ✅)</option>
                                <option value="lost" {{ $lead->status == 'lost' ? 'selected' : '' }}>Closed Lost (បាត់បង់ ❌)</option>
                                <option value="nurturing" {{ $lead->status == 'nurturing' ? 'selected' : '' }}>Follow-up / Nurturing (តាមដានបន្ត)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Full Name <span class="label-kh">(ឈ្មោះពេញ)</span></label>
                        <i class="fas fa-user field-icon"></i>
                        <input type="text" name="customer_name" class="input-custom" value="{{ $lead->customer_name }}" required>
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Primary Contact <span class="label-kh">(លេខទូរស័ព្ទ)</span></label>
                        <i class="fas fa-phone-alt field-icon"></i>
                        <input type="text" name="contact_info" id="phoneInput" class="input-custom" value="{{ $lead->contact_info }}" required>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Telegram ID <span class="label-kh">(តេឡេក្រាម)</span></label>
                        <i class="fab fa-telegram-plane field-icon"></i>
                        <input type="text" name="telegram" class="input-custom" value="{{ $lead->telegram }}" placeholder="@username">
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Province / City <span class="label-kh">(ខេត្ត/ក្រុង)</span></label>
                        <i class="fas fa-city field-icon"></i>
                        <select name="location" class="input-custom">
                            @foreach(['Phnom Penh', 'Siem Reap', 'Battambang', 'Kandal', 'Preah Sihanouk', 'Kampong Cham', 'Takeo', 'Kampot', 'Kratie', 'Mondulkiri'] as $prov)
                                <option value="{{ $prov }}" {{ $lead->location == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 field-wrapper">
                        <label class="label-custom">Lead Source <span class="label-kh">(ប្រភពអតិថិជន)</span></label>
                        <i class="fas fa-bullhorn field-icon"></i>
                        <select name="source" class="input-custom">
                            @foreach(['Facebook Page', 'TikTok', 'Walk-in', 'Referral', 'Roadshow'] as $src)
                                <option value="{{ $src }}" {{ $lead->source == $src ? 'selected' : '' }}>{{ $src }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="section-title"><i class="fas fa-car-side"></i> 03. Asset Interest & Deal Framework <span class="label-kh">(ម៉ូដែល និងហិរញ្ញវត្ថុ)</span></div>
                <div class="row g-4">
                    <div class="col-md-3 field-wrapper">
                        <label class="label-custom">Interested Model <span class="label-kh">(ម៉ូដែលឡាន)</span></label>
                        <i class="fas fa-car field-icon"></i>
                        <select name="model" class="input-custom" required>
                            @foreach(['MG ZS', 'MG HS', 'MG RX5', 'MG RX8', 'MG GT', 'MG7', 'MG HS PHEV'] as $model)
                                <option value="{{ $model }}" {{ $lead->model == $model ? 'selected' : '' }}>{{ $model }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 field-wrapper">
                        <label class="label-custom">Preferred Color <span class="label-kh">(ពណ៌)</span></label>
                        <i class="fas fa-palette field-icon"></i>
                        <input type="text" name="color" class="input-custom" value="{{ $lead->color }}">
                    </div>
                    <div class="col-md-2 field-wrapper">
                        <label class="label-custom">Down Payment (%) <span class="label-kh">(ប្រាក់កក់ %)</span></label>
                        <i class="fas fa-percent field-icon"></i>
                        <select name="dp_percent" class="input-custom">
                            @foreach([0, 10, 15, 20, 25, 30, 40, 50] as $p)
                                <option value="{{ $p }}" {{ $lead->dp_percent == $p ? 'selected' : '' }}>{{ $p }}% @if($p==0) (Full Loan) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 field-wrapper">
                        <label class="label-custom">Monthly Budget <span class="label-kh">(បង់ប្រចាំខែ)</span></label>
                        <i class="fas fa-coins field-icon"></i>
                        <input type="number" step="0.01" name="mp" class="input-custom text-success fw-bold" value="{{ $lead->mp }}">
                    </div>
                    <div class="col-md-2 field-wrapper">
                        <label class="label-custom">Loan Period <span class="label-kh">(រយៈពេលបង់)</span></label>
                        <i class="fas fa-history field-icon"></i>
                        <select name="lp" class="input-custom">
                            <option value="0" {{ $lead->lp == 0 ? 'selected' : '' }}>CASH / NA</option>
                            @for($i = 12; $i <= 84; $i += 12)
                                <option value="{{ $i }}" {{ $lead->lp == $i ? 'selected' : '' }}>{{ $i }} Months ({{ $i/12 }} ឆ្នាំ)</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="section-title"><i class="fas fa-road"></i> 04. Phase Milestone Timeline <span class="label-kh">(កាលវិភាគតាមដានអតិថិជន)</span></div>
                <div class="row g-4">
                    @for($i = 2; $i <= 9; $i++)
                    <div class="col-md-3">
                        <div class="stage-card shadow-sm text-center">
                            <span class="stage-badge">PHASE 0{{ $i }}</span>
                            <div class="mb-2">
                                <label class="label-custom text-primary" style="font-size: 10px;">Follow-up Date</label>
                                <input type="date" name="date{{ $i }}" class="form-control form-control-sm border-0 bg-white text-center shadow-none" 
                                    value="{{ $lead->{'date'.$i} ? \Carbon\Carbon::parse($lead->{'date'.$i})->format('Y-m-d') : '' }}">
                            </div>
                            <textarea name="comment{{ $i }}" class="form-control border-0 bg-white text-center shadow-none" rows="2" style="font-size: 0.75rem; resize: none;" placeholder="Notes...">{{ $lead->{'comment'.$i} }}</textarea>
                        </div>
                    </div>
                    @endfor
                </div>

                <div class="section-title"><i class="fas fa-brain"></i> 05. Qualitative Intelligence <span class="label-kh">(កំណត់ចំណាំស៊ីជម្រៅ)</span></div>
                <div class="row g-4">
                    <div class="col-md-6 field-wrapper">
                        <label class="label-custom">Main Concern <span class="label-kh">(ក្តីបារម្ភអតិថិជន)</span></label>
                        <i class="fas fa-exclamation-triangle field-icon"></i>
                        <input type="text" name="concern" class="input-custom" value="{{ $lead->concern }}" placeholder="What makes them hesitant?">
                    </div>
                    <div class="col-md-6 field-wrapper">
                        <label class="label-custom">Competitor Comparison <span class="label-kh">(គូប្រជែង)</span></label>
                        <i class="fas fa-shield-alt field-icon"></i>
                        <input type="text" name="competition" class="input-custom" value="{{ $lead->competition }}" placeholder="Comparing with other brands?">
                    </div>
                    <div class="col-12 field-wrapper">
                        <label class="label-custom">Final Executive Remarks <span class="label-kh">(កំណត់ចំណាំចុងក្រោយ)</span></label>
                        <i class="fas fa-comment-dots field-icon"></i>
                        <textarea name="comment" class="input-custom" rows="3" style="padding-left: 45px;" placeholder="Strategy for closing this lead...">{{ $lead->comment }}</textarea>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="btn-update shadow-lg">
                    COMMIT & SYNCHRONIZE RECORD <span class="label-kh" style="color:white">(រក្សាទុកទិន្នន័យដែលបានកែប្រែ)</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Phone Formatter
    $('#phoneInput').on('input', function() {
        let n = $(this).val().replace(/[^\d]/g, '');
        if (n.length > 3 && n.length <= 6) n = n.replace(/(\d{3})(\d+)/, '$1 $2');
        else if (n.length > 6) n = n.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        $(this).val(n);
    });

    // Form Submit handling
    $('#editLeadForm').on('submit', function() {
        Swal.fire({
            title: 'Synchronizing Database...',
            text: 'សូមរង់ចាំ ខណៈពេលដែលប្រព័ន្ធកំពុងរក្សាទុកការកែប្រែ។',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Update Successful!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif
</script>
@endsection