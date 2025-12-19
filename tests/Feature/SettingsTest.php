<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'phone' => '1234567890',
            'role' => 'admin',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_get_setting_value()
    {
        Setting::set('company_name', 'Test Studio', 'text');
        
        $value = Setting::get('company_name');
        
        $this->assertEquals('Test Studio', $value);
    }

    public function test_can_set_text_setting()
    {
        Setting::set('company_email', 'test@example.com', 'text');
        
        $this->assertDatabaseHas('settings', [
            'key' => 'company_email',
            'value' => 'test@example.com',
            'type' => 'text',
        ]);
    }

    public function test_can_set_boolean_setting()
    {
        Setting::set('maintenance_mode', true, 'boolean');
        
        $value = Setting::get('maintenance_mode');
        
        $this->assertTrue($value);
    }

    public function test_can_set_json_setting()
    {
        $links = [
            ['platform' => 'Facebook', 'url' => 'https://facebook.com'],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com'],
        ];
        
        Setting::set('social_media_links', $links, 'json');
        
        $retrieved = Setting::get('social_media_links');
        
        $this->assertEquals($links, $retrieved);
    }

    public function test_returns_default_when_setting_not_found()
    {
        $value = Setting::get('non_existent_key', 'default_value');
        
        $this->assertEquals('default_value', $value);
    }

    public function test_maintenance_mode_blocks_non_admin_users()
    {
        Setting::set('maintenance_mode', true, 'boolean');
        Setting::set('maintenance_message', 'Under maintenance', 'text');
        
        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'phone' => '9876543210',
            'role' => 'customer',
            'phone_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        
        $response = $this->actingAs($customer)->get('/studios');
        
        $response->assertStatus(503);
        $response->assertSee('Under maintenance');
    }

    public function test_maintenance_mode_allows_admin_users()
    {
        Setting::set('maintenance_mode', true, 'boolean');
        
        $response = $this->actingAs($this->admin)->get('/admin');
        
        $response->assertStatus(200);
    }
}
