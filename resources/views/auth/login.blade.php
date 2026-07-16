@extends('layouts.auth')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <span style="color: var(--primary); font-weight: 800; font-size: 24px; letter-spacing: -0.02em;">
            <i class="fas fa-satellite-dish me-2"></i>MikroTik Monitor
        </span>
    </div>
    <div class="card elevation-1" style="border-radius: var(--radius); border: 1px solid var(--border);">
        <div class="card-body login-card-body" style="padding: 30px;">
            <p class="login-box-msg" style="font-size: 14px; color: var(--text-muted);">Sign in to start monitoring</p>

            @if($errors->any())
            <div class="alert alert-danger" style="font-size: 13px; border-radius: var(--radius-sm);">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-envelope" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-lock" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-7">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember" style="font-size: 13px; color: var(--text-muted);">Remember me</label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary w-100" style="border-radius: var(--radius-sm); font-weight: 600;">
                            <i class="fas fa-sign-in-alt me-1"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

            <p class="mb-0" style="font-size: 13px; text-align: center; color: var(--text-muted);">
                Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
