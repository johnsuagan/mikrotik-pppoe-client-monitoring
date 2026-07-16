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
            <p class="login-box-msg" style="font-size: 14px; color: var(--text-muted);">Create your account</p>

            @if($errors->any())
            <div class="alert alert-danger" style="font-size: 13px; border-radius: var(--radius-sm);">
                <i class="fas fa-exclamation-circle me-1"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}@if(!$loop->last)<br>@endif
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Full name" value="{{ old('name') }}" required autofocus style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-user" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-envelope" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" required style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-lock" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required style="border-radius: var(--radius-sm);">
                    <div class="input-group-text" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0; background: var(--surface-alt); border-color: var(--border);">
                        <span class="fas fa-lock" style="color: var(--text-muted);"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3" style="border-radius: var(--radius-sm); font-weight: 600;">
                    <i class="fas fa-user-plus me-1"></i> Create Account
                </button>
            </form>

            <p class="mb-0" style="font-size: 13px; text-align: center; color: var(--text-muted);">
                Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
