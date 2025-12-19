<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Bookings', Booking::where('booking_status', 'pending')->count())
                ->description('Bookings waiting for approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(\App\Filament\Admin\Resources\BookingResource::getUrl('index', [
                    'tableFilters' => [
                        'booking_status' => ['value' => 'pending'],
                    ],
                ])),
            
            Stat::make('Today\'s Bookings', Booking::whereDate('booking_date', today())->count())
                ->description('Scheduled for today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->url(\App\Filament\Admin\Resources\BookingResource::getUrl('index', [
                    'tableFilters' => [
                        'booking_date' => [
                            'from' => today()->format('Y-m-d'),
                            'until' => today()->format('Y-m-d'),
                        ],
                    ],
                ])),

            Stat::make('Total Revenue', '$' . number_format(Booking::where('payment_status', 'paid')->sum('total_amount'), 2))
                ->description('Total earnings from confirmed bookings')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->url(\App\Filament\Admin\Resources\BookingResource::getUrl('index', [
                    'tableFilters' => [
                        'payment_status' => ['value' => 'paid'],
                    ],
                ])),
        ];
    }
}
