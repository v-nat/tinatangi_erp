<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('walk_in_occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('table_for_reservations')->cascadeOnDelete();
            $table->unsignedTinyInteger('table_number');
            $table->timestamp('occupied_at');
            $table->timestamp('freed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_occupancies');
    }
};
