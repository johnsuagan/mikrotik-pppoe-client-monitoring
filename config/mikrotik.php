<?php

namespace App\Config;

return [
    'default_host' => env('MIKROTIK_HOST', '192.168.200.1'),
    'default_port' => env('MIKROTIK_PORT', 8728),
    'default_user' => env('MIKROTIK_USER', 'admin'),
    'default_password' => env('MIKROTIK_PASSWORD', ''),
];
