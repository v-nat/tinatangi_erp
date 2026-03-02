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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('sss_employer_share', 10, 2)->default(0)->after('pagibig');
            $table->decimal('philhealth_employer_share', 10, 2)->default(0)->after('sss_employer_share');
            $table->decimal('pagibig_employer_share', 10, 2)->default(0)->after('philhealth_employer_share');
            $table->foreignId('contribution_rate_id')->nullable()->after('pagibig_employer_share')
                  ->constrained('contribution_rates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contribution_rate_id');
            $table->dropColumn(['sss_employer_share', 'philhealth_employer_share', 'pagibig_employer_share']);
        });
    }
};
