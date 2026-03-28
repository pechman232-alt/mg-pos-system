@extends('layouts.homePages')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-indigo: #4f46e5;
        --exec-dark: #0f172a;
        --bg-body: #f8fafc;
        --border-light: #e2e8f0;
    }

    body { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-body); 
        color: #334155;
    }

    .container-full {
        width: 100% !important;
        max-width: 100% !important;
        padding: 30px 50px !important;
    }

    /* 🎬 ANIMATION ENTRANCE */
    @keyframes revealUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-view { animation: revealUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    /* 📋 MAIN CARD STRUCTURE */
    .card-view { 
        border: 1px solid var(--border-light); 
        border-radius: 35px; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.04); 
        background: #fff; overflow: hidden; 
        width: 100%; max-width: 1450px; margin: 0 auto;
    }

    /* 🎨 PROFESSIONAL DYNAMIC HEADER */
    .header-view { 
        padding: 50px 60px; 
        color: white; 
        display: flex; justify-content: space-between; align-items: center;
        position: relative;
        background: {{ $issue->status == 'Resolved' ? 'linear-gradient(135deg, #059669 0%, #10b981 100%)' : ($issue->status == 'Closed' ? 'linear-gradient(135deg, #475569 0%, #1e293b 100%)' : 'linear-gradient(135deg, #1e293b 0%, #4f46e5 100%)') }};
    }
    
    .header-view::after {
        content: ""; position: absolute; top: 0; right: 0; bottom: 0; left: 0;
        background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
        opacity: 0.1; pointer-events: none;
    }

    .header-info h2 { font-weight: 800; font-size: 1.8rem; margin: 0; letter-spacing: 1px; font-family: 'JetBrains Mono'; }
    .header-info span { font-weight: 700; font-size: 0.75rem; opacity: 0.7; letter-spacing: 4px; text-transform: uppercase; }

    .status-indicator-badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 20px; border-radius: 12px; font-weight: 800; font-size: 0.75rem;
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); 
        backdrop-filter: blur(10px); color: white; margin-top: 15px;
    }

    /* 📊 INFO SECTION TILES */
    .report-tile { 
        background: #fcfdfe; border-radius: 24px; padding: 25px; 
        border: 1.5px solid #f1f5f9; height: 100%; 
        transition: all 0.3s ease;
    }
    .report-tile:hover { 
        background: #fff; transform: translateY(-5px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); border-color: var(--brand-indigo); 
    }
    
    .label-accent { font-weight: 800; color: #94a3b8; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; display: block; margin-bottom: 12px; }
    .value-bold { font-weight: 800; color: var(--exec-dark); font-size: 1.15rem; line-height: 1.2; }
    .kh-label { font-family: 'Noto Sans Khmer'; font-weight: 700; font-size: 0.85rem; color: var(--brand-indigo); display: block; margin-top: 5px; }

    /* 📝 TEXT CONTENT BOXES */
    .content-box-title { 
        font-weight: 800; font-size: 0.75rem; color: #64748b; 
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;
        display: flex; align-items: center; gap: 10px;
    }

    .desc-wrapper { 
        background: #fff; border: 1.5px solid #f1f5f9; border-left: 6px solid var(--brand-indigo); 
        padding: 35px; border-radius: 20px; font-size: 1.05rem; 
        line-height: 1.8; color: #334155; box-shadow: 0 5px 15px rgba(0,0,0,0.01);
    }

    .remark-wrapper {
        background: #fff; border: 1.5px solid #f1f5f9; border-left: 6px solid #f59e0b;
        padding: 35px; border-radius: 20px; font-size: 1.05rem;
        line-height: 1.8; color: #92400e; box-shadow: 0 5px 15px rgba(0,0,0,0.01);
    }

    /* ⚙️ ACTION BAR */
    .action-bar { padding: 40px 60px; background: #fafbfc; border-top: 1.5px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    
    .btn-exec { 
        padding: 18px 35px; border-radius: 16px; font-weight: 800; font-size: 0.9rem; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        text-transform: uppercase; border: none; display: flex; align-items: center; gap: 12px;
        cursor: pointer; text-decoration: none;
    }
    
    .btn-resolve-final { background: #10b981; color: white; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }
    .btn-resolve-final:hover { background: #059669; transform: scale(1.05) translateY(-3px); box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3); }
    
    .btn-modify-intel { background: #fff; color: #475569; border: 1.5px solid #e2e8f0; }
    .btn-modify-intel:hover { background: var(--exec-dark); color: white; border-color: var(--exec-dark); transform: translateY(-3px); }

    .btn-return {
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
        color: white; padding: 12px 25px; border-radius: 14px;
        font-weight: 800; font-size: 0.75rem; text-decoration: none; transition: 0.3s;
    }
    .btn-return:hover { background: white; color: var(--exec-dark); }
</style>

<div class="container-full animate-view">
    <div class="card-view">
        <div class="header-view">
            <div class="header-info">
                <span><i class="fa-solid fa-microchip me-2"></i> DATA ANALYTICS LOG</span>
                <h2>REPORT IDENTIFIER: {{ $issue->issue_id }}</h2>
                <div class="status-indicator-badge">
                    <i class="fa-solid fa-circle-dot {{ $issue->status == 'Open' ? 'text-warning' : 'text-light' }}"></i> 
                    LIFECYCLE: {{ strtoupper($issue->status) }}
                </div>
            </div>
            
            <a href="{{ route('issues.index') }}" class="btn-return">
                <i class="fa-solid fa-arrow-left-long me-2"></i> EXIT TO DIRECTORY
            </a>
        </div>

        <div class="p-5">
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="report-tile" style="border-left: 5px solid var(--brand-indigo);">
                        <span class="label-accent">Case Subject</span>
                        <div class="value-bold">{{ $issue->subject }}</div>
                        <span class="kh-label">កម្មវត្ថុនៃបញ្ហា</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-tile" style="border-left: 5px solid #a855f7;">
                        <span class="label-accent">Classification</span>
                        <div class="value-bold">{{ $issue->category }}</div>
                        <div class="small text-muted fw-700 mt-1 opacity-50">SCOPE: {{ strtoupper($issue->department) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="report-tile" style="border-left: 5px solid #ef4444;">
                        <span class="label-accent">Intelligence Level</span>
                        <div class="value-bold" style="color: {{ $issue->priority == 'Urgent' ? '#ef4444' : ($issue->priority == 'High' ? '#f59e0b' : '#6366f1') }}">
                            <i class="fa-solid fa-shield-halved me-1"></i> {{ strtoupper($issue->priority) }}
                        </div>
                        <span class="kh-label">កម្រិតអាទិភាព</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="report-tile">
                        <span class="label-accent">Originating Personnel</span>
                        <div class="d-flex align-items-center gap-3 mt-2">
                            <div style="width: 50px; height: 50px; background: #eef2ff; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: var(--brand-indigo); border: 1px solid #c7d2fe;">
                                <i class="fa-solid fa-user-secret fa-lg"></i>
                            </div>
                            <div>
                                <div class="value-bold">{{ $issue->reported_by }}</div>
                                <div class="small text-muted fw-800" style="font-size: 0.65rem;">AUTHORIZED SYSTEM USER</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="report-tile">
                        <span class="label-accent">Temporal Metadata</span>
                        <div class="value-bold mt-2"><i class="fa-solid fa-clock-rotate-left me-2 text-indigo-500"></i> {{ $issue->created_at->format('d M Y | h:i A') }}</div>
                        <div class="small text-muted fw-800 mt-1" style="font-size: 0.65rem;">SYNCHRONIZED: {{ strtoupper($issue->created_at->diffForHumans()) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-xl-6">
                    <div class="content-box-title"><i class="fa-solid fa-file-waveform"></i> Primary Incident Log</div>
                    <div class="desc-wrapper">
                        {!! nl2br(e($issue->description)) !!}
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="content-box-title"><i class="fa-solid fa-comment-medical"></i> Administrative Remark</div>
                    <div class="remark-wrapper">
                        @if($issue->admin_remark)
                            {!! nl2br(e($issue->admin_remark)) !!}
                        @else
                            <div class="text-center py-4 opacity-30">
                                <i class="fa-solid fa-box-open d-block mb-2 fa-2x"></i>
                                <span class="italic small fw-700">No administrative intelligence recorded yet.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <a href="{{ route('issues.edit', $issue->id) }}" class="btn-exec btn-modify-intel">
                <i class="fa-solid fa-fingerprint"></i> Modify Intelligence
            </a>

            <div class="d-flex align-items-center gap-4">
                @if($issue->status != 'Resolved' && $issue->status != 'Closed')
                <form action="{{ route('issues.update', $issue->id) }}" method="POST" id="resolveForm">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="Resolved">
                    <button type="submit" class="btn-exec btn-resolve-final">
                        <i class="fa-solid fa-circle-check"></i> Execute Resolution
                    </button>
                </form>
                @elseif($issue->status == 'Resolved')
                <div class="d-flex align-items-center gap-2" style="color: #059669; font-weight: 900; letter-spacing: 1px;">
                    <i class="fa-solid fa-certificate fa-2x"></i> 
                    <div>
                        <div style="font-size: 0.9rem;">CASE VERIFIED</div>
                        <div style="font-size: 0.6rem; opacity: 0.7;">OPERATIONAL SUCCESS</div>
                    </div>
                </div>
                @else
                <div class="text-muted fw-900 d-flex align-items-center gap-2" style="letter-spacing: 1px;">
                    <i class="fa-solid fa-box-archive fa-lg"></i> RECORD ARCHIVED
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('#resolveForm').on('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: '<span style="font-family:Plus Jakarta Sans">Verify Resolution?</span>',
            text: "Are you sure this intelligence operation is complete?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'YES, COMMIT SUCCESS',
            borderRadius: '25px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Synchronizing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                this.submit();
            }
        });
    });
</script>
@endsection