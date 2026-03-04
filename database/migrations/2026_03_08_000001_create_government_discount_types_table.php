<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('government_discount_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedTinyInteger('percentage');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed Philippine government-mandated discount types
        DB::table('government_discount_types')->insert([
            [
                'name'        => 'Senior Citizen',
                'percentage'  => 20,
                'description' => 'Republic Act 9994 — Expanded Senior Citizens Act',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'PWD / Person with Disability',
                'percentage'  => 20,
                'description' => 'Republic Act 10754 — An Act Expanding the Benefits and Privileges of PWDs',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('government_discount_types');
    }
};
