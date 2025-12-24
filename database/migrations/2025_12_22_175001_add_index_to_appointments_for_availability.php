<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Add composite index for staff availability queries
            $table->index(['staff_id', 'start_datetime', 'end_datetime', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['staff_id', 'start_datetime', 'end_datetime', 'status']);
        });
    }
};