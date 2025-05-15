@extends('layouts.auth.app')

@section('title', 'Verify Email')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Verify Email</h4>
                    <div class="nk-block-des">
                        <p>
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
                            If you didn't receive the email, we will gladly send you another.
                        </p>
                    </div>
                </div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-info">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div class="form-group">
                    <button type="submit" id="resend-verification-btn" class="btn btn-lg btn-primary btn-block">
                        <span>Resend Verification Email</span>
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-warning btn-block">
                        <span>Logout</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @if (session('status') == 'verification-link-sent')
        <script>
            $(function() {
                let timer = 30;
                const resendButton = $('#resend-verification-btn');
                const forms = $('form');

                forms.find('button').prop('disabled', true);
                resendButton.text(`Request another one in ${timer} seconds`);

                const interval = setInterval(function() {
                    timer--;
                    resendButton.text(`Request another one in ${timer} seconds`);

                    if (timer <= 0) {
                        clearInterval(interval);
                        forms.find('button').prop('disabled', false);
                        resendButton.html('<span>Resend Verification Email</span>');
                    }
                }, 1000);
            });
        </script>
    @endif
@endpush
