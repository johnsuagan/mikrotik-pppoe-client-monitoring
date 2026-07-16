@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">Dashboard</h1>
                @if($selectedRouter)
                <p class="text-muted mb-0" style="font-size: 13px;">
                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                    Connected to <strong>{{ $selectedRouter->name }}</strong> ({{ $selectedRouter->host }})
                </p>
                @endif
            </div>
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
            <a href="{{ route('routers.create') }}" style="color: var(--primary); font-weight: 600;">Add a router</a> to get started.
        </div>
        @else

        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Router Name</div>
                        <div class="stat-value">{{ $identity['name'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">CPU Usage</div>
                        <div class="stat-value">{{ $resource['cpu-load'] ?? '0' }}<small>%</small></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Online Users</div>
                        <div class="stat-value">{{ $onlineUsers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="stat-info">
                        <div class="stat-label">Interfaces</div>
                        <div class="stat-value">{{ $interfaces }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(99,102,241,0.1); color: var(--primary);">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>Router Information</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted); width: 40%;">Router Name</td>
                                    <td style="font-weight: 600;">{{ $identity['name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);">RouterOS</td>
                                    <td><span class="badge badge-primary">{{ $resource['version'] ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);">Board</td>
                                    <td>{{ $resource['board-name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-muted);">Uptime</td>
                                    <td><span class="badge badge-success">{{ $resource['uptime'] ?? 'N/A' }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(6,182,212,0.1); color: var(--accent);">
                            <i class="fas fa-memory"></i>
                        </div>
                        <h3>System Resources</h3>
                    </div>
                    <div class="panel-body p-4">
                        @php $cpu = $resource['cpu-load'] ?? 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">CPU Load</span>
                                <span style="font-size: 13px; font-weight: 700;">{{ $cpu }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $cpu > 80 ? 'bg-danger' : ($cpu > 50 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $cpu }}%; border-radius: 100px;"></div>
                            </div>
                        </div>
                        @php
                            $freeMem = isset($resource['free-memory']) ? round($resource['free-memory']/1048576, 1) : 0;
                            $totalMem = isset($resource['total-memory']) ? round($resource['total-memory']/1048576, 1) : 1;
                            $memPct = $totalMem > 0 ? round(($totalMem - $freeMem) / $totalMem * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Memory</span>
                                <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">{{ number_format($freeMem, 1) }} MB free / {{ number_format($totalMem, 1) }} MB total</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $memPct > 80 ? 'bg-danger' : ($memPct > 50 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ $memPct }}%; border-radius: 100px;"></div>
                            </div>
                        </div>
                        @php
                            $freeDisk = isset($resource['free-hdd-space']) ? round($resource['free-hdd-space']/1048576, 1) : 0;
                            $totalDisk = isset($resource['total-hdd-space']) ? round($resource['total-hdd-space']/1048576, 1) : 1;
                            $diskPct = $totalDisk > 0 ? round(($totalDisk - $freeDisk) / $totalDisk * 100, 1) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Disk</span>
                                <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">{{ number_format($freeDisk, 1) }} MB free / {{ number_format($totalDisk, 1) }} MB total</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $diskPct > 80 ? 'bg-danger' : ($diskPct > 50 ? 'bg-warning' : 'bg-info') }}" style="width: {{ $diskPct }}%; border-radius: 100px;"></div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-4">
                                <div class="text-center p-3" style="background: var(--surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                    <div style="font-size: 18px; font-weight: 700; color: var(--text);">{{ $cpu }}%</div>
                                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">CPU</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3" style="background: var(--surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                    <div style="font-size: 18px; font-weight: 700; color: var(--text);">{{ $memPct }}%</div>
                                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">RAM</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-3" style="background: var(--surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                    <div style="font-size: 18px; font-weight: 700; color: var(--text);">{{ $diskPct }}%</div>
                                    <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted);">DISK</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="info-panel">
                    <div class="panel-header">
                        <div class="panel-icon" style="background: rgba(80,70,229,0.15); color: var(--primary);">
                            <i class="fas fa-chart-area"></i>
                        </div>
                        <h3>Total Traffic</h3>
                        <span class="badge badge-primary ms-2">Live</span>
                        <div class="ms-auto d-flex align-items-center gap-3" style="font-size: 12px;">
                            <span><span style="color: rgba(80,70,229,0.85);">&#9632;</span> Download: <strong id="dashTotalRx">0</strong></span>
                            <span><span style="color: rgba(8,145,178,0.85);">&#9632;</span> Upload: <strong id="dashTotalTx">0</strong></span>
                        </div>
                    </div>
                    <div class="panel-body p-3">
                        <div style="height: 280px; position: relative;">
                            <canvas id="dashTrafficChart"></canvas>
                        </div>
                    </div>
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
    const url = '{{ route("api.traffic", $selectedRouter) }}';
    let labels = [];
    let rxData = [];
    let txData = [];
    let prevRx = {};
    let prevTx = {};
    let chart = null;

    function initChart() {
        const c = window.getChartColors();
        const ctx = document.getElementById('dashTrafficChart');
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

                data.data.forEach(iface => {
                    const rxBytes = iface.rx_byte || 0;
                    const txBytes = iface.tx_byte || 0;

                    if (prevRx[iface.name] !== undefined) {
                        const rxDelta = Math.max(0, rxBytes - (prevRx[iface.name] || 0));
                        const txDelta = Math.max(0, txBytes - (prevTx[iface.name] || 0));
                        totalRx += rxDelta * 8 / 3;
                        totalTx += txDelta * 8 / 3;
                    }

                    prevRx[iface.name] = rxBytes;
                    prevTx[iface.name] = txBytes;
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

                document.getElementById('dashTotalRx').textContent = window.formatRate(totalRx);
                document.getElementById('dashTotalTx').textContent = window.formatRate(totalTx);

                if (chart) {
                    chart.data.labels = labels;
                    chart.data.datasets[0].data = rxData;
                    chart.data.datasets[1].data = txData;
                    chart.update('none');
                }
            })
            .catch(() => {});
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
