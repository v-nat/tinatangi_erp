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
        Schema::create('contribution_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('sss_employee_rate', 5, 4)->default(0);
            $table->decimal('sss_employer_rate', 5, 4)->default(0);
            $table->decimal('philhealth_employee_rate', 5, 4)->default(0);
            $table->decimal('philhealth_employer_rate', 5, 4)->default(0);
            $table->decimal('pagibig_employee_rate', 5, 4)->default(0);
            $table->decimal('pagibig_employer_rate', 5, 4)->default(0);
            $table->date('effective_date');
            $table->tinyInteger('is_active')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_rates');
    }
};
