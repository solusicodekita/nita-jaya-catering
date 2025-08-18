@extends('layouts.app')
@section('content')
<style>
    body {
        background: #f5f7fa;
    }
    .login-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 32px rgba(26,35,126,0.08);
        padding: 2.5rem 2rem 2rem 2rem;
        background: #fff;
    }
    .login-logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
    }
    .login-logo img {
        height: 70px;
        width: auto;
    }
    .login-title {
        text-align: center;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 0.5rem;
        font-size: 1.5rem;
        letter-spacing: 1px;
    }
    .login-subtitle {
        text-align: center;
        color: #757575;
        margin-bottom: 2rem;
        font-size: 1rem;
    }
    .btn-primary {
        background: #1a237e;
        border: none;
        font-weight: 600;
        letter-spacing: 1px;
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: #ffc107;
        color: #1a237e;
    }
    .form-check-label {
        color: #1a237e;
    }
    .card-header {
        background: none;
        border-bottom: none;
        text-align: center;
        font-size: 1.25rem;
        color: #1a237e;
        font-weight: 600;
    }
</style>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">
            <div class="login-card">
                <div class="login-logo">
                    <img src="{{ asset('images/nitajaya.png') }}" alt="Nita Jaya Catering Logo">
                </div>
                <div class="login-title">Nita Jaya Catering</div>
                <div class="login-subtitle">Silakan login untuk melanjutkan</div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('Username Or Email') }}</label>
                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                            name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
