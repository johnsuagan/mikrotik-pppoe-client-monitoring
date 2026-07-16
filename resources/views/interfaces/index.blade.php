@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Interfaces</h1>
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

        <div class="row g-3 mb-3">
            @php
                $running = collect($interfaces)->filter(fn($i) => isset($i['running']) && $i['running'] === 'true')->count();
                $down = count($interfaces) - $running;
            @endphp
            <div class="col-md-3 col-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-network-wired"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total</div>
                        <div class="stat-value">{{ count($interfaces) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Running</div>
                        <div class="stat-value">{{ $running }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-danger">
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Down</div>
                        <div class="stat-value">{{ $down }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-panel">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(6,182,212,0.1); color: var(--accent);">
                    <i class="fas fa-ethernet"></i>
                </div>
                <h3>Network Interfaces</h3>
                <span class="badge badge-info ms-2">Auto-refresh 5s</span>
                <div class="ms-auto search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="interfaceSearch" class="form-control" placeholder="Search interfaces..." style="width: 240px;">
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="interfaceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>RX Rate</th>
                                <th>TX Rate</th>
                                <th>RX Bytes</th>
                                <th>TX Bytes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interfaces as $iface)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><strong style="color: var(--text);">{{ $iface['name'] ?? '' }}</strong></td>
                                <td><span class="badge badge-secondary">{{ $iface['type'] ?? '' }}</span></td>
                                <td>
                                    @if(isset($iface['running']) && $iface['running'] === 'true')
                                        <span class="badge badge-success"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> Running</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-circle me-1" style="font-size: 6px;"></i> Down</span>
                                    @endif
                                </td>
                                <td class="iface-rx" data-name="{{ $iface['name'] ?? '' }}">
                                    @if(isset($iface['rx-rate']) && $iface['rx-rate'] !== '0bps')
                                        <span class="text-success-custom" style="font-weight: 700;">{{ $iface['rx-rate'] }}</span>
                                    @else
                                        <span class="text-muted">0bps</span>
                                    @endif
                                </td>
                                <td class="iface-tx" data-name="{{ $iface['name'] ?? '' }}">
                                    @if(isset($iface['tx-rate']) && $iface['tx-rate'] !== '0bps')
                                        <span style="font-weight: 700; color: var(--primary);">{{ $iface['tx-rate'] }}</span>
                                    @else
                                        <span class="text-muted">0bps</span>
                                    @endif
                                </td>
                                <td>{{ number_format(($iface['rx-byte'] ?? 0) / 1048576, 2) }} MB</td>
                                <td>{{ number_format(($iface['tx-byte'] ?? 0) / 1048576, 2) }} MB</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-plug mb-2" style="font-size: 24px; opacity: 0.3;"></i>
                                    <div>No interfaces found</div>
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
    const searchInput = document.getElementById('interfaceSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#interfaceTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }

    @if($selectedRouter)
    let prevIfaceRx = {};
    let prevIfaceTx = {};
    setInterval(function() {
        fetch('{{ route("api.traffic", $selectedRouter) }}')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                data.data.forEach(iface => {
                    const rxCell = document.querySelector('.iface-rx[data-name="' + iface.name + '"]');
                    const txCell = document.querySelector('.iface-tx[data-name="' + iface.name + '"]');
                    const rxBytes = iface.rx_byte || 0;
                    const txBytes = iface.tx_byte || 0;
                    let rxRate = 0;
                    let txRate = 0;
                    if (prevIfaceRx[iface.name] !== undefined) {
                        rxRate = Math.max(0, rxBytes - prevIfaceRx[iface.name]) * 8 / 5;
                        txRate = Math.max(0, txBytes - prevIfaceTx[iface.name]) * 8 / 5;
                    }
                    prevIfaceRx[iface.name] = rxBytes;
                    prevIfaceTx[iface.name] = txBytes;
                    if (rxCell) {
                        rxCell.innerHTML = rxRate > 0
                            ? '<span class="text-success-custom" style="font-weight:700;">' + window.formatRate(rxRate) + '</span>'
                            : '<span class="text-muted">0bps</span>';
                    }
                    if (txCell) {
                        txCell.innerHTML = txRate > 0
                            ? '<span style="font-weight:700;color:var(--primary);">' + window.formatRate(txRate) + '</span>'
                            : '<span class="text-muted">0bps</span>';
                    }
                });
            })
            .catch(() => {});
    }, 5000);
    @endif
});
</script>
@endpush
