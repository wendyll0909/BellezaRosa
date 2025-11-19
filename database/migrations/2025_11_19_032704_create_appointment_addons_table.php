<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_addons', function (Blueprint $table) {
            $table->id('addon_id');
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_addons');
    }
};