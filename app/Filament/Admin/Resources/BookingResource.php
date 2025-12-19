<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Filament\Admin\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\User;
use App\Models\Studio;
use App\Models\Addon;
use App\Models\TimeSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Booking Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'phone')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->phone ?? 'No Phone')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->required()
                                    ->tel()
                                    ->unique(),
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Booking Details')
                    ->columnSpan('full')
                    ->schema([
                        Forms\Components\Select::make('studio_id')
                            ->label('Event/Resource')
                            ->relationship('studio', 'name', function ($query) {
                                return $query->where('is_active', true);
                            })
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    $studio = Studio::find($state);
                                    if ($studio) {
                                        $startTime = $get('start_time');
                                        $endTime = $get('end_time');
                                        if ($startTime && $endTime) {
                                            $hours = \Carbon\Carbon::parse($endTime)->diffInHours(\Carbon\Carbon::parse($startTime));
                                            $baseAmount = $studio->hourly_rate * $hours;
                                            $set('base_amount', $baseAmount);
                                            $set('total_amount', $baseAmount + ($get('addon_amount') ?? 0));
                                        }
                                    }
                                }
                            }),
                        Forms\Components\DatePicker::make('booking_date')
                            ->required()
                            ->native(false)
                            ->default(today())
                            ->minDate(today())
                            ->live(), // Make live to trigger slot updates
                            
                        Forms\Components\ViewField::make('start_time')
                            ->label('Time Slot')
                            ->hiddenOn('view')
                            ->view('filament.forms.components.time-slot-picker')
                            ->viewData(function (Forms\Get $get, ?Booking $record) {
                                $studioId = $get('studio_id');
                                $date = $get('booking_date');
                                
                                if ($date) {
                                    try {
                                        $date = \Carbon\Carbon::parse($date)->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        // Ignore
                                    }
                                }
                                
                                $slots = [];
                                if ($studioId && $date) {
                                    // Generate slots if they don't exist
                                    $count = TimeSlot::where('studio_id', $studioId)
                                        ->where('date', $date)
                                        ->count();
                                        
                                    if ($count === 0) {
                                        $studio = Studio::find($studioId);
                                        if ($studio) {
                                            $dayOfWeek = strtolower(\Carbon\Carbon::parse($date)->format('l'));
                                            $operatingHours = $studio->operating_hours;
                                            $dayConfig = null;
                                            
                                            if ($operatingHours && is_array($operatingHours)) {
                                                foreach ($operatingHours as $config) {
                                                    if (isset($config['day']) && strtolower($config['day']) === $dayOfWeek) {
                                                        $dayConfig = $config;
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            // Default if not configured
                                            if (!$dayConfig) {
                                                $dayConfig = ['enabled' => true, 'start' => '09:00', 'end' => '22:00'];
                                            }
                                            
                                            if (isset($dayConfig['enabled']) && $dayConfig['enabled']) {
                                                $startTime = \Carbon\Carbon::parse($date . ' ' . ($dayConfig['start'] ?? '09:00'));
                                                $endTime = \Carbon\Carbon::parse($date . ' ' . ($dayConfig['end'] ?? '22:00'));
                                                $slotDuration = $studio->slot_duration ?? 60;
                                                
                                                $currentSlot = $startTime->copy();
                                                $now = now();
                                                $isToday = $date === $now->format('Y-m-d');
                                                
                                                while ($currentSlot->lt($endTime)) {
                                                    $slotEnd = $currentSlot->copy()->addMinutes($slotDuration);
                                                    if ($slotEnd->gt($endTime)) break;
                                                    
                                                    $slotStartStr = $currentSlot->format('H:i:s');
                                                    $slotEndStr = $slotEnd->format('H:i:s');
                                                    
                                                    if ($isToday && $currentSlot->isPast()) {
                                                        $currentSlot->addMinutes($slotDuration);
                                                        continue;
                                                    }
                                                    
                                                    TimeSlot::firstOrCreate([
                                                        'studio_id' => $studioId,
                                                        'date' => $date,
                                                        'start_time' => $slotStartStr,
                                                    ], [
                                                        'end_time' => $slotEndStr,
                                                        'is_booked' => false,
                                                    ]);
                                                    
                                                    $currentSlot->addMinutes($slotDuration);
                                                }
                                            }
                                        }
                                    }
                                    
                                    $query = TimeSlot::where('studio_id', $studioId)
                                        ->where('date', $date)
                                        ->with('booking')
                                        ->orderBy('start_time');

                                    if ($date === now()->format('Y-m-d')) {
                                        $query->where('start_time', '>', now()->format('H:i:s'));
                                    }

                                    $slots = $query->get();
                                }

                                return [
                                    'slots' => $slots,
                                    'studioId' => $studioId,
                                    'bookingDate' => $date,
                                    'currentBookingId' => $record?->id,
                                ];
                            })
                            ->key(fn (Forms\Get $get) => 'slots-' . $get('studio_id') . '-' . $get('booking_date'))
                            ->required()
                            ->live()
                            ->columnSpan('full')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Auto-set end time based on slot duration
                                    $studioId = $get('studio_id');
                                    $slotDuration = 60;
                                    if ($studioId) {
                                        $studio = Studio::find($studioId);
                                        if ($studio) $slotDuration = $studio->slot_duration ?? 60;
                                    }
                                    
                                    $endTime = \Carbon\Carbon::parse($state)->addMinutes($slotDuration)->format('H:i');
                                    $set('end_time', $endTime);
                                    
                                    // Recalculate amounts
                                    if ($studioId) {
                                        $studio = Studio::find($studioId);
                                        $hours = $slotDuration / 60;
                                        $baseAmount = $studio->hourly_rate * $hours;
                                        $set('base_amount', $baseAmount);
                                        $set('total_hours', $hours);
                                        $set('total_amount', $baseAmount + ($get('addon_amount') ?? 0));
                                    }
                                }
                            }),

                        Forms\Components\Hidden::make('end_time')
                            ->required(),
                            
                        Forms\Components\Placeholder::make('booking_summary')
                            ->label('Booking Summary')
                            ->content(function (Forms\Get $get) {
                                $start = $get('start_time');
                                if (!$start) return 'Please select a time slot';
                                
                                $studioId = $get('studio_id');
                                $studioName = 'Select Event/Resource';
                                $hourlyRate = 0;
                                $slotDuration = 60;
                                
                                if ($studioId) {
                                    $studio = Studio::find($studioId);
                                    if ($studio) {
                                        $studioName = $studio->name;
                                        $hourlyRate = $studio->hourly_rate;
                                        $slotDuration = $studio->slot_duration ?? 60;
                                    } else {
                                        $studioName = 'Unknown Event/Resource';
                                    }
                                }
                                
                                $end = $get('end_time');
                                if (!$end) $end = \Carbon\Carbon::parse($start)->addMinutes($slotDuration)->format('H:i');
                                
                                // Calculate total duration from start/end times
                                $startTime = \Carbon\Carbon::parse($start);
                                $endTime = \Carbon\Carbon::parse($end);
                                $totalDuration = $startTime->floatDiffInHours($endTime);
                                
                                $timeRange = $startTime->format('g:i A') . " - " . $endTime->format('g:i A');
                                
                                $addonsHtml = '';
                                $addons = $get('booking_addons') ?? [];
                                $addonAmount = 0;
                                $extraHours = 0;

                                if (is_array($addons)) {
                                    foreach ($addons as $addonItem) {
                                        if (empty($addonItem['addon_id'])) continue;
                                        $addonModel = Addon::find($addonItem['addon_id']);
                                        if (!$addonModel) continue;
                                        
                                        $name = $addonModel->name;
                                        $qty = $addonItem['quantity'] ?? 1;
                                        $price = $addonItem['price'] ?? 0;
                                        $rowTotal = $qty * $price;
                                        $addonAmount += $rowTotal;
                                        
                                        if ($name === 'Extra Hour') {
                                            $extraHours += $qty;
                                        }
                                        
                                        $addonsHtml .= "
                                            <div class='flex justify-between items-center text-gray-500 dark:text-gray-400 text-xs ml-4 mt-1 w-full'>
                                                <div>-- {$name} ({$qty} X " . number_format($price, 2) . ")</div>
                                                <div>" . number_format($rowTotal, 2) . "</div>
                                            </div>
                                        ";
                                    }
                                }
                                
                                // Base hours is total duration minus the extra hours from addons
                                // Calculate base slots
                                $baseSlots = ($totalDuration * 60) / $slotDuration;
                                // Subtract extra slots (assuming extra hour addon adds 1 slot)
                                // If extra hour addon is literally 1 hour, we need to adjust.
                                // Assuming 'Extra Hour' adds 1 unit of time which is 1 slot? 
                                // Or is it strictly 1 hour? The previous logic treated it as 1 unit.
                                // Let's assume 1 unit = 1 slot for simplicity in this context unless specified otherwise.
                                // But wait, the addon name is "Extra Hour". 
                                // If slot duration is 30 mins, does "Extra Hour" mean 2 slots or 1 slot?
                                // In StudioBooking.php we treated it as slots. Let's stick to that for consistency.
                                
                                $baseSlots = max(1, ($totalDuration * 60 / $slotDuration) - $extraHours);
                                $baseAmount = ($hourlyRate * ($slotDuration / 60)) * $baseSlots;
                                
                                $totalAmount = $baseAmount + $addonAmount;
                                
                                return new \Illuminate\Support\HtmlString("
                                    <div class='bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-4 rounded-lg border border-gray-200 dark:border-gray-700 text-sm font-mono'>
                                        <div class='flex justify-between items-start mb-2'>
                                            <div>
                                                <div class='font-bold text-base'>{$studioName}</div>
                                                <div class='text-gray-500 dark:text-gray-400 text-xs'>{$timeRange}</div>
                                            </div>
                                            <div class='font-medium'>" . number_format($baseAmount, 2) . "</div>
                                        </div>
                                        
                                        {$addonsHtml}
                                        
                                        <div class='border-t border-gray-200 dark:border-gray-700 my-3'></div>
                                        
                                        <div class='flex justify-between items-center font-bold text-base'>
                                            <div>Total Hours: {$totalDuration}</div>
                                            <div>" . number_format($totalAmount, 2) . "</div>
                                        </div>
                                    </div>
                                ");
                            })
                            ->key(fn (Forms\Get $get) => 'summary-' . $get('end_time') . '-' . $get('total_amount') . '-' . json_encode($get('booking_addons'))),
                        Forms\Components\Hidden::make('total_hours')
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Addons')
                    ->columnSpan('full')
                    ->schema([
                        Forms\Components\Repeater::make('booking_addons')
                            ->label('Add-ons')
                            ->schema([
                                Forms\Components\Select::make('addon_id')
                                    ->label('Addon')
                                    ->options(Addon::where('is_active', true)->get()->mapWithKeys(fn($addon) => [$addon->id => $addon->name ?? 'Unknown'])->toArray())
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $addon = Addon::find($state);
                                            if ($addon) {
                                                $set('price', $addon->price);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->reactive(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix(\App\Models\Setting::get('currency_symbol', '₹'))
                                    ->suffix(\App\Models\Setting::get('currency_code', 'INR'))
                                    ->required()
                                    ->reactive(),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->defaultItems(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $addonAmount = 0;
                                $extraHours = 0;
                                $hasExtraHour = false;
                                
                                if (is_array($state)) {
                                    foreach ($state as $addonItem) {
                                        $addonAmount += ($addonItem['price'] ?? 0) * ($addonItem['quantity'] ?? 1);
                                        
                                        if (!empty($addonItem['addon_id'])) {
                                            $addonModel = Addon::find($addonItem['addon_id']);
                                            if ($addonModel && $addonModel->name === 'Extra Hour') {
                                                $extraHours += ($addonItem['quantity'] ?? 1);
                                                $hasExtraHour = true;
                                            }
                                        }
                                    }
                                }
                                
                                // Check availability for extra hours
                                if ($hasExtraHour) {
                                    $startTime = $get('start_time');
                                    $date = $get('booking_date');
                                    $studioId = $get('studio_id');
                                    
                                    if ($startTime && $date && $studioId) {
                                        $start = \Carbon\Carbon::parse($startTime);
                                        $conflictFound = false;
                                        $conflictTime = '';
                                        
                                        // Check slots for the extra hours (starting from 1 hour after start time)
                                        for ($i = 1; $i <= $extraHours; $i++) {
                                            $checkTime = $start->copy()->addHours($i)->format('H:i:00');
                                            // Format checkTime to match DB format (H:i:s) or just H:i:00
                                            // TimeSlot stores as H:i:s usually.
                                            
                                            $isBooked = \App\Models\TimeSlot::where('studio_id', $studioId)
                                                ->where('date', $date)
                                                ->where('start_time', 'like', substr($checkTime, 0, 5) . '%') // Match H:i
                                                ->where('is_booked', true)
                                                ->exists();
                                                
                                            if ($isBooked) {
                                                $conflictFound = true;
                                                $conflictTime = \Carbon\Carbon::parse($checkTime)->format('g:i A');
                                                break;
                                            }
                                        }
                                        
                                        if ($conflictFound) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Slot Unavailable')
                                                ->body("The slot at {$conflictTime} is already booked. Cannot add extra hours.")
                                                ->danger()
                                                ->send();
                                                
                                            // Remove Extra Hour addon
                                            $newState = [];
                                            foreach ($state as $uuid => $item) {
                                                if (empty($item['addon_id'])) continue;
                                                $addonModel = Addon::find($item['addon_id']);
                                                if ($addonModel && $addonModel->name === 'Extra Hour') {
                                                    continue;
                                                }
                                                $newState[$uuid] = $item;
                                            }
                                            $set('booking_addons', $newState);
                                            return;
                                        }
                                    }
                                }
                                
                                $set('addon_amount', $addonAmount);
                                
                                // Update Total Hours and End Time
                                $baseHours = 1;
                                $totalHours = $baseHours + $extraHours;
                                $set('total_hours', $totalHours);
                                
                                $startTime = $get('start_time');
                                if ($startTime) {
                                    $endTime = \Carbon\Carbon::parse($startTime)->addHours($totalHours)->format('H:i');
                                    $set('end_time', $endTime);
                                }
                                
                                $set('total_amount', ($get('base_amount') ?? 0) + $addonAmount);
                            }),
                    ]),

                Forms\Components\Section::make('Payment & Status')
                    ->schema([
                        Forms\Components\Placeholder::make('invoice_link')
                            ->label('Invoice ID')
                            ->content(function ($record) {
                                if (!$record || !$record->invoice) {
                                    return 'No Invoice Generated';
                                }
                                return new \Illuminate\Support\HtmlString(
                                    '<a href="' . InvoiceResource::getUrl('view', ['record' => $record->invoice]) . '" class="text-primary-600 hover:underline" style="color: #e9ab00;">' . $record->invoice->invoice_number . '</a>'
                                );
                            }),
                        Forms\Components\Placeholder::make('base_amount_display')
                            ->label('Base Amount')
                            ->content(function (Forms\Get $get) {
                                $studioId = $get('studio_id');
                                $hourlyRate = 0;
                                if ($studioId) {
                                    $studio = Studio::find($studioId);
                                    if ($studio) $hourlyRate = $studio->hourly_rate;
                                }
                                
                                $start = $get('start_time');
                                $end = $get('end_time');
                                $baseSlots = 1;
                                $slotDuration = 60;
                                if ($studioId) {
                                    $studio = Studio::find($studioId);
                                    if ($studio) $slotDuration = $studio->slot_duration ?? 60;
                                }
                                
                                if ($start && $end) {
                                    $startTime = \Carbon\Carbon::parse($start);
                                    $endTime = \Carbon\Carbon::parse($end);
                                    $totalDuration = $startTime->floatDiffInHours($endTime);
                                    
                                    $extraHours = 0;
                                    $addons = $get('booking_addons') ?? [];
                                    if (is_array($addons)) {
                                        foreach ($addons as $item) {
                                            if (!empty($item['addon_id'])) {
                                                $addonModel = Addon::find($item['addon_id']);
                                                if ($addonModel && $addonModel->name === 'Extra Hour') {
                                                    $extraHours += ($item['quantity'] ?? 1);
                                                }
                                            }
                                        }
                                    }
                                    
                                    $baseSlots = max(1, ($totalDuration * 60 / $slotDuration) - $extraHours);
                                }
                                
                                $amount = ($hourlyRate * ($slotDuration / 60)) * $baseSlots;
                                return \App\Models\Setting::get('currency_symbol', '₹') . number_format($amount, 2) . ' ' . \App\Models\Setting::get('currency_code', 'INR');
                            }),
                        Forms\Components\Placeholder::make('addon_amount_display')
                            ->label('Add-ons Amount')
                            ->content(function (Forms\Get $get) {
                                $addons = $get('booking_addons') ?? [];
                                $total = 0;
                                if (is_array($addons)) {
                                    foreach ($addons as $item) {
                                        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                                    }
                                }
                                return \App\Models\Setting::get('currency_symbol', '₹') . number_format($total, 2) . ' ' . \App\Models\Setting::get('currency_code', 'INR');
                            }),
                        Forms\Components\Placeholder::make('total_amount_display')
                            ->label('Total Amount')
                            ->content(function (Forms\Get $get) {
                                // Calculate Base
                                $studioId = $get('studio_id');
                                $hourlyRate = 0;
                                $slotDuration = 60;
                                if ($studioId) {
                                    $studio = Studio::find($studioId);
                                    if ($studio) {
                                        $hourlyRate = $studio->hourly_rate;
                                        $slotDuration = $studio->slot_duration ?? 60;
                                    }
                                }
                                
                                $start = $get('start_time');
                                $end = $get('end_time');
                                $baseSlots = 1;
                                
                                if ($start && $end) {
                                    $startTime = \Carbon\Carbon::parse($start);
                                    $endTime = \Carbon\Carbon::parse($end);
                                    $totalDuration = $startTime->floatDiffInHours($endTime);
                                    
                                    $extraHours = 0;
                                    $addons = $get('booking_addons') ?? [];
                                    if (is_array($addons)) {
                                        foreach ($addons as $item) {
                                            if (!empty($item['addon_id'])) {
                                                $addonModel = Addon::find($item['addon_id']);
                                                if ($addonModel && $addonModel->name === 'Extra Hour') {
                                                    $extraHours += ($item['quantity'] ?? 1);
                                                }
                                            }
                                        }
                                    }
                                    
                                    $baseSlots = max(1, ($totalDuration * 60 / $slotDuration) - $extraHours);
                                }
                                $baseAmount = ($hourlyRate * ($slotDuration / 60)) * $baseSlots;
                                
                                // Calculate Addons
                                $addonAmount = 0;
                                $addons = $get('booking_addons') ?? [];
                                if (is_array($addons)) {
                                    foreach ($addons as $item) {
                                        $addonAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                                    }
                                }
                                
                                return \App\Models\Setting::get('currency_symbol', '₹') . number_format($baseAmount + $addonAmount, 2) . ' ' . \App\Models\Setting::get('currency_code', 'INR');
                            }),
                        Forms\Components\Hidden::make('base_amount')
                            ->default(0),
                        Forms\Components\Hidden::make('addon_amount')
                            ->default(0),
                        Forms\Components\Hidden::make('total_amount')
                            ->default(0),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'upi' => 'UPI',
                                'card' => 'Card',
                                'bank_transfer' => 'Bank Transfer',
                                'online' => 'Online Payment',
                                'pay_at_studio' => 'Pay at Studio',
                            ])
                            ->required()
                            ->default('pay_at_studio'),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Select::make('booking_status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->maxLength(65535),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->url(fn (Booking $record) => BookingResource::getUrl('view', ['record' => $record])),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('Customer Phone')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('studio.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('total_hours')
                    ->suffix(' hrs')
                    ->formatStateUsing(fn ($state) => abs($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(\App\Models\Setting::get('currency_code', 'INR'))
                    ->suffix(' ' . \App\Models\Setting::get('currency_code', 'INR'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ]),
                Tables\Columns\BadgeColumn::make('booking_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'info' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('booking_status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('booking_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('booking_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Booking $record) => 
                        $record->payment_status !== 'paid' && 
                        $record->payment_status !== 'refunded' && 
                        $record->booking_status !== 'cancelled'
                    )
                    ->form([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => 'Cash',
                                'upi' => 'UPI',
                                'card' => 'Card',
                                'bank_transfer' => 'Bank Transfer',
                            ])
                            ->required()
                            ->default('upi'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                    ])
                    ->action(function (Booking $record, array $data) {
                        // 1. Update Booking
                        $record->update([
                            'payment_status' => 'paid',
                            'booking_status' => 'confirmed',
                            'payment_method' => $data['payment_method'],
                        ]);

                        // 2. Create/Update Payment Record
                        // 2. Create/Update Payment Record
                        $existingPayment = \App\Models\Payment::where('booking_id', $record->id)
                            ->where('status', 'pending')
                            ->first();

                        if ($existingPayment) {
                            $existingPayment->update([
                                'amount' => $record->total_amount, // Ensure amount matches
                                'payment_method' => $data['payment_method'],
                                'transaction_id' => $data['transaction_id'],
                                'status' => 'success',
                                'payment_date' => now(),
                                'notes' => $data['notes'] ?? null,
                            ]);
                        } else {
                            \App\Models\Payment::create([
                                'booking_id' => $record->id,
                                'user_id' => $record->user_id,
                                'amount' => $record->total_amount,
                                'payment_method' => $data['payment_method'],
                                'transaction_id' => $data['transaction_id'],
                                'status' => 'success',
                                'payment_date' => now(),
                                'notes' => $data['notes'] ?? null,
                            ]);
                        }

                        // 3. Update Invoice
                        $invoice = \App\Models\Invoice::where('booking_id', $record->id)->first();
                        if ($invoice) {
                            $invoice->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                                'payment_method' => $data['payment_method'],
                                'transaction_id' => $data['transaction_id'],
                            ]);
                        } else {
                            // Create invoice if it doesn't exist
                            \App\Models\Invoice::create([
                                'booking_id' => $record->id,
                                'user_id' => $record->user_id,
                                'invoice_number' => 'INV-' . str_pad($record->id, 6, '0', STR_PAD_LEFT),
                                'total_amount' => $record->total_amount,
                                'status' => 'paid',
                                'issue_date' => now(),
                                'due_date' => now(),
                                'paid_at' => now(),
                                'payment_method' => $data['payment_method'],
                                'transaction_id' => $data['transaction_id'],
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Booking Marked as Paid')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Mark Booking as Paid')
                    ->modalDescription('Please enter the transaction details to confirm payment.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
