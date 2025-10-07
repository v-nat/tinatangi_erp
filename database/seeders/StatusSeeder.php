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
        $statuses = [
            'Active',
            'Inactive',
            'Suspended',
            'Terminated',
            'Resigned',
            'Present',
            'On Time',
            'On leave',
            'Late',
            'Absent',
            'Pending',
            'Rejected',
            'Approved',
            'On Process',
            'Released',
            'Delivered',
            'Partial Delivered',
            'Approved - Pending Dispatch',
            'Rejected - Supplier',
            'Accepted - Supplier',
            'Pending - Supplier',
            'Return',
            'Completed',
        ];

        foreach ($statuses as $status) {
            Status::create(['status' => $status]);
        }
    }
}
