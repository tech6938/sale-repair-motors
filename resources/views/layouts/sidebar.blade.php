<div class="nk-sidebar nk-sidebar-fixed is-light {{ $isCompactSidebar ? 'is-compact' : '' }}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ route('dashboard') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png') }}" srcset="{{ asset('assets/images/logo2x.png') }} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png') }}" srcset="{{ asset('assets/images/logo-dark2x.png') }} 2x" alt="logo-dark">
                <img class="logo-small logo-img logo-img-small" src="{{ asset('assets/images/logo-small.png') }}" srcset="{{ asset('assets/images/logo-small2x.png') }} 2x" alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger me-n2">
            <a href="javascript:void(0);" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu">
                <em class="icon ni ni-arrow-left"></em>
            </a>
            <a href="javascript:void(0);" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu" id="toggle-sidebar" data-url="{{ route('settings.store') }}" data-value="{{ !$isCompactSidebar }}">
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
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())

                    <li class="nk-menu-item">
                        <a href="{{ route('admins.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-users-fill"></em></span>
                            <span class="nk-menu-text">Admin</span>
                        </a>
                    </li>

                    <li class="nk-menu-item">
                        <a href="{{ route('staffs.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Staff</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isPreparationManager())
                    <li class="nk-menu-item">
                        <a href="{{ route('prepration-managers.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Preperation Manager</span>
                        </a>
                    </li>

                    <li class="nk-menu-item">
                        <a href="{{ route('prepration-staffs.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Preperation Staff</span>
                        </a>
                    </li>

                    @endif

                    @if(auth()->user()->isPreparationManager())
                    <li class="nk-menu-item">
                        <a href="{{ route('prepration-staffs.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Assigned Vehicles</span>
                        </a>
                    </li>

                    @endif
                @if(auth()->user()->isSuperAdmin())
                    <li class="nk-menu-item">
                        <a href="{{ route('vehicles-assign.index') }}" class="nk-menu-link">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list-fill"></em></span>
                            <span class="nk-menu-text">Assign Vehicles</span>
                        </a>
                    </li>

                    @endif

                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <li class="nk-menu-item">
                        <a href="#" class="nk-menu-link" onclick="toggleDropdown(event)">
                            <span class="nk-menu-icon"><em class="icon ni ni-truck"></em></span>
                            <span class="nk-menu-text">Vehicle</span>
                            <span class="dropdown-arrow">&#9662;</span> <!-- Down arrow icon -->
                        </a>

                        <ul class="dropdown-submenu submenudrop" style="display: none; padding-left: 30px; background-color:#EBEEF2;">
                            <li><a class="nk-menu-link" href="{{ route('vehicles.index') }}">New Vehicles</a></li>
                            <li><a class="nk-menu-link" href="{{ route('vehicles.index') }}">Ready for Picture</a></li>
                            <li><a class="nk-menu-link" href="{{ route('vehicles.index') }}">Ready for Sale</a></li>
                        </ul>
                    </li>

                    @endif
                </ul>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function toggleDropdown(event) {
        event.preventDefault();
        const submenudrop = event.currentTarget.nextElementSibling;
        submenudrop.style.display = submenudrop.style.display === "block" ? "none" : "block";
    }

</script>

@endpush
