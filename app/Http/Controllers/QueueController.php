<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Exception;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $routers = Router::where('active', true)->get();
        $selectedId = $request->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        $queues = [];
        $error = null;

        if ($selectedRouter) {
            try {
                $repo = MikroTikRepository::fromRouter($selectedRouter);
                $queues = $repo->getSimpleQueues();
            } catch (Exception $e) {
                $error = 'Could not connect to router: ' . $selectedRouter->name;
            }
        }

        return view('queues.index', compact('routers', 'selectedRouter', 'queues', 'error'));
    }

    public function traffic(Request $request)
    {
        $routers = Router::where('active', true)->get();
        $selectedId = $request->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        $queues = [];
        $error = null;

        if ($selectedRouter) {
            try {
                $repo = MikroTikRepository::fromRouter($selectedRouter);
                $queues = $repo->getSimpleQueues();
            } catch (Exception $e) {
                $error = 'Could not connect to router: ' . $selectedRouter->name;
            }
        }

        return view('queues.traffic', compact('routers', 'selectedRouter', 'queues', 'error'));
    }

    public function stats(Request $request, Router $router)
    {
        try {
            $repo = MikroTikRepository::fromRouter($router);
            $queues = $repo->getSimpleQueues();
            return response()->json(['success' => true, 'queues' => $queues]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
