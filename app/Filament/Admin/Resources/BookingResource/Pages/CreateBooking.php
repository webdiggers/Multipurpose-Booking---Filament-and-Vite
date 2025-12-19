<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use App\Filament\Admin\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set created_by to current user
        $data['created_by'] = auth()->id();
        
        // Calculate total_hours from start_time and end_time
        if (isset($data['start_time']) && isset($data['end_time'])) {
            $startTime = \Carbon\Carbon::parse($data['start_time']);
            $endTime = \Carbon\Carbon::parse($data['end_time']);
            // Ensure we calculate positive duration from start to end
            $data['total_hours'] = $startTime->floatDiffInHours($endTime);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Create payment and invoice records after booking is created
        $booking = $this->record;
        
        // Attach addons if provided
        if (isset($this->data['booking_addons']) && is_array($this->data['booking_addons'])) {
            foreach ($this->data['booking_addons'] as $addon) {
                if (isset($addon['addon_id'])) {
                    $booking->addons()->attach($addon['addon_id'], [
                        'quantity' => $addon['quantity'] ?? 1,
                        'price' => $addon['price'] ?? 0,
                    ]);
                }
            }
        }
        
        // Create payment record
        $paymentStatus = $booking->payment_status === 'paid' ? 'success' : 'pending';
        if ($booking->payment_status === 'failed') $paymentStatus = 'failed';
        
        $payment = $booking->payment()->create([
            'amount' => $booking->total_amount,
            'payment_method' => $booking->payment_method,
            'status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'success' ? now() : null,
        ]);
        
        // Create invoice
        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'subtotal' => $booking->total_amount,
            'total_amount' => $booking->total_amount,
            'status' => $booking->payment_status === 'paid' ? 'paid' : 'pending',
            'generated_at' => now(),
        ]);

        // Mark Time Slots as Booked
        $startTime = \Carbon\Carbon::parse($booking->start_time);
        $totalHours = ceil($booking->total_hours);
        
        for ($i = 0; $i < $totalHours; $i++) {
            $slotStart = $startTime->copy()->addHours($i)->format('H:i:00');
            
            \App\Models\TimeSlot::where('studio_id', $booking->studio_id)
                ->where('date', $booking->booking_date)
                ->where('start_time', $slotStart)
                ->update([
                    'is_booked' => true,
                    'booking_id' => $booking->id,
                ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to invoice view page
        return InvoiceResource::getUrl('view', ['record' => $this->record->invoice]);
    }
}
