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

            $table->foreignId('department')->nullable()
                ->constrained('departments')->onUpdate('cascade')->onDelete('set null');
            
            $table->decimal('amount',10,2)->default(0);

            $table->unsignedBigInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('employees')->onDelete('cascade');
         
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
