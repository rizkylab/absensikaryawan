<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
            ],
            [
                'name' => 'atasan',
                'display_name' => 'Supervisor',
                'description' => 'Can approve overtime and leave requests, view team reports',
            ],
            [
                'name' => 'karyawan',
                'display_name' => 'Employee',
                'description' => 'Can check-in/out, request overtime and leave',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
