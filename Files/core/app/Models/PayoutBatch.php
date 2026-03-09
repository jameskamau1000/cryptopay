<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutBatch extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'summary' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(PayoutBatchItem::class, 'batch_id');
    }
}
