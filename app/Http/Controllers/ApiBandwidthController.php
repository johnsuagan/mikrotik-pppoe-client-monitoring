<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Exception;
use Illuminate\Http\Request;

class ApiBandwidthController extends Controller
{
    public static function formatRate($bps): string
    {
        $bps = (int) $bps;
        if ($bps >= 1000000) return round($bps / 1000000, 1) . ' Mbps';
        if ($bps >= 1000) return round($bps / 1000, 1) . ' kbps';
        return $bps . ' bps';
    }

    public function interfaceTraffic(Request $request, Router $router)
    {
        try {
            $repo = MikroTikRepository::fromRouter($router);
            $interfaces = $repo->getInterfaces();

            $data = [];
            foreach ($interfaces as $iface) {
                $data[] = [
                    'name' => $iface['name'] ?? '',
                    'rx_byte' => (int) ($iface['rx-byte'] ?? 0),
                    'tx_byte' => (int) ($iface['tx-byte'] ?? 0),
                    'running' => isset($iface['running']) && $iface['running'] === 'true',
                ];
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function queueTraffic(Request $request, Router $router)
    {
        try {
            $repo = MikroTikRepository::fromRouter($router);
            $queues = $repo->getSimpleQueues();

            $data = [];
            foreach ($queues as $queue) {
                [$rxByte, $txByte] = $this->splitSlash($queue['bytes'] ?? '0/0');
                [$rxRate, $txRate] = $this->splitSlash($queue['rate'] ?? '0/0');

                $data[] = [
                    'name' => $queue['name'] ?? '',
                    'target' => $queue['target'] ?? '',
                    'rx_rate' => (int) $rxRate,
                    'tx_rate' => (int) $txRate,
                    'rx_byte' => $rxByte,
                    'tx_byte' => $txByte,
                    'max_limit' => $queue['max-limit'] ?? '',
                ];
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function splitSlash(string $value): array
    {
        $parts = explode('/', $value);
        $rx = (int) trim($parts[0] ?? '0');
        $tx = (int) trim($parts[1] ?? $parts[0] ?? '0');
        return [$rx, $tx];
    }

    public function pppoeUsers(Request $request, Router $router)
    {
        try {
            $repo = MikroTikRepository::fromRouter($router);
            $users = $repo->getActivePPPoE();
            return response()->json(['success' => true, 'users' => $users]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function disconnect(Request $request, Router $router)
    {
        try {
            $id = $request->input('id');
            if (!$id) {
                return response()->json(['success' => false, 'error' => 'No user ID provided.'], 400);
            }
            $repo = MikroTikRepository::fromRouter($router);
            $repo->disconnectPPPoE($id);
            return response()->json(['success' => true, 'message' => 'User disconnected.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
