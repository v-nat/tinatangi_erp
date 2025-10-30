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
        Schema::create('service_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->float('environment_rating');
            $table->float('food_rating');
            $table->float('staff_rating');
            $table->float('overall_rating');
            $table->string('photo')->nullable();

            $table->string('ip_address');
            $table->string('location');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_feedback');
    }
};
