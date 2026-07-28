@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* ==========================================================================
       ✨ 1. CORE DESIGN SYSTEM & TYPOGRAPHY
       ========================================================================== */
    :root {
        --c-primary: #4f46e5;      --c-primary-hover: #4338ca;
        --c-success: #10b981;      --c-warning: #f59e0b;
        --c-danger: #ef4444;       --c-info: #0ea5e9;
        
        --c-slate-50: #f8fafc;     --c-slate-100: #f1f5f9;
        --c-slate-200: #e2e8f0;    --c-slate-300: #cbd5e1;
        --c-slate-400: #94a3b8;    --c-slate-500: #64748b;
        --c-slate-600: #475569;    --c-slate-700: #334155;
        --c-slate-800: #1e293b;    --c-slate-900: #0f172a;

        --radius-sm: 8px;          --radius-md: 12px;         --radius-lg: 16px;
        --border-light: 1px solid var(--c-slate-200);
    }

    body { background-color: var(--c-slate-50); font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif; }
    
    .saas-container {
        padding: 24px 32px;
        min-height: 100vh;
        width: 100%;
    }

    /* ==========================================================================
       🎬 2. ANIMATIONS
       ========================================================================== */
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up { animation: fadeInUp 0.4s ease-out forwards; opacity: 0; }
    .stagger-1 { animation-delay: 0.05s; }
    .stagger-2 { animation-delay: 0.10s; }
    .stagger-3 { animation-delay: 0.15s; }
    .stagger-4 { animation-delay: 0.20s; }
    .stagger-5 { animation-delay: 0.25s; }
    .stagger-6 { animation-delay: 0.30s; }

    /* ==========================================================================
       📊 3. SUMMARY CARDS
       ========================================================================== */
    .summary-card { 
        background: #fff; border: var(--border-light); border-radius: var(--radius-lg); padding: 20px; 
        cursor: pointer; transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02); height: 100%;
    }
    .summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.08); }
    
    .summary-card.active { border: 1px solid var(--c-primary); background: #f8fafc; }
    
    /* Dynamic Bottom Borders */
    .summary-card[data-type="today"] { border-bottom: 4px solid var(--c-danger); }
    .summary-card[data-type="days_1_7"] { border-bottom: 4px solid var(--c-success); }
    .summary-card[data-type="days_8_14"] { border-bottom: 4px solid var(--c-warning); }
    .summary-card[data-type="days_15_30"] { border-bottom: 4px solid var(--c-info); }

    .summary-card.active[data-type="today"] { border-color: var(--c-danger); }
    .summary-card.active[data-type="days_1_7"] { border-color: var(--c-success); }
    .summary-card.active[data-type="days_8_14"] { border-color: var(--c-warning); }
    .summary-card.active[data-type="days_15_30"] { border-color: var(--c-info); }

    .stage-badge { 
        font-size: 0.65rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; margin: 3px; 
        display: inline-block; background: var(--c-slate-100); color: var(--c-slate-700); border: 1px solid var(--c-slate-200); 
    }

    /* Custom Stats Cards */
    .custom-stat-card-success { background-color: #ecfdf5 !important; border: 1px solid #10b981 !important; border-radius: var(--radius-lg); color: #047857 !important; box-shadow: 0 2px 10px rgba(16, 185, 129, 0.05); cursor: pointer; transition: 0.2s; }
    .custom-stat-card-success:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15); }
    
    .custom-stat-card-info { background-color: #f0f9ff !important; border: 1px solid #0ea5e9 !important; border-radius: var(--radius-lg); color: #0369a1 !important; box-shadow: 0 2px 10px rgba(14, 165, 233, 0.05); cursor: pointer; transition: 0.2s; }
    .custom-stat-card-info:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(14, 165, 233, 0.15); }

    /* ==========================================================================
       📋 4. TASK CARDS
       ========================================================================== */
    .task-card { 
        background: #fff; border: 1px solid var(--c-slate-200); border-radius: var(--radius-lg); 
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: all 0.2s ease; overflow: hidden; 
    }
    .task-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); }
    
    /* Left border color based on status */
    .task-today { border-left: 4px solid var(--c-danger); } 
    .task-upcoming { border-left: 4px solid var(--c-primary); } 
    
    .badge-status { padding: 6px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; border: 1px solid transparent; }
    
    .contact-circle { 
        width: 45px; height: 45px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; 
        background: #eff6ff; color: var(--c-primary); font-size: 1.2rem; 
    }
    
    .activity-scroll { 
        background: var(--c-slate-50); border-radius: var(--radius-md); padding: 15px; border: 1px dashed var(--c-slate-300); 
        max-height: 150px; overflow-y: auto; 
    }
    .activity-scroll::-webkit-scrollbar { width: 4px; }
    .activity-scroll::-webkit-scrollbar-thumb { background: var(--c-slate-300); border-radius: 4px; }
    
    .btn-action { 
        border-radius: var(--radius-md); font-weight: 700; padding: 10px 0; font-size: 0.8rem; 
        transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px; 
    }
    .btn-action:hover { transform: translateY(-2px); filter: brightness(1.05); }

    /* ==========================================================================
       🌟 5. SWEETALERT CUSTOM POPUPS
       ========================================================================== */
    .swal2-ticket-popup { border-radius: 20px !important; padding: 0 !important; }
    .swal2-ticket-popup .swal2-html-container { margin: 0 !important; padding: 25px 25px 0 !important; overflow: hidden; }
    .swal2-ticket-popup .swal2-actions { width: 100%; margin: 20px 0 0 0 !important; padding: 0 25px 25px !important; display: block !important; }
    .swal2-ticket-popup .swal2-confirm { width: 100%; border-radius: 12px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; font-size: 0.95rem !important; background-color: var(--c-slate-800) !important; padding: 12px 0 !important; font-weight: 700 !important; }

    .swal2-list-popup { width: 95% !important; max-width: 500px !important; border-radius: 24px !important; padding: 0 !important; }
    .swal2-list-popup .swal2-html-container { margin: 0 !important; padding: 0 !important; text-align: left !important; }
    
    .swal-custom-scroll { max-height: 60vh; overflow-y: auto; padding: 24px; background: var(--c-slate-50); }
    .swal-custom-scroll::-webkit-scrollbar { width: 6px; }
    .swal-custom-scroll::-webkit-scrollbar-thumb { background: var(--c-slate-300); border-radius: 10px; }
    
    .list-item-card { 
        background: #fff; border: 1px solid var(--c-slate-200); border-radius: 16px; padding: 20px; 
        margin-bottom: 15px; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.02); 
    }
    .list-item-card:hover { border-color: var(--c-primary); box-shadow: 0 6px 16px rgba(0,0,0,0.06); transform: translateY(-2px); }

    /* Button SaaS General */
    .btn-saas { 
        display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; 
        border-radius: var(--radius-md); font-size: 0.9rem; font-weight: 700; cursor: pointer; 
        transition: all 0.2s; border: none; text-decoration: none; background: #fff; border: 1px solid var(--c-slate-300); color: var(--c-slate-700);
    }
    .btn-saas:hover { background: var(--c-slate-50); border-color: var(--c-slate-400); color: var(--c-slate-900); }
