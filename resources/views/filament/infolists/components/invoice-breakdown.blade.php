@php
    $invoice = $getRecord();
    $booking = $invoice->booking;
@endphp

@if($booking)
    @php
        $studioName = $booking->studio->name ?? 'Unknown Studio';
        $start = $booking->start_time;
        $end = $booking->end_time;
        $timeRange = \Carbon\Carbon::parse($start)->format('g:i A') . " - " . \Carbon\Carbon::parse($end)->format('g:i A');
        $baseAmount = $booking->base_amount;
        $totalAmount = $invoice->total_amount;
        $totalHours = abs($booking->total_hours);
    @endphp

    <div class="w-full bg-gray-900 text-white p-4 rounded-lg border border-gray-700 text-sm font-mono">
        <div class="flex justify-between items-start mb-2">
            <div>
                <div class="font-bold text-base">{{ $studioName }}</div>
                <div class="text-gray-400 text-xs">{{ $timeRange }}</div>
            </div>
            <div class="font-medium">{{ number_format($baseAmount, 2) }}</div>
        </div>
        
        @if($booking->addons)
            @foreach($booking->addons as $addon)
                @php
                    $name = $addon->name;
                    $qty = $addon->pivot->quantity;
                    $price = $addon->pivot->price;
                    $rowTotal = $qty * $price;
                @endphp
                <div class="flex justify-between items-center text-gray-400 text-xs ml-4 mt-1">
                    <div>-- {{ $name }} ({{ $qty }} X {{ $price }})</div>
                    <div>{{ number_format($rowTotal, 2) }}</div>
                </div>
            @endforeach
        @endif
        
        <div class="border-t border-gray-700 my-3"></div>
        
        <div class="flex justify-between items-center font-bold text-base">
            <div>Total Hours: {{ $totalHours }}</div>
            <div>{{ number_format($totalAmount, 2) }}</div>
        </div>
    </div>
@else
    <div class="text-gray-500">No booking details available</div>
@endif
