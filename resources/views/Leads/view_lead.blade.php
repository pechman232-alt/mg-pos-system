@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@php
    // 👤 ១. បង្កើត User Context
    $currentUser = auth('users')->user();
    $isAdminOrDirector = $currentUser ? in_array($currentUser->role_id, [1, 2, 6]) : false;

    // 🎯 ២. ទាញយក Instruction ចុងក្រោយ (ការពារ Undefined Variable)
    $latestInstruction = $lead->managerInstructions->first(); 

    // 🔒 ៣. LOCK LOGIC: Unlock 'Contract of sales', Lock only for 'Delivery' & 'Lost'
    $isClosed = in_array(strtolower($lead->status), ['delivery', 'lost']); 
    $isDelivered = strtolower($lead->status) == 'delivery';
    $isLost = strtolower($lead->status) == 'lost';

    // 📱 ៤. រៀបចំទិន្នន័យទូទៅ
    $phone = preg_replace('/[^0-9]/', '', $lead->contact_info);
    $tgPhone = (strpos($phone, '0') === 0) ? '855' . substr($phone, 1) : $phone;
    $currentRemind = $lead->remind_date ? $lead->remind_date->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'));

    // 🌟 ៥. រៀបចំទិន្នន័យ Stage Lock
    $pipelineStages = ['Prospecting', 'Appointment', 'Meet & Greet', 'Consulting ( Q & A )', 'Double Open', 'Presentation', 'Test Drive', 'Trail Close', 'Negotiation', 'Double Close', 'Sales ( contract of sales )', 'Delivery', 'Lost'];
    $currentLeadStatus = strtolower($lead->status);
    $currentStageIndex = array_search($currentLeadStatus, array_map('strtolower', $pipelineStages));
    if ($currentStageIndex === false) $currentStageIndex = 0;
@endphp

