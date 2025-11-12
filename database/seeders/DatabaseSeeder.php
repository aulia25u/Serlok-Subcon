<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DeptSeeder::class,
            SectionSeeder::class,
            PositionSeeder::class,
            PlantSeeder::class,
            RoleSeeder::class,
            MenuSeeder::class,
            RoleToMenuSeeder::class,
            UserDetailSeeder::class,
            MasterInspectionSeeder::class,
        ]);
    }
}
