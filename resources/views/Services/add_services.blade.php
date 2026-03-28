@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root {
        --brand-primary: #4f46e5;
        --brand-dark: #1e293b;
        --border-color: #e2e8f0;
    }

    body { 
        font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif !important; 
        background-color: #f0f2f5; 
    }

    .card-custom { 
        border: none; border-radius: 24px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
        overflow: hidden;
    }

    .card-header-custom { 
        background: var(--brand-dark); color: white; padding: 1.5rem 2rem; border: none; 
    }

    .label-custom { 
        font-weight: 700; color: #64748b; font-size: 0.8rem; margin-bottom: 6px; display: block; 
    }

    .form-control, .form-select { 
        border-radius: 12px; border: 1.5px solid var(--border-color); 
        padding: 10px 15px; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;
    }

    .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

    /* Time Slot Style */
    .time-slot-box { 
        border: 1.5px solid #f1f5f9; border-radius: 12px; padding: 8px; 
        cursor: pointer; text-align: center; background: white; transition: 0.2s; 
    }
    .time-val { font-size: 0.85rem; font-weight: 700; }
    .status-val { font-size: 0.65rem; font-weight: 600; }
    .btn-check:checked + .time-slot-box { border-color: var(--brand-primary); background: #f5f3ff; }
    .btn-check:disabled + .time-slot-box { background: #f8fafc; color: #cbd5e1; cursor: not-allowed; opacity: 0.6; }

    .btn-save { 
        background: var(--brand-primary); color: white; border: none; padding: 12px 40px; 
        border-radius: 12px; font-weight: 700; transition: 0.4s; 
    }
    .btn-save:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3); }
</style>

<div class="container-xxl flex-grow-1 container-p-y text-start">
    
    <div class="card card-custom animate__animated animate__fadeIn">
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <h5 class="m-0 fw-bold text-white">
                <i class="fas fa-calendar-plus me-2 text-warning"></i> ចុះឈ្មោះកក់សេវាកម្មថ្មី (NEW BOOKING)
            </h5>
            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">ត្រឡប់ក្រោយ</a>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('services.store') }}" method="POST" id="bookingForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="label-custom">១. ឈ្មោះអតិថិជន (Customer Name)</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="បញ្ចូលឈ្មោះ" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">២. លេខទូរស័ព្ទ (Contact Number)</label>
                        <input type="text" name="contact" class="form-control" placeholder="012 345 678" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">៣. ម៉ូឌែលរថយន្ត (Vehicle Model)</label>
                        <select name="model" class="form-select" required>
                            <option value="" selected disabled>--- រើសម៉ូឌែល ---</option>
                            @foreach(['MG ZS', 'MG HS', 'MG RX5', 'MG GT', 'MG Marvel R', 'MG 4 EV'] as $car)
                                <option value="{{ $car }}">{{ $car }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">៤. ស្លាកលេខ (Plate Number)</label>
                        <input type="text" name="plate" id="plate" class="form-control fw-bold text-uppercase" placeholder="2AB-1234" required>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">៥. ប្រភេទសេវាកម្ម (Service Type)</label>
                        <select name="service_type" id="service_type" class="form-select" required>
                            <option value="" selected disabled>--- រើសសេវាកម្ម ---</option>
                            <option value="Maintenance">ថែទាំតាមកំណត់ (Maintenance)</option>
                            <option value="Repair">ជួសជុលទូទៅ (Repair)</option>
                            <option value="Check-up">ត្រួតពិនិត្យ (Check-up)</option>
                            <option value="Warranty">ការធានា (Warranty)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">៦. សាខា (Branch Location)</label>
                        <select name="location" id="location" class="form-select" required>
                            <option value="" selected disabled>--- រើសសាខា ---</option>
                            <option value="HQ">សាខាភ្នំពេញ (HQ)</option>
                            <option value="50M">សាខាផ្លូវ ៥០ម៉ែត្រ</option>
                            <option value="SR">សាខាសៀមរាប (SR)</option>
                            <option value="BTB">សាខាបាត់ដំបង (BTB)</option>
                            <option value="MC">មជ្ឈមណ្ឌល MC</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">៧. ថ្ងៃត្រូវមក (Date)</label>
                        <input type="date" name="booking_date" id="booking_date" class="form-control fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-12 mt-4">
                        <label class="label-custom">៨. ជ្រើសរើសម៉ោងដែលទំនេរ (Available Slots)</label>
                        <div class="row g-2" id="time-slots-container">
                            <div class="col-12 text-center py-3 text-muted small">សូមរើស ប្រភេទសេវាកម្ម សាខា និងថ្ងៃសិន...</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-end">
                    <button type="reset" class="btn btn-link text-muted fw-bold text-decoration-none me-3">សម្អាត Form</button>
                    <button type="submit" id="submitBtn" class="btn btn-save shadow">
                        <i class="fas fa-check-circle me-2"></i> រក្សាទុកការកក់
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function fetchSlots() {
        const date = $('#booking_date').val();
        const location = $('#location').val();
        const serviceType = $('#service_type').val();

        if (date && location && serviceType) {
            const container = $('#time-slots-container');
            container.html('<div class="col-12 text-center py-2 small text-muted">កំពុងឆែកម៉ោង...</div>');

            $.ajax({
                url: "{{ route('slots.check') }}",
                type: "GET",
                data: { date: date, location: location, service_type: serviceType },
                success: function(response) {
                    let html = '';
                    response.forEach(function(slot, index) {
                        let isDisabled = slot.isFull ? 'disabled' : '';
                        let statusColor = slot.isFull ? '#ef4444' : '#10b981';
                        let statusText = slot.isFull ? 'ពេញ (Full)' : 'ទំនេរ: ' + slot.remaining;
                        
                        html += `
                            <div class="col-4 col-md-2">
                                <input type="radio" class="btn-check" name="booking_time" id="time${index}" value="${slot.time}" ${isDisabled} required>
                                <label class="time-slot-box d-block shadow-sm" for="time${index}">
                                    <div class="time-val">${slot.time}</div>
                                    <div class="status-val" style="color:${statusColor}">${statusText}</div>
                                </label>
                            </div>`;
                    });
                    container.html(html);
                }
            });
        }
    }

    $(document).ready(function() {
        $('#booking_date, #location, #service_type').on('change', fetchSlots);
        $('#plate').on('input', function() { $(this).val($(this).val().toUpperCase()); });

        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'បញ្ជាក់ការរក្សាទុក?',
                text: "សូមពិនិត្យព័ត៌មានអតិថិជនឱ្យបានត្រឹមត្រូវ!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'បាទ/ចាស, រក្សាទុក',
                cancelButtonText: 'ពិនិត្យម្ដងទៀត',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'កំពុងរក្សាទុក...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    this.submit();
                }
            });
        });
    });
</script>
@endsection