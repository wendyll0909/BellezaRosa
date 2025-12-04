<?php
// [file name]: 2025_12_01_000000_create_simple_inventory_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('hair_care');
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(3);
            $table->string('unit')->default('pcs');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->timestamps();
            
            $table->index('current_stock');
        });

        Schema::create('inventory_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['add', 'subtract', 'set']);
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->text('remark'); // Required description
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->date('update_date')->default(now()->toDateString());
            $table->timestamps();
            
            $table->index('update_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_updates');
        Schema::dropIfExists('inventory_items');
    }
};