<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Administrator'],
            ['role_name' => 'Manager'],
            ['role_name' => 'Supervisor'],
            ['role_name' => 'Employee'],
            ['role_name' => 'Guest'],
            ['role_name' => 'HR Staff'],
            ['role_name' => 'IT Staff'],
            ['role_name' => 'Finance Staff'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}