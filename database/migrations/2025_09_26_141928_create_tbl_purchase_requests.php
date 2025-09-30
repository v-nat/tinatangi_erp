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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            
            $table->string('type');
            $table->date('requested_date');

            $table->foreignId('department')->nullable()
                ->constrained('departments')->onUpdate('cascade')->onDelete('set null');
            
            $table->decimal('amount',10,2)->default(0);

            $table->unsignedBigInteger('requested_by_id');
            $table->foreign('requested_by_id')->references('id')->on('employees')->onDelete('cascade');

            $table->string('remarks')->nullable();
         
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
        Schema::dropIfExists('purchase_requests');
    }
};
