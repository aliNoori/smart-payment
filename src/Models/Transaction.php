<?php

namespace SmartPayment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Transaction
 *
 * Represents a payment transaction processed through a gateway.
 * Each transaction belongs to a specific order and may include metadata
 * such as card information and gateway response codes.
 *
 * @package SmartPayment\Models
 */
class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',     // Foreign key to the related order
        'gateway',      // Name of the payment gateway (e.g. Zarinpal, IDPay)
        'amount',       // Amount processed in this transaction
        'authority',    // Gateway authority code (used for verification)
        'ref_id',       // Reference ID returned by the gateway
        'status',       // Transaction status: pending, paid, failed
        'card_pan',     // Masked card number (e.g. 6037-****-****-1234)
        'card_hash',    // Hashed card identifier for fraud detection
        'paid_at',      // Timestamp when the transaction was successfully paid
    ];

    /**
     * Get the order associated with this transaction.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
