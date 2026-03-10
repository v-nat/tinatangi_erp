<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->after('delivered_qnty');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn('expiration_date');
        });
    }
};
