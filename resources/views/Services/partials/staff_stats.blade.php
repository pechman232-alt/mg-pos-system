<style>
    :root {
        --mg-red: #e31e24;
        --mg-blue: #002663;
        --mg-success: #10b981;
        --mg-danger: #ef4444;
        --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Card Styling */
    .team-card {
        border-radius: 24px;
        background: #ffffff;
        border: none;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card-header-custom {
        padding: 1.5rem 1.25rem;
        background: transparent;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* List Item Styling (ជំនួស Table Row) */
    .staff-row {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 18px;
        margin-bottom: 12px;
        padding: 12px 16px;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .staff-row:hover {
        transform: translateX(8px);
        border-color: var(--mg-blue);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    /* Status Indicators */
    .status-box {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 100px;
        justify-content: center;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .is-busy-dot {
        background-color: var(--mg-danger);
        box-shadow: 0 0 12px var(--mg-danger);
        animation: pulse-red 2s infinite;
    }

    .is-free-dot {
        background-color: var(--mg-success);
        box-shadow: 0 0 12px var(--mg-success);
    }

    /* Avatar & Text */
    .staff-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .avatar-circle-new {
        width: 42px;
        height: 42px;
        border-radius: 12px; /* រាងជ្រុងមូល បែប App ទំនើប */
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--mg-blue);
        border: 2px solid #fff;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    /* Performance Stats */
    .stat-box {
        text-align: right;
        min-width: 80px;
    }

    .performance-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        font-weight: 700;
        margin-top: 2px;
    }

    .car-count-badge {
        background: var(--mg-red);
        color: white;
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(227, 30, 36, 0.2);
    }

    @keyframes pulse-red {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>




<div class="row g-4 mb-4 animate__animated animate__fadeIn">
    
    <div class="col-lg-7">
        <div class="card team-card h-100">
            <div class="card-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-success bg-opacity-10 rounded-3">
                        <i class="fas fa-screwdriver-wrench text-success"></i>
                    </div>
                    <h6 class="fw-800 mb-0">ក្រុមមេជាង (Workshop Team)</h6>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border-0 px-3 py-2" style="font-size: 0.6rem;">FREE</span>
                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border-0 px-3 py-2" style="font-size: 0.6rem;">BUSY</span>
                </div>
            </div>
            
            <div class="px-4 pb-4">
                @php $allTechs = ['Lorn Vanthorn', 'Heang Tongchhay', 'Keam Piseth', 'Sum Thoeunrethy', 'Han Hak']; @endphp
                @foreach($allTechs as $tName)
                    @php 
                        $isBusy = $jobs->where('status', 'In Progress')->where('technician_name', $tName)->isNotEmpty();
                        $countDone = $technicianStats->where('technician_name', $tName)->first()->total ?? 0;
                    @endphp
                    <div class="staff-row">
                        <div class="staff-info">
                            <div class="avatar-circle-new">{{ substr($tName, 0, 1) }}</div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $tName }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">Technician Specialist</div>
                            </div>
                        </div>
                        
                        <div class="status-box">
                            <div class="status-dot {{ $isBusy ? 'is-busy-dot' : 'is-free-dot' }}"></div>
                            <span class="small fw-bold {{ $isBusy ? 'text-danger' : 'text-success' }}">
                                {{ $isBusy ? 'Working' : 'Available' }}
                            </span>
                        </div>

                        <div class="stat-box">
                            <div class="fw-800 text-primary" style="font-size: 1rem;">{{ $countDone }}</div>
                            <div class="performance-label">Done</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card team-card h-100">
            <div class="card-header-custom">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                        <i class="fas fa-user-tie text-primary"></i>
                    </div>
                    <h6 class="fw-800 mb-0">ទីប្រឹក្សាសេវាកម្ម (SA)</h6>
                </div>
            </div>
            
            <div class="px-4 pb-4">
                @php $allSAs = ['Chap Pov', 'Nang Kimyeang', 'Poeurn Hengleap']; @endphp
                @foreach($allSAs as $saName)
                    @php 
                        $activeJobs = $jobs->where('status', 'In Progress')->where('sa_name', $saName)->count();
                        $totalDone = $saStats->where('sa_name', $saName)->first()->total ?? 0;
                    @endphp
                    <div class="staff-row">
                        <div class="staff-info">
                            <div class="avatar-circle-new" style="background: #eef2ff;">{{ substr($saName, 0, 1) }}</div>
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $saName }}</div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-4">
                            @if($activeJobs > 0)
                                <div class="car-count-badge animate__animated animate__pulse animate__infinite">
                                    <i class="fas fa-car-side me-1"></i> {{ $activeJobs }}
                                </div>
                            @endif
                            <div class="stat-box">
                                <div class="fw-800 text-dark">{{ $totalDone }}</div>
                                <div class="performance-label">Jobs</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>