@php
    $activeSubMenus = session()->get('activeSubMenus') ?? [];
    $activeMenus = session()->get('activeMenus') ?? [];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ==================== ULTRA-CLEAN PROFESSIONAL SAAS THEME ==================== */
    :root {
        --sidebar-bg: #0B1120; /* Deepest blue-gray */
        --sidebar-border: #1E293B; /* Subtle border */
        
        --text-active: #FFFFFF; /* Pure white for active items */
        --text-hover: #E2E8F0; /* Bright gray for hover */
        --text-muted: #94A3B8; /* Muted slate for default */
        --text-dim: #475569; /* Darker slate for headers */
        
        --hover-bg: rgba(255, 255, 255, 0.04); /* Extremely subtle hover */
        --active-bg: rgba(255, 255, 255, 0.06); /* Very subtle active bg */
        --active-folder: rgba(15, 23, 42, 0.6); /* For opened submenus */
        
        /* Brand Accent - Used ONLY for tiny indicators, not text */
        --accent: #EAB308; /* TF Gold */
        
        --transition-smooth: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #layout-menu {
        font-family: 'Inter', sans-serif !important;
        width: 260px;
        background: var(--sidebar-bg) !important; 
        border-right: 1px solid var(--sidebar-border);
        transition: width 0.3s ease;
    }
    
    /* Logo Section */
    .logo-center {
        display: flex; flex-direction: column; align-items: center; padding: 32px 0 24px;
        background: transparent; border-bottom: 1px solid var(--sidebar-border);
    }
    .logo-center img { width: 105px; transition: var(--transition-smooth); opacity: 0.95; }
    .logo-center img:hover { transform: scale(1.02); opacity: 1; }
    
    /* Menu Items General */
    .menu-inner { padding: 24px 0; overflow-x: hidden; }
    .menu-item { margin: 2px 16px; border-radius: 8px; transition: var(--transition-smooth); position: relative; }
    
    /* Folder Open State */
    .menu-item.open { background: var(--active-folder); }
    
    .menu-link {
        color: var(--text-muted) !important; font-size: 13.5px; font-weight: 500; padding: 10px 14px; 
        border-radius: 8px; background: transparent; transition: var(--transition-smooth);
        cursor: pointer; display: flex; align-items: center; gap: 12px;
        position: relative; /* Needed for active indicator */
    }
    .menu-link div { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; flex-grow: 1; }
    .menu-link i { font-size: 1.25rem; color: var(--text-muted); transition: var(--transition-smooth); flex-shrink: 0; opacity: 0.8; }
    
    .menu-link .menu-toggle-icon { font-size: 1rem; color: var(--text-dim); transition: var(--transition-smooth); }
    
    /* Hover State - Clean text color change, no heavy background */
    .menu-item:not(.active) > .menu-link:hover { 
        background: var(--hover-bg) !important; 
        color: var(--text-hover) !important; 
    }
    .menu-item:not(.active) > .menu-link:hover i { color: var(--text-hover) !important; opacity: 1; }
    
    /* ==================== NEW CLEAN ACTIVE STATE ==================== */
    /* Pure white text instead of gold, with a subtle gold left border indicator */
    .menu-item.active > .menu-link { 
        background: var(--active-bg) !important; 
        color: var(--text-active) !important; 
        font-weight: 600; 
    }
    .menu-item.active > .menu-link i { color: var(--text-active) !important; opacity: 1; }
    
    /* Tiny Gold Indicator Line on the left */
    .menu-item.active > .menu-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20%;
        height: 60%;
        width: 3px;
        background-color: var(--accent);
        border-radius: 0 4px 4px 0;
    }
    
    /* Submenu Headers */
    .menu-header { margin: 28px 20px 8px 20px; }
    .menu-header-text { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-dim); }
    
    /* ==================== CLEAN SUB-MENU ==================== */
    .menu-sub { 
        margin-left: 24px; padding-left: 12px; padding-bottom: 8px; margin-top: 2px;
        border-left: 1px solid var(--sidebar-border);
        display: none; 
    }
    .menu-item.open > .menu-sub, .menu-item.active > .menu-sub { display: block; } 
    
    .menu-sub .menu-link { padding: 8px 12px; font-size: 13px; border-radius: 6px; }
    
    .menu-sub .menu-item:not(.active) > .menu-link:hover {
        color: var(--text-hover) !important;
        background: var(--hover-bg) !important;
    }
    
    /* Clean Active Submenu - White text, tiny gold dot (No glow) */
    .menu-sub .menu-item.active > .menu-link { 
        background: transparent !important; 
        color: var(--text-active) !important; 
        font-weight: 600; 
    }
    .menu-sub .menu-item.active > .menu-link::before {
        content: ''; 
        position: absolute; 
        left: -14px; 
        top: 50%; 
        transform: translateY(-50%);
        width: 5px; 
        height: 5px; 
        background-color: var(--accent); 
        border-radius: 50%; 
    }
    
    /* ==================== CLEAN LOGOUT BUTTON ==================== */
    .logout-btn { 
        margin: 24px 16px 16px !important; 
        background: transparent !important; 
        color: var(--text-dim) !important; /* Subtle gray by default */
        border: 1px solid transparent; 
        border-radius: 8px;
        justify-content: center;
        transition: var(--transition-smooth);
    }
    .logout-btn i { color: var(--text-dim) !important; }
    .logout-btn:hover { 
        background: rgba(239, 68, 68, 0.05) !important; 
        color: #F87171 !important; /* Turns red only on hover */
        border-color: rgba(239, 68, 68, 0.2);
    }
    .logout-btn:hover i { color: #F87171 !important; }
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="logo-center">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large d-block d-xl-none mobile-close-btn">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <ul class="menu-inner py-2">
        <li class="menu-item @if(Request::is('/') || Request::is('dashboard*')) active @endif">
            <a href="{{ url('') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-header"><span class="menu-header-text">View Pages</span></li>

        @foreach ($activeMenus as $index => $activeMenu)
        @php
            $isParentActive = false;
            if ($activeMenu->route == '#') {
                foreach ($activeSubMenus as $sub) {
                    if ($sub->sub_of == $activeMenu->menuid) {
                        $baseRoute = ltrim($sub->route, '/');
                        // មេម៉ឺនុយអាចប្រើ * បាន ព្រោះយើងចង់ឱ្យវាបើក (Open) ឱ្យតែត្រូវនឹងកូនណាមួយ
                        if (request()->is($baseRoute) || request()->is($baseRoute . '/*')) {
                            $isParentActive = true;
                            break;
                        }
                    }
                }
            } else {
                $baseRoute = ltrim($activeMenu->route, '/');
                if (request()->is($baseRoute) || request()->is($baseRoute . '/*')) {
                    $isParentActive = true;
                }
            }
        @endphp

        <li class="menu-item {{ $isParentActive ? 'active open' : '' }}">
            <a href="{{ $activeMenu->route }}"
               class="menu-link @if ($activeMenu->route == '#') menu-toggle @endif" title="{{ $activeMenu->menuName }}">
                <i class="menu-icon tf-icons bx {{ $activeMenu->icon }}"></i>
                <div>{{ $activeMenu->menuName }}</div>
            </a>
            
            @if ($activeMenu->route == '#')
            <ul class="menu-sub">
                @foreach ($activeSubMenus as $activeSubMenu)
                    @if ($activeSubMenu->sub_of == $activeMenu->menuid)
                        @php
                            $baseRoute = ltrim($activeSubMenu->route, '/');
                            $currentPath = request()->path();
                            
                            // ១. ឆែកមើលថា URL ដូចគ្នាបេះបិទឬអត់ (ការពារកុំឱ្យ Active ព្រមគ្នា)
                            $isSubActive = ($currentPath === $baseRoute);
                            
                            // ២. ការពារករណី Edit/Show (ឧទាហរណ៍: services/1/edit គឺត្រូវតែ Active ម៉ឺនុយ services ដដែល)
                            if (!$isSubActive && str_starts_with($currentPath, $baseRoute . '/')) {
                                $remainingPath = substr($currentPath, strlen($baseRoute) + 1);
                                // បើចុងកន្ទុយផ្តើមដោយលេខ (ID) នោះឱ្យវា Active ដដែល
                                if (preg_match('/^[0-9]+/', $remainingPath)) {
                                    $isSubActive = true;
                                }
                            }
                        @endphp
                        
                        <li class="menu-item {{ $isSubActive ? 'active' : '' }}">
                            <a href="{{ url($activeSubMenu->route) }}" class="menu-link" title="{{ $activeSubMenu->menuName }}">
                                <div>{{ $activeSubMenu->menuName }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
            @endif
        </li>
    @endforeach

        <li class="menu-item">
            <a href="{{ url('/logout') }}" class="menu-link logout-trigger logout-btn">
                <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
                <div style="font-weight: 600">Sign Out</div>
            </a>
        </li>
    </ul>
</aside>

<audio id="sound-default" preload="auto"></audio>
<audio id="sound-logout" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2574/2574-preview.mp3" type="audio/mpeg">
</audio>

<script>
    function playClickSound(type = 'default') {
        try {
            const audio = document.getElementById('sound-' + type);
            if (audio && audio.innerHTML.includes('<source')) {
                audio.pause();
                audio.currentTime = 0;
                audio.play().catch(e => console.log("Sound play prevented."));
            }
        } catch(err) {}
    }

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Standard menu links
        document.querySelectorAll('.menu-link:not(.logout-trigger)').forEach(link => {
            link.addEventListener('click', function() {
                playClickSound('default');
            });
        });

        // 2. Handle Logout specifically
        document.querySelectorAll('.logout-trigger').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if(href) {
                    e.preventDefault(); 
                    playClickSound('logout');
                    setTimeout(() => { window.location.href = href; }, 300);
                }
            });
        });
    });
</script>