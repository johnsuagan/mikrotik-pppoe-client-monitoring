<?php

namespace App\Repositories;

use App\Models\Router;
use App\Services\MikroTikService;
use RouterOS\Client;
use RouterOS\Query;

class MikroTikRepository
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function fromEnv(): static
    {
        $service = MikroTikService::fromEnv();
        return new static($service->client());
    }

    public static function fromRouter(Router $router): static
    {
        $service = MikroTikService::fromRouter($router);
        return new static($service->client());
    }

    public function getIdentity(): array
    {
        return $this->client->query(new Query('/system/identity/print'))->read()[0] ?? [];
    }

    public function getResource(): array
    {
        return $this->client->query(new Query('/system/resource/print'))->read()[0] ?? [];
    }

    public function getActivePPPoE(): array
    {
        return $this->client->query(new Query('/ppp/active/print'))->read();
    }

    public function getInterfaces(): array
    {
        return $this->client->query(new Query('/interface/print'))->read();
    }

    public function getInterfaceStats(): array
    {
        return $this->client->query(new Query('/interface/print', ['stats']))->read();
    }

    public function getSimpleQueues(): array
    {
        return $this->client->query(new Query('/queue/simple/print'))->read();
    }

    public function getQueueTree(): array
    {
        return $this->client->query(new Query('/queue/tree/print'))->read();
    }

    public function getQueuePCQ(): array
    {
        return $this->client->query(new Query('/queue/simple/print', ['stats']))->read();
    }

    public function getHotspotActive(): array
    {
        return $this->client->query(new Query('/ip/hotspot/active/print'))->read();
    }

    public function disconnectPPPoE(string $id): void
    {
        $this->client->query(new Query('/ppp/active/remove', ['.id=' . $id]))->read();
    }

    public function getTrafficRate(string $interface): array
    {
        $query = new Query('/interface/print', ['stats', '?name=' . $interface]);
        $result = $this->client->query($query)->read();
        return $result[0] ?? [];
    }
}
