<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayoutBatchItem extends Model
{
    protected $guarded = ['id'];

    public function batch()
    {
        return $this->belongsTo(PayoutBatch::class, 'batch_id');
    }
}