<style>
    :root {
        --crm-primary: #4f46e5;
        --crm-secondary: #0f172a;
        --crm-accent: #f59e0b;
        --crm-success: #10b981;
        --crm-danger: #e11d48;
        --crm-bg: #f8fafc;
        --crm-border: #e2e8f0;
    }

    body { background-color: var(--crm-bg); }
    #lead-details-app { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif; color: #1e293b; }

    /* 🚨 Manager Alert Bar */
    .manager-alert-bar {
        background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fde68a; border-left: 6px solid var(--crm-accent);
        border-radius: 16px; padding: 20px; margin-bottom: 30px;
    }

    /* 🔘 Professional Buttons */
    .ld-btn-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 30px; }
    .ld-btn-item {
        padding: 14px; border-radius: 14px; border: none; color: #fff; font-weight: 700; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: center; gap: 10px;
        transition: 0.3s; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .ld-btn-item:hover { transform: translateY(-3px); filter: brightness(1.1); }

    /* 📜 Connected Timeline Design */
    .ld-panel { background: #ffffff; border-radius: 24px; border: 1px solid var(--crm-border); padding: 25px; margin-bottom: 25px; }
    .ld-timeline-wrapper { position: relative; padding-left: 10px; }
    .ld-timeline-line { position: absolute; left: 35px; top: 0; bottom: 0; width: 2px; background: #e2e8f0; border-left: 2px dashed #cbd5e1; z-index: 1; }
    .ld-timeline-item { position: relative; margin-bottom: 40px; z-index: 2; }
    .ld-marker {
        position: absolute; left: 0; top: 0; width: 34px; height: 34px;
        background: #fff; border: 3px solid var(--crm-primary); border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: var(--crm-primary);
        box-shadow: 0 0 0 6px var(--crm-bg);
    }
    .ld-timeline-card { margin-left: 55px; background: #fff; border: 1px solid #edf2f7; border-radius: 18px; overflow: hidden; text-align: left !important; transition: 0.3s; }
    .ld-timeline-card:hover { border-color: var(--crm-primary); transform: translateX(5px); }

    /* 👤 Sidebar Style */
    .ld-sidebar-dark {
        background: var(--crm-secondary); color: #ffffff;
        border-radius: 28px; padding: 35px; position: sticky; top: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .ld-inner-card { background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 18px; text-align: center; }

    /* 👑 Manager Box */
    .ld-manager-box {
        background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.15);
        border-left: 5px solid var(--crm-accent); border-radius: 20px; padding: 22px; margin-top: 30px;
    }
    .mgr-textarea { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; border-radius: 14px; padding: 15px; width: 100%; }
</style>

<div id="lead-details-app" class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="fas fa-id-card-clip text-primary me-2"></i> Lead Profile: <span class="text-primary">{{ $lead->customer_name }}</span>
        </h5>
        <a href="{{ route('leads.index') }}" class="btn btn-white border  py-2 fw-bold shadow-sm rounded-pill" style="background:#fff;">
            <i class="fas fa-arrow-left me-2 text-muted"></i> BACK TO PIPELINE
        </a>
    </div>

    @if($latestInstruction)
        <div class="manager-alert-bar shadow-sm d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-4">
                <div style="width: 55px; height: 55px; background: var(--crm-accent); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 15px rgba(245, 158, 11, 0.2);">
                    <i class="fas fa-bolt-lightning fa-lg"></i>
                </div>
                <div>
                    <span style="font-size: 11px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 1px;">Management Instruction</span>
                    <div style="font-size: 1.15rem; font-weight: 700; color: #92400e; font-family: 'Kantumruy Pro'; line-height: 1.4;">
                        "{{ $latestInstruction->instruction }}"
                    </div>
                    <div class="small opacity-75 mt-1" style="font-size: 11px; font-weight: 600; color: #b45309;">
                        By {{ $latestInstruction->user->full_name }} • {{ $latestInstruction->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!$isClosed)
    <div class="ld-btn-group">
        <button class="ld-btn-item" style="background: var(--crm-primary)" onclick="openQuickNote('CALL', '📞 Client Discussion')"><i class="fas fa-phone-volume"></i> CALL LOG</button>
        <button class="ld-btn-item" style="background: #6366f1" onclick="openQuickNote('MEETING', '🏢 Direct Meeting')"><i class="fas fa-handshake"></i> MEETING</button>
        <button class="ld-btn-item" style="background: #0ea5e9" onclick="openReschedule()"><i class="fas fa-clock-rotate-left"></i> RESCHEDULE</button>
        <button class="ld-btn-item" style="background: var(--crm-accent)" onclick="openStatusUpdate()"><i class="fas fa-shuffle"></i> SYNC STATUS</button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            @if(!$isClosed)
            <div class="ld-panel shadow-sm border-0">
                <div class="fw-bold mb-3 text-muted small uppercase letter-spacing-1">INTERNAL FEEDBACK LOG</div>
                <div class="row g-3">
                    <div class="col-12"><textarea id="mainComment" class="form-control border-2 shadow-none" rows="2" style="border-radius:18px; background:#fcfcfd; padding:18px;" placeholder="Document the interaction..."></textarea></div>
                    <div class="col-md-5">
                        <select id="mainStatus" class="form-select fw-bold" style="border-radius:12px; height:50px;">
                            {{-- 🌟 ប្រើប្រាស់ Loop ដើម្បីការពារការរើសថយក្រោយ --}}
                            @foreach($pipelineStages as $index => $st)
                                @php
                                    $isDisabled = ($index < $currentStageIndex && strtolower($st) !== 'lost') ? 'disabled' : '';
                                    $isSelected = ($currentLeadStatus == strtolower($st)) ? 'selected' : '';
                                    $displayLabel = $isDisabled ? "🔒 " . strtoupper($st) . " (LOCKED)" : strtoupper($st);
                                @endphp
                                <option value="{{ $st }}" {{ $isDisabled }} {{ $isSelected }} {!! $isDisabled ? 'style="color:#cbd5e1;"' : '' !!}>{{ $displayLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><input type="date" id="mainRemind" class="form-control fw-bold" style="border-radius:12px; height:50px;" value="{{ $currentRemind }}"></div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100 fw-bold shadow-sm" style="height:50px; border-radius:12px;" onclick="saveFeedback()">
                            <i class="fas fa-paper-plane me-2"></i> SEND
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <div class="ld-panel shadow-sm border-0" style="border-radius: 24px;">
                <div class="ld-section-title" style="padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; margin-bottom: 30px;">
                    <i class="fas fa-stream text-primary"></i> ACTIVITY LOG HISTORY
                </div>

                <div class="ld-timeline-wrapper">
                    <div class="ld-timeline-line"></div>
                    @forelse ($lead->activities->sortByDesc('created_at') as $index => $activity)
                        @php 
                            $logIdx = $lead->activities->count() - $loop->index; 
                            $isSystem = str_contains($activity->comment, 'CLIENT:');
                            $stageName = strtoupper($activity->status);
                            
                            $stageColor = match($stageName) {
                                'DELIVERY' => ['bg' => '#10b981', 'text' => '#ffffff'],
                                'SALES ( CONTRACT OF SALES )' => ['bg' => '#dcfce7', 'text' => '#15803d'],
                                'LOST' => ['bg' => '#fee2e2', 'text' => '#b91c1c'],
                                default => ['bg' => '#f1f5f9', 'text' => '#475569'],
                            };
                        @endphp
                        <div class="ld-timeline-item">
                            <div class="ld-marker" style="border-color: {{ $stageName == 'DELIVERY' ? '#10b981' : 'var(--crm-primary)' }}">{{ $logIdx }}</div>
                            <div class="ld-timeline-card shadow-sm">
                                <div class="ld-card-header" style="background: #f8fafc; padding: 15px 22px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
                                    <div class="d-flex align-items-center gap-4">
                                        <div>
                                            <span class="text-muted d-block" style="font-size:8px; font-weight:800;">LOGGED TIMESTAMP</span>
                                            <span class="fw-bold small text-dark"><i class="far fa-clock me-1 text-primary"></i> {{ $activity->created_at->format('d M Y • h:i A') }}</span>
                                        </div>
                                        <div class="px-3 border-start">
                                            <span class="text-muted d-block" style="font-size:8px; font-weight:800;">STAGE</span>
                                            <span class="badge" style="background:{{ $stageColor['bg'] }}; color:{{ $stageColor['text'] }}; font-size:9px; font-weight:800;">{{ $stageName }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted d-block" style="font-size:8px; font-weight:800;">Updated by</span>
                                        <span class="fw-bold text-primary small"><i class="far fa-user-circle me-1"></i> {{ $activity->user->full_name ?? 'System' }}</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="ld-remark-area" style="font-family: 'Kantumruy Pro'; font-size: 0.95rem; line-height: 1.8; color: #334155;">
                                        @if($isSystem)
                                            <div style="background: #f0fdf4; border-radius: 12px; padding: 18px; border: 1px solid #dcfce7; color: #166534;">
                                                {!! str_replace("\n", "<br>", e($activity->comment)) !!}
                                            </div>
                                        @else
                                            <div style="padding-left: 15px; border-left: 3px solid #e2e8f0; color: #475569;">
                                                {!! nl2br(e($activity->comment)) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 opacity-50 small italic">No history found.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ld-sidebar-dark">
                <div class="text-center mb-5">
                    <div style="width: 85px; height: 85px; background: rgba(255,255,255,0.08); border-radius: 25px; display: flex; align-items: center; justify-content: center; font-size: 38px; margin: 0 auto 20px; border: 1px solid rgba(255,255,255,0.12);">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">{{ strtoupper($lead->customer_name) }}</h3>
                    
                    @if($isDelivered)
                        <div class="badge bg-success px-3 py-1 rounded-pill mb-2 shadow-sm" style="font-size: 10px; border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-check-circle me-1"></i> SUCCESSFUL DELIVERY
                        </div>
                    @elseif($isLost)
                        <div class="badge bg-danger px-3 py-1 rounded-pill mb-2 shadow-sm" style="font-size: 10px;">
                            <i class="fas fa-times-circle me-1"></i> CLOSED / LOST
                        </div>
                    @else
                        <span class="badge bg-white text-dark px-3 py-1 rounded-pill small fw-bold" style="font-size: 10px;">{{ $lead->model }}</span>
                    @endif
                    <div class="small opacity-50 mt-1 letter-spacing-1">{{ strtoupper($lead->status) }}</div>
                </div>

                <div class="mb-5">
                    <label class="small fw-bold opacity-40 mb-2 uppercase">Direct Contact</label>
                    <div class="ld-inner-card d-flex align-items-center gap-3">
                        <i class="fas fa-phone-volume text-success fs-3"></i>
                        <span class="fs-3 fw-bold text-white letter-spacing-1">{{ $lead->contact_info }}</span>
                    </div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-6"><div class="ld-inner-card"><span class="small opacity-40 mb-1 d-block text-start">BRANCH</span><div class="fw-bold text-warning text-start small">{{ str_replace('MG ', '', $lead->branch_name) }}</div></div></div>
                    <div class="col-6"><div class="ld-inner-card"><span class="small opacity-40 mb-1 d-block text-start">PROVINCE</span><div class="fw-bold text-white text-start small">{{ $lead->location ?? 'Global' }}</div></div></div>
                </div>

                <div class="d-grid gap-3 mb-5">
                    <a href="tel:{{ $lead->contact_info }}" class="btn btn-success py-3 fw-bold rounded-4 border-0 shadow-lg" style="background:#10b981;"><i class="fas fa-phone-alt me-2"></i> INITIATE CALL</a>
                    <a href="https://t.me/+{{ $tgPhone }}" target="_blank" class="btn btn-info py-3 fw-bold rounded-4 border-0 text-white" style="background:#0ea5e9;"><i class="fab fa-telegram-plane me-2"></i> TELEGRAM</a>
                </div>

                <div class="ld-manager-box">
                    <div class="ld-section-title text-warning mb-3 small fw-bold" style="letter-spacing: 1px;">Management Comment</div>
                    
                    @php
                        $currentUser = auth('users')->user();
                        $userRoleId = (int) $currentUser->role_id;
                        
                        // ១. កំណត់សិទ្ធិថ្នាក់លើ (Admin, Director, GM) - អាច Comment បានទាំងអស់
                        $isTopManagement = in_array($userRoleId, [1, 2, 6]); 
                        
                        // ២. កំណត់សិទ្ធិថ្នាក់គ្រប់គ្រងសាខា (Sales Managers)
                        $isBranchManager = in_array($userRoleId, [3, 4, 5, 7, 8]); 
                        
                        $canComment = false;
                
                        if ($isTopManagement) {
                            $canComment = true; // ថ្នាក់លើ អនុញ្ញាតឱ្យ Comment លើយកតែម្តង
                        } elseif ($isBranchManager) {
                            // បើជា Manager សាខា ត្រូវឆែកមើលថាតើសាខារបស់គាត់ ដូចគ្នានឹងសាខារបស់ Lead ដែរឬទេ
                            $myBranch = \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name');
                            
                            // កាត់ពាក្យ MG ចេញដើម្បីងាយស្រួលផ្ទៀងផ្ទាត់ (ឧទាហរណ៍ "MG 60M" និង "60M")
                            $cleanMyBranch = trim(strtolower(str_replace(['MG ', 'Motors'], '', $myBranch)));
                            $cleanLeadBranch = trim(strtolower(str_replace(['MG ', 'Motors'], '', $lead->branch_name ?? $lead->branch)));
                            
                            if ($cleanMyBranch == $cleanLeadBranch || str_contains($cleanLeadBranch, $cleanMyBranch)) {
                                $canComment = true; // អនុញ្ញាតឱ្យ Comment ព្រោះត្រូវសាខាគ្នា
                            }
                        }
                    @endphp
                
                    {{-- 🌟 បង្ហាញប្រអប់ Comment តែចំពោះអ្នកដែលមានសិទ្ធិ ($canComment = true) --}}
                    @if($canComment)
                        <div class="mb-4">
                            <textarea id="mgr_note_input" class="mgr-textarea mb-3" rows="3" placeholder="Enter instructions or strategic notes here..."></textarea>
                            <button class="btn btn-warning w-100 fw-bold rounded-4 py-2 shadow-sm text-dark" onclick="submitManagerNote()">PUBLISH ALERT</button>
                        </div>
                    @endif
                
                    <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                        @forelse($lead->managerInstructions as $item)
                            <div class="history-item">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold" style="font-size: 9px; color:rgba(255,255,255,0.8);">
                                        <i class="fas fa-user-shield me-1 text-warning"></i> 
                                        {{ $item->user->full_name }} 
                                        <span style="opacity: 0.6; font-weight: normal;">
                                            ({{ in_array($item->user->role_id, [1, 2, 6]) ? 'Director' : 'Manager' }})
                                        </span>
                                    </span>
                                    <span style="font-size: 8px; opacity: 0.5;">{{ $item->created_at->format('d M, h:i A') }}</span>
                                </div>
                                <div style="font-size: 0.88rem; color: #fde68a; line-height: 1.6; font-family: 'Kantumruy Pro';">
                                    {{ $item->instruction }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 opacity-25 small italic">No strategic notes logged.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // AJAX Engine
    function submitData(comment, remindDate, status) {
        Swal.fire({ title: 'Processing Transaction...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.post("{{ url('/leads/quick-update') }}", {
            id: "{{ $lead->id }}", comment: comment, remind_date: remindDate, status: status, _token: "{{ csrf_token() }}"
        }, function(res) {
            if(res.success) { location.reload(); }
        });
    }

    function saveFeedback() {
        const comment = $('#mainComment').val().trim();
        if(comment.length < 5) { Swal.fire('Context Required', 'Please enter at least 5 characters.', 'warning'); return; }
        submitData(comment, $('#mainRemind').val(), $('#mainStatus').val());
    }

    function openQuickNote(type, defaultMsg) {
        Swal.fire({
            title: `Quick Log: ${type}`,
            html: `<textarea id="swal_note" class="form-control mb-3" rows="3" style="border-radius:15px">${defaultMsg}</textarea>
                   <input type="date" id="swal_date" class="form-control" style="border-radius:12px" value="{{ $currentRemind }}">`,
            showCancelButton: true, confirmButtonText: 'CONFIRM LOG', confirmButtonColor: '#4f46e5',
            preConfirm: () => ({ note: $('#swal_note').val(), date: $('#swal_date').val() })
        }).then((result) => { if (result.isConfirmed) submitData(result.value.note, result.value.date, 'current'); });
    }

    function openStatusUpdate() {
        // 🌟 រៀបចំ Stage Lock នៅក្នុង SweetAlert Popup 🌟
        const stages = ['Prospecting', 'Appointment', 'Meet & Greet', 'Consulting ( Q & A )', 'Double Open', 'Presentation', 'Test Drive', 'Trail Close', 'Negotiation', 'Double Close', 'Sales ( contract of sales )', 'Delivery', 'Lost'];
        const currentStatus = "{{ strtolower($lead->status) }}";
        const currentIndex = stages.findIndex(s => s.toLowerCase() === currentStatus);

        let optionsHtml = '';
        stages.forEach((st, index) => {
            let isDisabled = (index < currentIndex && st.toLowerCase() !== 'lost') ? 'disabled' : '';
            let isSelected = (index === currentIndex) ? 'selected' : '';
            let displayLabel = isDisabled ? `🔒 ${st.toUpperCase()} (LOCKED)` : st.toUpperCase();
            
            optionsHtml += `<option value="${st}" ${isDisabled} ${isSelected} ${isDisabled ? 'style="color:#cbd5e1;"' : ''}>${displayLabel}</option>`;
        });

        Swal.fire({
            title: 'Shift Stage',
            html: `<div class="text-start mb-2"><label class="small fw-bold text-muted">Select New Stage:</label></div>
                   <select id="swal_s" class="form-select mb-3 fw-bold" style="border-radius:14px; height: 50px;">
                       ${optionsHtml}
                   </select>
                   <textarea id="swal_c" class="form-control mb-3" rows="2" style="border-radius:14px" placeholder="State reason for transition..."></textarea>
                   <input type="date" id="swal_d" class="form-control fw-bold" style="border-radius:14px; height: 50px;" value="{{ $currentRemind }}">`,
            showCancelButton: true, confirmButtonText: 'SYNC PIPELINE', confirmButtonColor: '#f59e0b',
            preConfirm: () => ({ status: $('#swal_s').val(), comment: "🔄 STAGE CHANGE: " + $('#swal_c').val(), date: $('#swal_d').val() })
        }).then((result) => { if (result.isConfirmed) submitData(result.value.comment, result.value.date, result.value.status); });
    }

    function openReschedule() {
        Swal.fire({
            title: 'Reschedule',
            html: `<input type="date" id="res_date" class="form-control mb-3" style="border-radius:14px" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                   <textarea id="res_reason" class="form-control" rows="2" style="border-radius:14px" placeholder="Context..."></textarea>`,
            showCancelButton: true, confirmButtonText: 'CONFIRM', confirmButtonColor: '#0ea5e9',
            preConfirm: () => ({ id: "{{ $lead->id }}", new_date: $('#res_date').val(), comment: $('#res_reason').val(), _token: "{{ csrf_token() }}" })
        }).then((result) => {
            if (result.isConfirmed) { $.post("{{ url('/leads/reschedule') }}", result.value, function(res) { if(res.success) location.reload(); }); }
        });
    }

    function submitManagerNote() {
        const comment = $('#mgr_note_input').val().trim();
        if(!comment) return;
        $.post("{{ url('/leads/update-manager-comment') }}", {
            id: "{{ $lead->id }}", manager_comment: comment, _token: "{{ csrf_token() }}"
        }, function(res) { if(res.success) location.reload(); });
    }
</script>
@endsection