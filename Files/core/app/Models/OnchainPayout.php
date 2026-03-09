<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnchainPayout extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payload' => 'array',
        'broadcasted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function payout()
    {
        return $this->belongsTo(Payout::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(CustodyWallet::class, 'from_wallet_id');
    }
}
