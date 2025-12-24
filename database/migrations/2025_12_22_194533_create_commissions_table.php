<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->decimal('service_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2); // e.g., 30.00 for 30%
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['appointment_id', 'staff_id']);
            $table->index(['staff_id', 'status']);
            $table->index('payment_date');
        });

        // Add commission settings to salon_settings
        Schema::table('salon_settings', function (Blueprint $table) {
            $table->decimal('default_commission_rate', 5, 2)->default(30.00)->after('cancel_cutoff_hours');
            $table->integer('commission_payment_day')->default(15)->after('default_commission_rate'); // Day of month to pay commissions
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
        
        Schema::table('salon_settings', function (Blueprint $table) {
            $table->dropColumn(['default_commission_rate', 'commission_payment_day']);
        });
    }
};