@extends('layouts.homePages')

@section('content')

@php
    $authUser = auth('users')->user() ?? auth()->user();
    $roleId = (int)($authUser->role_id ?? 0);
    $isManager = in_array($roleId, [1, 6, 11]);
    $userBranchId = $authUser->branch_id ?? null;
@endphp

<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;500;600&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*, *::before, *::after { box-sizing: border-box; }

:root {
    --blue:        #002663;
    --blue-hover:  #001a45;
    --blue-light:  #EFF6FF;
    --blue-mid:    #BFDBFE;
    --red:         #B70101;
    --red-light:   #FEF2F2;
    --dark:        #0F172A;
    --mid:         #475569;
    --soft:        #94A3B8;
    --bg:          #F1F5F9;
    --white:       #ffffff;
    --border:      #E2E8F0;
    --green:       #059669;
    --green-bg:    #ECFDF5;
    --font:        'DM Sans', 'Noto Sans Khmer', sans-serif;
}

body,
.content-wrapper,
.main-content {
    font-family: var(--font) !important;
    background-color: var(--bg) !important;
}

.container-fluid { padding: 28px 28px 40px !important; }

/* ── Page Header ── */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.page-header-left { display: flex; align-items: center; gap: 14px; }
.page-icon {
    width: 42px; height: 42px;
    background: var(--blue);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.page-icon i { color: #fff; font-size: 1rem; }
.page-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
    letter-spacing: -0.3px;
}
.page-subtitle {
    font-size: 12px;
    color: var(--soft);
    margin-top: 2px;
    font-weight: 400;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--white);
    border: 0.5px solid var(--border);
    color: var(--mid);
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    font-family: var(--font);
    transition: all 0.15s;
}
.btn-back:hover {
    background: var(--bg);
    color: var(--dark);
    border-color: #cbd5e1;
}

/* ── Card ── */
.card {
    background: var(--white);
    border: 0.5px solid var(--border) !important;
    border-radius: 12px !important;
    box-shadow: none !important;
    margin-bottom: 16px;
    overflow: hidden;
}
.card-header {
    background: var(--white) !important;
    border-bottom: 0.5px solid var(--border) !important;
    padding: 14px 20px !important;
    display: flex;
    align-items: center;
    gap: 10px;
}
.card-header-icon {
    width: 28px; height: 28px;
    background: var(--blue-light);
    border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
}
.card-header-icon i { color: var(--blue); font-size: 12px; }
.card-header-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--dark);
    text-transform: uppercase;
    letter-spacing: 0.7px;
    margin: 0;
}
.card-body { padding: 20px !important; }

/* ── Form Controls ── */
.field { display: flex; flex-direction: column; gap: 5px; }
.field-label {
    font-size: 11px;
    font-weight: 500;
    color: var(--soft);
    letter-spacing: 0.3px;
}
.field-label .req { color: var(--red); }

.field input,
.field select,
.field textarea {
    font-family: var(--font);
    font-size: 13px;
    font-weight: 500;
    color: var(--dark);
    background: var(--bg);
    border: 0.5px solid var(--border);
    border-radius: 8px;
    padding: 10px 12px;
    width: 100%;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
}
.field input::placeholder,
.field textarea::placeholder { color: var(--soft); font-weight: 400; }
.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: var(--blue);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(0, 38, 99, 0.07);
}
.field textarea { resize: none; }
.field select { cursor: pointer; }

/* Input with addon */
.input-addon-wrap { position: relative; }
.input-addon-wrap input { padding-left: 38px; }
.input-addon-wrap .addon-icon {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    color: var(--soft);
    font-size: 13px;
    pointer-events: none;
}
.input-addon-wrap-right input { padding-right: 44px; }
.input-addon-wrap-right .addon-suffix {
    position: absolute;
    right: 0; top: 0; bottom: 0;
    display: flex; align-items: center;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 700;
    color: var(--soft);
    border-left: 0.5px solid var(--border);
    background: var(--bg);
    border-radius: 0 8px 8px 0;
    pointer-events: none;
}

/* select arrow */
.select-wrap { position: relative; }
.select-wrap select { padding-right: 32px; }
.select-wrap::after {
    content: '';
    position: absolute;
    right: 12px; top: 50%;
    transform: translateY(-50%);
    width: 0; height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-top: 5px solid var(--soft);
    pointer-events: none;
}
.select-wrap.field select { padding-right: 32px; }

/* locked select (non-manager) */
.select-locked {
    background: #F8FAFC !important;
    pointer-events: none;
    color: var(--mid) !important;
}

/* ── Date and Time input ── */
input[type="date"], input[type="time"] {
    font-family: var(--font);
    font-weight: 600;
    color: var(--blue);
}

/* ── Sticky sidebar ── */
.sidebar-sticky { position: sticky; top: 24px; }

