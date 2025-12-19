<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Studio;
use App\Models\Addon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'phone' => '9876543210',
            'email' => 'admin@studio.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone_verified_at' => now(),
        ]);

        // Create Reception Staff
        User::create([
            'name' => 'Reception Staff',
            'phone' => '9876543211',
            'email' => 'reception@studio.com',
            'password' => bcrypt('password'),
            'role' => 'reception',
            'phone_verified_at' => now(),
        ]);

        // Create Sample Customer
        User::create([
            'name' => 'John Doe',
            'phone' => '9876543212',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone_verified_at' => now(),
        ]);

        // Create Studios (Renamed to Resources contextually, though class is Studio)
        Studio::create([
            'name' => 'Conference Room A',
            'description' => 'Modern conference room equipped for video meetings and presentations.',
            'hourly_rate' => 2000,
            'capacity' => 10,
            'amenities' => ['Wi-Fi', 'Projector', 'Whiteboard', 'Video Conferencing', 'A/C'],
            'is_active' => true,
        ]);

        Studio::create([
            'name' => 'Private Office B',
            'description' => 'Quiet private office perfect for focused work or small interviews.',
            'hourly_rate' => 800,
            'capacity' => 3,
            'amenities' => ['High-speed Internet', 'Ergonomic Chairs', 'Desk', 'A/C'],
            'is_active' => true,
        ]);

        Studio::create([
            'name' => 'Main Event Hall',
            'description' => 'Spacious hall suitable for workshops, seminars, and networking events.',
            'hourly_rate' => 5000,
            'capacity' => 50,
            'amenities' => ['Sound System', 'Stage', 'Projector', 'Chairs', 'Lighting'],
            'is_active' => true,
        ]);

        // Create Addons
        Addon::create([
            'name' => 'Extra Time (1 Hour)',
            'description' => 'Extend your booking by one hour',
            'price' => 500,
            'type' => 'hourly_extension',
            'is_active' => true,
        ]);

        Addon::create([
            'name' => 'Technical Support',
            'description' => 'On-site IT or technical assistance',
            'price' => 1000,
            'type' => 'service',
            'is_active' => true,
        ]);

        Addon::create([
            'name' => 'Coffee & Refreshments',
            'description' => 'Basic coffee and refreshment service for your group',
            'price' => 300,
            'type' => 'service',
            'is_active' => true,
        ]);

        Addon::create([
            'name' => 'Video Recording',
            'description' => 'Professional recording of your event/meeting',
            'price' => 2500,
            'type' => 'service',
            'is_active' => true,
        ]);
    }
}
