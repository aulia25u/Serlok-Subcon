<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'Dashboard',
            'User Management',
            'Department Management',
            'Section Management',
            'Position Management',
            'Role Management',
            'Plant Management',
            'Tenant List Management',
            'Tenant Owner Management',
            'Menu Management',
            'History Management',
        ];

        foreach ($menus as $menuName) {
            Menu::updateOrCreate(
                ['menu_name' => $menuName],
                ['menu_name' => $menuName]
            );
        }
    }
}
