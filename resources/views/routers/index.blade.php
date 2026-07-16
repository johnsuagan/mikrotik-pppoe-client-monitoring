@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Router Settings</h1>
            <a href="{{ route('routers.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add Router
            </a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="info-panel">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(99,102,241,0.1); color: var(--primary);">
                    <i class="fas fa-server"></i>
                </div>
                <h3>Configured Routers</h3>
                <span class="badge badge-primary ms-2">{{ $routers->count() }}</span>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Host</th>
                                <th>Port</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routers as $router)
                            <tr>
                                <td class="text-muted">{{ $router->id }}</td>
                                <td><strong style="color: var(--text);">{{ $router->name }}</strong></td>
                                <td><code>{{ $router->host }}</code></td>
                                <td>{{ $router->port }}</td>
                                <td>{{ $router->username }}</td>
                                <td>
                                    @if($router->active)
                                        <span class="badge badge-success"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-info btn-sm test-btn" data-url="{{ route('routers.test', $router) }}">
                                        <i class="fas fa-plug me-1"></i> Test
                                    </button>
                                    <a href="{{ route('routers.edit', $router) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('routers.destroy', $router) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this router?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-server mb-2" style="font-size: 24px; opacity: 0.3;"></i>
                                    <div>No routers configured yet.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.test-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            const btnEl = this;
            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert('Connection OK: ' + data.identity);
                    } else {
                        alert('Connection Failed: ' + data.error);
                    }
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-plug me-1"></i> Test';
                })
                .catch(() => {
                    alert('Connection Failed');
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-plug me-1"></i> Test';
                });
        });
    });
});
</script>
@endpush
