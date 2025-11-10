<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            ['section_name' => 'Recruitment', 'dept_id' => 1],
            ['section_name' => 'Employee Relations', 'dept_id' => 1],
            ['section_name' => 'Payroll', 'dept_id' => 1],
            ['section_name' => 'Software Development', 'dept_id' => 2],
            ['section_name' => 'System Administration', 'dept_id' => 2],
            ['section_name' => 'Network Security', 'dept_id' => 2],
            ['section_name' => 'Accounting', 'dept_id' => 3],
            ['section_name' => 'Budget Planning', 'dept_id' => 3],
            ['section_name' => 'Digital Marketing', 'dept_id' => 4],
            ['section_name' => 'Brand Management', 'dept_id' => 4],
            ['section_name' => 'Production', 'dept_id' => 5],
            ['section_name' => 'Logistics', 'dept_id' => 5],
            ['section_name' => 'Quality Control', 'dept_id' => 6],
            ['section_name' => 'Testing', 'dept_id' => 6],
            ['section_name' => 'Product Innovation', 'dept_id' => 7],
            ['section_name' => 'Technical Research', 'dept_id' => 7],
            ['section_name' => 'Support Team', 'dept_id' => 8],
            ['section_name' => 'Customer Success', 'dept_id' => 8],
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }
    }
}