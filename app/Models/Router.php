<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = ['name', 'host', 'port', 'username', 'password', 'active'];

    protected $hidden = ['password'];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer',
    ];
}
