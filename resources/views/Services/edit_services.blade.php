@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --saas-primary: #4f46e5;
        --saas-primary-hover: #4338ca;
        --saas-danger: #ef4444;
        --saas-danger-hover: #dc2626;
        --saas-success: #10b981;
        --saas-bg: #f8fafc;
        --saas-card-bg: #ffffff;
        --saas-border: #e2e8f0;
        --saas-text-main: #0f172a;
        --saas-text-muted: #64748b;
    }

    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: var(--saas-bg); color: var(--saas-text-main); }
    
    .saas-container { padding: 2rem; max-width: 1600px; margin: 0 auto; }
    
    .saas-card {
        background: var(--saas-card-bg);
        border: 1px solid var(--saas-border);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .saas-card-header {
        background: var(--saas-card-bg);
        border-bottom: 1px solid var(--saas-border);
        padding: 1.25rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .saas-card-body { padding: 2rem; }

    .saas-section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--saas-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .saas-section-title::after {
        content: ""; flex: 1; height: 1px; background: var(--saas-border); margin-left: 10px;
    }

    .saas-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }

    .saas-input {
        border-radius: 10px;
        border: 1px solid var(--saas-border);
        padding: 10px 14px;
        font-weight: 500;
        font-size: 0.95rem;
        color: var(--saas-text-main);
        background-color: #fcfcfd;
        transition: all 0.2s ease-in-out;
        width: 100%;
    }

    .saas-input:focus {
        background-color: #ffffff;
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .saas-select { cursor: pointer; }

    .locked-field {
        background-color: #f1f5f9 !important;
        color: #94a3b8 !important;
        border-style: dashed !important;
        cursor: not-allowed;
    }

    .saas-btn {
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-saas { background: var(--saas-primary); color: white; }
    .btn-primary-saas:hover { background: var(--saas-primary-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); }
    
    .btn-light-saas { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-light-saas:hover { background: #e2e8f0; color: #1e293b; }

    .status-alert {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    .radio-group-saas { display: flex; flex-wrap: wrap; gap: 10px; }
    .radio-group-saas .btn-check:checked + .btn-outline-primary {
        background-color: var(--saas-primary);
        border-color: var(--saas-primary);
        color: white;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
    }
    .radio-group-saas .btn-outline-primary {
        border-radius: 8px;
        border: 1px solid var(--saas-border);
        color: var(--saas-text-muted);
        font-weight: 600;
        background: white;
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    .radio-group-saas .btn-outline-primary:hover { border-color: var(--saas-primary); color: var(--saas-primary); }

    /* Custom Scrollbar for textareas */
    textarea::-webkit-scrollbar { width: 6px; }
    textarea::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>

<div class="saas-container animate__animated animate__fadeIn">
    
    @php 
        $isLocked = (strtolower($booking->status) == 'completed'); 
        
        $models = [
            '🚙 MG SUVs' => ['MG ZS 1.5', 'MG ZS 1.0 / 1.5', 'MG ZS Hybrid', 'MG ZS EV', 'MG ZS32', 'MG HS', 'MG HS 2026 Hybrid', 'MG RX5 2017–2021', 'MG RX5 Reborn / Sport', 'MG RX5 MCE', 'MG RX8', 'MG RX9', 'MG GS 2.0', 'MG D60'],
            '🏎️ Sedan & Sport' => ['MG GT 2024', 'MG GT 2026', 'MG 7 1.5T', 'MG 7 2.0T', 'MG 350', 'MG 550', 'MG 750', 'MG Cyberster'],
            '⚡ EV Models' => ['MG 4 Urban', 'MG 4 (Comfort)', 'MG 4 (Luxury)', 'MG Marvel R'],
            '🚐 Maxus & MIFA' => ['Maxus G50', 'MG G50 PHEV', 'Maxus T60', 'MG T70', 'Maxus T90', 'Maxus V80', 'MIFA 7', 'MIFA 9'],
            '🔋 Leapmotor & IM' => ['IM 5', 'IM 6', 'LPM T03', 'LPM B10', 'LPM C10 (CH)', 'LPM C10 (Global)', 'LPM C16 (Front)', 'LPM C16 (Rear)']
        ];

        $services = [
            'ត្រួតពិនិត្យ និងជួសជុល (Repair)' => [
                'Engine Malfunction' => 'លោតឆេកម៉ាស៊ីន | Engine Check',
                'General Repair' => 'ការជួសជុលទូទៅ | General Repair',
                'General Inspection' => 'ត្រួតពិនិត្យទូទៅ | General Inspection',
                'Check Vibration' => 'ត្រួតពិនិត្យរំញ័រ | Check Vibration',
                'Check Noise' => 'ត្រួតពិនិត្យសំឡេង | Check Noise'
            ],
            'ថែទាំតាមកម្រិត (Maintenance)' => [
                'Interval Maintenance' => 'ថែទាំតាមកាលកំណត់ | Interval Service',
                'Service 20000KM' => 'សេវាកម្ម 20,000 km | Service 20K',
                'Service 40000KM' => 'សេវាកម្ម 40,000 km | Service 40K',
                'Service 50000KM' => 'សេវាកម្ម 50,000 km | Service 50K',
                'Service 60000KM' => 'សេវាកម្ម 60,000 km | Service 60K'
            ],
            'ផ្សេងៗ (Others)' => [
                'Body and Paint' => 'តោន និងបាញ់ថ្នាំ | Body & Paint',
                'Warranty' => 'ការធានាផលិតផល | Warranty'
            ]
        ];
    @endphp

    @if($isLocked)
        <div class="status-alert shadow-sm">
            <i class="fas fa-shield-alt fs-4 text-emerald-600"></i>
            <div>
                <h6 class="mb-1 fw-bold">ឯកសារត្រូវបានចាក់សោរ (Completed)</h6>
                <p class="mb-0" style="font-size: 0.85rem; opacity: 0.9;">ព័ត៌មាននេះត្រូវបានបញ្ជាក់ថាបញ្ចប់រួចរាល់ និងមិនអនុញ្ញាតអោយកែប្រែបានឡើយ ដើម្បីរក្សាសុវត្ថិភាពទិន្នន័យ។</p>
            </div>
        </div>
    @endif

    <div class="saas-card">
        <div class="saas-card-header">
            <div>
                <h5 class="m-0 fw-bold text-slate-800">
                    <i class="fas fa-pen-to-square text-primary me-2"></i> កែសម្រួលព័ត៌មានការកក់
                </h5>
                <p class="text-muted m-0 mt-1" style="font-size: 0.85rem;">លេខស្លាកលេខ៖ <span class="badge bg-light text-dark border ms-1 fs-6">{{ $booking->plate ?? 'N/A' }}</span></p>
            </div>
            <a href="{{ route('services.index') }}" class="saas-btn btn-light-saas">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
        
        <div class="saas-card-body">
            <form action="{{ route('services.update', $booking->id) }}" method="POST" id="editBookingForm">
                @csrf
                <input type="hidden" name="_method" value="POST">
                
                <input type="hidden" name="booking_date" value="{{ $booking->booking_date }}">
                <input type="hidden" name="booking_time" value="{{ $booking->booking_time }}">

                <div class="saas-section-title"><i class="fas fa-user-circle"></i> ព័ត៌មានអតិថិជន & រថយន្ត</div>
                <div class="row g-4 mb-5">
                    <div class="col-lg-4 col-md-6">
                        <label class="saas-label">ឈ្មោះអតិថិជន (Customer Name)</label>
                        <input type="text" name="customer_name" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                               value="{{ old('customer_name', $booking->customer_name) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="saas-label">លេខទូរស័ព្ទ (Contact)</label>
                        <input type="text" name="contact" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                               value="{{ old('contact', $booking->contact) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="saas-label">ស្លាកលេខ (Plate Number)</label>
                        <input type="text" name="plate" class="saas-input text-uppercase {{ $isLocked ? 'locked-field' : '' }}" 
                               value="{{ old('plate', $booking->plate) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <label class="saas-label">ម៉ូឌែលរថយន្ត (Vehicle Model)</label>
                        <select name="model" class="saas-input saas-select {{ $isLocked ? 'locked-field' : '' }}" {{ $isLocked ? 'disabled' : 'required' }}>
                            <option value="" disabled>--- ជ្រើសរើសម៉ូឌែល ---</option>
                            @foreach($models as $group => $cars)
                                <optgroup label="{{ $group }}">
                                    @foreach($cars as $car)
                                        <option value="{{ $car }}" {{ old('model', $booking->model) == $car ? 'selected' : '' }}>{{ $car }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @if($isLocked) <input type="hidden" name="model" value="{{ $booking->model }}"> @endif
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <label class="saas-label">គីឡូម៉ែត្រ (Mileage / ODO)</label>
                        <div class="input-group">
                            <input type="number" name="mileage" class="form-control saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                   value="{{ old('mileage', $booking->mileage) }}" {{ $isLocked ? 'readonly' : '' }} style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <span class="input-group-text bg-light text-muted fw-bold" style="border-color: var(--saas-border); border-top-right-radius: 10px; border-bottom-right-radius: 10px;">KM</span>
                        </div>
                    </div>
                </div>

                <div class="saas-section-title"><i class="fas fa-tools"></i> ព័ត៌មានសេវាកម្ម & ទីតាំង</div>
                <div class="row g-4 mb-5">
                    <div class="col-lg-6">
                        <label class="saas-label">ប្រភេទសេវាកម្ម (Service Type)</label>
                        <select name="service_type" class="saas-input saas-select {{ $isLocked ? 'locked-field' : '' }}" {{ $isLocked ? 'disabled' : 'required' }}>
                            <option value="" disabled>--- ជ្រើសរើសសេវាកម្ម ---</option>
                            @foreach($services as $group => $options)
                                <optgroup label="{{ $group }}">
                                    @foreach($options as $value => $label)
                                        <option value="{{ $value }}" {{ old('service_type', $booking->service_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @if($isLocked) <input type="hidden" name="service_type" value="{{ $booking->service_type }}"> @endif
                    </div>

                    <div class="col-lg-6">
                        <label class="saas-label">ស្ថានភាពការងារ (Job Status)</label>
                        @if($isLocked)
                            <div class="saas-input locked-field fw-bold text-success"><i class="fas fa-check-circle me-2"></i> រួចរាល់ (COMPLETED)</div>
                            <input type="hidden" name="status" value="Completed">
                        @else
                            <select name="status" id="statusSelect" class="saas-input saas-select border-primary" style="background-color: #f5f3ff; color: var(--saas-primary); font-weight: 700;">
                                <optgroup label="ដំណើរការទូទៅ (General Process)">
                                    <option value="Pending" {{ $booking->status == 'Pending' ? 'selected' : '' }}>Pending (កំពុងរង់ចាំ)</option>
                                    <option value="Arrived" {{ $booking->status == 'Arrived' ? 'selected' : '' }}>Arrived (មកដល់ហើយ)</option>
                                    <option value="Waiting Approved" {{ $booking->status == 'Waiting Approved' ? 'selected' : '' }}>កំពុងរង់ចាំការអនុម័ត | Waiting Approved</option>
                                    <option value="Under Diagnosis" {{ $booking->status == 'Under Diagnosis' ? 'selected' : '' }}>កំពុងធ្វើការត្រួតពិនិត្យបញ្ហា | Under Diagnosis</option>
                                    <option value="Waiting Service" {{ $booking->status == 'Waiting Service' ? 'selected' : '' }}>កំពុងរង់ចាំសេវាកម្ម | Waiting Service</option>
                                    <option value="In Progress" {{ strtolower($booking->status) == 'in progress' ? 'selected' : '' }}>កំពុងដំណើរការ | In Progress</option>
                                    <option value="Under Repair" {{ strtolower($booking->status) == 'under repair' ? 'selected' : '' }}>កំពុងជួសជុល | Under Repair</option>
                                </optgroup>
                                <optgroup label="បញ្ចប់ (Completion)">
                                    <option value="Waiting Delivery" {{ $booking->status == 'Waiting Delivery' ? 'selected' : '' }}>កំពុងរង់ចាំការដឹកជញ្ជូន | Waiting Delivery</option>
                                    <option value="Completed" {{ strtolower($booking->status) == 'completed' ? 'selected' : '' }}>បានបញ្ចប់ | Completed</option>
                                    <option value="Cancel" {{ strtolower($booking->status) == 'cancel' ? 'selected' : '' }}>បានបោះបង់ | Cancel</option>
                                </optgroup>
                            </select>
                        @endif
                    </div>

                    <div class="col-12 mt-4">
                        <label class="saas-label">សាខាជួសជុល (Branch Location)</label>
                        <div class="radio-group-saas">
                            @php
                                $branchesList = [
                                    '13' => '🏢 ការិយាល័យកណ្តាល (HQ)',
                                    '11' => '📍 សាខា ៥០ម៉ែត្រ (50M)',
                                    '12' => '📍 សាខា សៀមរាប (SR)',
                                    '10' => '📍 សាខា បាត់ដំបង (BTB)'
                                ];
                            @endphp
                            @foreach($branchesList as $id => $locName)
                                <input type="radio" class="btn-check" name="location" id="loc{{ $id }}" value="{{ $id }}" 
                                       {{ old('location', $booking->location) == $id ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                <label class="btn btn-outline-primary" for="loc{{ $id }}">{{ $locName }}</label>
                            @endforeach
                        </div>
                        @if($isLocked) <input type="hidden" name="location" value="{{ $booking->location }}"> @endif
                    </div>
                </div>

                <div class="saas-section-title"><i class="fas fa-users-cog"></i> ការចាត់តាំង & ពេលវេលា (Staff & Timing)</div>
                <div class="row g-4 mb-5 p-4 rounded-4" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                    <div class="col-md-6 col-lg-3">
                        <label class="saas-label">ទីប្រឹក្សាសេវាកម្ម (SA)</label>
                        @if($isLocked)
                            <input type="text" class="saas-input locked-field" value="{{ $booking->sa_name }}" readonly>
                        @else
                            <select name="sa_name" class="saas-input saas-select">
                                <option value="" {{ empty($booking->sa_name) ? 'selected' : '' }} disabled>--- រើស SA ---</option>
                                @if(isset($serviceAdvisors))
                                    @foreach($serviceAdvisors as $sa)
                                        <option value="{{ $sa }}" {{ old('sa_name', $booking->sa_name) == $sa ? 'selected' : '' }}>
                                            {{ $sa }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label class="saas-label">មេជាង (Technician)</label>
                        @if($isLocked)
                            <input type="text" class="saas-input locked-field" value="{{ $booking->technician_name }}" readonly>
                        @else
                            <select name="technician_name" class="saas-input saas-select">
                                <option value="" {{ empty($booking->technician_name) ? 'selected' : '' }} disabled>--- រើសមេជាង ---</option>
                                @if(isset($technicians))
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech }}" {{ old('technician_name', $booking->technician_name) == $tech ? 'selected' : '' }}>
                                            {{ $tech }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        @endif
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <label class="saas-label">ម៉ោងចាប់ផ្ដើម (Start Time)</label>
                        <input type="time" name="start_time" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                               value="{{ $booking->start_time }}" {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="saas-label">ម៉ោងបញ្ចប់ (End Time)</label>
                        <input type="time" name="end_time" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                               value="{{ $booking->end_time }}" {{ $isLocked ? 'readonly' : '' }}>
                    </div>
                </div>

                <div class="saas-section-title"><i class="fas fa-comment-alt"></i> កំណត់ចំណាំ (Remarks)</div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="saas-label text-warning">សំណូមពរអតិថិជន (Customer Remark)</label>
                        <textarea name="remark" class="saas-input" rows="3" placeholder="វាយបញ្ចូលសំណូមពរអតិថិជននៅទីនេះ..." {{ $isLocked ? 'readonly' : '' }} style="resize: none;">{{ old('remark', $booking->remark) }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="saas-label text-primary">ចំណាំផ្ទៃក្នុង (Admin Note)</label>
                        <textarea name="note" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" rows="3" placeholder="វាយបញ្ចូលកំណត់ចំណាំសម្រាប់បុគ្គលិក..." {{ $isLocked ? 'readonly' : '' }} style="resize: none;">{{ old('note', $booking->note) }}</textarea>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3 align-items-center">
                    <a href="{{ route('services.index') }}" class="text-muted fw-bold text-decoration-none me-3" style="font-size: 0.95rem; transition: color 0.2s;">
                        បោះបង់ (Cancel)
                    </a>
                    @if(!$isLocked)
                        <button type="submit" class="saas-btn btn-primary-saas">
                            <i class="fas fa-cloud-arrow-up"></i> Update & Save Records
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('statusSelect');
        
        // Validation: If status is Completed, require staff & timing
        if (statusSelect && statusSelect.value === 'Completed') {
            const sa = document.querySelector('select[name="sa_name"]').value;
            const tech = document.querySelector('select[name="technician_name"]').value;
            const start = document.getElementsByName('start_time')[0].value;
            const end = document.getElementsByName('end_time')[0].value;

            if (!sa || !tech || !start || !end) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'ព័ត៌មានមិនទាន់គ្រប់គ្រាន់!',
                    text: 'សូមជ្រើសរើសឈ្មោះ SA, មេជាង និងម៉ោងជួសជុលឱ្យបានត្រឹមត្រូវ មុននឹងបិទ Job (Completed)!',
                    confirmButtonColor: '#ef4444',
                    customClass: { popup: 'rounded-4' }
                });
                return;
            }
        }

        e.preventDefault();
        Swal.fire({
            title: 'បញ្ជាក់ការរក្សាទុក?',
            text: statusSelect && statusSelect.value === 'Completed' ? "ប្រយ័ត្ន៖ ទិន្នន័យនឹងត្រូវចាក់សោរមិនឱ្យកែប្រែក្រោយពេលប្តូរទៅ Completed!" : "តើអ្នកចង់រក្សាទុកការកែប្រែនេះមែនទេ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'បាទ, រក្សាទុក',
            cancelButtonText: 'ពិនិត្យឡើងវិញ',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) { 
                Swal.fire({ title: 'កំពុងរក្សាទុក...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                this.submit(); 
            }
        });
    });
</script>
@endsection