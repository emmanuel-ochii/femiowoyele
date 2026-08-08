<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'reference', 'book_id', 'pickup_point_id', 'name', 'email', 'phone', 'quantity',
        'unit_amount', 'total_amount', 'currency', 'status', 'payment_provider',
        'payment_reference', 'paid_at', 'payment_meta',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_amount' => 'integer',
            'total_amount' => 'integer',
            'paid_at' => 'datetime',
            'payment_meta' => 'array',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function pickupPoint(): BelongsTo
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /** Minor units (kobo) rendered as a display string, e.g. "₦12,500". */
    public function formattedTotal(): string
    {
        return $this->formatAmount($this->total_amount);
    }

    public function formattedUnit(): string
    {
        return $this->formatAmount($this->unit_amount);
    }

    private function formatAmount(int $minorUnits): string
    {
        $symbol = config('payments.currency_symbol', '₦');

        return $symbol.number_format($minorUnits / 100, 2);
    }
}
