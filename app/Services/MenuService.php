<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\RoleToMenu;
use Illuminate\Support\Facades\Auth;

class MenuService
{
    public static function getAllowedMenus()
    {
        $user = Auth::user();
        if (!$user) {
            return self::getDefaultMenu();
        }

        $userDetail = $user->userDetail;
        if (!$userDetail || !$userDetail->role) {
            return self::getDefaultMenu();
        }

        $roleId = $userDetail->role->id;

        // Get menu IDs that this role has read access to
        $allowedMenuIds = RoleToMenu::where('role_id', $roleId)
            ->where('is_read', true)
            ->pluck('menu_id')
            ->toArray();

        // Get the menu records
        $menus = Menu::whereIn('id', $allowedMenuIds)->get();

        // Build the menu structure
        $menuItems = [
            // Navbar items:
            [
                'type' => 'fullscreen-widget',
                'topnav_right' => true,
            ],

            // Sidebar items:
            [
                'type' => 'sidebar-menu-search',
                'text' => 'search',
                'class' => 'sidebar-menu-search',
            ],
        ];

        $rbacChildren = [];

        // Separate RBAC menus for dropdown
        foreach ($menus as $menu) {
            $menuName = $menu->menu_name;
            $label = self::getMenuLabel($menuName);

            // Check if it's an RBAC menu (e.g., contains 'Management')
            if (str_contains($menuName, 'Management')) {
                $rbacChildren[] = [
                    'text' => $label,
                    'url' => self::getMenuUrl($menuName),
                    'icon' => self::getMenuIcon($menuName),
                ];
            } else {
                // Non-RBAC flat menus (if any)
                $menuItems[] = [
                    'text' => $label,
                    'url' => self::getMenuUrl($menuName),
                    'icon' => self::getMenuIcon($menuName),
                ];
            }
        }

        $tenantLabels = ['Tenant List', 'Tenant Owner', 'Customer'];
        $tenantChildren = [];
        $internalChildren = [];

        foreach ($rbacChildren as $child) {
            if (in_array($child['text'], $tenantLabels)) {
                $tenantChildren[] = $child;
            } else {
                $internalChildren[] = $child;
            }
        }

        if (!empty($internalChildren)) {
            $menuItems[] = [
                'text' => 'Internal Management',
                'icon' => 'fas fa-sitemap',
                'submenu' => $internalChildren,
            ];
        }

        if (!empty($tenantChildren)) {
            $menuItems[] = [
                'text' => 'Tenant Management',
                'icon' => 'fas fa-door-open',
                'submenu' => $tenantChildren,
            ];
        }

        if ($userMenu = self::getUserMenu()) {
            $menuItems[] = $userMenu;
        }

        return $menuItems;
    }

    private static function getDefaultMenu()
    {
        return [
            // Navbar items:
            [
                'type' => 'fullscreen-widget',
                'topnav_right' => true,
            ],

            // Sidebar items:
            [
                'type' => 'sidebar-menu-search',
                'text' => 'search',
                'class' => 'sidebar-menu-search',
            ],
            [
                'text' => 'Dashboard',
                'url' => 'dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],
        ];
    }

    private static function getMenuLabel($menuName)
    {
        return trim(str_replace(' Management', '', $menuName));
    }

    private static function getMenuUrl($menuName)
    {
        $urlMap = [
            'Dashboard' => 'dashboard',
            'User Management' => 'rbac/user-data',
            'Role Management' => 'rbac/role',
            'History Management' => 'rbac/history',
            'Department Management' => 'rbac/department',
            'Section Management' => 'rbac/section',
            'Position Management' => 'rbac/position',
            'Plant Management' => 'rbac/plant',
            'Customer Management' => 'rbac/customer',
            'Tenant List Management' => 'rbac/customer',
            'Tenant Owner Management' => 'rbac/tenant-owner',
            'Menu Management' => 'rbac/master-menu',
        ];

        return $urlMap[$menuName] ?? '#';
    }

    private static function getMenuIcon($menuName)
    {
        $iconMap = [
            'Dashboard' => 'fas fa-fw fa-tachometer-alt',
            'User Management' => 'fas fa-fw fa-users',
            'Role Management' => 'fas fa-fw fa-user-shield',
            'History Management' => 'fas fa-fw fa-history',
            'Department Management' => 'fas fa-fw fa-building',
            'Section Management' => 'fas fa-fw fa-door-closed',
            'Position Management' => 'fas fa-fw fa-user-tag',
            'Customer Management' => 'fas fa-fw fa-user-friends',
            'Tenant Owner Management' => 'fas fa-fw fa-id-card',
            'Tenant List Management' => 'fas fa-fw fa-building',
            'Plant Management' => 'fas fa-fw fa-industry',
            'Menu Management' => 'fas fa-fw fa-bars',
        ];

        return $iconMap[$menuName] ?? 'fas fa-fw fa-circle';
    }

    private static function getUserMenu()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $displayName = self::getUserDisplayName($user);

        return [
            'text' => $displayName,
            'icon' => 'fas fa-fw fa-user',
            'topnav_right' => true,
            'submenu' => [
                [
                    'text' => 'Profile',
                    'url' => route('profile.edit'),
                    'icon' => 'fas fa-fw fa-user-cog',
                ],
                [
                    'text' => 'Logout',
                    'url' => route('logout'),
                    'icon' => 'fas fa-fw fa-sign-out-alt',
                ],
            ],
        ];
    }

    private static function getUserDisplayName($user)
    {
        return optional($user->userDetail)->employee_name
            ?? $user->name
            ?? $user->username
            ?? 'User';
    }
}
