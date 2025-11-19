<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            
            // THIS IS THE FIX → explicitly tell Laravel the correct table name
            $table->foreignId('category_id')
                  ->constrained('service_categories')  // ← explicitly specify table
                  ->onDelete('cascade');

            $table->string('name', 100);
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price_regular', 10, 2)->nullable();
            $table->decimal('price_premium', 10, 2)->nullable();
            $table->boolean('is_premium')->default(false);
            $table->text('description')->nullable();
            $table->json('consumables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category_id', 'name']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};