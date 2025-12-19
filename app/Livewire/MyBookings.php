<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\TimeSlot;

class MyBookings extends Component
{
    public $filter = 'all'; // all, upcoming, past, cancelled

    public function cancelBooking($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            session()->flash('error', 'Booking not found');
            return;
        }

        if ($booking->booking_status === 'cancelled') {
            session()->flash('error', 'Booking is already cancelled');
            return;
        }

        if ($booking->booking_status === 'completed') {
            session()->flash('error', 'Cannot cancel completed booking');
            return;
        }

        // Release time slots
        TimeSlot::where('booking_id', $booking->id)
            ->update([
                'is_booked' => false,
                'booking_id' => null,
            ]);

        // Update booking and payment status
        $booking->update([
            'booking_status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        if ($booking->payment) {
            $booking->payment->update(['payment_status' => 'refunded']);
        }

        session()->flash('success', 'Booking cancelled successfully');
    }

    public function generateInvoice($bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->first();

        if ($booking && !$booking->invoice) {
            \App\Models\Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'subtotal' => $booking->total_amount,
                'total_amount' => $booking->total_amount,
                'status' => $booking->payment_status === 'paid' ? 'paid' : 'pending',
                'generated_at' => now(),
            ]);
            
            session()->flash('success', 'Invoice generated successfully');
        }
    }

    public function render()
    {
        $query = Booking::where('user_id', auth()->id())
            ->with(['studio', 'addons', 'payment', 'invoice'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc');

        // Apply filters
        switch ($this->filter) {
            case 'upcoming':
                $query->where('booking_date', '>=', now()->format('Y-m-d'))
                    ->where('booking_status', '!=', 'cancelled');
                break;
            case 'past':
                $query->where(function ($q) {
                    $q->where('booking_date', '<', now()->format('Y-m-d'))
                        ->orWhere('booking_status', 'completed');
                });
                break;
            case 'cancelled':
                $query->where('booking_status', 'cancelled');
                break;
        }

        $bookings = $query->get();

        return view('livewire.my-bookings', [
            'bookings' => $bookings,
        ])->layout('components.layouts.app');
    }
}
