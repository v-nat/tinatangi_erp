<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('applicable_to')->default('all')->after('min_spend'); // 'all' | 'specific'
            $table->unsignedInteger('usage_limit')->nullable()->after('applicable_to');
            $table->unsignedInteger('usage_count')->default(0)->after('usage_limit');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['applicable_to', 'usage_limit', 'usage_count']);
        });
    }
};
