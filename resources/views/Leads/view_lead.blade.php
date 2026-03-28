@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --brand-indigo: #4f46e5;
        --brand-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --exec-slate: #0f172a;
        --text-muted: #64748b;
        --bg-main: #f8fafc;
        --border-soft: #f1f5f9;
    }

    body { background-color: var(--bg-main); font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; color: var(--exec-slate); }
    
    .container-full { width: 100%; padding: 15px; }
    @media (min-width: 992px) { .container-full { padding: 30px 50px; } }

    /* ✨ Smooth Entry Page Animation */
    .container-full { animation: fadeInScale 0.6s ease-out both; }
    @keyframes fadeInScale { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    /* 🏷️ Header Design */
    .exec-header-glass {
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);
        border: 1px solid var(--border-soft); border-radius: 24px;
        padding: 20px 30px; margin-bottom: 25px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }
    .header-title-main { font-weight: 800; font-size: 1.4rem; margin: 0; background: var(--brand-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px; }

    /* 🏎️ Stepper / Pipeline */
    .pipeline-container { background: #fff; padding: 30px 20px; border-radius: 24px; border: 1px solid var(--border-soft); margin-bottom: 30px; overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); }
    .pipeline-inner { display: flex; justify-content: space-between; min-width: 600px; }
    .pipeline-step { flex: 1; text-align: center; }
    .step-dot { 
        width: 38px; height: 38px; background: #fff; border: 2px solid #e2e8f0; 
        border-radius: 12px; margin: 0 auto 10px; display: flex; align-items: center; 
        justify-content: center; font-weight: 800; font-size: 0.9rem; color: var(--text-muted); transition: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .step-active .step-dot { background: var(--brand-indigo); color: #fff; border-color: var(--brand-indigo); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); transform: scale(1.1); }
    .step-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
    .step-active .step-label { color: var(--brand-indigo); }

    /* 📊 Content Cards */
    .info-card { background: #fff; border-radius: 24px; padding: 25px; margin-bottom: 25px; border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0,0,0,0.03); transition: 0.3s; }
    .data-row { display: flex; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f8fafc; align-items: center; }
    .data-label { font-size: 0.68rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
    .data-val { font-size: 0.88rem; font-weight: 800; color: var(--exec-slate); }

    /* 📜 Intelligence Logs / Timeline */
    .audit-trail { padding-left: 20px; border-left: 2.5px dashed #e2e8f0; position: relative; margin-left: 10px; }
    .trail-item { position: relative; margin-bottom: 25px; }
    .trail-marker { position: absolute; left: -30.5px; top: 12px; width: 18px; height: 18px; background: #fff; border: 4px solid var(--brand-indigo); border-radius: 50%; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    .trail-card { background: #f8fafc; padding: 20px; border-radius: 20px; border-left: 6px solid var(--brand-indigo); transition: 0.3s ease; }
    .trail-card:hover { transform: translateX(8px); background: #fff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }

    /* 🔘 Premium Sync Button */
    .btn-sync-exec { 
        background: var(--brand-gradient); color: #fff !important; 
        border: none; padding: 10px 25px; border-radius: 14px; 
        font-weight: 800; font-size: 0.8rem; text-transform: uppercase; 
        letter-spacing: 1px; transition: 0.3s; box-shadow: 0 10px 15px rgba(79, 70, 229, 0.2); 
    }
    .btn-sync-exec:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(79, 70, 229, 0.3); }

    /* 🚀 SweetAlert2 Custom Sync Card */
    .swal-sync-card { border-radius: 28px !important; padding: 20px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; }
    .swal-sync-card select, .swal-sync-card textarea { border-radius: 14px !important; border: 1.5px solid #f1f5f9 !important; padding: 12px !important; }
</style>

<div class="container-full">
    @php
        $statusKey = $lead->status;
        $isLocked = in_array(strtolower($statusKey), ['delivery', 'won', 'lost']);
        $steps = ['Interest' => '១', 'Need' => '២', 'Survey' => '៣', 'Book' => '៤', 'Delivery' => '៥'];
        $stepsKh = ['Interest' => 'ចាប់អារម្មណ៍', 'Need' => 'ស្វែងយល់', 'Survey' => 'ពិភាក្សា', 'Book' => 'កក់ប្រាក់', 'Delivery' => 'ប្រគល់ឡាន'];
        $stepKeys = array_keys($steps);
        $currIdx = array_search($statusKey, $stepKeys) !== false ? array_search($statusKey, $stepKeys) : 0;
        
        // Telegram Phone Format
        $cleanPhone = preg_replace('/[^0-9]/', '', $lead->contact_info);
        $tgPhone = (strpos($cleanPhone, '0') === 0) ? substr($cleanPhone, 1) : $cleanPhone;
    @endphp

    <div class="exec-header-glass">
        <div>
            <span class="small fw-800 text-muted text-uppercase mb-1 d-block" style="letter-spacing: 1.5px;">Lead Intelligence</span>
            <h2 class="header-title-main">{{ strtoupper($lead->customer_name) }}</h2>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill fw-800 px-4 border shadow-sm">BACK</a>
            @if(!$isLocked)
                <button class="btn btn-sync-exec btn-quick-sync" 
                        data-id="{{ $lead->id }}" data-name="{{ $lead->customer_name }}" data-status="{{ $lead->status }}">
                    <i class="fas fa-sync-alt me-2"></i> SYNC STATUS
                </button>
            @else
                <button class="btn btn-light btn-sm rounded-pill fw-800 px-4 text-muted border shadow-none" disabled>
                    <i class="fas fa-lock me-2"></i> FINALIZED
                </button>
            @endif
        </div>
    </div>

    <div class="pipeline-container">
        <div class="pipeline-inner">
            @foreach ($steps as $en => $num)
                @php $index = array_search($en, $stepKeys); @endphp
                <div class="pipeline-step {{ $index <= $currIdx ? 'step-active' : '' }}">
                    <div class="step-dot">{{ $num }}</div>
                    <div class="step-label">{{ $stepsKh[$en] }}<br><small style="opacity: 0.5; font-size: 0.5rem;">{{ $en }}</small></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="info-card">
                <h6 class="fw-800 text-uppercase mb-4 small text-primary d-flex align-items-center"><i class="fas fa-info-circle me-2"></i> Specification Overview</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="data-row"><span class="data-label">Sales Consultant</span><span class="data-val">{{ $lead->sales_person }}</span></div>
                        <div class="data-row"><span class="data-label">Assigned Branch</span><span class="data-val text-primary fw-800">{{ $lead->branch }}</span></div>
                    </div>
                    <div class="col-md-6">
                        <div class="data-row"><span class="data-label">Client Location</span><span class="data-val">{{ $lead->location }}</span></div>
                        <div class="data-row"><span class="data-label">Lead Source</span><span class="data-val"><span class="badge bg-light text-dark border fw-700">{{ $lead->source }}</span></span></div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h6 class="fw-800 text-uppercase mb-4 small text-primary"><i class="fas fa-stream me-2"></i> Activity Intelligence Logs</h6>
                <div class="audit-trail">
                    @foreach ($lead->activities->sortByDesc('created_at') as $activity)
                        <div class="trail-item">
                            <div class="trail-marker" style="border-color: {{ strtolower($activity->status) == 'delivery' ? '#10b981' : (strtolower($activity->status) == 'lost' ? '#f43f5e' : '#4f46e5') }};"></div>
                            <div class="trail-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-800 small text-uppercase" style="font-size: 0.65rem; color: var(--brand-indigo);">STAGE: {{ $activity->status }}</div>
                                        <p class="m-0 small fw-600 text-dark mt-2" style="line-height: 1.6;">{{ $activity->comment }}</p>
                                        <div class="mt-2 small text-muted"><i class="fas fa-user-circle me-1"></i> Staff: <b>{{ $activity->user->full_name ?? 'System' }}</b></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-800 text-dark small">{{ $activity->created_at->format('d M Y') }}</div>
                                        <div class="text-muted" style="font-size: 0.6rem;">{{ $activity->created_at->format('h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="info-card bg-dark text-white shadow-lg sticky-top border-0" style="top: 25px;">
                <div class="p-2">
                    <h6 class="fw-800 text-uppercase small opacity-50 mb-4" style="letter-spacing: 1px;">
                        <i class="fas fa-bolt text-warning me-2"></i> Client Engagement
                    </h6>
                    
                    <div class="text-center py-4 mb-4 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <span class="d-block small text-white-50 mb-1 fw-700">PROSPECTIVE ASSET</span>
                        <h2 class="fw-800 mb-0 text-white" style="letter-spacing: -1px;">{{ $lead->model ?? 'N/A' }}</h2>
                    </div>
                    
                    <div class="mb-4">
                        <div class="data-row border-0 py-2">
                            <span class="data-label text-white-50">Authorized Owner</span>
                            <span class="data-val text-white fw-800">{{ strtoupper($lead->customer_name) }}</span>
                        </div>
                        <div class="data-row py-2" style="border-color: rgba(255,255,255,0.1);">
                            <span class="data-label text-white-50">Commitment (Monthly)</span>
                            <span class="data-val text-success h4 fw-800 mb-0">${{ number_format($lead->mp ?? 0, 2) }}</span>
                        </div>
                    </div>
        
                    <div class="text-center mb-4">
                        <div class="d-inline-block bg-white p-2 rounded-4 shadow-sm">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:{{ $lead->contact_info }}" class="btn btn-success rounded-4 fw-800 py-3">
                            <i class="fas fa-phone-alt me-2"></i> CALL CLIENT
                        </a>
                        <a href="https://t.me/+855{{ $tgPhone }}" target="_blank" class="btn btn-info text-white rounded-4 fw-800 py-3" style="background: #0ea5e9;">
                            <i class="fab fa-telegram-plane me-2"></i> SEND TELEGRAM
                        </a>
                    </div>
                </div>
            </div>
        </div>

<script>
    // ⚡ Generate QR Code
    new QRCode(document.getElementById("qrcode"), { 
        text: "tel:{{ $lead->contact_info }}", 
        width: 130, height: 130,
        colorDark : "#0f172a", colorLight : "#ffffff"
    });

    // ⚡ Sync Intelligence Popup (with Smooth Shadow & Animation)
    $(document).on('click', '.btn-quick-sync', function() {
        const id = $(this).data('id'), name = $(this).data('name'), currentStatus = $(this).data('status');
        
        Swal.fire({
            title: `<span style="font-family:'Noto Sans Khmer'; font-size:17px; font-weight:800;">Sync Intelligence: ${name}</span>`,
            html: `
                <div class="text-start mt-3" style="font-family:'Plus Jakarta Sans','Noto Sans Khmer';">
                    <label class="small fw-bold mb-2 text-muted text-uppercase">Target Pipeline Phase</label>
                    <select id="swal_status" class="form-select border-0 bg-light mb-3" style="padding:12px; border-radius:14px;">
                        <option value="Interest" ${currentStatus == 'Interest' ? 'selected' : ''}>១. Interest (ចាប់អារម្មណ៍)</option>
                        <option value="Need" ${currentStatus == 'Need' ? 'selected' : ''}>២. Need (ស្វែងយល់តម្រូវការ)</option>
                        <option value="Survey" ${currentStatus == 'Survey' ? 'selected' : ''}>៣. Survey (ពិភាក្សា)</option>
                        <option value="Book" ${currentStatus == 'Book' ? 'selected' : ''}>៤. Book (កក់ប្រាក់)</option>
                        <option value="Delivery" ${currentStatus == 'Delivery' ? 'selected' : ''}>៥. Delivery (ប្រគល់ឡាន)</option>
                        <option value="lost" ${currentStatus == 'lost' ? 'selected' : ''}>❌ Lost (បោះបង់)</option>
                    </select>
                    <label class="small fw-bold mb-2 text-muted text-uppercase">Operational Remark</label>
                    <textarea id="swal_comment" class="form-control border-0 bg-light" rows="4" style="padding:12px; border-radius:14px;" placeholder="Describe current situation..."></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'SYNC NOW',
            confirmButtonColor: '#4f46e5',
            showClass: { popup: 'animate__animated animate__zoomIn' }, // Bootstrap/Animate.css style
            customClass: { popup: 'swal-sync-card' },
            preConfirm: () => {
                const status = document.getElementById('swal_status').value;
                const comment = document.getElementById('swal_comment').value;
                if(!comment) { Swal.showValidationMessage('សូមបញ្ចូលកំណត់ចំណាំ Remark ផងបង!'); return false; }
                return { id, status, comment }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Syncing Data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.post("{{ url('/leads/quick-update') }}", { _token: "{{ csrf_token() }}", ...result.value }, function(res) {
                    if(res.success) location.reload();
                });
            }
        });
    });
</script>
@endsection