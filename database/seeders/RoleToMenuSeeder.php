<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\RoleToMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleToMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuMap = Menu::pluck('id', 'menu_name')->toArray();

        $roleToMenus = [
            // Administrator - Full access to all menus
            ['role_id' => 1, 'menu_name' => 'Dashboard', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'User Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Department Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Section Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Position Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Role Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Plant Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Menu Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'History Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],

            // Manager - Read access to most menus, limited create/update
            ['role_id' => 2, 'menu_name' => 'Dashboard', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'User Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Department Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Section Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Position Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Role Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Plant Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Menu Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'History Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],

            // Employee - Limited access
            ['role_id' => 4, 'menu_name' => 'Dashboard', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'User Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Department Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Section Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Position Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Role Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Plant Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Menu Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
        ];

        foreach ($roleToMenus as $roleToMenu) {
            $menuId = $menuMap[$roleToMenu['menu_name']] ?? null;
            if (!$menuId) {
                continue;
            }

            RoleToMenu::create([
                'role_id' => $roleToMenu['role_id'],
                'menu_id' => $menuId,
                'is_create' => $roleToMenu['is_create'],
                'is_read' => $roleToMenu['is_read'],
                'is_update' => $roleToMenu['is_update'],
                'is_delete' => $roleToMenu['is_delete'],
            ]);
        }
    }
}
