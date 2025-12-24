<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->text('description')->nullable(); // Add description column
            $table->enum('specialty', ['hair', 'nail', 'both'])->default('both'); // Add specialty column
            $table->boolean('is_active')->default(true); // Add is_active column
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique('name');
            $table->index('display_order');
            $table->index('specialty');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};