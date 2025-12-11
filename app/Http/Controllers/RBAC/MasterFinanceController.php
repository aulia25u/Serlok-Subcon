<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MasterFinance;
use App\Models\TenantOwner;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class MasterFinanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = MasterFinance::with('tenantOwner')->select('master_finances.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();

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

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tenant_name', function (MasterFinance $masterFinance) {
                    return $masterFinance->tenantOwner ? $masterFinance->tenantOwner->name : 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm master_finance-edit-btn me-1">Edit</button>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm master_finance-delete-btn">Delete</button>';
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

        return view('rbac.master-finance.index', compact('tenantOwners'));
    }

    public function store(Request $request)
    {
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        $isTenant = !is_null($currentCustomerId);

        $rules = [
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
        ];

        if (!$isTenant) {
            $rules['tenant_id'] = 'required|exists:tenant_owners,id';
        }

        $request->validate($rules);

        $data = $request->all();

        if ($isTenant) {
            $tenantOwner = TenantOwner::where('customer_id', $currentCustomerId)->first();

            if (!$tenantOwner) {
                return response()->json(['error' => 'No Tenant Owner record found for your organization.'], 403);
            }
            $data['tenant_id'] = $tenantOwner->id;
        }

        $masterFinance = MasterFinance::create($data);

        // Log activity
        ActivityLogService::logCreate('master_finances', $masterFinance->id, [
            'bank_name' => $request->bank_name,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'tenant_id' => $masterFinance->tenant_id,
        ]);

        return response()->json(['success' => 'Master Finance created successfully.']);
    }

    public function edit($id)
    {
        $masterFinance = MasterFinance::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterFinance->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                abort(403);
            }
        }

        if (request()->ajax()) {
            return response()->json($masterFinance);
        }

        return abort(404); // View not needed for modal edit
    }

    public function update(Request $request, $id)
    {
        $masterFinance = MasterFinance::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterFinance->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $rules = [
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
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

        $oldValues = $masterFinance->toArray();
        $masterFinance->update($data);

        // Log activity
        $newValues = $masterFinance->toArray();
        ActivityLogService::logUpdate('master_finances', $masterFinance->id, $oldValues, $newValues);

        return response()->json(['success' => 'Master Finance updated successfully.']);
    }

    public function destroy($id)
    {
        $masterFinance = MasterFinance::findOrFail($id);

        // RBAC Check
        $user = Auth::user();
        if ($user->userDetail && $user->userDetail->customer_id) {
            if ($masterFinance->tenantOwner->customer_id !== $user->userDetail->customer_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $oldValues = $masterFinance->toArray();
        $masterFinance->delete();

        // Log activity
        ActivityLogService::logDelete('master_finances', $id, $oldValues);

        return response()->json(['success' => 'Master Finance deleted successfully.']);
    }
}
