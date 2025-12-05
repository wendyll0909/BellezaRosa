<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('report_type', ['appointments', 'revenue', 'inventory']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('filename');
            $table->integer('record_count')->default(0);
            $table->text('parameters')->nullable(); // JSON for filter parameters
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('report_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_history');
    }
};