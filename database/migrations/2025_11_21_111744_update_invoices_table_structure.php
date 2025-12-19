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
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'total')) {
                $table->renameColumn('total', 'total_amount');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('pending');
            }
            $table->decimal('subtotal', 10, 2)->nullable()->change();
            $table->timestamp('generated_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'total');
            $table->dropColumn('status');
            $table->decimal('subtotal', 10, 2)->nullable(false)->change();
            $table->timestamp('generated_at')->nullable(false)->change();
        });
    }
};
