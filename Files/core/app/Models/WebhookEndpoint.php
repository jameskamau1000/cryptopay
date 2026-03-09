<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'events' => 'array',
        'status' => 'boolean',
        'last_rotated_at' => 'datetime',
    ];
}
