@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Queue Monitoring</h1>
            <a href="{{ route('queues.traffic', ['router' => $selectedRouter?->id]) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i> Individual Traffic Graphs
            </a>
        </div>
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
                $totalQ = count($queues);
                $activeQ = collect($queues)->filter(fn($q) => isset($q['rx-rate']) && $q['rx-rate'] !== '0bps')->count();
            @endphp
            <div class="col-md-3 col-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Queues</div>
                        <div class="stat-value">{{ $totalQ }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Active</div>
                        <div class="stat-value">{{ $activeQ }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Download</div>
                        <div class="stat-value" id="queueTotalRx">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Upload</div>
                        <div class="stat-value" id="queueTotalTx">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(80,70,229,0.15); color: var(--primary);">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <h3>Queue Traffic</h3>
                        <span class="badge badge-primary ms-2">Live</span>
                        <a href="{{ route('queues.traffic', ['router' => $selectedRouter?->id]) }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-chart-line me-1"></i> Individual Graphs
                        </a>
                        <div class="ms-auto d-flex align-items-center gap-3" style="font-size: 12px;">
                            <span><span style="color: rgba(80,70,229,0.85);">&#9632;</span> Download</span>
                            <span><span style="color: rgba(8,145,178,0.85);">&#9632;</span> Upload</span>
                        </div>
                    </div>
                    <div class="panel-body p-3">
                        <div style="height: 280px; position: relative;">
                            <canvas id="queueTrafficChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-panel">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(245,158,11,0.15); color: #d97706;">
                    <i class="fas fa-list"></i>
                </div>
                <h3>Simple Queues</h3>
                <span class="badge badge-warning ms-2">{{ count($queues) }} queues</span>
                <div class="ms-auto search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="queueSearch" class="form-control" placeholder="Search queues..." style="width: 240px;">
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="queueTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Target</th>
                                <th>Max Limit</th>
                                <th>RX Rate</th>
                                <th>TX Rate</th>
                                <th>RX Bytes</th>
                                <th>TX Bytes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($queues as $queue)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><strong style="color: var(--text);">{{ $queue['name'] ?? '' }}</strong></td>
                                <td><code>{{ $queue['target'] ?? '' }}</code></td>
                                <td><span class="badge badge-primary">{{ $queue['max-limit'] ?? 'unlimited' }}</span></td>
                                <td class="q-rx" data-name="{{ $queue['name'] ?? '' }}">
                                    @if(isset($queue['rx-rate']) && $queue['rx-rate'] !== '0bps')
                                        <span class="text-success-custom" style="font-weight: 700;">{{ $queue['rx-rate'] }}</span>
                                    @else
                                        <span class="text-muted">0bps</span>
                                    @endif
                                </td>
                                <td class="q-tx" data-name="{{ $queue['name'] ?? '' }}">
                                    @if(isset($queue['tx-rate']) && $queue['tx-rate'] !== '0bps')
                                        <span style="font-weight: 700; color: var(--primary);">{{ $queue['tx-rate'] }}</span>
                                    @else
                                        <span class="text-muted">0bps</span>
                                    @endif
                                </td>
                                <td>{{ $queue['rx-byte'] ?? '0' }}</td>
                                <td>{{ $queue['tx-byte'] ?? '0' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox mb-2" style="font-size: 24px; opacity: 0.3;"></i>
                                    <div>No queues configured</div>
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
@if($selectedRouter)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const MAX_POINTS = 60;
    const url = '{{ route("api.queues", $selectedRouter) }}';
    let labels = [];
    let rxData = [];
    let txData = [];
    let chart = null;

    function initChart() {
        const c = window.getChartColors();
        const ctx = document.getElementById('queueTrafficChart');
        if (!ctx) return;

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Download',
                        data: rxData,
                        borderColor: c.download,
                        backgroundColor: c.downloadBg,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHitRadius: 10,
                    },
                    {
                        label: 'Upload',
                        data: txData,
                        borderColor: c.upload,
                        backgroundColor: c.uploadBg,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHitRadius: 10,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 300 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: c.bg,
                        titleColor: c.text,
                        bodyColor: c.text,
                        borderColor: c.grid,
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': ' + window.formatRate(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: { display: true, grid: { color: c.grid }, ticks: { color: c.text, font: { size: 10 }, maxTicksLimit: 10 } },
                    y: { display: true, grid: { color: c.grid }, ticks: { color: c.text, font: { size: 10 }, callback: function(v) { return window.formatRate(v); } }, beginAtZero: true }
                }
            }
        });
    }

    function fetchData() {
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                let totalRx = 0;
                let totalTx = 0;

                data.data.forEach(queue => {
                    const rx = window.parseRate(queue.rx_rate);
                    const tx = window.parseRate(queue.tx_rate);
                    totalRx += rx;
                    totalTx += tx;

                    const rxCell = document.querySelector('.q-rx[data-name="' + queue.name + '"]');
                    const txCell = document.querySelector('.q-tx[data-name="' + queue.name + '"]');
                    if (rxCell) {
                        rxCell.innerHTML = rx > 0
                            ? '<span class="text-success-custom" style="font-weight:700;">' + window.formatRate(rx) + '</span>'
                            : '<span class="text-muted">0bps</span>';
                    }
                    if (txCell) {
                        txCell.innerHTML = tx > 0
                            ? '<span style="font-weight:700;color:var(--primary);">' + window.formatRate(tx) + '</span>'
                            : '<span class="text-muted">0bps</span>';
                    }
                });

                const now = new Date();
                const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0') + ':' + now.getSeconds().toString().padStart(2,'0');

                labels.push(time);
                rxData.push(totalRx);
                txData.push(totalTx);

                if (labels.length > MAX_POINTS) {
                    labels.shift();
                    rxData.shift();
                    txData.shift();
                }

                document.getElementById('queueTotalRx').textContent = window.formatRate(totalRx);
                document.getElementById('queueTotalTx').textContent = window.formatRate(totalTx);

                if (chart) {
                    chart.data.labels = labels;
                    chart.data.datasets[0].data = rxData;
                    chart.data.datasets[1].data = txData;
                    chart.update('none');
                }
            })
            .catch(() => {});
    }

    const searchInput = document.getElementById('queueSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#queueTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    }

    initChart();
    fetchData();
    setInterval(fetchData, 3000);

    window.__updateChartsTheme = function() {
        if (chart) window.updateChartTheme(chart);
    };
});
</script>
@endif
@endpush
