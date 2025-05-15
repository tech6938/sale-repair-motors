@extends('layouts.auth.app')

@section('title', 'Register')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">Register</h4>
                    <div class="nk-block-des">
                        <p>Create a new account to access the application panel.</p>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="name">Name</label>
                    </div>
                    <div class="form-control-wrap">
                        <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="off"
                            placeholder="Enter your full name">
                        @if ($errors->has('name'))
                            <small class="text-danger">{{ $errors->first('name') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email">Email</label>
                    </div>
                    <div class="form-control-wrap">
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autocomplete="off"
                            placeholder="Enter your email address">
                        @if ($errors->has('email'))
                            <small class="text-danger">{{ $errors->first('email') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Join As</label>
                    <div class="form-control-wrap ">
                        <div class="form-control-select">
                            <select class="form-control" id="role" name="role" required>
                                @foreach (App\Models\User::getRoles() as $role)
                                    <option value="{{ $role }}" @selected(old('role') == $role)>
                                        {{ humanize($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @if ($errors->has('role'))
                            <small class="text-danger">{{ $errors->first('role') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="form-control-wrap">
                        <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="password" tabindex="-1">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required autocomplete="off" placeholder="Enter your password">
                        @if ($errors->has('password'))
                            <small class="text-danger">{{ $errors->first('password') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                    </div>
                    <div class="form-control-wrap">
                        <a href="javascript:void(0);" class="form-icon form-icon-right passcode-switch lg" data-target="password_confirmation" tabindex="-1">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>
                        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required autocomplete="off"
                            placeholder="Confirm your password">
                        @if ($errors->has('password_confirmation'))
                            <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">
                        <span>Register</span>
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
