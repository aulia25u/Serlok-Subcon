<?php

namespace Database\Seeders;

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
        $roleToMenus = [
            // Administrator - Full access to all menus
            ['role_id' => 1, 'menu_id' => 1, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 2, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 3, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 4, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 5, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 6, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 7, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 8, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 9, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],
            ['role_id' => 1, 'menu_id' => 10, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => true],

            // Manager - Read access to most menus, limited create/update
            ['role_id' => 2, 'menu_id' => 1, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 2, 'is_create' => true, 'is_read' => true, 'is_update' => true, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 3, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 4, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 5, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 6, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 7, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 8, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 9, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 2, 'menu_id' => 10, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],

            // Employee - Limited access
            ['role_id' => 4, 'menu_id' => 1, 'is_create' => false, 'is_read' => true, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 2, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 3, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 4, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 5, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 6, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 7, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 8, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 9, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
            ['role_id' => 4, 'menu_id' => 10, 'is_create' => false, 'is_read' => false, 'is_update' => false, 'is_delete' => false],
        ];

        foreach ($roleToMenus as $roleToMenu) {
            RoleToMenu::create($roleToMenu);
        }
    }
}