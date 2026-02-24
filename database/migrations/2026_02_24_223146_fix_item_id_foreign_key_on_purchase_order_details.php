<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original migration used incorrect syntax:
     *   foreignId('item_id')->constrained('id')->on('items')
     *
     * This created the column but left the foreign-key constraint pointing at
     * a non-existent table named 'id'. This migration drops any stale
     * constraint and re-adds the correct one pointing to items.id.
     */
    public function up(): void
    {
        // Drop the stale constraint first (ignore if it doesn't exist).
        try {
            Schema::table('purchase_order_details', function (Blueprint $table) {
                $table->dropForeign(['item_id']);
            });
        } catch (\Exception) {
            // Constraint did not exist — nothing to drop.
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
            // Nothing to roll back.
        }
    }
};
