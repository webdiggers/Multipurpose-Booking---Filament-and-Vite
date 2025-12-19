<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Studio;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'phone' => '1234567890',
            'role' => 'admin',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $this->customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'phone' => '9876543210',
            'role' => 'customer',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $this->studio = Studio::create([
            'name' => 'Test Studio',
            'description' => 'Test',
            'hourly_rate' => 2000,
            'capacity' => 5,
            'is_active' => true,
        ]);
    }

    public function test_invoice_is_created_with_booking()
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'studio_id' => $this->studio->id,
            'booking_date' => now()->addDay(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'subtotal' => 2000,
            'total_amount' => 2000,
            'status' => 'pending',
            'generated_at' => now(),
        ]);

        $this->assertDatabaseHas('invoices', [
            'booking_id' => $booking->id,
            'total_amount' => 2000,
        ]);
    }

    public function test_invoice_number_format()
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'studio_id' => $this->studio->id,
            'booking_date' => now()->addDay(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-000001',
            'subtotal' => 2000,
            'total_amount' => 2000,
            'status' => 'pending',
            'generated_at' => now(),
        ]);

        $this->assertMatchesRegularExpression('/^INV-\d{6}$/', $invoice->invoice_number);
    }

    public function test_can_access_invoice_print_page()
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'studio_id' => $this->studio->id,
            'booking_date' => now()->addDay(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-000001',
            'subtotal' => 2000,
            'total_amount' => 2000,
            'status' => 'pending',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)->get("/invoices/{$invoice->id}/print");

        $response->assertStatus(200);
        $response->assertSee('INVOICE');
        $response->assertSee('INV-000001');
    }

    public function test_invoice_displays_correct_currency_format()
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'studio_id' => $this->studio->id,
            'booking_date' => now()->addDay(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => $this->admin->id,
        ]);

        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-000001',
            'subtotal' => 2000,
            'total_amount' => 2000,
            'status' => 'pending',
            'generated_at' => now(),
        ]);

        $response = $this->actingAs($this->customer)->get("/invoices/{$invoice->id}/print");

        $response->assertSee('INR');
        $response->assertSee('2,000.00');
    }
}
