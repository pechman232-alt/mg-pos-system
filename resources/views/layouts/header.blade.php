<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* 🚀 CRM Professional Header Styles */
    .crm-navbar {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #f1f5f9;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.015);
        padding: 0.5rem 1.5rem;
        z-index: 100;
    }
    
    .crm-search-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        max-width: 350px;
        width: 100%;
    }
    .crm-search-box:focus-within {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05);
    }
    .crm-search-box input {
        border: none;
        outline: none;
        background: transparent;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
        width: 100%;
        margin-left: 0.5rem;
    }
    .crm-search-box input::placeholder { color: #94a3b8; }

    .crm-clock {
        background: #f1f5f9;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    .crm-icon-btn {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #64748b;
        transition: 0.2s;
        text-decoration: none;
    }
    .crm-icon-btn:hover { background: #f8fafc; color: #4f46e5; border-color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    
    .crm-notif-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444; 
        color: #ffffff;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        min-width: 20px;
        text-align: center;
        border-radius: 20px;
        border: 2px solid #ffffff;
        line-height: 1;
        animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    .crm-notif-badge.service-badge { background: #f59e0b; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); }
    .crm-notif-badge.lead-badge { background: #ef4444; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); }

    @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    
    .hover-bg-slate-50:hover { background-color: #f8fafc !important; }

    .crm-profile-pill {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.3rem 0.3rem 0.3rem 1rem;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: 0.2s;
    }
    .crm-profile-pill:hover { background: #f8fafc; border-color: #cbd5e1; }
    .crm-profile-img { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }
    
    /* Mute Button Hover */
    .sound-toggle-btn { transition: 0.2s; cursor: pointer; border-radius: 6px; padding: 4px 8px; }
    .sound-toggle-btn:hover { background: #e2e8f0; }
</style>

@php
    $currentUser = auth('users')->user();
    $newLeadCount = 0;

    if ($currentUser) {
        $roleId = (int) $currentUser->role_id;
        
        // 🔍 រាប់តែទិន្នន័យដែលទើបបង្កើត 'ថ្ងៃនេះ' (Today) និងមិនទាន់ Archive
        $leadQuery = \App\Models\Lead::whereDate('created_at', \Carbon\Carbon::today())
                                     ->where(function($q) {
                                         $q->whereNull('is_archived')->orWhere('is_archived', 0);
                                     });

        // 👑 ប្រសិនបើមិនមែន Admin/Director/GM (1, 2, 6, 11) -> ត្រូវបែងចែកសិទ្ធិ
        if (!in_array($roleId, [1, 2, 6, 11, 12, 13, 14, 15, 16])) {
            
            // 🏢 បើជា Manager (3, 4, 5, 7, 8) -> រាប់តែអ្នកនៅក្នុងសាខាខ្លួន
            if (in_array($roleId, [3, 4, 5, 7, 8])) {
                $branchName = \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name');
                $subordinates = \App\Models\User::where('branch_id', $currentUser->branch_id)->pluck('full_name')->toArray();
                
                $leadQuery->where(function ($q) use ($branchName, $subordinates) {
                    if ($branchName) {
                        $searchName = trim(str_ireplace(['MG ', 'Mg '], '', $branchName));
                        $q->where('branch_name', 'LIKE', "%{$searchName}%");
                    }
                    if (!empty($subordinates)) {
                        $q->orWhereIn('sales_person', $subordinates);
                    }
                });
            } 
            // 👤 បើជា Sale ធម្មតា (9) -> រាប់តែអតិថិជនរបស់ខ្លួនឯង
            else {
                $leadQuery->where('sales_person', $currentUser->full_name);
            }
        }

        // 🎯 ទាញយកចំនួនចុងក្រោយ
        $newLeadCount = $leadQuery->count();
    }
@endphp

<nav class="layout-navbar navbar navbar-expand-xl align-items-center crm-navbar" id="layout-navbar">
    <div class="container-fluid d-flex align-items-center justify-content-between p-0">
        
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4 text-slate-600" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3 ms-auto">
            
            <div class="crm-clock d-none d-lg-flex">
                <i class="bx bx-time-five text-indigo-500"></i>
                <span id="headerLiveClock" style="font-family: 'JetBrains Mono', monospace; font-weight: 700; font-size: 12px; color: #334155;">00:00:00 AM</span>
            </div>

            <a href="{{ route('leads.index') }}" class="crm-icon-btn position-relative" title="New Leads Today">
                <i class="fas fa-bullseye fs-5"></i>
                @if($newLeadCount > 0)
                    <span class="crm-notif-badge lead-badge">{{ $newLeadCount > 99 ? '99+' : $newLeadCount }}</span>
                @endif
            </a>

            <div class="nav-item dropdown">
                <a href="javascript:void(0);" class="crm-icon-btn position-relative" data-bs-toggle="dropdown" id="notifBellIcon">
                    <i class="bx bx-bell fs-5"></i>
                    <span class="crm-notif-badge service-badge d-none" id="notifCountBadge">0</span>
                </a>
                
                <div class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-200 mt-3 p-0" style="width: 320px; border-radius: 16px; overflow: hidden;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-slate-50">
                        <span class="fw-800 text-slate-700" style="font-size: 11px; letter-spacing: 0.5px;">ការកក់ថ្មី (NEW BOOKINGS)</span>
                        
                        <div class="d-flex align-items-center gap-2">
                            <div id="soundToggleBtn" class="sound-toggle-btn text-muted" title="បើក/បិទ សំឡេងជូនដំណឹង">
                                <i class="fas fa-volume-up" id="soundIcon"></i>
                            </div>
                            <a href="{{ route('services.index') }}" class="text-indigo-600 fw-bold" style="font-size: 11px; text-decoration:none;">View list</a>
                        </div>
                    </div>
                    
                    <div class="notif-scroll-area custom-scrollbar" id="noti-list" style="max-height: 350px; overflow-y: auto;">
                        <div class="text-center py-5 text-slate-400">
                            <i class="bx bx-loader-alt bx-spin fs-3 mb-2 opacity-50"></i>
                            <p class="mb-0" style="font-size: 12px; font-weight: 600;">កំពុងទាញទិន្នន័យ...</p>
                        </div>
                    </div>
                    
                    <div class="p-2 border-top text-center bg-white">
                        <a href="{{ route('services.index') }}" class="text-slate-500 fw-bold text-decoration-none hover-indigo" style="font-size: 11px;">មើលការកក់ទាំងអស់ (View All)</a>
                    </div>
                </div>
            </div>

            <div class="d-none d-sm-block" style="width: 1px; height: 24px; background-color: #e2e8f0;"></div>

            @if(auth('users')->check())
            <div class="nav-item dropdown">
                <div class="crm-profile-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="cursor:pointer;">
                    <div class="text-end d-none d-md-block">
                        <div class="fw-bold text-slate-800 leading-none" style="font-size: 13px;">{{ auth('users')->user()?->full_name ?? 'Staff Member' }}</div>
                        <div class="fw-bold text-success text-uppercase mt-1" style="font-size: 9px; letter-spacing: 0.5px;">● Active Session</div>
                    </div>
                    <img src="{{ url(auth('users')->user()->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . auth('users')->user()->profile_pic) }}" class="crm-profile-img shadow-sm">
                </div>

                <ul class="dropdown-menu dropdown-menu-end border border-slate-200 shadow-lg mt-3 p-2" style="border-radius: 16px; min-width: 220px;">
                    <li class="p-2 border-bottom mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ url(auth('users')->user()->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . auth('users')->user()->profile_pic) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-bold text-slate-800">{{ auth('users')->user()?->full_name }}</h6>
                                <small class="text-slate-400 fw-semibold" style="font-size: 11px;">{{ auth('users')->user()?->username }}</small>
                            </div>
                        </div>
                    </li>
                    <li><a class="dropdown-item py-2 rounded text-slate-700 fw-semibold hover-bg-slate-50" href="#" style="font-size: 13px;"><i class="bx bx-user me-2 text-slate-400"></i> My Profile</a></li>
                    <li><a class="dropdown-item py-2 rounded text-slate-700 fw-semibold hover-bg-slate-50" href="#" style="font-size: 13px;"><i class="bx bx-cog me-2 text-slate-400"></i> Settings</a></li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li><a class="dropdown-item py-2 rounded text-danger fw-bold hover-bg-red-50" href="{{ url('/logout') }}" style="font-size: 13px;"><i class="bx bx-power-off me-2"></i> Log Out</a></li>
                </ul>
            </div>
            @endif

        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // ⏰ Header Live Clock
    function updateHeaderTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
        $('#headerLiveClock').text(timeStr);
    }
    setInterval(updateHeaderTime, 1000);
    updateHeaderTime();

    // ⌨️ Keyboard shortcut for Search (Press / to focus)
    $(document).keydown(function(e) {
        if (e.key === "/" && e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA") {
            e.preventDefault();
            $('.crm-search-box input').focus();
        }
    });

    // 🔊 មុខងារ បើក/បិទ សំឡេង (Sound Toggle)
    let isSoundEnabled = localStorage.getItem('mgNotificationSound') !== 'false'; // Default គឺបើក
    
    function updateSoundIcon() {
        if (isSoundEnabled) {
            $('#soundIcon').removeClass('fa-volume-mute text-danger').addClass('fa-volume-up text-primary');
            $('#soundToggleBtn').attr('title', 'សំឡេងកំពុងបើក (ចុចដើម្បីបិទ)');
        } else {
            $('#soundIcon').removeClass('fa-volume-up text-primary').addClass('fa-volume-mute text-danger');
            $('#soundToggleBtn').attr('title', 'សំឡេងកំពុងបិទ (ចុចដើម្បីបើក)');
        }
    }
    
    updateSoundIcon(); // ដំណើរការរូប Icon ពេលបើកទំព័រដំបូង
    
    // ពេលចុចលើ Icon បើក/បិទសំឡេង
    $('#soundToggleBtn').click(function(e) {
        e.stopPropagation(); // ការពារកុំឱ្យ Dropdown បិទពេលចុចប៉ះ
        isSoundEnabled = !isSoundEnabled;
        localStorage.setItem('mgNotificationSound', isSoundEnabled); // Save ចូល Browser
        updateSoundIcon();
    });

    // 🔔 មុខងារកណ្ដឹងជួង Auto-Refresh សម្រាប់ Service
    let lastNotifId = 0; 
    let isFirstLoad = true;

    function fetchHeaderNotifications() {
        $.ajax({
            url: "{{ route('services.notifications') }}",
            type: "GET",
            success: function(response) {
                let count = response.count;
                let data = response.data;
                
                // លោតលេខពណ៌លឿងលើកណ្ដឹង
                if (count > 0) {
                    $('#notifCountBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                } else {
                    $('#notifCountBadge').addClass('d-none');
                }

                let html = '';
                if (data.length > 0) {
                    
                    // 🌟 ឆែកមានឡានថ្មី និងចាក់សំឡេងបើសិនជាសំឡេងបានបើក (isSoundEnabled = true)
                    let latestId = data[0].id;
                    if (!isFirstLoad && latestId > lastNotifId && isSoundEnabled) {
                        let notifSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                        notifSound.play().catch(function() {
                            console.log("Browser តម្រូវឱ្យចុចអេក្រង់មុនសិនទើបអាចចាក់សំឡេងបាន។");
                        });
                    }
                    lastNotifId = latestId;

                    data.forEach(function(item) {
                        html += `
                            <a href="{{ route('services.index') }}?search=${item.plate}" class="dropdown-item d-flex align-items-start p-3 border-bottom hover-bg-slate-50">
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 small fw-bold text-slate-800 text-truncate">${item.customer}</h6>
                                        <small class="text-slate-400 fw-semibold" style="font-size: 10px;"><i class="bx bx-time-five"></i> ${item.time}</small>
                                    </div>
                                    <p class="mb-0 text-slate-500 text-truncate" style="font-size: 12px;">
                                        ស្លាកលេខ៖ <span class="text-danger fw-bold" style="font-family: monospace;">${item.plate}</span>
                                    </p>
                                </div>
                            </a>
                        `;
                    });
                } else {
                    html = `
                        <div class="text-center py-5 text-slate-400">
                            <i class="bx bx-check-shield fs-1 mb-2 text-success opacity-50"></i>
                            <p class="mb-0" style="font-size: 12px; font-weight: 600;">មិនមានការកក់ថ្មីដែលរង់ចាំទេ</p>
                        </div>
                    `;
                }
                $('#noti-list').html(html);
                isFirstLoad = false;
            }
        });
    }

    fetchHeaderNotifications();
    setInterval(fetchHeaderNotifications, 15000);
});
</script>