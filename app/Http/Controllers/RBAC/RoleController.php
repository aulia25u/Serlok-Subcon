<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Role;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

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
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            $query = Role::with('customer');
            $query = TenantService::scopeQueryByCustomer($query);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return $row->customer->customer_name ?? 'Internal';
                })
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

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : collect();

        return view('rbac.role.index', [
            'customers' => $customers,
            'currentCustomerId' => $customerId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $customerId = TenantService::resolveCustomerId($request->customer_id);

        $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($customerId) {
                    if (is_null($customerId)) {
                        $query->whereNull('customer_id');
                    } else {
                        $query->where('customer_id', $customerId);
                    }
                }),
            ],
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create([
                'role_name' => $request->role_name,
                'customer_id' => $customerId,
            ]);

            DB::commit();

            // Log activity
            ActivityLogService::logCreate('roles', $role->id, [
                'role_name' => $request->role_name,
                'customer_id' => $role->customer_id,
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
        $role = Role::findOrFail($id);
        TenantService::assertAccess($role->customer_id);
        $customerId = TenantService::resolveCustomerId($request->customer_id);

        $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) use ($customerId) {
                    if (is_null($customerId)) {
                        $query->whereNull('customer_id');
                    } else {
                        $query->where('customer_id', $customerId);
                    }
                })->ignore($id),
            ],
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        DB::beginTransaction();

        try {
            $oldValues = $role->toArray();

            $role->update([
                'role_name' => $request->role_name,
                'customer_id' => $customerId,
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
            TenantService::assertAccess($role->customer_id);
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

    public function getByCustomer($customer_id)
    {
        $resolvedCustomerId = $customer_id === 'null' ? null : (int) $customer_id;
        TenantService::assertAccess($resolvedCustomerId);

        $query = Role::orderBy('role_name');
        if (is_null($resolvedCustomerId)) {
            $query->whereNull('customer_id');
        } else {
            $query->where('customer_id', $resolvedCustomerId);
        }

        $roles = $query->get(['id', 'role_name']);

        return response()->json($roles);
    }
}
