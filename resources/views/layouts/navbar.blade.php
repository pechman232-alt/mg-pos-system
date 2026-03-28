@php
    $activeSubMenus = session()->get('activeSubMenus') ?? [];
    $activeMenus = session()->get('activeMenus') ?? [];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ==================== SIDEBAR STYLING ==================== */
#layout-menu {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    width: 260px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%) !important;
    transition: width 0.3s ease;
}

.logo-center {
    display: flex; flex-direction: column; align-items: center; padding: 25px 0;
    background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo-center img { width: 110px; }

.menu-inner .menu-item { margin: 4px 15px; border-radius: 10px; }

.menu-inner .menu-link {
    color: rgba(255, 255, 255, 0.85) !important;
    font-size: 14px; font-weight: 600; padding: 12px 16px; border-radius: 10px;
    background: rgba(255, 255, 255, 0.05); transition: all 0.3s ease;
    cursor: pointer;
}

.menu-inner .menu-link:hover {
    background: rgba(255, 255, 255, 0.15); color: #ffffff !important;
    transform: translateX(5px);
}

.menu-item.active > .menu-link {
    background: #ffffff !important; color: #764ba2 !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Logout Button Specific Style */
.logout-btn { 
    background: rgba(255, 77, 77, 0.15) !important; color: #ffb3b3 !important; margin-top: 20px;
}
.logout-btn:hover {
    background: #ff4d4d !important; color: #ffffff !important;
}
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="logo-center">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
    </div>

    <ul class="menu-inner py-2">
        <li class="menu-item @if(Request::is('/')) active @endif">
            <a href="{{ url('') }}" class="menu-link app-click-trigger">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-header"><span class="menu-header-text">View Pages</span></li>

        @foreach ($activeMenus as $index => $activeMenu)
            <li class="menu-item">
                <a href="{{ $activeMenu->route }}"
                   class="menu-link app-click-trigger @if ($activeMenu->route == '#') menu-toggle @endif">
                    <i class="menu-icon tf-icons bx {{ $activeMenu->icon }}"></i>
                    <div>{{ $activeMenu->menuName }}</div>
                </a>
                @if ($activeMenu->route == '#')
                <ul class="menu-sub">
                    @foreach ($activeSubMenus as $activeSubMenu)
                        @if ($activeSubMenu->sub_of == $activeMenu->menuid)
                            <li class="menu-item @if (request()->is($activeSubMenu->route)) active @endif">
                                <a href="{{ url($activeSubMenu->route) }}" class="menu-link app-click-trigger">
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
            <a href="{{ url('/logout') }}" class="menu-link logout-btn logout-trigger">
                <i class="menu-icon tf-icons bx bx-exit"></i>
                <div style="font-weight: 800">Logout</div>
            </a>
        </li>
    </ul>
</aside>

<audio id="sound-default" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3" type="audio/mpeg">
</audio>
<audio id="sound-logout" preload="auto">
    <source src="https://assets.mixkit.co/active_storage/sfx/2574/2574-preview.mp3" type="audio/mpeg">
</audio>

<script>
    /**
     * Reusable Sound Function
     * Usage: playClickSound('default') or playClickSound('logout')
     */
    function playClickSound(type = 'default') {
        const audio = document.getElementById('sound-' + type);
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
            audio.play().catch(e => {});
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Standard Menu Links
        document.querySelectorAll('.app-click-trigger').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                playClickSound('default');

                if (href && href !== '#' && !href.startsWith('javascript')) {
                    e.preventDefault();
                    setTimeout(() => { window.location.href = href; }, 150);
                }
            });
        });

        // 2. Logout Specific Link
        document.querySelectorAll('.logout-trigger').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                playClickSound('logout'); // Unique sound for logout

                e.preventDefault();
                setTimeout(() => { window.location.href = href; }, 400); // Slightly longer delay for logout sound
            });
        });

        // 3. Browser Audio Unlocker
        const unlock = () => {
            playClickSound('default');
            document.removeEventListener('click', unlock);
        };
        document.addEventListener('click', unlock);

        // Sidebar Auto-Expansion
        const activeSub = document.querySelector('.menu-sub .menu-item.active');
        if (activeSub) {
            const parent = activeSub.closest('.menu-item');
            if (parent) parent.classList.add('open', 'active');
        }
    });
</script>