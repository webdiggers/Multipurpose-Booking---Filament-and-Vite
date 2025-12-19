<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Studio;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\Payment;
use App\Models\Invoice;
use Carbon\Carbon;

class StudioBooking extends Component
{
    public $studio;
    public $selectedDate;
    public $selectedStartTime;
    public $selectedEndTime;
    public $selectedSlotCount = 1;
    public $durationString = '';
    public $availableSlots = [];
    public $bookedSlotIds = []; // Track booked slots separately
    public $selectedAddons = [];
    public $paymentMethod = 'pay_at_studio';
    public $notes = '';
    public $hasOnlinePayment = false;

    public $selectedDuration; // Duration in minutes
    public $availableDurations = [];

    // Pricing
    public $baseAmount = 0;
    public $addonAmount = 0;
    public $totalAmount = 0;

    // Steps
    public $currentStep = 1;

    public function mount($studio)
    {
        $this->studio = Studio::findOrFail($studio);
        $this->selectedDate = now()->setTimezone('Asia/Kolkata')->format('Y-m-d'); // Start with today in IST
        
        // Initialize durations
        if (!empty($this->studio->allowed_durations)) {
            $this->availableDurations = $this->studio->allowed_durations;
            // Sort by duration
            usort($this->availableDurations, function($a, $b) {
                return $a['duration'] <=> $b['duration'];
            });
            $this->selectedDuration = $this->availableDurations[0]['duration'];
        } else {
            // Fallback to slot_duration
            $duration = $this->studio->slot_duration ?? 60;
            $this->availableDurations = [
                ['duration' => $duration, 'label' => $this->formatDuration($duration)]
            ];
            $this->selectedDuration = $duration;
        }

        $this->loadAvailableSlots();
        $this->hasOnlinePayment = \App\Models\Setting::get('enable_paypal') || \App\Models\Setting::get('enable_phonepe');
    }

