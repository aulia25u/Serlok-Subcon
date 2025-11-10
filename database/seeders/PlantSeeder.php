<?php

namespace Database\Seeders;

use App\Models\Plant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plants = [
            ['plant_name' => 'Main Plant Jakarta'],
            ['plant_name' => 'Branch Plant Surabaya'],
            ['plant_name' => 'Branch Plant Bandung'],
            ['plant_name' => 'Branch Plant Medan'],
            ['plant_name' => 'Branch Plant Makassar'],
            ['plant_name' => 'Head Office'],
            ['plant_name' => 'Distribution Center'],
        ];

        foreach ($plants as $plant) {
            Plant::create($plant);
        }
    }
}