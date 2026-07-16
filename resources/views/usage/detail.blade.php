@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="m-0" style="font-weight: 700; font-size: 24px; letter-spacing: -0.02em;">
                    {{ $clientName }}
                </h1>
                <p class="text-muted mb-0" style="font-size: 13px;">
                    @if($target)
                    <code style="font-size: 11px;">{{ $target }}</code>
                    <span class="mx-2">•</span>
                    @endif
                    {{ $router->name }} — {{ ucfirst($period) }} Usage
                </p>
            </div>
            <div class="d-flex gap-2">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('usage.detail', ['router' => $router, 'clientName' => $clientName, 'period' => 'daily']) }}"
                       class="btn {{ $period === 'daily' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-day me-1"></i> Daily
                    </a>
                    <a href="{{ route('usage.detail', ['router' => $router, 'clientName' => $clientName, 'period' => 'weekly']) }}"
                       class="btn {{ $period === 'weekly' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-week me-1"></i> Weekly
                    </a>
                    <a href="{{ route('usage.detail', ['router' => $router, 'clientName' => $clientName, 'period' => 'monthly']) }}"
                       class="btn {{ $period === 'monthly' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-calendar-alt me-1"></i> Monthly
                    </a>
                </div>
                <a href="{{ route('usage.export', ['router' => $router, 'clientName' => $clientName, 'period' => $period]) }}"
                   class="btn btn-sm btn-outline-success">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
                <a href="{{ route('usage', ['router' => $router]) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-download"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Download</div>
                        <div class="stat-value">{{ format_bytes($totalRx) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-upload"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Total Upload</div>
                        <div class="stat-value">{{ format_bytes($totalTx) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-info">
                    <div class="stat-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Peak Download</div>
                        <div class="stat-value" style="font-size: 18px;">{{ $peakRx ? \App\Http\Controllers\ApiBandwidthController::formatRate($peakRx) : '0 bps' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <div class="stat-info">
                        <div class="stat-label">Peak Upload</div>
                        <div class="stat-value" style="font-size: 18px;">{{ $peakTx ? \App\Http\Controllers\ApiBandwidthController::formatRate($peakTx) : '0 bps' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-panel mb-3">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(80,70,229,0.15); color: var(--primary);">
                    <i class="fas fa-chart-area"></i>
                </div>
                <h3>Bandwidth Usage</h3>
                <span class="badge badge-primary ms-2">{{ $samples }} samples</span>
                <div class="ms-auto d-flex align-items-center gap-3" style="font-size: 12px;">
                    <span><span style="color: rgba(80,70,229,0.85);">&#9632;</span> Download</span>
                    <span><span style="color: rgba(8,145,178,0.85);">&#9632;</span> Upload</span>
                </div>
            </div>
            <div class="panel-body p-3">
                <div style="height: 320px; position: relative;">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>

        <div class="info-panel">
            <div class="panel-header">
                <div class="panel-icon" style="background: rgba(245,158,11,0.15); color: #d97706;">
                    <i class="fas fa-list"></i>
                </div>
                <h3>Usage Log</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Timestamp</th>
                                <th>Download</th>
                                <th>Upload</th>
                                <th>Download Rate</th>
                                <th>Upload Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(collect($chartData)->reverse()->toArray() as $entry)
                            <tr>
                                <td class="text-muted" style="font-size: 12px;">{{ $entry['datetime'] }}</td>
                                <td style="font-weight: 600; color: var(--primary);">{{ format_bytes($entry['rx_byte']) }}</td>
                                <td style="font-weight: 600; color: var(--accent);">{{ format_bytes($entry['tx_byte']) }}</td>
                                <td>{{ \App\Http\Controllers\ApiBandwidthController::formatRate($entry['rx_rate']) }}</td>
                                <td>{{ \App\Http\Controllers\ApiBandwidthController::formatRate($entry['tx_rate']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No data for this period</td>
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
    var chartData = @json($chartData);
    var c = window.getChartColors();

    var labels = chartData.map(function(d) { return d.datetime; });
    var rxData = chartData.map(function(d) { return d.rx_byte; });
    var txData = chartData.map(function(d) { return d.tx_byte; });

    var ctx = document.getElementById('usageChart');
    if (!ctx) return;

    var chart = new Chart(ctx, {
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
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: c.download,
                },
                {
                    label: 'Upload',
                    data: txData,
                    borderColor: c.upload,
                    backgroundColor: c.uploadBg,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: c.upload,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 500 },
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
                    titleFont: { size: 11 },
                    bodyFont: { size: 11 },
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ' + window.formatBytes(ctx.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: { color: c.grid },
                    ticks: { color: c.text, font: { size: 10 }, maxTicksLimit: 12 }
                },
                y: {
                    display: true,
                    grid: { color: c.grid },
                    ticks: {
                        color: c.text,
                        font: { size: 10 },
                        callback: function(v) { return window.formatBytes(v); }
                    },
                    beginAtZero: true
                }
            }
        }
    });

    window.formatBytes = function(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        var units = ['B', 'KB', 'MB', 'GB', 'TB'];
        var i = Math.floor(Math.log(bytes) / Math.log(1024));
        i = Math.min(i, units.length - 1);
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
    };

    window.__updateChartsTheme = function() {
        window.updateChartTheme(chart);
    };
});
</script>
@endpush
