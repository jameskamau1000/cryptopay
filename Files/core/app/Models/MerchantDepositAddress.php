<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantDepositAddress extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
