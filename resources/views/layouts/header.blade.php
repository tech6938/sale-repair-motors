<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="{{ route('dashboard') }}" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu">
                    <em class="icon ni ni-menu"></em>
                </a>
            </div>

            <div class="nk-header-brand d-xl-none">
                <a href="javascript:void(0);" class="logo-link">
                    <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png') }}" srcset="{{ asset('assets/images/logo2x.png') }} 2x" alt="logo">
                    <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png') }}" srcset="{{ asset('assets/images/logo-dark2x.png') }} 2x" alt="logo-dark">
                </a>
            </div>

            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle me-n1" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm auth-avatar">
                                    {!! getAvatarHtml() !!}
                                </div>
                                <div class="user-info d-none d-xl-block" style="margin-left: 0.5rem;">
                                    <div class="user-name dropdown-indicator">{{ auth()->user()->name }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar auth-avatar">
                                        {!! getAvatarHtml() !!}
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ auth()->user()->name }}</span>
                                        <span class="sub-text">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <a href="{{ route('profile.edit') }}">
                                            <em class="icon ni ni-user-alt"></em>
                                            <span>View Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dark-switch" href="javascript:void(0);" data-url="{{ route('settings.store') }}" data-value="{{ !$isDarkMode }}">
                                            <em class="icon ni ni-moon"></em>
                                            <span>Dark Mode</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>
                                        <form method="post" action="{{ route('logout') }}">
                                            @csrf
                                            <a href="javascript:void(0);" onclick="event.preventDefault();this.closest('form').submit();">
                                                <em class="icon ni ni-signout"></em>
                                                <span>Sign out</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
