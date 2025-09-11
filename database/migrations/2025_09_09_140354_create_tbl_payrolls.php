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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

            $table->integer('month');
            $table->integer('days_start')->default(0); 
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('payroll_date');

            // Attendance Summary
            $table->integer('days_present')->default(0);
            $table->decimal('total_hours_worked', 8, 2)->default(0);

            // Earnings
            $table->decimal('regular_hour_pay', 10, 2)->default(0);
            $table->decimal('overtime_pay', 10, 2)->default(0);

            // Deductions
            $table->integer('days_absent')->default(0);
            $table->decimal('days_absent_deduction', 10, 2)->default(0);
            $table->decimal('tardiness_deduction', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0); // Total deductions
            $table->decimal('tax_deduction', 10, 2)->default(0);

            // Totals
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('salary_before_tax', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);

            $table->string('status')->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
