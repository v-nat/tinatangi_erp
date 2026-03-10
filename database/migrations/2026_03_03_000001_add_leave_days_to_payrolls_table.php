<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedTinyInteger('paid_leave_days')->default(0)->after('leave_pay');
            $table->unsignedTinyInteger('unpaid_leave_days')->default(0)->after('paid_leave_days');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['paid_leave_days', 'unpaid_leave_days']);
        });
    }
};
