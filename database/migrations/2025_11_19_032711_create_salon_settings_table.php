<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_id')->primary();
            $table->time('opening_time')->default('09:00:00');
            $table->time('closing_time')->default('20:00:00');
            $table->integer('slot_interval_minutes')->default(30);
            $table->integer('max_days_book_ahead')->default(60);
            $table->integer('cancel_cutoff_hours')->default(2);
            $table->timestamps();
        });

        // Insert default row
        DB::table('salon_settings')->insert(['setting_id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_settings');
    }
};