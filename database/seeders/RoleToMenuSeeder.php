<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
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
        // Ensure roles are seeded if not present
        if (Role::count() == 0) {
            $this->call(RoleSeeder::class);
        }

        // Ensure menus are seeded if not present
        if (Menu::count() == 0) {
            $this->call(MenuSeeder::class);
        }

        $menuMap = Menu::pluck('id', 'menu_name')->toArray();
        $availableRoleIds = Role::pluck('id')->toArray();

        $roleToMenus = [
            // Administrator - Full access to all menus
            ['role_id' => 1, 'menu_name' => 'Dashboard', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'User Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Company Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Menu Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Master Customer', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'Master Item', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_name' => 'History Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],

            // Manager - Read access to most menus, limited create/update
            ['role_id' => 2, 'menu_name' => 'Dashboard', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'User Management', 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Company Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Menu Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Master Customer', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'Master Item', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_name' => 'History Management', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],

            // Employee - Limited access
            ['role_id' => 4, 'menu_name' => 'Dashboard', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Master Customer', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Master Item', 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'User Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Company Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_name' => 'Menu Management', 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
        ];

        foreach ($roleToMenus as $roleToMenu) {
            $roleId = $roleToMenu['role_id'];
            if (!in_array($roleId, $availableRoleIds, true)) {
                continue;
            }

            $menuId = $menuMap[$roleToMenu['menu_name']] ?? null;
            if (!$menuId) {
                continue;
            }

            RoleToMenu::updateOrCreate(
                [
                    'role_id' => $roleToMenu['role_id'],
                    'menu_id' => $menuId,
                ],
                [
                    'is_create' => $roleToMenu['is_create'],
                    'is_read' => $roleToMenu['is_read'],
                    'is_update' => $roleToMenu['is_update'],
                    'is_delete' => $roleToMenu['is_delete'],
                ]
            );
        }
    }
}
