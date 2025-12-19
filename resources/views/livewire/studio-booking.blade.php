<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('studios.index') }}" class="text-black dark:text-white hover:text-gray-700 dark:hover:text-gray-300 flex items-center mb-4 transition-colors">
                    ← Back to Resources
                </a>
                <h1 class="text-3xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">Book {{ $studio->name }}</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300">Complete your booking in 3 easy steps</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center {{ $currentStep >= 1 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold {{ $currentStep >= 1 ? 'border-primary-600 dark:border-primary-400 bg-primary-50 dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600' }}">
                            1
                        </div>
                        <span class="ml-2 text-sm font-medium hidden sm:block">Date & Time</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $currentStep >= 2 ? 'bg-primary-600 dark:bg-primary-400' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center {{ $currentStep >= 2 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold {{ $currentStep >= 2 ? 'border-primary-600 dark:border-primary-400 bg-primary-50 dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600' }}">
                            2
                        </div>
                        <span class="ml-2 text-sm font-medium hidden sm:block">Add-ons</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 {{ $currentStep >= 3 ? 'bg-primary-600 dark:bg-primary-400' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center {{ $currentStep >= 3 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-semibold {{ $currentStep >= 3 ? 'border-primary-600 dark:border-primary-400 bg-primary-50 dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600' }}">
                            3
                        </div>
                        <span class="ml-2 text-sm font-medium hidden sm:block">Confirm</span>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                <!-- Step 1: Date & Time Selection -->
                @if ($currentStep === 1)
                    <div class="p-6">
                        <h2 class="text-xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-6">Select Date & Time</h2>
                        
                        <!-- Duration Selection -->
                        @if(count($availableDurations) > 1)
                            <div class="mb-6">
                                <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Duration</label>
                                <select 
                                    wire:model.live="selectedDuration" 
                                    id="duration"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                >
                                    @foreach($availableDurations as $duration)
                                        <option value="{{ $duration['duration'] }}">{{ $duration['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Date Picker -->
                        <div class="mb-6">
                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Booking Date</label>
                            <input 
                                type="date" 
                                wire:model.live="selectedDate" 
                                id="date"
                                min="{{ now()->format('Y-m-d') }}"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                        </div>

                        <!-- Available Time Slots -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Available Time Slots</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @forelse($availableSlots as $slot)
                                    @php
                                        $isBooked = in_array($slot->id, $bookedSlotIds);
                                        $slotEndTime = \Carbon\Carbon::parse($slot->start_time)->addMinutes((int) $selectedDuration)->format('g:i A');
                                    @endphp
                                    <button 
                                        wire:key="slot-{{ $slot->id }}"
                                        @if(!$isBooked)
                                            wire:click="selectTimeSlot('{{ $slot->start_time }}')"
                                        @endif
                                        @if($isBooked) disabled @endif
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-wait"
                                        class="px-3 py-2 border rounded-lg text-sm font-medium transition-all duration-300 relative
                                        {{ $isBooked ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-600 cursor-not-allowed' : 
                                            ($selectedStartTime === $slot->start_time ? 'bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30 border-transparent' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:border-primary-500 dark:hover:border-primary-400 hover:shadow-md hover:shadow-black/20') }}"
                                    >
                                        <span wire:loading.remove wire:target="selectTimeSlot('{{ $slot->start_time }}')">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ $slotEndTime }}
                                        </span>
                                        <span wire:loading wire:target="selectTimeSlot('{{ $slot->start_time }}')">
                                            ...
                                        </span>
                                        @if($isBooked) <span class="block text-xs font-normal">(Booked)</span> @endif
                                    </button>
                                @empty
                                    <div class="col-span-full text-center py-8 text-gray-500">
                                        No available slots for this date
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        @if($selectedStartTime)
                            <!-- Selected Slot Info -->
                            <div class="bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 rounded-lg p-4 mb-4">
                                <p class="text-sm font-medium text-primary-900 dark:text-primary-300">Selected Time:</p>
                                <p class="text-lg font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">
                                    {{ \Carbon\Carbon::parse($selectedStartTime)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($selectedEndTime)->format('g:i A') }}
                                    ({{ $durationString }})
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Step 2: Add-ons -->
                @if ($currentStep === 2)
                    <div class="p-6">
                        <h2 class="text-xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-6">Add-ons & Services</h2>
                        
                        @if($allAddons->count() > 0)
                            <div class="space-y-4">
                                @foreach($allAddons as $addon)
                                    <div class="border rounded-lg p-4 {{ isset($selectedAddons[$addon->id]) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30' : 'border-gray-200 dark:border-gray-700' }}">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-start flex-1">
                                                <input 
                                                    type="checkbox" 
                                                    wire:click="toggleAddon({{ $addon->id }})"
                                                    {{ isset($selectedAddons[$addon->id]) ? 'checked' : '' }}
                                                    class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                                >
                                                <div class="ml-3 flex-1">
                                                    <label class="font-medium text-gray-900 dark:text-white">{{ $addon->name }}</label>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $addon->description }}</p>
                                                    <p class="text-lg font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mt-1">₹{{ number_format($addon->price) }}</p>
                                                </div>
                                            </div>
                                            
                                            @if(isset($selectedAddons[$addon->id]))
                                                <div class="ml-4">
                                                    <label class="text-xs text-gray-600 dark:text-gray-400">Quantity</label>
                                                    <input 
                                                        type="number" 
                                                        wire:model.live="selectedAddons.{{ $addon->id }}.quantity"
                                                        min="1"
                                                        class="block w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-center bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                    >
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">No add-ons available</p>
                        @endif
                    </div>
                @endif

                <!-- Step 3: Confirmation -->
                @if ($currentStep === 3)
                    <div class="p-6">
                        <h2 class="text-xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent mb-6">Confirm Booking</h2>
                        
                        <!-- Booking Summary -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Booking Summary</h3>
                            
                            @php
                                $extraHours = 0;
                                foreach($selectedAddons as $addon) {
                                    if($addon['name'] === 'Extra Hour') {
                                        $extraHours += $addon['quantity'];
                                    }
                                }
                                
                                $slotDuration = $studio->slot_duration ?? 60;
                                // Base duration from selected slots + Extra Hours (60 mins each)
                                $totalMinutes = ($selectedSlotCount * $slotDuration) + ($extraHours * 60);
                                
                                $hours = floor($totalMinutes / 60);
                                $mins = $totalMinutes % 60;
                                $displayDuration = $hours . ' Hour' . ($hours != 1 ? 's' : '');
                                if ($mins > 0) {
                                    $displayDuration .= ' ' . $mins . ' Minutes';
                                }
                                
                                $displayEndTime = \Carbon\Carbon::parse($selectedEndTime)->addMinutes($extraHours * 60)->format('g:i A');
                            @endphp

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Resource:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $studio->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Time:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($selectedStartTime)->format('g:i A') }} - 
                                        {{ $displayEndTime }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $displayDuration }}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-300 dark:border-gray-600 mt-4 pt-4">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600 dark:text-gray-400">Base Amount:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">₹{{ number_format($baseAmount) }}</span>
                                </div>
                                
                                @if(count($selectedAddons) > 0)
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Add-ons:</div>
                                    @foreach($selectedAddons as $addon)
                                        <div class="flex justify-between text-sm ml-4 mb-1">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $addon['name'] }} (×{{ $addon['quantity'] }})</span>
                                            <span class="text-gray-900 dark:text-white">₹{{ number_format($addon['price'] * $addon['quantity']) }}</span>
                                        </div>
                                    @endforeach
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Add-ons Total:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">₹{{ number_format($addonAmount) }}</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-300 dark:border-gray-600 mt-3 pt-3">
                                    <div class="flex justify-between text-lg">
                                        <span class="font-bold text-gray-900 dark:text-white">Total Amount:</span>
                                        <span class="font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">₹{{ number_format($totalAmount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Payment Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer {{ $paymentMethod === 'pay_at_studio' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input 
                                        type="radio" 
                                        wire:model="paymentMethod" 
                                        value="pay_at_studio"
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500"
                                    >
                                    <span class="ml-3 font-medium text-gray-900 dark:text-white">Pay at Studio</span>
                                </label>
                                
                                @if($hasOnlinePayment)
                                    <label class="flex items-center p-4 border rounded-lg cursor-pointer {{ $paymentMethod === 'online' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30' : 'border-gray-300 dark:border-gray-600' }}">
                                        <input 
                                            type="radio" 
                                            wire:model="paymentMethod" 
                                            value="online"
                                            class="h-4 w-4 text-primary-600 focus:ring-primary-500"
                                        >
                                        <span class="ml-3 font-medium text-gray-900 dark:text-white">Online Payment</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional Notes (Optional)</label>
                            <textarea 
                                wire:model="notes" 
                                id="notes"
                                rows="3"
                                class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                placeholder="Any special requirements or notes..."
                            ></textarea>
                        </div>
                    </div>
                @endif

                <!-- Navigation Buttons -->
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-between border-t border-gray-200 dark:border-gray-700">
                    @if($currentStep > 1)
                        <button 
                            wire:click="previousStep"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-200 font-medium hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="previousStep">Previous</span>
                            <span wire:loading wire:target="previousStep">Loading...</span>
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($currentStep < 3)
                        <button 
                            wire:click="nextStep"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] transition-all duration-300 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 border-none"
                        >
                            <span wire:loading.remove wire:target="nextStep">Next</span>
                            <span wire:loading wire:target="nextStep">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    @else
                        <button 
                            wire:click="submitBooking"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] transition-all duration-300 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 border-none"
                        >
                            <span wire:loading.remove wire:target="submitBooking">Confirm Booking</span>
                            <span wire:loading wire:target="submitBooking">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
</div>

@script
<script>
    $wire.on('scroll-to-top', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
@endscript
