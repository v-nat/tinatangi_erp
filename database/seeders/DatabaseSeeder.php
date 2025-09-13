<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Status;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartmentSeeder::class);
        $this->call(StatusSeeder::class);
        User::create([
            'id' => 1,
            'first_name' => 'Taylor',
            'last_name' => 'Swift',
            'email' => 'ts13@mail.com',
            'password'=> bcrypt('password'),
            'phone_number'=> '09123456789',
            'user_type'=> 'employee',
        ]);
        User::create([
            'id'=> 2,
            'first_name' => 'Nathaniel',
            'last_name' => 'Vasquez',
            'email' => 'nat@mail.com',
            'password'=> bcrypt('password'),
            'phone_number'=> '09123456789',
            'user_type'=> 'employee',
        ]);

    }
}
