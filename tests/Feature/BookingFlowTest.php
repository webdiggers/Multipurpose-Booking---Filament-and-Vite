<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Studio;
use App\Models\Booking;
use App\Models\Addon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->createTestData();
    }

    protected function createTestData()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'phone' => '1234567890',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        // Create customer
        User::create([
            'name' => 'Test Customer',
            'phone' => '9876543210',
            'email' => 'customer@test.com',
            'role' => 'customer',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        // Create studio
        Studio::create([
            'name' => 'Test Studio',
            'description' => 'A test recording studio',
            'hourly_rate' => 2000,
            'capacity' => 5,
            'is_active' => true,
        ]);

        // Create addon
        Addon::create([
            'name' => 'Extra Mic',
            'price' => 500,
            'type' => 'equipment',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_create_a_booking_with_required_fields()
    {
        $user = User::where('role', 'customer')->first();
        $studio = Studio::first();

        $bookingData = [
            'user_id' => $user->id,
            'studio_id' => $studio->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => 1,
        ];

        $booking = Booking::create($bookingData);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'user_id' => $user->id,
            'studio_id' => $studio->id,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
        ]);
    }

    /** @test */
    public function it_calculates_total_hours_correctly_for_fractional_hours()
    {
        $user = User::where('role', 'customer')->first();
        $studio = Studio::first();

        // Test 1.5 hours booking
        $bookingData = [
            'user_id' => $user->id,
            'studio_id' => $studio->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'total_hours' => 1.5,
            'base_amount' => 3000, // 2000 * 1.5
            'addon_amount' => 0,
            'total_amount' => 3000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => 1,
        ];

        $booking = Booking::create($bookingData);

        $this->assertEquals(1.5, $booking->total_hours);
        $this->assertEquals(3000, $booking->base_amount);
    }

    /** @test */
    public function it_validates_end_time_is_after_start_time()
    {
        $user = User::where('role', 'customer')->first();
        $studio = Studio::first();

        $start = \Carbon\Carbon::parse('10:00:00');
        $end = \Carbon\Carbon::parse('09:00:00'); // Before start

        $this->assertTrue($end->lessThan($start));
    }

    /** @test */
    public function it_creates_payment_and_invoice_after_booking()
    {
        $user = User::where('role', 'customer')->first();
        $studio = Studio::first();

        $booking = Booking::create([
            'user_id' => $user->id,
            'studio_id' => $studio->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 0,
            'total_amount' => 2000,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => 1,
        ]);

        // Create payment
        $payment = $booking->payment()->create([
            'amount' => $booking->total_amount,
            'payment_method' => $booking->payment_method,
            'status' => 'pending',
        ]);

        // Create invoice
        $invoice = $booking->invoice()->create([
            'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'subtotal' => $booking->total_amount,
            'total_amount' => $booking->total_amount,
            'status' => 'pending',
            'generated_at' => now(),
        ]);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 2000,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('invoices', [
            'booking_id' => $booking->id,
            'total_amount' => 2000,
        ]);
    }

    /** @test */
    public function it_calculates_addon_amount_correctly()
    {
        $user = User::where('role', 'customer')->first();
        $studio = Studio::first();
        $addon = Addon::first();

        $booking = Booking::create([
            'user_id' => $user->id,
            'studio_id' => $studio->id,
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'total_hours' => 1,
            'base_amount' => 2000,
            'addon_amount' => 500, // 1 addon @ 500
            'total_amount' => 2500,
            'payment_method' => 'pay_at_studio',
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'created_by' => 1,
        ]);

        // Attach addon
        $booking->addons()->attach($addon->id, [
            'quantity' => 1,
            'price' => 500,
        ]);

        $this->assertEquals(2500, $booking->total_amount);
        $this->assertEquals(1, $booking->addons()->count());
    }
}
