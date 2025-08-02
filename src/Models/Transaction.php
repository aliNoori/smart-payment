<?php

namespace SmartPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'gateway',
        'amount',
        'authority',
        'ref_id',
        'status',
        'card_pan',
        'card_hash',
        'paid_at',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
