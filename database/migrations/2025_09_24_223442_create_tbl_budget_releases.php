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
        Schema::create('budget_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('release_id')->unique();
            $table->string('type');
            $table->double('amount');

            $table->unsignedBigInteger('request_id')->unique();
            $table->unsignedBigInteger('requested_by_id')->nullable();
            $table->foreign('requested_by_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamp('requested_at');
            $table->unsignedBigInteger('released_by_id')->nullable();
            $table->foreign('released_by_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamp('released_at')->nullable();
            $table->foreignId('department')->nullable()
                ->constrained('departments')->onUpdate('cascade')->onDelete('set null');

            $table->string('notes')->nullable();

            $table->unsignedBigInteger('status')->default(14);
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
        Schema::dropIfExists('budget_releases');
    }
};
