<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'customer' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apiKey()
    {
        return $this->belongsTo(MerchantApiKey::class, 'api_key_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    public function depositAddress()
    {
        return $this->hasOne(MerchantDepositAddress::class);
    }

    public function onchainDeposits()
    {
        return $this->hasMany(OnchainDeposit::class);
    }
}
