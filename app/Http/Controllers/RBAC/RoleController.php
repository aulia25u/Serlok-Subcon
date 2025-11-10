<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::query();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#addModal" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rbac.role.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create([
                'role_name' => $request->role_name,
            ]);

            DB::commit();

            // Log activity
            ActivityLogService::logCreate('roles', $role->id, [
                'role_name' => $request->role_name,
            ]);

            return response()->json(['success' => 'Role created successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Role creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name,' . $id,
        ]);

        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);
            $oldValues = $role->toArray();

            $role->update([
                'role_name' => $request->role_name,
            ]);

            DB::commit();

            // Log activity
            $newValues = $role->toArray();
            ActivityLogService::logUpdate('roles', $role->id, $oldValues, $newValues);

            return response()->json(['success' => 'Role updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Role update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update role.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $role = Role::findOrFail($id);
            $oldValues = $role->toArray();

            $role->delete();

            DB::commit();

            // Log activity
            ActivityLogService::logDelete('roles', $id, $oldValues);

            return response()->json(['success' => 'Role deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Role deletion failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete role.'], 500);
        }
    }
}
