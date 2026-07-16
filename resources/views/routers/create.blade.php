@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Add Router</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3>New Router</h3>
                    </div>
                    <div class="panel-body p-4">
                        <form action="{{ route('routers.store') }}" method="POST">
                            @csrf

                            @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                @foreach($errors->all() as $error)
                                    <div><i class="fas fa-exclamation-triangle me-1"></i> {{ $error }}</div>
                                @endforeach
                            </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Router Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. Main Router">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-9">
                                    <label for="host" class="form-label">Host / IP Address</label>
                                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host', '192.168.1.1') }}" required placeholder="192.168.1.1">
                                </div>
                                <div class="col-md-3">
                                    <label for="port" class="form-label">API Port</label>
                                    <input type="number" name="port" id="port" class="form-control" value="{{ old('port', 8728) }}" required min="1" max="65535">
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required placeholder="admin">
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" value="{{ old('password') }}" required>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Save Router
                                </button>
                                <a href="{{ route('routers.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
