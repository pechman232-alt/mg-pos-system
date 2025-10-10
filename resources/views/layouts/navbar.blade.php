@php
    
    $activeSubMenus = session()->get('activeSubMenus');
    $activeMenus = session()->get('activeMenus');

@endphp

<style>
   /* Sidebar container animation */
#layout-menu {
    width: 250px; /* Adjust sidebar width */
    opacity: 0;
    transform: translateX(-50px);
    animation: slideInSidebar 0.6s forwards;
    transition: width 0.3s ease;
}

/* Slide-in sidebar */
@keyframes slideInSidebar {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
/* Sidebar logo container */
.logo-center {
    display: flex;
    flex-direction: column;
    align-items: center; /* Horizontal center */
    justify-content: center; /* Vertical center inside its container */
    padding: 20px 0;
    position: relative;
}

/* Logo image */
.logo-center img {
    width: 120px;
    opacity: 0;
    transform: scale(0.8);
    animation: fadeInLogo 0.6s forwards;
    animation-delay: 0.2s;
}

/* Optional: app brand toggle (for mobile) */
.logo-center .app-brand {
    margin-top: 10px;
    display: flex;
    align-items: center;
}

/* Logo fade-in keyframes */
@keyframes fadeInLogo {
    to {
        opacity: 1;
        transform: scale(1);
    }
}


@keyframes fadeInLogo {
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Menu item fade-in & slide */
.menu-inner .menu-item {
    opacity: 0;
    transform: translateX(-20px);
    animation: fadeInMenuItem 0.5s forwards;
}

/* Stagger menu items */
.menu-inner .menu-item:nth-child(1) { animation-delay: 0.3s; }
.menu-inner .menu-item:nth-child(2) { animation-delay: 0.4s; }
.menu-inner .menu-item:nth-child(3) { animation-delay: 0.5s; }
/* Extend for more items dynamically if needed */

/* Fade-in animation */
@keyframes fadeInMenuItem {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Hover effect for menu items */

/* Icon and text hover slide */
.menu-inner .menu-link i,
.menu-inner .menu-link div {
    transition: all 0.3s ease;
}

.menu-inner .menu-item:hover i,
.menu-inner .menu-item:hover div {
    transform: translateX(5px);
}

/* Active menu styling */


/* Submenu collapse & fade animation */
.menu-inner .menu-sub {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.4s ease, opacity 0.4s ease;
}

.menu-item.open > .menu-sub {
    max-height: 500px; /* enough to show all submenus */
    opacity: 1;
}
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="logo-center">
        <img src="{{ asset('assets/images/logo.png') }}" alt="">
        <div class="app-brand">
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>
    </div>

    <ul class="menu-inner py-1">

        <li class="menu-item active">
            <a href="/" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">View Pages</span>
        </li>

        @foreach ($activeMenus as $activeMenu)
            <li class="menu-item">
                <a href="{{ $activeMenu->route }}"
                    class="menu-link @if ($activeMenu->route == '#')menu-toggle @endif">
                    <i class="menu-icon tf-icons bx {{ $activeMenu->icon }}"></i>
                    <div data-i18n="Layouts">{{ $activeMenu->menuName }}</div>
                </a>
                <ul class="menu-sub">
                    @foreach ($activeSubMenus as $activeSubMenu)
                        @if ($activeSubMenu->sub_of == $activeMenu->menuid)
                            <li class="menu-item @if (request()->is($activeSubMenu->route)) active @endif">
                                <a href="{{ url($activeSubMenu->route) }}" class="menu-link">
                                    <div data-i18n="Notifications">{{ $activeSubMenu->menuName }}</div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endforeach

        <script>
            $('.active').parent('ul').parent('li').addClass('open');
        </script>

        {{-- ------------------------------------------- Logouts ------------------------------------------------ --}}
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Logout</span></li>
        <li class="menu-item">
            <a href="{{ url('/logout') }}" class="menu-link logout-btn">
                <i class="menu-icon tf-icons bx bx-exit"></i>
                <div data-i18n="Analytics" style="font-weight: bold">Logout</div>
            </a>
        </li>
    </ul>
</aside>
