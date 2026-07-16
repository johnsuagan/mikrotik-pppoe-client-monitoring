@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Client Usage</h1>
        @if($selectedRouter)
        <p class="text-muted mb-0" style="font-size: 13px;">
            <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
            {{ $selectedRouter->name }} — All clients with bandwidth usage
        </p>
        @endif
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        @if(!$selectedRouter)
        <div class="alert alert-warning">
            <i class="fas fa-info-circle me-2"></i> No routers configured.
        </div>
        @else

        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Clients</div>
                        <div class="stat-value">{{ $clients->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-download"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Download</div>
                        <div class="stat-value" id="usageTotalRx">{{ format_bytes($clients->sum('total_rx')) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-upload"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Upload</div>
                        <div class="stat-value" id="usageTotalTx">{{ format_bytes($clients->sum('total_tx')) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-bolt"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Live Download</div>
                        <div class="stat-value" id="usageLiveRx" style="font-size: 18px;">0 bps</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-panel mb-3">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(80,70,229,0.15); color: var(--primary);">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Clients</h3>
                <span class="badge badge-primary ms-2">{{ $clients->count() }}</span>
                <div class="ms-auto search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="clientSearch" class="form-control" placeholder="Search clients..." style="width: 240px;">
                </div>
            </div>
            <div class="panel-body">
                <div class="row g-3" id="clientGrid">
                @forelse($clients as $client)
                <div class="col-md-4 col-lg-3 client-card" data-search="{{ strtolower($client->queue_name . ' ' . ($client->target ?? '')) }}" data-queue="{{ $client->queue_name }}">
                    <div class="info-panel" style="height: 100%;">
                        <div class="panel-body p-3" style="display: flex; flex-direction: column; gap: 10px;">
                            <div>
                                <div style="font-weight: 700; font-size: 15px; color: var(--text); margin-bottom: 2px;">{{ $client->queue_name }}</div>
                                @if($client->target)
                                <code style="font-size: 11px;">{{ $client->target }}</code>
                                @endif
                            </div>
                            <div style="display: flex; gap: 16px; font-size: 12px;">
                                <div>
                                    <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">Download</div>
                                    <div style="font-weight: 700; color: var(--primary);">{{ format_bytes($client->total_rx) }}</div>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">Upload</div>
                                    <div style="font-weight: 700; color: var(--accent);">{{ format_bytes($client->total_tx) }}</div>
                                </div>
                            </div>
                            <div class="card-rate" style="display: flex; gap: 12px; font-size: 11px;">
                                <span class="text-muted"><i class="fas fa-arrow-down me-1" style="color: var(--primary); font-size: 9px;"></i><span class="card-rx-rate">0 bps</span></span>
                                <span class="text-muted"><i class="fas fa-arrow-up me-1" style="color: var(--accent); font-size: 9px;"></i><span class="card-tx-rate">0 bps</span></span>
                            </div>
                            <div class="mt-auto" style="display: flex; gap: 6px;">
                                <a href="{{ route('usage.detail', ['router' => $selectedRouter->id, 'clientName' => $client->queue_name, 'period' => 'daily']) }}"
                                   class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="fas fa-chart-line me-1"></i> View
                                </a>
                                <a href="{{ route('usage.export', ['router' => $selectedRouter->id, 'clientName' => $client->queue_name, 'period' => 'daily']) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Download CSV">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="panel-body p-5 text-center text-muted">
                        <i class="fas fa-inbox mb-3" style="font-size: 40px; opacity: 0.2;"></i>
                        <div style="font-size: 15px; font-weight: 500;">No usage data yet</div>
                        <p style="font-size: 13px; margin-top: 6px;">Usage data is logged every 5 minutes. Check back soon.</p>
                    </div>
                </div>
                @endforelse
                </div>
            </div>
        </div>

        @endif
    </div>
</section>
@endsection

@push('scripts')
@if($selectedRouter)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('clientSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            document.querySelectorAll('.client-card').forEach(function(card) {
                var text = card.getAttribute('data-search') || '';
                card.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    var queueUrl = '{{ route("api.queues", $selectedRouter) }}';

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB', 'TB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        i = Math.min(i, units.length - 1);
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
    }

    var prevBytes = {};

    function fetchLiveData() {
        fetch(queueUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) return;

                var totalRx = 0;
                var totalTx = 0;
                var liveRx = 0;
                var liveTx = 0;

                data.data.forEach(function(queue) {
                    var rxByte = queue.rx_byte || 0;
                    var txByte = queue.tx_byte || 0;
                    totalRx += rxByte;
                    totalTx += txByte;

                    var rxRate = queue.rx_rate || 0;
                    var txRate = queue.tx_rate || 0;
                    liveRx += rxRate;
                    liveTx += txRate;

                    var card = document.querySelector('.client-card[data-queue="' + queue.name + '"]');
                    if (card) {
                        var rxRateEl = card.querySelector('.card-rx-rate');
                        var txRateEl = card.querySelector('.card-tx-rate');
                        if (rxRateEl) rxRateEl.textContent = window.formatRate(rxRate * 1000);
                        if (txRateEl) txRateEl.textContent = window.formatRate(txRate * 1000);
                    }
                });

                var totalRxEl = document.getElementById('usageTotalRx');
                var totalTxEl = document.getElementById('usageTotalTx');
                var liveRxEl = document.getElementById('usageLiveRx');
                var liveTxEl = document.getElementById('usageLiveTx');
                if (totalRxEl) totalRxEl.textContent = formatBytes(totalRx);
                if (totalTxEl) totalTxEl.textContent = formatBytes(totalTx);
                if (liveRxEl) liveRxEl.textContent = window.formatRate(liveRx * 1000);
                if (liveTxEl) liveTxEl.textContent = window.formatRate(liveTx * 1000);
            })
            .catch(function() {});
    }

    fetchLiveData();
    setInterval(fetchLiveData, 3000);
});
</script>
@endif
@endpush
