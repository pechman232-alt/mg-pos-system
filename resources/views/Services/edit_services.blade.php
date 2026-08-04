@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<style>
    :root {
        --saas-primary: #4f46e5;
        --saas-primary-hover: #4338ca;
        --saas-danger: #ef4444;
        --saas-success: #10b981;
        --saas-bg: #f8fafc;
        --saas-card-bg: #ffffff;
        --saas-border: #e2e8f0;
        --saas-text-main: #0f172a;
        --saas-text-muted: #64748b;
        --saas-input-bg: #f8fafc;
    }

    /* 🌟 COMPACT & FULL-WIDTH FIT SCREEN LAYOUT 🌟 */
    body { 
        margin: 0; padding: 0;
        font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; 
        background-color: var(--saas-bg) !important; 
        color: var(--saas-text-main); 
        overflow: hidden !important; 
    }
    
    .content-fluid-wrapper { 
        height: 100vh; 
        max-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* 🌟 Sticky Compact Header 🌟 */
    .saas-header-fixed {
        background: var(--saas-card-bg);
        border-bottom: 1px solid var(--saas-border);
        padding: 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        z-index: 50;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    /* 🌟 Main Scrollable Area (Full Width Expansion) 🌟 */
    .saas-scroll-area {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1rem 1.5rem;
        padding-bottom: 2rem;
        background-color: var(--saas-bg);
    }
    
    .saas-scroll-area::-webkit-scrollbar { width: 5px; }
    .saas-scroll-area::-webkit-scrollbar-track { background: transparent; }
    .saas-scroll-area::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .saas-scroll-area::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* 🌟 Full Width Container (លាតពេញអេក្រង់) 🌟 */
    .saas-form-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }
    
    .saas-main-card {
        background: var(--saas-card-bg);
        border: 1px solid var(--saas-border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .saas-card-body { 
        padding: 1.25rem; 
    }

    /* 🌟 Compact Inner Sections 🌟 */
    .saas-inner-section {
        background: #ffffff;
        border: 1px solid var(--saas-border);
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    .saas-inner-section:hover {
        border-color: #cbd5e1;
    }

    .saas-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--saas-text-main);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .saas-section-title i {
        background: #e0e7ff;
        color: var(--saas-primary);
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    .saas-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 4px;
        display: block;
    }

    .saas-input {
        border-radius: 8px;
        border: 1px solid var(--saas-border);
        padding: 6px 12px;
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--saas-text-main);
        background-color: var(--saas-input-bg);
        transition: all 0.2s ease-in-out;
        width: 100%;
        height: 38px;
    }

    .saas-input:focus {
        background-color: #ffffff;
        border-color: var(--saas-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }
    
    textarea.saas-input { height: auto; }
    .saas-select { cursor: pointer; }

    .locked-field {
        background-color: #f1f5f9 !important;
        color: #64748b !important;
        border-style: dashed !important;
        cursor: not-allowed;
    }

    /* 🌟 Buttons 🌟 */
    .saas-btn {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-primary-saas { background: var(--saas-primary); color: white; }
    .btn-primary-saas:hover { background: var(--saas-primary-hover); color: white; }
    
    .btn-light-saas { background: #f1f5f9; color: #475569; border: 1px solid var(--saas-border); }
    .btn-light-saas:hover { background: #e2e8f0; color: #1e293b; }

    .status-alert {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .radio-group-saas { display: flex; flex-wrap: wrap; gap: 8px; }
    .radio-group-saas .btn-check:checked + .btn-outline-primary {
        background-color: var(--saas-primary);
        border-color: var(--saas-primary);
        color: white;
    }
    .radio-group-saas .btn-outline-primary {
        border-radius: 6px;
        border: 1px solid var(--saas-border);
        color: var(--saas-text-muted);
        font-weight: 600;
        background: white;
        padding: 6px 14px;
        font-size: 0.82rem;
        transition: all 0.2s;
    }
    .radio-group-saas .btn-outline-primary:hover { 
        border-color: var(--saas-primary); color: var(--saas-primary); background-color: #f5f3ff;
    }
</style>

<!-- 🌟 FULL SCREEN FLUID WRAPPER 🌟 -->
<div class="content-fluid-wrapper animate__animated animate__fadeIn">

    <!-- 🌟 Fixed Header 🌟 -->
    <div class="saas-header-fixed">
        <div>
            <h5 class="m-0 fw-bold text-slate-800 d-flex align-items-center gap-2">
                <i class="fas fa-pen-to-square text-primary"></i> កែសម្រួលការកក់ (Edit Booking)
            </h5>
            <p class="text-muted m-0 mt-1" style="font-size: 0.8rem;">
                ស្លាកលេខ៖ <span class="badge bg-light text-dark border px-2 py-1 fs-6 rounded-2">{{ $booking->plate ?? 'N/A' }}</span>
            </p>
        </div>
        <a href="{{ route('services.index') }}" class="saas-btn btn-light-saas">
            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
        </a>
    </div>

    <!-- 🌟 Scrollable Form Area 🌟 -->
    <div class="saas-scroll-area">
        <div class="saas-form-container">
            
            @if($isLocked)
                <div class="status-alert shadow-sm animate__animated animate__fadeInDown">
                    <i class="fas fa-shield-check fs-4 text-emerald-600"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">ឯកសារត្រូវបានចាក់សោរ (Completed)</h6>
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">ព័ត៌មាននេះត្រូវបានបញ្ជាក់ថាបញ្ចប់រួចរាល់ និងមិនអនុញ្ញាតអោយកែប្រែបានឡើយ ដើម្បីរក្សាសុវត្ថិភាពទិន្នន័យ។</p>
                    </div>
                </div>
            @endif

            <div class="saas-main-card">
                <div class="saas-card-body">
                    <form action="{{ route('services.update', $booking->id) }}" method="POST" id="editBookingForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_method" value="POST">
                        
                        <!-- ផ្នែកទី ១៖ ព័ត៌មានអតិថិជន និងរថយន្ត -->
                        <div class="saas-inner-section">
                            <div class="saas-section-title">
                                <i class="fas fa-user"></i> ព័ត៌មានអតិថិជន & រថយន្ត
                            </div>
                            <div class="row g-3">
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <label class="saas-label">ឈ្មោះអតិថិជន (Name)</label>
                                    <input type="text" name="customer_name" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                           value="{{ old('customer_name', $booking->customer_name) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                                </div>
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <label class="saas-label">លេខទូរស័ព្ទ (Contact)</label>
                                    <input type="text" name="contact" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                           value="{{ old('contact', $booking->contact) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                                </div>
                                <div class="col-xl-2 col-lg-4 col-md-6">
                                    <label class="saas-label">ស្លាកលេខ (Plate)</label>
                                    <input type="text" name="plate" class="saas-input text-uppercase {{ $isLocked ? 'locked-field' : '' }}" 
                                           value="{{ old('plate', $booking->plate) }}" {{ $isLocked ? 'readonly' : 'required' }}>
                                </div>
                                <div class="col-xl-2 col-lg-6 col-md-6">
                                    <label class="saas-label">ម៉ូឌែល (Model)</label>
                                    <select name="model" class="saas-input saas-select {{ $isLocked ? 'locked-field' : '' }}" {{ $isLocked ? 'disabled' : 'required' }}>
                                        <option value="" disabled>--- ជ្រើសរើស ---</option>
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
                                <div class="col-xl-2 col-lg-6 col-md-12">
                                    <label class="saas-label">គីឡូម៉ែត្រ (ODO)</label>
                                    <div class="input-group">
                                        <input type="number" name="mileage" class="form-control saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                               value="{{ old('mileage', $booking->mileage) }}" {{ $isLocked ? 'readonly' : '' }} style="border-top-right-radius: 0; border-bottom-right-radius: 0; height: 38px;">
                                        <span class="input-group-text bg-light text-muted fw-bold text-xs" style="border-color: var(--saas-border); border-top-right-radius: 8px; border-bottom-right-radius: 8px;">KM</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ផ្នែកទី ២៖ ព័ត៌មានសេវាកម្ម និងទីតាំង -->
                        <div class="saas-inner-section">
                            <div class="saas-section-title">
                                <i class="fas fa-wrench"></i> ព័ត៌មានសេវាកម្ម & ទីតាំង
                            </div>
                            <div class="row g-3">
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
                                        <div class="saas-input locked-field fw-bold text-success d-flex align-items-center gap-2">
                                            <i class="fas fa-check-circle"></i> រួចរាល់ (COMPLETED)
                                        </div>
                                        <input type="hidden" name="status" value="Completed">
                                    @else
                                        <select name="status" id="statusSelect" class="saas-input saas-select" style="background-color: #f5f3ff; border-color: #8b5cf6; color: #6d28d9; font-weight: 700;">
                                            <optgroup label="ដំណើរការទូទៅ (General Process)">
                                                <option value="Pending" {{ $booking->status == 'Pending' ? 'selected' : '' }}>Pending (កំពុងរង់ចាំ)</option>
                                                <option value="Arrived" {{ $booking->status == 'Arrived' ? 'selected' : '' }}>Arrived (មកដល់ហើយ)</option>
                                                <option value="Waiting Approved" {{ $booking->status == 'Waiting Approved' ? 'selected' : '' }}>Waiting Approved</option>
                                                <option value="Under Diagnosis" {{ $booking->status == 'Under Diagnosis' ? 'selected' : '' }}>Under Diagnosis</option>
                                                <option value="Waiting Service" {{ $booking->status == 'Waiting Service' ? 'selected' : '' }}>Waiting Service</option>
                                                <option value="In Progress" {{ strtolower($booking->status) == 'in progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Under Repair" {{ strtolower($booking->status) == 'under repair' ? 'selected' : '' }}>Under Repair</option>
                                            </optgroup>
                                            <optgroup label="បញ្ចប់ (Completion)">
                                                <option value="Waiting Delivery" {{ $booking->status == 'Waiting Delivery' ? 'selected' : '' }}>Waiting Delivery</option>
                                                <option value="Completed" {{ strtolower($booking->status) == 'completed' ? 'selected' : '' }}>Completed (បានបញ្ចប់)</option>
                                                <option value="Cancel" {{ strtolower($booking->status) == 'cancel' ? 'selected' : '' }}>Cancel (បានបោះបង់)</option>
                                            </optgroup>
                                        </select>
                                    @endif
                                </div>

                                <div class="col-12 mt-2">
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
                        </div>

                        <!-- ផ្នែកទី ៣៖ ការចាត់តាំង & ពេលវេលា -->
                        <div class="saas-inner-section">
                            <div class="saas-section-title">
                                <i class="fas fa-clock"></i> ការចាត់តាំង & ពេលវេលា (Staff & Timing)
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label class="saas-label">កាលបរិច្ឆេទកក់ (Booking Date)</label>
                                    <input type="date" name="booking_date" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                           value="{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') : '' }}" {{ $isLocked ? 'readonly' : 'required' }}>
                                </div>

                                <div class="col-md-6 col-lg-3">
                                    <label class="saas-label">ម៉ោងកក់ (Booking Time)</label>
                                    <input type="time" name="booking_time" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" 
                                           value="{{ $booking->booking_time }}" {{ $isLocked ? 'readonly' : 'required' }}>
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

                                <div class="col-md-6 col-lg-4">
                                    <label class="saas-label">ទីប្រឹក្សាសេវាកម្ម (SA)</label>
                                    @if($isLocked)
                                        <input type="text" class="saas-input locked-field" value="{{ $booking->sa_name }}" readonly>
                                    @else
                                        <select name="sa_name" class="saas-input saas-select">
                                            <option value="" {{ empty($booking->sa_name) ? 'selected' : '' }} disabled>--- រើស SA ---</option>
                                            @if(isset($serviceAdvisors))
                                                @foreach($serviceAdvisors as $sa)
                                                    <option value="{{ $sa }}" {{ old('sa_name', $booking->sa_name) == $sa ? 'selected' : '' }}>{{ $sa }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </div>

                                <div class="col-md-6 col-lg-4">
                                    <label class="saas-label">មេជាង (Technician)</label>
                                    @if($isLocked)
                                        <input type="text" class="saas-input locked-field" value="{{ $booking->technician_name }}" readonly>
                                    @else
                                        <select name="technician_name" class="saas-input saas-select">
                                            <option value="" {{ empty($booking->technician_name) ? 'selected' : '' }} disabled>--- រើសមេជាង ---</option>
                                            @if(isset($technicians))
                                                @foreach($technicians as $tech)
                                                    <option value="{{ $tech }}" {{ old('technician_name', $booking->technician_name) == $tech ? 'selected' : '' }}>{{ $tech }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </div>
                                
                                <div class="col-md-12 col-lg-4">
                                    <label class="saas-label">ថ្ងៃបង្កើតកំណត់ត្រា (Created At)</label>
                                    <input type="text" class="saas-input locked-field text-muted" 
                                           value="{{ $booking->created_at ? \Carbon\Carbon::parse($booking->created_at)->format('d-M-Y h:i A') : '' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- ផ្នែកទី ៤៖ កំណត់ចំណាំ -->
                        <div class="saas-inner-section mb-2">
                            <div class="saas-section-title">
                                <i class="fas fa-comment-dots"></i> កំណត់ចំណាំ (Remarks)
                            </div>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="saas-label text-warning">សំណូមពរអតិថិជន (Customer Remark)</label>
                                    <textarea name="remark" class="saas-input" rows="2" placeholder="វាយបញ្ចូលសំណូមពរអតិថិជននៅទីនេះ..." {{ $isLocked ? 'readonly' : '' }} style="resize: none;">{{ old('remark', $booking->remark) }}</textarea>
                                </div>
                                <div class="col-lg-6">
                                    <label class="saas-label text-primary">ចំណាំផ្ទៃក្នុង (Admin Note)</label>
                                    <textarea name="note" class="saas-input {{ $isLocked ? 'locked-field' : '' }}" rows="2" placeholder="វាយបញ្ចូលកំណត់ចំណាំសម្រាប់បុគ្គលិក..." {{ $isLocked ? 'readonly' : '' }} style="resize: none;">{{ old('note', $booking->note) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 align-items-center mt-3">
                            <a href="{{ route('services.index') }}" class="text-muted fw-bold text-decoration-none px-3 py-2" style="font-size: 0.88rem;">
                                បោះបង់ (Cancel)
                            </a>
                            @if(!$isLocked)
                                <button type="submit" class="saas-btn btn-primary-saas">
                                    <i class="fas fa-check-circle"></i> រក្សាទុកការកែប្រែ (Save Changes)
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- End Scroll Area -->
</div> <!-- End Fluid Wrapper -->

<script>
    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('statusSelect');
        
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