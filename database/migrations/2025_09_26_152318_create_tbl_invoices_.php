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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('purchase_requests')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('delivery_no')->nullable();
            $table->timestamp('date_received')->nullable();
            $table->timestamp('date_approved')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('invoices');
    }
};
