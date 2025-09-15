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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->integer('tardiness',)->default(0);
            $table->boolean('is_leave')->default(false);
            $table->integer('tardiness_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->unsignedBigInteger('leave_id')->nullable();
            $table->unsignedBigInteger('overtime_id')->nullable();
            $table->unsignedBigInteger('status')->default(8); //preset
            $table->foreign('status')->references('id')->on('status')->onDelete('cascade');
            $table->foreign('leave_id')->references('id')->on('leaves')->nullOnDelete();
            $table->foreign('overtime_id')->references('id')->on('overtimes')->nullOnDelete();


            $table->unique(['employee_id', 'date']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
