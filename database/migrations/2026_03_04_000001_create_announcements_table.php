<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['announcement', 'discount', 'voucher'])->default('announcement');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('icon')->nullable()->default('fa-solid fa-bullhorn');
            $table->string('theme')->default('gold');
            $table->string('bg_style')->default('solid');
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->decimal('min_spend', 8, 2)->nullable();
            $table->string('voucher_code')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedInteger('status')->default(34);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
