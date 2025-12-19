<div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px;">
    @forelse($slots as $slot)
        @php
            $isMyBooking = isset($currentBookingId) && $slot->booking_id == $currentBookingId;
            $isBooked = $slot->is_booked && (!$slot->booking || $slot->booking->booking_status !== 'cancelled') && !$isMyBooking;
            $isSelected = $getState() == $slot->start_time;
            $timeDisplay = \Carbon\Carbon::parse($slot->start_time)->format('g:i A');
        @endphp

        <button
            type="button"
            wire:click="$set('{{ $getStatePath() }}', '{{ $slot->start_time }}')"
            @if($isBooked) disabled @endif
            class="
                px-2 py-1.5 text-xs font-medium rounded border shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800
                {{ $isBooked 
                    ? 'bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 cursor-not-allowed border-gray-200 dark:border-gray-800' 
                    : ($isSelected 
                        ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-indigo-600 dark:border-indigo-400 ring-1 ring-indigo-600 dark:ring-indigo-400' 
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700') 
                }}
            "
        >
            {{ $timeDisplay }}
            @if($isBooked)
                <span class="block text-[10px] font-normal">(Booked)</span>
            @endif
        </button>
    @empty
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-4">
            @if(!$studioId || !$bookingDate)
                Please select a studio and date first.
            @else
                No slots available for this date.
            @endif
        </div>
    @endforelse
</div>
