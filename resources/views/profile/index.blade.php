@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block">
                <div class="card card-bordered">
                    <div class="card-aside-wrap">
                        <div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg toggle-screen-lg" data-content="userAside"
                            data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="card-inner-group" data-simplebar="init">
                                <div class="simplebar-wrapper" style="margin: 0px;">
                                    <div class="simplebar-height-auto-observer-wrapper">
                                        <div class="simplebar-height-auto-observer"></div>
                                    </div>
                                    <div class="simplebar-mask">
                                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                            <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                                                style="height: auto; overflow: hidden;">

                                                <div class="card-inner">
                                                    <div class="user-card">
                                                        <div class="user-avatar auth-avatar">{!! getAvatarHtml() !!}</div>
                                                        <div class="user-info">
                                                            <span class="lead-text auth-name">{{ auth()->user()->name }}</span>
                                                            <span class="sub-text">{{ auth()->user()->email }}</span>
                                                        </div>
                                                        <div class="user-action">
                                                            <div class="dropdown">
                                                                <a class="btn btn-icon btn-trigger mr-n2" data-bs-toggle="dropdown" href="javascript:void(0);">
                                                                    <em class="icon ni ni-more-v"></em>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li>
                                                                            <a href="{{ route('profile.avatar.edit') }}" async-modal data-method="post">
                                                                                <em class="icon ni ni-camera-fill"></em>
                                                                                <span>Change Photo</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-inner p-0">
                                                    <ul class="link-list-menu">
                                                        <li>
                                                            <a class="nav-link active" data-toggle="tab" href="#personal-tab">
                                                                <em class="icon ni ni-user-fill-c"></em>
                                                                <span>Account Information</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('profile.password.edit') }}" async-modal data-method="post">
                                                                <em class="icon ni ni-lock-alt-fill"></em>
                                                                <span>Change Password</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="simplebar-placeholder" style="width: auto; height: 504px;"></div>
                                </div>
                                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                    <div class="simplebar-scrollbar simplebar-visible" style="width: 0px; display: none;"></div>
                                </div>
                                <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                                    <div class="simplebar-scrollbar simplebar-visible" style="height: 0px; display: none;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-inner card-inner-lg">
                            <div class="tab-content">
                                <div class="tab-pane active" id="personal-tab">
                                    <div class="nk-block-head nk-block-head-lg pb-0">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h4 class="nk-block-title">Account Information</h4>
                                                <div class="nk-block-des">
                                                    <p>Basic info, like your name and address, that you use on {{ config('app.name') }}.</p>
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="javascript:void(0);" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside">
                                                    <em class="icon ni ni-menu-alt-r"></em>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="nk-data data-list">
                                        <a href="{{ route('profile.edit') }}" class="data-item" async-modal data-method="post">
                                            <div class="data-col">
                                                <span class="data-label">Full Name</span>
                                                <span id="name" class="data-value overflow-auto">{{ auth()->user()->name }}</span>
                                            </div>
                                            <div class="data-col data-col-end">
                                                <span class="data-more">
                                                    <em class="icon ni ni-forward-ios"></em>
                                                </span>
                                            </div>
                                        </a>

                                        <div class="data-item">
                                            <div class="data-col">
                                                <span class="data-label">Email</span>
                                                <span class="data-value">{{ auth()->user()->email }}</span>
                                            </div>
                                            <div class="data-col data-col-end">
                                                <span class="data-more disable">
                                                    <em class="icon ni ni-lock-alt"></em>
                                                </span>
                                            </div>
                                        </div>

                                        <a href="{{ route('profile.edit') }}" class="data-item" async-modal data-method="post">
                                            <div class="data-col">
                                                <span class="data-label">Phone Number</span>
                                                <span id="phone" class="data-value overflow-auto">{!! canEmpty(auth()->user()->phone) !!}</span>
                                            </div>
                                            <div class="data-col data-col-end">
                                                <span class="data-more">
                                                    <em class="icon ni ni-forward-ios"></em>
                                                </span>
                                            </div>
                                        </a>

                                        <a href="{{ route('profile.edit') }}" class="data-item" async-modal data-method="post">
                                            <div class="data-col">
                                                <span class="data-label">Address</span>
                                                <span id="address" class="data-value overflow-auto">{!! canEmpty(auth()->user()->address) !!}</span>
                                            </div>
                                            <div class="data-col data-col-end">
                                                <span class="data-more">
                                                    <em class="icon ni ni-forward-ios"></em>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('.nav-link').on('click', function(e) {
            e.preventDefault();

            $('.nav-link').removeClass('active');
            $('.tab-pane').removeClass('active');

            $(this).addClass('active');
            $($(this).attr('href')).addClass('active');
        });

        $(document).on('profile.updated', function(e, params) {
            const $profile = $('#personal-tab');

            for (const key in params) {
                $profile.find(`#${key}`).html(params[key] ?? '<small><i>Unavailable</i></small>');

                if (key == 'name') {
                    $('body').find('.auth-name').text(params[key]);
                }
            };
        });

        $(document).on('avatar.updated', function(e, params) {
            if (typeof params !== 'undefined' && typeof params.url !== 'undefined') {
                $('body').find('.auth-avatar').find('img').attr('src', params.url).show().siblings('span').hide();
            }
        });
    </script>
@endpush
