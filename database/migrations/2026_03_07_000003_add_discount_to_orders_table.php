<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable()->after('payment_status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_id');

            $table->foreign('discount_id')
                  ->references('id')->on('announcements')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropColumn(['discount_id', 'discount_amount']);
        });
    }
};
