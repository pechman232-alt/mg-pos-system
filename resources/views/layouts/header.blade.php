<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 d-xl-none">
                <a class="nav-item nav-link px-0" href="javascript:void(0)"><i class="bx bx-menu bx-sm"></i></a>
            </div>
        </div>

        <div class="flex-grow-1 d-flex justify-content-center px-4 d-none d-md-flex">
            <div class="search-minimal-group d-flex align-items-center gap-3 w-100" style="max-width: 400px; background: #f8fafc; padding: 8px 15px; border-radius: 12px; border: 1px solid #eef2f6;">
                <i class="bx bx-search fs-5 text-slate-400"></i>
                <input type="text" placeholder="Search Command Center..." style="border:none; outline:none; background:transparent; font-weight:600; width:100%; font-size: 13px;">
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 ms-auto">
            
            <div class="timer-badge d-none d-lg-flex align-items-center gap-2 px-3 py-1 bg-label-primary rounded-pill">
                <i class="bx bx-bolt-circle bx-spin" style="color: var(--tf-indigo);"></i>
                <span id="headerLiveClock" style="font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 11px; color: var(--tf-indigo);">00:00:00 AM</span>
            </div>

            <div class="nav-item dropdown">
                <a href="javascript:void(0);" class="icon-action-btn position-relative" data-bs-toggle="dropdown" id="notifBellIcon">
                    <i class="bx bx-bell fs-4 text-slate-700"></i>
                    @if(auth('users')->check())
                        @php $unreadCount = auth('users')->user()->unreadNotifications()->count(); @endphp
                        <span class="notif-badge-count pulse-animation {{ $unreadCount > 0 ? '' : 'd-none' }}" id="notifCountBadge">{{ $unreadCount }}</span>
                    @endif
                </a>
                
                <div class="dropdown-menu dropdown-menu-end notif-menu shadow-lg border-0 mt-3 p-0" style="width: 350px; border-radius: 15px; overflow: hidden;">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <span class="fw-800 text-dark small">ALERTS CENTER</span>
                        <a href="javascript:void(0);" id="markAllRead" class="text-indigo-600 fw-bold x-small text-decoration-none">Mark all as read</a>
                    </div>
                    
                    <div class="notif-scroll-area custom-scrollbar" id="notifListContainer" style="max-height: 350px; overflow-y: auto;">
                        @if(auth('users')->check())
                            @forelse(auth('users')->user()->unreadNotifications as $notification)
                                <a href="{{ isset($notification->data['id_primary']) ? route('issues.view', $notification->data['id_primary']) : '#' }}" class="dropdown-item d-flex align-items-start p-3 border-bottom notif-item-hover">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-file-blank fs-5"></i></span>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 small fw-800 text-dark text-truncate">{{ $notification->data['issue_id'] ?? 'TICKET' }}</h6>
                                            <small class="text-indigo-400 fw-bold" style="font-size: 9px;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-muted x-small text-truncate">
                                            {{ $notification->data['message'] ?? 'New notification received' }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-5 text-muted small">
                                    <i class="bx bx-bell-off d-block fs-1 opacity-25 mb-2"></i>
                                    No new alerts found.
                                </div>
                            @endforelse
                        @endif
                    </div>
                    
                    <div class="p-2 border-top text-center bg-white">
                        <a href="javascript:void(0);" class="text-slate-500 fw-bold x-small text-decoration-none hover-indigo">View All Activity Log</a>
                    </div>
                </div>
            </div>

            <div class="v-line" style="width: 1px; height: 25px; background: #e2e8f0;"></div>

            @if(auth('users')->check())
            <div class="nav-item dropdown">
                <div class="profile-pill dropdown-toggle hide-arrow d-flex align-items-center gap-2 p-1" data-bs-toggle="dropdown" style="cursor:pointer;">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-800 text-dark small leading-none">{{ auth('users')->user()?->full_name ?? 'Staff Member' }}</div>
                        <div class="text-indigo-500 fw-bold uppercase" style="font-size: 9px; letter-spacing: 0.5px;">TF Verified</div>
                    </div>
                    <div class="avatar avatar-online">
                        <img src="{{ url(auth('users')->user()->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . auth('users')->user()->profile_pic) }}" 
                             class="rounded-circle profile-img-header shadow-sm">
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3" style="border-radius: 12px; min-width: 200px;">
                    <li class="p-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm">
                                <img src="{{ url(auth('users')->user()->profile_pic == '' ? 'backend/assets/img/avatars/1.png' : '/imageUser/' . auth('users')->user()->profile_pic) }}" class="rounded-circle">
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-800">{{ auth('users')->user()?->full_name }}</h6>
                                <small class="text-muted" style="font-size: 10px;">{{ auth('users')->user()?->username }}</small>
                            </div>
                        </div>
                    </li>
                    <li><a class="dropdown-item py-2" href="#"><i class="bx bx-user me-2"></i> My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger fw-bold" href="{{ url('/logout') }}"><i class="bx bx-power-off me-2"></i> Log Out</a></li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</nav>

<script>
$(document).ready(function() {
    // ⏰ មុខងារបង្ហាញម៉ោងបច្ចុប្បន្នលើ Header
    function updateHeaderTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true 
        });
        $('#headerLiveClock').text(timeStr);
    }
    setInterval(updateHeaderTime, 1000);
    updateHeaderTime();

    // 🔊 បន្តស្គ្រីប Notif របស់បងខាងក្រោម... (កូដចាស់បងដើរល្អហើយ)
});
</script>