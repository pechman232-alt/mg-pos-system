@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Kantumruy+Pro:wght@400;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --indigo: #4f46e5;
        --dark: #0f172a;
        --border: #e2e8f0;
        --bg-body: #f8fafc;
        --font-main: 'Plus Jakarta Sans', sans-serif;
        --font-kh: 'Kantumruy Pro', sans-serif;
    }

    body { background-color: var(--bg-body); color: var(--dark); font-family: var(--font-main); font-size: 0.88rem; }

    /* Card Layout */
    .card-view { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); background: #fff; overflow: hidden; margin-bottom: 2rem; }
    .header-accent { background: var(--dark); padding: 1.5rem 2.5rem; border: none; }
    
    /* Identity Header */
    .identity-container { background: #fff; padding: 2.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 2.5rem; }
    .avatar-view { width: 140px; height: 140px; border-radius: 24px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .name-en { font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; margin: 0; }
    .name-kh { font-family: var(--font-kh); font-size: 1.3rem; color: var(--indigo); font-weight: 700; margin-top: 4px; }

    /* Data Fields */
    .section-title { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin: 2.5rem 0 1.2rem; display: flex; align-items: center; gap: 15px; }
    .section-title::after { content: ""; height: 1px; flex: 1; background: var(--border); }
    
    .data-label { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
    .data-value { background: #fcfdfe; border: 1px solid #f1f5f9; border-radius: 10px; padding: 10px 14px; font-weight: 700; min-height: 42px; display: flex; align-items: center; justify-content: space-between; }
    .pw-text { font-family: 'JetBrains Mono', monospace; color: #ef4444; font-size: 0.8rem; }

    /* Credentials Card */
    .sys-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 15px; height: 100%; transition: 0.2s; }
    .sys-card:hover { border-color: var(--indigo); background: #fcfdfe; }

    /* Print Configuration (A4 Professional) */
    @media print {
        @page { size: A4; margin: 15mm; }
        body { background: #fff; padding: 0; }
        .no-print { display: none !important; }
        .card-view { box-shadow: none; border: none; border-radius: 0; }
        .identity-container { padding: 1rem 0; gap: 2rem; border-bottom: 2px solid #000; }
        .avatar-view { width: 120px; height: 120px; border: 1px solid #ddd; }
        .data-value { border: none; padding: 5px 0; background: none; border-bottom: 1px solid #eee; border-radius: 0; min-height: auto; }
        .sys-card { border: none; border-bottom: 1px dashed #ccc; border-radius: 0; padding: 10px 0; }
        .header-accent { background: #eee !important; color: #000 !important; }
        .header-accent h5 { color: #000 !important; }
        .section-title { margin: 1.5rem 0 1rem; color: #000; }
        .section-title::after { background: #000; }
    }
</style>

<div class="container-xxl mt-4">
    <div class="card-view">
        <div class="header-accent d-flex justify-content-between align-items-center no-print">
            <h5 class="text-white m-0 fw-bold"><i class="fas fa-id-card-alt me-2"></i> PERSONNEL RECORD</h5>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-print me-2"></i> PRINT A4
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-4 fw-bold">DIRECTORY</a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="identity-container">
                <img src="{{ $employees->image_employee ? url('/imageEmployee/' . $employees->image_employee) : url('backend/assets/img/avatars/1.png') }}" class="avatar-view">
                <div class="flex-grow-1">
                    <h1 class="name-en">{{ $employees->full_name }}</h1>
                    <div class="name-kh">{{ $employees->first_name_kh }} {{ $employees->last_name_kh }}</div>
                    <div class="mt-4 d-flex gap-3 align-items-center">
                        <span class="badge bg-dark rounded-pill px-3 py-2">ID: {{ $employees->user_id }}</span>
                        <span class="fw-bold" style="color: {{ $employees->status == 1 ? '#10b981' : '#ef4444' }}; font-size: 0.75rem;">
                            {{ $employees->status == 1 ? '● SYSTEM ACTIVE' : '● DEACTIVATED' }}
                        </span>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="data-label">Join Date</div>
                    <div class="fw-bold text-dark h5 mb-0">{{ $employees->contract ?? '---' }}</div>
                </div>
            </div>

            <div class="px-5 pb-5">
                <div class="section-title">Personnel Demographics</div>
                <div class="row g-4">
                    <div class="col-3">
                        <div class="data-label">Gender</div>
                        <div class="data-value">{{ $employees->gender }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Age / Lifecycle</div>
                        <div class="data-value">{{ $employees->dob ? \Carbon\Carbon::parse($employees->dob)->age : 'N/A' }} Years</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Date of Birth</div>
                        <div class="data-value">{{ $employees->dob ?? '---' }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Primary Contact</div>
                        <div class="data-value text-primary">{{ $employees->phone_number ?? '---' }}</div>
                    </div>
                </div>

                <div class="section-title">Organization & Role</div>
                <div class="row g-4">
                    <div class="col-4">
                        <div class="data-label">Parent Entity</div>
                        <div class="data-value">{{ $employees->company->company_name ?? $employees->companyName }}</div>
                    </div>
                    <div class="col-4">
                        <div class="data-label">Department</div>
                        <div class="data-value">{{ $employees->department->name ?? $employees->departmentName }}</div>
                    </div>
                    <div class="col-4">
                        <div class="data-label">Position</div>
                        <div class="data-value fw-bold text-indigo">{{ $employees->position->name ?? $employees->postionName }}</div>
                    </div>
                </div>

                <div class="section-title">Digital Infrastructure (Credentials)</div>
                <div class="row g-3">
                    @php
                        $sysList = [
                            ['label' => 'Official Portal', 'u' => $employees->official_userid, 'p' => $employees->official_pass, 'icon' => 'fa-desktop'],
                            ['label' => 'M365 / Email', 'u' => $employees->m_user_id, 'p' => $employees->m_pass, 'icon' => 'fa-envelope'],
                            ['label' => 'NAS Storage', 'u' => $employees->s_user_id, 'p' => $employees->s_pass, 'icon' => 'fa-server']
                        ];
                    @endphp
                    @foreach($sysList as $sys)
                    <div class="col-md-4">
                        <div class="sys-card shadow-sm">
                            <div class="data-label mb-2"><i class="fas {{ $sys['icon'] }} me-1"></i> {{ $sys['label'] }}</div>
                            <div class="data-value mb-1 fw-normal small">User: <b>{{ $sys['u'] ?? '-' }}</b></div>
                            <div class="data-value pw-text small">Pass: <b>{{ $sys['p'] ?? '-' }}</b></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="section-title">Facilities & Security</div>
                <div class="row g-4">
                    <div class="col-3">
                        <div class="data-label">Extension</div>
                        <div class="data-value"># {{ $employees->extension_id ?? 'N/A' }}</div>
                    </div>
                    <div class="col-3">
                        <div class="data-label">Door Access Card</div>
                        <div class="data-value pw-text">{{ $employees->access_door ?? 'N/A' }}</div>
                    </div>
                    <div class="col-6">
                        <div class="data-label">Administrative Note</div>
                        <div class="data-value fw-normal" style="font-size: 0.75rem;">{{ $employees->note ?? 'No notes recorded.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 border-top text-center no-print">
            <a href="{{ route('employees.edit', encrypt($employees->id)) }}" class="btn btn-indigo px-5 py-3 rounded-pill fw-bold shadow-sm">
                <i class="fas fa-edit me-2"></i> EDIT INFORMATION
            </a>
        </div>
    </div>
</div>

@endsection