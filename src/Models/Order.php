<?php

namespace SmartPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 *
 * Represents a payment order in the SmartPayment system.
 * Each order can have multiple related transactions (e.g. retries, partial payments).
 *
 * @package SmartPayment\Models
 */
class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',      // ID of the user who placed the order
        'amount',       // Total amount of the order
        'status',       // Current status: pending, paid, failed, canceled
        'description',  // Optional description or notes
        'currency',     // Currency code (e.g. IRR, USD)
    ];

    /**
     * Get all transactions associated with this order.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
