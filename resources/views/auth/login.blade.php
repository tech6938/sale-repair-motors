@extends('layouts.auth.app')

@section('title', 'Login')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Sign-In</h4>
                    <div class="nk-block-des">
                        <p>Access the application panel using your email and password.</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <div class="form-control-wrap">
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="Enter your email address">
                        @if ($errors->has('email'))
                            <small class="text-danger">{{ $errors->first('email') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a class="link link-primary link-sm" href="{{ route('password.request') }}" tabindex="-1">Forgot Password?</a>
                        @endif
                    </div>
                    <div class="form-control-wrap">
                        <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="password" tabindex="-1">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required placeholder="Enter your password">
                        @if ($errors->has('password'))
                            <small class="text-danger">{{ $errors->first('password') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-control-xs custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember" id="remember">
                        <label class="custom-control-label" for="remember">Remember me</label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">
                        <span>Sign in</span>
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="form-note-s2 text-center pt-4">
                    New on our platform? <a href="{{ route('register') }}">Create an account</a>
                </div>
            @endif
        </div>
    </div>
@endsection
