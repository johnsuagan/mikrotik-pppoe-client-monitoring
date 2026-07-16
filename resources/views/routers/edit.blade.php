@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Edit Router: {{ $router->name }}</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h3>Edit Router</h3>
                    </div>
                    <div class="panel-body p-4">
                        <form action="{{ route('routers.update', $router) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                @foreach($errors->all() as $error)
                                    <div><i class="fas fa-exclamation-triangle me-1"></i> {{ $error }}</div>
                                @endforeach
                            </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Router Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $router->name) }}" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-9">
                                    <label for="host" class="form-label">Host / IP Address</label>
                                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host', $router->host) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="port" class="form-label">API Port</label>
                                    <input type="number" name="port" id="port" class="form-control" value="{{ old('port', $router->port) }}" required min="1" max="65535">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $router->username) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" value="{{ old('password', $router->password) }}" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" class="form-check-input" id="active" value="1" {{ old('active', $router->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active" style="font-weight: 600; font-size: 13px;">Active</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Router
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
