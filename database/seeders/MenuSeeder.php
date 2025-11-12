<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\RoleToMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacyMenus = [
            'Department Management',
            'Section Management',
            'Position Management',
            'Role Management',
            'Plant Management',
        ];

        $legacyMenuIds = Menu::whereIn('menu_name', $legacyMenus)->pluck('id')->toArray();
        if (!empty($legacyMenuIds)) {
            RoleToMenu::whereIn('menu_id', $legacyMenuIds)->delete();
            Menu::whereIn('id', $legacyMenuIds)->delete();
        }

        $menus = [
            'Dashboard',
            'User Management',
            'Company Management',
            'Customer Management',
            'Tenant List Management',
            'Tenant Owner Management',
            'Menu Management',
            'History Management',
            'Master Data',
        ];

        foreach ($menus as $menuName) {
            Menu::updateOrCreate(
                ['menu_name' => $menuName],
                ['menu_name' => $menuName]
            );
        }

        $masterDataMenu = Menu::where('menu_name', 'Master Data')->first();

        if ($masterDataMenu) {
            Menu::updateOrCreate(
                ['menu_name' => 'Master Customer'],
                ['menu_name' => 'Master Customer', 'parent_id' => $masterDataMenu->id]
            );
            Menu::updateOrCreate(
                ['menu_name' => 'Master Item'],
                ['menu_name' => 'Master Item', 'parent_id' => $masterDataMenu->id]
            );
        }
    }
}
