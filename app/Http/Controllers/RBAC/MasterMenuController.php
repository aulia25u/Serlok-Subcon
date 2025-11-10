<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\RoleToMenu;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterMenuController extends Controller
{
    /**
     * Display the master menu index view with necessary data for forms.
     * This method serves the initial HTML page.
     */
    public function show()
    {
        $roles = Role::all();
        $menus = Menu::all();
        
        return view('rbac.master-menu.index', compact('roles', 'menus'));
    }

    /**
     * Handle the AJAX request to get data for DataTables.
     * This method only returns JSON.
     */
    public function data(Request $request)
    {
        $query = RoleToMenu::with(['role', 'menu'])
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->end_date);
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('role_name', function ($row) {
                return $row->role->role_name ?? '-';
            })
            ->addColumn('menu_name', function ($row) {
                return $row->menu->menu_name ?? '-';
            })
            ->addColumn('permissions', function ($row) {
                $permissions = [];
                if ($row->is_create) $permissions[] = '<span class="badge badge-success">Create</span>';
                if ($row->is_read) $permissions[] = '<span class="badge badge-info">Read</span>';
                if ($row->is_update) $permissions[] = '<span class="badge badge-warning">Update</span>';
                if ($row->is_delete) $permissions[] = '<span class="badge badge-danger">Delete</span>';
                return implode(' ', $permissions);
            })
            ->addColumn('action', function ($row) {
                $btn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i> Edit
                        </button>';
                $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i> Delete
                        </button>';
                return $btn;
            })
            ->rawColumns(['permissions', 'action'])
            ->make(true);
    }

    // The rest of your methods are correct and remain the same
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $existing = RoleToMenu::where('role_id', $request->role_id)
            ->where('menu_id', $request->menu_id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'This role-menu combination already exists'], 400);
        }

        RoleToMenu::create([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
            'is_create' => $request->has('is_create'),
            'is_read' => $request->has('is_read'),
            'is_update' => $request->has('is_update'),
            'is_delete' => $request->has('is_delete'),
        ]);

        return response()->json(['success' => 'Master menu created successfully']);
    }

    public function edit($id)
    {
        $roleToMenu = RoleToMenu::with(['role', 'menu'])->findOrFail($id);
        return response()->json($roleToMenu);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $roleToMenu = RoleToMenu::findOrFail($id);
        
        $existing = RoleToMenu::where('role_id', $request->role_id)
            ->where('menu_id', $request->menu_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'This role-menu combination already exists'], 400);
        }

        $roleToMenu->update([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
            'is_create' => $request->has('is_create'),
            'is_read' => $request->has('is_read'),
            'is_update' => $request->has('is_update'),
            'is_delete' => $request->has('is_delete'),
        ]);

        return response()->json(['success' => 'Master menu updated successfully']);
    }

    public function destroy($id)
    {
        $roleToMenu = RoleToMenu::findOrFail($id);
        $roleToMenu->delete();
        
        return response()->json(['success' => 'Master menu deleted successfully']);
    }
}