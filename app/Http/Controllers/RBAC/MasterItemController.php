<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MasterItem;
use App\Models\TenantOwner;
use App\Models\MasterCustomer;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class MasterItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = MasterItem::with(['tenantOwner', 'masterCustomer'])->select('master_items.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();

            \Illuminate\Support\Facades\Log::info('MasterItem Debug', [
                'user_id' => $user->id,
                'customer_id' => $currentCustomerId,
                'is_admin' => $user->userDetail && $user->userDetail->role ? $user->userDetail->role->role_name : 'no-role',
            ]);

            // RBAC:
            // 1. If user is Tenant (has customer_id), only show their items.
            // 2. If user is Internal (no customer_id), ONLY show items if they are Administrator.
            if ($currentCustomerId) {
                $query->whereHas('tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            } else {
                // Internal user
                $isAdmin = $user->userDetail && $user->userDetail->role && $user->userDetail->role->role_name === 'Administrator';
                if (!$isAdmin) {
                    // If not Administrator, show nothing
                    $query->whereRaw('1 = 0');
                }
            }

            // Date Filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tenant_name', function (MasterItem $masterItem) {
                    return $masterItem->tenantOwner ? $masterItem->tenantOwner->name : 'N/A';
                })
                ->addColumn('customer_name', function (MasterItem $masterItem) {
                    return $masterItem->masterCustomer ? $masterItem->masterCustomer->customer_name : 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm master_item-edit-btn me-1">Edit</button>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm master_item-delete-btn">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Pass TenantOwners for the modal (Internal users only)
        $user = Auth::user();
        $tenantOwners = [];
        if (!$user->userDetail || !$user->userDetail->customer_id) {
            $tenantOwners = TenantOwner::all();
        }

        $masterCustomers = MasterCustomer::all();

        return view('master_item.index', compact('tenantOwners', 'masterCustomers'));
    }

    public function create()
    {
        // Not used with modal
        abort(404);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        $isTenant = !is_null($currentCustomerId);

        $rules = [
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:master_items,item_code',
            'master_customer_id' => 'nullable|exists:master_customers,id',
            'product_status' => 'nullable|string|in:Continue,Not Continue',
            'part_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit' => 'nullable|string|in:PCS,KG,ROLL',
            'description' => 'nullable|string',
        ];

        if (!$isTenant) {
            $rules['tenant_id'] = 'required|exists:tenant_owners,id';
        }

        $request->validate($rules);

        $data = $request->all();

        if ($isTenant) {
            // Auto-assign tenant_id for Tenant users
            // Assuming the first TenantOwner for this customer is the target
            // Ideally, the logged-in user IS the TenantOwner or linked to one
            $tenantOwner = TenantOwner::where('customer_id', $currentCustomerId)->first();

            if (!$tenantOwner) {
                return response()->json(['error' => 'No Tenant Owner record found for your organization.'], 403);
            }
            $data['tenant_id'] = $tenantOwner->id;
        }

        $masterItem = MasterItem::create($data);

        // Log activity
        ActivityLogService::logCreate('master_items', $masterItem->id, [
            'item_name' => $request->item_name,
            'item_code' => $request->item_code,
            'tenant_id' => $masterItem->tenant_id,
        ]);

        return response()->json(['success' => 'Master Item created successfully.']);
    }

    public function show(MasterItem $masterItem)
    {
        // Optional: Implement RBAC for show if needed, or just return view
        return view('master_item.show', compact('masterItem'));
    }

    public function edit($id)
    {
        $masterItem = MasterItem::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterItem->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                abort(403);
            }
        }

        if (request()->ajax()) {
            return response()->json($masterItem);
        }

        return view('master_item.edit', compact('masterItem'));
    }

    public function update(Request $request, $id)
    {
        $masterItem = MasterItem::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterItem->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $rules = [
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:master_items,item_code,' . $masterItem->id,
            'master_customer_id' => 'nullable|exists:master_customers,id',
            'product_status' => 'nullable|string|in:Continue,Not Continue',
            'part_number' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit' => 'nullable|string|in:PCS,KG,ROLL',
            'description' => 'nullable|string',
        ];

        $isTenant = $user->userDetail && $user->userDetail->customer_id;
        if (!$isTenant) {
            $rules['tenant_id'] = 'required|exists:tenant_owners,id';
        }

        $request->validate($rules);

        $data = $request->all();

        // Prevent Tenant from changing tenant_id
        if ($isTenant) {
            unset($data['tenant_id']);
        }

        $oldValues = $masterItem->toArray();
        $masterItem->update($data);

        // Log activity
        $newValues = $masterItem->toArray();
        ActivityLogService::logUpdate('master_items', $masterItem->id, $oldValues, $newValues);

        return response()->json(['success' => 'Master Item updated successfully.']);
    }

    public function destroy($id)
    {
        $masterItem = MasterItem::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterItem->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $oldValues = $masterItem->toArray();
        $masterItem->delete();

        // Log activity
        ActivityLogService::logDelete('master_items', $id, $oldValues);

        return response()->json(['success' => 'Master Item deleted successfully.']);
    }
}
