<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('restrict');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');

            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable(); // filled by observer

            $table->enum('status', [
                'scheduled', 'confirmed', 'in_progress',
                'completed', 'no_show', 'cancelled'
            ])->default('scheduled');

            $table->enum('payment_method', [
                'cash', 'gcash', 'bank_transfer', 'online', 'unpaid'
            ])->default('unpaid');

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_walk_in')->default(false);

            $table->timestamps();

            // Prevent double-booking for the same staff at the same time
            $table->unique(['staff_id', 'start_datetime']);

            // Indexes for performance
            $table->index('start_datetime');
            $table->index('status');
            $table->index('customer_id');
            $table->index('service_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};