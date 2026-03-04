<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('gov_discount_type_id')->nullable()->after('discount_amount');
            $table->decimal('gov_discount_amount', 10, 2)->default(0)->after('gov_discount_type_id');

            $table->foreign('gov_discount_type_id')
                  ->references('id')
                  ->on('government_discount_types')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['gov_discount_type_id']);
            $table->dropColumn(['gov_discount_type_id', 'gov_discount_amount']);
        });
    }
};
