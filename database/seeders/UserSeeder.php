<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $atasanRole = Role::where('name', 'atasan')->first();
        $karyawanRole = Role::where('name', 'karyawan')->first();

        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'employee_id' => 'EMP001',
            'phone' => '081234567890',
            'position' => 'System Administrator',
            'base_salary' => 15000000,
        ]);

        // Create Atasan User
        User::create([
            'name' => 'Supervisor One',
            'email' => 'atasan@example.com',
            'password' => Hash::make('password'),
            'role_id' => $atasanRole->id,
            'employee_id' => 'EMP002',
            'phone' => '081234567891',
            'position' => 'Team Leader',
            'base_salary' => 10000000,
        ]);

        // Create Karyawan Users
        $supervisor = User::where('employee_id', 'EMP002')->first();
        
        User::create([
            'name' => 'Employee One',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('password'),
            'role_id' => $karyawanRole->id,
            'employee_id' => 'EMP003',
            'phone' => '081234567892',
            'position' => 'Staff',
            'base_salary' => 5000000,
            'supervisor_id' => $supervisor->id,
        ]);

        User::create([
            'name' => 'Employee Two',
            'email' => 'karyawan2@example.com',
            'password' => Hash::make('password'),
            'role_id' => $karyawanRole->id,
            'employee_id' => 'EMP004',
            'phone' => '081234567893',
            'position' => 'Staff',
            'base_salary' => 5000000,
            'supervisor_id' => $supervisor->id,
        ]);
    }
}
