@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">PPPoE Users</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        @if($error)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ $error }}
        </div>
        @endif

        @if(!$selectedRouter)
        <div class="alert alert-warning">
            <i class="fas fa-info-circle me-2"></i> No routers configured.
        </div>
        @else

        <div class="info-panel">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Active Users</h3>
                <span class="badge badge-success ms-2">{{ count($users) }} online</span>
                <div class="ms-auto search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="pppoeSearch" class="form-control" placeholder="Search users..." style="width: 240px;">
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="pppoeTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>IP Address</th>
                                <th>Uptime</th>
                                <th>Service</th>
                                <th>Caller ID</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><strong style="color: var(--text);">{{ $user['name'] ?? '' }}</strong></td>
                                <td><code>{{ $user['address'] ?? '' }}</code></td>
                                <td><span class="badge badge-success">{{ $user['uptime'] ?? '' }}</span></td>
                                <td><span class="badge badge-info">{{ $user['service'] ?? '' }}</span></td>
                                <td><code>{{ $user['caller-id'] ?? '' }}</code></td>
                                <td class="text-end">
                                    @if(isset($user['.id']))
                                    <button class="btn btn-danger btn-sm disconnect-btn"
                                            data-id="{{ $user['.id'] }}"
                                            data-name="{{ $user['name'] ?? '' }}"
                                            data-url="{{ route('api.disconnect', $selectedRouter) }}">
                                        <i class="fas fa-times-circle me-1"></i> Disconnect
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-user-slash mb-2" style="font-size: 24px; opacity: 0.3;"></i>
                                    <div>No active PPPoE users</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('pppoeSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#pppoeTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('.disconnect-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Disconnect user "' + this.dataset.name + '"?')) return;
            const url = this.dataset.url;
            const id = this.dataset.id;
            const row = this.closest('tr');
            const btnEl = this;

            btnEl.disabled = true;
            btnEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    row.style.transition = 'all 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                    btnEl.disabled = false;
                    btnEl.innerHTML = '<i class="fas fa-times-circle me-1"></i> Disconnect';
                }
            })
            .catch(() => {
                btnEl.disabled = false;
                btnEl.innerHTML = '<i class="fas fa-times-circle me-1"></i> Disconnect';
            });
        });
    });
});
</script>
@endpush
