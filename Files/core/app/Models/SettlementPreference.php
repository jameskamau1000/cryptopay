<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementPreference extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'auto_settle' => 'boolean',
    ];
}
