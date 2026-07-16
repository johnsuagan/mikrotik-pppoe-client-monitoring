<?php

namespace App\Services;

use App\Models\Router;
use RouterOS\Client;
use RouterOS\Config;

class MikroTikService
{
    protected Client $client;

    public function __construct(array $config)
    {
        $this->client = new Client(new Config($config));
    }

    public static function fromEnv(): static
    {
        return new static([
            'host' => env('MIKROTIK_HOST', '192.168.200.1'),
            'port' => (int) env('MIKROTIK_PORT', 8728),
            'user' => env('MIKROTIK_USER', 'admin'),
            'pass' => env('MIKROTIK_PASSWORD', ''),
        ]);
    }

    public static function fromRouter(Router $router): static
    {
        return new static([
            'host' => $router->host,
            'port' => $router->port,
            'user' => $router->username,
            'pass' => $router->password,
        ]);
    }

    public function client(): Client
    {
        return $this->client;
    }
}
