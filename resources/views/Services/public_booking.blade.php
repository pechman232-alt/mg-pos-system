@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --mg-red: #e31e24; --mg-blue: #002663; --soft-bg: #f0f4f8; }
    body { font-family: 'Kantumruy Pro', sans-serif !important; background-color: var(--soft-bg); }
    .booking-container { max-width: 800px; margin: 40px auto; }
    .glass-card { background: white; border: none; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); overflow: hidden; }
    .hero-banner { background: linear-gradient(135deg, var(--mg-blue) 0%, #06163a 100%); padding: 50px 20px; color: white; text-align: center; position: relative; }
    .hero-banner i { font-size: 3rem; margin-bottom: 15px; opacity: 0.9; }
    .step-indicator { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--mg-red); letter-spacing: 1px; margin-bottom: 10px; display: block; }
    .form-label { font-weight: 700; color: #334155; font-size: 0.85rem; }
    .form-control, .form-select { border-radius: 14px; padding: 14px 18px; border: 2px solid #e2e8f0; transition: 0.3s; font-weight: 600; }
    .form-control:focus { border-color: var(--mg-red); box-shadow: 0 0 0 4px rgba(227, 30, 36, 0.1); }

    .slot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; }
    .slot-card { cursor: pointer; position: relative; }
    .slot-card input { position: absolute; opacity: 0; }
    .slot-content { display: block; padding: 15px 10px; border: 2px solid #e2e8f0; border-radius: 18px; text-align: center; transition: 0.3s; background: white; }
    .slot-card input:checked + .slot-content { border-color: var(--mg-red); background: #fff1f2; color: var(--mg-red); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(227, 30, 36, 0.15); }
    .slot-card input:disabled + .slot-content { background: #f8fafc; border-style: dashed; opacity: 0.5; cursor: not-allowed; }

    .btn-submit { background: var(--mg-red); color: white; border: none; padding: 20px; border-radius: 18px; width: 100%; font-weight: 800; font-size: 1.1rem; transition: 0.4s; margin-top: 20px; }
    .btn-submit:hover:not(:disabled) { background: var(--mg-blue); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(0,38,99,0.25); }
    .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }
</style>

<div class="container px-3">
    <div class="booking-container animate__animated animate__fadeIn">
        <div class="glass-card">
            <div class="hero-banner">
                <i class="fas fa-calendar-check animate__animated animate__bounceInDown"></i>
                <h2 class="fw-800 mb-1">ONLINE BOOKING</h2>
                <p class="opacity-75 mb-0">កក់សេវាកម្មជួសជុលរថយន្តលោកអ្នកយ៉ាងឆាប់រហ័ស</p>
            </div>

            <div class="p-4 p-md-5">
                <form id="bookingForm" action="{{ route('services.public_store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <span class="step-indicator">Step 01: Customer Details</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name / ឈ្មោះពេញ</label>
                                    <input type="text" name="customer_name" class="form-control" placeholder="ឧ. សុខ ដារ៉ា" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number / លេខទូរស័ព្ទ</label>
                                    <input type="tel" name="contact" class="form-control" placeholder="012 345 678" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <span class="step-indicator">Step 02: Vehicle & Service</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Car Model / ម៉ូឌែល</label>
                                    <select name="model" class="form-select" required>
                                        <option value="" disabled selected>ជ្រើសរើសម៉ូឌែល</option>
                                        @foreach(['MG ZS', 'MG HS', 'MG GT', 'MG RX5', 'MG 4 EV'] as $model)
                                            <option value="{{ $model }}">{{ $model }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Plate Number / ស្លាកលេខ</label>
                                    <input type="text" name="plate" id="plate" class="form-control text-uppercase" placeholder="2AB-1234" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Service Type / ប្រភេទសេវាកម្ម</label>
                                    <select name="service_type" id="service_type" class="form-select" required>
                                        <option value="Maintenance" selected>Maintenance (ថែទាំតាមកំណត់)</option>
                                        <option value="Repair">General Repair (ជួសជុលទូទៅ)</option>
                                        <option value="Check-up">Safety Check (ត្រួតពិនិត្យសុវត្ថិភាព)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <span class="step-indicator">Step 03: Schedule</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-primary">Service Center / សាខា</label>
                                    <select name="location" id="location" class="form-select border-primary" required>
                                        <option value="សាខាការិយាល័យកណ្តាល">សាខាការិយាល័យកណ្តាល</option>
                                        <option value="សាខាផ្លូវលេខ ៦០ម៉ែត្រ">សាខាផ្លូវលេខ ៦០ម៉ែត្រ</option>
                                        <option value="សាខាសៀមរាប">សាខាសៀមរាប</option>
                                        <option value="សាខាបាត់ដំបង">សាខាបាត់ដំបង</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date / ថ្ងៃណាត់ជួប</label>
                                    <input type="date" name="booking_date" id="booking_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label mb-3"><i class="far fa-clock me-2"></i>Select Time / ជ្រើសរើសម៉ោងដែលទំនេរ</label>
                            <div id="slot-container" class="slot-grid"></div>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn-submit">
                        <i class="fas fa-paper-plane me-2"></i> CONFIRM BOOKING (យល់ព្រម)
                    </button>
                </form>
            </div>
        </div>
        <p class="text-center text-muted mt-4 small">© 2026 MG Cambodia. High-Quality Service Guaranteed.</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function fetchAvailableSlots() {
        const date = $('#booking_date').val();
        const loc = $('#location').val();
        const type = $('#service_type').val();

        if(!date || !loc) return;

        $('#slot-container').html('<div class="text-center py-3 w-100 small text-muted"><i class="fas fa-spinner fa-spin me-2"></i>កំពុងឆែកម៉ោងទំនេរ...</div>');

        $.get("{{ route('slots.check') }}", { date: date, location: loc, service_type: type }, function(data) {
            let html = '';
            if(data.length === 0) {
                html = '<div class="col-12 text-center text-danger small">មិនមានម៉ោងទំនេរសម្រាប់ថ្ងៃនេះទេ។</div>';
            } else {
                data.forEach((slot, i) => {
                    const isDisabled = slot.isFull ? 'disabled' : '';
                    html += `
                        <label class="slot-card">
                            <input type="radio" name="booking_time" value="${slot.time}" ${isDisabled} required>
                            <div class="slot-content shadow-sm">
                                <div class="fw-bold" style="font-size:0.9rem">${slot.time}</div>
                                <div style="font-size:0.65rem" class="${slot.isFull ? 'text-danger' : 'text-success'}">
                                    ${slot.isFull ? 'FULL' : 'ទំនេរ: '+slot.remaining}
                                </div>
                            </div>
                        </label>`;
                });
            }
            $('#slot-container').html(html);
        }).fail(function() {
            $('#slot-container').html('<div class="col-12 text-center text-danger small">មានបញ្ហាបច្ចេកទេសក្នុងការទាញយកម៉ោង។</div>');
        });
    }

    $(document).ready(function() {
        fetchAvailableSlots();
        $('#booking_date, #location, #service_type').on('change', fetchAvailableSlots);
        $('#plate').on('input', function() { $(this).val($(this).val().toUpperCase()); });

        // ការពារការចុច Submit ច្រើនដង និងបង្ហាញ Loading
        $('#bookingForm').on('submit', function() {
            const btn = $('#btnSubmit');
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin me-2"></i>កំពុងបញ្ជូនទិន្នន័យ...');
            
            Swal.fire({
                title: 'កំពុងរក្សាទុក...',
                text: 'សូមរង់ចាំមួយភ្លែត',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
        });

        // បង្ហាញ Alert Success
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'ជោគជ័យ!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#002663',
                timer: 5000
            });
        @endif
        
        // បង្ហាញ Alert Error (រួមទាំងបញ្ហា 429)
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'កក់មិនបានជោគជ័យ!',
                text: "{{ $errors->first() }}",
                confirmButtonColor: '#e31e24'
            });
        @endif
    });


    
</script>
@endsection