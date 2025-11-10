<?php

namespace Database\Seeders;

use App\Models\MasterInspection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterInspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inspections = [
            [
                'inspection_item' => 'Dimensional Check - Length',
                'inspection_method' => 'Caliper/Micrometer',
                'standard' => '±0.05mm',
            ],
            [
                'inspection_item' => 'Dimensional Check - Diameter',
                'inspection_method' => 'Micrometer',
                'standard' => '±0.02mm',
            ],
            [
                'inspection_item' => 'Surface Roughness',
                'inspection_method' => 'Surface Roughness Tester',
                'standard' => 'Ra ≤ 3.2μm',
            ],
            [
                'inspection_item' => 'Visual Inspection',
                'inspection_method' => 'Visual Check',
                'standard' => 'No defects, scratches, or dents',
            ],
            [
                'inspection_item' => 'Hardness Test',
                'inspection_method' => 'Rockwell Hardness Tester',
                'standard' => 'HRC 45-55',
            ],
            [
                'inspection_item' => 'Thread Check',
                'inspection_method' => 'Thread Gauge',
                'standard' => 'Per drawing specification',
            ],
            [
                'inspection_item' => 'Concentricity',
                'inspection_method' => 'Dial Indicator',
                'standard' => 'TIR ≤ 0.05mm',
            ],
            [
                'inspection_item' => 'Parallelism',
                'inspection_method' => 'Dial Indicator/CMM',
                'standard' => '≤ 0.03mm',
            ],
            [
                'inspection_item' => 'Perpendicularity',
                'inspection_method' => 'Square/CMM',
                'standard' => '≤ 0.05mm',
            ],
            [
                'inspection_item' => 'Flatness',
                'inspection_method' => 'Surface Plate/Dial Indicator',
                'standard' => '≤ 0.02mm',
            ],
        ];

        foreach ($inspections as $inspection) {
            MasterInspection::create($inspection);
        }
    }
}
