<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">My Bookings</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-300">View and manage your studio bookings</p>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6 transition-colors duration-300">
                <div class="flex flex-wrap gap-2">
                    <button 
                        wire:click="$set('filter', 'all')"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 {{ $filter === 'all' ? 'bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600' }}"
                    >
                        All Bookings
                    </button>
                    <button 
                        wire:click="$set('filter', 'upcoming')"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 {{ $filter === 'upcoming' ? 'bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600' }}"
                    >
                        Upcoming
                    </button>
                    <button 
                        wire:click="$set('filter', 'past')"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 {{ $filter === 'past' ? 'bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600' }}"
                    >
                        Past
                    </button>
                    <button 
                        wire:click="$set('filter', 'cancelled')"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 {{ $filter === 'cancelled' ? 'bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white shadow-md shadow-black/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600' }}"
                    >
                        Cancelled
                    </button>
                </div>
            </div>

            <!-- Bookings List -->
            @if($bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                    <!-- Booking Info -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-primary-500 transition-colors">{{ $booking->studio->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Booking #{{ $booking->id }}</p>
                                            </div>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <!-- Booking Status Badge -->
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'confirmed' => 'bg-green-100 text-green-800',
                                                        'completed' => 'bg-blue-100 text-blue-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$booking->booking_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                    Booking: {{ ucfirst($booking->booking_status) }}
                                                </span>
                                                
                                                <!-- Payment Status Badge -->
                                                @php
                                                    $paymentColors = [
                                                        'pending' => 'bg-orange-100 text-orange-800',
                                                        'paid' => 'bg-green-100 text-green-800',
                                                        'failed' => 'bg-red-100 text-red-800',
                                                        'refunded' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                @endphp
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$booking->payment_status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                                    Payment: {{ ucfirst($booking->payment_status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Date</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Time</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Duration</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ $booking->total_hours }} {{ $booking->total_hours > 1 ? 'hours' : 'hour' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Payment Method</p>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</p>
                                            </div>
                                        </div>

                                        <!-- Add-ons -->
                                        @if($booking->addons->count() > 0)
                                            <div class="mb-4">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Add-ons:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($booking->addons as $addon)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300">
                                                            {{ $addon->name }} (×{{ $addon->pivot->quantity }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Notes -->
                                        @if($booking->notes)
                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Notes:</p>
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $booking->notes }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Pricing & Actions -->
                                    <div class="md:ml-6 mt-4 md:mt-0">
                                        <div class="bg-primary-50 dark:bg-primary-900/30 rounded-lg p-4 mb-4 min-w-[200px] border border-primary-200 dark:border-[#6d421e]">
                                            <p class="text-sm text-primary-900 dark:text-primary-300 mb-2">Total Amount</p>
                                            <p class="text-3xl font-bold bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 bg-clip-text text-transparent">₹{{ number_format($booking->total_amount) }}</p>
                                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                                <p>Base: ₹{{ number_format($booking->base_amount) }}</p>
                                                @if($booking->addon_amount > 0)
                                                    <p>Add-ons: ₹{{ number_format($booking->addon_amount) }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        @if($booking->booking_status !== 'cancelled' && $booking->booking_status !== 'completed')
                                            <button 
                                                wire:click="cancelBooking({{ $booking->id }})"
                                                wire:confirm="Are you sure you want to cancel this booking?"
                                                wire:loading.attr="disabled"
                                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 shadow-md shadow-black/20"
                                            >
                                                <span wire:loading.remove wire:target="cancelBooking({{ $booking->id }})">Cancel Booking</span>
                                                <span wire:loading wire:target="cancelBooking({{ $booking->id }})">
                                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Cancelling...
                                                </span>
                                            </button>
                                        @endif

                                        @if($booking->invoice)
                                            @if($booking->payment_status === 'paid')
                                                <a 
                                                    href="{{ route('invoices.print', $booking->invoice) }}" 
                                                    target="_blank"
                                                    class="block w-full mt-2 px-4 py-2 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-600 text-center transition-colors shadow-sm"
                                                >
                                                    View Invoice
                                                </a>
                                            @else
                                                <a 
                                                    href="{{ route('invoices.print', $booking->invoice) }}" 
                                                    target="_blank"
                                                    class="block w-full mt-2 px-4 py-2 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white rounded-lg font-medium shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] text-center transition-all duration-300 border-none"
                                                >
                                                    Pay Invoice
                                                </a>
                                            @endif
                                        @elseif($booking->booking_status !== 'cancelled')
                                            <button 
                                                wire:click="generateInvoice({{ $booking->id }})"
                                                wire:loading.attr="disabled"
                                                class="block w-full mt-2 px-4 py-2 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white rounded-lg font-medium shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] text-center transition-all duration-300 border-none flex items-center justify-center gap-2"
                                            >
                                                <span wire:loading.remove wire:target="generateInvoice({{ $booking->id }})">Generate Invoice</span>
                                                <span wire:loading wire:target="generateInvoice({{ $booking->id }})">
                                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center transition-colors duration-300">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No bookings found</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        @if($filter === 'upcoming')
                            You don't have any upcoming bookings
                        @elseif($filter === 'past')
                            You don't have any past bookings
                        @elseif($filter === 'cancelled')
                            You don't have any cancelled bookings
                        @else
                            You haven't made any bookings yet
                        @endif
                    </p>
                    <a 
                        href="{{ route('studios.index') }}" 
                        class="inline-block px-6 py-3 bg-gradient-to-br from-primary-300 via-primary-500 to-primary-600 text-white rounded-lg font-medium shadow-md shadow-black/30 hover:shadow-lg hover:shadow-black/40 hover:scale-[1.02] transition-all duration-300"
                    >
                        Browse Studios
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
