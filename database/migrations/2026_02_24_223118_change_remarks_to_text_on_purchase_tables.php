<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->text('remarks')->nullable()->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->text('remarks')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();
        });
    }
};
