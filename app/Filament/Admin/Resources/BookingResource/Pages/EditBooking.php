<?php

namespace App\Filament\Admin\Resources\BookingResource\Pages;

use App\Filament\Admin\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $booking = $this->record;
        
        // Sync addons
        if (isset($this->data['booking_addons']) && is_array($this->data['booking_addons'])) {
            $syncData = [];
            foreach ($this->data['booking_addons'] as $addon) {
                if (isset($addon['addon_id'])) {
                    $syncData[$addon['addon_id']] = [
                        'quantity' => $addon['quantity'] ?? 1,
                        'price' => $addon['price'] ?? 0,
                    ];
                }
            }
            $booking->addons()->sync($syncData);
        } else {
            // If booking_addons is empty or not present, detach all
            $booking->addons()->detach();
        }

        // Update Invoice if exists
        if ($booking->invoice) {
            $booking->invoice->update([
                'subtotal' => $booking->total_amount,
                'total_amount' => $booking->total_amount,
            ]);
        }
        
        // Update Payment if pending
        if ($booking->payment && $booking->payment->status === 'pending') {
            $booking->payment->update([
                'amount' => $booking->total_amount,
            ]);
        }
    }
}
