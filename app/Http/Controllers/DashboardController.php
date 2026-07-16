<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $routers = Router::where('active', true)->get();

        if ($routers->isEmpty()) {
            return view('dashboard', [
                'routers' => $routers,
                'selectedRouter' => null,
                'identity' => null,
                'resource' => null,
                'onlineUsers' => 0,
                'interfaces' => 0,
                'error' => null,
            ]);
        }

        $selectedId = request()->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        if (!$selectedRouter) {
            $selectedRouter = $routers->first();
        }

        try {
            $repo = MikroTikRepository::fromRouter($selectedRouter);
            $identity = $repo->getIdentity();
            $resource = $repo->getResource();
            $pppoe = $repo->getActivePPPoE();
            $interfaces = $repo->getInterfaces();

            return view('dashboard', [
                'routers' => $routers,
                'selectedRouter' => $selectedRouter,
                'identity' => $identity,
                'resource' => $resource,
                'onlineUsers' => is_countable($pppoe) ? count($pppoe) : 0,
                'interfaces' => is_countable($interfaces) ? count($interfaces) : 0,
                'error' => null,
            ]);
        } catch (Exception $e) {
            Log::error("Dashboard Error ({$selectedRouter->name}): " . $e->getMessage());

            return view('dashboard', [
                'routers' => $routers,
                'selectedRouter' => $selectedRouter,
                'identity' => null,
                'resource' => null,
                'onlineUsers' => 0,
                'interfaces' => 0,
                'error' => 'Could not connect to router: ' . $selectedRouter->name,
            ]);
        }
    }
}
