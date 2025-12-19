<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'studio_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_hours',
        'base_amount',
        'addon_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'booking_status',
        'created_by',
        'notes'
    ];

    protected $appends = ['booking_addons'];

    protected $casts = [
        'booking_date' => 'date',
        'base_amount' => 'decimal:2',
        'addon_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'booking_addons')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->base_amount + $this->addon_amount;
        return $this->total_amount;
    }

    public function canAddHourlyExtension()
    {
        // Check if next slot is available
        $nextSlotStart = \Carbon\Carbon::parse($this->end_time);
        $nextSlotEnd = $nextSlotStart->copy()->addHour();

        return !TimeSlot::where('studio_id', $this->studio_id)
            ->where('date', $this->booking_date)
            ->where('start_time', $nextSlotStart->format('H:i:s'))
            ->where('is_booked', true)
            ->exists();
    }

    public function scopePending($query)
    {
        return $query->where('booking_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function getBookingAddonsAttribute()
    {
        return $this->addons->map(function ($addon) {
            return [
                'addon_id' => $addon->id,
                'quantity' => $addon->pivot->quantity,
                'price' => $addon->pivot->price,
            ];
        })->toArray();
    }
}
