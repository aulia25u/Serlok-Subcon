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

        $menuMap = $menus->keyBy('menu_name');
        $internalOrder = [
            'Company Management',
            'User Management',
            'Menu Management',
            'History Management',
        ];
        $tenantLabels = ['Tenant List', 'Tenant Owner', 'Customer'];
        $masterDataLabels = ['Master Customer', 'Master Item'];
        $tenantChildren = [];
        $internalChildren = [];
        $masterDataChildren = [];

        foreach ($internalOrder as $menuName) {
            if (!$menuMap->has($menuName)) {
                continue;
            }

            $internalChildren[] = [
                'text' => self::getMenuLabel($menuName),
                'url' => self::getMenuUrl($menuName),
                'icon' => self::getMenuIcon($menuName),
            ];
        }

        foreach ($menus as $menu) {
            if (in_array($menu->menu_name, $internalOrder, true)) {
                continue;
            }

            if ($menu->menu_name === 'Master Data') {
                continue;
            }

            $label = self::getMenuLabel($menu->menu_name);
            $item = [
                'text' => $label,
                'url' => self::getMenuUrl($menu->menu_name),
                'icon' => self::getMenuIcon($menu->menu_name),
            ];

            if (in_array($menu->menu_name, $masterDataLabels, true)) {
                $masterDataChildren[] = $item;
            } elseif (in_array($label, $tenantLabels, true)) {
                $tenantChildren[] = $item;
            } else {
                $menuItems[] = $item;
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

        if (!empty($masterDataChildren)) {
            $menuItems[] = [
                'text' => 'Master Data',
                'icon' => 'fas fa-fw fa-layer-group',
                'submenu' => $masterDataChildren,
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
            'Company Management' => 'rbac/company',
            'History Management' => 'rbac/history',
            'Customer Management' => 'rbac/customer',
            'Tenant List Management' => 'rbac/customer',
            'Tenant Owner Management' => 'rbac/tenant-owner',
            'Menu Management' => 'rbac/master-menu',
            'Master Customer' => 'rbac/master-customer',
            'Master Item' => 'rbac/master-item',
        ];

        return $urlMap[$menuName] ?? '#';
    }

    private static function getMenuIcon($menuName)
    {
        $iconMap = [
            'Dashboard' => 'fas fa-fw fa-tachometer-alt',
            'User Management' => 'fas fa-fw fa-users',
            'Company Management' => 'fas fa-fw fa-briefcase',
            'History Management' => 'fas fa-fw fa-history',
            'Customer Management' => 'fas fa-fw fa-user-friends',
            'Tenant Owner Management' => 'fas fa-fw fa-id-card',
            'Tenant List Management' => 'fas fa-fw fa-building',
            'Menu Management' => 'fas fa-fw fa-bars',
            'Master Customer' => 'fas fa-fw fa-users',
            'Master Item' => 'fas fa-fw fa-box-open',
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
