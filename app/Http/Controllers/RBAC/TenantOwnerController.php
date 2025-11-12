<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\TenantOwner;
use App\Models\User;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TenantOwnerController extends Controller
{
    private function ensureInternal(): void
    {
        if (!TenantService::isInternal()) {
            abort(403, 'Only internal administrators can manage tenant owners.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureInternal();

        if ($request->ajax()) {
            $query = TenantOwner::with(['user', 'customer'])
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('owner_name', function ($row) {
                    return $row->user->name ?? $row->user->username;
                })
                ->addColumn('owner_email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('tenant', function ($row) {
                    return $row->customer->customer_name ?? '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-secondary">Inactive</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
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
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $users = User::orderBy('username')->get();
        $customers = Customer::orderBy('customer_name')->get();

        return view('rbac.tenant-owner.index', compact('users', 'customers'));
    }

    public function getAll(Request $request)
    {
        $this->ensureInternal();

        $tenantOwners = TenantOwner::with('user')->get();

        $data = $tenantOwners->map(function ($owner) {
            return [
                'id' => $owner->id,
                'name' => $owner->user->name ?? $owner->user->username,
            ];
        })->toArray();

        // Add Internal (Global) option
        array_unshift($data, [
            'id' => null,
            'name' => 'Internal (Global)',
        ]);

        return response()->json([
            'data' => $data
        ]);
    }

    public function getByCustomer()
    {
        $customerId = TenantService::currentCustomerId();

        if (!$customerId) {
            abort(403, 'Only tenant users can access this endpoint.');
        }

        $tenantOwners = TenantOwner::with('user')
            ->where('customer_id', $customerId)
            ->get();

        $data = $tenantOwners->map(function ($owner) {
            return [
                'id' => $owner->id,
                'name' => $owner->user->name ?? $owner->user->username,
            ];
        })->toArray();

        return response()->json([
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureInternal();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'is_active' => 'nullable|boolean',
        ]);

        if (TenantOwner::where('user_id', $request->user_id)->where('customer_id', $request->customer_id)->exists()) {
            return response()->json(['error' => 'This owner is already assigned to the selected tenant.'], 422);
        }

        TenantOwner::create([
            'user_id' => $request->user_id,
            'customer_id' => $request->customer_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => 'Tenant owner assigned successfully.']);
    }

    public function edit($id)
    {
        $this->ensureInternal();

        $owner = TenantOwner::findOrFail($id);
        return response()->json($owner);
    }

    public function update(Request $request, $id)
    {
        $this->ensureInternal();

        $owner = TenantOwner::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'is_active' => 'nullable|boolean',
        ]);

        if (TenantOwner::where('user_id', $request->user_id)
            ->where('customer_id', $request->customer_id)
            ->where('id', '!=', $id)
            ->exists()) {
            return response()->json(['error' => 'This owner is already assigned to the selected tenant.'], 422);
        }

        $owner->update([
            'user_id' => $request->user_id,
            'customer_id' => $request->customer_id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => 'Tenant owner updated successfully.']);
    }

    public function destroy($id)
    {
        $this->ensureInternal();

        $owner = TenantOwner::findOrFail($id);
        $owner->delete();

        return response()->json(['success' => 'Tenant owner removed successfully.']);
    }
}
