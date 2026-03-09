<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnchainDeposit extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'detected_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
