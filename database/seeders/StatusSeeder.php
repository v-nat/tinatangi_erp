<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $statuses = ['active', 'inactive', 'suspended', 'terminated', 'on leave', 'resigned', 'pending'];

        foreach ($statuses as $status) {
            Status::create(['status' => $status]);
        }
    }
}
