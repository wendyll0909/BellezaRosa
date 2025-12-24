<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Add cancellation reason field
            $table->text('cancellation_reason')->nullable()->after('notes');
            // Add cancelled_by field to track who cancelled
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancellation_reason');
            // Add cancelled_at timestamp
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            
            // Update status enum to include 'failed'
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM(
                'scheduled', 'confirmed', 'in_progress', 'completed', 'no_show', 'cancelled', 'failed'
            ) DEFAULT 'scheduled'");
            
            // Update payment_method enum
            DB::statement("ALTER TABLE appointments MODIFY COLUMN payment_method ENUM(
                'cash', 'gcash', 'bank_transfer', 'online', 'unpaid', 'refunded', 'failed'
            ) DEFAULT 'unpaid'");
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_by', 'cancelled_at']);
            
            // Revert status enum
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM(
                'scheduled', 'confirmed', 'in_progress', 'completed', 'no_show', 'cancelled'
            ) DEFAULT 'scheduled'");
            
            // Revert payment_method enum
            DB::statement("ALTER TABLE appointments MODIFY COLUMN payment_method ENUM(
                'cash', 'gcash', 'bank_transfer', 'online', 'unpaid'
            ) DEFAULT 'unpaid'");
        });
    }
};