<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['position_name' => 'HR Manager', 'section_id' => 1],
            ['position_name' => 'Recruitment Specialist', 'section_id' => 1],
            ['position_name' => 'Employee Relations Officer', 'section_id' => 2],
            ['position_name' => 'Payroll Administrator', 'section_id' => 3],
            ['position_name' => 'Senior Developer', 'section_id' => 4],
            ['position_name' => 'Junior Developer', 'section_id' => 4],
            ['position_name' => 'System Administrator', 'section_id' => 5],
            ['position_name' => 'Network Engineer', 'section_id' => 6],
            ['position_name' => 'Accountant', 'section_id' => 7],
            ['position_name' => 'Financial Analyst', 'section_id' => 8],
            ['position_name' => 'Marketing Specialist', 'section_id' => 9],
            ['position_name' => 'Brand Manager', 'section_id' => 10],
            ['position_name' => 'Production Supervisor', 'section_id' => 11],
            ['position_name' => 'Logistics Coordinator', 'section_id' => 12],
            ['position_name' => 'Quality Inspector', 'section_id' => 13],
            ['position_name' => 'Test Engineer', 'section_id' => 14],
            ['position_name' => 'Research Scientist', 'section_id' => 15],
            ['position_name' => 'Technical Lead', 'section_id' => 16],
            ['position_name' => 'Support Representative', 'section_id' => 17],
            ['position_name' => 'Customer Success Manager', 'section_id' => 18],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}