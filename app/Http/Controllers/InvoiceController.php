<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function print(Invoice $invoice)
    {
        return view('invoices.print', compact('invoice'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function generatePdfForWhatsApp(Invoice $invoice)
    {
        // Generate PDF and save to storage
        $pdf = Pdf::loadView('invoices.print', compact('invoice'));
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        $path = storage_path('app/public/invoices/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(storage_path('app/public/invoices'))) {
            mkdir(storage_path('app/public/invoices'), 0755, true);
        }
        
        $pdf->save($path);
        
        // Return the public URL
        return asset('storage/invoices/' . $filename);
    }
}
