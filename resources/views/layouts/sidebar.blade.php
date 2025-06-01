<div class="nk-sidebar nk-sidebar-fixed is-light {{ $isCompactSidebar ? 'is-compact' : '' }}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png') }}" srcset="{{ asset('assets/images/logo2x.png') }} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png') }}" srcset="{{ asset('assets/images/logo-dark2x.png') }} 2x" alt="logo-dark">
                <img class="logo-small logo-img logo-img-small" src="{{ asset('assets/images/logo-small.png') }}" srcset="{{ asset('assets/images/logo-small2x.png') }} 2x"
                    alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="javascript:void(0);" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                <em class="icon ni ni-arrow-left"></em>
            </a>
            <a href="javascript:void(0);" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu" id="toggle-sidebar"
                data-url="{{ route('settings.store') }}" data-value="{{ !$isCompactSidebar }}">
                <em class="icon ni ni-menu"></em>
            </a>
        </div>
    </div>

    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>

                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="{{ route('dashboard') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('admins.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                            <span class="nk-menu-text">Admins</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('staffs.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Staffs</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('vehicles.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-truck"></em></span>
                            <span class="nk-menu-text">Vehicles</span>
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</div>
