@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --mg-blue: #4f46e5; --mg-dark: #1e293b; --border-color: #e2e8f0; --mg-red: #db2d2d; }
    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: #f8fafc; }
    .card-custom { border: none; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); overflow: hidden; }
    .card-header-custom { background: var(--mg-dark); color: white; padding: 1.5rem 2rem; }
    .label-custom { font-weight: 700; color: #64748b; font-size: 0.85rem; margin-bottom: 8px; display: block; }
    .form-control, .form-select { border-radius: 12px; border: 1.5px solid var(--border-color); padding: 12px 15px; font-weight: 600; transition: all 0.3s ease; }
    .form-control:focus, .form-select:focus { border-color: var(--mg-blue); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    
    /* Styles for Locked State */
    .locked-bg { background-color: #f1f5f9 !important; cursor: not-allowed; color: #94a3b8; }
    .status-locked-badge { background: #dcfce7; color: #166534; padding: 12px 25px; border-radius: 12px; font-weight: 800; border: 1px solid #166534; display: inline-block; }
    .remark-section { background-color: #f0f7ff; border-radius: 20px; padding: 25px; border: 1px dashed var(--mg-blue); }
    .btn-update { background: var(--mg-blue); color: white; padding: 12px 45px; border-radius: 12px; font-weight: 700; border: none; transition: 0.3s; }
    .btn-update:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }
</style>

<div class="container-xxl flex-grow-1 container-p-y text-start">
    
    @php $isLocked = ($booking->status == 'Completed'); @endphp

    @if($isLocked)
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 animate__animated animate__fadeInDown d-flex align-items-center">
            <i class="fas fa-lock me-3 fs-3"></i>
            <div>
                <b style="font-family: 'Kantumruy Pro';">ការកក់នេះត្រូវបានបិទបញ្ចប់រួចរាល់ (Status: Completed)</b><br>
                <small>This record is finalized and cannot be modified to ensure data integrity.</small>
            </div>
        </div>
    @endif

    <div class="card card-custom animate__animated animate__fadeIn">
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <h5 class="m-0 fw-bold">
                <i class="fas fa-edit me-2 text-warning"></i> UPDATE SERVICE: {{ $booking->plate }}
            </h5>
            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Back to List</a>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('services.update', $booking->id) }}" method="POST" id="editBookingForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="label-custom">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control {{ $isLocked ? 'locked-bg' : '' }}" 
                               value="{{ $booking->customer_name }}" {{ $isLocked ? 'readonly' : 'required' }}>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">Contact Number</label>
                        <input type="text" name="contact" class="form-control {{ $isLocked ? 'locked-bg' : '' }}" 
                               value="{{ $booking->contact }}" {{ $isLocked ? 'readonly' : 'required' }}>
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom">Vehicle Model</label>
                        <select name="model" class="form-select {{ $isLocked ? 'locked-bg' : '' }}" {{ $isLocked ? 'disabled' : 'required' }}>
                            @foreach(['MG ZS', 'MG HS', 'MG RX5', 'MG GT', 'MG Marvel R', 'MG 4 EV'] as $car)
                                <option value="{{ $car }}" {{ $booking->model == $car ? 'selected' : '' }}>{{ $car }}</option>
                            @endforeach
                        </select>
                        @if($isLocked) <input type="hidden" name="model" value="{{ $booking->model }}"> @endif
                    </div>

                    <div class="col-md-4">
                        <label class="label-custom text-primary fw-bold">Service Status</label>
                        @if($isLocked)
                            <div class="status-locked-badge animate__animated animate__pulse">
                                <i class="fas fa-check-double me-2"></i> COMPLETED (រួចរាល់)
                            </div>
                            <input type="hidden" name="status" value="Completed">
                        @else
                            <select name="status" id="statusSelect" class="form-select border-primary fw-bold text-primary">
                                <option value="Pending" {{ $booking->status == 'Pending' ? 'selected' : '' }}>Pending (កំពុងរង់ចាំ)</option>
                                <option value="In Progress" {{ $booking->status == 'In Progress' ? 'selected' : '' }}>In Progress (កំពុងធ្វើការ)</option>
                                <option value="Completed">Completed (បញ្ចប់ការងារ)</option>
                            </select>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <label class="label-custom">Branch Location</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['PP' => 'Phnom Penh', 'SR' => 'Siem Reap', 'BTB' => 'Battambang', 'PP2' => 'MG 60M'] as $val => $label)
                                <input type="radio" class="btn-check" name="location" id="loc{{ $val }}" value="{{ $val }}" 
                                       {{ $booking->location == $val ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                <label class="btn btn-outline-primary px-3 rounded-pill fw-bold" for="loc{{ $val }}">{{ $label }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <div class="remark-section animate__animated animate__fadeIn">
                            <h6 class="fw-bold mb-4 text-dark"><i class="fas fa-user-tie me-2 text-primary"></i> Staff Assignment & Repair Detail</h6>
                            <div class="row g-4 text-start">
                                
                                <div class="col-md-6">
                                    <label class="label-custom">SA Name (ឈ្មោះអ្នកទទួលបន្ទុក)</label>
                                    @if($isLocked)
                                        <input type="text" class="form-control locked-bg" value="{{ $booking->sa_name }}" readonly>
                                    @else
                                        <select name="sa_name" class="form-select border-primary" required>
                                            <option value="" disabled selected>--- ជ្រើសរើសឈ្មោះ SA ---</option>
                                            @foreach(['Chap Pov', 'Nang Kimyeang', 'Poeurn Hengleap'] as $sa)
                                                <option value="{{ $sa }}" {{ $booking->sa_name == $sa ? 'selected' : '' }}>{{ $sa }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label class="label-custom">Technician Name (ឈ្មោះមេជាងជួសជុល)</label>
                                    @if($isLocked)
                                        <input type="text" class="form-control locked-bg" value="{{ $booking->technician_name }}" readonly>
                                    @else
                                        <select name="technician_name" class="form-select border-primary" required>
                                            <option value="" disabled selected>--- ជ្រើសរើសឈ្មោះមេជាង ---</option>
                                            @foreach([
                                                'Lorn Vanthorn', 'Heang Tongchhay', 'Keam Piseth', 
                                                'Sum Thoeunrethy', 'Han Hak', 'Chhoeurn Phirith', 
                                                'Hea Veasna', 'Seng Sopheaktra'
                                            ] as $tech)
                                                <option value="{{ $tech }}" {{ $booking->technician_name == $tech ? 'selected' : '' }}>{{ $tech }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label class="label-custom">Start Repair Time (ម៉ោងចាប់ផ្ដើម)</label>
                                    <input type="time" name="start_time" class="form-control {{ $isLocked ? 'locked-bg' : '' }}" 
                                           value="{{ $booking->start_time }}" {{ $isLocked ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-custom">End Repair Time (ម៉ោងបញ្ចប់)</label>
                                    <input type="time" name="end_time" class="form-control {{ $isLocked ? 'locked-bg' : '' }}" 
                                           value="{{ $booking->end_time }}" {{ $isLocked ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-end">
                    <a href="{{ route('services.index') }}" class="btn btn-link text-muted fw-bold text-decoration-none me-4">Cancel</a>
                    @if(!$isLocked)
                        <button type="submit" class="btn btn-update shadow">
                            <i class="fas fa-save me-2"></i> Save & Lock Data
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const statusSelect = document.getElementById('statusSelect');
    const editForm = document.getElementById('editBookingForm');

    editForm.addEventListener('submit', function(e) {
        // បង្ខំឱ្យបំពេញព័ត៌មានបុគ្គលិក បើសិនជាជ្រើសរើសយក Completed
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
                    text: 'សូមជ្រើសរើសឈ្មោះ SA, មេជាង និងបំពេញម៉ោងជួសជុលឱ្យបានត្រឹមត្រូវ មុននឹងបិទ Job!',
                    confirmButtonColor: '#db2d2d',
                });
                return;
            }
        }

        e.preventDefault();
        Swal.fire({
            title: 'បញ្ជាក់ការរក្សាទុក?',
            text: statusSelect && statusSelect.value === 'Completed' ? "ប្រយ័ត្ន៖ ទិន្នន័យនឹងត្រូវចាក់សោរមិនឱ្យកែប្រែក្រោយពេលប្តូរទៅ Completed!" : "តើអ្នកចង់រក្សាទុកការកែប្រែនេះមែនទេ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'បាទ, រក្សាទុក',
            cancelButtonText: 'ពិនិត្យឡើងវិញ'
        }).then((result) => {
            if (result.isConfirmed) { 
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                editForm.submit(); 
            }
        });
    });
</script>
@endsection