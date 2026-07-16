<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::all();
        $selectedRouter = null;
        return view('routers.index', compact('routers', 'selectedRouter'));
    }

    public function create()
    {
        $routers = Router::all();
        $selectedRouter = null;
        return view('routers.create', compact('routers', 'selectedRouter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $validated['active'] = $request->boolean('active');

        Router::create($validated);

        return redirect()->route('routers.index')->with('success', 'Router added successfully.');
    }

    public function edit(Router $router)
    {
        $routers = Router::all();
        $selectedRouter = null;
        return view('routers.edit', compact('router', 'routers', 'selectedRouter'));
    }

    public function update(Request $request, Router $router)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $validated['active'] = $request->boolean('active');

        $router->update($validated);

        return redirect()->route('routers.index')->with('success', 'Router updated successfully.');
    }

    public function destroy(Router $router)
    {
        $router->delete();
        return redirect()->route('routers.index')->with('success', 'Router deleted successfully.');
    }

    public function test(Router $router)
    {
        try {
            $repo = \App\Repositories\MikroTikRepository::fromRouter($router);
            $identity = $repo->getIdentity();
            return response()->json(['success' => true, 'identity' => $identity['name'] ?? 'Unknown']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
