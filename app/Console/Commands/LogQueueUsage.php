<?php

namespace App\Console\Commands;

use App\Models\QueueUsageLog;
use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Illuminate\Console\Command;

class LogQueueUsage extends Command
{
    protected $signature = 'queue:log-usage';
    protected $description = 'Log current queue byte counters for all active routers';

    public function handle(): int
    {
        $routers = Router::where('active', true)->get();
        $logged = 0;

        foreach ($routers as $router) {
            try {
                $repo = MikroTikRepository::fromRouter($router);
                $queues = $repo->getSimpleQueues();

                foreach ($queues as $queue) {
                    [$rxByte, $txByte] = $this->splitSlash($queue['bytes'] ?? '0/0');
                    [$rxRate, $txRate] = $this->splitSlash($queue['rate'] ?? '0/0');

                    QueueUsageLog::create([
                        'router_id' => $router->id,
                        'queue_name' => $queue['name'] ?? '',
                        'target' => $queue['target'] ?? null,
                        'rx_byte' => $rxByte,
                        'tx_byte' => $txByte,
                        'rx_rate' => (int) $rxRate,
                        'tx_rate' => (int) $txRate,
                    ]);
                    $logged++;
                }

                $this->info("Logged " . count($queues) . " queues from {$router->name}");
            } catch (\Exception $e) {
                $this->error("Failed to log queues for {$router->name}: " . $e->getMessage());
            }
        }

        $this->info("Total queue entries logged: {$logged}");
        return 0;
    }

    private function splitSlash(string $value): array
    {
        $parts = explode('/', $value);
        $rx = (int) trim($parts[0] ?? '0');
        $tx = (int) trim($parts[1] ?? $parts[0] ?? '0');
        return [$rx, $tx];
    }

    private function parseBytes(string $value): int
    {
        if (preg_match('/([\d.]+)\s*(B|KB|MB|GB|TB)/i', $value, $m)) {
            $val = (float) $m[1];
            $unit = strtoupper($m[2]);
            return match($unit) {
                'TB' => (int) ($val * 1099511627776),
                'GB' => (int) ($val * 1073741824),
                'MB' => (int) ($val * 1048576),
                'KB' => (int) ($val * 1024),
                default => (int) $val,
            };
        }
        return (int) $value;
    }

    private function parseRateToBps(string $rate): int
    {
        if (!$rate || $rate === '0bps') return 0;
        if (preg_match('/([\d.]+)\s*(bit|kbps|Mbps|Gbps|bps)/i', $rate, $m)) {
            $val = (float) $m[1];
            $unit = strtolower($m[2]);
            return (int) match($unit) {
                'gbps' => $val * 1000000,
                'mbps' => $val * 1000,
                'kbps' => $val,
                'bps' => $val,
                'bit' => $val,
                default => $val,
            };
        }
        return 0;
    }
}
