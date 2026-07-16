@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Queue Traffic</h1>
                @if($selectedRouter)
                <p class="text-muted mb-0" style="font-size: 13px;">
                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                    {{ $selectedRouter->name }} - Individual bandwidth graphs
                </p>
                @endif
            </div>
            <a href="{{ route('queues', ['router' => $selectedRouter?->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Queues
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

        @forelse($queues as $queue)
        <div class="info-panel mb-3 queue-chart-panel" data-queue-name="{{ $queue['name'] ?? '' }}">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(80,70,229,0.15); color: var(--primary);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>{{ $queue['name'] ?? 'Unknown' }}</h3>
                <code style="font-size: 11px; margin-left: 4px;">{{ $queue['target'] ?? '' }}</code>
                <span class="badge badge-primary ms-2">{{ $queue['max-limit'] ?? 'unlimited' }}</span>
                <div class="ms-auto d-flex align-items-center gap-4" style="font-size: 12px;">
                    <span class="q-live-rx" style="font-weight: 600;">
                        <span style="color: var(--primary);">&#9650;</span>
                        <span class="rx-val">0 bps</span>
                    </span>
                    <span class="q-live-tx" style="font-weight: 600;">
                        <span style="color: var(--accent);">&#9660;</span>
                        <span class="tx-val">0 bps</span>
                    </span>
                </div>
            </div>
            <div class="panel-body p-3">
                <div style="height: 200px; position: relative;">
                    <canvas class="queue-chart-canvas" data-queue="{{ $queue['name'] ?? '' }}"></canvas>
                </div>
            </div>
        </div>
        @empty
        <div class="info-panel">
            <div class="panel-body p-5 text-center text-muted">
                <i class="fas fa-inbox mb-3" style="font-size: 40px; opacity: 0.2;"></i>
                <div style="font-size: 15px; font-weight: 500;">No queues configured</div>
                <p style="font-size: 13px; margin-top: 6px;">Create queues on your MikroTik router to see individual traffic graphs.</p>
            </div>
        </div>
        @endforelse

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
    const queueNames = @json(collect($queues)->pluck('name')->values()->all());

    const charts = {};
    const rxHistory = {};
    const txHistory = {};
    const labelsHistory = {};

    queueNames.forEach(name => {
        rxHistory[name] = [];
        txHistory[name] = [];
        labelsHistory[name] = [];
    });

    function initAllCharts() {
        const c = window.getChartColors();

        queueNames.forEach(name => {
            const canvas = document.querySelector('.queue-chart-canvas[data-queue="' + name + '"]');
            if (!canvas) return;

            charts[name] = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Download',
                            data: [],
                            borderColor: c.download,
                            backgroundColor: c.downloadBg,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHitRadius: 8,
                        },
                        {
                            label: 'Upload',
                            data: [],
                            borderColor: c.upload,
                            backgroundColor: c.uploadBg,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHitRadius: 8,
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
                            padding: 8,
                            titleFont: { size: 11 },
                            bodyFont: { size: 11 },
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.dataset.label + ': ' + window.formatRate(ctx.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { display: true, grid: { color: c.grid }, ticks: { color: c.text, font: { size: 9 }, maxTicksLimit: 8 } },
                        y: { display: true, grid: { color: c.grid }, ticks: { color: c.text, font: { size: 9 }, callback: function(v) { return window.formatRate(v); } }, beginAtZero: true }
                    }
                }
            });
        });
    }

    function fetchData() {
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const now = new Date();
                const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0') + ':' + now.getSeconds().toString().padStart(2,'0');

                data.data.forEach(queue => {
                    const name = queue.name;
                    if (!charts[name]) return;

                    const rx = window.parseRate(queue.rx_rate);
                    const tx = window.parseRate(queue.tx_rate);

                    const panel = document.querySelector('.queue-chart-panel[data-queue-name="' + name + '"]');
                    if (panel) {
                        const rxSpan = panel.querySelector('.rx-val');
                        const txSpan = panel.querySelector('.tx-val');
                        if (rxSpan) rxSpan.textContent = rx > 0 ? window.formatRate(rx) : '0 bps';
                        if (txSpan) txSpan.textContent = tx > 0 ? window.formatRate(tx) : '0 bps';
                    }

                    labelsHistory[name].push(time);
                    rxHistory[name].push(rx);
                    txHistory[name].push(tx);

                    if (labelsHistory[name].length > MAX_POINTS) {
                        labelsHistory[name].shift();
                        rxHistory[name].shift();
                        txHistory[name].shift();
                    }

                    const chart = charts[name];
                    chart.data.labels = labelsHistory[name];
                    chart.data.datasets[0].data = rxHistory[name];
                    chart.data.datasets[1].data = txHistory[name];
                    chart.update('none');
                });
            })
            .catch(() => {});
    }

    initAllCharts();
    fetchData();
    setInterval(fetchData, 3000);

    window.__updateChartsTheme = function() {
        Object.values(charts).forEach(c => window.updateChartTheme(c));
    };
});
</script>
@endif
@endpush
