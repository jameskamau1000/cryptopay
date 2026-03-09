<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'next_retry_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];
}
