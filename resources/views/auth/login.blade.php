@extends('layouts.app')

@section('content')
    <div class="container-fluid login-container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card login-card">
                    <div class="login-header">
                        <div style="display: inline-block; margin-top:30px">
                            {{ __('Login Here') }}
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-0 row-email">
                                <div>
                                    <label for="email"
                                        class="col-md-4 col-form-label text-md-start email-label">{{-- __('EmailAddress') --}}
                                        Username</label>
                                </div>
                                <div>
                                    <div class="col-md-12">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror email-input"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus placeholder="Email">

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0 row-password">
                                <div>
                                    <label for="password"
                                        class="col-md-4 col-form-label text-md-start password-label">{{ __('Password') }}</label>
                                </div>
                                <div>
                                    <div class="col-md-12">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror password-input"
                                            name="password" required autocomplete="current-password" placeholder="Password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-2 row-submit">
                                <div class="col-md-5 row-remember-me">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}  style="cursor: pointer">

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-warning submit-button">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </div>
                            <div class="row row-forgot-password">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
