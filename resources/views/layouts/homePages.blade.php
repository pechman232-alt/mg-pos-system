<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- 🌟 ១. បន្ថែម CSRF Token សម្រាប់ការពារសុវត្ថិភាព AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    
    <title>MG Dealer CRM</title>
    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('backend/assets/js/config.js') }}"></script>

    <link href="{{ asset('/datatable/datatables.min.css') }}" rel="stylesheet">
    <script src="{{ url('/datatable/datatables.min.js') }}"></script>

    <link href="{{ asset('/assets/select2/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ url('/assets/select2/js/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

    <style>
        body, html, * { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif; }
        body, html { overflow: hidden !important; height: 100vh; margin: 0; background-color: #f8fafc; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; z-index: 1000; }
        .layout-page { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        .content-wrapper { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; position: relative; background-color: #f8fafc; }
        footer, .content-footer { flex-shrink: 0; position: relative; z-index: 100 !important; }

        .custom-topbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 10px 32px; display: flex; justify-content: space-between; align-items: center; z-index: 900; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .notify-icon-btn { position: relative; display: flex; align-items: center; justify-content: center; width: 42px; height: 42px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; color: #64748b; text-decoration: none; transition: all 0.2s ease; }
        .notify-icon-btn:hover { background: #ffffff; border-color: #cbd5e1; color: #0f172a; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .notify-badge { position: absolute; top: -6px; right: -6px; background: #ef4444; color: white; font-size: 10px; font-weight: 800; padding: 4px 6px; min-width: 22px; text-align: center; border-radius: 20px; border: 2px solid #fff; line-height: 1; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .notify-badge.service-badge { background: #f59e0b; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); color: #fff; }
        .notify-badge.lead-badge { background: #ef4444; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); color: #fff; }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .pulse-circle { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); } 70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
        .hover-bg-slate-50:hover { background-color: #f8fafc !important; }
        .sound-toggle-btn { transition: 0.2s; cursor: pointer; border-radius: 6px; padding: 4px 8px; }
        .sound-toggle-btn:hover { background: #e2e8f0; }
    </style>
</head>

<body>
    @include('Helps.toast')

    @php
        $currentUser = auth('users')->user();
        $roleId = $currentUser ? (int) $currentUser->role_id : 0;

        // 🌟 កំណត់សិទ្ធិមើល Notification តាម Role (បងអាចកែសម្រួលលេខ Role ID បានតាមតម្រូវការ)
        
        // 1. អ្នកដែលមានសិទ្ធិមើល *LEAD NOTIFICATION* (ឧ. Super Admin, Admin, Sale Director, Managers, Sale)
        // ឧទាហរណ៍: 1(Super Admin), 2(Admin), 6(Sale Director), 7(Sales Admin), 9(Sale)...
        $canSeeLeadNotif = in_array($roleId, [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13]); 
        
        // 2. អ្នកដែលមានសិទ្ធិមើល *SERVICE NOTIFICATION* (ឧ. Super Admin, Admin, Group Manager)
        // ឧទាហរណ៍: 1(Super Admin), 2(Admin), 10(Group Manager)...
        // (បងដាក់ Role អីក៏បានឱ្យតែចង់ឱ្យគាត់មើលឃើញ Service)
        $canSeeServiceNotif = in_array($roleId, [1, 2, 10, 11, 12, 13, 14, 15, 16]); 
    @endphp

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.navbar')
            <div class="layout-page">
                <div class="custom-topbar">
                    <div class="layout-menu-toggle d-xl-none cursor-pointer"><i class="bx bx-menu bx-md text-slate-700"></i></div>
                    <div class="d-none d-xl-block"><span class="fw-bold text-slate-400 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Customer Relationship Management</span></div>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <div class="px-3 py-2 bg-slate-50 border rounded-3 text-slate-700 fw-bold d-flex align-items-center" style="font-family: monospace; font-size: 0.95rem; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                            <i class="far fa-clock me-2 text-primary opacity-75"></i>
                            <span id="live_clock_topbar">--:--:-- --</span>
                        </div>

                        @if($canSeeLeadNotif)
                        <div class="nav-item dropdown">
                            <a href="javascript:void(0);" class="notify-icon-btn position-relative" data-bs-toggle="dropdown" data-bs-offset="0,15" id="leadNotifBellIcon" title="New Leads Today">
                                <i class="fas fa-bullseye fs-5"></i>
                                <span class="notify-badge lead-badge d-none" id="leadNotifCountBadge">0</span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-200 p-0" style="width: 320px; border-radius: 16px; overflow: hidden;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-slate-50">
                                    <span class="fw-800 text-slate-700" style="font-size: 11px; letter-spacing: 0.5px;">អតិថិជនថ្មី (NEW LEADS TODAY)</span>
                                    <a href="{{ route('leads.index') }}" class="text-indigo-600 fw-bold" style="font-size: 11px; text-decoration:none;">View pipeline</a>
                                </div>
                                <div class="notif-scroll-area custom-scrollbar" id="lead-noti-list" style="max-height: 350px; overflow-y: auto;">
                                    <div class="text-center py-5 text-slate-400">
                                        <i class="bx bx-loader-alt bx-spin fs-3 mb-2 opacity-50"></i>
                                        <p class="mb-0" style="font-size: 12px; font-weight: 600;">កំពុងទាញទិន្នន័យ...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($canSeeServiceNotif)
                        <div class="nav-item dropdown">
                            <a href="javascript:void(0);" class="notify-icon-btn position-relative" data-bs-toggle="dropdown" data-bs-offset="0,15" id="notifBellIcon" title="Service Bookings">
                                <i class="bx bx-bell fs-5"></i>
                                <span class="notify-badge service-badge d-none" id="notifCountBadge">0</span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border border-slate-200 p-0" style="width: 320px; border-radius: 16px; overflow: hidden;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-slate-50">
                                    <span class="fw-800 text-slate-700" style="font-size: 11px; letter-spacing: 0.5px;">ការកក់ថ្មី (NEW BOOKINGS TODAY)</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div id="soundToggleBtn" class="sound-toggle-btn text-muted" title="បើក/បិទ សំឡេងជូនដំណឹង"><i class="fas fa-volume-up" id="soundIcon"></i></div>
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
                        @endif

                        <div class="vr bg-slate-200 mx-1" style="width: 2px;"></div>

                        @if($currentUser)
                        <div class="nav-item dropdown">
                            <div class="d-flex align-items-center border rounded-pill p-1 bg-white shadow-sm cursor-pointer dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="border-color: #cbd5e1 !important; transition: 0.2s;" onmouseover="this.style.borderColor='#4f46e5';" onmouseout="this.style.borderColor='#cbd5e1';">
                                <div class="text-end me-3 ms-3 d-none d-md-block">
                                    <div class="fw-bolder text-slate-900 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px; line-height: 1;">{{ $currentUser->full_name ?? 'USER PROFILE' }}</div>
                                    <div class="text-success fw-bold d-flex align-items-center justify-content-end gap-1 mt-1" style="font-size: 0.6rem;"><span class="pulse-circle bg-success rounded-circle" style="width: 6px; height: 6px;"></span> ACTIVE SESSION</div>
                                </div>
                                <img src="{{ url($currentUser->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $currentUser->profile_pic) }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->full_name ?? 'User') }}&background=e0f2fe&color=0ea5e9&bold=true'" class="rounded-circle" width="36" height="36" style="border: 2px solid #f8fafc; box-shadow: 0 0 0 1px #cbd5e1; object-fit: cover;">
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end border border-slate-200 shadow-lg mt-3 p-2" style="border-radius: 16px; min-width: 220px;">
                                <li class="p-2 border-bottom mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ url($currentUser->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $currentUser->profile_pic) }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->full_name ?? 'User') }}&background=e0f2fe&color=0ea5e9&bold=true'" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 small fw-bold text-slate-800">{{ $currentUser->full_name }}</h6>
                                            <small class="text-slate-400 fw-semibold" style="font-size: 11px;">{{ $currentUser->username }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li><a class="dropdown-item py-2 rounded text-danger fw-bold hover-bg-red-50" href="{{ url('/logout') }}" style="font-size: 13px;"><i class="bx bx-power-off me-2"></i> Log Out</a></li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="content-wrapper">@yield('content')</div>
                @include('layouts.footer')
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    @yield('modals')

    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>

    <script>
        function updateTopbarClock() {
            const now = new Date();
            let hours = now.getHours(), minutes = now.getMinutes(), seconds = now.getSeconds();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12; hours = hours ? hours : 12; 
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            document.getElementById('live_clock_topbar').innerText = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        }
        setInterval(updateTopbarClock, 1000); updateTopbarClock();

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $(document).ready(function() {
            $(document).on('click', '.layout-menu-toggle', function(e) {
                e.preventDefault(); $('html').toggleClass('layout-menu-expanded');
            });
        });

        @if($currentUser)
            setInterval(function() {
                fetch('/check-auth-session')
                    .then(response => response.json())
                    .then(data => {
                        if (data.active === false) {
                            Swal.fire({
                                icon: 'warning', title: 'Session Expired', text: 'គណនីនេះត្រូវបាន Login ពីឧបករណ៍ផ្សេង។ អ្នកនឹងត្រូវ Log out ចេញដោយស្វ័យប្រវត្តិ!',
                                confirmButtonText: 'OK', allowOutsideClick: false
                            }).then(() => { window.location.href = "{{ url('login') }}"; });
                        }
                    }).catch(e => console.error(e));
            }, 5000);
        @endif

        // ==========================================
        // 🌟 AJAX Notification Logic (Service & Leads)
        // ==========================================
        let isSoundEnabled = localStorage.getItem('mgNotificationSound') !== 'false';
        function updateSoundIcon() {
            if (isSoundEnabled) {
                $('#soundIcon').removeClass('fa-volume-mute text-danger').addClass('fa-volume-up text-primary');
            } else {
                $('#soundIcon').removeClass('fa-volume-up text-primary').addClass('fa-volume-mute text-danger');
            }
        }
        updateSoundIcon();
        $('#soundToggleBtn').click(function(e) {
            e.stopPropagation(); isSoundEnabled = !isSoundEnabled;
            localStorage.setItem('mgNotificationSound', isSoundEnabled); updateSoundIcon();
        });

        let lastServiceNotifId = 0, lastLeadNotifId = 0, isFirstLoad = true;

        // 🌟 Service Notification 
        @if($canSeeServiceNotif)
        function fetchServiceNotifications() {
            $.ajax({
                url: "{{ route('services.notifications') }}", type: "GET",
                success: function(response) {
                    let count = response.count || 0; let data = response.data || [];
                    if (response.data && Array.isArray(response.data.data)) data = response.data.data;

                    if (count > 0) $('#notifCountBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                    else $('#notifCountBadge').addClass('d-none');

                    let html = '';
                    if (Array.isArray(data) && data.length > 0) {
                        let latestId = data[0].id || 0;
                        if (!isFirstLoad && latestId > lastServiceNotifId && isSoundEnabled) {
                            new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e=>{});
                        }
                        lastServiceNotifId = latestId;

                        data.forEach(function(item) {
                            let customerName = item.customer || item.customer_name || 'អតិថិជន';
                            let timeStr = item.time || item.created_at || '';
                            let plateNum = item.plate || item.plate_number || 'N/A';
                            html += `
                                <a href="{{ route('services.index') }}?search=${plateNum}" class="dropdown-item d-flex align-items-start p-3 border-bottom hover-bg-slate-50">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 small fw-bold text-slate-800 text-truncate">${customerName}</h6>
                                            <small class="text-slate-400 fw-semibold" style="font-size: 10px;"><i class="bx bx-time-five"></i> ${timeStr}</small>
                                        </div>
                                        <p class="mb-0 text-slate-500 text-truncate" style="font-size: 12px;">ស្លាកលេខ៖ <span class="text-danger fw-bold" style="font-family: monospace;">${plateNum}</span></p>
                                    </div>
                                </a>`;
                        });
                    } else {
                        html = `<div class="text-center py-5 text-slate-400"><i class="bx bx-check-shield fs-1 mb-2 text-success opacity-50"></i><p class="mb-0" style="font-size: 12px; font-weight: 600;">មិនមានការកក់ថ្មីដែលរង់ចាំទេ (ថ្ងៃនេះ)</p></div>`;
                    }
                    $('#noti-list').html(html);
                },
                error: function() { $('#noti-list').html(`<div class="text-center py-4 text-danger"><i class="bx bx-error-circle fs-2 mb-2"></i><p style="font-size:12px;">Error Loading Data</p></div>`); }
            });
        }
        @endif

        // 🌟 Lead Notification
        @if($canSeeLeadNotif)
        function fetchLeadNotifications() {
            $.ajax({
                url: "{{ url('/leads/notifications') }}", type: "GET",
                success: function(response) {
                    let count = response.count || 0; let data = response.data || [];
                    if (response.data && Array.isArray(response.data.data)) data = response.data.data;

                    if (count > 0) $('#leadNotifCountBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                    else $('#leadNotifCountBadge').addClass('d-none');

                    let html = '';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(function(item) {
                            let customerName = item.customer || 'អតិថិជន';
                            let timeStr = item.time || '';
                            let phoneNum = item.phone || 'N/A';
                            let model = item.model || '';

                            html += `
                                <a href="{{ route('leads.index') }}?search=${phoneNum}" class="dropdown-item d-flex align-items-start p-3 border-bottom hover-bg-slate-50">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 small fw-bold text-slate-800 text-truncate">${customerName}</h6>
                                            <small class="text-slate-400 fw-semibold" style="font-size: 10px;"><i class="bx bx-time-five"></i> ${timeStr}</small>
                                        </div>
                                        <p class="mb-0 text-slate-500 text-truncate" style="font-size: 12px;">
                                            <i class="fas fa-phone-alt text-primary opacity-75"></i> <span class="text-primary fw-bold" style="font-family: monospace;">${phoneNum}</span> 
                                            ${model ? `| <span class="text-slate-600">${model}</span>` : ''}
                                        </p>
                                    </div>
                                </a>`;
                        });
                    } else {
                        html = `<div class="text-center py-5 text-slate-400"><i class="bx bx-check-shield fs-1 mb-2 text-success opacity-50"></i><p class="mb-0" style="font-size: 12px; font-weight: 600;">មិនមានអតិថិជនថ្មីទេ (ថ្ងៃនេះ)</p></div>`;
                    }
                    $('#lead-noti-list').html(html);
                },
                error: function() { $('#lead-noti-list').html(`<div class="text-center py-4 text-danger"><i class="bx bx-error-circle fs-2 mb-2"></i><p style="font-size:12px;">Error Loading Data</p></div>`); }
            });
        }
        @endif

        function fetchAllNotifications() {
            @if($canSeeServiceNotif) fetchServiceNotifications(); @endif
            @if($canSeeLeadNotif) fetchLeadNotifications(); @endif
            isFirstLoad = false;
        }

        fetchAllNotifications();
        setInterval(fetchAllNotifications, 15000); 
    </script>
    @stack('scripts')
</body>
</html>