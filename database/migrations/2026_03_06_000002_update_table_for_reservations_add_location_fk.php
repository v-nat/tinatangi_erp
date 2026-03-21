<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('table_for_reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('table_location_id')->nullable()->after('location');
            $table->foreign('table_location_id')->references('id')->on('table_locations')->nullOnDelete();
        });

        $existing = DB::table('table_for_reservations')
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->pluck('location')
            ->unique();

        foreach ($existing as $locationName) {
            $locationId = DB::table('table_locations')->insertGetId([
                'name'       => $locationName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('table_for_reservations')
                ->where('location', $locationName)
                ->update(['table_location_id' => $locationId]);
        }

        Schema::table('table_for_reservations', function (Blueprint $table) {
            $table->dropColumn('location');
        });
    }

    public function down(): void
    {
        Schema::table('table_for_reservations', function (Blueprint $table) {
            $table->string('location')->nullable()->after('table_location_id');
        });

        $tables = DB::table('table_for_reservations')
            ->join('table_locations', 'table_for_reservations.table_location_id', '=', 'table_locations.id')
            ->select('table_for_reservations.id', 'table_locations.name as location_name')
            ->get();

        foreach ($tables as $row) {
            DB::table('table_for_reservations')
                ->where('id', $row->id)
                ->update(['location' => $row->location_name]);
        }

        Schema::table('table_for_reservations', function (Blueprint $table) {
            $table->dropForeign(['table_location_id']);
            $table->dropColumn('table_location_id');
        });
    }
};
