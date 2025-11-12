<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleToMenu;
use App\Models\Menu;
use App\Services\MenuService;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $userDetail = $user->userDetail;
        if (!$userDetail || !$userDetail->role) {
            return redirect('/dashboard'); // Or abort(403)
        }

        $roleId = $userDetail->role->id;
        $currentRouteName = $request->route()->getName();

        // Skip permission check for dashboard and profile
        if (in_array($currentRouteName, ['dashboard', 'profile.edit', 'profile.update', 'profile.destroy'])) {
            return $next($request);
        }

        // Map route name to menu name using reverse mapping
        $menuName = $this->getMenuNameFromRoute($currentRouteName);

        if (!$menuName) {
            return $next($request); // Allow if no mapping
        }

        // Get menu ID
        $menu = Menu::where('menu_name', $menuName)->first();
        if (!$menu) {
            return $next($request);
        }

        // Get the role-to-menu record
        $roleToMenu = RoleToMenu::where('role_id', $roleId)
            ->where('menu_id', $menu->id)
            ->first();

        if (!$roleToMenu) {
            abort(403, 'No permissions assigned for this menu.');
        }

        // Determine required permission based on HTTP method
        $method = $request->method();
        $permissionField = match($method) {
            'GET' => 'is_read',
            'POST' => 'is_create',
            'PUT', 'PATCH' => 'is_update',
            'DELETE' => 'is_delete',
            default => 'is_read', // Fallback to read for other methods
        };

        $hasPermission = $roleToMenu->{$permissionField};

        if (!$hasPermission) {
            $action = match($method) {
                'GET' => 'view',
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'access',
            };
            abort(403, "Unauthorized to {$action} this resource. You only have permission for " . ($roleToMenu->is_read ? 'read' : 'no access') . ".");
        }

        return $next($request);
    }

    private function getMenuNameFromRoute($routeName)
    {
        // Base mapping from route base strings to menu names (dynamic: covers all CRUD/auxiliary variants via str_contains)
        // For new menus, add entry like: 'new-feature' => 'New Feature Management'
        $baseRouteToMenu = [
            'user-data' => 'User Management',
            'role' => 'Role Management',
            'history' => 'History Management',
            'department' => 'Department Management',
            'section' => 'Section Management',
            'position' => 'Position Management',
            'plant' => 'Plant Management',
            'master-menu' => 'Menu Management',
        ];

        foreach ($baseRouteToMenu as $baseKey => $menuName) {
            if (str_contains($routeName, $baseKey)) {
                return $menuName;
            }
        }

        return null; // Allow if no mapping
    }
}
