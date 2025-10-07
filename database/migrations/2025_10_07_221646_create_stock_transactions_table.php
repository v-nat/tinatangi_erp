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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            // Transaction details
            $table->enum('transaction_type', ['IN', 'OUT', 'ADJ', 'TRANSFER']);
            $table->decimal('quantity', 10, 3);
            $table->timestamp('transaction_date')->useCurrent();

            // Reference for tracing
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            // User tracking
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

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
        Schema::dropIfExists('stock_transactions');
    }
};
