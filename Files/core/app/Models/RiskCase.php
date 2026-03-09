<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskCase extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'evidence' => 'array',
        'resolved_at' => 'datetime',
    ];
}
