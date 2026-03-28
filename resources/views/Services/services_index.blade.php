@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>

<style>
    :root {
        --mg-blue: #4f46e5;
        --mg-dark: #1e293b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #0ea5e9;
    }

    body { 
        font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; 
        background-color: #f8fafc; 
        color: #1e293b;
    }

    /* Summary Cards */
    .summary-card {
        border: none; border-radius: 24px; background: #ffffff;
        padding: 22px; transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,0,0,0.02);
        border-bottom: 4px solid transparent;
    }
    .summary-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
    .icon-box {
        width: 52px; height: 52px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    }

    /* Table Styling */
    .card-main { border: none; border-radius: 30px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
    .table thead th { 
        background-color: #f8fafc; color: #64748b; font-weight: 800; 
        text-transform: uppercase; font-size: 0.65rem; border: none; padding: 20px 15px;
    }
    .table tbody td { vertical-align: middle; border-bottom: 1px solid #f8fafc; padding: 15px; font-size: 0.82rem; }

    .badge-plate { background: var(--mg-dark); color: #fff; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-family: 'Roboto Mono', monospace; }
    .status-badge { padding: 6px 14px; border-radius: 50px; font-size: 0.65rem; font-weight: 800; display: inline-flex; align-items: center; }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-completed { background-color: #dcfce7; color: #065f46; }
    .status-progress { background-color: #e0f2fe; color: #075985; }
    .status-arrived { background-color: #ffedd5; color: #ea580c; }

    .time-box { 
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; 
        padding: 2px 8px; display: inline-block; font-family: 'Roboto Mono', monospace; 
        font-weight: 700; font-size: 0.75rem; min-width: 85px; text-align: center;
    }
    .kh-text { font-size: 0.8rem; display: block; margin-bottom: 2px; }
</style>

<div class="container-fluid p-4 text-start">
    
    <div class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-extrabold m-0" style="letter-spacing: -1px; color: var(--mg-dark);">Booking Dashboard</h2>
            <p class="text-muted m-0"><span class="kh-text text-primary fw-bold">គ្រប់គ្រងទិន្នន័យការកក់ និងរបាយការណ៍សរុប</span>Comprehensive Service Booking Reports</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportToExcel()" class="btn btn-light rounded-4 px-4 fw-bold shadow-sm border border-2">
                <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
            </button>
            <a href="{{ route('services.add') }}" class="btn btn-primary rounded-4 px-4 fw-bold shadow-sm d-flex align-items-center border-0" style="background: var(--mg-blue);">
                <i class="fas fa-plus-circle me-2"></i> Add Booking
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5 animate__animated animate__fadeIn">
        @php
            $summary = [
                ['total', 'ការកក់សរុប', 'TOTAL BOOKINGS', 'bg-primary', 'fa-calendar-check', 'border-primary'],
                ['pending', 'កំពុងរង់ចាំ', 'PENDING', 'bg-warning', 'fa-hourglass-half', 'border-warning'],
                ['progress', 'កំពុងជួសជុល', 'IN PROGRESS', 'bg-info', 'fa-screwdriver-wrench', 'border-info'],
                ['completed', 'រួចរាល់សរុប', 'COMPLETED', 'bg-success', 'fa-check-double', 'border-success']
            ];
        @endphp
        @foreach($summary as $item)
        <div class="col-md-3">
            <div class="card summary-card {{ $item[5] }}">
                <div class="d-flex align-items-center">
                    <div class="icon-box {{ $item[3] }} bg-opacity-10 {{ str_replace('bg', 'text', $item[3]) }}">
                        <i class="fas {{ $item[4] }}"></i>
                    </div>
                    <div class="ms-3">
                        <span class="kh-text text-muted fw-bold">{{ $item[1] }}</span>
                        <h4 class="fw-bold m-0">{{ $stats[$item[0]] ?? 0 }}</h4>
                        <small class="text-muted x-small uppercase fw-bold">{{ $item[2] }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="filter-wrapper mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">ស្វែងរក (Search)</label>
                <input type="text" id="liveSearch" class="form-control form-control-custom" placeholder="Search customer, plate, or contact...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">សាខា (Branch)</label>
                <select id="branchFilter" class="form-select form-control-custom">
                    <option value="all">គ្រប់សាខាទាំងអស់</option>
                    @foreach(['សាខាការិយាល័យកណ្តាល', 'សាខាផ្លូវលេខ ៦០ម៉ែត្រ', 'សាខាសៀមរាប', 'សាខាបាត់ដំបង'] as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">រើសថ្ងៃ (Date Filter)</label>
                <input type="date" id="dateFilter" class="form-control form-control-custom">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button onclick="resetFilters()" class="btn btn-light rounded-3 fw-bold w-100 border py-2 text-muted">Reset</button>
            </div>
        </div>
    </div>

    <div class="card card-main animate__animated animate__fadeInUp">
        <div class="table-responsive">
            <table class="table mb-0" id="bookingTable">
                <thead>
                    <tr>
                        <th class="ps-4">Customer & Car</th>
                        <th>Location & Service</th>
                        <th>Booking / Arrived</th>
                        <th class="text-center">Work Time (S-E)</th>
                        <th class="text-center">Duration</th>
                        <th class="text-center">Status</th>
                        <th>Staff Info</th>
                        <th class="text-center no-export">Action</th>
                    </tr>
                </thead>
                <tbody id="bookingTableBody">
                    @forelse($bookings as $booking)
                    <tr class="booking-row" 
                        data-branch="{{ $booking->location }}" 
                        data-date="{{ date('Y-m-d', strtotime($booking->booking_date)) }}">
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $booking->customer_name }}</div>
                            <span class="badge-plate my-1 d-inline-block">{{ $booking->plate }}</span>
                            <div class="x-small text-muted fw-bold text-uppercase">{{ $booking->model }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary small mb-1">{{ $booking->service_type }}</div>
                            <div class="text-muted x-small fw-bold"><i class="fas fa-location-dot me-1 text-danger"></i>{{ $booking->location }}</div>
                        </td>
                        <td>
                            <div class="fw-bold small text-mg-blue">{{ date('h:i A', strtotime($booking->booking_time)) }}</div>
                            <div class="text-orange x-small fw-bold">
                                <i class="fas fa-user-clock me-1"></i> Arrived: {{ $booking->arrival_time ? date('h:i A', strtotime($booking->arrival_time)) : 'N/A' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="mb-1 d-flex align-items-center justify-content-center">
                                <span class="time-box text-success">{{ $booking->start_time ? date('h:i A', strtotime($booking->start_time)) : '--:--' }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="time-box text-danger">{{ $booking->end_time ? date('h:i A', strtotime($booking->end_time)) : '--:--' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($booking->total_seconds)
                                <span class="badge bg-light text-dark border fw-bold">
                                    {{ floor($booking->total_seconds / 60) }} mn
                                </span>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $status = $booking->status;
                                $class = match($status) {
                                    'Completed' => 'status-completed',
                                    'In Progress' => 'status-progress',
                                    'Arrived' => 'status-arrived',
                                    default => 'status-pending'
                                };
                                $labelKh = match($status) {
                                    'Completed' => 'រួចរាល់',
                                    'In Progress' => 'កំពុងជួសជុល',
                                    'Arrived' => 'មកដល់ហើយ',
                                    default => 'រង់ចាំ'
                                };
                            @endphp
                            <span class="status-badge {{ $class }} shadow-sm">
                                {{ $labelKh }}
                            </span>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark mb-1">SA: {{ $booking->sa_name ?? 'N/A' }}</div>
                            <div class="small text-muted fw-bold">Tech: {{ $booking->technician_name ?? 'N/A' }}</div>
                        </td>
                        <td class="text-center no-export">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('services.edit', encrypt($booking->id)) }}" class="btn btn-sm btn-light border rounded-3 shadow-sm">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <button onclick="confirmDelete('{{ encrypt($booking->id) }}', '{{ $booking->customer_name }}')" class="btn btn-sm btn-light border rounded-3 shadow-sm">
                                    <i class="fas fa-trash-can text-danger"></i>
                                </button>
                                <form id="delete-form-{{ encrypt($booking->id) }}" action="{{ route('services.delete', encrypt($booking->id)) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted fw-bold">មិនទាន់មានទិន្នន័យនៅឡើយទេ។</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top bg-light">
            {{ $bookings->links() }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // --- ១. មុខងារ Auto Alert សម្រាប់កក់ Online ថ្មី ---
    function autoCheckNewBookings() {
        $.ajax({
            url: "{{ route('bookings.checkNew') }}",
            type: "GET",
            success: function(response) {
                if (response.hasNew) {
                    // បាញ់សំឡេង Alert
                    let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play();

                    // បង្ហាញ SweetAlert2
                    Swal.fire({
                        title: '🔔 មានការកក់ថ្មី!',
                        html: `អតិថិជន: <b>${response.customer}</b><br>ស្លាកលេខ: <b>${response.plate}</b>`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'មើលបញ្ជីថ្មី',
                        cancelButtonText: 'បិទ'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload(); 
                        }
                    });
                }
            }
        });
    }
    // ឆែករាល់ ២០ វិនាទី
    setInterval(autoCheckNewBookings, 20000);

    // --- ២. មុខងារ Filters ---
    function applyFilters() {
        let search = $('#liveSearch').val().toLowerCase();
        let branch = $('#branchFilter').val();
        let date = $('#dateFilter').val();

        $('.booking-row').each(function() {
            let rowText = $(this).text().toLowerCase();
            let rowBranch = $(this).attr('data-branch');
            let rowDate = $(this).attr('data-date');

            let matchSearch = rowText.indexOf(search) > -1;
            let matchBranch = (branch === 'all' || rowBranch === branch);
            let matchDate = (date === '' || rowDate === date);

            $(this).toggle(matchSearch && matchBranch && matchDate);
        });
    }

    $('#liveSearch').on('keyup', applyFilters);
    $('#branchFilter, #dateFilter').on('change', applyFilters);

    function resetFilters() {
        $('#liveSearch').val('');
        $('#branchFilter').val('all');
        $('#dateFilter').val('');
        applyFilters();
    }

    // --- ៣. មុខងារ Export & Delete ---
    function exportToExcel() {
        let table = document.getElementById("bookingTable");
        TableToExcel.convert(table, {
            name: "MG_Workshop_Report_" + new Date().toLocaleDateString() + ".xlsx",
            sheet: { name: "Records" },
            excludeBySelector: ".no-export"
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'តើអ្នកប្រាកដទេ?',
            html: `ទិន្នន័យរបស់ <b>${name}</b> នឹងត្រូវលុប!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'បាទ លុបវា!',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection