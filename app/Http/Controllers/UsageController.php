<?php

namespace App\Http\Controllers;

use App\Models\QueueUsageLog;
use App\Models\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsageController extends Controller
{
    public function index(Request $request)
    {
        $routers = Router::where('active', true)->get();
        $selectedId = $request->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        $clients = [];
        if ($selectedRouter) {
            $clients = QueueUsageLog::where('router_id', $selectedRouter->id)
                ->select('queue_name', 'target')
                ->selectRaw('SUM(tx_byte) as total_tx')
                ->selectRaw('SUM(rx_byte) as total_rx')
                ->groupBy('queue_name', 'target')
                ->orderBy('queue_name')
                ->get();
        }

        return view('usage.index', compact('routers', 'selectedRouter', 'clients'));
    }

    public function detail(Request $request, Router $router, string $clientName)
    {
        $period = $request->query('period', 'daily');
        $from = match ($period) {
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            default => now()->subDay(),
        };

        $logs = QueueUsageLog::where('router_id', $router->id)
            ->where('queue_name', $clientName)
            ->where('created_at', '>=', $from)
            ->orderBy('created_at')
            ->get();

        $chartData = [];
        foreach ($logs as $log) {
            $chartData[] = [
                'time' => $log->created_at->format('H:i'),
                'date' => $log->created_at->format('M d'),
                'datetime' => $log->created_at->format('M d H:i'),
                'rx_byte' => $log->rx_byte,
                'tx_byte' => $log->tx_byte,
                'rx_rate' => $log->rx_rate,
                'tx_rate' => $log->tx_rate,
            ];
        }

        $totalTx = $logs->sum('tx_byte');
        $totalRx = $logs->sum('rx_byte');
        $peakTx = $logs->max('tx_rate');
        $peakRx = $logs->max('rx_rate');
        $samples = $logs->count();

        $target = $logs->first()->target ?? '';

        return view('usage.detail', compact(
            'router', 'clientName', 'target', 'period', 'chartData',
            'totalTx', 'totalRx', 'peakTx', 'peakRx', 'samples'
        ));
    }

    public function export(Request $request, Router $router, string $clientName)
    {
        $period = $request->query('period', 'daily');
        $from = match ($period) {
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            default => now()->subDay(),
        };

        $logs = QueueUsageLog::where('router_id', $router->id)
            ->where('queue_name', $clientName)
            ->where('created_at', '>=', $from)
            ->orderBy('created_at')
            ->get();

        $filename = "usage_{$clientName}_{$period}_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs, $clientName) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'Download Bytes', 'Upload Bytes', 'Download Rate', 'Upload Rate']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->rx_byte,
                    $log->tx_byte,
                    $log->rx_rate,
                    $log->tx_rate,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
