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
        $statusesWithId = [
            1 => 'Active',
            2 => 'Inactive',
            3 => 'Suspended',
            4 => 'Terminated',
            5 => 'Resigned',
            6 => 'Present',
            7 => 'On Time',
            8 => 'On leave',
            9 => 'Late',
            10 => 'Absent',
            11 => 'Pending',
            12 => 'Rejected',
            13 => 'Approved',
            14 => 'On Process',
            15 => 'Released',
            16 => 'Delivered',
            17 => 'Partial Delivered',
            18 => 'Approved - Pending Dispatch',
            19 => 'Rejected - Supplier',
            20 => 'Accepted - Supplier',
            21 => 'Pending - Supplier',
            22 => 'Return',
            23 => 'Completed',
            24 => 'On Stock',
            25 => 'Low Stock',
            26 => 'Out of Stock',
            27 => 'Pending Restock',
            28 => 'In Queue',
            29 => 'In Prep',
            30 => 'Ready',
            31 => 'Voided',
            32 => 'Loss',
            33 => 'Growth',
            34 => 'Hidden',
            35 => 'Displayed',

        ];

        foreach ($statusesWithId as $id => $status) {
            Status::create(['id' => $id, 'status' => $status]);
        }
    }
}
