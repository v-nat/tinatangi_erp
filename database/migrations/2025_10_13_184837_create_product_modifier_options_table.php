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
        Schema::create('product_modifier_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_modifier_id')->default(1);
            $table->foreign('product_modifier_id')->references('id')->on('product_modifiers')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_impact', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_modifier_options');
    }
};
