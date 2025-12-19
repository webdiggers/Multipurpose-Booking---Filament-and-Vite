<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'phone' => '1234567890',
            'role' => 'customer',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_list_active_studios()
    {
        Studio::create([
            'name' => 'Active Studio',
            'description' => 'Test studio',
            'hourly_rate' => 2000,
            'capacity' => 5,
            'is_active' => true,
        ]);

        Studio::create([
            'name' => 'Inactive Studio',
            'description' => 'Test studio',
            'hourly_rate' => 2000,
            'capacity' => 5,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)->get('/studios');

        $response->assertStatus(200);
        $response->assertSee('Active Studio');
        $response->assertDontSee('Inactive Studio');
    }

    public function test_can_view_studio_details()
    {
        $studio = Studio::create([
            'name' => 'TestStudio',
            'description' => 'Amazing studio',
            'hourly_rate' => 2000,
            'capacity' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get("/studios/{$studio->id}");

        $response->assertStatus(200);
        $response->assertSee('TestStudio');
        $response->assertSee('Amazing studio');
    }

    public function test_studio_hourly_rate_is_numeric()
    {
        $studio = Studio::create([
            'name' => 'Test Studio',
            'description' => 'Test',
            'hourly_rate' => 2500.50,
            'capacity' => 5,
            'is_active' => true,
        ]);

        $this->assertEquals(2500.50, $studio->hourly_rate);
        $this->assertIsNumeric($studio->hourly_rate);
    }

    public function test_can_filter_studios_by_active_status()
    {
        Studio::create(['name' => 'Active 1', 'description' => 'Test', 'hourly_rate' => 2000, 'capacity' => 5, 'is_active' => true]);
        Studio::create(['name' => 'Active 2', 'description' => 'Test', 'hourly_rate' => 2000, 'capacity' => 5, 'is_active' => true]);
        Studio::create(['name' => 'Inactive', 'description' => 'Test', 'hourly_rate' => 2000, 'capacity' => 5, 'is_active' => false]);

        $activeStudios = Studio::where('is_active', true)->get();

        $this->assertCount(2, $activeStudios);
    }
}
