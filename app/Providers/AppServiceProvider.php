<?php

namespace App\Providers;

use App\Models\Router;
use App\Repositories\MikroTikRepository;
use App\Services\MikroTikService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MikroTikService::class, function () {
            return MikroTikService::fromEnv();
        });

        $this->app->singleton(MikroTikRepository::class, function () {
            return MikroTikRepository::fromEnv();
        });
    }

    public function boot(): void
    {
        require_once app_path('helpers.php');
    }
}
