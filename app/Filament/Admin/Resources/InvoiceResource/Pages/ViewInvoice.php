<?php

namespace App\Filament\Admin\Resources\InvoiceResource\Pages;

use App\Filament\Admin\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('invoices.print', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('invoices.download', $this->record)),
            Actions\Action::make('whatsapp')
                ->label('Share via WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->url(function () {
                    $invoice = $this->record;
                    $downloadUrl = route('invoices.download', $invoice);
                    $message = urlencode(
                        "Hello {$invoice->booking->user->name},\n\n" .
                        "Your invoice from " . Setting::get('company_name', 'Company Name') . " is ready!\n\n" .
                        "Invoice: #{$invoice->invoice_number}\n" .
                        "Amount: ₹{$invoice->total_amount}\n" .
                        "Date: " . $invoice->created_at->format('F d, Y') . "\n" .
                        "Studio: {$invoice->booking->studio->name}\n" .
                        "Time: " . \Carbon\Carbon::parse($invoice->booking->start_time)->format('h:i A') . " - " . \Carbon\Carbon::parse($invoice->booking->end_time)->format('h:i A') . "\n\n" .
                        "Download your invoice: " . url($downloadUrl) . "\n\n" .
                        "Thank you for choosing " . Setting::get('company_name', 'Company Name') . "! 🎵"
                    );
                    $phone = $invoice->booking->user->phone ?? '';
                    return "https://wa.me/{$phone}?text={$message}";
                })
                ->openUrlInNewTab(),
            Actions\Action::make('email')
                ->label('Email Invoice')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->url(function () {
                    $invoice = $this->record;
                    $email = $invoice->booking->user->email ?? '';
                    $subject = urlencode("Invoice #{$invoice->invoice_number} from " . Setting::get('company_name', 'Company Name'));
                    $body = urlencode("Hi {$invoice->booking->user->name},\n\nPlease find your invoice #{$invoice->invoice_number} attached.\n\nTotal Amount: ₹{$invoice->total_amount}\n\nThank you!");
                    return "mailto:{$email}?subject={$subject}&body={$body}";
                }),
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->action(fn () => $this->record->booking->delete())
                ->successRedirectUrl(InvoiceResource::getUrl('index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Invoice Details')
                    ->schema([
                        Components\TextEntry::make('invoice_number')
                            ->label('Invoice Number'),
                        Components\TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime(),
                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(3),

                Components\Section::make('Customer Information')
                    ->schema([
                        Components\TextEntry::make('booking.user.name')
                            ->label('Customer Name'),
                        Components\TextEntry::make('booking.user.phone')
                            ->label('Phone'),
                        Components\TextEntry::make('booking.user.email')
                            ->label('Email'),
                    ])->columns(3),

                Components\Section::make('Booking Details')
                    ->schema([
                        Components\TextEntry::make('booking.studio.name')
                            ->label('Studio'),
                        Components\TextEntry::make('booking.booking_date')
                            ->label('Date')
                            ->date(),
                        Components\TextEntry::make('booking.start_time')
                            ->label('Start Time')
                            ->time('H:i'),
                        Components\TextEntry::make('booking.end_time')
                            ->label('End Time')
                            ->time('H:i'),
                        Components\TextEntry::make('booking.total_hours')
                            ->label('Total Hours')
                            ->suffix(' hours')
                            ->formatStateUsing(fn ($state) => abs($state)),
                    ])->columns(3),

                Components\Section::make('Invoice Breakdown')
                    ->columnSpan('full')
                    ->schema([
                        Components\ViewEntry::make('breakdown')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->view('filament.infolists.components.invoice-breakdown'),
                    ])->columns(1),

                Components\Section::make('Payment Information')
                    ->schema([
                        Components\TextEntry::make('booking.payment_method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'online' => 'Online Payment',
                                'pay_at_studio' => 'Pay at Studio',
                                default => $state,
                            }),
                        Components\TextEntry::make('booking.payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Components\Section::make('Notes')
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->default('No notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
