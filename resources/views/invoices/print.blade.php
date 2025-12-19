<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    @php
        $primaryColor = \App\Models\Setting::get('light_primary_color', '#e9ab00');
        $palette = \Filament\Support\Colors\Color::hex($primaryColor);
    @endphp
    <style>
        :root {
            --primary-50: {{ $palette[50] }};
            --primary-100: {{ $palette[100] }};
            --primary-200: {{ $palette[200] }};
            --primary-500: {{ $palette[500] }};
            --primary-600: {{ $palette[600] }};
            --primary-700: {{ $palette[700] }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        .invoice-header {
            border-bottom: 3px solid rgb(var(--primary-500));
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            width: 100%;
        }
        
        .company-info table {
            width: 100%;
        }
        
        .company-info td {
            vertical-align: top;
        }
        
        .company-info .left {
            width: 60%;
        }
        
        .company-info .right {
            width: 40%;
            text-align: right;
        }
        
        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: rgb(var(--primary-700));
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 12px;
            color: #666;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-info {
            width: 100%;
            margin-bottom: 30px;
        }
        
        .invoice-info table {
            width: 100%;
        }
        
        .invoice-info td {
            width: 50%;
            padding: 0 15px;
            vertical-align: top;
        }
        
        .invoice-info td:first-child {
            padding-left: 0;
        }
        
        .invoice-info td:last-child {
            padding-right: 0;
        }
        
        .info-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: rgb(var(--primary-700));
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .info-section p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .invoice-table thead {
            background-color: rgb(var(--primary-50));
        }
        
        .invoice-table th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: rgb(var(--primary-700));
            border-bottom: 2px solid rgb(var(--primary-500));
        }
        
        .invoice-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin-left: auto;
            width: 300px;
            margin-top: 20px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        
        .totals-row.total {
            border-top: 2px solid #333;
            font-size: 18px;
            font-weight: bold;
            color: rgb(var(--primary-700));
            margin-top: 10px;
            padding-top: 15px;
        }
        
        .payment-info {
            background-color: rgb(var(--primary-50));
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            border: 1px solid rgb(var(--primary-200));
        }
        
        .payment-info h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: rgb(var(--primary-700));
        }
        
        .payment-info p {
            font-size: 13px;
            color: #666;
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .invoice-container {
                padding: 20px;
            }
            
            @page {
                margin: 1cm;
            }
        }
        
        .payment-actions {
            margin-top: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .pay-btn {
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: white;
            transition: opacity 0.2s;
        }
        
        .pay-btn:hover {
            opacity: 0.9;
        }
        
        .paypal-btn {
            background-color: #0070ba;
        }
        
        .phonepe-btn {
            background-color: #5f259f;
        }
        
        @media print {
            .payment-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <table>
                    <tr>
                        <td class="left">
                            @if($logo = App\Models\Setting::get('company_logo'))
                                <img src="{{ \Storage::url($logo) }}" alt="Logo" class="company-logo">
                            @else
                                <div class="company-name">{{ App\Models\Setting::get('company_name', 'Company Name') }}</div>
                            @endif
                            <div class="company-details">
                                Professional Recording Studio<br>
                                Phone: {{ App\Models\Setting::get('company_phone', '+91 XXX XXX XXXX') }}<br>
                                Email: {{ App\Models\Setting::get('company_email', 'info@company.com') }}
                            </div>
                        </td>
                        <td class="right">
                            <div class="invoice-title">
                                <h1>INVOICE</h1>
                                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                                <div class="invoice-number">{{ $invoice->created_at->format('F d, Y') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <table>
                <tr>
                    <td>
                        <div class="info-section">
                            <h3>Bill To</h3>
                            <p><strong>{{ $invoice->booking->user->name }}</strong></p>
                            <p>{{ $invoice->booking->user->phone }}</p>
                            @if($invoice->booking->user->email)
                                <p>{{ $invoice->booking->user->email }}</p>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="info-section">
                            <h3>Booking Details</h3>
                            <p><strong>Studio:</strong> {{ $invoice->booking->studio->name }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->booking->booking_date)->format('F d, Y') }}</p>
                            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($invoice->booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($invoice->booking->end_time)->format('h:i A') }}</p>
                            <p><strong>Duration:</strong> {{ abs($invoice->booking->total_hours) }} hours</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Quantity</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Studio Booking - {{ $invoice->booking->studio->name }}</strong><br>
                        <small class="invoice-description">
                            {{ \Carbon\Carbon::parse($invoice->booking->booking_date)->format('F d, Y') }} 
                            ({{ \Carbon\Carbon::parse($invoice->booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($invoice->booking->end_time)->format('h:i A') }})
                        </small>
                    </td>
                    <td class="text-right">{{ abs($invoice->booking->total_hours) }} hrs</td>
                    <td class="text-right">{{ number_format($invoice->booking->studio->hourly_rate, 2) }} INR</td>
                    <td class="text-right">{{ number_format($invoice->booking->base_amount, 2) }} INR</td>
                </tr>
                
                @if($invoice->booking->addons->count() > 0)
                    @foreach($invoice->booking->addons as $addon)
                        <tr>
                            <td>
                                <strong>{{ $addon->name }}</strong><br>
                                <small class="invoice-description">{{ $addon->description }}</small>
                            </td>
                            <td class="text-right">{{ $addon->pivot->quantity }}</td>
                            <td class="text-right">{{ number_format($addon->pivot->price, 2) }} INR</td>
                            <td class="text-right">{{ number_format($addon->pivot->quantity * $addon->pivot->price, 2) }} INR</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>{{ number_format($invoice->subtotal, 2) }} INR</span>
            </div>
            <div class="totals-row total">
                <span>TOTAL:</span>
                <span>{{ number_format($invoice->total_amount, 2) }} INR</span>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <h3>Payment Information</h3>
            <p><strong>Payment Method:</strong> 
                {{ $invoice->booking->payment_method === 'online' ? 'Online Payment' : 'Pay at Studio' }}
            </p>
            <p>
                <strong>Status:</strong> 
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
            </p>
            @if($invoice->booking->notes)
                <p class="invoice-notes"><strong>Notes:</strong> {{ $invoice->booking->notes }}</p>
            @endif
        </div>

        @if($invoice->status !== 'paid')
            <div class="payment-actions">
                @if(App\Models\Setting::get('enable_paypal'))
                    <button onclick="alert('Redirecting to PayPal...')" class="pay-btn paypal-btn">Pay with PayPal</button>
                @endif
                
                @if(App\Models\Setting::get('enable_phonepe'))
                    <button onclick="alert('Redirecting to PhonePe...')" class="pay-btn phonepe-btn">Pay with PhonePe</button>
                @endif
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing {{ App\Models\Setting::get('company_name', 'Company Name') }}!</p>
            <p class="invoice-footer-text">For any queries, please contact us at {{ App\Models\Setting::get('company_email', 'info@company.com') }}</p>
        </div>
    </div>
</body>
</html>
