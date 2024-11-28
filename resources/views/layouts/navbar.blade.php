@php
    
    $activeSubMenus = session()->get('activeSubMenus');
    $activeMenus = session()->get('activeMenus');

@endphp

<style>
    .logo-center img {
        /* padding: 10px; */
        width: 110px;
        margin-top: 15px;
        margin-left: 70px;
        padding-top: 35px;
        padding-bottom: 35px;
    }

    .app-brand .layout-menu-toggle {
        position: absolute;
        left: 15rem;
        top: 4rem;
        border-radius: 50%;
    }

    .app-brand .layout-menu-toggle {
        background-color: #CE181E;
        border: 7px solid #f5f5f9;
    }

    .bg-menu-theme .menu-inner>.menu-item.active>.menu-link {
        color: #DEAA5E;
        /* background-color: #fde9e9 !important; */
        background-color: #f5e2c5 !important;
    }

    .bg-menu-theme .menu-inner>.menu-item.active:before {
        background: #DEAA5E;
    }

    .menu-link.logout-btn {
        color: #fff;
        background: #DEAA5E;
    }

    .menu-link.logout-btn:hover {
        color: #fff;
        background: red !important;
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
