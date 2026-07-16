<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Exception;
use Illuminate\Http\Request;

class PPPoEController extends Controller
{
    public function index(Request $request)
    {
        $routers = Router::where('active', true)->get();
        $selectedId = $request->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        $users = [];
        $error = null;

        if ($selectedRouter) {
            try {
                $repo = MikroTikRepository::fromRouter($selectedRouter);
                $users = $repo->getActivePPPoE();
            } catch (Exception $e) {
                $error = 'Could not connect to router: ' . $selectedRouter->name;
            }
        }

        return view('pppoe.index', compact('routers', 'selectedRouter', 'users', 'error'));
    }

    public function disconnect(Request $request, Router $router)
    {
        try {
            $id = $request->input('id');
            $repo = MikroTikRepository::fromRouter($router);
            $repo->disconnectPPPoE($id);
            return response()->json(['success' => true, 'message' => 'User disconnected.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
