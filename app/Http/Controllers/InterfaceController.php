<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use Exception;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    public function index(Request $request)
    {
        $routers = Router::where('active', true)->get();
        $selectedId = $request->query('router');
        $selectedRouter = $selectedId ? $routers->firstWhere('id', $selectedId) : $routers->first();

        $interfaces = [];
        $error = null;

        if ($selectedRouter) {
            try {
                $repo = MikroTikRepository::fromRouter($selectedRouter);
                $interfaces = $repo->getInterfaces();
            } catch (Exception $e) {
                $error = 'Could not connect to router: ' . $selectedRouter->name;
            }
        }

        return view('interfaces.index', compact('routers', 'selectedRouter', 'interfaces', 'error'));
    }

    public function stats(Request $request, Router $router)
    {
        try {
            $repo = MikroTikRepository::fromRouter($router);
            $interfaces = $repo->getInterfaces();
            return response()->json(['success' => true, 'interfaces' => $interfaces]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
