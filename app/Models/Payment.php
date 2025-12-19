<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'transaction_id',
        'payment_gateway',
        'status',
        'paid_at',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function markAsPaid(): void
    {
        $this->status = 'success';
        $this->paid_at = now();
        $this->save();

        // Update booking payment status
        $this->booking->payment_status = 'paid';
        $this->booking->booking_status = 'confirmed';
        $this->booking->save();
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
        $this->save();

        // Update booking payment status
        $this->booking->payment_status = 'failed';
        $this->booking->save();
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
