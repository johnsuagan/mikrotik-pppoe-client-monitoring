<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PPPoEController;
use App\Http\Controllers\InterfaceController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\ApiBandwidthController;
use App\Http\Controllers\UsageController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pppoe', [PPPoEController::class, 'index'])->name('pppoe');
    Route::post('/pppoe/{router}/disconnect', [PPPoEController::class, 'disconnect'])->name('pppoe.disconnect');

    Route::get('/interfaces', [InterfaceController::class, 'index'])->name('interfaces');

    Route::get('/queues', [QueueController::class, 'index'])->name('queues');
    Route::get('/queues/traffic', [QueueController::class, 'traffic'])->name('queues.traffic');

    Route::get('/usage', [UsageController::class, 'index'])->name('usage');
    Route::get('/usage/{router}/{clientName}', [UsageController::class, 'detail'])->name('usage.detail');
    Route::get('/usage/{router}/{clientName}/export', [UsageController::class, 'export'])->name('usage.export');

    Route::resource('routers', RouterController::class)->except('show');
    Route::post('/routers/{router}/test', [RouterController::class, 'test'])->name('routers.test');

    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/router/{router}/traffic', [ApiBandwidthController::class, 'interfaceTraffic'])->name('traffic');
        Route::get('/router/{router}/queues', [ApiBandwidthController::class, 'queueTraffic'])->name('queues');
        Route::get('/router/{router}/pppoe', [ApiBandwidthController::class, 'pppoeUsers'])->name('pppoe');
        Route::post('/router/{router}/disconnect', [ApiBandwidthController::class, 'disconnect'])->name('disconnect');
    });
});
