<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleToMenu;
use App\Models\Menu;
use App\Services\MenuService;
use App\Services\TenantService;

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

        // Skip permission check for dashboard, profile, and helper routes (dropdowns)
        $excludedRoutes = [
            'dashboard',
            'profile.edit',
            'profile.update',
            'profile.destroy',
            'rbac.departments.by-customer',
            'rbac.sections.by-customer',
            'rbac.sections.by-department',
            'rbac.positions.by-section',
            'rbac.roles.by-customer',
            'rbac.departments.all',
            'rbac.sections.all',
            'rbac.tenant-owner.by-customer',
            'rbac.tenant-owner.all',
            'rbac.customer.get'
        ];

        if (in_array($currentRouteName, $excludedRoutes)) {
            return $next($request);
        }

        // Find menu based on route_name mapping in database
        // We iterate to support comma-separated patterns and prefix matching
        $menu = Menu::all()->first(function ($menu) use ($currentRouteName) {
            if (empty($menu->route_name))
                return false;

            $patterns = explode(',', $menu->route_name);
            foreach ($patterns as $pattern) {
                $pattern = trim($pattern);
                // Match exact route or route starting with pattern followed by dot (e.g. rbac.user-data.edit)
                if ($pattern === $currentRouteName || str_starts_with($currentRouteName, $pattern . '.')) {
                    return true;
                }
            }
            return false;
        });

        if (!$menu) {
            abort(403, 'Access denied: No menu mapping found for this route.');
        }

        // Get the role-to-menu record
        $query = RoleToMenu::where('role_id', $roleId)
            ->where('menu_id', $menu->id);

        if (TenantService::isInternal()) {
            $query->whereNull('customer_id');
        } else {
            $query->where('customer_id', TenantService::currentCustomerId());
        }

        $roleToMenu = $query->first();

        if (!$roleToMenu) {
            abort(403, 'No permissions assigned for this menu.');
        }

        // Determine required permission based on HTTP method
        $method = $request->method();
        $permissionField = match ($method) {
            'GET' => 'is_read',
            'POST' => 'is_create',
            'PUT', 'PATCH' => 'is_update',
            'DELETE' => 'is_delete',
            default => 'is_read', // Fallback to read for other methods
        };

        $hasPermission = $roleToMenu->{$permissionField};

        if (!$hasPermission) {
            $action = match ($method) {
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
}
