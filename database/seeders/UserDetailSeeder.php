<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some users first
        $users = [
            [
                'username' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@shimada.com',
                'password' => Hash::make('password'),
            ],
            [
                'username' => 'manager1',
                'name' => 'Manager One',
                'email' => 'manager1@shimada.com',
                'password' => Hash::make('password'),
            ],
            [
                'username' => 'employee1',
                'name' => 'Employee One',
                'email' => 'employee1@shimada.com',
                'password' => Hash::make('password'),
            ],
            [
                'username' => 'hr_staff',
                'name' => 'HR Staff',
                'email' => 'hr@shimada.com',
                'password' => Hash::make('password'),
            ],
            [
                'username' => 'it_staff',
                'name' => 'IT Staff',
                'email' => 'it@shimada.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create user details
        $userDetails = [
            [
                'user_id' => 1,
                'position_id' => 1, // HR Manager
                'role_id' => 1, // Administrator
                'employee_id' => 'EMP001',
                'employee_name' => 'John Admin',
                'gender' => 'Male',
                'address' => 'Jakarta, Indonesia',
                'phone' => '081234567890',
                'join_date' => '2020-01-01',
                'status_active' => true,
                'employee_photo' => 'default_photo.jpg',
            ],
            [
                'user_id' => 2,
                'position_id' => 11, // Production Supervisor
                'role_id' => 2, // Manager
                'employee_id' => 'EMP002',
                'employee_name' => 'Jane Manager',
                'gender' => 'Female',
                'address' => 'Surabaya, Indonesia',
                'phone' => '081234567891',
                'join_date' => '2021-03-15',
                'status_active' => true,
                'employee_photo' => 'default_photo.jpg',
            ],
            [
                'user_id' => 3,
                'position_id' => 6, // Junior Developer
                'role_id' => 4, // Employee
                'employee_id' => 'EMP003',
                'employee_name' => 'Bob Employee',
                'gender' => 'Male',
                'address' => 'Bandung, Indonesia',
                'phone' => '081234567892',
                'join_date' => '2022-06-01',
                'status_active' => true,
                'employee_photo' => 'default_photo.jpg',
            ],
            [
                'user_id' => 4,
                'position_id' => 2, // Recruitment Specialist
                'role_id' => 6, // HR Staff
                'employee_id' => 'EMP004',
                'employee_name' => 'Alice HR',
                'gender' => 'Female',
                'address' => 'Medan, Indonesia',
                'phone' => '081234567893',
                'join_date' => '2021-09-10',
                'status_active' => true,
                'employee_photo' => 'default_photo.jpg',
            ],
            [
                'user_id' => 5,
                'position_id' => 7, // System Administrator
                'role_id' => 7, // IT Staff
                'employee_id' => 'EMP005',
                'employee_name' => 'Charlie IT',
                'gender' => 'Male',
                'address' => 'Makassar, Indonesia',
                'phone' => '081234567894',
                'join_date' => '2020-11-20',
                'status_active' => true,
                'employee_photo' => 'default_photo.jpg',
            ],
        ];

        foreach ($userDetails as $userDetail) {
            UserDetail::create($userDetail);
        }
    }
}