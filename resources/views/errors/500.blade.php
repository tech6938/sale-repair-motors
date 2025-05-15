<!DOCTYPE html>
<html lang="en" class="js">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="author" content="Taimoor Ali">
    <meta name="description" content="Cloud-based system for submitting vehicle inspections with media, reviewed through app.">

    <title>500 | {{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css?ver=3.2.3') }}">
</head>

<body>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content">
                    <div class="nk-block nk-block-middle wide-xs mx-auto">
                        <div class="nk-block-content nk-error-ld text-center">
                            <h1 class="nk-error-head">504</h1>
                            <h3 class="nk-error-title">Gateway Timeout Error</h3>
                            <p class="nk-error-text">We are very sorry for inconvenience. It looks like some how our server did not receive a timely response.</p>
                            <a href="{{ url('/') }}" class="btn btn-lg btn-primary mt-2">Back To Home</a>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
</body>

</html>
