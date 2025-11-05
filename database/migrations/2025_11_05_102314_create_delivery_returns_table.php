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
        Schema::create('delivery_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_detail_id')
                ->constrained('purchase_order_details')
                ->onDelete('cascade');
            $table->text('reason');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_returns');
    }
};
