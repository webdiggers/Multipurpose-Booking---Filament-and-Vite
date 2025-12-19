<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->nullable()->after('name');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('whatsapp_verification_code')->nullable()->after('phone_verified_at');
            $table->timestamp('verification_expires_at')->nullable()->after('whatsapp_verification_code');
            $table->enum('role', ['customer', 'reception', 'admin'])->default('customer')->after('verification_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'whatsapp_verification_code',
                'verification_expires_at',
                'role'
            ]);
        });
    }
};
