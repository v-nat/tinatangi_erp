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
        Schema::create('employee_salary_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->nullable()
                ->constrained('positions')->onUpdate('cascade')->onDelete('set null');

            $table->decimal('base_salary', 12, 2)->default(0.00);
            $table->decimal('rate_per_hour', 8, 2)->default(0.00);
            $table->decimal('rate_per_day', 8, 2)->default(0.00);

            $table->unsignedBigInteger('status')->default(1);
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
        Schema::dropIfExists('employee_salary_settings');
    }
};
