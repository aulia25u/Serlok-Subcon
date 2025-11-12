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
            ['menu_name' => 'Dashboard'],
            ['menu_name' => 'User Management'],
            ['menu_name' => 'Department Management'],
            ['menu_name' => 'Section Management'],
            ['menu_name' => 'Position Management'],
            ['menu_name' => 'Role Management'],
            ['menu_name' => 'Plant Management'],
            ['menu_name' => 'Menu Management'],
            ['menu_name' => 'History Management'],
            ['menu_name' => 'Customers'],
            ['menu_name' => 'Calendar Pitching'],
            ['menu_name' => 'MoM Customer'],
            ['menu_name' => 'Reports'],
            ['menu_name' => 'Settings'],
            ['menu_name' => 'POS Monitoring'],
            ['menu_name' => 'Invoice Monitoring'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
