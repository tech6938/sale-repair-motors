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

    @stack('styles')
</head>

<body class="nk-body ui-rounder npc-default pg-auth">
    <!-- app-root @s -->
    <div class="nk-app-root">
        <div class="nk-main">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        @include('layouts.auth.header')

                        @yield('content')
                    </div>

                    @include('layouts.auth.footer')
                </div>
                <!-- content @e -->
            </div>
            <!-- wrap @e -->
        </div>
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js?ver=3.2.3') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=3.2.3') }}"></script>

    <script>
        $(document).on('submit', 'form', function(e) {
            $(this)
                .find('button')
                .prop('disabled', true)
                .prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            const forms = $(this).siblings('form');

            if (forms.length > 0) {
                forms
                    .find('button')
                    .prop('disabled', true);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
