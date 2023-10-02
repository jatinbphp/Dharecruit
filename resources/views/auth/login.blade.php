@extends('layouts.app')

@section('content')
<div class="section-padding login-main">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header section-tittle">
                        <h2>{{ __('Login') }}</h2>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">

                                <div class="col-xl-12">
                                    <div class="form-group mb-3">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('Email Address') }}">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="form-group mb-3">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-group d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label m-0" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 offset-md-4">
                                    <div class="form-group" style="text-align: right;">
                                        @if (Route::has('password.request'))
                                            <p>
                                                <a href="{{ route('password.request') }}">
                                                    {{ __('Forgot Your Password?') }}
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                        
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-10 offset-md-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login') }}
                                        </button>

                                        <span>OR</span>

                                        <a class="btn btn-link" href="{{ url('auth/facebook') }}" style="background: blue;" id="btn-fblogin">
                                            <i class="fab fa-facebook-square" aria-hidden="true"></i> Login with Facebook
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            @if (Route::has('register'))
                                <div class="row mb-3">
                                    <div class="col-xl-12">
                                        <div class="form-group mb-3">
                                            <p>If you no have an account with us, please register at the <a href="{{ route('register') }}">{{ __('Register') }} here</a>.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
