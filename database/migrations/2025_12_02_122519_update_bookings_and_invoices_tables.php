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
        // Update bookings table
        Schema::table('bookings', function (Blueprint $table) {
            // Change payment_method to string to allow more values
            $table->string('payment_method')->change();
        });

        // Update invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert payment_method to enum (might fail if data exists, so just changing to string is safer or leave it)
            // For now, we won't revert it back to strict enum to avoid data loss
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'issue_date',
                'due_date',
                'paid_at',
                'payment_method',
                'transaction_id'
            ]);
        });
    }
};