/* ── Summary preview ── */
.summary-box {
    background: var(--bg);
    border-radius: 8px;
    padding: 14px;
    margin-bottom: 16px;
    border: 0.5px solid var(--border);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 5px 0;
    font-size: 12px;
}
.summary-row:not(:last-child) { border-bottom: 0.5px solid var(--border); }
.summary-label { color: var(--soft); }
.summary-value { font-weight: 600; color: var(--dark); text-align: right; max-width: 60%; }
.summary-value.empty { color: var(--border); font-weight: 400; font-style: italic; }

/* ── Buttons ── */
.btn-submit {
    width: 100%;
    height: 48px;
    background: var(--dark);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: var(--font);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.15s, transform 0.1s;
    letter-spacing: 0.2px;
    margin-bottom: 10px;
}
.btn-submit:hover:not(:disabled) { background: #1e293b; }
.btn-submit:active:not(:disabled) { transform: scale(0.99); }
.btn-submit:disabled { background: var(--border); color: var(--soft); cursor: not-allowed; }

.btn-clear {
    width: 100%;
    height: 42px;
    background: var(--white);
    color: var(--mid);
    border: 0.5px solid var(--border);
    border-radius: 10px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-clear:hover { background: var(--bg); color: var(--dark); border-color: #cbd5e1; }

/* ── SweetAlert ── */
.swal2-popup { font-family: var(--font) !important; border-radius: 14px !important; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .container-fluid { padding: 16px !important; }
    .sidebar-sticky { position: static; }
}
</style>

<div class="container-fluid">

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="page-title">Walk-In Service Booking</div>
                <div class="page-subtitle">ទម្រង់បញ្ចូលការកក់សម្រាប់អតិថិជន Walk-in</div>
            </div>
        </div>
        <a href="{{ route('services.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('services.store') }}" method="POST" id="bookingForm">
        @csrf
        <input type="hidden" name="is_walk_in" value="1">

        <div class="row g-4">

            <!-- ── Left Column ── -->
            <div class="col-xl-8 col-lg-7">

                <!-- Customer -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon"><i class="fas fa-user"></i></div>
                        <div class="card-header-title">Customer Information</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field">
                                    <label class="field-label">ឈ្មោះអតិថិជន <span class="req">*</span></label>
                                    <input type="text" name="customer_name" id="f_name"
                                        placeholder="ឧ. លោក សុខ ដារ៉ា" required
                                        oninput="updateSummary()">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field input-addon-wrap">
                                    <label class="field-label">លេខទូរស័ព្ទ <span class="req">*</span></label>
                                    <i class="fas fa-phone-alt addon-icon"></i>
                                    <input type="tel" name="contact" id="f_phone"
                                        placeholder="012 345 678" required
                                        oninput="updateSummary()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon"><i class="fas fa-car-side"></i></div>
                        <div class="card-header-title">Vehicle Information</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="field select-wrap">
                                    <label class="field-label">ម៉ូឌែលរថយន្ត <span class="req">*</span></label>
                                    <select name="model" id="f_model" required onchange="updateSummary()">
                                        <option value="" disabled selected>-- ជ្រើសរើសម៉ូឌែល --</option>
                                        <optgroup label="MG SUVs">
                                            <option>MG ZS 1.5</option>
                                            <option>MG ZS 1.0 / 1.5</option>
                                            <option>MG ZS Hybrid</option>
                                            <option>MG ZS EV</option>
                                            <option>MG ZS32</option>
                                            <option>MG HS</option>
                                            <option>MG HS 2026 Hybrid</option>
                                            <option>MG RX5 2017–2021</option>
                                            <option>MG RX5 Reborn / Sport</option>
                                            <option>MG RX5 MCE</option>
                                            <option>MG RX8</option>
                                            <option>MG RX9</option>
                                            <option>MG GS 2.0</option>
                                            <option>MG D60</option>
                                        </optgroup>
                                        <optgroup label="Sedan & Sports">
                                            <option>MG GT 2024</option>
                                            <option>MG GT 2026</option>
                                            <option>MG 7 1.5T</option>
                                            <option>MG 7 2.0T</option>
                                            <option>MG 6</option>
                                            <option>MG 350</option>
                                            <option>MG 550</option>
                                            <option>MG 750</option>
                                            <option>MG Cyberster</option>
                                        </optgroup>
                                        <optgroup label="EV Series">
                                            <option>MG 4 Urban</option>
                                            <option>MG 4 (Comfort)</option>
                                            <option>MG 4 (Luxury)</option>
                                            <option>MG Marvel R</option>
                                        </optgroup>
                                        <optgroup label="Maxus & MIFA">
                                            <option>Maxus G50</option>
                                            <option>MG G50 PHEV</option>
                                            <option>Maxus T60</option>
                                            <option>MG T70</option>
                                            <option>Maxus T90</option>
                                            <option>Maxus V80</option>
                                            <option>MIFA 7</option>
                                            <option>MIFA 9</option>
                                        </optgroup>
                                        <optgroup label="Leapmotor & IM">
                                            <option>IM 5</option>
                                            <option>IM 6</option>
                                            <option>LPM T03</option>
                                            <option>LPM B10</option>
                                            <option>LPM C10 (CH)</option>
                                            <option>LPM C10 (Global)</option>
                                            <option>LPM C16 (Front)</option>
                                            <option>LPM C16 (Rear)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field">
                                    <label class="field-label">ស្លាកលេខ <span class="req">*</span></label>
                                    <input type="text" name="plate" id="plate"
                                        placeholder="2AB-1234" required
                                        oninput="updateSummary()">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field input-addon-wrap input-addon-wrap-right">
                                    <label class="field-label">គីឡូម៉ែត្រ (ODO) <span class="req">*</span></label>
                                    <input type="number" name="mileage" id="mileage"
                                        placeholder="25000" required>
                                    <span class="addon-suffix">KM</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon"><i class="fas fa-wrench"></i></div>
                        <div class="card-header-title">Service Details</div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="field select-wrap">
                                    <label class="field-label">ប្រភេទសេវាកម្ម <span class="req">*</span></label>
                                    <select name="service_type" id="f_service" required onchange="updateSummary()">
                                        <option value="" disabled selected>-- ជ្រើសរើសសេវាកម្ម --</option>
                                        <optgroup label="ការត្រួតពិនិត្យ និងជួសជុល">
                                            <option value="Engine Malfunction">លោតឆេកម៉ាស៊ីន | Engine Check</option>
                                            <option value="General Repair">ការជួសជុលទូទៅ | General Repair</option>
                                            <option value="General Inspection">ត្រួតពិនិត្យទូទៅ | General Inspection</option>
                                            <option value="Check Vibration">ត្រួតពិនិត្យរំញ័រ | Check Vibration</option>
                                            <option value="Check Noise">ត្រួតពិនិត្យសំឡេង | Check Noise</option>
                                        </optgroup>
                                        <optgroup label="ការថែទាំតាមកម្រិតគីឡូ">
                                            <option value="Interval Maintenance">ថែទាំតាមកាលកំណត់ | Interval Service</option>
                                            <option value="Service 20000KM">20,000 km Service</option>
                                            <option value="Service 40000KM">40,000 km Service</option>
                                            <option value="Service 50000KM">50,000 km Service</option>
                                            <option value="Service 60000KM">60,000 km Service</option>
                                        </optgroup>
                                        <optgroup label="សេវាកម្មផ្សេងៗ">
                                            <option value="Body and Paint">Body & Paint | តោន/ថ្នាំ</option>
                                            <option value="Warranty">Warranty | ការធានា</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="field">
                                    <label class="field-label">បញ្ជាក់បន្ថែម (Remark)</label>
                                    <textarea name="remark" id="remark" rows="3"
                                        placeholder="បញ្ជាក់បញ្ហា ឬសំណូមពរអតិថិជន..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Right Column ── -->
            <div class="col-xl-4 col-lg-5">
                <div class="sidebar-sticky">

                    <!-- Scheduling -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="far fa-clock"></i></div>
                            <div class="card-header-title">Walk-in Schedule</div>
                        </div>
                        <div class="card-body">

                            <div class="field select-wrap mb-3">
                                <label class="field-label">សាខា (Branch) <span class="req">*</span></label>
                                @if($isManager)
                                    <select name="location" id="location" required onchange="updateSummary()">
                                        <option value="" disabled selected>ជ្រើសរើសសាខា</option>
                                        <option value="13">ការិយាល័យកណ្តាល (Head Office)</option>
                                        <option value="11">ផ្លូវលេខ ៥០ ម (Street 50M)</option>
                                        <option value="12">សៀមរាប (Siem Reap)</option>
                                        <option value="10">បាត់ដំបង (Battambang)</option>
                                    </select>
                                @else
                                    <select name="location" id="location" class="select-locked" required>
                                        @if($userBranchId == 13)
                                            <option value="13" selected>ការិយាល័យកណ្តាល (Head Office)</option>
                                        @elseif($userBranchId == 11)
                                            <option value="11" selected>ផ្លូវលេខ ៥០ ម (Street 50M)</option>
                                        @elseif($userBranchId == 12)
                                            <option value="12" selected>សៀមរាប (Siem Reap)</option>
                                        @elseif($userBranchId == 10)
                                            <option value="10" selected>បាត់ដំបង (Battambang)</option>
                                        @else
                                            <option value="{{ $userBranchId }}" selected>Your Branch</option>
                                        @endif
                                    </select>
                                @endif
                            </div>

                            <div class="field mb-3">
                                <label class="field-label">កាលបរិច្ឆេទ (Date) <span class="req">*</span></label>
                                <input type="date" name="booking_date" id="booking_date"
                                    value="{{ date('Y-m-d') }}"
                                    min="{{ date('Y-m-d') }}" required
                                    onchange="updateSummary()">
                            </div>

                            <div class="field mb-4">
                                <label class="field-label">ម៉ោងចូល (Time) <span class="req">*</span></label>
                                <input type="time" name="booking_time" id="booking_time"
                                    min="08:00" max="17:00" required
                                    onchange="validateTime(); updateSummary()">
                                <small class="text-muted" style="font-size: 11px; margin-top: 5px;">
                                    <i class="fas fa-info-circle text-info"></i> អនុញ្ញាតចន្លោះម៉ោង 08:00 AM ដល់ 05:00 PM
                                </small>
                            </div>

                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-receipt"></i></div>
                            <div class="card-header-title">Booking Summary</div>
                        </div>
                        <div class="card-body" style="padding:16px !important">
                            <div class="summary-box">
                                <div class="summary-row">
                                    <span class="summary-label">អតិថិជន</span>
                                    <span class="summary-value empty" id="s_name">—</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">ទូរស័ព្ទ</span>
                                    <span class="summary-value empty" id="s_phone">—</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">ម៉ូឌែល</span>
                                    <span class="summary-value empty" id="s_model">—</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">សេវាកម្ម</span>
                                    <span class="summary-value empty" id="s_service">—</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">ថ្ងៃ · ម៉ោង</span>
                                    <span class="summary-value empty" id="s_datetime">—</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-submit" id="btnSubmit">
                                <i class="fas fa-check-circle"></i> រក្សាទុក
                            </button>
                            <button type="reset" class="btn-clear" onclick="clearSummary()">
                                សម្អាតទិន្នន័យ
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ការពារការបញ្ចូលម៉ោងខុសពីចន្លោះ ០៨:០០ ដល់ ១៧:០០
function validateTime() {
    const timeInput = document.getElementById('booking_time');
    const selectedTime = timeInput.value;

    if (selectedTime) {
        if (selectedTime < '08:00' || selectedTime > '17:00') {
            Swal.fire({
                icon: 'error',
                title: 'ម៉ោងមិនត្រឹមត្រូវ',
                text: 'សូមអភ័យទោស! ម៉ោងអនុញ្ញាតគឺចាប់ពី 08:00 AM ដល់ 05:00 PM ប៉ុណ្ណោះ។',
                confirmButtonColor: '#B70101',
            });
            timeInput.value = ''; // លុបទិន្នន័យខុសចេញ
            return false;
        }
    }
    return true;
}

function updateSummary() {
    const set = (id, val) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (val) {
            el.textContent = val;
            el.classList.remove('empty');
        } else {
            el.textContent = '—';
            el.classList.add('empty');
        }
    };

    set('s_name',    document.getElementById('f_name')?.value.trim());
    set('s_phone',   document.getElementById('f_phone')?.value.trim());
    set('s_model',   document.getElementById('f_model')?.value);
    set('s_service', document.getElementById('f_service')?.value);

    const date = document.getElementById('booking_date')?.value;
    const timeVal = document.getElementById('booking_time')?.value;
    
    // បំប្លែងម៉ោងទៅជាទម្រង់ 12-hour AM/PM សម្រាប់បង្ហាញលើ Summary ឱ្យស្អាត
    let formattedTime = null;
    if(timeVal) {
        let [h, m] = timeVal.split(':');
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        formattedTime = h + ':' + m + ' ' + ampm;
    }

    set('s_datetime', (date && formattedTime) ? date + ' · ' + formattedTime : (date || null));
}

function clearSummary() {
    ['s_name','s_phone','s_model','s_service','s_datetime'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.textContent = '—'; el.classList.add('empty'); }
    });
}

$(document).ready(function() {
    $('#plate').on('input', function() { $(this).val($(this).val().toUpperCase()); });

    $('#bookingForm').on('submit', function(e) {
        
        // ឆែកមើលម៉ោងម្តងទៀតមុននឹង Save ដើម្បីប្រាកដថាអត់មានបញ្ហា
        if (!validateTime()) {
            e.preventDefault();
            return;
        }

        if (!$('#booking_time').val()) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'ភ្លេចរើសម៉ោង',
                text: 'សូមបញ្ចូលម៉ោង Walk-in នៅផ្ទាំង Schedule',
                confirmButtonColor: '#002663',
            });
            return;
        }

        $('#btnSubmit').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin"></i> កំពុងរក្សាទុក...');
        Swal.fire({
            title: 'កំពុងដំណើរការ...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });
    });
});
</script>

@endsection