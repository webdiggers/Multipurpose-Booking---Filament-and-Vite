<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = [
        'studio_id',
        'date',
        'start_time',
        'end_time',
        'is_booked',
        'booking_id'
    ];

    protected $casts = [
        'date' => 'date',
        'is_booked' => 'boolean',
    ];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function isAvailable(): bool
    {
        return !$this->is_booked;
    }

    public function book(Booking $booking): bool
    {
        $this->is_booked = true;
        $this->booking_id = $booking->id;
        return $this->save();
    }

    public function release(): bool
    {
        $this->is_booked = false;
        $this->booking_id = null;
        return $this->save();
    }
}
