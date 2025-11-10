<?php

namespace Database\Seeders;

use App\Models\Dept;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $depts = [
            ['dept_name' => 'Human Resources'],
            ['dept_name' => 'Information Technology'],
            ['dept_name' => 'Finance'],
            ['dept_name' => 'Marketing'],
            ['dept_name' => 'Operations'],
            ['dept_name' => 'Quality Assurance'],
            ['dept_name' => 'Research & Development'],
            ['dept_name' => 'Customer Service'],
        ];

        foreach ($depts as $dept) {
            Dept::create($dept);
        }
    }
}


