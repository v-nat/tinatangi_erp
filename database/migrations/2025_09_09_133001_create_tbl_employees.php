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
            $table->string('position')->nullable();

            // Foreign Keys
            // $table->unsignedBigInteger('department')->nullable();
            $table->foreignId('department')->nullable()
                ->constrained('departments')->onUpdate('cascade')->onDelete('set null');
            
            // $table->unsignedBigInteger('direct_supervisor')->nullable();
            $table->foreignId('direct_supervisor')->nullable()
                ->constrained('employees')->onUpdate('cascade')->onDelete('set null');

            // Salary
            $table->decimal('sss', 10, 2)->nullable();
            $table->decimal('pagibig', 10, 2)->nullable();
            $table->decimal('philhealth', 10, 2)->nullable();
            $table->decimal('salary', 10, 2)->default(0.00);

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
