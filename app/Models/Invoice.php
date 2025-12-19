<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'invoice_number',
        'subtotal',
        'tax',
        'total_amount',
        'status',
        'generated_at',
        'issue_date',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_id',
        'pdf_path'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'generated_at' => 'datetime',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::latest('id')->first();
        $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
        
        return 'INV-' . date('Y') . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoice_number;
    }
}
