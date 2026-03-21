<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        try {
            Schema::table('purchase_order_details', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
        } catch (\Exception) {
        }

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('purchase_order_details', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
        } catch (\Exception) {
        }
    }
};
