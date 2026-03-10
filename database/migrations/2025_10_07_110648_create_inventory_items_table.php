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
            $table->string('sku')->unique();
            $table->foreignId('item_id')->references('id')->on('items')->onDelete('cascade');
           
            $table->foreignId('inventory_location_id')
                ->nullable()
                ->constrained('inventory_locations')
                ->onDelete('cascade');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('item_units')->onDelete('cascade');
            $table->foreign('base_unit_id')->references('id')->on('item_units')->onDelete('cascade');
            $table->foreignId('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('unit_cost', 12, 5)->default(0);
            $table->decimal('stock_level', 12, 4)->default(0);
            $table->decimal('base_unit_stock_level', 12, 4)->default(0);
            $table->unsignedBigInteger('status')->default(11);
            $table->foreign('status')->references('id')->on('status')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
