<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="author" content="Taimoor Ali">
    <meta name="description" content="Cloud-based system for submitting vehicle inspections with media, reviewed through app.">

    <title>@yield('title') | {{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css?ver=3.2.3') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        let fallbackThumbnail = "{{ asset('assets/images/placeholder.jpg') }}";

        // Bind image on error. ie = Image Error
        function _ie(_self) {
            _self.src = fallbackThumbnail;
        }
    </script>

    @stack('styles')
</head>

<body class="nk-body bg-lighter npc-default has-sidebar {{ $isDarkMode ? 'dark-mode' : '' }}">
    <div class="nk-app-root">
        <div class="nk-main ">
            @include('layouts.sidebar')

            <div class="nk-wrap ">
                @include('layouts.header')

                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                @include('layouts.partials.user-notices')

                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->

                @include('layouts.footer')

            </div>
        </div>
    </div>
    <!-- app-root @e -->

    <!-- Async modal @s -->
    <div class="modal fade zoom" id="async-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <a href="javascript:void(0);" class="close" async-modal-close>
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-body modal-body-lg">
                    <div id="content"></div>
                    <div id="spinner">
                        <div class="modal-body text-center">
                            <div class="spinner-border text-center" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Async modal @e -->

    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js?ver=3.2.3') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=3.2.3') }}"></script>

    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            oLanguage: {
                sSearch: '',
                sSearchPlaceholder: 'Type in to Search',
                sLengthMenu: 'Show _MENU_',
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
