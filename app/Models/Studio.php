<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    protected $fillable = [
        'name',
        'description',
        'hourly_rate',
        'capacity',
        'slot_duration',
        'operating_hours',
        'amenities',
        'image',
        'gallery',
        'is_active',
        'allowed_durations',
    ];

    protected $casts = [
        'amenities' => 'array',
        'gallery' => 'array',
        'operating_hours' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'slot_duration' => 'integer',
        'allowed_durations' => 'array',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableSlots($date)
    {
        return $this->timeSlots()
            ->where('date', $date)
            ->where('is_booked', false)
            ->orderBy('start_time')
            ->get();
    }

    public function isAvailableAt($date, $startTime, $endTime)
    {
        return !$this->timeSlots()
            ->where('date', $date)
            ->where('is_booked', true)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();
    }
}