    private function formatDuration($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' Minutes';
        } elseif ($minutes == 60) {
            return '1 Hour';
        } else {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            return $hours . ' Hour' . ($hours > 1 ? 's' : '') . ($mins > 0 ? ' ' . $mins . ' Minutes' : '');
        }
    }

    public function updatedSelectedDuration()
    {
        $this->selectedStartTime = null;
        $this->selectedEndTime = null;
        $this->loadAvailableSlots();
    }

    public function updatedSelectedDate()
    {
        $this->loadAvailableSlots();
        $this->selectedStartTime = null;
        $this->selectedEndTime = null;
    }

    public function loadAvailableSlots()
    {
        // Check for duration mismatch and regenerate if safe
        $existingSlots = TimeSlot::where('studio_id', $this->studio->id)
            ->where('date', $this->selectedDate)
            ->get();

        if ($existingSlots->isNotEmpty()) {
            $firstSlot = $existingSlots->first();
            $slotDuration = $this->studio->slot_duration ?? 60;
            $existingDuration = \Carbon\Carbon::parse($firstSlot->end_time)
                ->diffInMinutes(\Carbon\Carbon::parse($firstSlot->start_time));

            // Allow 1 min tolerance for calculation differences
            if (abs($existingDuration - $slotDuration) > 1) {
                // Check if any are booked
                $hasBookings = $existingSlots->where('is_booked', true)->isNotEmpty();
                
                if (!$hasBookings) {
                    // Safe to delete and regenerate
                    TimeSlot::where('studio_id', $this->studio->id)
                        ->where('date', $this->selectedDate)
                        ->delete();
                    
                    $this->generateTimeSlots();
                }
            }
        } else {
             // Generate if empty
             $this->generateTimeSlots();
        }

        // Get ALL time slots for selected date
        $allSlots = TimeSlot::where('studio_id', $this->studio->id)
            ->where('date', $this->selectedDate)
            ->with('booking')
            ->orderBy('start_time')
            ->get()
            ->values(); // Ensure sequential keys

        $now = now()->setTimezone('Asia/Kolkata');
        
        // Ensure selectedDuration is set
        if (!$this->selectedDuration) {
            $this->selectedDuration = $this->studio->slot_duration ?? 60;
        }
        
        // Filter slots based on selected duration
        $validStartSlots = [];
        $requiredDuration = (int) $this->selectedDuration;
        
        foreach ($allSlots as $index => $slot) {
            // Skip past slots if today
            if ($this->selectedDate === $now->format('Y-m-d')) {
                if ($slot->start_time <= $now->format('H:i:s')) {
                    continue;
                }
            }

            // Check if this slot can be a start slot for the selected duration
            // We need to check if we have enough consecutive free slots to cover the duration
            
            $currentDuration = 0;
            $isValid = true;
            
            // Look ahead
            for ($i = $index; $i < count($allSlots); $i++) {
                $checkSlot = $allSlots[$i];
                
                // Check if booked
                if ($checkSlot->is_booked && ($checkSlot->booking && $checkSlot->booking->booking_status !== 'cancelled')) {
                    $isValid = false;
                    break;
                }
                
                // Check continuity (gap check)
                if ($i > $index) {
                    $prevSlot = $allSlots[$i-1];
                    if ($prevSlot->end_time !== $checkSlot->start_time) {
                        $isValid = false; // Not continuous
                        break;
                    }
                }
                
                $startTime = \Carbon\Carbon::parse($checkSlot->start_time);
                $endTime = \Carbon\Carbon::parse($checkSlot->end_time);
                
                // Handle overnight slots if necessary (end < start)
                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                
                $slotLen = $startTime->diffInMinutes($endTime);
                $currentDuration += $slotLen;
                
                if ($currentDuration >= $requiredDuration) {
                    break;
                }
            }
            
            if ($isValid && $currentDuration >= $requiredDuration) {
                $validStartSlots[] = $slot;
            }
        }
        
        $this->availableSlots = collect($validStartSlots);
            
        // Populate booked slot IDs - EXCLUDE cancelled bookings
        // For the purpose of the UI, we just show available slots.
        // But if we want to show "Booked" slots, we need to include them?
        // The user requirement says "slots will be visbel according to the slot duration time".
        // This implies we only show AVAILABLE options.
        // So `availableSlots` should only contain valid options.
        // I will stick to returning only valid start slots.
        
        $this->bookedSlotIds = []; // Not really needed if we only show available ones, but keeping for compatibility
    }

    private function generateTimeSlots()
    {
        $date = $this->selectedDate;
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));
        
        // Get operating hours for the day
        $operatingHours = $this->studio->operating_hours;
        $dayConfig = null;
        
        if ($operatingHours && is_array($operatingHours)) {
            foreach ($operatingHours as $config) {
                if (isset($config['day']) && strtolower($config['day']) === $dayOfWeek) {
                    $dayConfig = $config;
                    break;
                }
            }
        }
        
        // Default if not configured or day not found
        if (!$dayConfig) {
            $dayConfig = [
                'enabled' => true,
                'start' => '09:00',
                'end' => '22:00'
            ];
        }
        
        // Check if day is enabled
        if (isset($dayConfig['enabled']) && !$dayConfig['enabled']) {
            return; // No slots for this day
        }
        
        $startHour = isset($dayConfig['start']) ? Carbon::parse($dayConfig['start'])->hour : 9;
        $endHour = isset($dayConfig['end']) ? Carbon::parse($dayConfig['end'])->hour : 22;
        
        // Adjust for minutes if needed, but simple loop uses hours. 
        // For custom duration, we need to loop by minutes.
        $startTime = Carbon::parse($date . ' ' . ($dayConfig['start'] ?? '09:00'));
        $endTime = Carbon::parse($date . ' ' . ($dayConfig['end'] ?? '22:00'));
        
        $slotDuration = $this->studio->slot_duration ?? 60; // Default 60 mins
        
        $currentSlot = $startTime->copy();
        $now = now()->setTimezone('Asia/Kolkata');
        $isToday = $date === $now->format('Y-m-d');

        while ($currentSlot->copy()->addMinutes($slotDuration)->lte($endTime)) {
            $slotEnd = $currentSlot->copy()->addMinutes($slotDuration);
            
            // If slot end is strictly after closing time, stop (though lte check above handles this)
            if ($slotEnd->gt($endTime)) {
                break;
            }
            
            $slotStartStr = $currentSlot->format('H:i:s');
            $slotEndStr = $slotEnd->format('H:i:s');
            
            // Skip past time slots if it's today
            if ($isToday) {
                // Compare with current time in IST
                if ($currentSlot->format('H:i:s') <= $now->format('H:i:s')) {
                    $currentSlot->addMinutes($slotDuration);
                    continue; 
                }
            }
            
            TimeSlot::firstOrCreate([
                'studio_id' => $this->studio->id,
                'date' => $date,
                'start_time' => $slotStartStr,
            ], [
                'end_time' => $slotEndStr,
                'is_booked' => false,
            ]);
            
            $currentSlot->addMinutes($slotDuration);
        }
    }

    public function selectTimeSlot($startTime)
    {
        \Log::info('Selecting slot', [
            'studio_id' => $this->studio->id,
            'date' => $this->selectedDate,
            'start_time' => $startTime,
            'duration' => $this->selectedDuration,
        ]);

        // Calculate End Time based on duration
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = $start->copy()->addMinutes($this->selectedDuration);
        $endTime = $end->format('H:i:s');

        // Check if this slot is already booked (double check)
        // We already filtered available slots, but good to check again
        // Logic similar to checkExtraHourAvailability or just rely on UI?
        // Let's rely on UI filtering for now, but maybe add a check if needed.

        $this->selectedStartTime = $startTime;
        $this->selectedEndTime = $endTime;
        
        $slotDuration = $this->studio->slot_duration ?? 60;
        
        // Calculate number of slots
        $this->selectedSlotCount = ceil($this->selectedDuration / $slotDuration);
        
        // Generate duration string
        $this->durationString = $this->formatDuration($this->selectedDuration);
        
        $this->calculatePricing();
    }

    public function toggleAddon($addonId)
    {
        if (isset($this->selectedAddons[$addonId])) {
            unset($this->selectedAddons[$addonId]);
        } else {
            $addon = Addon::find($addonId);
            
            // Check if it's Extra Hour and if next slot is available
            if ($addon->name === 'Extra Hour' && $this->selectedEndTime) {
                if (!$this->checkExtraHourAvailability(1)) {
                    return; // Stop if not available
                }
            }

            $this->selectedAddons[$addonId] = [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => $addon->price,
                'quantity' => 1,
            ];
        }
        $this->calculatePricing();
    }

    public function updatedSelectedAddons()
    {
        $this->calculatePricing();
    }

    public function updateAddonQuantity($addonId, $quantity)
    {
        if (isset($this->selectedAddons[$addonId]) && $quantity > 0) {
            // Check if it's Extra Hour
            if ($this->selectedAddons[$addonId]['name'] === 'Extra Hour' && $this->selectedEndTime) {
                if (!$this->checkExtraHourAvailability($quantity)) {
                    return; // Stop if not available
                }
            }

            $this->selectedAddons[$addonId]['quantity'] = $quantity;
            $this->calculatePricing();
        }
    }

    private function checkExtraHourAvailability($quantity)
    {
        if (!$this->selectedEndTime) return true;

        $endTime = Carbon::createFromFormat('H:i:s', $this->selectedEndTime);
        $checkEndTime = $endTime->copy()->addHours($quantity); // Adds 60 mins * quantity
        
        // Check Booking Table for conflicts
        $conflict = Booking::where('studio_id', $this->studio->id)
            ->where('booking_date', $this->selectedDate)
            ->where('booking_status', '!=', 'cancelled')
            ->where(function($query) use ($endTime, $checkEndTime) {
                $startStr = $endTime->format('H:i:s');
                $endStr = $checkEndTime->format('H:i:s');
                
                $query->where('start_time', '<', $endStr)
                      ->where('end_time', '>', $startStr);
            })
            ->exists();

        if ($conflict) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "The slot for extra hour is conflicting with an existing booking."
            ]);
            session()->flash('error', "The slot for extra hour is conflicting with an existing booking.");
            return false;
        }

        // Also check TimeSlots for consistency
        $startStr = $endTime->format('H:i:s');
        $endStr = $checkEndTime->format('H:i:s');
        
        $conflictingSlot = TimeSlot::where('studio_id', $this->studio->id)
            ->where('date', $this->selectedDate)
            ->where('is_booked', true)
            ->where(function($query) use ($startStr, $endStr) {
                 $query->where('start_time', '<', $endStr)
                       ->where('end_time', '>', $startStr);
            })
            ->with('booking')
            ->get()
            ->filter(function($slot) {
                return !$slot->booking || $slot->booking->booking_status !== 'cancelled';
            })
            ->isNotEmpty();

        if ($conflictingSlot) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "The slot for extra time is unavailable."
            ]);
            return false;
        }
        
        return true;
    }

    public function calculatePricing()
    {
        // Base amount
        // Calculate duration in hours
        $hours = $this->selectedDuration / 60;
        
        $this->baseAmount = $this->studio->hourly_rate * $hours;

        // Addon amount
        $this->addonAmount = 0;
        foreach ($this->selectedAddons as $addon) {
            $this->addonAmount += $addon['price'] * $addon['quantity'];
        }

        // Total
        $this->totalAmount = $this->baseAmount + $this->addonAmount;
    }

    public function nextStep()
    {
        if ($this->currentStep === 1 && !$this->selectedStartTime) {
            session()->flash('error', 'Please select a time slot');
            return;
        }

        $this->currentStep++;
        $this->dispatch('scroll-to-top');
    }

    public function previousStep()
    {
        $this->currentStep--;
    }

    public function submitBooking()
    {
        // Validate
        if (!$this->selectedStartTime || !$this->selectedDate) {
            session()->flash('error', 'Please select date and time');
            return;
        }
        
        // Validate not booking in the past
        $bookingDateTime = Carbon::parse($this->selectedDate . ' ' . $this->selectedStartTime);
        if ($bookingDateTime->isPast()) {
            session()->flash('error', 'Cannot book in the past. Please select a future time slot.');
            return;
        }
        
        // Calculate extra hours from addons
            $extraHours = 0;
            foreach ($this->selectedAddons as $addon) {
                if ($addon['name'] === 'Extra Hour') {
                    $extraHours += $addon['quantity'];
                }
            }

            // Calculate final end time and total hours
            $start = Carbon::createFromFormat('H:i:s', $this->selectedStartTime);
            $baseEnd = Carbon::createFromFormat('H:i:s', $this->selectedEndTime);
            
            $slotDuration = $this->studio->slot_duration ?? 60;
            
            // Add extra hours (60 mins each) to base end time
            $finalEndTime = $baseEnd->copy()->addMinutes($extraHours * 60)->format('H:i:s');
            
            // Calculate total duration in minutes
            $baseDuration = abs($baseEnd->diffInMinutes($start));
            $totalDuration = $baseDuration + ($extraHours * 60);
            
            // Calculate total slots to mark as booked (ceil)
            // We need to mark all slots that are touched by this booking
            $totalBookingSlots = ceil($totalDuration / $slotDuration);
            
            // Calculate total hours for DB (can be fractional)
            $totalBookingHours = $totalDuration / 60;
        
        // Check for conflicting bookings in database
        $conflictingBooking = Booking::where('studio_id', $this->studio->id)
            ->where('booking_date', $this->selectedDate)
            ->where('booking_status', '!=', 'cancelled')
            ->where(function($query) use ($finalEndTime) {
                $query->where(function($q) {
                    // Selected time starts during an existing booking
                    $q->where('start_time', '<=', $this->selectedStartTime)
                      ->where('end_time', '>', $this->selectedStartTime);
                })->orWhere(function($q) use ($finalEndTime) {
                    // Selected time ends during an existing booking
                    $q->where('start_time', '<', $finalEndTime)
                      ->where('end_time', '>=', $finalEndTime);
                })->orWhere(function($q) use ($finalEndTime) {
                    // Selected time completely encompasses an existing booking
                    $q->where('start_time', '>=', $this->selectedStartTime)
                      ->where('end_time', '<=', $finalEndTime);
                });
            })
            ->first();
            
        if ($conflictingBooking) {
            $msg = 'This time slot conflicts with an existing booking.';
            if ($conflictingBooking->user_id === auth()->id() && $conflictingBooking->booking_status === 'pending') {
                $msg = 'You already have a pending booking for this time slot. Please check "My Bookings".';
            }
            session()->flash('error', $msg);
            return;
        }

        try {
            \DB::beginTransaction();

            // Create booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'studio_id' => $this->studio->id,
                'booking_date' => $this->selectedDate,
                'start_time' => $this->selectedStartTime,
                'end_time' => $finalEndTime, // Use final end time including extra hours
                'total_hours' => $totalBookingHours, // Use total hours including extra
                'base_amount' => $this->baseAmount,
                'addon_amount' => $this->addonAmount,
                'total_amount' => $this->totalAmount,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
                'booking_status' => 'pending', // Pending until paid/confirmed
                'created_by' => auth()->id(),
                'notes' => $this->notes,
            ]);

            // Attach addons
            foreach ($this->selectedAddons as $addon) {
                $booking->addons()->attach($addon['id'], [
                    'quantity' => $addon['quantity'],
                    'price' => $addon['price'],
                ]);
            }

            // Mark time slots as booked (including extra hours)
            for ($i = 0; $i < $totalBookingSlots; $i++) {
                $slotStart = $start->copy()->addMinutes($i * $slotDuration);
                $targetTime = $slotStart->format('H:i:s');
                
                TimeSlot::updateOrCreate(
                    [
                        'studio_id' => $this->studio->id,
                        'date' => $this->selectedDate,
                        'start_time' => $targetTime,
                    ],
                    [
                        'end_time' => $slotStart->copy()->addMinutes($slotDuration)->format('H:i:s'),
                        'is_booked' => true,
                        'booking_id' => $booking->id,
                    ]
                );
            }

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $this->totalAmount,
                'payment_method' => $this->paymentMethod,
                'status' => 'pending',
            ]);

            // Create invoice
            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'subtotal' => $this->totalAmount,
                'total_amount' => $this->totalAmount,
                'status' => 'pending',
                'generated_at' => now(),
            ]);

            \DB::commit();

            session()->flash('success', 'Booking created successfully!');
            
            if ($this->paymentMethod === 'online') {
                return redirect()->route('invoices.print', $booking->invoice);
            }
            
            return redirect()->route('my-bookings');

        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $allAddons = Addon::where('is_active', true)->get();

        return view('livewire.studio-booking', [
            'allAddons' => $allAddons,
        ])->layout('components.layouts.app');
    }
}
