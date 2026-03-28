@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Moul&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    @font-face { 
        font-family: 'Century Gothic'; 
        src: local('Century Gothic'), local('AppleGothic'), local('Futura'), sans-serif; 
    }

    :root { 
        --mg-red: #e31e24; --mg-blue: #002663; --mg-green: #7ed348; 
        --mg-orange: #f97316; --hold-gray: #64748b; --bg-main: #f4f7fa; 
        --font-primary: "Century Gothic", sans-serif; 
    }

    /* Layout Reset */
    #sidebar-wrapper, .main-sidebar, .main-header, .navbar, footer, .main-footer { display: none !important; }
    .content-wrapper, .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; background: var(--bg-main) !important; }
    
    body { 
        font-family: var(--font-primary), "Kantumruy Pro", sans-serif !important; 
        background-color: var(--bg-main); margin: 0; -webkit-font-smoothing: antialiased;
    }

    .dashboard-wrapper { padding: 20px; }

    /* Summary Header */
    .summary-header {
        display: flex; justify-content: space-between; align-items: center;
        background: white; padding: 12px 25px; border-radius: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03); margin-bottom: 20px;
    }

    .live-clock { font-family: var(--font-primary); font-size: 1.8rem; font-weight: 900; color: var(--mg-blue); }

    /* Jobs Grid */
    .jobs-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    /* Card UI */
    .job-card {
        background: white; border-radius: 30px; padding: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-top: 8px solid var(--mg-red);
        display: flex; flex-direction: column; min-height: 480px;
        transition: 0.3s; position: relative;
    }
    
    .status-highlight-in-progress { border-top-color: var(--mg-blue) !important; }
    .status-highlight-arrived { border-top-color: var(--mg-orange) !important; }
    .status-highlight-hold { border-top-color: var(--hold-gray) !important; opacity: 0.95; }

    /* License Plate */
    .license-plate {
        width: 100px; height: 75px; background: white; border: 2px solid var(--mg-blue);
        border-radius: 12px; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }
    .plate-top { font-family: 'Moul'; font-size: 0.5rem; color: var(--mg-blue); }
    .plate-num { font-family: var(--font-primary); font-size: 1.3rem; font-weight: 900; color: var(--mg-blue); line-height: 1; }
    .plate-bot { font-family: var(--font-primary); font-size: 0.4rem; font-weight: 900; color: var(--mg-red); letter-spacing: 1px; }

    .cust-name { font-size: 1.4rem; font-weight: 900; margin: 10px 0 2px 0; color: #1e293b; }
    
    .status-info-box {
        background: #f8fafc; padding: 8px 12px; border-radius: 12px;
        margin: 10px 0; border-left: 4px solid var(--mg-blue); font-size: 0.8rem;
    }

    .contact-row {
        background: #f1f5f9; padding: 10px 15px; border-radius: 15px;
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;
    }

    /* IMPORTANT: Timer Styling */
    .timer-display {
        font-family: var(--font-primary); font-size: 3.5rem; font-weight: 900; 
        color: var(--mg-red); line-height: 1; margin-bottom: 5px; letter-spacing: -1px;
    }

    .btn-action {
        border: none; border-radius: 18px; padding: 15px; font-size: 1rem;
        font-weight: 900; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: 0.2s; cursor: pointer;
    }
    .btn-finish { background: var(--mg-green); color: white; flex-grow: 1; }
    .btn-hold { background: var(--hold-gray); color: white; width: 60px; }
    .btn-main { background: var(--mg-blue); color: white; width: 100%; }
</style>

<div class="dashboard-wrapper">
    <div class="summary-header">
        <div class="d-flex gap-2">
            <div class="badge bg-light text-dark px-3 py-2 rounded-pill border">🚗 Total: {{ $jobs->count() }}</div>
            <div class="badge bg-light text-danger px-3 py-2 rounded-pill border">🛠️ Doing: {{ $jobs->where('status', 'In Progress')->count() }}</div>
        </div>
        <div class="live-clock" id="liveClock">00:00:00</div>
        <select id="branchFilter" class="form-select border-0 shadow-sm rounded-pill fw-bold" style="width: 200px; font-size: 0.8rem;">
            <option value="all">📍 All Branches</option>
            @foreach(['សាខាការិយាល័យកណ្តាល', 'សាខាផ្លូវលេខ ៦០ម៉ែត្រ', 'សាខាសៀមរាប', 'សាខាបាត់ដំបង'] as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>
    </div>

    <div class="jobs-grid" id="jobsGrid">
        @php $now = \Carbon\Carbon::now(); @endphp
        @forelse($jobs as $job)
        @php 
            $onlyDate = date('Y-m-d', strtotime($job->booking_date));
            // បង្កើត ISO String សម្រាប់ឱ្យ JavaScript យកទៅគណនា (Fix Timer Not Live)
            $timerStart = ($job->status == 'In Progress' && $job->start_time) ? \Carbon\Carbon::parse($onlyDate . ' ' . $job->start_time)->toIso8601String() : '';
        @endphp

        <div class="job-item" data-branch="{{ $job->location }}">
            <div class="job-card status-highlight-{{ strtolower(str_replace(' ', '-', $job->status)) }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="license-plate shadow-sm">
                        <div class="plate-top">ភ្នំពេញ</div>
                        <div class="plate-num">{{ $job->plate }}</div>
                        <div class="plate-bot">CAMBODIA</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-muted border" style="font-size: 0.6rem;">{{ $job->location }}</span>
                        <div style="font-family: var(--font-primary); font-weight: 900; color: #6366f1; font-size: 1.5rem;">{{ date('h:i A', strtotime($job->booking_time)) }}</div>
                    </div>
                </div>

                <h3 class="cust-name text-truncate">{{ $job->customer_name }}</h3>
                <div class="mb-1">
                    <span class="badge bg-light text-dark border py-1" style="font-size: 0.7rem;">{{ $job->model }}</span> 
                    <span class="badge bg-danger py-1" style="font-size: 0.65rem;">{{ $job->service_type }}</span>
                </div>
                
                <div class="status-info-box">
                    @if($job->status == 'In Progress') <b>កំពុងធ្វើ</b> (ជាង៖ {{ $job->technician_name }})
                    @elseif($job->status == 'Arrived') <b>មកដល់ហើយ</b> (រង់ចាំ Start)
                    @elseif($job->status == 'Hold') <b>ផ្អាកបណ្ដោះអាសន្ន</b>
                    @else <b>រង់ចាំអតិថិជន</b> @endif
                </div>

                <div class="contact-row">
                    <span class="fw-bold text-dark">{{ $job->contact }}</span>
                    <div class="d-flex gap-2">
                        @php $tgP = str_starts_with(preg_replace('/[^0-9]/', '', $job->contact), '0') ? '855'.substr(preg_replace('/[^0-9]/', '', $job->contact), 1) : preg_replace('/[^0-9]/', '', $job->contact); @endphp
                        <a href="https://t.me/+{{ $tgP }}" target="_blank" class="text-primary"><i class="fab fa-telegram-plane"></i></a>
                        <a href="tel:{{ $job->contact }}" class="text-success"><i class="fas fa-phone-alt"></i></a>
                    </div>
                </div>

                <div class="mt-auto">
                    @if($job->status == 'In Progress')
                        <div class="text-center mb-3">
                            <div class="timer-display" data-start="{{ $timerStart }}">00:00:00</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn-action btn-hold" onclick="confirmHold('{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-pause"></i></button>
                            <button class="btn-action btn-finish" onclick="confirmFinish('{{ $job->id }}')">Finish</button>
                        </div>
                    @elseif($job->status == 'Hold')
                        <button class="btn-action btn-main" onclick="confirmResume('{{ $job->id }}', '{{ $job->plate }}')"><i class="fas fa-play"></i> Resume</button>
                    @elseif($job->status == 'Arrived')
                        <button class="btn-action btn-main" onclick="confirmStart('{{ $job->id }}', '{{ $job->plate }}')">Start Job</button>
                    @else
                        <button class="btn-action btn-main" style="background: var(--mg-orange);" onclick="confirmArrive('{{ $job->id }}', '{{ $job->plate }}')">Arrive</button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5"><h2>No Data</h2></div>
        @endforelse
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });

    // Function គណនាម៉ោងរត់ Live
    function updateRepairTimers() {
        document.querySelectorAll('.timer-display').forEach(t => {
            let startStr = t.dataset.start;
            if(!startStr) return;
            
            let startTime = new Date(startStr);
            let now = new Date();
            let diffMs = now - startTime;
            
            if (diffMs < 0) diffMs = 0;
            
            let diffSec = Math.floor(diffMs / 1000);
            let h = Math.floor(diffSec / 3600).toString().padStart(2, '0');
            let m = Math.floor((diffSec % 3600) / 60).toString().padStart(2, '0');
            let s = (diffSec % 60).toString().padStart(2, '0');
            
            t.innerText = h + ":" + m + ":" + s;
        });
    }

    // Update ម៉ោងរៀងរាល់ ១ វិនាទី
    setInterval(updateRepairTimers, 1000);

    // Live Clock ខាងលើ
    setInterval(() => { 
        let clock = document.getElementById('liveClock');
        if(clock) clock.innerText = new Date().toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' }); 
    }, 1000);

    // Refresh Dashboard រាល់ ៤០ វិនាទី
    function refresh() { 
        $.get(window.location.href, function(data) { 
            $('#jobsGrid').html($(data).find('#jobsGrid').html()); 
            updateRepairTimers(); 
        }); 
    }
    setInterval(refresh, 40000);

    // Actions
    function confirmArrive(id, plate) { $.post("{{ url('workshop/arrive') }}/"+id).done(refresh); }
    function confirmStart(id, plate) {
        Swal.fire({ 
            title: 'Assign Staff', 
            html: `<select id="sa_name" class="form-select mb-2">@foreach(['Chap Pov', 'Nang Kimyeang', 'Poeurn Hengleap'] as $sa) <option value="{{ $sa }}">{{ $sa }}</option> @endforeach</select>
                   <select id="tech_name" class="form-select">@foreach(['Keam Piseth', 'Lorn Vanthorn', 'Heang Tongchhay'] as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach</select>`,
            confirmButtonText: 'Start'
        }).then((res) => { if (res.isConfirmed) $.post("{{ url('workshop/start') }}/"+id, {sa_name: $('#sa_name').val(), technician_name: $('#tech_name').val()}).done(refresh); });
    }
    function confirmHold(id, plate) { $.post("{{ url('workshop/hold') }}/"+id).done(refresh); }
    function confirmResume(id, plate) { $.post("{{ url('workshop/resume') }}/"+id).done(refresh); }
    function confirmFinish(id) { $.post("{{ url('workshop/finish') }}/"+id).done(refresh); }
</script>
@endsection