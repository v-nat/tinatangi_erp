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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('purchase_order_name')->nullable();

            $table->date('order_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();

            $table->string('delivery_name')->nullable();
            $table->string('remarks')->nullable();
            
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');

            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

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
        Schema::dropIfExists('purchase_orders');
    }
};