</style>

<div class="saas-container">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 animate-fade-up">
        <div>
            <h2 class="m-0" style="font-weight: 800; color: var(--c-slate-900); font-size: 1.8rem; letter-spacing: -0.5px;">
                Daily <span style="color: var(--c-primary);">Focus</span>
            </h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem; font-weight: 500;">Overview of leads by next scheduled action.</p>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('leads.index') }}" class="btn-saas shadow-sm">
                <i class="fas fa-arrow-left text-muted"></i> FULL PIPELINE
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4 animate-fade-up stagger-1">
        <div class="col-6 col-md-3">
            <div class="p-4 custom-stat-card-success h-100 d-flex flex-column justify-content-center" onclick="showLeadList('sales')">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;"><i class="fas fa-file-signature me-1"></i> Contract of Sales</div>
                    <i class="fas fa-chevron-right opacity-50 small mt-1"></i>
                </div>
                <h3 class="mb-0 fw-bolder" style="font-size: 2.2rem;">{{ $salesCount ?? 0 }} <span class="fs-6 fw-bold opacity-75">Leads</span></h3>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-4 custom-stat-card-info h-100 d-flex flex-column justify-content-center" onclick="showLeadList('delivery')">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;"><i class="fas fa-car me-1"></i> Deliveries</div>
                    <i class="fas fa-chevron-right opacity-50 small mt-1"></i>
                </div>
                <h3 class="mb-0 fw-bolder" style="font-size: 2.2rem;">{{ $deliveryCount ?? 0 }} <span class="fs-6 fw-bold opacity-75">Leads</span></h3>
            </div>
        </div>
    </div>

    {{-- ទិន្នន័យលាក់ទុកសម្រាប់បញ្ហាញក្នុង Popup List --}}
    @php
        $salesListJson = json_encode($salesList ?? []);
        $deliveryListJson = json_encode($deliveryList ?? []);
    @endphp

    <div class="row g-3 mb-5">
        @php $delay = 2; @endphp
        @foreach($stageSummary as $key => $data)
            <div class="col-xl-3 col-md-6 animate-fade-up stagger-{{ $delay++ }}">
                <div class="summary-card {{ $key == 'today' ? 'active' : '' }}" data-type="{{ $key }}" onclick="filterTasks('{{ $key }}', this)">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-slate-500" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $data['title'] }}</span>
                        <span class="badge" style="background: var(--c-slate-800); font-size: 0.75rem; padding: 6px 10px; border-radius: 8px;">{{ $data['count'] }} Leads</span>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        @forelse($data['stages'] as $stageName => $count)
                            <span class="stage-badge">{{ $stageName }}: <span class="text-primary">{{ $count }}</span></span>
                        @empty
                            <span class="small text-muted fst-italic opacity-50" style="font-size: 0.8rem;">No leads in this timeframe.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4" id="task-container">
        @php $taskDelay = 1; @endphp
        @forelse($tasks as $lead)
            @if($lead->filter_group == 'future') @continue @endif
            
            @php
                $isTodayOrPast = $lead->filter_group == 'today';
                $cardClass = $isTodayOrPast ? 'task-today' : 'task-upcoming';
                
                // Color Logic
                $badgeBg = $isTodayOrPast ? '#fee2e2' : '#eff6ff';
                $badgeColor = $isTodayOrPast ? '#b91c1c' : '#1d4ed8';
                $badgeBorder = $isTodayOrPast ? '#fecaca' : '#bfdbfe';
                $icon = $isTodayOrPast ? 'fa-exclamation-circle' : 'fa-calendar-day';
                
                $phone = preg_replace('/[^0-9]/', '', $lead->contact_info);
                $tgPhone = (strpos($phone, '0') === 0) ? '855' . substr($phone, 1) : $phone;
                $nextRemindDate = $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('Y-m-d') : date('Y-m-d', strtotime('+1 day'));
            @endphp

            <div class="col-xl-4 col-md-6 task-item animate-fade-up stagger-{{ ($taskDelay++ % 6) + 1 }}" data-group="{{ $lead->filter_group }}" style="{{ $isTodayOrPast ? '' : 'display: none;' }}">
                <div class="task-card {{ $cardClass }} p-4 h-100 d-flex flex-column">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="badge-status" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border-color: {{ $badgeBorder }};">
                            <i class="fas {{ $icon }} me-1"></i> {{ \Carbon\Carbon::parse($lead->remind_date)->format('d M, Y') }}
                        </div>
                        <span class="badge bg-slate-100 text-slate-700 border border-slate-200" style="padding: 6px 12px; border-radius: 8px;">{{ $lead->model }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="contact-circle"><i class="fas fa-user"></i></div>
                        <div>
                            <h5 class="fw-bolder mb-1" style="color: var(--c-slate-900); font-size: 1.1rem;">{{ $lead->customer_name }}</h5>
                            <div class="text-primary fw-bold" style="font-size: 0.9rem; font-family: monospace;"><i class="fas fa-phone-alt me-1 opacity-75"></i> {{ $lead->contact_info }}</div>
                            <div class="mt-2" style="font-size: 0.75rem; font-weight: 700; background: var(--c-slate-100); color: var(--c-slate-600); padding: 4px 8px; border-radius: 6px; display: inline-block;">
                                <i class="fas fa-user-tie text-primary opacity-75 me-1"></i> {{ $lead->sales_person ?? 'No Assignee' }} 
                                <span class="mx-1 opacity-50">|</span> 
                                <i class="fas fa-map-marker-alt text-danger opacity-75 me-1"></i> {{ str_replace('MG ', '', $lead->branch_name) }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 flex-grow-1">
                        <span class="fw-bold text-slate-400 d-block mb-2" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Interaction History</span>
                        <div class="activity-scroll">
                            @forelse($lead->activities as $activity)
                                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom border-slate-200' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold" style="font-size: 0.75rem; color: var(--c-primary);"><i class="fas fa-check-circle me-1 opacity-75"></i> {{ strtoupper($activity->status) }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem; font-weight: 600;">{{ $activity->created_at->format('d M, g:i A') }}</span>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--c-slate-700); font-family: 'Kantumruy Pro'; line-height: 1.5; font-weight: 500;">
                                        {!! nl2br(e(str_replace('📝 QUICK NOTE: ', '', $activity->comment))) !!}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-3 text-muted fst-italic opacity-75" style="font-size: 0.85rem; font-weight: 500;">No previous interaction logged.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <a href="tel:{{ $lead->contact_info }}" class="btn-action flex-fill text-white shadow-sm" title="Call Client" style="background: var(--c-success);">
                            <i class="fas fa-phone"></i>
                        </a>
                        <a href="https://t.me/+{{ $tgPhone }}" target="_blank" class="btn-action flex-fill text-white shadow-sm" title="Chat on Telegram" style="background: var(--c-info);">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <button onclick="openQuickStageUpdate('{{ $lead->id }}', '{{ $lead->status }}', '{{ $nextRemindDate }}')" class="btn-action flex-fill text-dark shadow-sm" style="background: var(--c-warning);">
                            <i class="fas fa-sync-alt"></i> UPDATE
                        </button>
                        
                        <button onclick="viewTicket('{{ $lead->id }}', '{{ addslashes($lead->customer_name) }}', '{{ $lead->contact_info }}', '{{ $lead->model }}', '{{ $lead->status }}', '{{ $nextRemindDate }}', '{{ addslashes($lead->sales_person) }}')" class="btn-action shadow-sm" style="background: var(--c-slate-800); color: #fff; padding: 10px 16px;" title="View Ticket">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 animate-fade-up">
                <div style="font-size: 4rem; color: var(--c-slate-200); margin-bottom: 20px;"><i class="fas fa-folder-open"></i></div>
                <h3 class="fw-bolder" style="color: var(--c-slate-400);">Pipeline is Empty</h3>
                <p class="fw-bold" style="color: var(--c-slate-400);">No upcoming follow-ups found for this category.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function filterTasks(groupKey, element) {
        $('.summary-card').removeClass('active');
        $(element).addClass('active');
        $('.task-item').hide();
        $(`.task-item[data-group="${groupKey}"]`).fadeIn(300);
    }

    // 🌟 មុខងារ Popup ថ្មី (Clean & Scrollable)
    function showLeadList(type) {
        const salesData = {!! $salesListJson !!};
        const deliveryData = {!! $deliveryListJson !!};
        
        let dataList = type === 'sales' ? salesData : deliveryData;
        let title = type === 'sales' ? '<i class="fas fa-file-signature text-success me-2"></i> Contract of Sales' : '<i class="fas fa-car text-info me-2"></i> Deliveries';
        let headerColor = type === 'sales' ? 'text-success' : 'text-info';
        
        if(dataList.length === 0) {
            Swal.fire({ icon: 'info', title: 'No Leads Found', text: 'There are no records in this category.', customClass: { popup: 'rounded-4' }, confirmButtonColor: 'var(--c-slate-800)' });
            return;
        }

        let htmlContent = `<div class="swal-custom-scroll">`;
        
        dataList.forEach(lead => {
            let phoneNum = lead.contact_info.replace(/[^0-9]/g, '');
            let tgPhone = (phoneNum.indexOf('0') === 0) ? '855' + phoneNum.substring(1) : phoneNum;
            let sp = lead.sales_person ? lead.sales_person : 'No Assignee';
            
            htmlContent += `
                <div class="list-item-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="fw-bolder mb-1 text-dark" style="font-size: 1.05rem;">${lead.customer_name}</h6>
                            <span class="badge bg-slate-100 text-slate-700 border border-slate-200 px-2 py-1" style="font-size:0.7rem;">${lead.model}</span>
                        </div>
                        <span class="badge bg-slate-50 text-slate-400 border px-2 py-1" style="font-size:0.7rem;">#${lead.id}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mt-3 pt-3 border-top border-slate-100">
                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--c-slate-600);">
                            <div class="mb-1"><i class="fas fa-phone-alt text-primary opacity-50 me-2"></i> <span style="font-family: monospace;">${lead.contact_info}</span></div>
                            <div><i class="fas fa-user-tie text-primary opacity-50 me-2"></i> ${sp}</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="tel:${lead.contact_info}" class="btn-action text-white rounded-circle shadow-sm" style="background: var(--c-success); width: 36px; height: 36px; padding: 0;"><i class="fas fa-phone"></i></a>
                            <a href="https://t.me/+${tgPhone}" target="_blank" class="btn-action text-white rounded-circle shadow-sm" style="background: var(--c-info); width: 36px; height: 36px; padding: 0;"><i class="fab fa-telegram-plane"></i></a>
                        </div>
                    </div>
                </div>
            `;
        });
        htmlContent += `</div>`;

        Swal.fire({
            title: `<div class="fs-4 fw-bolder py-2 ${headerColor}">${title}</div>`,
            html: htmlContent,
            showCloseButton: true,
            showConfirmButton: false,
            customClass: { 
                popup: 'swal2-list-popup shadow-lg border-0', 
                header: 'bg-white border-bottom-0 pt-4 pb-0' 
            }
        });
    }

    function openQuickStageUpdate(leadId, currentStatus, currentRemindDate) {
        const stages = ['Prospecting', 'Appointment', 'Meet & Greet', 'Consulting ( Q & A )', 'Double Open', 'Presentation', 'Test Drive', 'Trail Close', 'Negotiation', 'Double Close', 'Sales ( contract of sales )', 'Delivery', 'Lost'];
        const currentIndex = stages.findIndex(s => s.toLowerCase() === currentStatus.toLowerCase());
        
        let optionsHtml = '';
        stages.forEach((st, index) => {
            let isDisabled = (index < currentIndex && st.toLowerCase() !== 'lost') ? 'disabled' : '';
            let isSelected = (index === currentIndex) ? 'selected' : '';
            let displayLabel = isDisabled ? `🔒 ${st.toUpperCase()} (Locked)` : st.toUpperCase();
            optionsHtml += `<option value="${st}" ${isDisabled} ${isSelected} ${isDisabled ? 'style="color:#94a3b8;"' : ''}>${displayLabel}</option>`;
        });

        Swal.fire({
            title: '<div style="font-weight: 800; font-family: \'Plus Jakarta Sans\'; font-size: 1.4rem;">Quick Follow-up</div>',
            html: `
                <div style="background: var(--c-slate-50); border: 1px solid var(--c-slate-200); border-radius: 16px; padding: 20px; text-align: left;">
                    <div class="mb-3">
                        <label class="fw-bold mb-2" style="font-size: 0.8rem; color: var(--c-slate-600); text-transform: uppercase;">1. Update Pipeline Stage:</label>
                        <select id="swal_s" class="form-select fw-bold" style="border-radius:10px; height: 45px; border-color: var(--c-slate-300); color: var(--c-slate-800);">${optionsHtml}</select>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2" style="font-size: 0.8rem; color: var(--c-slate-600); text-transform: uppercase;">2. Follow-up Notes:</label>
                        <textarea id="swal_c" class="form-control" rows="3" style="border-radius:10px; border-color: var(--c-slate-300); font-weight: 500;" placeholder="Write your notes here..."></textarea>
                    </div>
                    <div>
                        <label class="fw-bold mb-2" style="font-size: 0.8rem; color: var(--c-slate-600); text-transform: uppercase;">3. Next Schedule:</label>
                        <input type="date" id="swal_d" class="form-control fw-bold text-primary" style="border-radius:10px; height: 45px; border-color: var(--c-slate-300);" value="${currentRemindDate}">
                    </div>
                </div>
            `,
            showCancelButton: true, confirmButtonText: 'SAVE UPDATE', confirmButtonColor: 'var(--c-primary)', cancelButtonText: 'CANCEL',
            customClass: { popup: 'rounded-4 border-0 shadow-lg', confirmButton: 'btn btn-primary fw-bold px-4 py-2 rounded-3', cancelButton: 'btn btn-light fw-bold px-4 py-2 border rounded-3 text-dark' },
            buttonsStyling: false,
            preConfirm: () => {
                const note = $('#swal_c').val().trim();
                if(!note) { Swal.showValidationMessage('Please enter follow-up notes!'); return false; }
                return { id: leadId, status: $('#swal_s').val(), comment: "📝 QUICK NOTE: " + note, remind_date: $('#swal_d').val(), _token: "{{ csrf_token() }}" }
            }
        }).then((result) => { 
            if (result.isConfirmed) {
                Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post("{{ url('/leads/quick-update') }}", result.value, function(res) {
                    if(res.success) { Swal.fire({ icon: 'success', title: 'Task Completed!', timer: 1500, showConfirmButton: false }).then(() => location.reload()); } 
                    else { Swal.fire('Error', res.message, 'error'); }
                });
            }
        });
    }

    function viewTicket(id, name, phone, model, stage, date, sale) {
        const ticketHtml = `
            <div style="font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif; text-align: center;">
                <div style="display: flex; justify-content: center; margin-bottom: 15px;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; border: 4px solid var(--c-success); background: #ecfdf5; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check" style="font-size: 24px; color: var(--c-success);"></i>
                    </div>
                </div>
                <h4 style="font-weight: 800; color: var(--c-slate-900); margin-bottom: 5px; font-size: 1.2rem;">ព័ត៌មានអតិថិជន</h4>
                <div style="color: var(--c-slate-400); font-size: 0.8rem; margin-bottom: 25px; font-weight: 700; font-family: monospace;">Ref: #MG-${id}</div>

                <div style="text-align: left; font-size: 0.9rem; color: var(--c-slate-700); line-height: 1.5; background: #fff; border: 1px solid var(--c-slate-200); border-radius: 12px; padding: 15px;">
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--c-slate-200);">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">អតិថិជន:</span>
                        <span style="font-weight: 800; color: var(--c-slate-900);">${name}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--c-slate-200);">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">ទូរស័ព្ទ:</span>
                        <span style="font-weight: 800; color: var(--c-primary); font-family: monospace;">${phone}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--c-slate-200);">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">រថយន្ត:</span>
                        <span style="font-weight: 700; background: var(--c-slate-100); padding: 2px 8px; border-radius: 6px;">${model}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--c-slate-200);">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">ស្ថានភាព:</span>
                        <span style="font-weight: 800; color: var(--c-info); text-transform: uppercase;">${stage}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--c-slate-200);">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">ថ្ងៃណាត់ជួប:</span>
                        <span style="font-weight: 800; color: var(--c-danger);">${date}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                        <span style="color: var(--c-slate-500); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">អ្នកលក់ (Sale):</span>
                        <span style="font-weight: 700;">${sale}</span>
                    </div>
                </div>
                <div style="margin-top: 20px; font-size: 0.75rem; color: var(--c-slate-400); font-weight: 600;"><i class="fas fa-camera me-1"></i> សូមថតអេក្រង់ (Screenshot) ទុកជាឯកសារយោង។</div>
            </div>
        `;
        Swal.fire({ 
            html: ticketHtml, 
            showConfirmButton: true, 
            confirmButtonText: 'បិទ / Close', 
            width: '400px', 
            customClass: { popup: 'swal2-ticket-popup border-0 shadow-lg', confirmButton: 'btn fw-bold px-4 py-2' }, 
            buttonsStyling: false 
        });
    }
</script>

@endsection