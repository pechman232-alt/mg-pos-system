<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Security: CSRF Token & HTTPS Enforcement -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    
    <title>MG Dealer CRM</title>
    <link rel="shortcut icon" href="{{ asset('/assets/images/logo.png') }}">

    <!-- Typography: SaaS Standard -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Core Icons & Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Theme Assets -->
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('backend/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Scripts -->
    <script src="{{ asset('backend/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('backend/assets/js/config.js') }}"></script>

    <!-- DataTables & Select2 -->
    <link href="{{ asset('/datatable/datatables.min.css') }}" rel="stylesheet">
    <script src="{{ url('/datatable/datatables.min.js') }}"></script>
    <link href="{{ asset('/assets/select2/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ url('/assets/select2/js/select2.min.js') }}"></script>

    @stack('styles')

    <style>
        /* =========================================================
           GLOBAL SAAS DESIGN VARIABLES 
           ========================================================= */
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --surface: #FFFFFF;
            --bg-body: #F8FAFC;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;

            --font-body: 'Inter', sans-serif;
            --font-display: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body, html, * { font-family: var(--font-body); }
        body, html { overflow: hidden !important; height: 100vh; margin: 0; background-color: var(--bg-body); color: var(--text-main); }
        
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; z-index: 1000; }
        .layout-page { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        .content-wrapper { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; position: relative; background-color: var(--bg-body); }
        footer, .content-footer { flex-shrink: 0; position: relative; z-index: 100 !important; }

        /* Topbar Styling */
        .custom-topbar { background: var(--surface); border-bottom: 1px solid var(--border-color); padding: 12px 32px; display: flex; justify-content: space-between; align-items: center; z-index: 900; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        .topbar-title { font-family: var(--font-display); font-weight: 700; color: var(--text-muted); font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase; }

        /* Notification Icons */
        .notify-icon-btn { position: relative; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 10px; color: var(--text-muted); text-decoration: none; transition: all 0.2s ease; }
        .notify-icon-btn:hover { background: var(--surface); border-color: #D1D5DB; color: var(--text-main); transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        .notify-badge { position: absolute; top: -6px; right: -6px; color: white; font-size: 10px; font-weight: 800; padding: 4px 6px; min-width: 22px; text-align: center; border-radius: 20px; border: 2px solid var(--surface); line-height: 1; animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
        .service-badge { background: #F59E0B; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3); }
        .lead-badge { background: #EF4444; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3); }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        
        .pulse-circle { animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); } 70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
        
        .sound-toggle-btn { transition: 0.2s; cursor: pointer; border-radius: 6px; padding: 4px 8px; }
        .sound-toggle-btn:hover { background: #F3F4F6; }
        
        .hover-bg-slate-50:hover { background-color: #F8FAFC !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #F3F4F6; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 4px; }
    </style>
</head>

<body>
    @include('Helps.toast')

    @php
        $currentUser = auth('users')->user();
        $roleId = $currentUser ? (int) $currentUser->role_id : 0;

        // Role Authorization Matrices
        $canSeeLeadNotif = in_array($roleId, [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13]); 
        $canSeeServiceNotif = in_array($roleId, [1, 2, 10, 11, 12, 13, 14, 15, 16]); 
    @endphp

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            
            @include('layouts.navbar')
            
            <div class="layout-page">
                <!-- TOP BAR -->
                <div class="custom-topbar">
                    <div class="layout-menu-toggle d-xl-none cursor-pointer"><i class="bx bx-menu bx-md text-muted"></i></div>
                    <div class="d-none d-xl-block"><span class="topbar-title">Customer Relationship Management</span></div>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        
                        <!-- Live System Clock -->
                        <div class="px-3 py-2 bg-body border rounded-3 text-main fw-bold d-flex align-items-center" style="font-family: var(--font-mono); font-size: 0.85rem; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                            <i class="far fa-clock me-2" style="color: var(--primary);"></i>
                            <span id="live_clock_topbar">--:--:-- --</span>
                        </div>

                        <!-- Leads Notification Dropdown -->
                        @if($canSeeLeadNotif)
                        <div class="nav-item dropdown">
                            <a href="javascript:void(0);" class="notify-icon-btn" data-bs-toggle="dropdown" data-bs-offset="0,15" id="leadNotifBellIcon" title="New Leads">
                                <i class="fas fa-bullseye fs-5"></i>
                                <span class="notify-badge lead-badge d-none" id="leadNotifCountBadge">0</span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-color p-0" style="width: 320px; border-radius: 12px; overflow: hidden;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-body);">
                                    <span class="fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: var(--text-muted); text-transform: uppercase;">New Leads Today</span>
                                    <a href="{{ route('leads.index') }}" style="color: var(--primary); font-weight: 600; font-size: 0.75rem; text-decoration:none;">View pipeline</a>
                                </div>
                                <div class="notif-scroll-area custom-scrollbar" id="lead-noti-list" style="max-height: 350px; overflow-y: auto;">
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-loader-alt bx-spin fs-3 mb-2 opacity-50"></i>
                                        <p class="mb-0" style="font-size: 0.75rem; font-weight: 500;">Fetching data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Service Notification Dropdown -->
                        @if($canSeeServiceNotif)
                        <div class="nav-item dropdown">
                            <a href="javascript:void(0);" class="notify-icon-btn" data-bs-toggle="dropdown" data-bs-offset="0,15" id="notifBellIcon" title="Service Bookings">
                                <i class="bx bx-bell fs-5"></i>
                                <span class="notify-badge service-badge d-none" id="notifCountBadge">0</span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-color p-0" style="width: 320px; border-radius: 12px; overflow: hidden;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-body);">
                                    <span class="fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px; color: var(--text-muted); text-transform: uppercase;">New Bookings</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <div id="soundToggleBtn" class="sound-toggle-btn text-muted" title="Toggle Alert Sound"><i class="fas fa-volume-up" id="soundIcon"></i></div>
                                        <a href="{{ route('services.index') }}" style="color: var(--primary); font-weight: 600; font-size: 0.75rem; text-decoration:none;">View list</a>
                                    </div>
                                </div>
                                <div class="notif-scroll-area custom-scrollbar" id="noti-list" style="max-height: 350px; overflow-y: auto;">
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-loader-alt bx-spin fs-3 mb-2 opacity-50"></i>
                                        <p class="mb-0" style="font-size: 0.75rem; font-weight: 500;">Fetching data...</p>
                                    </div>
                                </div>
                                <div class="p-2 border-top text-center" style="background: var(--surface);">
                                    <a href="{{ route('services.index') }}" style="color: var(--text-muted); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; text-decoration: none;">View All Bookings</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="vr mx-1" style="width: 1px; background: var(--border-color);"></div>

                        <!-- User Profile Dropdown -->
                        @if($currentUser)
                        <div class="nav-item dropdown">
                            <div class="d-flex align-items-center border rounded-pill p-1 bg-white cursor-pointer dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="border-color: var(--border-color) !important; transition: 0.2s;" onmouseover="this.style.borderColor='var(--primary)';" onmouseout="this.style.borderColor='var(--border-color)';">
                                <div class="text-end me-3 ms-3 d-none d-md-block">
                                    <div class="fw-bold text-uppercase" style="font-family: var(--font-display); font-size: 0.75rem; letter-spacing: 0.5px; color: var(--text-main);">{{ $currentUser->full_name ?? 'USER PROFILE' }}</div>
                                    <div class="d-flex align-items-center justify-content-end gap-1 mt-1" style="font-size: 0.6rem; font-weight: 600; color: #10B981;">
                                        <span class="pulse-circle rounded-circle" style="width: 6px; height: 6px; background: #10B981;"></span> ACTIVE SESSION
                                    </div>
                                </div>
                                <img src="{{ url($currentUser->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $currentUser->profile_pic) }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->full_name ?? 'User') }}&background=EEF2FF&color=4F46E5&bold=true'" class="rounded-circle" width="36" height="36" style="border: 2px solid #FFF; box-shadow: 0 0 0 1px var(--border-color); object-fit: cover;">
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end border shadow-lg mt-3 p-2" style="border-color: var(--border-color); border-radius: 12px; min-width: 220px;">
                                <li class="p-2 border-bottom mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ url($currentUser->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . $currentUser->profile_pic) }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUser->full_name ?? 'User') }}&background=EEF2FF&color=4F46E5&bold=true'" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 small fw-bold" style="color: var(--text-main);">{{ $currentUser->full_name }}</h6>
                                            <small style="font-size: 11px; color: var(--text-muted); font-weight: 500;">{{ $currentUser->username }}</small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 rounded fw-bold d-flex align-items-center" href="{{ url('/logout') }}" style="font-size: 13px; color: #DC2626; transition: 0.2s;" onmouseover="this.style.background='#FEF2F2';" onmouseout="this.style.background='transparent';">
                                        <i class="bx bx-power-off me-2"></i> Log Out
                                    </a>
                                </li>
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

    <!-- Core Scripts -->
    <script src="{{ asset('backend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('backend/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('backend/assets/js/main.js') }}"></script>
    <script src="{{ asset('backend/assets/js/dashboards-analytics.js') }}"></script>

    <script>
        // System Clock Logic
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

        // Security: Multi-Device Login Monitor
        @if($currentUser)
            setInterval(function() {
                fetch('/check-auth-session')
                    .then(response => response.json())
                    .then(data => {
                        if (data.active === false) {
                            Swal.fire({
                                icon: 'warning', title: 'Session Expired', text: 'This account was accessed from another device. You have been logged out.',
                                confirmButtonText: 'Acknowledge', confirmButtonColor: '#4F46E5', allowOutsideClick: false,
                                customClass: { popup: 'rounded-4 shadow-sm border-0' }
                            }).then(() => { window.location.href = "{{ url('login') }}"; });
                        }
                    }).catch(e => console.error('Auth Check Silent Error'));
            }, 10000); // Reduced polling to save server resources
        @endif

        // ==========================================
        // SECURE AJAX POLLING FOR NOTIFICATIONS
        // ==========================================
        let isSoundEnabled = localStorage.getItem('mgNotificationSound') !== 'false';
        let continuousFailures = 0; // Tracking failures to prevent 500 error loops

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

        let lastNotifId = 0, isFirstLoad = true;

        @if($canSeeServiceNotif)
        function fetchHeaderNotifications() {
            if(continuousFailures > 5) return; // Circuit breaker: Stop hitting server if it's continuously failing

            $.ajax({
                url: "{{ route('services.notifications') }}", 
                type: "GET",
                success: function(response) {
                    continuousFailures = 0; // Reset circuit breaker
                    let count = response.count || 0; 
                    let data = response.data || [];
                    if (response.data && Array.isArray(response.data.data)) data = response.data.data;

                    if (count > 0) $('#notifCountBadge').text(count > 99 ? '99+' : count).removeClass('d-none');
                    else $('#notifCountBadge').addClass('d-none');

                    let html = '';
                    if (Array.isArray(data) && data.length > 0) {
                        let latestId = data[0].id || 0;
                        if (!isFirstLoad && latestId > lastNotifId && isSoundEnabled) {
                            new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3').play().catch(e=>{});
                        }
                        lastNotifId = latestId;

                        data.forEach(function(item) {
                            let customerName = item.customer || item.customer_name || 'Customer';
                            let timeStr = item.time || item.created_at || '';
                            let plateNum = item.plate || item.plate_number || 'N/A';
                            html += `
                                <a href="{{ route('services.index') }}?search=${plateNum}" class="dropdown-item d-flex align-items-start p-3 border-bottom hover-bg-slate-50" style="transition: 0.2s;">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem; color: var(--text-main);">${customerName}</h6>
                                            <small style="font-size: 10px; color: var(--text-muted); font-weight: 500;"><i class="bx bx-time-five"></i> ${timeStr}</small>
                                        </div>
                                        <p class="mb-0 text-truncate" style="font-size: 0.75rem; color: var(--text-muted);">Plate: <span style="color: #DC2626; font-weight: 700; font-family: var(--font-mono);">${plateNum}</span></p>
                                    </div>
                                </a>`;
                        });
                    } else {
                        html = `<div class="text-center py-5"><i class="bx bx-check-shield fs-1 mb-2" style="color: #10B981; opacity: 0.5;"></i><p class="mb-0" style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted);">No pending bookings today.</p></div>`;
                    }
                    $('#noti-list').html(html);
                    isFirstLoad = false;
                },
                error: function(xhr) { 
                    continuousFailures++;
                    if(continuousFailures === 1) { // Only show error state on first failure to prevent UI flicker
                        $('#noti-list').html(`<div class="text-center py-4" style="color: #DC2626;"><i class="bx bx-error-circle fs-2 mb-2"></i><p style="font-size:0.75rem;">Sync Unavailable</p></div>`); 
                    }
                }
            });
        }

        // Initialize polling with a safer interval (20 seconds) to reduce server load
        fetchHeaderNotifications();
        setInterval(fetchHeaderNotifications, 20000); 
        @endif

    </script>
    @stack('scripts')
</body>
</html>