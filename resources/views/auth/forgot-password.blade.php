@extends('layouts.auth.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Forgot Password</h4>
                    <div class="nk-block-des">
                        <p>Forgot your password? No problem. Just let us know your email address and we will email you the instructions to reset.</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <form method="post" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <div class="form-control-wrap">
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="off"
                            placeholder="Enter your email address">
                        @if ($errors->has('email'))
                            <small class="text-danger">{{ $errors->first('email') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">
                        <span>Email Password Reset Link</span>
                    </button>
                </div>
            </form>

            @if (Route::has('login'))
                <div class="form-note-s2 text-center pt-4">
                    Already registered? <a href="{{ route('login') }}">Login</a>
                </div>
            @endif
        </div>
    </div>
@endsection
