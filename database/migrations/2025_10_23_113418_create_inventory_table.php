<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level');
            $table->integer('maximum_level')->nullable();
            $table->enum('status', ['in_stock', 'on_order', 'low_stock', 'out_of_stock']);
            $table->string('unit_of_measure')->default('units');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->enum('movement_type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->string('reference_number')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('performed_by')->constrained('users');
            $table->timestamp('movement_date');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
    }
};
