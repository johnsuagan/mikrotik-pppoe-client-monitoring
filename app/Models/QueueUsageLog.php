<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueueUsageLog extends Model
{
    protected $fillable = [
        'router_id',
        'queue_name',
        'target',
        'rx_byte',
        'tx_byte',
        'rx_rate',
        'tx_rate',
    ];

    protected $casts = [
        'rx_byte' => 'integer',
        'tx_byte' => 'integer',
        'rx_rate' => 'integer',
        'tx_rate' => 'integer',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}
