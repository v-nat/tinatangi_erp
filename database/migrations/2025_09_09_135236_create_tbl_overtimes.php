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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->date('date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('total_minutes')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('approval_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
