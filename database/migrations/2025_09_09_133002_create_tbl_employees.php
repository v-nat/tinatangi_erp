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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Employee Information
            // $table->unsignedBigInteger('user_id');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('address');
            $table->integer('postal_code');
            $table->string('gender');
            $table->date('birth_date');
            $table->integer('age');
            $table->string('phone_number', 11);
            $table->string('citizenship');

            // Foreign Keys
            $table->foreignId('department')->nullable()
                ->constrained('departments')->onUpdate('cascade')->onDelete('set null');

            // $table->foreignId('direct_supervisor')->nullable()
            //     ->constrained('employees')->onUpdate('cascade')->onDelete('set null');

            $table->foreignId('supervisor_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('level', ['staff', 'supervisor', 'manager', 'ceo'])->default('staff');

            $table->foreignId('position')->nullable()
                ->constrained('positions')->onUpdate('cascade')->onDelete('set null');

            // Mandatory Deductions
            $table->decimal('sss', 10, 2)->default(600.00);
            $table->decimal('pagibig', 10, 2)->default(100.00);
            $table->decimal('philhealth', 10, 2)->default(450.00);

            // Salary
            $table->decimal('base_salary', 10, 2)->default(0.00);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
