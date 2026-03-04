<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE announcements MODIFY COLUMN type ENUM('announcement','discount','voucher','promo') NOT NULL DEFAULT 'announcement'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE announcements MODIFY COLUMN type ENUM('announcement','discount','voucher') NOT NULL DEFAULT 'announcement'");
    }
};
